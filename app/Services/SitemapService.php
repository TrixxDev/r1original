<?php

namespace App\Services;

use Carbon\Carbon;
use Generator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SitemapService
{
    protected const META_CACHE_KEY = 'sitemap.meta';

    /** @var string|null ISO date used for static/CMS URLs during warmCache() */
    protected ?string $generatedAt = null;

    public function entries(): Generator
    {
        $seen = [];

        foreach ($this->allEntries() as $entry) {
            $loc = $entry['loc'] ?? '';

            if ($loc === '' || isset($seen[$loc])) {
                continue;
            }

            $seen[$loc] = true;
            yield $entry;
        }
    }

    public function warmCache(): array
    {
        $this->generatedAt = now()->toIso8601String();
        $entries = iterator_to_array($this->entries());
        $this->generatedAt = null;
        $maxPerFile = (int) config('sitemap.max_urls_per_file', 50000);
        $chunks = array_chunk($entries, max(1, $maxPerFile));
        $chunkCount = count($chunks);
        $ttl = (int) config('sitemap.cache_ttl', 86400);

        $meta = [
            'chunk_count' => $chunkCount,
            'url_count' => count($entries),
            'generated_at' => now()->toIso8601String(),
        ];

        Cache::forget(self::META_CACHE_KEY);

        for ($page = 1; $page <= $chunkCount; $page++) {
            Cache::forget($this->chunkCacheKey($page));
        }

        Cache::put(self::META_CACHE_KEY, $meta, $ttl);

        foreach ($chunks as $index => $chunk) {
            Cache::put(
                $this->chunkCacheKey($index + 1),
                $this->buildChunkXml($chunk),
                $ttl
            );
        }

        return $meta;
    }

    public function getIndexOrSingleXml(): ?string
    {
        $meta = Cache::get(self::META_CACHE_KEY);

        if (!is_array($meta) || empty($meta['chunk_count'])) {
            return null;
        }

        if ((int) $meta['chunk_count'] === 1) {
            return Cache::get($this->chunkCacheKey(1));
        }

        return $this->buildIndexXml((int) $meta['chunk_count'], $meta['generated_at'] ?? null);
    }

    public function getChunkXml(int $page): ?string
    {
        if ($page < 1) {
            return null;
        }

        return Cache::get($this->chunkCacheKey($page));
    }

    protected function allEntries(): Generator
    {
        yield from $this->staticEntries();
        yield from $this->branchEntries();
        yield from $this->cmsPageEntries();
        yield from $this->autoBrandHubEntries();
        yield from $this->autoTireEntries();
        yield from $this->motoTireEntries();
        yield from $this->quadrTireEntries();
        yield from $this->bigTireEntries();
        yield from $this->rimEntries();
        yield from $this->quadrimEntries();
        yield from $this->studEntries();
    }

    protected function branchEntries(): Generator
    {
        foreach (config('seo.organization.locations', []) as $location) {
            $routeName = $location['route_name'] ?? null;

            if (!$routeName || !\Illuminate\Support\Facades\Route::has($routeName)) {
                continue;
            }

            yield $this->entry(route($routeName), $this->generatedAt);
        }
    }

    protected function staticEntries(): Generator
    {
        foreach ([
            '/pieraksts',
            '/vasaras-riepas',
            '/ziemas-riepas',
            '/motociklu-riepas',
            '/kvadru-riepas',
            '/lielas-riepas',
            '/lietie-diski',
            '/kvadru-diski',
            '/kvadraciklu-diski',
            '/radzes',
            '/akcijas',
            '/paskaidrojumi',
            '/kalkulators',
        ] as $path) {
            yield $this->entry(url($path), $this->generatedAt);
        }
    }

    protected function cmsPageEntries(): Generator
    {
        $rows = DB::table('pages')
            ->whereNotNull('route')
            ->where('route', '<>', '')
            ->select('route')
            ->orderBy('id')
            ->cursor();

        foreach ($rows as $row) {
            yield $this->entry(url('/' . ltrim((string) $row->route, '/')), $this->generatedAt);
        }
    }

    protected function autoBrandHubEntries(): Generator
    {
        yield $this->entry(route('riepu-razotaji'), $this->autoTireCatalogLastmod());

        $brandRows = DB::table('auto_brands')
            ->join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
            ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
            ->where('auto_tires.visible_users', '<>', 0)
            ->select('auto_brands.slug as brand_slug')
            ->selectRaw('MAX(auto_tires.updated_at) as lastmod')
            ->groupBy('auto_brands.brand_id', 'auto_brands.slug')
            ->orderBy('auto_brands.slug')
            ->cursor();

        foreach ($brandRows as $brand) {
            yield $this->entry(route('riepu-razotaji-brand', $brand->brand_slug), $brand->lastmod);
        }

        $treadRows = DB::table('auto_treads')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
            ->where('auto_tires.visible_users', '<>', 0)
            ->select(
                'auto_brands.slug as brand_slug',
                'auto_treads.t_title as tread_title'
            )
            ->selectRaw('MAX(auto_tires.updated_at) as lastmod')
            ->groupBy('auto_treads.tread_id', 'auto_brands.slug', 'auto_treads.t_title')
            ->orderBy('auto_brands.slug')
            ->orderBy('auto_treads.t_title')
            ->cursor();

        foreach ($treadRows as $tread) {
            yield $this->entry(route('riepu-razotaji-tread', [
                'brand' => $tread->brand_slug,
                'tread' => strtolower($this->treadSegment($tread->tread_title)),
            ]), $tread->lastmod);
        }
    }

    protected function autoTireCatalogLastmod(): ?string
    {
        return DB::table('auto_tires')
            ->where('visible_users', '<>', 0)
            ->max('updated_at');
    }

    protected function autoTireEntries(): Generator
    {
        $rows = DB::table('auto_tires')
            ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_tires.visible_users', '<>', 0)
            ->where('auto_tires.visible_list', '<>', 0)
            ->whereIn('auto_treads.season', [1, 2])
            ->select(
                'auto_tires.tire_id',
                'auto_tires.updated_at',
                'auto_treads.t_title as tread_title',
                'auto_treads.season',
                'auto_brands.slug as brand_slug'
            )
            ->orderBy('auto_tires.tire_id')
            ->cursor();

        foreach ($rows as $tire) {
            $route = ((int) $tire->season === 1) ? 'vasaras-riepa' : 'ziemas-riepa';

            yield $this->entry(
                route($route, [
                    $tire->brand_slug,
                    $this->treadSegment($tire->tread_title),
                    $tire->tire_id,
                ]),
                $tire->updated_at
            );
        }
    }

    protected function motoTireEntries(): Generator
    {
        $rows = DB::table('moto_tires')
            ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
            ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
            ->where('moto_tires.visible_users', '<>', 0)
            ->where('moto_tires.visible_list', '<>', 0)
            ->select(
                'moto_tires.tire_id',
                'moto_tires.updated_at',
                'moto_treads.title as tread_title',
                'moto_brands.title as brand_title'
            )
            ->orderBy('moto_tires.tire_id')
            ->cursor();

        foreach ($rows as $tire) {
            yield $this->entry(
                route('motociklu-riepa', [
                    strtolower((string) $tire->brand_title),
                    $this->treadSegment($tire->tread_title),
                    $tire->tire_id,
                ]),
                $tire->updated_at
            );
        }
    }

    protected function quadrTireEntries(): Generator
    {
        $rows = DB::table('quadr_tires')
            ->join('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
            ->join('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_tires.visible_users', '<>', 0)
            ->where('quadr_tires.visible_list', '<>', 0)
            ->select(
                'quadr_tires.tire_id',
                'quadr_tires.updated_at',
                'quadr_treads.t_title as tread_title',
                'quadr_brands.slug as brand_slug'
            )
            ->orderBy('quadr_tires.tire_id')
            ->cursor();

        foreach ($rows as $tire) {
            yield $this->entry(
                route('kvadraciklu-riepa', [
                    $tire->brand_slug,
                    $this->treadSegment($tire->tread_title),
                    $tire->tire_id,
                ]),
                $tire->updated_at
            );
        }
    }

    protected function bigTireEntries(): Generator
    {
        $rows = DB::table('big_tires')
            ->join('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
            ->join('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
            ->where('big_tires.visible_users', '<>', 0)
            ->where('big_tires.visible_list', '<>', 0)
            ->select(
                'big_tires.tire_id',
                'big_tires.updated_at',
                'bigtire_treads.title as tread_title',
                'bigtire_brands.slug as brand_slug'
            )
            ->orderBy('big_tires.tire_id')
            ->cursor();

        foreach ($rows as $tire) {
            yield $this->entry(
                route('lielas-riepa', [
                    $tire->brand_slug,
                    $this->treadSegment($tire->tread_title),
                    $tire->tire_id,
                ]),
                $tire->updated_at
            );
        }
    }

    protected function rimEntries(): Generator
    {
        $rows = DB::table('rims')
            ->join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
            ->join('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
            ->where('rims.visible_users', '<>', 0)
            ->where('rims.visible_list', '<>', 0)
            ->select(
                'rims.rim_id',
                'rims.updated_at',
                'rim_makes.title as tread_title',
                'rim_brands.title as brand_title'
            )
            ->orderBy('rims.rim_id')
            ->cursor();

        foreach ($rows as $rim) {
            yield $this->entry(
                route('lietais-disks', [
                    $rim->brand_title,
                    $this->treadSegment($rim->tread_title),
                    $rim->rim_id,
                ]),
                $rim->updated_at
            );
        }
    }

    protected function quadrimEntries(): Generator
    {
        $rows = DB::table('quadrims')
            ->join('quadrim_makes', 'quadrims.make_id', '=', 'quadrim_makes.make_id')
            ->join('quadrim_brands', 'quadrim_makes.brand_id', '=', 'quadrim_brands.brand_id')
            ->where('quadrims.visible_users', '<>', 0)
            ->where('quadrims.visible_list', '<>', 0)
            ->select(
                'quadrims.rim_id',
                'quadrims.updated_at',
                'quadrim_makes.t_title as tread_title',
                'quadrim_brands.b_title as brand_title'
            )
            ->orderBy('quadrims.rim_id')
            ->cursor();

        foreach ($rows as $rim) {
            yield $this->entry(
                route('kvadracikla-disks', [
                    Str::slug((string) $rim->brand_title),
                    strtolower($this->treadSegment($rim->tread_title)),
                    $rim->rim_id,
                ]),
                $rim->updated_at
            );
        }
    }

    protected function studEntries(): Generator
    {
        $rows = DB::table('studs')
            ->join('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
            ->join('studs_brands', 'studs_treads.brand_id', '=', 'studs_brands.brand_id')
            ->where('studs.visible_users', '<>', 0)
            ->where('studs.visible_list', '<>', 0)
            ->select(
                'studs.stud_id',
                'studs.updated_at',
                'studs_treads.t_title as tread_title',
                'studs_brands.b_title as brand_title'
            )
            ->orderBy('studs.stud_id')
            ->cursor();

        foreach ($rows as $stud) {
            yield $this->entry(
                route('radze', [
                    $stud->brand_title,
                    $this->treadSegment($stud->tread_title),
                    $stud->stud_id,
                ]),
                $stud->updated_at
            );
        }
    }

    protected function entry(string $loc, $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $this->formatLastmod($lastmod),
        ];
    }

    protected function formatLastmod($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function buildChunkXml(array $entries): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($entries as $entry) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . htmlspecialchars((string) $entry['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</loc>';

            if (!empty($entry['lastmod'])) {
                $lines[] = '    <lastmod>' . $entry['lastmod'] . '</lastmod>';
            }

            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function buildIndexXml(int $chunkCount, ?string $generatedAt): string
    {
        $lastmod = $this->formatLastmod($generatedAt);
        $baseUrl = rtrim((string) config('app.url'), '/');

        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        for ($page = 1; $page <= $chunkCount; $page++) {
            $lines[] = '  <sitemap>';
            $lines[] = '    <loc>' . htmlspecialchars($baseUrl . '/sitemap-' . $page . '.xml', ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</loc>';

            if ($lastmod !== null) {
                $lines[] = '    <lastmod>' . $lastmod . '</lastmod>';
            }

            $lines[] = '  </sitemap>';
        }

        $lines[] = '</sitemapindex>';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function chunkCacheKey(int $page): string
    {
        return 'sitemap.chunk.' . $page;
    }

    protected function treadSegment(?string $value): string
    {
        return str_replace('/', '_', (string) $value);
    }
}

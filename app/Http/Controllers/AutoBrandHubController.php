<?php

namespace App\Http\Controllers;

use App\Helper\BrandLogo;
use App\Models\Autobrand;
use App\Models\Autotire;
use App\Models\Autotread;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AutoBrandHubController extends Controller
{
    public function index()
    {
        $brands = $this->brandsWithStats()->map(function ($brand) {
            $brand->logo_url = BrandLogo::url($brand->slug);

            return $brand;
        });

        return view('brands.index', compact('brands'));
    }

    public function show(string $slug)
    {
        $brand = Autobrand::where('slug', $slug)->first();

        if (!$brand) {
            abort(404);
        }

        $stats = $this->brandStats((int) $brand->brand_id);

        if ($stats['tire_count'] === 0) {
            abort(404);
        }

        $treads = $this->brandTreads((int) $brand->brand_id, $brand->slug);
        $brand->logo_url = BrandLogo::url($brand->slug);

        return view('brands.show', compact('brand', 'stats', 'treads'));
    }

    public function tread(Request $request, string $brandSlug, string $treadSlug)
    {
        $brand = Autobrand::where('slug', $brandSlug)->first();

        if (!$brand) {
            abort(404);
        }

        $tread = $this->resolveTread((int) $brand->brand_id, $treadSlug);

        if (!$tread) {
            abort(404);
        }

        $selectedTires = [];
        if ($request->input('selected')) {
            $selectedTires = array_map('intval', explode(',', (string) $request->input('selected')));
        }

        $tires = Autotire::query()
            ->selectRaw('auto_tires.*, auto_treads.*, auto_brands.*, auto_brands.title as brands_title')
            ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.tread_id', $tread->tread_id)
            ->where('auto_tires.visible_users', '<>', 0)
            ->orderBy('auto_tires.d3')
            ->orderBy('auto_tires.d1')
            ->orderBy('auto_tires.d2')
            ->get();

        if ($tires->isEmpty()) {
            abort(404);
        }

        Autotire::preloadStockData($tires->pluck('tire_id')->all());

        $tireId = (int) $request->query('tire');
        $currTire = $tireId ? $tires->firstWhere('tire_id', $tireId) : $tires->first();
        $activeTireId = $tireId ? optional($currTire)->tire_id : null;

        if (!$currTire) {
            abort(404);
        }

        $currTire->includeStock = true;
        $currBrand = $brand;
        $season = (int) $tread->season;
        $code_array = $this->codeArray();
        $cartQty = 4;
        $catalogAjaxUrl = $season === 1 ? url('/vasaras-riepas/ajax') : url('/ziemas-riepas/ajax');

        return view('brands.tread', compact(
            'brand',
            'tread',
            'tires',
            'season',
            'currTire',
            'activeTireId',
            'currBrand',
            'selectedTires',
            'code_array',
            'cartQty',
            'catalogAjaxUrl'
        ));
    }

    protected function brandsWithStats(): Collection
    {
        return Autobrand::query()
            ->select('auto_brands.brand_id', 'auto_brands.title', 'auto_brands.slug', 'auto_brands.b_comment')
            ->selectRaw('COUNT(DISTINCT auto_treads.tread_id) as tread_count')
            ->selectRaw('COUNT(DISTINCT auto_tires.tire_id) as tire_count')
            ->selectRaw('MIN(auto_tires.price2) as min_price')
            ->join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
            ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
            ->where('auto_tires.visible_users', '<>', 0)
            ->groupBy('auto_brands.brand_id', 'auto_brands.title', 'auto_brands.slug', 'auto_brands.b_comment')
            ->orderBy('auto_brands.title')
            ->get();
    }

    protected function brandStats(int $brandId): array
    {
        $row = Autobrand::query()
            ->selectRaw('COUNT(DISTINCT auto_treads.tread_id) as tread_count')
            ->selectRaw('COUNT(DISTINCT auto_tires.tire_id) as tire_count')
            ->selectRaw('MIN(auto_tires.price2) as min_price')
            ->selectRaw('SUM(CASE WHEN auto_treads.season = 1 THEN 1 ELSE 0 END) as summer_tires')
            ->selectRaw('SUM(CASE WHEN auto_treads.season = 2 THEN 1 ELSE 0 END) as winter_tires')
            ->join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
            ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
            ->where('auto_brands.brand_id', $brandId)
            ->where('auto_tires.visible_users', '<>', 0)
            ->first();

        return [
            'tread_count' => (int) ($row->tread_count ?? 0),
            'tire_count' => (int) ($row->tire_count ?? 0),
            'min_price' => (int) ($row->min_price ?? 0),
            'has_summer' => (int) ($row->summer_tires ?? 0) > 0,
            'has_winter' => (int) ($row->winter_tires ?? 0) > 0,
        ];
    }

    protected function brandTreads(int $brandId, string $brandSlug): Collection
    {
        $treads = Autotread::query()
            ->select('auto_treads.tread_id', 'auto_treads.t_title', 'auto_treads.season', 'auto_treads.brand_id')
            ->selectRaw('COUNT(auto_tires.tire_id) as tire_count')
            ->selectRaw('MIN(auto_tires.price2) as min_price')
            ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
            ->where('auto_treads.brand_id', $brandId)
            ->where('auto_tires.visible_users', '<>', 0)
            ->groupBy('auto_treads.tread_id', 'auto_treads.t_title', 'auto_treads.season', 'auto_treads.brand_id')
            ->orderBy('auto_treads.t_title')
            ->get();

        return $treads->map(function ($tread) use ($brandSlug) {
            $season = (int) $tread->season;
            $tread->season_label = $season === 1 ? 'Vasaras' : 'Ziemas';
            $tread->hub_url = route('riepu-razotaji-tread', [
                'brand' => $brandSlug,
                'tread' => self::treadSegment($tread->t_title),
            ]);

            return $tread;
        });
    }

    protected function resolveTread(int $brandId, string $treadSlug): ?Autotread
    {
        $normalized = strtolower($treadSlug);

        return Autotread::query()
            ->where('brand_id', $brandId)
            ->where(function ($query) use ($normalized) {
                $query->where('slug', $normalized)
                    ->orWhereRaw('LOWER(REPLACE(t_title, "/", "_")) = ?', [$normalized]);
            })
            ->first();
    }

    protected function codeArray(): array
    {
        $codes = Cache::remember('codes_table_all', 3600, function () {
            return Code::all();
        });

        $map = [];
        foreach ($codes as $code) {
            $map[$code->name] = $code->explanation;
        }

        return $map;
    }

    public static function treadSegment(string $treadTitle): string
    {
        return strtolower(str_replace('/', '_', $treadTitle));
    }

    public static function buildTireUrl(int $season, string $brandSlug, string $treadTitle, int $tireId): string
    {
        $prefix = $season === 1 ? '/vasaras-riepas' : '/ziemas-riepas';

        return $prefix . '/' . $brandSlug . '/' . self::treadSegment($treadTitle) . '/' . $tireId;
    }

    public static function catalogUrlForBrand(string $brandTitle, int $season): string
    {
        $route = $season === 1 ? 'vasaras-riepas-meklet' : 'ziemas-riepas-meklet';

        return route($route, ['brand' => $brandTitle]);
    }
}

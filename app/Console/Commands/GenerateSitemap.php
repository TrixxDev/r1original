<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Build and cache sitemap.xml (and chunks if needed)';

    public function handle(SitemapService $sitemap): int
    {
        $this->info('Generating sitemap...');

        $meta = $sitemap->warmCache();

        $this->info(sprintf(
            'Done: %d URLs in %d file(s).',
            $meta['url_count'],
            $meta['chunk_count']
        ));

        return self::SUCCESS;
    }
}


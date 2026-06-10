<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemap): Response
    {
        $xml = $sitemap->getIndexOrSingleXml();

        if ($xml === null) {
            $sitemap->warmCache();
            $xml = $sitemap->getIndexOrSingleXml();
        }

        return $this->xmlResponse($xml ?? '');
    }

    public function chunk(int $page, SitemapService $sitemap): Response
    {
        if ($page < 1) {
            abort(HttpResponse::HTTP_NOT_FOUND);
        }

        $xml = $sitemap->getChunkXml($page);

        if ($xml === null) {
            $sitemap->warmCache();
            $xml = $sitemap->getChunkXml($page);
        }

        if ($xml === null) {
            abort(HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->xmlResponse($xml);
    }

    public function robots(): Response
    {
        $sitemapUrl = rtrim((string) config('app.url'), '/') . '/sitemap.xml';

        $body = implode("\n", [
            'User-agent: *',
            'Disallow:',
            'Sitemap: ' . $sitemapUrl,
            '',
        ]);

        return response($body, HttpResponse::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function xmlResponse(string $xml): Response
    {
        return response($xml, HttpResponse::HTTP_OK, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}


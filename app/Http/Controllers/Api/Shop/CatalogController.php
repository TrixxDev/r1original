<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Autotire;
use App\Services\Shop\AutoTireCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * @var AutoTireCatalogService
     */
    protected $catalog;

    public function __construct(AutoTireCatalogService $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * Fasetu izmēri: d1/d2/d3 saraksti atkarībā no pārējiem query parametriem (saderīgas kombinācijas).
     */
    public function filterSizes(Request $request): JsonResponse
    {
        $request->validate([
            'season' => 'required|in:1,2',
            'd1' => 'nullable|string',
            'd2' => 'nullable|string',
            'd3' => 'nullable|string',
        ]);

        $season = (int) $request->query('season');
        $result = $this->catalog->facetSizes(
            $request->query('d1'),
            $request->query('d2'),
            $request->query('d3'),
            $season
        );

        return response()->json([
            'labels' => [
                'd1' => 'Platums',
                'd2' => 'Augstums',
                'd3' => 'Diametrs',
            ],
            'd1' => $result['d1'],
            'd2' => $result['d2'],
            'd3' => $result['d3'],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'season' => 'required|in:1,2',
        ]);

        $season = (int) $request->query('season');
        $paginator = $this->catalog->paginate($request, $season);

        $data = $paginator->getCollection()->map(function (Autotire $tire) use ($season) {
            return $this->catalog->mapTireToListItem($tire, $season);
        })->values()->all();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(Request $request, int $tireId): JsonResponse
    {
        $request->validate([
            'season' => 'required|in:1,2',
        ]);

        $season = (int) $request->query('season');

        $tire = Autotire::selectRaw('auto_tires.*, auto_tires.quantity as tire_quantity, auto_treads.*, (SELECT SUM(quantity) FROM auto_stock WHERE auto_stock.tire_id = auto_tires.tire_id) as stock_quantity, auto_brands.title as api_brand_title, auto_brands.slug as api_brand_slug')
            ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_tires.tire_id', $tireId)
            ->where('auto_treads.season', $season)
            ->where('auto_tires.visible_users', '<>', 0)
            ->first();

        if (! $tire) {
            return response()->json(['message' => 'Riepa nav atrasta'], 404);
        }

        return response()->json($this->catalog->mapTireToDetail($tire, $season));
    }
}


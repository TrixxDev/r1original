<?php

namespace App\Http\Controllers;

use App\Helper\Tires;
use App\Models\Quadrbrand;
use App\Models\Quadrtread;
use Cart;
use Illuminate\Http\Request;
use App\Models\Quadr;
use DB;
use View;
use Auth;

class QuadTireController extends Controller
{

    public $brands;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $quadrTiresD1;
    public $quadrTiresD2;
    public $quadrTiresD3;
    public $model = 'Quadr';
    public $availability = [];
    public $filterCount = 0;

    public $cartQty = 2;

    public function __construct(Request $request)
    {
        $this->brands = $this->tires_getBrands();

        $this->quadrTiresD1 = Tires::getQuadrTiresD1();
        $this->quadrTiresD2 = Tires::getQuadrTiresD2();
        $this->quadrTiresD3 = Tires::getQuadrTiresD3();

        ($request->brand == 'Visi') ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;
        ($this->currBrand === NULL) ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;

        ($request->d1 == 'Visi') ? $this->d1 = 'Visi' : $this->d1 = $request->d1;
        ($request->d2 == 'Visi') ? $this->d2 = 'Visi' : $this->d2 = $request->d2;
        ($request->d3 == NULL) ? $this->d3 = 12 : $this->d3 = $request->d3;

        if ($request->d1 == NULL && $this->d1 == NULL) {
          $this->d1 = 25;
        }

        if ($request->d2 == NULL && $this->d2 == NULL) {
          $this->d2 = 8;
        }

        if ($request->d3 == NULL && $this->d3 == NULL) {
          $this->d3 = 12;
        }

        View::share('brands', $this->brands);
        View::share('quadrTiresD1', $this->quadrTiresD1);
        View::share('quadrTiresD2', $this->quadrTiresD2);
        View::share('quadrTiresD3', $this->quadrTiresD3);
        View::share('currBrand', $this->currBrand);
        View::share('d1', $this->d1);
        View::share('d2', $this->d2);
        View::share('d3', $this->d3);
        View::share('filterCount', $this->filterCount);
        View::share('availability', $this->availability);
        View::share('cartQty', $this->cartQty);

    }

    public function index()
    {

        DB::enableQueryLog();

        $tires = Quadr::with('tread')->leftJoin('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
                          ->when($this->d1, function($query) {
                            $query->where('d1', $this->d1);
                          })->when($this->d2, function($query) {
                            $query->where('d2', $this->d2);
                          })->when($this->d3, function($query) {
                            $query->where('d3', $this->d3);
                          })->groupBy('quadr_tires.article')
                          ->where('quadr_tires.visible_users', '<>', 0)
                          ->orderBy('d3', 'ASC')
                          ->orderBy('d1', 'ASC')
                          ->orderBy('d2', 'ASC')
                          ->orderBy('price2', 'DESC')->paginate(80);

//        dd(DB::getQueryLog());


          return view('tires.quadr.index',
              compact('tires')
          );
    }

    public function tires_tread($brand, $tread, $tire)
    {
        $brand = Quadrbrand::where('title', $brand)->first();

        $tread = str_replace('_', '/', $tread);
        $tread = str_replace('$1', '&', $tread);
        $tread = Quadrtread::where('title', $tread)->first();


        $tires = Quadr::selectRaw('quadr_tires.*, quadr_treads.*, quadr_brands.*,
                                                quadr_brands.title as brands_title, quadr_treads.title as treads_title')
            ->join('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
            ->join('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_tires.visible_users', '<>', 0)
            ->where('quadr_brands.title', $brand->title)
            ->where('quadr_treads.title', $tread->title)
            ->orderBy('d3', 'ASC')
            ->orderBy('d1', 'ASC')
            ->orderBy('d2', 'ASC')
            ->get();

        $currTire = Quadr::selectRaw('quadr_tires.*, quadr_treads.*, quadr_brands.*,
                                                quadr_brands.title as brands_title, quadr_treads.title as treads_title')
            ->join('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
            ->join('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_brands.title', $brand->title)
            ->where('quadr_treads.title', $tread->title)
            ->where('quadr_tires.tire_id', $tire)
            ->first();

        $currBrand = Quadrbrand::where('brand_id', $tread->brand_id)->first();

        $currTire->includeStock = true;

        return view('tires.quadr.quadrtread',
            compact('tires', 'currTire', 'currBrand')
        );
    }

    public function tires_ajax(Request $request) {

        $tire = Quadr::with('tread')->selectRaw('quadr_tires.*, quadr_treads.*')
                          ->rightJoin('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
                          ->where('quadr_tires.tire_id', $request->tire_id)
                          ->first();

        if ($request->quantity) {
          $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
        } else {
          $cart = CartController::addProduct($this->model, $tire->tire_id, $this->cartQty);
        }

        $quantity = Cart::count();
        $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
        $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

        echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

  public function splitInput($input) {
    $input = str_replace(',', '.', $input);

    if (preg_match('/^(\d{2})(\d)(\d{2})$/', $input, $matches)) {
      $d1 = $matches[1]; // 25
      $d2 = $matches[2]; // 8
      $d3 = $matches[3]; // 12
    } else if (preg_match('/^(\d{2})(\d{2})(\d{2})$/', $input, $matches)) {
      $d1 = $matches[1]; // 25
      $d2 = $matches[2]; // 10
      $d3 = $matches[3]; // 12
    } else if (preg_match('/^(\d{2})((\d).(\d))(\d{2})$/', $input, $matches)) {
      $d1 = $matches[1]; // 24
      $d2 = $matches[2]; // 9.5
      $d3 = $matches[5]; // 10
    } else if (preg_match('/^(\d{2})((\d{2}).(\d))(\d{2})$/', $input, $matches)) {
      $d1 = $matches[1]; // 23
      $d2 = $matches[2]; // 10.5
      $d3 = $matches[5]; // 12
    } else if (preg_match('/^(\d{2})((\d).(\d))(\d)$/', $input, $matches)) {
      $d1 = $matches[1]; // 16
      $d2 = $matches[2]; // 6.5
      $d3 = $matches[5]; // 8
    } else if (preg_match('/^(\d{2})((\d{2}).(\d))(\d)$/', $input, $matches)) {
      $d1 = $matches[1]; // 25
      $d2 = $matches[2]; // 12.5
      $d3 = $matches[5]; // 1
    } else {
      $d1 = 1;
      $d2 = 1;
      $d3 = 1;
    }

    return compact('d1', 'd2', 'd3');
  }

  public function tires_find(Request $request) {

    DB::enableQueryLog();

    $this->filterCount = 0;

    ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;

    $this->d1 = $d1 = ($this->d1 == 'Visi') ? '' : $request->d1;
    $this->d2 = $d2 = ($this->d2 == 'Visi') ? '' : $request->d2;
    $this->d3 = $d3 = ($this->d3 == 'Visi') ? '' : $request->d3;

    $fastsearch = $request->fastsearch;

    if ($fastsearch) {
      $splited = $this->splitInput($fastsearch);
      $this->d1 = $d1 = $splited['d1'];
      $this->d2 = $d2 = $splited['d2'];
      $this->d3 = $d3 = $splited['d3'];
    }

     if ($request->availability) {
       $this->filterCount += 1;
       $this->availability = $request->availability;
     } else {
       $this->availability = [];
     }

    if ($request->types) {
      $this->filterCount += 1;
      $this->types = $request->types;
    } else {
      $this->types = '';
    }

    if ($request->code) {
      $this->code = $request->code;
      $this->filterCount += 1;
    } else {
      $this->code = '';
    }
    if ($request->fuel) {
      $this->fuel = $request->fuel;
      $this->filterCount += 1;
    } else {
      $this->fuel = '';
    }
    if ($request->wet) {
      $this->wet = $request->wet;
      $this->filterCount += 1;
    } else {
      $this->wet = '';
    }

    $tires = Quadr::select('quadr_tires.*', 'quadr_treads.*', 'quadr_treads.slug as tread_slug', 'quadr_brands.slug as brand_slug')
                    ->join('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
                    ->join('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
                    ->when($this->currBrand, function($query) {
                      $query->where('quadr_brands.slug', \Str::slug($this->currBrand));
                    })->when($this->d1, function($query) {
                      $query->where('d1', $this->d1);
                    })->when($this->d2, function($query) {
                      $query->where('d2', $this->d2);
                    })->when($this->d3, function($query) {
                      $query->where('d3', $this->d3);
                    })->where('quadr_tires.visible_users', '<>', 0)
                      ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                      ->orderBy('price2', 'DESC')
                      ->groupBy('quadr_tires.tire_id')->paginate()->appends($request->query());
//
//    dd(DB::getQueryLog());

    return view('tires.quadr.index',
      ['tires' => $tires, 'filterCount' => $this->filterCount, 'availability' => $this->availability, 'd1' => $d1, 'd2' => $d2, 'd3' => $d3]
    );
  }

  public function get_sizes()
  {
    try {
      $tireSizes = Quadr::select(DB::raw('CONCAT(D1, D2, D3) as tire_size'))
        ->where('quadr_tires.visible_users', '<>', 0)
        ->groupBy('quadr_tires.article')
        ->distinct()
        ->get();

      return response()->json($tireSizes, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function tires_getBrands()
  {
    $brands = [];

    foreach (Quadrbrand::all() as $brand) {
      $treads = Quadrtread::where('brand_id', $brand->brand_id)->get();
      foreach ($treads as $tread) {
        $tire = Quadr::where('make_id', $tread->tread_id)->where('visible_users', '<>', 0)->first();
        if (!$tire) continue;
        $brand_id = $tread->brand_id;
        array_push($brands, $brand_id);
      }
    }

    $brands = array_unique($brands);
    $brands = array_values($brands);
    $brand_list = [];
    foreach ($brands as $brand) {
      $brand = Quadrbrand::where('brand_id', $brand)->first();
      $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
    }

    //      asort($brand_list);
    asort($brand_list, SORT_NATURAL | SORT_FLAG_CASE);

    return $brand_list;
  }

}

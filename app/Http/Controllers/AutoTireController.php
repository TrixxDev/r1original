<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Helper\Tires;
use App\Models\Autobrand;
use App\Models\Autotire;
use App\Models\Autotread;
use Cart;
use Illuminate\Http\Request;
use Route;
use Auth;
use View;
use DB;

class AutoTireController extends Controller
{

    public $brands;
    public $season;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $autoTiresD1;
    public $autoTiresD2;
    public $autoTiresD3;
    public $model = 'Autotire';
    public $tiresSize;
    public $code;
    public $fuel;
    public $wet;
    public $availability;

    public function __construct(Request $request)
    {

      // || strpos(str_replace(url('/'), '', \URL::previous()), 'vasaras-riepas') !== false

        if (strpos(url()->current(), 'vasaras-riepas') !== false) {
            $this->season = 1;
            View::share('season', 'Vasaras riepas');
            View::share('current_url', 'vasaras-riepa');
            View::share('season_title', 'vasaras-riepas');
        } else if (strpos(url()->current(), 'ziemas-riepas') !== false) {
            $this->season = 2;
            View::share('season', 'Ziemas riepas');
            View::share('current_url', 'ziemas-riepa');
            View::share('season_title', 'ziemas-riepas');
        }
        View::share('season_id', $this->season);

        $this->brands = Tires::getAllAutoBrands($this->season);

        $this->autoTiresD1 = Tires::getAutoTiresD1($this->season);
        $this->autoTiresD2 = Tires::getAutoTiresD2($this->season);
        $this->autoTiresD3 = Tires::getAutoTiresD3($this->season);

        ($request->brand == 'Visi') ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;
        ($this->currBrand === NULL) ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;

        ($request->d1 == 'Visi') ? $this->d1 = 'Visi' : $this->d1 = $request->d1;
        ($request->d2 == 'Visi') ? $this->d2 = 'Visi' : $this->d2 = $request->d2;
        ($request->d3 == NULL) ? $this->d3 = 16 : $this->d3 = $request->d3;

        ($request->code) ? $this->code = $request->code : $this->code = [];
        ($request->fuel) ? $this->fuel = $request->fuel : $this->fuel = [];
        ($request->wet) ? $this->wet = $request->wet : $this->wet = [];

        if ($request->d1 == NULL && $this->d1 == NULL) {
            $this->d1 = 205;
        }

        if ($request->d2 == NULL && $this->d2 == NULL) {
            $this->d2 = 55;
        }

//        if ($request->d3 == NULL && $this->d3 == NULL) {
//            $this->d3 = 16;
//        }

        View::share('brands', $this->brands);
        View::share('autoTiresD1', $this->autoTiresD1);
        View::share('autoTiresD2', $this->autoTiresD2);
        View::share('autoTiresD3', $this->autoTiresD3);
        View::share('currBrand', $this->currBrand);
        View::share('d1', $this->d1);
        View::share('d2', $this->d2);
        View::share('d3', $this->d3);
        View::share('code', $this->code);
        View::share('fuel', $this->fuel);
        View::share('wet', $this->wet);
    }

    public function tires() {

        $tires = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                           ->when($this->d1, function($query) {
                               $query->where('d1', $this->d1);
                           })->when($this->d2, function($query) {
                               $query->where('d2', $this->d2);
                           })->when($this->d3, function($query) {
                               $query->where('d3', $this->d3);
                           })->where('auto_treads.season', $this->season)
                           ->where('auto_tires.visible_users', '<>', 0)
                           ->orderBy('d3', 'ASC')
                           ->orderBy('d1', 'ASC')
                           ->orderBy('d2', 'ASC')
                           ->orderBy('price2', 'DESC')
                           ->paginate();

        return view('tires.auto.tires', compact('tires'));
    }

    public function tires_ajax(Request $request) {

        $tire = Autotire::with('tread')->selectRaw('auto_tires.*, auto_treads.*')
                                               ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                               ->where('auto_treads.season', $this->season)
                                               ->where('auto_tires.tire_id', $request->tire_id)
                                               ->where('auto_tires.visible_users', '<>', 0)
                                               ->first();

        if ($request->quantity) {
            $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
        } else {
            $cart = CartController::addProduct($this->model, $tire->tire_id, 4);
        }

        $quantity = Cart::count();
        $total_sum = str_replace([',', '.00'], '', Cart::total());
        $bought = ($request->quantity) ? $request->quantity : 4;

        echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

    public function tires_find(Request $request) {

        DB::enableQueryLog();

        ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;


        ($this->d1 == 'Visi') ? $this->d1 = '' : $this->d1 = $request->d1;
        ($this->d2 == 'Visi') ? $this->d2 = '' : $this->d2 = $request->d2;

        ($request->code) ? $this->code = $request->code : $this->code = '';
        ($request->fuel) ? $this->fuel = $request->fuel : $this->fuel = '';
        ($request->wet) ? $this->wet = $request->wet : $this->wet = '';

        $tires = Autotire::with('tread')->select('auto_tires.*', 'auto_treads.*', 'auto_treads.slug as tread_slug', 'auto_brands.slug as brand_slug')
                          ->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                          ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
                          ->when($this->currBrand, function($query) {
                              $query->where('auto_brands.slug', \Str::slug($this->currBrand));
                          })->when($this->d1, function($query) {
                              $query->where('d1', $this->d1);
                          })->when($this->d2, function($query) {
                              $query->where('d2', $this->d2);
                          })->when($this->d3, function($query) {
                              $query->where('d3', $this->d3);
                          })->when($this->code, function($query) {
                              $query->whereIn('code', $this->code);
                          })->when($this->fuel, function($query) {
                              $query->whereIn('eco', $this->fuel);
                          })->when($this->wet, function($query) {
                              $query->whereIn('wet', $this->wet);
                          })->where('auto_treads.season', $this->season)
                          ->where('auto_tires.visible_users', '<>', 0)
                          ->orderBy('d3', 'ASC')
                          ->orderBy('d1', 'ASC')
                          ->orderBy('d2', 'ASC')
                          ->orderBy('price2', 'DESC')->paginate()->appends($request->query());


//        dd(DB::getQueryLog());
//        dd($tires);

        return view('tires.auto.tires',
            compact('tires')
        );
    }

    public function tires_filter(Request $request) {

      DB::enableQueryLog();

      $this->d1 = $request->d1;
      $this->d2 = $request->d2;
      $this->d3 = $request->d3;

      $where = '`auto_treads`.`season` = ' . $this->season;

      if ($this->d1 !== NULL) {
        $where .= ' AND `auto_tires`.`d1` = ' . $this->d1;
      }
      if ($this->d2 !== NULL) {
        $where .= ' AND `auto_tires`.`d2` = ' . $this->d2;
      }

      $where .= ' AND `auto_tires`.`d3` = ' . $this->d3;

      if ($request->availabilities['green'] == 1) {
        $where .= " AND `auto_tires`.`quantity` > 0";
      }
      if ($request->availabilities['yellow'] == 1) {
        $where .= " AND `auto_stock`.`quantity` > 0";
      }
      if ($request->availabilities['red'] == 1) {
        $where .= ' AND `auto_tires`.`quantity` = 0';
      }

      $tires = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                              ->leftJoin('auto_stock', 'auto_tires.tire_id', '=', 'auto_stock.tire_id')
                                              ->whereRaw($where)
                                              ->orderBy('d3', 'ASC')
                                              ->orderBy('d1', 'ASC')
                                              ->orderBy('d2', 'ASC')
                                              ->orderBy('price2', 'DESC')
                                              ->get();

      dd(DB::getQueryLog());

      $tires_array = [];

      foreach ($tires as $tire) {

        $size = $tire->d1 . '/' . $tire->d2 . 'R' . $tire->d3;

        $tires_array[$size] = $tire;

      }

      return json_encode($tires_array);
    }

    public function tires_search(Request $request) {

        ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;

        $code = $request->code;
        $sql = Autotread::selectRaw('auto_treads.*, auto_treads.title as tread_title')
            ->selectRaw('auto_brands.*, auto_brands.title as brand_title')
            ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.season', $this->season)
            ->where('auto_brands.title', $this->currBrand)
            ->get();
        $makes = [];
        foreach ($sql as $make) {
            $makes[] = $make->tread_id;
        }

        ($this->d1 == 'Visi') ? $this->d1 = '' : $this->d1 = $request->d1;
        ($this->d2 == 'Visi') ? $this->d2 = '' : $this->d2 = $request->d2;

        $tires = Autotire::join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')->when($makes, function($query) use ($makes) {
            $query->whereIn('make_id', $makes);
        })->when($this->d1, function($query) {
            $query->where('d1', $this->d1);
        })->when($this->d2, function($query) {
            $query->where('d2', $this->d2);
        })->when($code, function($query) use ($code){
            $query->where('code', 'LIKE', '%' . $code . '%');
        })->where('d3', $this->d3)
          ->where('auto_treads.season', $this->season)
          ->orderBy('d3', 'ASC')
          ->orderBy('d1', 'ASC')
          ->orderBy('d2', 'ASC')
          ->orderBy('price2', 'DESC')
          ->paginate($this->itemsPerPage)->appends($request->query());

        return view('tires.auto.tires',
            compact('tires', 'code')
        );
    }

    public function tires_tread($brand, $tread, $tire) {

        $brand = Autobrand::where('slug', $brand)->first();

        $tread = Autotread::where('slug', $tread)->first();

        $tires = Autotire::selectRaw('auto_tires.*, auto_treads.*, auto_brands.*,
                                                auto_brands.title as brands_title, auto_treads.title as treads_title')
                                                ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                                ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
                                                ->where('auto_brands.title', $brand->title)
                                                ->where('auto_treads.title', $tread->title)
                                                ->orderBy('d3', 'ASC')
                                                ->orderBy('d1', 'ASC')
                                                ->orderBy('d2', 'ASC')
                                                ->get();

//        $currTire = Autotire::with('tread')->selectRaw('auto_tires.*, auto_treads.*, auto_brands.*,
//                                               auto_brands.title as brands_title, auto_treads.title as treads_title')
//                                               ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
//                                               ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
//                                               ->where('auto_brands.title', $brand->title)
//                                               ->where('auto_treads.title', $tread->title)
//                                               ->where('auto_tires.tire_id', $tire)
//                                               ->first();

        $currTire = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                                   ->where('auto_treads.title', $tread->title)
                                                   ->where('auto_tires.tire_id', $tire)
                                                   ->first();

        $currTire->includeStock = true;

        return view('tires.auto.autotread',
            compact('tires', 'currTire')
        );
    }

    public function add_product_tread()
    {

    }

}

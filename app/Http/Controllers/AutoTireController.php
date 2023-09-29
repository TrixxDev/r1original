<?php

namespace App\Http\Controllers;

use App\Broadcasting\UpdateStockChannel;
use App\Http\Controllers\CartController;
use App\Helper\Tires;
use App\Models\Audit;
use App\Models\Autobrand;
use App\Models\Autotire;
use App\Models\Autotread;
use App\Models\Code;
use App\Models\Motobrand;
use App\Models\Mototread;
use App\Models\Quadrbrand;
use App\Models\Quadrtread;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Route;
use Auth;
use View;


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
    public $lastYear;
    public $type;
    public $code;
    public $fuel;
    public $wet;
    public $availability = [];
    public $code_array = [];
    public $filterCount = 0;

    public $cartQty = 4;

  public function __construct(Request $request)
  {


    // || strpos(str_replace(url('/'), '', \URL::previous()), 'vasaras-riepas') !== false

    if (strpos(url()->current(), 'vasaras-riepas') !== false) {
      $this->season = 1;
      View::share('season', 'Vasaras riepas');
      View::share('current_url', 'vasaras-riepa');
      View::share('season_title', 'vasaras-riepas');
    } else if (strpos(url()->current(), 'ziemas-riepas') !== false || \Request::route()->getName() == 'home') {
      $this->season = 2;
      View::share('season', 'Ziemas riepas');
      View::share('current_url', 'ziemas-riepa');
      View::share('season_title', 'ziemas-riepas');
    }
    View::share('season_id', $this->season);

    $this->brands = $this->tires_getBrands();

    $this->autoTiresD1 = Tires::getAutoTiresSize('d1', $this->season);
    $this->autoTiresD2 = Tires::getAutoTiresSize('d2', $this->season);
    $this->autoTiresD3 = Tires::getAutoTiresSize('d3', $this->season);

    $this->currBrand = ($request->brand == 'Visi') ? 'Visi' : $request->brand;
    $this->currBrand = ($this->currBrand === NULL) ? 'Visi' : $request->brand;

    $this->d1 = ($request->d1 == 'Visi') ? 'Visi' : $request->d1;
    $this->d2 = ($request->d2 == 'Visi') ? 'Visi' : $request->d2;
    $this->d3 = ($request->d3 == NULL) ? 16 : $request->d3;

    $this->types = ($request->types) ? $request->types : [];
    $this->code = ($request->code) ? $request->code : [];
    $this->fuel = ($request->fuel) ? $request->fuel : [];
    $this->wet = ($request->wet) ? $request->wet : [];

    if ($request->d1 == NULL && $this->d1 == NULL) {
      $this->d1 = 205;
    }

    if ($request->d2 == NULL && $this->d2 == NULL) {
      $this->d2 = 55;
    }

    $codes = Code::all();

    foreach ($codes as $code) {
      $this->code_array[$code->name] = $code->explanation;
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
        View::share('types', $this->types);
        View::share('code', $this->code);
        View::share('fuel', $this->fuel);
        View::share('wet', $this->wet);
	      View::share('code_array', $this->code_array);
	      View::share('filterCount', $this->filterCount);
	      View::share('availability', $this->availability);
        View::share('cartQty', $this->cartQty);
  }

  public function tires() {

    $tires = Autotire::leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
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
      ->groupBy('article')
      ->simplePaginate();

    $code_array = $this->code_array;

    return view('tires.auto.tires', compact('tires', 'code_array'));
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
      $cart = CartController::addProduct($this->model, $tire->tire_id, $this->cartQty);
    }

    $quantity = Cart::count();
    //dd(Cart::subTotal());
    $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
    $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
  }

  public function tires_find(Request $request) {

    DB::enableQueryLog();

    $this->filterCount = 0;

    $this->currBrand = ($request->brand == 'Visi') ? '' : $request->brand;

    $this->d1 = ($this->d1 == 'Visi') ? '' : $request->d1;
    $this->d2 = ($this->d2 == 'Visi') ? '' : $request->d2;
    $this->d3 = ($this->d3 == 'Visi') ? '' : $request->d3;

//    if ($request->availability) {
//      $this->filterCount += 1;
//      $this->availability = $request->availability;
//    } else {
//      $this->availability = [];
//    }

    if ($request->types) {
      $this->filterCount += 1;
      $this->types = $request->types;
    } else {
      $this->types = '';
    }

    if ($request->code) {
      $this->code = $request->code;
      $week = date("W");
      if (in_array('CURRYEAR', $this->code)) {
        $pastYear = substr(date('Y', strtotime(date('Y-m-d') . ' -1 year')), -2);
        $currentYear = substr(date('Y'), -2);
        $lastWeek = date('W',strtotime('28th December' . $currentYear)) . substr($currentYear, -2);
        $fromDate = $week . substr($pastYear, -2);
        if (($key = array_search('CURRYEAR', $this->code)) !== false) {
          $this->lastWeek = "";
          $this->lastYear = "BETWEEN $pastYear AND $currentYear";
          unset($this->code[$key]);
        }
      }
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

    $tires = Autotire::selectRaw('`auto_tires`.*, `auto_treads`.*, `auto_treads`.`slug` as `tread_slug`, `auto_brands`.`title` as `brand_name`, `auto_brands`.`slug` as `brand_slug`, SUBSTRING(TRIM(REPLACE(auto_tires.code, "DOT", "")),1,length(TRIM(REPLACE(auto_tires.code, "DOT", "")))-2) as `DotWeek`, RIGHT(TRIM(REPLACE(auto_tires.code, "DOT", "")), 2) as `DotYear`')
      ->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
      ->leftJoin('auto_stock', 'auto_tires.tire_id', '=', 'auto_stock.tire_id')
      ->when($this->currBrand, function($query) {
        $query->where('auto_brands.slug', \Str::slug($this->currBrand));
      })->when($this->d1, function($query) {
        $query->where('d1', $this->d1);
      })->when($this->d2, function($query) {
        $query->where('d2', $this->d2);
      })->when($this->d3, function($query) {
        $query->where('d3', $this->d3);
      })->when($this->types, function($query) {
        $query->whereIn('auto_tires.type', $this->types);
      })->when($this->code, function($query) {
        $query->whereLike('code', $this->code);
      })->when($this->fuel, function($query) {
        $query->whereIn('eco', $this->fuel);
      })->when($this->wet, function($query) {
        $query->whereIn('wet', $this->wet);
      })->where('auto_treads.season', $this->season)
      ->where('auto_tires.visible_users', '<>', 0)
      ->groupBy('auto_tires.tire_id')
      ->when($this->lastYear, function($query) {
        $query->havingRaw('`DotWeek` >= ' . date("W") . ' AND `DotYear` ' . $this->lastYear);
      })->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->orderBy('price2', 'DESC')->paginate()->appends($request->query());

//    $tires = Autotire::distinct()->selectRaw('`auto_tires`.*, `auto_treads`.*, `auto_treads`.`slug` as `tread_slug`, `auto_brands`.`slug` as `brand_slug`, `auto_tires`.*, `auto_treads`.*, `auto_treads`.`slug` as `tread_slug`, `auto_brands`.`slug` as `brand_slug`, SUBSTRING(TRIM(REPLACE(auto_tires.code, "DOT", "")),1,length(TRIM(REPLACE(auto_tires.code, "DOT", "")))-2) as `DotWeek`, RIGHT(TRIM(REPLACE(auto_tires.code, "DOT", "")), 2) as `DotYear`')
//      ->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
//      ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
//      ->leftJoin('auto_stock', 'auto_tires.tire_id', '=', 'auto_stock.tire_id')
//      ->when($this->currBrand, function($query) {
//        $query->where('auto_brands.slug', \Str::slug($this->currBrand));
//      })->when($this->d1, function($query) {
//        $query->where('d1', $this->d1);
//      })->when($this->d2, function($query) {
//        $query->where('d2', $this->d2);
//      })->when($this->d3, function($query) {
//        $query->where('d3', $this->d3);
//      })->when($this->types, function($query) {
//        $query->whereIn('auto_tires.type', $this->types);
//      })->when($this->code, function($query) {
//        $query->whereLike('code', $this->code);
//      })->when($this->fuel, function($query) {
//        $query->whereIn('eco', $this->fuel);
//      })->when($this->wet, function($query) {
//        $query->whereIn('wet', $this->wet);
//      })->where('auto_treads.season', $this->season)
//      ->where('auto_tires.visible_users', '<>', 0)
//      ->groupBy('auto_tires.tire_id')
//      ->when($this->lastYear, function($query) {
//        $query->havingRaw('`DotWeek` >= ' . date("W") . ' AND `DotYear` ' . $this->lastYear);
//      })->orderBy('d3', 'ASC')
//      ->orderBy('d1', 'ASC')
//      ->orderBy('d2', 'ASC')
//      ->orderBy('price2', 'DESC')->get();

//    dd(DB::getQueryLog());

    return view('tires.auto.tires',
            ['tires' => $tires, 'filterCount' => $this->filterCount, ]
    );
//    'availability' => $this->availability
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

    //dd(DB::getQueryLog());

    $tires_array = [];

    foreach ($tires as $tire) {

      $size = $tire->d1 . '/' . $tire->d2 . 'R' . $tire->d3;

      $tires_array[$size] = $tire;

    }

    return json_encode($tires_array);
  }

  public function tires_search(Request $request) {

    $this->currBrand = ($request->brand == 'Visi') ? '' : $request->brand;

    $code = $request->code;
    $sql = Autotread::selectRaw('auto_treads.*')
                      ->selectRaw('auto_brands.*, auto_brands.title as brand_title')
                      ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
                      ->where('auto_treads.season', $this->season)
                      ->where('auto_brands.title', $this->currBrand)
                      ->get();
    $makes = [];
    foreach ($sql as $make) {
      $makes[] = $make->tread_id;
    }

    $this->d1 = ($this->d1 == 'Visi') ? '' : $request->d1;
    $this->d2 = ($this->d2 == 'Visi') ? '' : $request->d2;

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
      ->paginate()->appends($request->query());

    return view('tires.auto.tires',
      compact('tires', 'code')
    );
  }

  public function tires_tread($brand, $tread, $tire) {

    DB::enableQueryLog();

    $brand = Autobrand::where('slug', $brand)->first();

    $tires = Autotire::selectRaw('auto_tires.*, auto_treads.*, auto_brands.*,
                                                auto_brands.title as brands_title')
      ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
      ->where('auto_brands.title', $brand->title)
      ->where('auto_treads.t_title', str_replace('_', '/', $tread))
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->get();

    $currTire = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->where('auto_treads.t_title', str_replace('_', '/', $tread))
      ->where('auto_tires.tire_id', $tire)
      ->first();

    $currBrand = Autobrand::where('brand_id', $currTire->brand_id)->first();

    $currTire->includeStock = true;

    return view('tires.auto.autotread',
      compact('tires', 'currTire', 'currBrand')
    );
  }

  public function tires_getBrands()
  {
    $brands = Autobrand::join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
      ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
      ->where('auto_tires.visible_users', '<>', 0)
      ->where('auto_treads.season', $this->season)
      ->distinct()
      ->pluck('auto_brands.title', 'auto_brands.brand_id')
      ->sort(SORT_NATURAL | SORT_FLAG_CASE);

    return $brands->all();
  }

}

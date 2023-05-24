<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Helper\Tires;
use App\Models\Bigbrand;
use App\Models\Bigtire;
use App\Models\Bigtread;
use App\Models\Code;
use Cart;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use View;

class BigTireController extends Controller
{

    public $brands;
    public $season;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $bigTiresD1;
    public $bigTiresD2;
    public $bigTiresD3;
    public $model = 'Bigtire';
    public $tiresSize;
    public $tire_types;
    public $tire_implementions;
    public $tire_axis;
    public $tire_conditions;
    public $code;
    public $code_array = [];
    public $type;
    public $types;
    public $implemention;
    public $implementions;
//    public $axi;
//    public $axis;
//    public $condition;
//    public $conditions;
    public $surface;
    public $availability;
    public $filterCount = 0;

    public $cartQty = 1;

    public function __construct(Request $request)
    {
      $this->brands = $this->tires_getBrands();

      $this->bigTiresD1 = Tires::getBigTiresD1();
      $this->bigTiresD2 = Tires::getBigTiresD2();
      $this->bigTiresD3 = Tires::getBigTiresD3();

      $this->currBrand = ($request->brand == 'Visi') ? 'Visi' : $request->brand;
      $this->currBrand = ($this->currBrand === NULL) ? 'Visi' : $request->brand;

      $this->d1 = ($request->d1 == 'Visi') ? 'Visi' : $request->d1;
      $this->d2 = ($request->d2 == 'Visi') ? 'Visi' : $request->d2;
      $this->d3 = ($request->d3 == 'Visi') ? 'Visi' : $request->d3;
//      $this->type = ($request->type == 'Visi') ? '' : $request->type;
//      $this->implement = ($request->implement == 'Visi') ? '' : $request->implement;
//      $this->axle = ($request->axle == 'Visi') ? '' : $request->axle;
//      $this->condition = ($request->condition == 'Visi') ? '' : $request->condition;

//      ($request->tire_type) ? $this->tire_type = $request->tire_type : $this->tire_type = [];
//      ($request->axle) ? $this->axle = $request->axle : $this->axle = [];
//      ($request->surface) ? $this->surface = $request->surface : $this->surface = [];

      if ($request->d1 == NULL && $this->d1 == NULL) {
        $this->d1 = 10;
      }

      if ($request->d2 == NULL && $this->d2 == NULL) {
        $this->d2 = '';
      }

      if ($request->d3 == NULL && $this->d3 == NULL) {
          $this->d3 = 16.5;
      }

      $codes = Code::all();

      foreach ($codes as $code) {
        $this->code_array[$code->name] = $code->explanation;
      }

      View::share('brands', $this->brands);
      View::share('bigTiresD1', $this->bigTiresD1);
      View::share('bigTiresD2', $this->bigTiresD2);
      View::share('bigTiresD3', $this->bigTiresD3);
      View::share('currBrand', $this->currBrand);
      View::share('d1', $this->d1);
      View::share('d2', $this->d2);
      View::share('d3', $this->d3);
      View::share('types', []);
      View::share('implementions', []);
      View::share('code_array', $this->code_array);
      View::share('tire_types', $this->getTireOptions()['tire_types']);
      View::share('tire_implementions', $this->getTireOptions()['tire_implementions']);
//      View::share('tire_axis', $this->getTireOptions()['tire_axis']);
//      View::share('tire_conditions', $this->getTireOptions()['tire_conditions']);
      View::share('filterCount', $this->filterCount);
      View::share('cartQty', $this->cartQty);
    }

    public function index()
    {

      $tires = Bigtire::with('tread')->leftJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
        ->when($this->d1, function($query) {
          $query->where('d1', $this->d1);
        })->when($this->d2, function($query) {
          $query->where('d2', $this->d2);
        })->when($this->d3, function($query) {
          $query->where('d3', $this->d3);
        })->where('visible_users', '<>', 0)
        ->orderBy('quantity', 'DESC')
        ->orderBy('price3', 'DESC')
        ->paginate();

      return view('tires.industrial.index',
                  compact('tires')
      );
    }

    public function tires_search(Request $request)
    {
      DB::enableQueryLog();

//      dd($request);
      $this->currBrand = ($request->brand == 'Visi') ? '' : $request->brand;

      $code = $request->code;
      $sql = Bigtread::selectRaw('bigtire_treads.*, bigtire_treads.title as tread_title')
                        ->selectRaw('bigtire_brands.*, bigtire_brands.title as brand_title')
                        ->leftJoin('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
                        ->where('bigtire_brands.title', $this->currBrand)
                        ->get();
      $makes = [];
      foreach ($sql as $make) {
        $makes[] = $make->tread_id;
      }

      $this->d1 = ($this->d1 == 'Visi') ? '' : $request->d1;
      $this->d2 = ($this->d2 == 'Visi') ? '' : $request->d2;
      $this->d3 = ($this->d3 == 'Visi') ? '' : $request->d3;

      $types = $this->type = ($request->type) ? $request->type : [];
      $implementions = $this->implementions = ($request->implemention) ? $request->implemention : [];
//      $axis = $this->axis = ($request->axi) ? $request->axi : [];
//      $conditions = $this->conditions = ($request->condition) ? $request->condition : [];

      $tires = Bigtire::join('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
      ->when($makes, function($query) use ($makes) {
        $query->whereIn('make_id', $makes);
      })->when($this->d1, function($query) {
        $query->where('d1', $this->d1);
      })->when($this->d2, function($query) {
        $query->where('d2', $this->d2);
      })->when($this->d3, function($query) {
          $query->where('d3', $this->d3);
      })->when($this->type, function($query) {
          $query->whereIn('big_tires.type', $this->type);
      })->when($this->implemention, function($query) {
          $query->whereIn('big_tires.implemention', $this->implemention);
      })->where('visible_users', '<>', 0)
        ->orderBy('quantity', 'DESC')
        ->orderBy('price3', 'DESC')
        ->paginate()->appends($request->query());

//      dd(DB::getQueryLog(), $tires);

//      ->when($this->axis, function($query) {
//      $query->whereIn('axis_bus', $this->axis)->orWhereIn('axis_truck', $this->axis);
//    })->when($this->conditions, function($query) {
//      $query->whereIn('conditions_bus', $this->conditions)->orWhereIn('conditions_truck', $this->conditions);
//    })

//      ->when($code, function($query) use ($code){
//      $query->where('code', 'LIKE', '%' . $code . '%');
//    })

      return view('tires.industrial.index',
        compact('tires', 'code', 'types', 'implementions')
      );
    }

    public function tires_tread($brand, $tread, $tire) {

      $brand = Bigbrand::where('title', $brand)->first();

      $tires = Bigtire::selectRaw('big_tires.*, bigtire_treads.*, bigtire_brands.*, bigtire_brands.title as brands_title, bigtire_treads.title as treads_title')
                        ->join('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                        ->join('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
                        ->where('bigtire_brands.title', $brand->title)
                        ->where('bigtire_treads.title', str_replace('_', '/', $tread))
                        ->where('visible_users', '<>', 0)
                        ->orderBy('d3', 'ASC')
                        ->orderBy('d1', 'ASC')
                        ->orderBy('d2', 'ASC')
                        ->orderBy('price3', 'ASC')
                        ->get();

      $currTire = Bigtire::with('tread')->leftJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                                                ->where('bigtire_treads.title', str_replace('_', '/', $tread))
                                                ->where('big_tires.tire_id', $tire)
                                                ->first();

      $currTire->includeStock = true;

      return view('tires.industrial.industrialtread',
        compact('tires', 'currTire', 'brand', 'tread')
      );
    }

    public function tires_ajax(Request $request) {

      $tire = Bigtire::with('tread')->selectRaw('big_tires.*, bigtire_treads.*')
        ->rightJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
        ->where('big_tires.tire_id', $request->tire_id)
        ->first();

      if ($request->quantity) {
        $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
      } else {
        $cart = CartController::addProduct($this->model, $tire->tire_id, $this->cartQty);
      }

      $quantity = Cart::count();
      $total_sum = str_replace([',', '.00'], '', Cart::total());
      $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

      echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

    public function tires_getBrands()
    {
      $brands = [];

      foreach (Bigbrand::all() as $brand) {
        $treads = Bigtread::where('brand_id', $brand->brand_id)->get();
        foreach ($treads as $tread) {
          $tire = Bigtire::where('make_id', $tread->tread_id)->where('visible_users', '<>', 0)->first();
          if (!$tire) continue;
          $brand_id = $tread->brand_id;
          array_push($brands, $brand_id);
        }
      }

      $brands = array_unique($brands);
      $brands = array_values($brands);
      $brand_list = [];
      foreach ($brands as $brand) {
        $brand = Bigbrand::where('brand_id', $brand)->first();
        $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
      }

  //      asort($brand_list);
      asort($brand_list, SORT_NATURAL | SORT_FLAG_CASE);

      return $brand_list;
    }

  public function getTireOptions()
  {
    $tire_type = [];
    $tire_implemention = [];
    $tire_axis = [];
    $tire_condition = [];

    foreach (Bigtire::all() as $tire) {
      array_push($tire_type, $tire->type);
      array_push($tire_implemention, $tire->implemention);
      array_push($tire_axis, $tire->axis);
      array_push($tire_condition, $tire->conditions);
    }

    $tire_type = array_unique($tire_type);
    $tire_type = array_values($tire_type);
    $tire_type = array_filter($tire_type);

    $tire_implemention = array_unique($tire_implemention);
    $tire_implemention = array_values($tire_implemention);
    $tire_implemention = array_filter($tire_implemention);

    $tire_axis = array_unique($tire_axis);
    $tire_axis = array_values($tire_axis);
    $tire_axis = array_filter($tire_axis);

    $tire_condition = array_unique($tire_condition);
    $tire_condition = array_values($tire_condition);
    $tire_condition = array_filter($tire_condition);

    asort($tire_type, SORT_NATURAL | SORT_FLAG_CASE);
    asort($tire_implemention, SORT_NATURAL | SORT_FLAG_CASE);
    asort($tire_axis, SORT_NATURAL | SORT_FLAG_CASE);
    asort($tire_condition, SORT_NATURAL | SORT_FLAG_CASE);

    return [
      'tire_types' => $tire_type,
      'tire_implementions' => $tire_implemention,
      'tire_axis' => $tire_axis,
      'tire_conditions' => $tire_condition,
    ];
  }

}

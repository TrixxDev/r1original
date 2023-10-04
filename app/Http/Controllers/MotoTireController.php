<?php

namespace App\Http\Controllers;

use App\Helper\Tires;
use Illuminate\Http\Request;
use App\Models\Moto;
use App\Models\Motobrand;
use App\Models\Mototread;
use App\Models\Code;
use Cart;
use Illuminate\Support\Facades\DB;
use View;
use Auth;

class MotoTireController extends Controller
{

    public $brands;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $motoTiresD1;
    public $motoTiresD2;
    public $motoTiresD3;
    public $model = 'Moto';
    public $type;
    public $availability = [];
    public $code_array = [];
    public $filterCount = 0;

    public $cartQty = 1;

    public function __construct(Request $request)
    {
        $this->brands = $this->tires_getBrands();

        $this->motoTiresD1 = Tires::getMotoTiresD1();
        $this->motoTiresD2 = Tires::getMotoTiresD2();
        $this->motoTiresD3 = Tires::getMotoTiresD3();

        ($request->brand == 'Visi') ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;
        ($this->currBrand === NULL) ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;

        ($request->d1 == 'Visi') ? $this->d1 = 'Visi' : $this->d1 = $request->d1;
        ($request->d2 == 'Visi') ? $this->d2 = 'Visi' : $this->d2 = $request->d2;
        ($request->d3 == NULL) ? $this->d3 = 17 : $this->d3 = $request->d3;

        ($request->type) ? $this->type = $request->type : $this->type = [];

        if ($request->d1 == NULL && $this->d1 == NULL) {
          $this->d1 = 120;
        }

        if ($request->d2 == NULL && $this->d2 == NULL) {
          $this->d2 = 70;
        }

        if ($request->d3 == NULL && $this->d3 == NULL) {
          $this->d3 = 17;
        }

	      $codes = Code::all();

        foreach ($codes as $code) {
            $this->code_array[$code->name] = $code->explanation;
        }

        View::share('brands', $this->brands);
        View::share('motoTiresD1', $this->motoTiresD1);
        View::share('motoTiresD2', $this->motoTiresD2);
        View::share('motoTiresD3', $this->motoTiresD3);
        View::share('currBrand', $this->currBrand);
        View::share('d1', $this->d1);
        View::share('d2', $this->d2);
        View::share('d3', $this->d3);
        View::share('type', $this->type);
        View::share('types', (new Moto)->types());
	      View::share('code_array', $this->code_array);
        View::share('filterCount', $this->filterCount);
        View::share('availability', $this->availability);
        View::share('cartQty', $this->cartQty);
    }

    public function index()
    {
//        $tires = Moto::leftJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
//            ->orderBy('price2', 'DESC')->get();

        DB::enableQueryLog();

        $tires = Moto::with('tread')->leftJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                                            ->when($this->d1, function($query) {
                                              $query->where('d1', $this->d1);
                                            })->when($this->d2, function($query) {
                                              $query->where('d2', $this->d2);
                                            })->when($this->d3, function($query) {
                                              $query->where('d3', $this->d3);
                                            })->groupBy('moto_tires.article')
                                            ->where('moto_tires.visible_users', '<>', 0)
                                            ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                                            ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                                            ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                                            ->orderBy('d4', 'ASC')
                                            ->orderBy('price2', 'DESC')->paginate();

        return view('tires.moto.index',
            compact('tires')
        );
    }

    public function tires_tread($brand, $tread, $tire)
    {
        $brand = Motobrand::where('title', $brand)->first();

        $tread = str_replace('_', '/', $tread);
        $tread = Mototread::where('title', $tread)->first();

        $tires = Moto::selectRaw('moto_tires.*, moto_treads.*, moto_brands.*,
                                  moto_brands.title as brands_title, moto_treads.title as treads_title')
                                  ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                                  ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                                  ->where('moto_tires.visible_users', '<>', 0)
                                  ->where('moto_brands.title', $brand->title)
                                  ->where('moto_treads.title',  $tread->title)
                                  ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                                  ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                                  ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                                  ->orderBy('d4', 'ASC')
                                  ->get();

        $currTire = Moto::selectRaw('moto_tires.*, moto_treads.*, moto_brands.*,
                                 moto_brands.title as brands_title, moto_treads.title as treads_title')
                                 ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                                 ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                                 ->where('moto_tires.visible_users', '<>', 0)
                                 ->where('moto_brands.title', $brand->title)
                                 ->where('moto_treads.title', $tread->title)
                                 ->where('moto_tires.tire_id', $tire)
                                 ->first();

        $currBrand = Motobrand::where('brand_id', $currTire->brand_id)->first();

	//dd($tire);
	//$stock = DB::table('moto_stock')->where('tire_id', $currTire->tire_id)->first();
        $currTire->includeStock = true;

        return view('tires.moto.mototread',
            compact('tires', 'currTire', 'currBrand')
        );
    }

    public function tires_ajax(Request $request) {

        $tire = Moto::query()->with('tread')->selectRaw('moto_tires.*, moto_tires.comment as tire_comment, moto_treads.*')
                      ->rightJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                      ->where('moto_tires.tire_id', $request->tire_id)
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

    $europeanPattern = '/^(\d{3})(\d{2})(\d{2})$/';

    $fractionalPattern = '/^([\d.]+)(\d{2})$/';

    $americanPattern = '/^([A-Z]+\d{2})(\d{2})$/';

    if (preg_match($europeanPattern, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = $matches[2];
      $d3 = $matches[3];
    } elseif (preg_match($fractionalPattern, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = '';
      $d3 = $matches[2];
    } elseif (preg_match($americanPattern, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = '';
      $d3 = $matches[2];
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

      $types = (new Moto)->types();

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

      if ($request->type) {
        $this->filterCount += 1;
        $this->type = $request->type;
      } else {
        $this->type = '';
      }

      $tires = Moto::select('moto_tires.*', 'moto_treads.*', 'moto_treads.slug as tread_slug', 'moto_brands.slug as brand_slug')
                    ->leftJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                    ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                    ->when($this->currBrand, function($query) {
                      $query->where('moto_brands.slug', \Str::slug($this->currBrand));
                    })->when($this->d1, function($query) {
                      $query->where('d1', $this->d1);
                    })->when($this->d2, function($query) {
                      $query->where('d2', $this->d2);
                    })->when($this->type, function($query) {
                      $query->whereIn('moto_tires.type', $this->type);
                    })->when($this->d3, function($query) {
                      $query->where('d3', $this->d3);
                    })->where('moto_tires.visible_users', '<>', 0)
                      ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                      ->orderBy('d4', 'ASC')
                      ->orderBy('price2', 'DESC')
                      ->groupBy('moto_tires.tire_id')->paginate()->appends($request->query());


//      dd(DB::getQueryLog());

      return view('tires.moto.index',
        ['tires' => $tires, 'filterCount' => $this->filterCount, 'availability' => $this->availability, 'd1' => $d1, 'd2' => $d2, 'd3' => $d3]
      );
  }

  public function get_sizes()
  {
    try {
      $tireSizes = Moto::select(DB::raw('CONCAT(D1, D2, D3) as tire_size'))
        ->where('moto_tires.visible_users', '<>', 0)
        ->groupBy('moto_tires.article')
        ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
        ->distinct()
        ->get()
        ->filter(function($value) {
          return $value->tire_size != null;
        });


      return response()->json($tireSizes, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function tires_getBrands()
  {
    $brands = [];

    foreach (Motobrand::all() as $brand) {
      $treads = Mototread::where('brand_id', $brand->brand_id)->get();
      foreach ($treads as $tread) {
        $tire = Moto::where('make_id', $tread->tread_id)->where('visible_users', '<>', 0)->first();
        if (!$tire) continue;
        $brand_id = $tread->brand_id;
        array_push($brands, $brand_id);
      }
    }

    $brands = array_unique($brands);
    $brands = array_values($brands);
    $brand_list = [];
    foreach ($brands as $brand) {
      $brand = Motobrand::where('brand_id', $brand)->first();
      $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
    }

    //      asort($brand_list);
    asort($brand_list, SORT_NATURAL | SORT_FLAG_CASE);

    return $brand_list;
  }

}

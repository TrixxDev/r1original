<?php

namespace App\Http\Controllers;

use App\Helper\Tires;
use App\Models\Quadr;
use Illuminate\Http\Request;
use App\Models\Moto;
use App\Models\Motobrand;
use App\Models\Mototread;
use App\Models\Code;
use Cart;
use View;
use Auth;
use DB;

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
    public $availability;
    public $code_array = [];

    public function __construct(Request $request)
    {
        $this->brands = Tires::getAllMotoBrands();

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
                                            })->where('moto_tires.visible_users', '<>', 0)
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
                                  ->orderby('d4', 'ASC')
                                  ->where('moto_treads.title',  $tread->title)
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
          $cart = CartController::addProduct($this->model, $tire->tire_id, 1);
        }

        $quantity = Cart::count();
        $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
        $bought = ($request->quantity) ? $request->quantity : 1;

        echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

  public function tires_find(Request $request) {

      DB::enableQueryLog();

      ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;

      $types = (new Moto)->types();

      ($this->d1 == 'Visi') ? $this->d1 = '' : $this->d1 = $request->d1;
      ($this->d2 == 'Visi') ? $this->d2 = '' : $this->d2 = $request->d2;

      if ($request->type) {
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
                    })->where('d3', $this->d3)
                      ->where('moto_tires.visible_users', '<>', 0)
                      ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                      ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                      ->orderBy('d4', 'ASC')
                      ->orderBy('price2', 'DESC')
                      ->paginate()->appends($request->query());


//      dd(DB::getQueryLog());

      return view('tires.moto.index',
        compact('tires')
      );
  }

}

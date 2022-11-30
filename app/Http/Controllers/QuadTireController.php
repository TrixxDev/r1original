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
    public $availability;

    public function __construct(Request $request)
    {
        $this->brands = Tires::getAllQuadrBrands();

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
                          })->where('quadr_tires.visible_users', '<>', 0)
                          ->orderBy('d3', 'ASC')
                          ->orderBy('d1', 'ASC')
                          ->orderBy('d2', 'ASC')
                          ->orderBy('price2', 'DESC')->paginate();

//        dd(DB::getQueryLog());


          return view('tires.quadr.index',
              compact('tires')
          );
    }

    public function tires_tread($brand, $tread, $tire)
    {
        $brand = Quadrbrand::where('title', $brand)->first();

        $tread = str_replace('_', '/', $tread);
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
          $cart = CartController::addProduct($this->model, $tire->tire_id, 2);
        }

        $quantity = Cart::count();
        $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
        $bought = ($request->quantity) ? $request->quantity : 2;

        echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

  public function tires_find(Request $request) {

    DB::enableQueryLog();

    ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;

    ($this->d1 == 'Visi') ? $this->d1 = '' : $this->d1 = $request->d1;
    ($this->d2 == 'Visi') ? $this->d2 = '' : $this->d2 = $request->d2;
    ($this->d3 == 'Visi') ? $this->d3 = '' : $this->d3 = $request->d3;

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
                      ->paginate()->appends($request->query());
//
//    dd(DB::getQueryLog());

    return view('tires.quadr.index',
      compact('tires')
    );
  }

}

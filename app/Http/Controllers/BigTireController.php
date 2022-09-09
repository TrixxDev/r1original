<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Helper\Tires;
use App\Models\Bigbrand;
use App\Models\Bigtire;
use App\Models\Bigtread;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Http\Request;
use Auth;
use View;
use DB;

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
    public $itemsPerPage = 50;
    public $code;
    public $axle;
    public $surface;
    public $availability;

    public function __construct(Request $request)
    {
      $this->brands = Tires::getAllBigBrands();

      $this->bigTiresD1 = Tires::getBigTiresD1();
      $this->bigTiresD2 = Tires::getBigTiresD2();
      $this->bigTiresD3 = Tires::getBigTiresD3();

      ($request->brand == 'Visi') ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;
      ($this->currBrand === NULL) ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;

      ($request->d1 == 'Visi') ? $this->d1 = 'Visi' : $this->d1 = $request->d1;
      ($request->d2 == 'Visi') ? $this->d2 = 'Visi' : $this->d2 = $request->d2;
      ($request->d3 == NULL) ? $this->d3 = 16.5 : $this->d3 = $request->d3;

      ($request->code) ? $this->code = $request->code : $this->code = [];
      ($request->axle) ? $this->axle = $request->axle : $this->axle = [];
      ($request->surface) ? $this->surface = $request->surface : $this->surface = [];

      if ($request->d1 == NULL && $this->d1 == NULL) {
        $this->d1 = 10;
      }

      if ($request->d2 == NULL && $this->d2 == NULL) {
        $this->d2 = '';
      }

      if ($request->d3 == NULL && $this->d3 == NULL) {
          $this->d3 = 16.5;
      }

      View::share('brands', $this->brands);
      View::share('bigTiresD1', $this->bigTiresD1);
      View::share('bigTiresD2', $this->bigTiresD2);
      View::share('bigTiresD3', $this->bigTiresD3);
      View::share('currBrand', $this->currBrand);
      View::share('d1', $this->d1);
      View::share('d2', $this->d2);
      View::share('d3', $this->d3);
      View::share('code', $this->code);
      View::share('axle', $this->axle);
      View::share('surface', $this->surface);
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
                                      })->where('visible_users', 1)
                                      ->where('visible_list', 1)
                                      ->orderBy('d3', 'ASC')
                                      ->orderBy('d1', 'ASC')
                                      ->orderBy('d2', 'ASC')
                                      ->orderBy('price2', 'DESC')->paginate($this->itemsPerPage);

      return view('tires.industrial.index',
                  compact('tires')
      );
    }

    public function tires_search(Request $request)
    {
      ($request->brand == 'Visi') ? $this->currBrand = '' : $this->currBrand = $request->brand;

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

      ($this->d1 == 'Visi') ? $this->d1 = '' : $this->d1 = $request->d1;
      ($this->d2 == 'Visi') ? $this->d2 = '' : $this->d2 = $request->d2;

      $tires = Bigtire::join('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')->when($makes, function($query) use ($makes) {
        $query->whereIn('make_id', $makes);
      })->when($this->d1, function($query) {
        $query->where('d1', $this->d1);
      })->when($this->d2, function($query) {
        $query->where('d2', $this->d2);
      })->where('d3', $this->d3)
        ->where('visible_users', 1)
        ->where('visible_list', 1)
        ->orderBy('d3', 'ASC')
        ->orderBy('d1', 'ASC')
        ->orderBy('d2', 'ASC')
        ->orderBy('price2', 'DESC')
        ->paginate($this->itemsPerPage);

//      ->when($code, function($query) use ($code){
//      $query->where('code', 'LIKE', '%' . $code . '%');
//    })

      return view('tires.industrial.index',
        compact('tires', 'code')
      );
    }

    public function big_tires_tread($brand, $tread, $tire) {

      $brand = Bigbrand::where('slug', $brand)->first();

      $tread = Bigtread::where('slug', $tread)->first();

      $tires = Bigtire::selectRaw('big_tires.*, bigtire_treads.*, bigtire_brands.*, bigtire_brands.title as brands_title, bigtire_treads.title as treads_title')
                        ->join('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                        ->join('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
                        ->where('bigtire_brands.title', $brand->title)
                        ->where('bigtire_treads.title', $tread->title)
                        ->where('big_tires.visible_users', 1)
                        ->where('big_tires.visible_list', 1)
                        ->orderBy('d3', 'ASC')
                        ->orderBy('d1', 'ASC')
                        ->orderBy('d2', 'ASC')
                        ->get();

      $currTire = Bigtire::with('tread')->leftJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                                                ->where('bigtire_treads.title', $tread->title)
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
        $cart = CartController::addProduct($this->model, $tire->tire_id, 4);
      }

      $cartObj = new Cart();
      $quantity = $cartObj->count();
      $total_sum = str_replace([',', '.00'], '', $cartObj->total());
      $bought = ($request->quantity) ? $request->quantity : 4;

      echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

}

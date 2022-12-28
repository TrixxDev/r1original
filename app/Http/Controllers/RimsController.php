<?php

namespace App\Http\Controllers;

use App\Models\FilterCars;
use App\Models\FilterSizes;
use App\Models\FilterModels;
use App\Models\Rim;
use App\Models\Rimbrand;
use App\Models\Rimmake;
use Dflydev\DotAccessData\Data;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class RimsController extends Controller
{

  public $currentCar;
  public $currentModel;
  public $currentR1;
  public $currentR2;
  public $currentR;

  public $currentForm;

  public $currentSkr;
  public $currentPcd;
  public $currentEt;

  public $model = 'Rim';

  public $models;

  public function __construct(Request $request)
  {
    $this->currentCar = ($request->car) ? $request->car : '';
    $this->currentModel = ($request->model) ? $request->model : '';
    $this->currentR1 = ($request->r1) ? $request->r1 : '';
    $this->currentR2 = ($request->r2) ? $request->r2 : '';
    $this->currentR = $this->currentR1;

    $this->currentForm = ($request->currentForm == 1) ? 1 : 2;

    $this->currentSkr = ($request->skr) ? $request->skr : '';
    $this->currentPcd = ($request->pcd) ? $request->pcd : '';
    $this->currentEt = ($request->et) ? $request->et : '';

    if (($this->currentSkr !== false)||($this->currentPcd !== false)||($this->currentEt !== false)) {
      $this->currentCar = -1;
      $this->currentModel = -1;
      if ($this->currentR2 !== false) $this->currentR = $this->currentR2;
    }

    if ($this->currentCar === -1) $this->currentModel = false;

    $pcdQ = FilterSizes::distinct('pcd')->where('pcd', '>', 0)->orderByRaw('cast(pcd as decimal(6,2)) ASC')->get();
    $pcdOpt = '';
    foreach ($pcdQ as $item) {
      $pcdOpt .= '<option value="' . $item->size_id . '"' . (($this->currentR == $item->size_id) ? ' selected="selected"' : '') . '>' . $item->size_id . '';
    }

    $dQ = FilterSizes::distinct('r')->whereRaw('cast(r as decimal(6,2)) > 0')->orderByRaw('cast(r as decimal(6,2)) ASC')->get();
    $dOpt = '';
    foreach ($dQ as $item) {
      $dOpt .= '<option value="' . $item->size_id . '"' . (($this->currentR == $item->size_id) ? ' selected="selected"' : '') . '>' . $item->size_id . '';
    }

    $skrQ = FilterSizes::distinct('skr')->whereRaw('cast(skr as decimal(6,2)) > 0')->orderByRaw('cast(skr as decimal(6,2)) ASC')->get();
    $skrOpt = '';
    foreach ($skrQ as $item) {
      $skrOpt .= '<option value="' . $item->size_id . '"' . (($this->currentR == $item->size_id) ? ' selected="selected"' : '') . '>' . $item->size_id . '';
    }

    $brandQ = FilterCars::orderBy('title', 'asc')->get();
    $brandOpt = '';
    foreach ($brandQ as $item) {
      $brandOpt .= '<a rel="nofollow" class="select-list" data-id="' . $item->car_id . '" id="' . strtoupper($item->title) . '">' . strtoupper($item->title) . '</a>';
    }

    View::share('brandOpt', $brandOpt);
    View::share('currentCar', $this->currentCar);

  }

  public function autorims()
  {

    // THESE HARDCODED VALUES SHOULD BE REPLACED WITH DATA FROM API
    $makes = ['Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley', 'BMW', 'Cadillac', 'Chevrolet (also form.Daewoo)', 'Chrysler', 'Citroen', 'Dacia', 'Daewoo', 'Daihatsu', 'Dodge', 'DR', 'Ferrari', 'Fiat', 'Ford', 'Great Wall Motor', 'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu', 'Iveco', 'Jaguar', 'Jeep', 'Kia', 'Lada', 'Lamborghini', 'Lancia', 'Land Rover', 'Lexus', 'Lincoln', 'Martin Motors', 'Maserati', 'Mazda', 'Mercedes Benz', 'MG', 'Mini', 'Mitsubishi', 'Nissan', 'Opel', 'Peugeot', 'Pontiac', 'Porsche', 'Renault', 'Rover', 'Saab', 'Seat', 'Shuanghuan', 'Skoda', 'Smart', 'SsangYong', 'Subaru', 'Suzuki', 'Toyota', 'Volkswagen', 'Volvo'];
    $models = ['Acura', 'Aiways', 'Aixam', 'Alfa Romeo', 'Alpine', 'ARO', 'Aston Martin', 'Audi', 'BAIC', 'Bentley', 'BMW', 'BMW Alpina', 'Borgward', 'Brilliance', 'Bugatti', 'Buick', 'BYD', 'Cadillac', 'Changan', 'Chery', 'Chevrolet', 'Chrysler', 'Citroën', 'Cupra', 'Dacia', 'Daewoo', 'Daihatsu', 'Datsun', 'Dodge', 'Dongfeng', 'DS', 'e.GO', 'Eagle', 'Exeed', 'FAW', 'Ferrari', 'Fiat', 'Fisker', 'Force', 'Ford', 'Foton', 'GAC', 'GAZ', 'Geely', 'Genesis', 'GEO', 'GMC', 'Great Wall (GWM)', 'Haval', 'Hindustan', 'Holden', 'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu', 'Iveco', 'JAC', 'Jaguar', 'Jeep', 'Jetour', 'Jinbei', 'JMC', 'Keyton', 'Kia', 'King Long', 'LADA', 'Lamborghini', 'Lancia', 'Land Rover', 'Landwind', 'LDV', 'LEVC', 'Lexus', 'Lifan', 'Ligier', 'Lincoln', 'Lotus', 'Luxgen', 'Mahindra', 'MAN', 'Maruti', 'Maserati', 'Maxus', 'Maybach', 'Mazda', 'McLaren', 'Mercedes-Benz', 'Mercedes-Maybach', 'Mercury', 'MG', 'Microcar', 'MINI', 'Mitsubishi', 'Mosler', 'Nio', 'Nissan', 'Oldsmobile', 'Opel', 'Ora', 'Panoz', 'Perodua', 'Peugeot', 'Plymouth', 'Polaris', 'Polestar', 'Pontiac', 'Porsche', 'Proton', 'Qiantu', 'Ram', 'Ravon', 'Hongqi', 'Renault', 'Renault Samsung', 'Rivian', 'Roewe', 'Rolls-Royce', 'Rover', 'Saab', 'Saturn', 'Scion', 'Seat', 'Sehol', 'Seres', 'Skoda', 'Smart', 'SsangYong', 'Subaru', 'Sunra', 'Suzuki', 'Tata', 'Tesla', 'Toyota', 'Vauxhall', 'VAZ', 'Venucia', 'VinFast', 'Volkswagen', 'Volvo', 'Weichai', 'Wey', 'Wuling', 'XPeng', 'Zedriv', 'Zeekr', 'Zotye', 'ZX'];
    $diameters = ['10', '12', '13', '14', '15', '16', '16.5', '17', '17.5', '18', '19', '19.5', '20', '21', '22', '23', '24'];
    $lug_count = ['3', '4', '5', '6', '8', '12'];

    $brands = Rimbrand::paginate();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
              ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
              ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
              ->orderBy('rim_brands.brand_id', 'ASC')
              ->where('rims.price1', '<>' , 0)
              ->where('rims.price2', '<>' , 0)
              ->where('rims.price3', '<>' , 0)
              ->paginate();
    return view('rims.autorims', compact('rims','brands', 'makes', 'models', 'diameters', 'lug_count'));
  }

  public function rims_search(){

  }

  public function autorims_tread($brand, $tread, $rim)
  {
    $brand = Rimbrand::where('slug', $brand)->first();

    $tread = Rimmake::where('slug', $tread)->first();

    $currRim = Rim::join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
                     ->where('rim_makes.title', $tread->title)
                     ->where('rims.rim_id', $rim)
                     ->first();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
      ->where('rims.make_id', $tread->make_id )
      ->paginate(20);

    return view('rims.auto.tread', compact('rims', 'currRim', 'brand', 'tread'));
  }

  public function quadrims_tread($brand, $tread, $rim)
  {
//    $brand = Rimbrand::where('slug', $brand)->first();
//
//    $tread = Rimmake::where('slug', $tread)->first();
//
//    $currRim = Rim::join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
//      ->where('rim_makes.title', $tread->title)
//      ->where('rims.rim_id', $rim)
//      ->first();
//
//    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
//      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
//      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
//      ->where('rims.make_id', $tread->make_id )
//      ->paginate(20);
//
//    return view('rims.auto.tread', compact('rims', 'currRim', 'brand', 'tread'));
  }

  public function rims_ajax(Request $request)
  {
    $rim = Rim::selectRaw('rims.*, rim_makes.*')
                ->rightJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
                ->where('rims.rim_id', $request->tire_id)
                ->first();

    if ($request->quantity) {
      $cart = CartController::addProduct($this->model, $rim->rim_id, $request->quantity);
    } else {
      $cart = CartController::addProduct($this->model, $rim->rim_id, 4);
    }

    $quantity = Cart::count();
    $total_sum = str_replace([',', '.00'], '', Cart::total());
    $bought = ($request->quantity) ? $request->quantity : 4;

    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);

//    dd($rim);
//      Rim::with('tread')->selectRaw('auto_tires.*, auto_treads.*')
//      ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
//      ->where('auto_treads.season', $this->season)
//      ->where('auto_tires.tire_id', $request->tire_id)
//      ->where('auto_tires.visible_users', '<>', 0)
//      ->first();
//
//    if ($request->quantity) {
//      $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
//    } else {
//      $cart = CartController::addProduct($this->model, $tire->tire_id, 4);
//    }
//
//    $quantity = Cart::count();
//    $total_sum = str_replace([',', '.00'], '', Cart::total());
//    $bought = ($request->quantity) ? $request->quantity : 4;
//
//    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
  }

  public function quadrim()
  {
    $brands = Rimbrand::paginate();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
      ->orderBy('rim_brands.brand_id', 'ASC')
      ->where('rims.price1', '<>' , 0)
      ->where('rims.price2', '<>' , 0)
      ->where('rims.price3', '<>' , 0)
      ->paginate();
//    return view('rims.autorims', compact('rims','brands'));
    return view('rims.quadrim', compact('rims', 'brands'));
  }

}

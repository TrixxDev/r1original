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

  public $currentWid;
  public $currentWid2;
  public $currentSkr;
  public $currentPcd;
  public $currentEt;
  public $currentEt2;
  public $currentDia;
  public $currentCenter;

  public $model = 'Rim';
  public $cartQty = 4;

  public $models;

  public function __construct(Request $request)
  {

    $this->d1 = ($request->d1 == 'Visi') ? 'Visi' : $request->d1;

    $this->currentCar = ($request->car) ? $request->car : '';
    $this->currentModel = ($request->model) ? $request->model : '';
    $this->currentR1 = ($request->r1) ? $request->r1 : '';
    $this->currentR2 = ($request->r2) ? $request->r2 : '';
    $this->currentR = $this->currentR1;

    $this->currentForm = ($request->currentForm == 1) ? 1 : 2;

    $this->currentWid = ($request->currentWid) ? $request->currentWid : 6;
    $this->currentWid2 = ($request->currentWid2) ? $request->currentWid2 : 8;
    $this->currentSkr = ($request->currentSkr) ? $request->currentSkr : 5;
    $this->currentPcd = ($request->currentPcd) ? $request->currentPcd : 112;
    $this->currentEt = ($request->currentEt) ? $request->currentEt : '';
    $this->currentEt2 = ($request->currentEt2) ? $request->currentEt2 : '';
    $this->currentDia = ($request->currentDia) ? $request->currentDia : 16;
    $this->currentCenter = ($request->currentCenter) ? $request->currentCenter : '';

    if (($this->currentSkr !== false)||($this->currentPcd !== false)||($this->currentEt !== false)) {
      $this->currentCar = -1;
      $this->currentModel = -1;
      if ($this->currentR2 !== false) $this->currentR = $this->currentR2;
    }

    if ($this->currentCar === -1) $this->currentModel = false;

    View::share('brandList', $this->getBrandList());
    View::share('currentCar', $this->currentCar);
    View::share('currentWid', $this->currentWid);
    View::share('currentWid2', $this->currentWid2);
    View::share('currentEt', $this->currentEt);
    View::share('currentEt2', $this->currentEt2);
    View::share('currentPcd', $this->currentPcd);
    View::share('currentSkr', $this->currentSkr);
    View::share('currentDia', $this->currentDia);
    View::share('currentCenter', $this->currentCenter);
    $options = $this->getRimOptions();
    View::share([
      'widths' => $options['widths'],
      'offsets' => $options['offsets'],
      'centers' => $options['rim_center'],
      'diameters' => $options['diameters'],
      'lugs' => $options['lug_counts'],
      'studs_spread' => $options['stud_spreads']
    ]);
    View::share('makes', $this->getRimMakes());
    View::share('models', $this->getRimModels());
    View::share('cartQty', $this->cartQty);

  }

  public function rims()
  {

    // THESE HARDCODED VALUES SHOULD BE REPLACED WITH DATA FROM API

    $brands = Rimbrand::paginate();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->when($this->currentWid, function($query) {
        $query->where('d1', '>=', $this->currentWid);
      })->when($this->currentWid2, function($query) {
        $query->where('d1', '<=', $this->currentWid2);
      })->when($this->currentSkr, function($query) {
        $query->where('skr', $this->currentSkr);
      })->when($this->currentPcd, function($query) {
        $query->where('pcd', $this->currentPcd);
      })->when($this->currentDia, function($query) {
        $query->where('d3', $this->currentDia);
      })->where('rims.visible_users', '<>', 0)
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })
      ->orderBy('quantity', 'DESC')
      ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
      ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
      ->orderBy('price3', 'DESC')
      ->paginate();

    return view('rims.autorims', compact('rims','brands'));
  }

  public function rims_search(Request $request){

    DB::enableQueryLog();

    $this->currentWid = ($request->currentWid == 'Visi') ? '' : $request->currentWid;
    $this->currentWid2 = ($request->currentWid2 == 'Visi') ? '' : $request->currentWid2;
    $this->currentEt = ($request->currentEt == 'Visi') ? '' : $request->currentEt;
    $this->currentEt2 = ($request->currentEt2 == 'Visi') ? '' : $request->currentEt2;
    $this->currentPcd = ($request->currentPcd == 'Visi') ? '' : $request->currentPcd;
    $this->currentSkr = ($request->currentSkr == 'Visi') ? '' : $request->currentSkr;
    $this->currentDia = ($request->currentDia == 'Visi') ? '' : $request->currentDia;
    $this->currentCenter = ($request->currentCenter == 'Visi') ? '' : $request->currentCenter;

    $rims = Rim::select('rims.*')
      ->leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->when($this->currentWid, function($query) {
        $query->where('rims.d1', '>=', $this->currentWid);
      })->when($this->currentWid2, function($query) {
        $query->where('rims.d1', '<=', $this->currentWid2);
      })->when($this->currentEt, function($query) {
        $query->where('rims.et', '>=', $this->currentEt);
      })->when($this->currentEt2, function($query) {
        $query->where('rims.et', '<=', $this->currentEt2);
      })->when($this->currentPcd, function($query) {
        $query->where('rims.pcd', $this->currentPcd);
      })->when($this->currentSkr, function($query) {
        $query->where('rims.skr', $this->currentSkr);
      })->when($this->currentDia, function($query) {
        $query->where('rims.d3', $this->currentDia);
      })->when($this->currentCenter, function($query) {
        $query->where('rims.dc', $this->currentCenter);
      })
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })->where('rims.visible_users', '<>', 0)
      ->orderBy('quantity', 'DESC')
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('price3', 'DESC')
      ->groupBy('rims.rim_id')->paginate()->appends($request->query());


//      dd(DB::getQueryLog());

//    return view('tires.moto.index',
//      ['tires' => $tires, 'filterCount' => $this->filterCount]
//    );

    return view('rims.autorims', compact('rims'));
  }

  public function rims_tread($brand, $tread, $rim)
  {
    $slug = $brand;

    $brand = Rimbrand::where('title', $brand)->first();
    if (is_null($brand)) {
      $brand = Rimbrand::where('slug', $slug)->first();
    }

    $rims = Rim::selectRaw('rims.*, rim_makes.*, rim_brands.*,
                                                rim_brands.title as brands_title,
                                                rims.comment as comment')
      ->join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->join('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->where('rim_brands.title', $brand->title)
      ->where('rim_makes.title', str_replace('_', '/', $tread))
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })
      ->orderBy('quantity', 'DESC')
      ->orderBy('price3', 'DESC')
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->get();

    $currRim = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->where('rim_makes.title', str_replace('_', '/', $tread))
      ->where('rims.rim_id', $rim)
      ->first();

    $currBrand = Rimbrand::where('brand_id', $currRim->brand_id)->first();

    $currRim->includeStock = true;

    return view('rims.auto.tread',
      compact('rims', 'currRim', 'currBrand')
    );
  }

  public function quadr_rims_tread($brand, $tread, $rim)
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

    return view('rims.quadr.tread', compact('rims', 'currRim', 'brand', 'tread'));
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
      $cart = CartController::addProduct($this->model, $rim->rim_id, $this->cartQty);
    }

    $quantity = Cart::count();
    $total_sum = str_replace([',', '.00'], '', Cart::total());
    $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);

  }

  public function quadr_rims()
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
      ->where('rims.price3', '<>' , 0)
      ->where('rims.price2', '<>' , 0)
      ->paginate();
//    return view('rims.autorims', compact('rims','brands'));
    return view('rims.quadrim', compact('rims', 'brands', 'makes', 'models', 'diameters', 'lug_count'));
  }

  public function getRimOptions()
  {
    $options = [
      'widths' => [],
      'offsets' => [],
      'diameters' => [],
      'lug_counts' => [],
      'stud_spreads' => [],
      'rim_center' => []
    ];

    $rimProperties = ['d1' => 'widths', 'et' => 'offsets', 'd3' => 'diameters', 'skr' => 'lug_counts', 'pcd' => 'stud_spreads', 'dc' => 'rim_center'];

    foreach (Rim::all() as $rim) {
      foreach ($rimProperties as $property => $optionKey) {
        $value = $rim->$property;

        if ($optionKey !== 'offsets' && empty($value)) continue;

        if (!in_array($value, $options[$optionKey], true)) {
          $options[$optionKey][] = $value;
        }
      }
    }

    foreach ($options as &$optionValues) {
      asort($optionValues, SORT_NATURAL | SORT_FLAG_CASE);
    }

    return $options;
  }

  public function getRimMakes()
  {
    $rim_makes = [];

    foreach (Rim::all() as $rim) {
      array_push($rim_makes, $rim->offset);
    }

    $rim_makes = array_unique($rim_makes);
    $rim_makes = array_values($rim_makes);

    asort($rim_makes, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_makes;
  }

  public function getRimModels()
  {
    $rim_models = [];

    foreach (Rim::all() as $rim) {
      array_push($rim_models, $rim->offset);
    }

    $rim_models = array_unique($rim_models);
    $rim_models = array_values($rim_models);

    asort($rim_models, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_models;
  }

  public function getBrandList()
  {
    $rim_brands = [];

    foreach (Rimbrand::all() as $rim_brand) {
      array_push($rim_brands, $rim_brand->title);
    }

    $rim_brands = array_unique($rim_brands);
    $rim_brands = array_values($rim_brands);

    asort($rim_brands, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_brands;
  }

}

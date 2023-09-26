<?php

namespace App\Http\Controllers;

use App\Helper\Env;
use App\Models\Autotire;
use App\Models\Bigtire;
use App\Models\Code;
use App\Models\Moto;
use App\Models\Quadr;
use App\Models\Rim;
use App\Models\Stud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TopTireController extends Controller
{

  public array $categories = [];
  public string $summerURL = 'vasaras-riepa';
  public string $winterURL = 'ziemas-riepa';
  public array $code_array;

  public function __construct() {

    set_time_limit(0);

    $codes = Code::all();

    foreach ($codes as $code) {
      $this->code_array[$code->name] = $code->explanation;
    }

    if ((int) env('SEASON') === 1) {
      if ($this->autoTires() !== null) {
        array_push($this->categories, ['name' => 'Vasaras riepas', 'class' => 'autoTiresSummer']);
        array_push($this->categories, ['name' => 'Ziemas riepas', 'class' => 'autoTiresSummer']);
      }
    } else {
      if ($this->autoTires() !== null) {
        array_push($this->categories, ['name' => 'Ziemas riepas', 'class' => 'autoTiresWinter']);
        array_push($this->categories, ['name' => 'Vasaras riepas', 'class' => 'autoTiresSummer']);
      }
    }

    if ($this->alloyRims() !== null) array_push($this->categories, ['name' => 'Lietie diski', 'class' => 'alloyRims']);
    if ($this->motoTires() !== null) array_push($this->categories, ['name' => 'Motociklu riepas', 'class' => 'motoTires']);
    if ($this->quadrTires() !== null) array_push($this->categories, ['name' => 'Kvadraciklu riepas', 'class' => 'quadrTires']);
    if ($this->bigTires() !== null) array_push($this->categories, ['name' => 'Lielās riepas', 'class' => 'bigTires']);
    if ($this->studs() !== null) array_push($this->categories, ['name' => 'Skrūvējamās radzes', 'class' => 'studs']);

    View::share('summerURL', $this->summerURL);
    View::share('winterURL', $this->winterURL);
    View::share('code_array', $this->code_array);
    View::share('categories', $this->categories);
  }

  public function index() {
    return view('sales');
  }

  public function autoTires()
  {
    $tires = Autotire::leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                      ->where('auto_tires.priceoffer', 1)
                      ->where('auto_tires.visible_users', '<>', 0)
                      ->orderBy('auto_tires.d3', 'ASC')
                      ->orderBy('auto_tires.d1', 'ASC')
                      ->orderBy('auto_tires.d2', 'ASC')
                      ->orderBy('auto_tires.price2', 'DESC')
                      ->groupBy('tire_id')
                      ->get();

    if ($tires->count() > 0) {
      return view('tires.auto.sales', compact('tires'));
    } else {
      return null;
    }
  }

  public function autoTiresSummer()
  {
    $tires = Autotire::leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                      ->where('priceoffer', 1)
                      ->where('auto_treads.season', 1)
                      ->where('auto_tires.visible_users', '<>', 0)
                      ->orderBy('d3', 'ASC')
                      ->orderBy('d1', 'ASC')
                      ->orderBy('d2', 'ASC')
                      ->orderBy('price2', 'DESC')
                      ->groupBy('tire_id')
                      ->get();

    if ($tires->count() > 0) {
      return view('tires.auto.seasonsales.summer', compact('tires'));
    } else {
      return null;
    }
  }

  public function autoTiresWinter()
  {

    $tires = Autotire::leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                      ->where('priceoffer', 1)
                      ->where('auto_treads.season', 2)
                      ->where('auto_tires.visible_users', '<>', 0)
                      ->orderBy('d3', 'ASC')
                      ->orderBy('d1', 'ASC')
                      ->orderBy('d2', 'ASC')
                      ->orderBy('price2', 'DESC')
                      ->groupBy('tire_id')
                      ->get();

    if ($tires->count() > 0) {
      return view('tires.auto.seasonsales.winter', compact('tires'));
    } else {
      return null;
    }
  }

  public function alloyRims()
  {

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
                        ->where('rims.priceoffer', 1)
                        ->where('rims.visible_users', '<>', 0)
                        ->orderBy('quantity', 'DESC')
                  //            ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                  //            ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                        ->orderBy('price3', 'DESC')
                        ->get();

    if ($rims->count() > 0) {
      return view('rims.auto.sales', compact('rims'));
    } else {
      return null;
    }
  }

  public function motoTires()
  {

    $tires = Moto::with('tread')->leftJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                  ->where('moto_tires.priceoffer', 1)
                  ->where('moto_tires.visible_users', '<>', 0)
                  ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                  ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                  ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                  ->orderBy('d4', 'ASC')
                  ->orderBy('price2', 'DESC')
                  ->get();

    if ($tires->count() > 0) {
      return view('tires.moto.sales', compact('tires'));
    } else {
      return null;
    }
  }

  public function quadrTires()
  {

    $tires = Quadr::with('tread')->leftJoin('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
                  ->where('quadr_tires.priceoffer', 1)
                  ->where('quadr_tires.visible_users', '<>', 0)
                  ->orderBy('d3', 'ASC')
                  ->orderBy('d1', 'ASC')
                  ->orderBy('d2', 'ASC')
                  ->orderBy('price2', 'DESC')
                  ->get();

    if ($tires->count() > 0) {
      return view('tires.quadr.sales', compact('tires'));
    } else {
      return null;
    }
  }

  public function bigTires()
  {

    $tires = Bigtire::with('tread')->leftJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                  ->where('priceoffer', 1)
                  ->where('visible_users', '<>', 0)
                  ->orderBy('d3', 'ASC')
                  ->orderBy('d1', 'ASC')
                  ->orderBy('d2', 'ASC')
                  ->orderBy('quantity', 'DESC')
                  ->get();

    if ($tires->count() > 0) {
      return view('tires.industrial.sales', compact('tires'));
    } else {
      return null;
    }
  }

  public function studs()
  {
    $studs = Stud::leftJoin('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
                  ->where('studs.priceoffer', 1)
                  ->where('studs.visible_users', '<>', 0)
                  ->orderBy('price2', 'DESC')
                  ->get();

    $length = [1,2,3,4,5,6,7,8,9];

    if ($studs->count() > 0) {
      return view('studs.sales', compact('studs', 'length'));
    } else {
      return null;
    }
  }

  public function filter(Request $request) {

    $categoryId = (int) $request->ct;

    $className = $this->categories[$categoryId]['class'];

    $view = $this->$className();

    return view('salesCt', compact('view', 'categoryId'));
  }

  public function changeSeason(Request $request)
  {
    $season = (int) $request->season;
    Env::addVar(['SEASON' => $season]);
    return 'okey';
  }
}

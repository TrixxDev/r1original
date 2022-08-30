<?php

namespace App\Http\Controllers;

use App\Models\FilterCars;
use App\Models\FilterSizes;
use App\Models\FilterModels;
use App\Models\Rim;
use App\Models\Rimbrand;
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

    $brands = Rimbrand::paginate(20);

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
              ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
              ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
              ->paginate(20);

//    dd($rims);

    return view('rims.autorims', compact('rims','brands'));
  }

  public function autorims_tread()
  {
    
  }

  public function quadrim()
  {
    return view('rims.quadrim');
  }

}

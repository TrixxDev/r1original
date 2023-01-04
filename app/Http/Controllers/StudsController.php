<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudsController extends Controller
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
  public $type;
  public $code;
  public $fuel;
  public $wet;
  public $availability;
  public $code_array = [];
  public $filterCount = 0;

  public function __construct(Request $request)
  {

  }

  public function studs() {

    $applications = ['Farming equipment', 'Heavy machinery', 'Light machinery', 'Footwear', 'Recreational vehicules', 'Forestry', 'Mining', 'Military', 'Bicycles', 'Snowmobile'];
    $length = [1,2,3,4,5,6,7,8,9];

    return view('studs.studs', compact('applications', 'length'));
  }

  public function studs_ajax(Request $request) {

  }

  public function studs_find(Request $request) {

  }

  public function studs_filter(Request $request) {

  }

  public function studs_search(Request $request) {

  }

  public function studs_tread($brand, $tread, $tire) {

  }
}

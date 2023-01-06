<?php

namespace App\Http\Controllers;

use App\Models\Autobrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;
use App\Models\Stud;

use Cart;
use Route;
use Auth;


class StudsController extends Controller
{
  public $brands;
  public $season;
  public $currBrand;
  public $autoTiresD1;
  public $autoTiresD2;
  public $autoTiresD3;
  public $model = 'Autotire';
  public $tiresSize;
  public $availability;
  public $code_array = [];
  public $filterCount = 0;

  public function __construct(Request $request)
  {
    View::share('current_url', 'radzes');
  }

  public function studs() {

    $studs = Stud::all();

    $applications = [
      1 => 'Apaviem',
      2 => 'Kvadracikliem',
      3 => 'Motocikliem',
      4 => 'Mini traktoriem',
      5 => 'Iekrāvējiem',
      6 => 'Būvniecības tehnikai',
      7 => 'Agro tehnikai',
      8 => '4x4 visurgājēji',
    ];

    $length = [1,2,3,4,5,6,7,8,9];

    return view('studs.studs', compact('applications', 'length', 'studs'));
  }

  public function studs_ajax(Request $request) {

  }

  public function studs_find(Request $request) {

  }

  public function studs_filter(Request $request) {

  }

  public function studs_search(Request $request) {

  }

  public function studs_tread($brand, $tread, $stud) {

    DB::enableQueryLog();

    $brand = Autobrand::where('slug', $brand)->first();

  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Autobrand;
use App\Models\Studbrand;
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

    $studs = Stud::leftJoin('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
      ->where('studs.visible_users', '<>', 0)
      ->orderBy('price2', 'DESC')
      ->paginate();

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

    $brand = Studbrand::where('b_title', $brand)->first();

    $studs = Stud::selectRaw('studs.*, studs_treads.*, studs_brands.*,
                              studs_brands.b_title as brands_title')
                              ->join('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
                              ->join('studs_brands', 'studs_treads.brand_id', '=', 'studs_brands.brand_id')
                              ->where('studs_brands.b_title', $brand->b_title)
                              ->where('studs_treads.t_title', str_replace('_', '/', $tread))
                              ->get();

    $currStud = Stud::leftJoin('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
                      ->where('studs_treads.t_title', str_replace('_', '/', $tread))
                      ->where('studs.stud_id', $stud)
                      ->first();

    $currBrand = Studbrand::where('brand_id', $currStud->brand_id)->first();

    return view('studs.tread',
      compact('studs', 'currStud', 'currBrand')
    );

  }
}

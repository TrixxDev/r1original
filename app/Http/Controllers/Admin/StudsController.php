<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudsController extends Controller {

  public function index(Request $request) {

    $applications = ['Farming equipment', 'Heavy machinery', 'Light machinery', 'Footwear', 'Recreational vehicules', 'Forestry', 'Mining', 'Military', 'Bicycles', 'Snowmobile'];
    $length = [1,2,3,4,5,6,7,8,9];

    return view('admin.studs.index', compact('applications', 'length'));
  }

}

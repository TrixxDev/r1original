<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{

  public $status_enum = [
    1 => 'Nav pabeigts/Nav informācijas',
    2 => 'Jauns',
    3 => 'Gaidām apmaksu',
    4 => 'Gaidām preci',
    6 => 'Prece nav pieejama',
    7 => 'Klients atteicās',
    5 => 'Pabeigts'
];

  public $pay_enum = [
    0 => '',
    1 => 'Apmaksa saņemšanas brīdī',
    2 => 'Bankas pārskaitījums',
    3 => 'Tiešsaistes apmaksa'
  ];

  public function orders()
  {

    $orders = Order::orderBy('id', 'desc')->get();
    $status_enum = $this->status_enum;
    $pay_enum = $this->pay_enum;

    return view('admin.shop.index', compact('orders', 'status_enum', 'pay_enum'));

  }

  public function order($id)
  {

    $status_enum = $this->status_enum;
    $pay_enum = $this->pay_enum;

    $order = Order::findOrFail($id);

    @$userData = json_decode(json_encode(unserialize($order->info)));
    //dd($order);
    if ($userData == false || !isset($userData->items)) { return \Redirect::to(route('admin.orders'))->with('danger', 'Nevar atvērt pasūtījumu'); }

    $offices = Office::all();

    $tires = $userData->items;

    if (property_exists($userData,'company_registration_number')){
      $hasCompanyData = true;
    }else{
      $hasCompanyData = false;
    }

    return view('admin.shop.order', compact('order', 'userData', 'tires', 'offices', 'status_enum', 'pay_enum'));

  }

  public function order_update(Request $request, $id)
  {
    $order = Order::findOrFail($id);

    $data = (object) unserialize($order->info);

//    if (strpos($request->name_suraname, ',') !== false) {
//      $names = explode(', ', $request->name_suraname);
//    } else {
//      $names = explode(' ', $request->name_suraname);
//    }

//    $data->name = $names[0];
//    $data->surname = $names[1];
//    $data->email = $request->email;

    $order->status = $request->order_status;
    $order->edituser = Auth::user()->id;

    if ($order->save()) {
      return redirect()->back()->with('success', 'Pasūtījums informācija veiksmīgi labota!');
    } else {
      return redirect()->back()->with('danger', 'Notika kļūda labojot pasūtījuma informāciju!');
    }

  }

  public function delete($id) {

    $order = Order::findOrFail($id);

    if ($order->delete()) {
	    return redirect()->route('admin.orders')
          ->with('success','Pasūtījums veiksmīgi dzēsts');
    } else {
	    return redirect()->route('admin.shop.orders')->with('danger', 'Kļūda pasūtījuma dzēšanā');
    }

  }

}

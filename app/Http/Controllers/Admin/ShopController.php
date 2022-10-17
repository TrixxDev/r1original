<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class ShopController extends Controller
{

  public function orders()
  {

    $orders = Order::whereIn('status', [1,2,3])->orderBy('id', 'desc')->paginate(30);

    return view('admin.shop.index', compact('orders'));

  }

  public function order($id)
  {

    $order = Order::findOrFail($id);

    @$userData = json_decode(json_encode(unserialize($order->info)));

    if ($userData == false || !isset($userData->items)) { return \Redirect::to(route('admin.orders'))->with('danger', 'Nevar atvērt pasūtījumu'); }

    $tires = $userData->items;

    if (property_exists($userData,'company_registration_number')){
      $hasCompanyData = true;
    }else{
      $hasCompanyData = false;
    }

    return view('admin.shop.order', compact('order', 'userData', 'tires'));

  }

  public function order_update(Request $request, $id)
  {
    $order = Order::findOrFail($id);

    $data = (object) unserialize($order->info);

    

    dd($request, $data);
  }
//
//  public function delete($id) {
//
//    Order::where('id', $id)->delete();
//
//    return redirect()->route('admin.shop.orders')
//      ->with('success','Product deleted successfully');
//  }

}

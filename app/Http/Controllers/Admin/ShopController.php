<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    $userData = json_decode(json_encode(unserialize($order->info)));

    $count = count($userData) - 1;

    $userData = $userData[$count];

    $tires = json_decode(json_encode(unserialize($order->info)));

    $orderedTires = array_pop($tires);


    if (property_exists($userData,'company_registration_number')){
      $hasCompanyData = true;
    }else{
      $hasCompanyData = false;
    }

    return view('admin.shop.order', compact('order', 'userData', 'tires'));

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

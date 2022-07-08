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

    return view('admin.shop.order', compact('order'));

  }

}

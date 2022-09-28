<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Auth;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Cart;

class CheckCart
{

  public function handle($request, Closure $next)
  {

//    if (Session::has('cart')) {
//      if (\Cart::countItems() == 0) {
//        Session::remove('cart');
//        return redirect(route('home'));
//      }
//    }
//
//    $session_id = Session::getId();
//
//    if (\Cart::instance($session_id)->content()->count() <= 0) {
//      \Cart::erase();
//      return Redirect::home();
//    }

    return $next($request);

  }
}

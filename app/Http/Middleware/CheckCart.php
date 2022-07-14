<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Cart;

class CheckCart
{

  public function handle($request, Closure $next)
  {

    if (Session::has('cart')) {
      if (\Cart::countItems() == 0) {
        Session::remove('cart');
        redirect(route('home'));
      }
    }

    return $next($request);

  }
}

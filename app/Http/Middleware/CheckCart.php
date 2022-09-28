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

    if (\Cart::countItems() == 0) {
      return Redirect::route('home');
    }

    return $next($request);

  }
}

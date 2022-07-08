<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Cart;

class CheckSession
{

  public $cart;

  public function __construct()
  {
    $this->cart = new Cart;
  }

  public function handle($request, Closure $next)
  {

    if (Session::has('cart')) {
      if ($this->cart->countItems() == 0) {
        Session::remove('cart');
      }
    }

    return $next($request);

  }
}

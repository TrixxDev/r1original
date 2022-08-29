<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Cart;

class CheckSession
{

  public function handle($request, Closure $next)
  {

    $response = $next($request);

    $routeAction = Route::getCurrentRoute()->getActionName();

    if (strpos($routeAction, 'Auth') === false) {
      Session::put('returnUrl', url()->previous());
    }

    var_dump(session('returnUrl'));

    Session::put('userTime', Carbon::now()->addMinutes(30));

    if (Session::has('cart')) {
      if (\Cart::countItems() == 0) {
        Session::remove('cart');
      }
    }

    $session_id = Session::getId();
    $order = Order::where('order_token', $session_id)->where('status', 1)->first();


    if (\Cart::instance($session_id)->content()->count() <= 0) {
      if ($order) {
        \Cart::erase();
        $order->delete();
        return Redirect::home();
      } else {
        \Cart::erase();
      }
      Session::setId(session_create_id());
    } else {
      if ($order) {
        $now = strtotime(Carbon::now());
        $orderTime = strtotime(Carbon::parse($order->timeRemaining));
        if ($now >= $orderTime) {
          \Cart::erase();
          $order->delete();
          Session::setId(session_create_id());
          return Redirect::home();
        } else {
          $order->timeRemaining = Carbon::now()->addMinutes(30)->format('H:i:s');
          $order->save();
        }
      }
    }

    return $response;

  }
}

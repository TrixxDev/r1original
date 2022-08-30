<?php

namespace App\Providers;

use App\Helper\Env;
use App\Http\Controllers\CartController;
use App\Models\Order;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\CartItemOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

      Paginator::defaultView('vendor.pagination.custom');
      Paginator::defaultSimpleView('vendor.pagination.custom');

      define('SLOT_STATUS_FREE', 0);
      define('SLOT_STATUS_TAKEN', 1);
      define('SLOT_STATUS_OFFER', 2);
      define('SLOT_STATUS_CLOSED', 3);

      $options = DB::table('cart_config')->get();

      foreach ($options as $option) {
        Config::set('app.settings.' . $option->name, (int) $option->value);
      }

      date_default_timezone_set('Europe/Riga');

    }
}

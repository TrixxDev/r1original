<?php

namespace App\Providers;

use App\Http\Controllers\CartController;
use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\CartItemOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

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

      define('SLOT_STATUS_FREE', 0);
      define('SLOT_STATUS_TAKEN', 1);
      define('SLOT_STATUS_OFFER', 2);
      define('SLOT_STATUS_CLOSED', 3);

      date_default_timezone_set('Europe/Riga');
    }
}

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

//        $configs = DB::table('cart_config')->get()->toArray();
//        foreach ($configs as $config) {
//          config()->set('app.settings.' . $config->name, $config->value);
//        }

        $cart = new Cart();
        \View::share('cart', $cart);
    }
}

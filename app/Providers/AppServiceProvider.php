<?php

namespace App\Providers;

use App\Helper\Env;
use App\Http\Controllers\CartController;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\CartItemOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

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

      if (env('APP_MAINTENANCE') == true) {
        Artisan::call('down', ['--render' => 'maintenance', '--allow' => '212.3.218.22']);
      } else {
        Artisan::call('up');
      }

      Builder::macro('whereLike', function($attributes, $terms) {
	$this->where(function($query) use ($attributes, $terms) {
	  foreach (Arr::wrap($attributes) as $attribute) {
	    foreach (Arr::wrap($terms) as $term) {
		//if (in_array('DOT%' . substr(date('Y'), -2)), $terms) { unset('DOT%' . substr(date('Y'), -2)) }
		if ($term == 'CURRYEAR') {
			$query->orWhere($attribute, 'LIKE', '%' . $term . '%');
			$query->orWhere($attribute, 'LIKE', 'DOT%' . substr(date('Y'), -2));
		}
		$query->orWhere($attribute, 'LIKE', '%' . $term . '%');
	    }
	  }
	});
	return $this;
      });

      Paginator::defaultView('vendor.pagination.custom');
      Paginator::defaultSimpleView('vendor.pagination.custom');


      view()->composer('*', function($view)
      {

        if (Auth::check()) {

          $user = User::findOrFail(Auth::user()->id);

	  Config::set('app.debug', true);
	  //if ($user->hasRole(['administrators']) {
	    //Config::set('app.debug', true);
	  //} else {
	    //Config::set('app.debug', false);
	  //}

          $minutesToAdd = gmdate('i', env('session_lifetime'));

          $userTime = \Carbon\Carbon::now()->addYear()->format('Y-m-d H:i');

          $user->timestamps = false;
          $user->lastActivityTime = $userTime;
          $user->save();

        } else {
	  Config::set('app.debug', false);
	}

      });

      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        define('user_ip', $_SERVER['HTTP_CLIENT_IP']);
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        define('user_ip', $_SERVER['HTTP_X_FORWARDED_FOR']);
      } else {
        if (isset($_SERVER['REMOTE_ADDR'])) {
          define('user_ip', $_SERVER['REMOTE_ADDR']);
        }
      }

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

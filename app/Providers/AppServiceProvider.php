<?php

namespace App\Providers;

use App\Helper\Env;
use App\Http\Controllers\CartController;
use App\Models\Bannerimage;
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
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Models\Audit;

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

      // sistēmas iekšējo moduļu auditēšana
      define('AUDIT_FACILITY_LOGIN',100);
      define('AUDIT_FACILITY_USER',101);
      define('AUDIT_FACILITY_DB',102);
      define('AUDIT_FACILITY_SYSCORE',103);
      define('AUDIT_FACILITY_MESSAGE',104);

      define('AUDIT_FACILITY_DOCUMENT',105);
      define('AUDIT_FACILITY_CATEGORY',105);

      define('AUDIT_FACILITY_FIZPERS',1);
      define('AUDIT_FACILITY_JURIDPERS',2);

      define('AUDIT_SEVERITY_CRITICAL',1);
      define('AUDIT_SEVERITY_WARNING',2);
      define('AUDIT_SEVERITY_INFO',3);
      define('AUDIT_SEVERITY_DEBUG',100);

//      $date=date("W");
//      dd($date." Week Number");

      $banners = Bannerimage::all();

      View::share('banners', $banners);

      if (env('APP_MAINTENANCE') == true) {
        Artisan::call('down', ['--render' => 'maintenance', '--secret' => 'r1riepas']);
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

      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        define('user_ip', $_SERVER['HTTP_CLIENT_IP']);
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        define('user_ip', $_SERVER['HTTP_X_FORWARDED_FOR']);
      } else {
        if (isset($_SERVER['REMOTE_ADDR'])) {
          define('user_ip', $_SERVER['REMOTE_ADDR']);
        }
      }

      define('TIRE_SEASON_SUMMER',1);
      define('TIRE_SEASON_WINTER',2);

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

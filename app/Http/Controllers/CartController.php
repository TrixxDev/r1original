<?php

namespace App\Http\Controllers;

use App\Models\Autotire;
use App\Models\Moto;
use App\Models\Order;
use App\Models\Pdf;
use App\Models\Quadr;
use App\Models\Bigtire;
use App\Models\Rim;
use App\Models\Quadrim;
use App\Models\Stud;
use Cart;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\CartItemOptions;
use http\Exception;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Paysera\WebToPay;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EmailController as Mailer;

class CartController extends Controller
{

  /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected static function getSelfUrl(): string
    {
      $url = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'));

      if (isset($_SERVER['HTTPS']) === true) {
        $url .= ($_SERVER['HTTPS'] === 'on') ? 's' : '';
      }

      $url .= '://' . $_SERVER['HTTP_HOST'];

      if (isset($_SERVER['SERVER_PORT']) === true && $_SERVER['SERVER_PORT'] !== '80') {
        $url .= ':' . $_SERVER['SERVER_PORT'];
      }

      $url .= dirname($_SERVER['SCRIPT_NAME']);

      return $url;
    }

    public static function options(): array
    {
      return [
        'shippingDef' => config('app.settings.shippingDef'),
        'Autotire' => [
          'shipping' => [
            1 => config('app.settings.shipping_autotire_one'),
            2 => config('app.settings.shipping_autotire_two'),
            4 => config('app.settings.shipping_autotire_four'),
            5 => config('app.settings.shipping_autotire_many'),
          ],
          'fitting' => [
            16 => [
              1 => config('app.settings.fitting_autotire_16_one'),
              2 => config('app.settings.fitting_autotire_16_two'),
              4 => config('app.settings.fitting_autotire_16_four'),
            ],
            17 => [
              1 => config('app.settings.fitting_autotire_17_one'),
              2 => config('app.settings.fitting_autotire_17_two'),
              4 => config('app.settings.fitting_autotire_17_four'),
            ],
            18 => [
              1 => config('app.settings.fitting_autotire_17_one'),
              2 => config('app.settings.fitting_autotire_17_two'),
              4 => config('app.settings.fitting_autotire_17_four'),
            ],
            19 => [
              1 => config('app.settings.fitting_autotire_19_one'),
              2 => config('app.settings.fitting_autotire_19_two'),
              4 => config('app.settings.fitting_autotire_19_four'),
            ],
            20 => [
              1 => config('app.settings.fitting_autotire_19_one'),
              2 => config('app.settings.fitting_autotire_19_two'),
              4 => config('app.settings.fitting_autotire_19_four'),
            ],
            21 => [
              1 => config('app.settings.fitting_autotire_21_one'),
              2 => config('app.settings.fitting_autotire_21_two'),
              4 => config('app.settings.fitting_autotire_21_four'),
            ],
          ]
        ],
        'Moto' => [
          'shipping' => [
            1 => config('app.settings.shipping_moto_one'),
            2 => config('app.settings.shipping_moto_two'),
            4 => config('app.settings.shipping_moto_many'),
            5 => config('app.settings.shipping_moto_many'),
          ],
          'fitting' => [
            1 => config('app.settings.fitting_moto_one'),
            2 => config('app.settings.fitting_moto_two'),
          ],
        ],
        'Quadr' => [
          'shipping' => [
            1 => config('app.settings.shipping_autotire_one'),
            2 => config('app.settings.shipping_autotire_two'),
            4 => config('app.settings.shipping_autotire_four'),
            5 => config('app.settings.shipping_autotire_many'),
          ],
          'fitting' => [
            1 => config('app.settings.fitting_quadr_one'),
            2 => config('app.settings.fitting_quadr_two'),
            4 => config('app.settings.fitting_quadr_four'),
          ],
        ],
        'Bigtire' => [
          'shipping' => [
            1 => config('app.settings.shipping_industrial_one'),
            2 => config('app.settings.shipping_industrial_two'),
            4 => config('app.settings.shipping_industrial_four'),
            5 => config('app.settings.shipping_industrial_many'),
          ]
        ]
      ];
    }

    public function index(Request $request)
    {

      $session_id = Session::getId();

      $_SESSION['cart'] = [];

      if ($request->post()) {
        $delivery = [];
        $fitting = [];

        if (Auth::check()) {
          $order = Order::where('userId', Auth::user()->id)->where('status', 1)->first();
          $_SESSION['cart']['user'] = Auth::user()->id;
        } else {
          $order = Order::where('userIp', user_ip)->where('status', 1)->first();
          $_SESSION['cart']['user'] = user_ip;
        }

        if ($request->data['cart_delivery_radio'] == 1 || $request->data['cart_delivery_radio'] == 2) {
          $fitting['fitting_address'] = $request->data['cart_delivery_radio'];
          $fitting['fitting'] = $request->fitting;
          $fitting['fitting_price'] = $request->fitting_price . '00';
        } else {
          $delivery['shipping_city'] = $request->data['shipping_city'];
          $delivery['shipping_address'] = $request->data['shipping_address'];
          $delivery['door_code'] = $request->data['door_code'];
          $delivery['delivery_price'] = $request->delivery_price . '00';
        }

        $cartData = (empty($fitting)) ? serialize($delivery) : serialize($fitting);

        $amount = str_replace(['.', ','], '', Cart::subtotal());

        if (!$order) $order = new Order;
        $order->userId = 0;
        $order->userIp = 0;
        if (Auth::check()) {
          $order->userId = Auth::user()->id;
        } else {
          $order->userIp = user_ip;
        }
        $order->status = 1;
        $order->price = substr($amount, 0, -2);
        $order->delivery_price = (isset($delivery['delivery_price'])) ? $delivery['delivery_price'] : 0;
        $order->fit_price = (isset($fitting['fitting_price'])) ? $fitting['fitting_price'] : 0;
        $order->info = $cartData;

        $order->save();

        $order_id = $order->id;
        $amount1 = $amount;
        $email = Session::get('email');

        $data = ['order_id' => $order_id, 'amount' => $amount1, 'email' => $email];
        Session::put('cart_options', $data);
        Session::put('cart.user', (Auth::check()) ? Auth::user()->id : user_ip);

        return redirect(route('order'));
      }
        //dd(Cart::content());
      return view('cart.home');
    }

    public function order(Request $request)
    {

      $session_id = Session::getId();

      if (Auth::check()) {
        $order = Order::where('userId', Auth::user()->id)->where('status', 1)->first();
      } else {
        $order = Order::where('userIp', user_ip)->where('status', 1)->first();
      }
      if (!$order) return Redirect::route('cart');
      $delivery_price = ($order->delivery_price) ? $order->delivery_price : 0;
      $fit_price = ($order->fit_price) ? $order->fit_price : 0;

      if ($request->post()) {

  //        Session::regenerate(false);

        $amount = str_replace(['.', ','], '', Cart::subtotal());
        $amount1 = $amount;
	if ($delivery_price > 0) {
	  $amount1 = (int) $amount1 + (int) $delivery_price;
	} elseif ($fit_price > 0) {
	  $amount1 = (int) $amount1 + (int) $fit_price;
	}

        $email = Session::get('email');
        $order_id = $order->id;

        $data = ['order_id' => $order_id, 'amount' => $amount1, 'email' => $email];

        if (isset($request->pay)) {
          return $this->pay($data);
        } else if (isset($request->end)) {
          return $this->end();
        }

        $cartData = unserialize($order->info);
        $i = 0;

        foreach (Cart::content() as $key => $item) {

	  //dd($item->options->tire);

          $cartData['items'][$i] = [
            'tire_id' => $item->id,
            'title' => $item->name,
            'quantity' => $item->qty,
            'price' => (int) $item->price,
            'article' => $item->options->tire['article'],
	  ];

          $i++;

        }

        $user_data = $request->input('data');
        $user_data = array_merge($user_data, $cartData);

        $order->status = 1;
        $order->userId = 0;
        $order->userIp = 0;
        if (Auth::check()) {
          $order->userId = Auth::user()->id;
        } else {
          $order->userIp = user_ip;
        }
        $order->price = substr($amount, 0, -2);
        $order->delivery_price = $delivery_price;
        $order->fit_price = $fit_price;
        $cartData = serialize($user_data);
        $order->info = $cartData;

        $order->save();

        $order_id = $order->id;

        if (!isset($user_data['email_notifications'])) {
          Session::remove('cart.email_notifications');
        }
        foreach ($user_data as $key => $value) {
          Session::put('cart.' . $key, $value);
        }
        Session::put('person', $request->person);

        $cats = [];
        $dogs = [];


        foreach (Cart::content() as $key => $item) {
          $cat = str_replace('App\\Models\\', '', $item->associatedModel);
          array_push($cats, $cat);
          array_push($dogs, $item->options->availability);
        }

        $cats = array_unique($cats);
        $dogs = array_unique($dogs);

        return view('cart.checkout', compact('user_data', 'cats', 'dogs', 'order_id'));
      }else {
        if (!Session::exists('person')) Session::put('person', 1);
        return view('cart.order');
      }

    }

      public function checkShipping(Request $request, $data = '')
      {
        $modelCount = [
          'Autotire' => [],
          'Moto' => [],
          'Quadr' => [],
          'Bigtire' => [],
        ];

//        Session::remove('cartOptions');

        Session::put('cartOptions.shipping', 1);

        foreach (Cart::content() as $item) {
          $model = str_replace('App\Models\\', '', $item->associatedModel);
          switch ($model) {
            case 'Autotire': {
              array_push($modelCount['Autotire'], $item->qty);
              break;
            }
            case 'Moto': {
              array_push($modelCount['Moto'], $item->qty);
              break;
            }
            case 'Quadr': {
              array_push($modelCount['Quadr'], $item->qty);
              break;
            }
            case 'Bigtire': {
              array_push($modelCount['Bigtire'], $item->qty);
              break;
            }
          }
        }

        foreach ($modelCount as $model => $count) {
          $modelCount[$model] = array_sum($count);
        }

        $highestValue = max($modelCount);
        $highestModel = array_search(max($modelCount), $modelCount);

        if ($highestValue >= 3 && $highestValue < 5) {
          $data = Self::options()[$highestModel]['shipping'][4];
        } else if ($highestValue > 4) {
          $data = Self::options()[$highestModel]['shipping'][5];
        } else {
          $data = Self::options()[$highestModel]['shipping'][$highestValue];
        }

        if ($request->city == 1 || $request->city == 2) {
          $data = Self::options()['shippingDef'];
        }

        return json_encode($data);
      }

      public function checkFitting(Request $request)
      {

        $data = json_decode(json_encode($request->input()));

//        Session::remove('cartOptions');


        if ($data->fitting >= 1) {
          switch ($data->total_items) {
            case 1:
            case 2:
            case 4: {
              foreach ($data as $key => $item) {
                (is_numeric($item)) ? $item = (int) $item : '';
                Session::put('cartOptions.' . $key, $item);
              }

              $catCount = [];

              foreach (Cart::content() as $key => $item) {
                $size = $item->options->tire['d3'];
                $item = str_replace('App\\Models\\', '', $item->associatedModel);
                array_push($catCount, $item);
                $cats = [
                  $item,
                  $size,
                ];
              }

              $cats = array_unique($cats);
              $catCount = count(array_unique($catCount));

              if ($catCount === 1) {
                $cat = $cats[0];
                $size = $cats[1];
              }

              if ($cat == 'Autotire') {
                if ($size <= 16) {
                  Session::put('cartOptions.fitting_price', Self::options()[$cat]['fitting'][16][$data->total_items]);
                } else {
                  Session::put('cartOptions.fitting_price', Self::options()[$cat]['fitting'][$size][$data->total_items]);
                }
//                dd($cat, $size, $data->total_items, Self::options()[$cat]['fitting'][$size][$data->total_items]);
              } else {
                Session::put('cartOptions.fitting_price', Self::options()[$cat]['fitting'][$data->total_items]);
              }
              break;
            }
            default:
              Session::put('cartOptions.fitting_price', 0);
          }
        } else {
          Session::put('cartOptions.fitting_price', 0);
        }

        return json_encode(['cartOptions' => Session::get('cartOptions')]);
      }

    public static function addProduct($model, $tire_id, $quantity = 4)
    {

        switch($model) {
            case 'Autotire': {

                $tire = new Autotire;

                $tire = $tire->query()->with('tread')->selectRaw('auto_treads.*, auto_tires.*, auto_tires.comment as tire_comment')
                    ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                    ->where('auto_tires.tire_id', $tire_id)
                    ->first();
                $image = 'auto';
                $availability = $tire->dotAvailable;
                break;
            }
            case 'Quadr': {

                $tire = new Quadr;

                $tire = $tire->query()->with('tread')->selectRaw('quadr_treads.*, quadr_tires.*, quadr_tires.comment as tire_comment')
                    ->rightJoin('quadr_treads', 'quadr_tires.make_id', '=', 'quadr_treads.tread_id')
                    ->where('quadr_tires.tire_id', $tire_id)
                    ->first();
                $image = 'quadr';
                $availability = $tire->dotAvailable;
                break;
            }
            case 'Moto': {

                $tire = new Moto;

                $tire = $tire->query()->with('tread')->selectRaw('moto_treads.*, moto_tires.*, moto_tires.comment as tire_comment')
                    ->rightJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                    ->where('moto_tires.tire_id', $tire_id)
                    ->first();
                $image = 'moto';
                $availability = $tire->dotAvailable;
                break;
            }
            case 'Bigtire': {

                $tire = new Bigtire;

                $tire = $tire->query()->with('tread')->selectRaw('bigtire_treads.*, big_tires.*, big_tires.comment as tire_comment')
                  ->rightJoin('bigtire_treads', 'big_tires.make_id', '=', 'bigtire_treads.tread_id')
                  ->where('big_tires.tire_id', $tire_id)
                  ->first();
                $image = 'industrial';
                $availability = $tire->dotAvailable;
                break;
            }
            case 'Rim': {
                $tire = new Rim;

                $tire = $tire->query()->with('tread')->selectRaw('rim_makes.*, rims.*, rims.comment as rim_comment, rim_makes.make_id as tread_id')
                  ->rightJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
                  ->where('rims.rim_id', $tire_id)
                  ->first();
                $image = 'rims';
                $availability = $tire->dotAvailable;
                break;
            }
            case 'Quadrim': {
              $tire = new Quadrim;

              $tire = $tire->query()->with('tread')->selectRaw('quadrim_makes.*, quadrims.*, quadrims.comment as rim_comment, quadrim_makes.make_id as tread_id')
                ->rightJoin('quadrim_makes', 'quadrims.make_id', '=', 'quadrim_makes.make_id')
                ->where('quadrims.rim_id', $tire_id)
                ->first();
              $image = 'quadrims';
              $availability = $tire->dotAvailable;
              break;
            }
            case 'Stud': {
              $tire = new Stud;

              $tire = $tire->query()->with('tread')->selectRaw('studs_treads.*, studs.*, studs.comment as stud_comment')
                ->leftJoin('studs_treads', 'studs.make_id', '=', 'studs_treads.tread_id')
                ->where('studs.stud_id', $tire_id)
                ->first();
              $image = 'stud';
              $availability = $tire->dotAvailable;
              break;
            }
        }

//        if ($cart->content()->isEmpty()) {
//          return $cart->add($tire_id, $tire->title, $quantity, $tire->price2, 0, ['tire' => $tire->toArray(), 'link' => $tire->link, 'total' => $total])
//            ->associate('App\Models\\' . ucfirst($model));
//        } else {
//          foreach ($cart->content() as $item) {
//            if ($item->id == $tire_id) {
//              return $cart->update($item->rowId, $item->qty + $quantity);
//            } else {
              return Cart::instance(Session::getId())->add($tire_id, $tire->fullName, $quantity, $tire->price2, 0, ['tire' => $tire->toArray(), 'tireObj' => $tire, 'link' => $tire->link, 'image' => $image, 'availability' => $availability])
                ->associate('App\Models\\' . ucfirst($model));
//            }
//          }
//        }

    }

    public function remove($id)
    {

        Cart::remove($id);

        return redirect()->back();

    }

    public function ajaxRefresh(Request $request)
    {

        $tire = Autotire::with('tread')->selectRaw('auto_tires.*, auto_treads.*')
                                             ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                             ->where('auto_tires.tire_id', $request->tire_id)
                                             ->first();

        $totalItems = Cart::count();
        $totalItems = $totalItems + 4;
        $totalSum = (float) Cart::totalFloat();
        $totalSum = $totalSum + ($tire->price2 * 4);

        return response()->json([
          'tire' => $tire,
          'total_items' => $totalItems,
          'total_sum' => $totalSum
        ]);
    }

    public function ajaxChangeQty(Request $request)
    {
        if ($request->qty <= 0) $request->qty = 1;
        Cart::update($request->tire_id, $request->qty);

        $totalItems = Cart::count();
        $totalSum = Cart::subtotal();

        return json_encode(['total_items' => $totalItems, 'total_sum' => $totalSum]);
    }

    public function pay($data)
    {

      $order_id = $data['order_id'];

      try {
        WebToPay::redirectToPayment([
          'projectid' => '209872',
          'sign_password' => 'ef3e86e4902558e3779ecc84d72a6d8c',
          'orderid' => $data['order_id'],
          'amount' => $data['amount'],
          'p_email' => $data['email'],
          'currency' => 'EUR',
          'country' => 'LV',
          'accepturl' => route('order.success', $order_id),
          'cancelurl' => Self::getSelfUrl() . 'pasutijums',
          'callbackurl' => Self::getSelfUrl() . 'callback',
          //'test' => 1,
        ]);
      } catch (Exception $exception) {
        echo get_class($exception) . ':' . $exception->getMessage();
      }
    }

    public function end() {
      //Cart::destroy();
      //Session::flush();
      if (Auth::check()) {
        $order = Order::where('userId', Auth::user()->id)->where('status', 1)->first();
      } else {
        $order = Order::where('userIp', user_ip)->where('status', 1)->first();
      }


      $order->status = 2;
      $order->payment = request()->payment;
      $order->save();
      $order_id = $order->id;
      return Redirect::route('order.done')->with('order_id', $order_id);
    }

    public function order_success($id) {
      Session::put('order_id', $id);

      return Redirect::route('order.done');
    }

    public function emailText($details) {
      $out = '<div style="width: 70%; margin: 20px auto; font-family: Arial, Helvetica, sans-serif;">
        <div style="display: flex;">
          <div style="margin: 10px 25px;">
            R1 Riepu serviss <br>
            Kalnciema iela 39 <br>
            Rīga, LV-1046 <br>
            Tālrunis: +37167910555 <br>
            E-pasts: <a href="mailto:info@r1riepas.lv">info@r1riepas.lv</a>
          </div>
          <div style="margin-left: auto;">
            <img src="https://r1riepas.lv/img/r1-riepas-logo-1515661637.jpg" alt="" style="height: 100px;">
          </div>
        </div>
        <div style="background-color: lightgrey; padding: 5px;">
          <b>Pasūtījuma informācija</b>
        </div>
        <div style="margin: 10px 25px;">
          Pasūtījuma Nr. ' . $details->id . '<br>
          Pasūtījuma datums: ' . $details->created_at . '<br>
          Pasūtījuma stāvoklis: <b>Pasūtījums tiek pārbaudīts.</b> <br> <br>
          Menedžeris ar Jums sazināsies tuvākajā laikā, lai informētu par pasūtījuma gaitu.
        </div>
        <div style="background-color: lightgrey; padding: 5px;">
          <b>Pasūtītāja dati</b>
        </div>
        <div>
          <div style="margin: 10px 25px;">
            E-pasts: ' . $details->info['email'] . '<br>
            Pasūtītājs: ' . $details->info['name'] . ' ' . $details->info['surname'] . '<br>
            Tālruņa numurs: ' . $details->info['phone_number'] . '<br>';
            if (isset($details->info['company_registration_number'])) {
              $out .= 'Reģistrācijas numurs: ' . $details->info['company_registration_number'] . '<br>';
              if (isset($details->info['company_pvn_number'])) $out .= 'PVN Numurs: ' . $details->info['company_pvn_number'] . '<br>';
              $out .= 'Uzņēmuma nosaukums: ' . $details->info['company_name'] . '<br>
              Juridiskā adrese: ' . $details->info['company_address'] . '<br>';
            }

            if (isset($details->info['fitting_address']) && $details->info['fitting_address'] == 1) {
              $out .= 'Saņemšanas vieta: Ulbroka, Acones iela 2A';
            } elseif (isset($details->info['fitting_address']) && $details->info['fitting_address'] == 2) {
              $out .= 'Saņemšanas vieta: Rīga, Kalnciema iela 39';
            } else {
              if (isset($details->info['shipping_city']) && $details->info['shipping_city'] == 1) {
                $out .= 'Piegādes adrese: Rīga, ' . $details->info['shipping_address'];
                if (isset($details->info['door_code'])) $out .= ', Durvju kods: ' . $details->info['door_code'];
              } elseif (isset($details->info['shipping_city']) && $details->info['shipping_city'] == 2) {
                $out .= 'Piegādes adrese: Salaspils, ' . $details->info['shipping_address'];
                if (isset($details->info['door_code'])) $out .= ', Durvju kods: ' . $details->info['door_code'];
              } else {
                $out .= 'Piegādes adrese: Cits, ' . $details->info['shipping_address'];
                if (isset($details->info['door_code'])) $out .= ', Durvju kods: ' . $details->info['door_code'];
              }
            }
          $out .= '</div>
          <div style="background-color: lightgrey; padding: 5px;">
            <b>Pasūtītās preces</b>
          </div>
          <div style="margin: 10px 25px;">
            <table style="border-collapse: collapse;
                          border-spacing: 0;
                          width: 100%;">
              <tr>
                <th style="text-align: left;">Nosaukums</th>
                <th style="text-align: center;">Skaits</th>
                <th style="text-align: center;">Cena</th>
                <th style="text-align: center;">Summa</th>
              </tr>';
              foreach ($details->cart as $item) {
              $out .= '<tr>
                <td>' . $item->options->tireObj->fullName . '</td>
                <td style="text-align: center;">' . $item->qty . '</td>
                <td style="text-align: center;">€ ' . $item->price . '</td>
                <td style="text-align: center;">€ ' . ($item->price * $item->qty) . '</td>
              </tr>';
              }
              if ($details->fit_price > 0) {
              $out .= '<tr>
                <td>Montāža</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: center;">€ ' . substr($details->fit_price, 0, -2) . '</td>
                <td style="text-align: center;">€ ' . substr($details->fit_price, 0, -2) . '</td>
              </tr>';
              }
              if ($details->delivery_price > 0) {
              $out .= '<tr>
                <td>Piegāde</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: center;">€ ' . substr($details->delivery_price, 0, -2) . '</td>
                <td style="text-align: center;">€ ' . substr($details->delivery_price, 0, -2) . '</td>
              </tr>';
              }
              $out .= '<tr>
                <td></td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"><b>Kopā:</b></td>';
                if ($details->delivery_price > 0) {
                  $out .= '<td style="text-align: center;">€ ' . ((int) $details->price + (int) substr($details->delivery_price, 0, 2)) . '</td>';
                } elseif ($details->fit_price > 0) {
                  $out .= '<td style="text-align: center;">€ ' . ((int) $details->price + (int) substr($details->fit_price, 0, 2)) . '</td>';
                } else {
                  $out .= '<td style="text-align: center;">€ ' . $details->price . '</td>';
                }
              $out .= '</tr>
            </table>
          </div>
        </div>
        <div style="background-color: lightgrey; padding: 5px;">
          <b>Pasūtītāja komentāri, piezīmes</b>
        </div>
        <div style="margin: 10px 25px;">';
          if (isset($details->info['notes'])) $out .= $details->info["notes"] . '<br>';
          if ($details->info['car_brand'] != '') $out .= 'Auto marka: ' . $details->info['car_brand'] . '<br>';
          if ($details->info['car_model'] != '') $out .= 'Auto modelis: ' . $details->info['car_model'] . '<br>';
          if ($details->info['car_release_year'] != '') $out .= 'Izlaiduma gads: ' . $details->info['car_release_year'] . '<br>';
          if ($details->info['car_engine_size'] != '') $out .= 'Dzinēja tilpums: ' . $details->info['car_engine_size'] . '<br>';
        $out .= '</div>
        <div style="background-color: lightgrey; padding: 5px;">
          <b>Apmaksas informācija</b>
        </div>
        <div style="margin: 10px 25px;">';
          if ($details->payment == 1) {
            $out .= 'Apmaksa saņemšanas brīdī';
          } elseif ($details->payment == 2) {
            $out .= 'Bankas pārskaitījums';
          } else {
            $out .= 'Tiešsaistes apmaksa';
          }
          $out .= '<br>
        </div>';
        if (isset($details->info['email_notifications'])) $out .= '<div style="background-color:lightgrey;padding:5px"><b>Piekrītu, ka man tiks sūtīti paziņojumi par akcijām un jaunumiem uz norādīto e-pastu</b></div>';
      $out .= '</div>';

      return $out;
    }

    public function order_done() {

      if (!Session::has('order_id')) {
        return Redirect::route('home');
      } else {
        $order_id = Session::get('order_id');
      }

      $order = Order::findOrFail($order_id);

      $order->status = 2;
      switch($order->payment) {
        case 1:
        case 2: {
          $order->payment = $order->payment;
          break;
        }
        default:
	      $order->payment = 3;
      }

      //dd($data);
      $order->save();

      $data = $order;
      $data->info = unserialize($data->info);

      $data->cart = Cart::content();

      $mailText = $this->emailText($data);

      $mailer = new Mailer();
      $mailer->addRecipient($data->info['email']);
      $bcc = 'karlis@r1riepas.lv';
      if ($bcc) {
        $mailer->addBCC($bcc);
        $mailer->addBCC('info@r1.com.lv');
      }
      $mailer->subject = 'R1riepas.lv internetveikala pasūtījums';
      $mailer->message = $mailText;
      $mailer->send();

//      if (!Mail::to($data->info['email'])->bcc('info@r1.com.lv')->send(new \App\Mail\CartMail($data))) {  }

      Session::remove('cart');
      Session::remove('cartOptions');
      Session::remove('cart_options');
      Session::remove('person');
      Session::remove('order_id');
      \Cart::destroy();

      return view('cart.done', compact('order_id'));
    }

    public function printCart($id)
    {

      $order = Order::where('id', $id)->first();
      $orders = unserialize($order->info);
      $order->cart = (object) $orders[0];
      $order->info = (object) $orders[1];

//      dd($order);

      $html = <<<EON
        <div style="color: red;"><b>asd</b></div>
      EON;

      $pdf = new Pdf();
      $pdf->setPrintHeader(false);
      $pdf->setPrintFooter(false);

      $pdf->AddPage();
      $pdf->SetMargins(10,0, 10, 0);


      $pdf->WriteHTML($html);
      $pdf->Output();
    }
}

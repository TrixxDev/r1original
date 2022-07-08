<?php

namespace App\Http\Controllers;

use App\Models\Autotire;
use App\Models\Moto;
use App\Models\Order;
use App\Models\Pdf;
use App\Models\Quadr;
use App\Models\Bigtire;
use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\CartItemOptions;
use http\Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebToPay;

class CartController extends Controller
{

    public $cart;

  /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cart = new Cart();
    }

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
      $options = [
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

      return $options;
    }

    public function index(Request $request)
    {
      if ($request->post()) {
          $delivery = [];
//          dd($this->cart->content());
//        dd($this->cart->content()['765d9b453db5045a4f84faddc5a18876']->associatedModel);

//        $this->checkCart($request->data);

//        foreach ($request->data as $key => $value) {
//          Session::put('.' . $key, $value);
//        }

//        foreach ($this->cart->content() as $key => $item) {
//          array_push($delivery, [
//            'cart_delivery_radio' => $item->cart_delivery_radio,
//            'shipping_city' => $item->shipping_city,
//            'shipping_address' => $item->shipping_address,
//            'door_code' => $item->door_code,
//          ]);
//        }
//        dd($delivery);
//
//        $cartData = serialize($cartData);
//
//        $amount = str_replace(['.', ','], '', $this->cart->subtotal());
//
//        $order = new Order;
//        $order->status = 1;
//        $order->price = substr($amount, 0, -2);
//        $order->delivery_price = 0;
//        $order->fit_price = 0;
//        $order->info = $cartData;
//        $order->save();
//
//        $order_id = $order->id;
//        $amount1 = $amount;
//        $email = Session::get('email');
//
//        $data = ['order_id' => $order_id, 'amount' => $amount1, 'email' => $email];

//        return 123;
//        dd($request->request);
        return redirect(route('order'));
      }
//        dd($this->cart->content());
        return view('cart.home');
      }

      public function checkShipping($data = '')
      {
        Session::remove('cartOptions');


        Session::put('cartOptions.shipping', 1);

        dd(Session::all());

        return json_encode($data);
      }

      public function checkFitting(Request $request)
      {

        $cart = new Cart;

        $data = json_decode(json_encode($request->input()));

        Session::remove('cartOptions');

        Session::put('cartOptions.fitting', 1);
        Session::put('cartOptions.total_items', $data->total_items);

        if ($data->fitting_needs == 1) {
          switch ($data->total_items) {
            case 1:
            case 2:
            case 4: {
              foreach ($data as $key => $item) {
                (is_numeric($item)) ? $item = (int) $item : '';
                Session::put('cartOptions.' . $key, $item);
              }

              $catCount = [];

              foreach ($cart->content() as $key => $item) {
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
                Session::put('cartOptions.fitting_price', Self::options()[$cat]['fitting'][$size][$data->total_items]);
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

        $cart = new Cart();

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
        }

//        if ($cart->content()->isEmpty()) {
//          return $cart->add($tire_id, $tire->title, $quantity, $tire->price2, 0, ['tire' => $tire->toArray(), 'link' => $tire->link, 'total' => $total])
//            ->associate('App\Models\\' . ucfirst($model));
//        } else {
//          foreach ($cart->content() as $item) {
//            if ($item->id == $tire_id) {
//              return $cart->update($item->rowId, $item->qty + $quantity);
//            } else {
              return $cart->add($tire_id, $tire->title, $quantity, $tire->price2, 0, ['tire' => $tire->toArray(), 'link' => $tire->link, 'image' => $image, 'availability' => $availability])
                ->associate('App\Models\\' . ucfirst($model));
//            }
//          }
//        }

    }

    public function remove($id)
    {

        $this->cart->remove($id);

        return redirect()->back();

    }

    public function ajaxRefresh(Request $request)
    {

        $tire = Autotire::with('tread')->selectRaw('auto_tires.*, auto_treads.*')
                                             ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
                                             ->where('auto_tires.tire_id', $request->tire_id)
                                             ->first();

        $totalItems = $this->cart->count();
        $totalItems = $totalItems + 4;
        $totalSum = (float) $this->cart->totalFloat();
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
        $this->cart->update($request->tire_id, $request->qty);

        $totalItems = $this->cart->count();
        $totalSum = $this->cart->subtotal();

        return json_encode(['total_items' => $totalItems, 'total_sum' => $totalSum]);
    }

    public function order(Request $request)
    {
      if ($request->post()) {
        $cartData = [];

        foreach ($this->cart->content() as $key => $item) {
          array_push($cartData, [
            'tire_id' => $item->id,
            'title' => $item->name,
            'quantity' => $item->qty,
            'price' => (int) $item->price,
          ]);
//            if (){
//              allow order online;
//            }
        }
        $cartData[] = $request->data;

        $cartData = serialize($cartData);

        $amount = str_replace(['.', ','], '', $this->cart->subtotal());

        $order = new Order;
        $order->status = 1;
        $order->price = substr($amount, 0, -2);
        $order->delivery_price = 0;
        $order->fit_price = 0;
        $order->info = $cartData;
        $order->save();

        $order_id = $order->id;
        $amount1 = $amount;
        $email = Session::get('email');



        $data = ['order_id' => $order_id, 'amount' => $amount1, 'email' => $email];

        if (isset($request->pay)) {
          return $this->pay($data);
        }
        $user_data = $request->input('data');
        if (!isset($user_data['email_notifications'])) {
          Session::remove('cart.email_notifications');
        }
        foreach ($user_data as $key => $value) {
          Session::put('cart.' . $key, $value);
        }
        Session::put('person', $request->person);

        $cats = [];
        $dogs = [];


        foreach ($this->cart->content() as $key => $item) {
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

    public function pay($data)
    {

      try {
        WebToPay::redirectToPayment([
          'projectid' => '230756',
          'sign_password' => 'c32f7c8bde605f29bb9c115bc85713a8',
          'orderid' => $data['order_id'],
          'amount' => $data['amount'],
          'p_email' => $data['email'],
          'currency' => 'EUR',
          'country' => 'LV',
          'accepturl' => Self::getSelfUrl() . '',
          'cancelurl' => Self::getSelfUrl() . 'pasutijums',
          'callbackurl' => Self::getSelfUrl() . 'callback.php',
          'test' => 1,
        ]);
      } catch (Exception $exception) {
        echo get_class($exception) . ':' . $exception->getMessage();
      }
    }

    public function printCart($id)
    {

      $order = Order::where('id', $id)->first();
      $orders = unserialize($order->info);
      $order->cart = (object) $orders[0];
      $order->info = (object) $orders[1];

//      dd($order);

      $html = 'You can now easily print text mixing different styles: <b>bold</b>, <i>italic</i>,
      <u>underlined</u>, or <b><i><u>all at once</u></i></b>!<br><br>You can also insert links on
      text, such as <a href="http://www.fpdf.org">www.fpdf.org</a>, or on an image: click on the logo.';

      $pdf = new PDF();
      /* Column headings */
      $header = array('Vārds', 'Uzvārds');
      /* Data loading */
//      $data = $pdf->LoadData($order);
// First page
      $pdf->AddPage();
      $pdf->SetFont('Arial','',20);
      $pdf->Write(5,"To find out what's new in this tutorial, click ");
      $pdf->SetFont('','U');
      $link = $pdf->AddLink();
      $pdf->Write(5,'here',$link);
      $pdf->SetFont('');
// Second page
      $pdf->AddPage();
      $pdf->SetLink($link);
//      $pdf->Image('logo.png',10,12,30,0,'','http://www.fpdf.org');
      $pdf->SetLeftMargin(45);
      $pdf->SetFontSize(14);
      foreach($header as $col)
        $pdf->Cell(38,7,$col,1);
      $pdf->Ln();
      /* Data */
      unset($order->info->status);
      foreach($order->info as $col)
      {
          $pdf->Cell(38,6,$col,1);
        $pdf->Ln();
      }
      $pdf->WriteHTML($html);

      $pdf->Output();
      die;
    }
}

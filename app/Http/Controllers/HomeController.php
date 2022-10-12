<?php

namespace App\Http\Controllers;

use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function checkSession(Request $request) {

      if ($request->isMethod('post')) {
        $user = User::findOrFail(Auth::user()->id);
        $minutesToAdd = gmdate('i', env('session_lifetime'));

        $userTime = \Carbon\Carbon::now()->addMinutes($minutesToAdd)->format('Y-m-d H:i');

        $user->timestamps = false;
        $user->lastActivityTime = $userTime;
        $user->save();
        return 1;
      }

      if (Auth::check()) {

        $user = User::findOrFail(Auth::user()->id);
        $currentTime = date('Y-m-d H:i', time());

        $timeLeft = \Carbon\Carbon::parse($user->lastActivityTime)->subMinutes(3)->format('Y-m-d H:i');

        if ($currentTime === $timeLeft) {
          return 1;
        } else {
          return 0;
        }

      }
    }

    public function login(Request $request) {
      if ($request->post()) {

        $field = (str_contains($request->username, '@') || !$request->username) ? 'email' : 'username';
        $request->merge([$field => $request->username]);

        $request->validate([
          $field => 'required|string',
          'password' => 'required|string',
        ]);

        $credentials = array(
          $field => $request->$field,
          "password" => $request->password,
        );

        if (Auth::attempt($credentials)) {
          return 1;
        }

        return 0;

      }
      return view('testings');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function my_account()
    {
        return view('auth.profile');
    }

    public function identity()
    {
        return view('auth.sub.identity');
    }

    public function address()
    {
        return view('auth.sub.address');
    }

    public function history()
    {
        return view('auth.sub.history');
    }

    public function order_slip()
    {
        return view('auth.sub.order_slip');
    }

    public function contacts()
    {
        return view('main.contacts');
    }

    public function terms()
    {
        return view('main.terms');
    }

    public function about()
    {
        return view('main.about');
    }

    public function moto_terms()
    {
        return view('main.moto_terms');
    }

    public function services()
    {
        return view('main.services');
    }

    public function conditioner()
    {
        return view('main.conditioner');
    }

    public function pages(Request $request, $page)
    {

      $db = DB::table('pages')->where('route', $page)->first();

      if ($db === null) {
        abort(404);
      }

      return view('pages.' . $page);
    }

    public function accrualOrder(Request $request)
    {
      function uploadFTP($server, $username, $password, $local_file, $remote_file){
        $connection = ftp_connect($server);

        if (@ftp_login($connection, $username, $password)){
        }else{
          return false;
        }

        ftp_put($connection, $remote_file, $local_file, FTP_BINARY);
        ftp_close($connection);
        return true;
      }

      $location = $request->info['location'];
      switch ($location) {
        case 'URS':
          $location = 'Noliktava';
          break;
        case 'KRS':
          $location = 'Veikals';
          break;
        default:
          break;
      }
      $summa = $request->info['total'];
      $summa_pvn = $summa - ($summa / 1.21);
      $summa_pvn = number_format((float)$summa_pvn, 2, '.', '');

      $article = $request->info['article'];
//      $prod = $request->info['prod'];
      $quantity = $request->info['qty'];
      $price = $request->info['price'];
      $price_pvn = ($price / 1.21);
      $price_pvn = number_format((float)$price_pvn, 2, '.', '');
      $comment = $request->info['comments'];
      $user = $request->info['user'];

      @$montage = $request->info['montage'];
      @$montage_price = round($request->info['price_montage']);
      @$montage_price_pvn = $montage_price / 1.21;

      @$safe = $request->info['safe'];
      @$safe_price = $request->info['price_safe'];
      @$safe_price_pvn = $safe_price / 1.21;

      $xml_order = \Illuminate\Support\Facades\DB::table('xml_orders')->insertGetId([
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s"),
      ]);

      $xml_string = '<?xml version="1.0" encoding="UTF-8"?>';
      $xml_string .= '<AccrualPZ>&#10;';
      $xml_string .= '&#009;<PZHeader>&#10;';
      $xml_string .= '&#009;&#009;<Struktura>' . $location . '</Struktura>&#10;';
      $xml_string .= '&#009;&#009;<Type>6</Type>&#10;';
      $xml_string .= '&#009;&#009;<WEB>' . $xml_order . '</WEB>&#10;';
      $xml_string .= '&#009;&#009;<Datums>' . date('d.m.Y') . '</Datums>&#10;';
//  $xml_string .= '&#009;&#009;<PartnNosaukums>Klients pasūtītājs</PartnNosaukums>&#10;';
      $xml_string .= '&#009;&#009;<PVNSumma>' . $summa_pvn . '</PVNSumma>&#10;';
      $xml_string .= '&#009;&#009;<Valuta>EUR</Valuta>&#10;';
      if ($comment != '') {
        $xml_string .= '&#009;&#009;<Piezimes>' . $comment . '</Piezimes>&#10;';
      }
      $xml_string .= '&#009;&#009;<SasPerson>' . $user . '</SasPerson>&#10;';
      $xml_string .= '&#009;</PZHeader>&#10;';
      $xml_string .= '&#009;<Ieraksti>&#10;';
      $xml_string .= '&#009;&#009;<Ieraksts>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Artikuls>' . $article . '</Artikuls>&#10;';
//      $xml_string .= '&#009;&#009;&#009;<Nosaukums>' . $prod . '</Nosaukums>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Mervieniba>gab</Mervieniba>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Cena>' . $price_pvn . '</Cena>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Daudzums>' . $quantity . '.000</Daudzums>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Summa>' . $price . '</Summa>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Nodoklis>PVN 21%</Nodoklis>&#10;';
      $xml_string .= '&#009;&#009;&#009;<Likme>21.00</Likme>&#10;';
      $xml_string .= '&#009;&#009;</Ieraksts>&#10;';
      if ($montage == 1) {
        $xml_string .= '&#009;&#009;<Ieraksts>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Artikuls>04</Artikuls>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Mervieniba>kompl.</Mervieniba>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Cena>' . $montage_price_pvn . '</Cena>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Daudzums>1.000</Daudzums>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Summa>' . $montage_price . '</Summa>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Nodoklis>PVN 21%</Nodoklis>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Likme>21.00</Likme>&#10;';
        $xml_string .= '&#009;&#009;</Ieraksts>&#10;';
      }
      if ($safe == 1) {
        $xml_string .= '&#009;&#009;<Ieraksts>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Artikuls>18</Artikuls>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Mervieniba>kompl.</Mervieniba>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Cena>' . $safe_price_pvn . '</Cena>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Daudzums>1.000</Daudzums>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Summa>' . $safe_price . '</Summa>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Nodoklis>PVN 21%</Nodoklis>&#10;';
        $xml_string .= '&#009;&#009;&#009;<Likme>21.00</Likme>&#10;';
        $xml_string .= '&#009;&#009;</Ieraksts>&#10;';
      }
      $xml_string .= '&#009;</Ieraksti>&#10;';
      $xml_string .= '</AccrualPZ>';

      $dom = new DOMDocument();
      $dom->preserveWhiteSpace = FALSE;
      $dom->loadXML($xml_string);

      $xml_file = fopen(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', 'wb');
      fwrite($xml_file, $xml_string);
      fclose($xml_file);
      //$dom->save(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml');

//      dd(is_file(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml'));
//      $ftp = uploadFTP("212.3.218.22", "r1_web", "RA5bgdGc", dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', "pasutijums$xml_order.xml");
      uploadFTP("212.3.218.22", "r1_web", "RA5bgdGc", dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', "pasutijums$xml_order.xml");

    }
    function fastOrder() {
      return view('/testing3');
    }
}

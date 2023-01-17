<?php

namespace App\Http\Controllers;

use App\Models\Autostock;
use App\Models\Autotire;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    public static $connection;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
      Self::$connection = false;
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

	$form = (object) ['ownerEmail' => 'indrikis38@gmail.com'];

      $details = [
          'car' => 1,
          'make' => 1,
          'purpose' => 1,
          'office' => 1,
          'day' => 1,
          'date' => 1,
          'time' => 1,
          'longPurpose' => 1
        ];

      Mail::to($form->ownerEmail)->send(new \App\Mail\Mail($details));

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

    public function changeArticles(Request $request)
    {

      if ($request->post()){

        $out = '';
        $count = 0;

        $data = $request->articles;
        $rows = explode("\n", trim($data));

        foreach ($rows as $idx=>$row){
          $row = trim($row);
          if (($idx>-1)&&($row!='')) {
            $fields = explode("\t", $row);

            $article = $fields[0];
            $i3Article = $fields[1];

            $itype = 'i3';

            $tire = Autotire::where('article', $article)->first();
            if (!$tire) continue;
            $stock = Autostock::where('tire_id', $tire->tire_id)->where('itype', $itype)->first();
            if (!$stock) {
              $tire->addSecondaryArticle($i3Article, 'i3');
              $out .= 'Nav atrasts ieraksts ar ID - ' . $tire->tire_id . '<br>';
              $count++;
              continue;
            }
            $stock->article = $i3Article;
            $stock->save();
          }
        }

        return $out . 'Nav atrasti - ' . $count . ' ieraksti';

      }

      return '<form method="post">' . @csrf_field() . '<textarea name="articles" id="" cols="30" rows="10"></textarea><button type="submit">Aiziet</button></form>';

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
        return view('pages.internet-veikals');
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
        return Redirect::to(route('home'));
      }

      return view('pages.' . $page);
    }

    public function accrualOrder(Request $request)
    {

      Self::$connection = ftp_connect('212.3.218.22');

      if (!@ftp_login(Self::$connection, 'r1_web', 'RA5bgdGc')){
        return 'Nesanāk savienoties ar Accrual serveri';
      }

      function uploadFTP($local_file, $remote_file){

        ftp_put(HomeController::$connection, $remote_file, $local_file, FTP_BINARY);
        ftp_close(HomeController::$connection);
        return true;
      }

      $location = $request->info['location'];
      switch ($location) {
        case 'URS':
          $location = 'Noliktava';
          $location_prefix = 'U';
          break;
        case 'KRS':
          $location = 'Veikals';
          $location_prefix = 'K';
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

      $xml_order = DB::table('xml_orders')->insertGetId([
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s"),
      ]);

      $number = str_pad($xml_order, 4, '0', STR_PAD_LEFT);
      $number = $location_prefix . '-' . $number;

      $xml_string = '<?xml version="1.0" encoding="UTF-8"?>';
      $xml_string .= '<AccrualPZ>';
      $xml_string .= '<PZHeader>';
      $xml_string .= '<Struktura>' . $location . '</Struktura>';
      $xml_string .= '<Type>6</Type>';
      $xml_string .= '<WEB>' . $xml_order . '</WEB>';
      $xml_string .= '<Datums>' . date('d.m.Y') . '</Datums>';
//  $xml_string .= '<PartnNosaukums>Klients pasūtītājs</PartnNosaukums>';
      $xml_string .= '<PVNSumma>' . $summa_pvn . '</PVNSumma>';
      $xml_string .= '<Valuta>EUR</Valuta>';
      if ($comment != '') {
        $xml_string .= '<Piezimes>' . $number . ' ' . $comment . '</Piezimes>';
      } else {
        $xml_string .= '<Piezimes>' . $number . '</Piezimes>';
      }
      $xml_string .= '<SasPerson>' . $user . '</SasPerson>';
      $xml_string .= '</PZHeader>';
      $xml_string .= '<Ieraksti>';
      $xml_string .= '<Ieraksts>';
      $xml_string .= '<Artikuls>' . $article . '</Artikuls>';
//      $xml_string .= '<Nosaukums>' . $prod . '</Nosaukums>';
      $xml_string .= '<Mervieniba>gab</Mervieniba>';
      $xml_string .= '<Cena>' . $price_pvn . '</Cena>';
      $xml_string .= '<Daudzums>' . $quantity . '.000</Daudzums>';
      $xml_string .= '<Summa>' . $price . '</Summa>';
      $xml_string .= '<Nodoklis>PVN 21%</Nodoklis>';
      $xml_string .= '<Likme>21.00</Likme>';
      $xml_string .= '</Ieraksts>';
      if ($montage == 1) {
        $xml_string .= '<Ieraksts>';
        $xml_string .= '<Artikuls>04</Artikuls>';
        $xml_string .= '<Mervieniba>kompl.</Mervieniba>';
        $xml_string .= '<Cena>' . $montage_price_pvn . '</Cena>';
        $xml_string .= '<Daudzums>1.000</Daudzums>';
        $xml_string .= '<Summa>' . $montage_price . '</Summa>';
        $xml_string .= '<Nodoklis>PVN 21%</Nodoklis>';
        $xml_string .= '<Likme>21.00</Likme>';
        $xml_string .= '</Ieraksts>';
      }
      if ($safe == 1) {
        $xml_string .= '<Ieraksts>';
        $xml_string .= '<Artikuls>18</Artikuls>';
        $xml_string .= '<Mervieniba>kompl.</Mervieniba>';
        $xml_string .= '<Cena>' . $safe_price_pvn . '</Cena>';
        $xml_string .= '<Daudzums>1.000</Daudzums>';
        $xml_string .= '<Summa>' . $safe_price . '</Summa>';
        $xml_string .= '<Nodoklis>PVN 21%</Nodoklis>';
        $xml_string .= '<Likme>21.00</Likme>';
        $xml_string .= '</Ieraksts>';
      }
      $xml_string .= '</Ieraksti>';
      $xml_string .= '</AccrualPZ>';

      $sync = new SyncController();
      $request = request()->merge(['article' => $article]);
      $old_stocks = $sync->accrual($request);
      $old_stocks = json_decode($old_stocks);

      $dom = new DOMDocument();
      $dom->preserveWhiteSpace = FALSE;
      $dom->loadXML($xml_string);
      $dom->formatOutput = TRUE;

      $xml_string = $dom->saveXML();

      $xml_file = fopen(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', 'wb');
      fwrite($xml_file, $xml_string);
      fclose($xml_file);

      //$dom->save(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml');

//      dd(is_file(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml'));
//      $ftp = uploadFTP("212.3.218.22", "r1_web", "RA5bgdGc", dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', "pasutijums$xml_order.xml");
      uploadFTP(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', "pasutijums$xml_order.xml");

      sleep(4);

      $request = request()->merge(['article' => $article]);
      $new_stocks = $sync->accrual($request);
      $new_stocks = json_decode($new_stocks);

      switch ($location_prefix) {
        case 'U': {
          if ($new_stocks->urs_quantity != $old_stocks->urs_quantity) {
            return json_encode(['success' => 'Pasūtījums ir pieņemts!<br><b>' . $number . '</b>']);
          } else {
            return json_encode(['danger' => 'Pasūtījums netika izveidots!']);
          }
        }
        case 'K': {
          if ($new_stocks->krs_quantity != $old_stocks->krs_quantity) {
            return json_encode(['success' => 'Pasūtījums ir pieņemts!<br><b>' . $number . '</b>']);
          } else {
            return json_encode(['danger' => 'Pasūtījums netika izveidots!']);
          }
        }
      }

      return true;

    }

    public function fastOrder() {
      $param = (object) request()->input();

      return view('/testing3', compact('param'));
    }
}

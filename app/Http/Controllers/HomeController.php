<?php

namespace App\Http\Controllers;

use App\Broadcasting\UpdateStockChannel;
use App\Helper\SmsSender;
use App\Helper\Tires;
use App\Models\Audit;
use App\Models\Autostock;
use App\Models\Autotire;
use App\Models\Office;
use App\Models\Quickorder;
use App\Models\Service;
use App\Models\Slot;
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

    public function dragNdrop(Request $request) {
      return view('testings');
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

//    public function about()
//    {
//        return view('pages.internet-veikals');
//    }

    public function moto_terms()
    {
        return view('main.moto_terms');
    }

    public function services()
    {
        return view('main.services');
    }

//    public function conditioner()
//    {
//        return view('pages.kondicionieris');
//    }

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
      $mobile = 0;
      $mobile_number = '';
      if (isset($request->info['mobile'])) {
        $mobile = $request->info['mobile'];
        $mobile_number = ($mobile == 1) ? '' . $request->info['mobile_number'] : '';
      }

      @$montage = $request->info['montage'];
      @$montage_price = round($request->info['price_montage']);
      @$montage_price_pvn = $montage_price / 1.21;

      @$safe = $request->info['safe'];
      @$safe_price = $request->info['price_safe'];
      @$safe_price_pvn = $safe_price / 1.21;

      $xml_order = new QuickOrder;
      $xml_order->office = $request->info['location'];
      $xml_order->item_article = $article;
      $xml_order->quantity = $quantity;
      $xml_order->price_per_one = $price;
      $xml_order->total_price = $summa;
      $xml_order->fitting = $montage;
      $xml_order->fitting_price = $montage_price_pvn;
      $xml_order->safe = $safe;
      $xml_order->safe_price = $safe_price_pvn;
      $xml_order->phone = $mobile;
      $xml_order->phone_number = $mobile_number;
      $xml_order->admin_id = $user;
      $xml_order->sms_sended = 0;
      $xml_order->created_at = date("Y-m-d H:i:s");
      $xml_order->updated_at = date("Y-m-d H:i:s");
      $xml_order->save();
      $order = $xml_order;
      $xml_order = $order->order_id;

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
        $xml_string .= '<Piezimes>' . $number . ' ' . $comment . ' T.' . $mobile_number . '</Piezimes>';
      } else {
        $xml_string .= '<Piezimes>' . $number . ' ' . $mobile_number . '</Piezimes>';
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

      $xml_file = fopen(dirname(__DIR__, 3) . '/public/storage/xml/pasutijums' . $xml_order . '-t.xml', 'wb');
      fwrite($xml_file, $xml_string);
      fclose($xml_file);

      $file = dirname(__DIR__, 3) . '/public/storage/xml/pasutijums' . $xml_order . '-t.xml';

      //$dom->save(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml');

//      dd(is_file(dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml'));
//      $ftp = uploadFTP("212.3.218.22", "r1_web", "RA5bgdGc", dirname(__DIR__, 3) . '/xml/pasutijums' . $xml_order . '.xml', "pasutijums$xml_order.xml");
      uploadFTP($file, "pasutijums$xml_order-t.xml");

      if (file_exists($file)) {
        unlink($file);
      }

      sleep(4);

      $request = request()->merge(['article' => $article]);
      $new_stocks = $sync->accrual($request);
      $new_stocks = json_decode($new_stocks);


      switch ($location_prefix) {
        case 'U': {
          if ($new_stocks->urs_quantity != $old_stocks->urs_quantity) {
            Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $order->order_id,0, 'Izveidots jauns ātrais pasūtījums', $order);
            return json_encode(['success' => 'Pasūtījums ir pieņemts!<br><b>' . $number . '</b>', 'orderId' => $number]);
          } else {
            return json_encode(['danger' => 'Pasūtījums netika izveidots!']);
          }
        }
        case 'K': {
          if ($new_stocks->krs_quantity != $old_stocks->krs_quantity) {
//            broadcast(new UpdateStockChannel($article, $new_stocks, 123));
            Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $order->order_id,0, 'Izveidots jauns ātrais pasūtījums', $order);
            return json_encode(['success' => 'Pasūtījums ir pieņemts!<br><b>' . $number . '</b>', 'orderId' => $number]);
          } else {
            return json_encode(['danger' => 'Pasūtījums netika izveidots!']);
          }
        }
      }

      return true;

    }

    public function sendOrderSMS(Request $request)
    {
      $sms = new SmsSender();
      $sms->sendOrderSMS($request->info, $request->orderId);
    }

    public function fastOrder() {
      $param = (object) request()->input();

      $model = new SyncController();
      $links = $model->getStockLinks($param->article);

      return view('/testing3', compact('param', 'links'));
    }

    public function changeName($array, $oldName, $newName)
    {
      if(!array_key_exists($oldName, $array))
      {
        return $array;
      }

      $names = array_keys($array);

      $names[array_search($oldName, $names)] = $newName;

      return array_combine($names, $array);
    }

    public function checkForTaken($args){
      return count(array_filter($args,function($v){return $v !== null;})) === 0;
    }

    public function getPrevQueue($queueList, $iorder, $date)
    {
      $queues = [];
      foreach ($queueList as $queue) {
        $slot = Slot::where('date', $date)->where('iorder', $iorder)->where('queue_id', $queue->queue_id)->first();
        if ($slot) {
          if ($slot->status == 0) {
            $queues[$queue->queue_id] = $slot;
          } else {
            $queues[$queue->queue_id] = null;
          }
        } else {
          $queues[$queue->queue_id] = null;
        }
      }
      return $queues;
    }

    public function getLastNonNullValue($array) {
      $array = array_reverse($array);
      return array_filter($array, function($slot) {
        if ($slot) {
          return $slot->status != 1;
        } else {
          return null;
        }
      });
    }


  public function queuetest(Request $request)
  {

    if ($request->post()) {
      $office = Office::where('office_id', $request->office_id)->first();

      $days = [];

      $date = date('Y-m-d');
      $visibleDays = 8;
      $todayDate = strtotime($date);

      $office->loadMobileQueues();
      foreach ($office->_queues as $queue) {
        $queue->loadWorkingDay($date, false);
        $queue->loadSlots($date, false);
        $slotSizes[] = $queue->_workingDays[$date]->slotSize;
        $workingDays[] = $date;
        for ($i = 1; $i < $visibleDays; $i++) {
          $ndate = date('Y-m-d', strtotime("+{$i} days", $todayDate));
          $queue->loadWorkingDay($ndate, true);
          $queue->loadSlots($ndate, true);
          $workingDays[] = $ndate;
        }
      }

      $workingDays = array_unique($workingDays);

      $_weekDays = array(
        1 => 'Pirmdiena',
        2 => 'Otrdiena',
        3 => 'Trešdiena',
        4 => 'Ceturtdiena',
        5 => 'Piektdiena',
        6 => 'Sestdiena',
        7 => 'Svētdiena',
      );

      $tires = new Tires();
      $timeStep = $tires->arrayGCD($slotSizes);

      $services = Service::orderBy('service_id', 'ASC')->get();

        $out = '<div class="w"><div><div class="reservation">';
        for ($day = 0; $day < $visibleDays; $day++) {

          $date = $workingDays[$day];
          $dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];
          $dateFmt = date('d.m.Y', strtotime($date.' 00:00:00'));
          $today = date('Y-m-d');

          $office->_openQueues = 0;
          foreach ($office->_queues as $queue){
            if ($queue->isVisible($date)) $office->_openQueues++;
          }

          if ($office->_openQueues > 0) {
            $out .= '<h3>' . $office->title . ' | ' . $dayOfWeek . ' ' . $dateFmt . '</h3>';
          } else {
            $out .= '';
          }

          $out .= '<div class="time-list" data-date="' . $date . '" style="margin-left:8px;">';

          $openTime = 0;
          $closeTime = -1;

          if ($openTime==0){
            $openTime = $office->getOpenTime($date);
          } else {
            $t = $office->getOpenTime($date);
            if ($t>0){
              $openTime = min($openTime, $t);
            }
          }
          $closeTime = max($closeTime, $office->getCloseTime($date));

          for ($i=$openTime;$i<$closeTime;$i+=$timeStep) {
            foreach ($office->_queues as $queue) {
              if ($queue->isIntervalBeginning($date,$i)) {
                $office->loadWorkingDays($date);
                $slots[$queue->getSlotNumberByInterval($date, $i)] = [
                  'time' => Office::timeByInterval($i),
                  'slots' => $this->getPrevQueue($office->_workingDays, $queue->getSlotNumberByInterval($date, $i), $date),
                  'date' => $date];
              }
            }
          }

          if (!empty($slots)) {
            foreach ($slots as $slot_iorder => $slot_info){
              if ($date == $slot_info['date']) {
                $freeSlot = $this->getLastNonNullValue($slot_info['slots']);
                if (!empty($freeSlot)) {
                  $slot = $freeSlot[array_key_first($freeSlot)];
                  $slot_id = 'data-slot_id=' . $slot->slot_id;
                  if (stripos($slot->comment, '% darbam') !== false) {
                    $slotText = str_replace('!', '', $slot->comment);
                    $slotText = '<span>' . $slotText . '</span>';
                    $availability = 'discount available';
                    $discount = true;
                  } else {
                    $slotText = 'Brīvs';
                    $slotText = '<span>' . $slotText . '</span>';
                    $availability = 'available';
                    $discount = false;
                  }
                } else {
                  $slotText = 'Aizņemts';
                  $slot_id = '';
                  $availability = 'unavailable';
                  $discount = false;
                }
                $out .= '<div class="time-slot">';
                $out .= '<div ' . $slot_id . ' class="' . $availability . ' slot active">' . $slot_info['time'] . '<br>' . $slotText . '</div>';
                $out .= '<div class="dots">';
                if (!empty($slot_info['slots'])) {
                  foreach ($slot_info['slots'] as $slot) {
                    if ($slot !== null) {
                      if (stripos($slot->comment, '% darbam') !== false) {
                        $out .= '<span class="dot-availability text-center">
                        <span class="dot orange" data-toggle="tooltip" data-html="true" title="Atlaide">
                          <span class="sort-order">orange</span>
                        </span>
                      </span>';
                      } else {
                        $out .= '<span class="dot-availability text-center">
                        <span class="dot green" data-toggle="tooltip" data-html="true" title="Brīvs">
                          <span class="sort-order">green</span>
                        </span>
                      </span>';
                      }
                    } else {
                      $out .= '<span class="dot-availability text-center">
                      <span class="dot red" data-toggle="tooltip" data-html="true" title="Aizņemts">
                        <span class="sort-order">red</span>
                      </span>
                    </span>';
                    }
                  }
                } else {
                  $out .= '<span class="dot-availability text-center">
                  <span class="dot transparent" style="" data-toggle="tooltip" data-html="true" title="Aizņemts">
                    <span class="sort-order">transparent</span>
                  </span>
                </span>';
                }
                $out .= '</div></div>';
              }
            }
          }

          $out .= '</div>';

        }
        $out .= '</div></div>';

        return $out;
    }


    return view('queuetest');

    }
}


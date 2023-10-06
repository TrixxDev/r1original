<?php

namespace App\Http\Controllers\Admin\Records;

use App\Helper\Tires;
use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\BookingForm;
use App\Models\Office;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use App\Models\Workingday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Utility;
use Illuminate\Support\Str;

class RecordController extends Controller
{

  public $startSendWpp;
  public $endSendWpp;
  public $ursWpp = '120363130984594947@g.us';
  public $krsWpp = '120363150684433547@g.us';
  public $now;
  public $timeStep = 15;

  public function __construct()
  {
    $this->startSendWpp = Carbon::create(date('Y'), date('m'), date('d'), 8, 45);
    $this->endSendWpp = Carbon::create(date('Y'), date('m'), date('d'), 18, 00);
    $this->now = Carbon::now();
  }

  public function parse_datetime($str){
    $str = str_replace('-','.',$str);
    $str = str_replace('/','.',$str);

    $date=null; $time=null;
    @list($date,$time) = explode(' ',$str);

    if ($date===null) return false;

    @list($d,$m,$y) = explode('.',$date);
    if (($d===null)||(!ctype_digit($d))||($d<0)||($d>31)) { return false; };
    if (($m===null)||(!ctype_digit($m))||($m<0)||($m>12)) { return false; };
    if (($y===null)||(!ctype_digit($y))||($y<=0)||($y>9999)) { return false; };

    $datestamp = strtotime($y.'-'.$m.'-'.$d);
    if (($datestamp===false)||($datestamp==-1)) return false;

    //echo $datestamp.':'.date('d-m-Y',$datestamp);

    if ($time!==null){
      @list($h,$mi) = explode(':',$time);
      if (($h===null)||(!ctype_digit($h))||($h<0)||($h>24)) { return false; };
      if (($mi===null)||(!ctype_digit($mi))||($mi<0)||($mi>59)) { return false; };
      //if (($s===null)||(!ctype_digit($s))||($s<0)||($s>59)) { return false; };
      $timestamp = strtotime($y.'-'.$m.'-'.$d.' '.$h.':'.$mi);
      if (($timestamp===false)||($timestamp==-1)) return false;
      return $timestamp;
      //echo $timestamp.':'.date('d-m-Y H:i',$timestamp);
    } else {
      return $datestamp;
    }

    return 0;
  }

  public function index(Request $request)
  {

    $_weekDays = [
      1=>'Pirmdiena',
      2=>'Otrdiena',
      3=>'Trešdiena',
      4=>'Ceturtdiena',
      5=>'Piektdiena',
      6=>'Sestdiena',
      7=>'Svētdiena',
    ];

    $offices = Office::all();

    $date = ($request->date !== null) ? $request->date : false;
    if ($date===false) $date = date('Y-m-d');

    $currentDate = strtotime($date);

    $workingDays = [];

    foreach ($offices as $office){
      $office->loadQueues();
      foreach ($office->_queues as $queue){
        $queue->loadWorkingDay($date,true);
        $queue->loadSlots($date,true);
        $workingDays[] = $date;
      }
    }

    return view('admin.records.index', compact('offices', 'date', '_weekDays', 'currentDate'));

  }

  public function queue_ajax(Request $request)
  {

    if ($request->post()) {

      $return=[];
      $errorCount = 0;
      $return['errorCount'] = 0;
      $return['error_fields'] = [
        'f_title'=>'',
        'f_opentime'=>'',
        'f_closetime'=>'',
        'f_visible'=>''
      ];

      $date = $request->d;
      $q = $request->q;
      switch ($request->f_purpose) {
        case('f_purpose0'): {
          $f_purpose=0;
          break;
        }
        case('f_purpose1'): {
          $f_purpose=1;
          break;
        }
        case('f_purpose2'): {
          $f_purpose=2;
          break;
        }
        case('f_purpose3'): {
          $f_purpose=3;
          break;
        }
        default: {
          $f_purpose = -1;
        }
      }

      switch ($request->f_rows) {
        case('f_rows0'): {
          $f_rows=1;
          break;
        }
        case('f_rows1'): {
          $f_rows=2;
          break;
        }
        default: {
          $f_rows = -1;
        }
      }


      $f_visible = (strtolower($request->isActive)=='true')?1:0;

      $queue = Queue::where('queue_id', $q)->first();

      $f_title = $request->title;
      $f_opentime = $request->opentime . ':00';
      $f_closetime = $request->closetime . ':00';

      $errorTime = $this->parse_datetime(date('d.m.Y').' '.$f_opentime);
      if ($errorTime<=0) {
        $errorCount++;
        $return['error_fields']['f_opentime'] = "Nepareizs laika formāts ".$date.' '.$f_opentime;
      }
      $errorTime = $this->parse_datetime(date('d.m.Y').' '.$f_closetime);
      if ($errorTime<=0) {
        $errorCount++;
        $return['error_fields']['f_closetime'] = "Nepareizs laika formāts";
      }

      $openInterval = Queue::intervalByTime($f_opentime);
      $closeInterval = Queue::intervalByTime($f_closetime);
      if ($openInterval>=$closeInterval){
        $errorCount++;
        $return['error_fields']['f_closetime'] = "Slēgšanas laiks nedrīkst būt mazāks par atvēršanas laiku";
      }

      if (!$f_title){
        $errorCount++;
        $return['error_fields']['f_title'] = "Rindas nosaukumam ir jābūt aizpildītam";
      }

      if ($errorCount==0){
        switch ($f_purpose) {
          case 0:{
            // neko nedaram, jā.
            break;
          }
          case 1:{
            $queue->loadWorkingDay($date,true);
            $nextWeekDate = date('Y-m-d',strtotime("+1 week",strtotime($date.' 00:00:00')));
            $queue->loadWorkingDay($nextWeekDate,true);
            $workingDay = $queue->_workingDays[$date];
            $workingDay->timestamps = false;

            $workingDay->opentime = $f_opentime;
            $workingDay->closetime = $f_closetime;

            if ($f_rows == 1) {
              if ($workingDay->secondaryAvailable != 0) {
                $workingDay->secondaryAvailable = 0;
                $workingDay->slotSize = 2;
                $queue->moveSlots($date, $f_rows);
              }
            } else {
              if ($workingDay->secondaryAvailable != 1) {
                $workingDay->secondaryAvailable = 1;
                $workingDay->slotSize = 4;
                $queue->moveSlots($date, $f_rows);
              }
            }
//            broadcast(new ChangeQueueChannel($date, $queue->queue_id, $workingDay->is_visible, $f_visible))->toOthers();
            $workingDay->is_visible = ($f_visible)?1:0;
            $workingDay->save();



            break;
          }
          case 2:{
            $queue->loadWorkingDay($date,true);
            $dayOfWeek = date('N', strtotime($date.' 00:00:00'));
            $queue->loadWeekDays($date,array($dayOfWeek));

            foreach ($queue->_workingDays as $workingdayDate=>$workingDay){
              if ($workingDay->weekday == $dayOfWeek){

                $workingDay->timestamps = false;
                $workingDay->opentime = $f_opentime;
                $workingDay->closetime = $f_closetime;

                if ($f_rows == 1) {
                  if ($workingDay->secondaryAvailable != 0) {
                    $workingDay->secondaryAvailable = 0;
                    $workingDay->slotSize = 2;
                    $queue->moveSlots($date, $f_rows);
                  }
                } else {
                  if ($workingDay->secondaryAvailable != 1) {
                    $workingDay->secondaryAvailable = 1;
                    $workingDay->slotSize = 4;
                    $queue->moveSlots($date, $f_rows);
                  }
                }
//                broadcast(new ChangeQueueChannel($date, $queue->queue_id, $workingDay->is_visible, $f_visible))->toOthers();
                $workingDay->is_visible = ($f_visible)?1:0;
                $workingDay->save();

              }
            }
            break;
          }
          case 3:{
            $queue->loadWorkingDay($date,true);
            $queue->loadWeekDays($date,[1,2,3,4,5]);
            foreach ($queue->_workingDays as $workingdayDate=>$workingDay){
              if (($workingDay->weekday >= 1) && ($workingDay->weekday<=5)){
                $workingDay->timestamps = false;
                $workingDay->opentime = $f_opentime;
                $workingDay->closetime = $f_closetime;

                if ($f_rows == 1) {
                  if ($workingDay->secondaryAvailable != 0) {
                    $workingDay->secondaryAvailable = 0;
                    $workingDay->slotSize = 2;
                    $queue->moveSlots($date, $f_rows);
                  }
                } else {
                  if ($workingDay->secondaryAvailable != 1) {
                    $workingDay->secondaryAvailable = 1;
                    $workingDay->slotSize = 4;
                    $queue->moveSlots($date, $f_rows);
                  }
                }
//                broadcast(new ChangeQueueChannel($date, $queue->queue_id, $workingDay->is_visible, $f_visible))->toOthers();
                $workingDay->is_visible = ($f_visible)?1:0;
                $workingDay->save();

              }
            }
            break;
          }
        }

        $queue->title = $f_title;
        $queue->timestamps = false;
        $queue->save();
      }


      //$return['errorCount'] = 1;
      $return['errorCount'] = $errorCount;
      $return['status'] = ($errorCount >= 1) ? 0 : 1;

      $json = json_encode($return);
      echo $json;
      die;
    }

    $date = $request->date;
    $q = $request->queue_id;

    $queue = Queue::where('queue_id', $q)->first();
    $queue->loadWorkingDay($date);

    $return = [];

    $return['q'] = $q;
    $return['d'] = $date;
    $return['f_date'] = date('d.m.Y',strtotime($date));

    $office = Office::where('office_id', $queue->office_id)->first();

    $return['f_office'] = $office->title;

    $_weekDays = array(
      1=>'pirmdienām',
      2=>'otrdienām',
      3=>'trešdienām',
      4=>'ceturtdienām',
      5=>'piektdienām',
      6=>'sestdienām',
      7=>'svētdienām',
    );
    $dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];

    $return['f_title'] = $queue->title;
    $return['f_day'] = $dayOfWeek;
    if ($queue->_workingDays[$date]->secondaryAvailable == 0 && $queue->_workingDays[$date]->slotSize == 2) {
      $return['f_rows'] = 1;
    } else {
      $return['f_rows'] = 2;
    }
    $return['f_opentime'] = Queue::timeByInterval(Queue::intervalByTime($queue->getOpenTime($date)));
    $return['f_closetime'] = Queue::timeByInterval(Queue::intervalByTime($queue->getCloseTime($date)));
    $return['f_visible'] = ($queue->isVisible($date))?1:0;


    $json = json_encode($return);
    echo $json;
  }

  public function slot_ajax(Request $request)
  {

    if ($request->post()) {
      $return=[];
      $errorCount = 0;
      $return['errorCount'] = 1;
      $return['error_fields'] = [
        'f_status'=>'',
        'f_slotcomment'=>''
      ];

      $date = $request->date;
      $q = $request->queue_id;
      $s = $request->slot_id;

      $queue = Queue::where('queue_id', $q)->first();
      $queue->loadWorkingDay($date);
      $queue->loadSlots($date);

      $slot = $queue->_slots[$date][$s];
      $slot->timestamps = false;

      if ($request->f_editTime == 1) {
        $slot->comment = $request->f_slotcomment;
        $slot->save();

        $return['status'] = 1;

        $json = json_encode($return);
        echo $json;
        die;
      }

      $f_status = $request->f_status;
      $f_slotcomment = $request->f_slotcomment;
      $userId = Auth::user()->id;

      if ($errorCount==0){
        // kļūdu nav, saglabājam
        $slot->status = $f_status;
        $slot->comment = $f_slotcomment;

        if ($slot->createtime=='') {
          $slot->createtime = NOW();
          $slot->createuser = $userId;
        }

        $slot->edittime = NOW();
        $slot->edituser = $userId;

        switch ($f_status) {
          case (0): {
            $slot->takenby = null;
            break;
          }
          case (1): {
            $slot->takenby = json_encode(['ownerPhone' => 'xxxxx', 'plate' => null, 'vehicleMake' => null, 'vehicleModel' => null]);
            break;
          }
        }

        $slot->save();
      }


      $return['errorCount'] = $errorCount;

      $json = json_encode($return);
      echo $json;
      die;
    }

    $date = $request->date;
    $q = $request->queue_id;
    $s = $request->slot_id;

    $queue = Queue::where('queue_id', $q)->first();
    $queue->loadWorkingDay($date);
    $queue->loadSlots($date);
    $office = Office::where('office_id', $queue->office_id)->first();

    $slot = $queue->_slots[$date][$s];

    $return = array();

    $return['q'] = $q;
    $return['d'] = $date;
    $return['s'] = $s;

    $return['is_mobile'] = $slot->is_mobile;
    $return['is_mobile2'] = $slot->is_mobile2;
    $return['f_date'] = date('d.m.Y',strtotime($date));
    $return['f_time'] = Queue::timeByInterval($queue->getSlotStartInterval($date, $s));
    $return['f_status'] = $slot->status;

    $return['f_slotcomment'] = $slot->comment;

    $return['f_office'] = $office->title;

    $return['f_statuses'][SLOT_STATUS_FREE] = 'Brīvs';
    $return['f_statuses'][SLOT_STATUS_TAKEN] = 'Aizņemts';
    //$return['f_statuses'][SLOT_STATUS_OFFER] = 'Īpašais piedāvājums';
    $return['f_statuses'][SLOT_STATUS_CLOSED] = 'Slēgts';

    $return['f_rimswith'] = $slot->rimsWith;

    $json = json_encode($return);
    echo $json;
  }

  public function discount(Request $request)
  {
    $slot = Slot::where('slot_id', $request->slot_id)->first();
    $slot->timestamps = false;

    if ($request->checked == 1) {
      if ($slot->status === SLOT_STATUS_FREE) {
        $slot->status = 2;
        $slot->comment = '-20% darbam ! ! !';
      } else {
        $slot->comment = '-20% darbam ! ! !';
      }
    } else {
      if ($slot->takenby === '' || $slot->takenby === NULL) {
        $slot->status = 0;
      } else {
        $slot->status = 1;
      }
      $slot->comment = '';
    }

    $slot->edittime = NOW();
    $slot->edituser = Auth::user()->id;
    $slot->save();

  }

  public function reservations(Request $request)
  {
    $offices = Office::all();

    $date = $request->date;
    if ($date===null) {
      $date = date('Y-m-d');
      $visibleDays = 14;
    } else {
      $visibleDays = 1;
    }
    $currentDate = strtotime($date);

    foreach ($offices as $office) {
      $office->loadQueues();
      foreach ($office->_queues as $queue){
        $queue->loadWorkingDay($date,true);
        $queue->loadSlots($date,true);
        $slotSizes[] = $queue->_workingDays[$date]->slotSize;
        $workingDays[] = $date;
        for ($i=1;$i<$visibleDays;$i++){
          $ndate = date('Y-m-d',strtotime("+{$i} days",$currentDate));
          $queue->loadWorkingDay($ndate,true);
          $queue->loadSlots($ndate,true);
          $workingDays[] = $ndate;
        }
      }
    }

    $workingDays = array_unique($workingDays);

    $_weekDays = array(
      1=>'Pirmdiena',
      2=>'Otrdiena',
      3=>'Trešdiena',
      4=>'Ceturtdiena',
      5=>'Piektdiena',
      6=>'Sestdiena',
      7=>'Svētdiena',
    );

    $tires = new Tires();
    $timeStep = $tires->arrayGCD($slotSizes);

    $services = Service::orderBy('service_id', 'ASC')->get();

    return view('admin.records.reservation', compact('offices', 'timeStep', 'workingDays', 'date', '_weekDays', 'currentDate', 'services'));

  }

  public function reservations_ajax(Request $request)
  {
    $dopParams = $request->input('dopParams');
    $result = array_reduce(explode('&', $request->input('formData')), function ($acc, $data) {
      [$key, $value] = explode('=', $data);
      $acc[$key] = $value;
      return $acc;
    }, []);

    $result = json_decode(json_encode($result), FALSE);
    $f_statuscase = (int) $request->input('f_statuscase');

    $today = date('Y-m-d');

    $slot = Slot::where('date', $dopParams['date'])->where('queue_id', $dopParams['queue_id'])->where('iorder', $dopParams['iorder'])->first();

    if (!is_null($f_statuscase)) {

      $targetDate = $dopParams['new_date'];
      $f_time = $dopParams['new_time'];
      $newOffice = Office::where('office_id', Queue::where('queue_id', $dopParams['new_queue'])->first()->office_id)->first()->office_id;

      if ($today == $dopParams['date'] && $this->now >= $this->startSendWpp && $this->now < $this->endSendWpp) {
        $office = $dopParams['office'];
        $service = Service::where('service_id', $result->service)->first();
        $vehicle = str_replace(' ', '%20', $result->car_brand);
        $model = str_replace(' ', '%20', $result->car_model);
        $userComment = (!empty($result->user_comment)) ? '%20|%20Piezīmes%20-%20' . str_replace(' ', '%20', $result->user_comment) : '';
        if (!is_null($service)) $service = str_replace(' ', '%20', $service->pdf_title);
        $vehiclePlate = str_replace(' ', '%20', $result->lic_plate);

        if (!empty($form->rimsWith)) {
          if ($form->rimsWith == 1) {
            $append = '%20-%20Riepas%20bez%20diskiem';
          } else {
            $append = '%20-%20Riepas%20ar%20diskiem';
          }
        } else {
          $append = '';
        }

        if ($targetDate) {
          if ($today == $targetDate) {
            $dateText = 'šodien';
          } else {
            $dateText = $targetDate;
          }
        } else {
          $dateText = 'šodien';
        }

        if ($f_statuscase) {
          switch ($f_statuscase) {
            case 1:
              {
                $ursUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment;
                $krsUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment;
                break;
              }
            case 2:
              {
                $ursUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Labots%20pieraksts%20-%20' . $f_time . ',%20' . $dateText . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment;
                $krsUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Labots%20pieraksts%20-%20' . $f_time . ',%20' . $dateText . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment;
                break;
              }
            case 3:
              {
                $ursUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate;
                $krsUrl = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate;
                break;
              }
          }

          if ($office == $newOffice) {
            if ($office == 1) {

              $cURLConnection = curl_init();

              curl_setopt($cURLConnection, CURLOPT_URL, $ursUrl);
              curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

              curl_exec($cURLConnection);

              curl_close($cURLConnection);
            } else {
              $cURLConnection = curl_init();

              curl_setopt($cURLConnection, CURLOPT_URL, $krsUrl);
              curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

              curl_exec($cURLConnection);

              curl_close($cURLConnection);
            }
          } else {
            if ($office == 1 && $f_statuscase == 2) {

              //The URLs that we want to send cURL requests to.
              $urls = [
                'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate,
                'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append,
              ];

              //An array that will contain all of the information
              //relating to each request.
              $requests = [];

              //Initiate a multiple cURL handle
              $mh = curl_multi_init();

              //Loop through each URL.
              foreach($urls as $k => $url){
                $requests[$k] = array();
                $requests[$k]['url'] = $url;
                //Create a normal cURL handle for this particular request.
                $requests[$k]['curl_handle'] = curl_init($url);
                //Configure the options for this request.
                curl_setopt($requests[$k]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
                //Add our normal / single cURL handle to the cURL multi handle.
                curl_multi_add_handle($mh, $requests[$k]['curl_handle']);
              }

              //Execute our requests using curl_multi_exec.
              $stillRunning = false;
              do {
                curl_multi_exec($mh, $stillRunning);
              } while ($stillRunning);

              //Loop through the requests that we executed.
              foreach($requests as $k => $reqs){
                //Remove the handle from the multi handle.
                curl_multi_remove_handle($mh, $reqs['curl_handle']);
                //Close the handle.
                curl_close($requests[$k]['curl_handle']);
              }
              //Close the multi handle.
              curl_multi_close($mh);
            } else if ($office == 2 && $f_statuscase == 2) {


              //The URLs that we want to send cURL requests to.
              $urls = [
                'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append,
                'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $f_time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate,
              ];

              //An array that will contain all of the information
              //relating to each request.
              $requests = [];

              //Initiate a multiple cURL handle
              $mh = curl_multi_init();

              //Loop through each URL.
              foreach($urls as $k => $url){
                $requests[$k] = array();
                $requests[$k]['url'] = $url;
                //Create a normal cURL handle for this particular request.
                $requests[$k]['curl_handle'] = curl_init($url);
                //Configure the options for this request.
                curl_setopt($requests[$k]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
                //Add our normal / single cURL handle to the cURL multi handle.
                curl_multi_add_handle($mh, $requests[$k]['curl_handle']);
              }

              //Execute our requests using curl_multi_exec.
              $stillRunning = false;
              do {
                curl_multi_exec($mh, $stillRunning);
              } while ($stillRunning);

              //Loop through the requests that we executed.
              foreach($requests as $k => $reqs){
                //Remove the handle from the multi handle.
                curl_multi_remove_handle($mh, $reqs['curl_handle']);
                //Close the handle.
                curl_close($requests[$k]['curl_handle']);
              }
              //Close the multi handle.
              curl_multi_close($mh);
            }
          }
        }
      }
    }

    if ($result->status == 0) {
      if (!is_null($slot)) {
        if ($result->slotcomment === 'null') {
          $slot->delete();
          return json_encode(['status' => $result->status, 'deleted_slot_admin' => true, 'comment' => $result->slotcomment ?? '']);
        } else {
          $slot->delete();
          $slot = new Slot;
          $slot->queue_id = $dopParams['queue_id'];
          $slot->date = $dopParams['date'];
          $slot->iorder = $dopParams['iorder'];
          $slot->status = 0;
          $slot->takenby = null;
          $slot->comment = $result->slotcomment;
          $slot->createtime = now();
          $slot->createuser = Auth::user() ? Auth::user()->id : 0;
          $slot->edittime = now();
          $slot->edituser = Auth::user() ? Auth::user()->id : 0;
          $slot->save();
          return json_encode(['status' => $result->status, 'edited_slot_admin' => true, 'comment' => $result->slotcomment ?? '']);
        }
      } else {
        if ($result->slotcomment) {
          $slot = new Slot;
          $slot->queue_id = $dopParams['queue_id'];
          $slot->date = $dopParams['date'];
          $slot->iorder = $dopParams['iorder'];
          $slot->status = 0;
          $slot->takenby = null;
          $slot->comment = $result->slotcomment;
          $slot->createtime = now();
          $slot->createuser = Auth::user() ? Auth::user()->id : 0;
          $slot->edittime = now();
          $slot->edituser = Auth::user() ? Auth::user()->id : 0;
          $slot->save();
          return json_encode(['status' => $result->status, 'edited_slot_admin' => true, 'comment' => $result->slotcomment ?? '']);
        }
      }
    } else if ($result->status == 1) {

      $dopParams = $request->dopParams;
      $workingDay = Workingday::where('date', $dopParams['date'])->where('queue_id', $dopParams['queue_id'])->first();

      $opentime = Carbon::parse($workingDay->timeopen);
      $closetime = Carbon::parse($workingDay->timeclose)->subMinutes($this->timeStep);

      $numberOfSteps = ceil($opentime->diffInMinutes($closetime) / $this->timeStep);

      for ($i = 0; $i <= $numberOfSteps; $i++) {
        $currentTime = $opentime->copy()->addMinutes($this->timeStep * $i)->format('H:i');

        if ($currentTime == $dopParams['new_time']) {
          $iorder = $i;
        }
      }

      $emptyData = true; // All time data will be empty

      $formDataArray = array_reduce(explode('&', $request->input('formData')), function ($acc, $data) {
        [$key, $value] = explode('=', $data);
        $acc[$key] = $value;
        return $acc;
      }, []);


      $move_slot = false;

      if ($dopParams['date'] !== $dopParams['new_date'] || $dopParams['time'] !== $dopParams['new_time'] || $dopParams['queue_id'] !== $dopParams['new_queue']) {
        $move_slot = true;
      }


      $formData = json_decode(json_encode($formDataArray), FALSE);
      if (isset($slot->takenby)) {
        $takenBy = (array) json_decode($slot->takenby);
        if (!isset($takenBy['cancelId'])) $takenBy['cancelId'] = $this->getRandomHash() . str_replace(':', '', $dopParams['new_time']);
        $formData->cancelId = $takenBy['cancelId'];
      }
      $newFormData = json_encode($formData);
      $discount = ($formData->slotcomment === 'null') ? null : $formData->slotcomment;
      unset($formData->service, $formData->status, $formData->slotcomment, $formDataArray['status'], $formDataArray['slotcomment']);
      foreach ($formData as $data) {
        if (!empty($data)) {
          // Check if form contains user data
          $emptyData = false;
          break;
        }
      }

      if (!is_null($slot)) { // Ja slots eksistē
        if ($emptyData) {
          $slot_id = $slot->slot_id;
          $time_created = $slot->createtime;
          $user_created = $slot->createuser;
          $slot->delete();
          $slot = new Slot();
          $slot->slot_id = $slot_id;
          $slot->timestamps = false;
          $slot->queue_id = $dopParams['queue_id'];
          $slot->date = $dopParams['date'];
          $slot->iorder = $dopParams['iorder'];
          $slot->status = 1;
          $slot->takenby = null;
          $slot->comment = $discount;
          $slot->createtime = $time_created;
          $slot->createuser = $user_created;
          $slot->edittime = now();
          $slot->edituser = Auth::user() ? Auth::user()->id : 0;
          $slot->save();

          return json_encode(['status' => 1, 'edited_slot_admin' => true, 'new_iorder' => (string) $iorder]);

        } else {
          if ($move_slot) {

            $newIorder = Slot::getSlotNumber($dopParams['new_time'], $dopParams['new_date'], $dopParams['new_queue']);
            if (!is_numeric($newIorder)) return json_encode(['failed' => true, 'failed_msg' => $newIorder]);

            $is_slot_taken = Slot::where('date', $dopParams['new_date'])
              ->where('queue_id', $dopParams['new_queue'])
              ->where('iorder', $newIorder)
              ->where('takenby', '!=', NULL)
              ->where('status', 1)
              ->count();

            if ($is_slot_taken) return json_encode(['failed' => true, 'failed_msg' => 'Laiks ' . $dopParams['new_time'] . ' ir aizņemts!']);


            // Ja slots ir jāpārvieto
            $slot_id = $slot->slot_id;
            $time_created = $slot->createtime;
            $user_created = $slot->createuser;
            $slot->delete();
            $slot = new Slot();
            $slot->slot_id = $slot_id;
            $slot->timestamps = false;
            $slot->queue_id = $dopParams['new_queue'];
            $slot->date = $dopParams['new_date'];
            $slot->iorder = $newIorder;
            $slot->status = 1;
            $slot->takenby = $newFormData;
            $slot->comment = $discount;
            $slot->createtime = $time_created;
            $slot->createuser = $user_created;
            $slot->edittime = now();
            $slot->edituser = Auth::user() ? Auth::user()->id : 0;
            $slot->save();

            return json_encode(['status' => 1, 'moved_slot_admin' => true, 'new_iorder' => (string) $newIorder]);
          } else {
            // Ja slots nav jāpārvieto
            $slot_id = $slot->slot_id;
            $time_created = $slot->createtime;
            $user_created = $slot->createuser;
            $slot->delete();
            $slot = new Slot();
            $slot->slot_id = $slot_id;
            $slot->timestamps = false;
            $slot->queue_id = $dopParams['queue_id'];
            $slot->date = $dopParams['date'];
            $slot->iorder = $dopParams['iorder'];
            $slot->status = 1;
            $slot->takenby = $newFormData;
            $slot->comment = $discount;
            $slot->createtime = $time_created;
            $slot->createuser = $user_created;
            $slot->edittime = now();
            $slot->edituser = Auth::user() ? Auth::user()->id : 0;
            $slot->save();

            return json_encode(['status' => 1, 'edited_slot_admin' => true, 'new_iorder' => (string) $iorder]);
          }
        }
      } else { // Ja slots neeksistē
        $slot = new Slot();
        $slot->timestamps = false;
        $slot->queue_id = $dopParams['queue_id'];
        $slot->date = $dopParams['date'];
        $slot->iorder = $dopParams['iorder'];
        $slot->status = 1;
        $slot->takenby = ($emptyData) ? null : $newFormData;
        $slot->comment = $discount;
        $slot->createtime = now();
        $slot->createuser = Auth::user() ? Auth::user()->id : 0;
        $slot->edittime = now();
        $slot->edituser = Auth::user() ? Auth::user()->id : 0;
        $slot->save();
        return json_encode(['status' => 1, 'edited_slot_admin' => true, 'new_iorder' => (string) $iorder]);

        // Vajag uztaisīt IF'u ar pārbaudi uz to vai ir emptyData, lai returnā padotu dažādus datus
      }
    } else {
      if (!$slot) $slot = new Slot;
      $slot->timestamps = false;
      $slot->queue_id = $dopParams['queue_id'];
      $slot->date = $dopParams['date'];
      $slot->iorder = $dopParams['iorder'];
      $slot->status = 3;
      $slot->takenby = null;
      $slot->createtime = now();
      $slot->createuser = Auth::user() ? Auth::user()->id : 0;
      $slot->edittime = now();
      $slot->edituser = Auth::user() ? Auth::user()->id : 0;
      $slot->save();
      return json_encode(['status' => $result->status, 'edited_slot_admin' => true, 'new_iorder' => $dopParams['iorder']]);
    }
  }

  public function getRandomHash(): string
  {
    $value = Str::random(32);
    $hash = hash('sha256', $value);

    // check if hash is already taken
    $isTaken = $this->isHashTaken($hash);
    if ($isTaken) {
      // if hash is taken, hash the value again
      $hash = hash('sha256', $hash . $value);

      // keep hashing until a unique hash is found
      while ($this->isHashTaken($hash)) {
        $hash = hash('sha256', $hash . $value);
      }
    }

    return substr($hash, 0, 20);
  }

  public function isHashTaken($value): bool
  {
    static $takenHashes = []; // static variable to store taken numbers

    $slots = Slot::select('takenby')->where('takenby', 'like', '%"cancelId":%')->get();
    foreach ($slots as $slot) {
      $takenBy = json_decode($slot->takenby);
      if (!empty($takenBy)) {
        if (property_exists($takenBy, 'cancelId')) {
          $takenHashes[] = $takenBy->cancelId;
        }
      }
    }

    if (in_array($value, $takenHashes)) {
      return true;
    }

    return false;
  }

}


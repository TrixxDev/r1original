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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\Utility;

class RecordController extends Controller
{

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

  public function reservations_ajax(Request $request, $q, $date, $s, $p)
  {

    if ($request->post()) {
      $return = [];
      $errorCount = 0;
      $return['errorCount'] = 1;
      $return['error_fields'] = [
        'f_date'=>'',
        'f_time'=>'',
        'f_office'=>'',
        'f_status'=>'',
        'f_car'=>'',
        'f_model'=>'',
        'f_plate'=>'',
        'f_purpose'=>'',
        'f_storagebin'=>'',
        'f_comment'=>'',
        'f_name'=>'',
        'f_phone'=>'',
        'f_email'=>'',
        'f_slotcomment'=>'',
        'f_rimswith'=>''
      ];
      /*$return['error_fields']['f_car'] = "";
      $return['error_fields']['f_car'] = "";
      $return['error_fields']['f_model'] = "";
      $return['error_fields']['f_purpose'] = "";*/

      $date = $request->f_currDate;

      $queue = Queue::where('queue_id', $q)->first();
      $queue->loadWorkingDay($date);
      $queue->loadSlots($date);

      $slot = $queue->_slots[$date][$s];

      $f_currDate = $request->f_currDate;

      //// filiāles/rindas maiņas
      $f_office = $request->f_office;
      $f_part = preg_replace("/[^A-Za-z ]/", '', $f_office);
      $f_office = preg_replace("/[^0-9 ]/", '', $f_office);

      //// datuma / laika maiņas
      $f_date = trim(date('d.m.Y', strtotime($request->f_date)));
      $f_time = trim($request->f_time);
      $targetDateTimestamp = $this->parse_datetime($f_date.' '.$f_time);

      $move = false;

      if ($targetDateTimestamp>0){
        $targetDate = date('Y-m-d', $targetDateTimestamp);
        $targetTime = date('H:i', $targetDateTimestamp);

        if ($queue->_workingDays[$date]->secondaryAvailable && ($p!='a')){
          $targetSlotStart = $queue->getSlotStartTime2($date, $s);
        } else {
          $targetSlotStart = $queue->getSlotStartTime($date, $s);
        }

        if (($targetDate!=$date)||($targetTime!=$targetSlotStart)||($q!=$f_office)||($p!=$f_part)){
          // pārvietošana laikā un vietā.
          $targetQueue = Queue::where('queue_id', $f_office)->first();
          $targetQueue->loadWorkingDay($targetDate);
          $targetQueue->loadSlots($targetDate);

          $targetSlotNum = $targetQueue->getSlotNumberByInterval($targetDate, Queue::intervalByTime($targetTime));
          if (($targetSlotNum===false) || (!isset($targetQueue->_slots[$targetDate][$targetSlotNum]))){
	    $errorCount++;
            $return['error_fields']['f_time'] = "Norādītais laiks ir ārpus darba laika";
          } else {
            $targetSlot = $targetQueue->_slots[$targetDate][$targetSlotNum];
            if ($f_part=='a'){
              $targetStatus = $targetSlot->status;
            } else {
              $targetStatus = $targetSlot->status2;
            }
            if (($targetStatus!=SLOT_STATUS_FREE)&&($targetStatus!=SLOT_STATUS_OFFER)){
              $errorCount++;
              $return['error_fields']['f_office'] = "Norādītajā rindā pieprasītais datums/laiks jau ir aizņemts";
            } else {
              $move = true;
            }
          }
        }

      } else {
        if (!$request->f_slotcomment) {
          $errorCount++;
          $errorDate = $this->parse_datetime($f_date.' 00:00');
          if ($errorDate<=0) $return['error_fields']['f_date'] = "Nepareizs datuma formāts";
          $errorTime = $this->parse_datetime(date('d.m.Y').' '.$f_time);
          if ($errorTime<=0) $return['error_fields']['f_time'] = "Nepareizs laika formāts";
        }
      }

      //dd($f_currDate, $targetDate, $move);

      $f_status = $request->f_status;
      $form = new \stdClass();

      $form->vehicleMake = $request->f_car;
      $form->vehicleModel = $request->f_model;
      $form->vehiclePlate = $request->f_plate;

      $form->purpose = $request->f_purpose;
      $form->storagebin = $request->f_storagebin;

      $form->storageBin = $request->f_storagebin;
      $form->comment = $request->f_comment;
      $form->ownerName = $request->f_name;
      $form->ownerPhone = $request->f_phone;
      $form->ownerEmail = $request->f_email;
      $form->rimsWith = $request->f_rimswith;

//      dd(array_map('intval', str_split($form->ownerPhone)));

      /**
       * Ja ir aizpildīts kāds no rezervācijas laukiem un attiecīgais lauks ir brīvs, statuss automātiski nomainās uz "Aizņemts"
       */
      if (($form->vehicleMake!='')||($form->vehicleModel!='')||($form->vehiclePlate!='')/*||($form->purpose>0)*/||($form->comment!='')||($form->ownerName!='')||($form->ownerPhone!='')||($form->ownerEmail!='')){
        if (($f_status == SLOT_STATUS_FREE)||($f_status == SLOT_STATUS_OFFER)) $f_status = SLOT_STATUS_TAKEN;
      }

      if ($request->f_status == SLOT_STATUS_TAKEN && array_filter((array) $form)){
        if (intval($form->ownerPhone) > 0) {
          if ($form->vehicleMake=='') {
            $errorCount++;
            $return['error_fields']['f_car'] = "Laukam \"Auto marka\" jābūt aizpildītam";
          }
          if ($form->vehicleModel=='') {
            $errorCount++;
            $return['error_fields']['f_model'] = "Laukam \"Auto modelis\" jābūt aizpildītam";
          }
          if ($form->purpose=='') {
            $errorCount++;
            $return['error_fields']['f_purpose'] = "Laukā \"Es vēlos\" jābūt norādītai vienai vērtībai";
          }
          if ($form->purpose==2 && (((!$form->storageBin) && (!$form->vehiclePlate)))) {
            $errorCount++;
            $return['error_fields']['f_storagebin'] = "Vismaz vienam no laukiem \"Glabāšanas talona numurs\" vai \"Reģistrācijas numurs\" jābūt aizpildītam!\n";
          }
          if ($form->ownerPhone=='') {
            $errorCount++;
            $return['error_fields']['f_phone'] = "Laukam \"Tālruņa numurs\" jābūt aizpildītam";
          }
          /*if ($form->ownerEmail=='') {
            $errorCount++;
            $return['error_fields']['f_email'] = "Laukam \"E-pasts\" jābūt aizpildītam";
          }*/
          if (!Auth::check()){
            if (($form->ownerEmail=='')&&(!$form->ownerEmail)) {
              $errorCount++;
              $return['error_fields']['f_email'] = "Lauks \"E-pasts\" aizpildīts nekorekti";
            }
          }
        }

        $formData = json_encode($form);
//        broadcast(new AdminNewSlotChannel($request->f_status, $formData, $slot->queue_id, $slot->iorder, $slot->date));
      } else {
        if ($request->f_status == SLOT_STATUS_FREE) {
          foreach ($form as $index => $value) {
            $value = '';
            $form->$index = $value;
          }
          $text = 'Izdzēsts pieraksts';
          $f_status = $request->f_status;
          $formData = json_encode($form);
        } else {
          if ($p=='a'){
	          $canDiscount = 1;
            $formData = $slot->takenby = json_encode(['ownerPhone' => 'xxxxx', 'plate' => null, 'vehicleMake' => null, 'vehicleModel' => null]);
          } else {
            $canDiscount = 1;
            $formData = $slot->takenby2 = json_encode(['ownerPhone' => 'xxxxx', 'plate' => null, 'vehicleMake' => null, 'vehicleModel' => null]);
          }
          $text = 'Jauns pieraksts';
        }
      }

      $f_slotcomment = $request->f_slotcomment;
      $userId = Auth::user()->id;

      $prevStatus = 0;
      $mailSlot = false;
      $mailQueue = false;
      $bQueue = false;

      if ($errorCount==0){
        // kļūdu nav, saglabājam
        if ($move){
          if ($f_part=='a'){
            $targetSlot->status = $f_status;
            $targetSlot->takenby = $formData;
	          $targetSlot->createtime = $slot->createtime;
            $targetSlot->createuser = $slot->createuser;
            $targetSlot->edittime = NOW();
            $targetSlot->edituser = $userId;
            $targetSlot->is_mobile = $slot->is_mobile;

            $slot->status = 0;
            $slot->takenby = '';
            $slot->createtime = null;
            $slot->createuser = -1;
            $slot->edittime = null;
            $slot->edituser = -1;
            $slot->is_mobile = null;
          } else {
            $bQueue = true;
            $targetSlot->status2 = $f_status;
            $targetSlot->takenby2 = $formData;
            $targetSlot->createtime2 = $slot->createtime2;
            $targetSlot->createuser2 = $slot->createuser2;
            $targetSlot->edittime2 = NOW();
            $targetSlot->edituser2 = $userId;
            $targetSlot->is_mobile2 = $slot->is_mobile2;

            $slot->status2 = 0;
            $slot->takenby2 = '';
            $slot->createtime2 = null;
            $slot->createuser2 = -1;
            $slot->edittime2 = null;
            $slot->edituser2 = -1;
            $slot->is_mobile2 = null;
          }
          $targetSlot->comment = $f_slotcomment;

          $targetSlot->timestamps = false;

          $targetSlot->save();
          $slot->save();

          // Pārlasam slotu, gadījumiem ja izmaiņa ir tā paša slota sekundārajā rindā
          if ($slot->slot_id>0){
            $slot = Slot::where('slot_id', $slot->slot_id)->first();
          }

          $mailSlot = $targetSlot;
          $mailQueue = $targetQueue;

          if ($p=='a'){
            $prevStatus = $slot->status;
            $slot->status = SLOT_STATUS_FREE;
            $slot->takenby = '';
          } else {
            $prevStatus = $slot->status2;
            $slot->status2 = SLOT_STATUS_FREE;
            $slot->takenby2 = '';
          }

//          if ($slot->takenby==SLOT_STATUS_FREE && $slot->takenby2==SLOT_STATUS_FREE) {
//            $slot->comment = '';
//            $slot->takenby = '';
//            $slot->createtime = '';
//            $slot->edittime = '';
//          }
          $slot->timestamps = false;

          //PRE($slot);die;
//          broadcast(new MoveSlotChannel($f_status, $targetSlot, $slot, $targetSlot->queue_id, $targetSlot->iorder, $targetSlot->date));
        } else {
          if ($p=='a'){
            $prevStatus = $slot->status;
          } else {
            $prevStatus = $slot->status2;
          }
          $mailSlot = $slot;
          $mailQueue = $queue;

          if ($f_part=='a'){
            //dd($f_status);
            $slot->status = $f_status;
            $slot->takenby = $formData;
	          if ($slot->createtime=='') {
              $slot->createtime = NOW();
              $slot->createuser = $userId;
            }
            $slot->edittime = NOW();
            $slot->edituser = $userId;
          } else {
            $bQueue = true;
            $slot->status2 = $f_status;
            $slot->takenby2 = $formData;
            if ($slot->createtime2=='') {
              $slot->createtime2 = NOW();
              $slot->createuser2 = $userId;
            }
            $slot->edittime2 = NOW();
            $slot->edituser2 = $userId;
          }
          if ($f_slotcomment) {
            $slot->comment = $f_slotcomment;
            if ($p=='a'){
	          //dd((array) $formData);
	            $slot->status = $f_status;
              $slot->takenby = $formData;
	            if ($slot->createtime=='') {
                $slot->createtime = NOW();
                $slot->createuser = $userId;
              }
              $slot->edittime = NOW();
              $slot->edituser = $userId;
            } else {
	          $slot->status = $f_status;
              $slot->takenby2 = $formData;
              if ($slot->createtime2=='') {
                $slot->createtime2 = NOW();
                $slot->createuser2 = $userId;
              }
              $slot->edittime2 = NOW();
              $slot->edituser2 = $userId;
	          }
          }

          $slot->timestamps = false;

          if (empty($text)) $text = 'Labots pieraksts';

          if ($slot->save()) {
            Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $slot->slot_id,0, $text, $slot);
          }
//          dd($slot);
//          broadcast(new EditSlotChannel($f_status, json_decode($slot->takenby), $slot->queue_id, $slot->iorder, $slot->date))->toOthers();
        }

//        if (($prevStatus!=SLOT_STATUS_TAKEN) && ($f_status==SLOT_STATUS_TAKEN)){
//          $sendEmail = 0;
//          if ($sendEmail && $form->ownerEmail){
//            $form = json_decode($formData);
//            $mailText = $mailQueue->parseNotification($mailQueue->notificationEmail, $mailSlot->date, $mailSlot->iorder, $form, $bQueue);
//            $mailer = new CMailer();
//            $mailer->addRecipient($form->ownerEmail);
//            $bcc = 'r1-dev@stormlv.eu';
//            if ($bcc) $mailer->addBCC($bcc);
//            $mailer->subject = $mailQueue->parseNotification($queue->notificationSubject, $mailSlot->date, $mailSlot->iorder, $form, $bQueue);
//            $mailer->message = $mailText;
//            $mailer->send();
//          } else {
//            $form = json_decode($formData);
//            $mailText = $mailQueue->parseNotification($mailQueue->notificationEmail, $mailSlot->date, $mailSlot->iorder, $form, $bQueue);
//          }
//        }
      }

      //$return['error_fields']['f_purpose'] = $targetDateTimestamp;


      $return['errorCount'] = $errorCount;
      $return['status'] = ($errorCount >= 1) ? 0 : 1;

      $json = json_encode($return);

      $returnSlot = json_decode($slot->takenby);
      $slotDate = date('Y-m-d',strtotime($date));

      echo $json;
      die;
    }


    $officeList = Office::all();

    $options = '';
    $return = [];

    foreach ($officeList as $office) {
      $office->loadQueues();
      foreach ($office->_queues as $queue) {
        $queue->loadWorkingDay($date);
        if ($queue->_workingDays[$date]->secondaryAvailable) {
          $options .= '<option value="' . $queue->queue_id . 'a">' . $office->title . ' | ' . $queue->title . ' | A</option>';
          $options .= '<option value="' . $queue->queue_id . 'b">' . $office->title . ' | ' . $queue->title . ' | B</option>';
        } else {
          $options .= '<option value="' . $queue->queue_id . 'a">' . $office->title . ' | ' . $queue->title . '</option>';
        }
      }
    }

    $queue = Queue::where('queue_id', $q)->first();
    $queue->loadWorkingDay($date);
    $queue->loadSlots($date);



    if (!$queue->_workingDays[$date]->secondaryAvailable) $p='a';

    $slot = $queue->_slots[$date][$s];

    $return['is_mobile'] = $slot->is_mobile;
    $return['is_mobile2'] = $slot->is_mobile2;
    $return['options'] = $options;
    $return['q'] = $q;
    $return['p'] = strtolower($p);
    $return['d'] = $date;
    $return['s'] = $s;

    $return['f_date'] = date('d.m.Y',strtotime($date));
    //$return['f_time'] = CQueue::timeByInterval($queue->getSlotStartInterval($date, $s));

    if ($queue->_workingDays[$date]->secondaryAvailable && $p!='a'){
      $return['f_time'] = $queue->getSlotStartTime2($date, $s);
    } else {
      $return['f_time'] = $queue->getSlotStartTime($date, $s);
    }

    if ($p=='a'){
      $return['f_status'] = $slot->status;
    } else {
      $return['f_status'] = $slot->status2;
    }

    $return['f_slotcomment'] = $slot->comment;

    $return['f_office'] = $queue->id.''.$p;

    $return['f_statuses'][SLOT_STATUS_FREE] = 'Brīvs';
    $return['f_statuses'][SLOT_STATUS_TAKEN] = 'Aizņemts';
    //$return['f_statuses'][SLOT_STATUS_OFFER] = 'Īpašais piedāvājums';
    $return['f_statuses'][SLOT_STATUS_CLOSED] = 'Slēgts';

    $status = ($p=='a')?$slot->status:$slot->status2;

    switch ($status){
      case SLOT_STATUS_OFFER:
      case SLOT_STATUS_CLOSED:
      case SLOT_STATUS_FREE:{
        break;
      }
      case SLOT_STATUS_TAKEN:	{
        if ($p=='a'){
          $form = json_decode($slot->takenby);
        } else {
          $form = json_decode($slot->takenby2);
        }

        $return['f_car'] = (isset($form->vehicleMake)) ? $form->vehicleMake : '';
        $return['f_model'] = (isset($form->vehicleModel)) ? $form->vehicleModel : '';
        $return['f_plate'] = (isset($form->vehiclePlate)) ? $form->vehiclePlate : '';
        $return['f_purpose'] = (isset($form->purpose)) ? $form->purpose : '';
        $return['f_storagebin'] = (isset($form->storageBin)) ? $form->storageBin : '';
        $return['f_comment'] = (isset($form->comment)) ? $form->comment : '';
        $return['f_name'] = (isset($form->ownerName)) ? $form->ownerName : '';
        $return['f_phone'] = (isset($form->ownerPhone)) ? $form->ownerPhone : '';
        $return['f_email'] = (isset($form->ownerEmail)) ? $form->ownerEmail : '';
        $return['f_rimswith'] = (isset($form->rimsWith)) ? $form->rimsWith : '';
        break;
      }
    }

    if ($slot->createtime!=''){
      $return['f_createtime'] = date('d.m.Y H:i:s',strtotime($slot->createtime));
    } else {
      $return['f_createtime'] = '';
    }

    if (($slot->edittime!='')&&($slot->edittime!=$slot->createtime)){
      $return['f_edittime'] = date('d.m.Y H:i:s',strtotime($slot->edittime));
    } else {
      $return['f_edittime']='';
    }

    if ($slot->edituser>0){
      $user = User::findOrFail($slot->edituser);
      $return['f_edituser'] = $user->name.' '.$user->surname;
    }

    if ($slot->createuser>0){
      $user = User::findOrFail($slot->createuser);
      $return['f_createuser'] = $user->name.' '.$user->surname;
    } else {
      $return['f_createuser'] = 'apmeklētājs';
    }

    if ($slot->createtime2!=''){
      $return['f_createtime2'] = date('d.m.Y H:i:s',strtotime($slot->createtime2));
    } else {
      $return['f_createtime2'] = '';
    }

    if (($slot->edittime2!='')&&($slot->edittime2!=$slot->createtime2)){
      $return['f_edittime2'] = date('d.m.Y H:i:s',strtotime($slot->edittime2));
    } else {
      $return['f_edittime2']='';
    }

    if ($slot->edituser2>0){
      $user = User::findOrFail($slot->edituser2);
      $return['f_edituser2'] = $user->name.' '.$user->surname;
    }

    if ($slot->createuser2>0){
      $user = User::findOrFail($slot->createuser2);
      $return['f_createuser2'] = $user->name.' '.$user->surname;
    } else {
      $return['f_createuser2'] = 'apmeklētājs';
    }


    $json = json_encode($return);
    echo $json;
  }

}


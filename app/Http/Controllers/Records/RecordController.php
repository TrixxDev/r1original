<?php

namespace App\Http\Controllers\Records;

use App\Helper\CMailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Workingday;
use App\Models\Service;
use App\Models\Office;
use App\Helper\Tires;
use App\Models\Queue;
use App\Models\Slot;
use App\Models\Pdf;
use App\Rules\ReCaptcha;
use Auth;

class RecordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

      $offices = Office::all();

      $date = date('Y-m-d');
      $visibleDays = 8;
      $todayDate = strtotime($date);

      foreach ($offices as $office) {
        $office->loadQueues();
        foreach ($office->_queues as $queue){
          $queue->loadWorkingDay($date,true);
          $queue->loadSlots($date,true);
          $slotSizes[] = $queue->_workingDays[$date]->slotSize;
          $workingDays[] = $date;
          for ($i=1;$i<$visibleDays;$i++){
            $ndate = date('Y-m-d',strtotime("+{$i} days",$todayDate));
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

      return view('records.index', compact('date', '_weekDays', 'workingDays', 'timeStep', 'visibleDays', 'offices', 'services'));
    }

    public function fillFiliale()
    {
        return Office::orderBy('office_id', 'DESC')->get();
    }

    public function fillDates()
    {
        $date = date('Y-m-d');
        $visibleDays = 7;
        $currentDate = strtotime($date);

        $_weekDays = [
            1=>'Pirmdiena',
            2=>'Otrdiena',
            3=>'Trešdiena',
            4=>'Ceturtdiena',
            5=>'Piektdiena',
            6=>'Sestdiena',
            7=>'Svētdiena',
        ];

        for ($i=1;$i<$visibleDays;$i++){
            $ndate = date('Y-m-d',strtotime("+{$i} days",$currentDate));
            $day = Workingday::where('date', $ndate)->first();
            $day = $_weekDays[$day->weekday];
            $workingDays[] = ['date_id' => $i, 'date' => $ndate, 'day' => $day];
        }

        return json_encode(array_reverse($workingDays));

    }

    public function fillSlots(Request $request)
    {

        $date = date('Y-m-d');
        $visibleDays = 8;
        $currentDate = strtotime($date);

        $offices = Office::where('office_id', $request->filiale)->get();

        $todayDate = time();
        $today = date('Y-m-d',$todayDate);

        $workingDays = [];
        $slotSizes = [];
        foreach ($offices as $office) {
          $office->loadQueues();
          foreach ($office->_queues as $queue){
            $queue->loadWorkingDay($date,false);
            $queue->loadSlots($date,false);
            $slotSizes[] = $queue->_workingDays[$date]->slotSize;
            $workingDays[] = $date;
            for ($i=1;$i<$visibleDays;$i++){
              $ndate = date('Y-m-d',strtotime("+{$i} days",$todayDate));
              $queue->loadWorkingDay($ndate,false);
              $queue->loadSlots($ndate,false);
              $workingDays[] = $ndate;
            }
          }
        }

        $tires = new Tires();
        $timeStep = (int) $tires->arrayGCD($slotSizes);

        $openTime = 0;
        $closeTime = -1;

        foreach ($offices as $office) {
            if ($openTime==0) {
                $openTime = $office->getOpenTime($request->date);
            } else {
                $t = $office->getOpenTime($request->date);
                if ($t > 0) {
                    $openTime = min($openTime, $t);
                }
            }
            $closeTime = max($closeTime, $office->getCloseTime($request->date));
        }

        $times = [];

        for ($i = $openTime; $i < $closeTime; $i += $timeStep) {
            foreach ($offices as $office) {
                foreach ($office->_queues as $queue) {
                    $queue->getSlots($request->date); // Šī funkcija neeksistē lol
                    $slotNumber = $queue->getSlotNumberByInterval($request->date,$i);
                    if (($slotNumber!==false)&&($queue->isVisible($request->date))) {
                        if ($queue->isIntervalBeginning($request->date,$i)) {
                            $slot = $queue->_slots[$request->date][$slotNumber];
                            //$times[Queue::timeByInterval($i)] = ['time' => Queue::timeByInterval($i), 'slot_id' => $slot->slot_id, 'taken' => true];
                            if ($slot->status == 0) {
                              $times[Queue::timeByInterval($i)] = ['time' => Queue::timeByInterval($i), 'slot_id' => $slot->slot_id];
                            }
                        }
                    }
                }
            }
        }

        return array_reverse($times);

    }

    public function getSlotInfo(Request $request)
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
        $date = $request->date;
        $queue_id = $request->queue_id;
        $slotNumber = $request->slotNumber;

        $queue = Queue::where('queue_id', $queue_id)->first();
        $office = Office::where('office_id', $queue->office_id)->first();

        $queue->loadWorkingDay($date);
        $queue->loadSlots($date, true);

        $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slotNumber),true);
        $fmtDate = date('d.m.Y',strtotime($date));
        $dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];

        return json_encode(['dayOfWeek' => $dayOfWeek, 'date' => $fmtDate, 'time' => $time, 'office_title' => $office->title]);
    }

    public function fillSlot(Request $request)
    {

      $action = $request->action;

      $curlData = array(
        'secret' => env('RECAPTCHAV3_SECRET'),
        'response' => $request->token,
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curlData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $curlResponse = curl_exec($ch);

      $captchaResponse = json_decode($curlResponse, true);

      if ($captchaResponse['success'] == true && $captchaResponse['action'] == $action && $captchaResponse['score'] >= 0.5 && $captchaResponse['hostname'] == $_SERVER['SERVER_NAME']) {
      } else {
      }
//
//      dd($resultJson);
//
//      if ($resultJson->success != true) {
//        return back()->withErrors(['captcha' => 'ReCaptcha Error']);
//      }
//
//      if ($resultJson->score >= 0.3) {
        $userID = -1;
        if (Auth::check()) {
          $userID = Auth::user()->id;
        }

        $date = $request->date;
        $queue_id = $request->queue_id;
        $slotNumber = $request->slotNumber;

        $_weekDays2 = array(
            1=>'pirmdien',
            2=>'otrdien',
            3=>'trešdien',
            4=>'ceturtdien',
            5=>'piektdien',
            6=>'sestdien',
            7=>'svētdien',
        );

        $car = $request->car;
        $carModel = $request->carModel;
        $licPlate = $request->licPlate;
        $purpose = $request->purpose;
        $storageBin = $request->storageBin;
        $comment = $request->comment;
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;

        $errorText = [];
        if (!$car) $errorText['brand'] = "Jābūt aizpildītam!\n";
        if (!$carModel) $errorText['model'] = "Jābūt aizpildītam!\n";

        if (!$purpose) $errorText['purpose'] = "Laukam \"Es vēlos\" jābūt aizpildītam!\n";
        if (!Auth::check()) {
            if (!$email) $errorText['email'] = "Jābūt aizpildītam!\n";
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errorText['emptyEmail'] = "Lauks \"Mans e-pasts\" aizpildīts nekorekti!\n";
            if (!$phone) $errorText['phone'] = "Jābūt aizpildītam!\n";
            if ($phone && !is_numeric($phone)) $errorText['wrongPhone'] = "Telefona numuram jāsastāv tikai no cipariem!\n";
        } else {
            if (!Auth::user()->hasRole(['administrators', 'moderators'])) {
                if (!$email) $errorText['email'] = "Jābūt aizpildītam!\n";
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errorText['emptyEmail'] = "Lauks \"Mans e-pasts\" aizpildīts nekorekti!\n";
                if (!$phone) $errorText['phone'] = "Jābūt aizpildītam!\n";
                if ($phone && !is_numeric($phone)) $errorText['wrongPhone'] = "Telefona numuram jāsastāv tikai no cipariem!\n";
            }
        }

        if (!empty($errorText)) {
            return json_encode(['error' => $errorText]);
        }

        $form = new \stdClass();
        $form->vehicleMake = $car;
        $form->vehicleModel = $carModel;
        $form->vehiclePlate = $licPlate;
        $form->purpose = $purpose;
        $form->storageBin = $storageBin;
        $form->comment = $comment;
        $form->ownerName = $name;
        $form->ownerPhone = $phone;
        $form->ownerEmail = $email;

        $queue = Queue::where('queue_id', $queue_id)->first();
        $office = Office::where('office_id', $queue->office_id)->first();

        $queue->loadWorkingDay($date);
        $queue->loadSlots($date, true);

        $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slotNumber),true);
        $fmtDate = date('d.m.Y',strtotime($date));
        $dayOfWeek2 = $_weekDays2[date('N', strtotime($date.' 00:00:00'))];

        $slot = $queue->_slots[$date][$slotNumber];
        $slot = Slot::findOrFail($slot->slot_id);

        if ($slot->status != SLOT_STATUS_FREE && $slot->status == SLOT_STATUS_TAKEN) return json_encode(['taken' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!']);

        $slot->timestamps = false;

        $slot->takenby = json_encode($form);
        $slot->status = 1;

        $slot->createTime = $slot->editTime = NOW();
        $slot->createUser = $slot->editUser = $userID;

        $slot->save();

        switch ($form->purpose){
          case 0:{
            $purpose = '';
            $purposeLong = '';
            break;
          }
          case 1:{
            $purpose = 'riepu nomaiņa';
            $purposeLong = 'Jūs vēlaties samainīt riepas vai riteņus, kuri Jums būs līdzi';
            break;
          }
          case 2:{
            $purpose = 'riepu nomaiņa';
            $purposeLong = 'Jūs vēlaties samainīt riepas vai riteņus, kuri glabājas pie mums';
            break;
          }
          case 3:{
            $purpose = 'riepu nomaiņa';
            $purposeLong = 'Jūs vēlaties samainīt riepas vai riteņus, kurus vēlaties pie mums nopirkt';
            break;
          }
          case 4:{
            $purpose = 'kondicioniera uzpilde';
            $purposeLong = 'Jūs vēlaties uzpildīt kondicionieri';
            break;
          }
        }

        $details = [
          'car' => $form->vehicleMake,
          'make' => $form->vehicleModel,
          'purpose' => $purpose,
          'office' => $office->title,
          'day' => $dayOfWeek2,
          'date' => $fmtDate,
          'time' => $time,
          'longPurpose' => $purposeLong
        ];

        Mail::to($form->ownerEmail)->send(new \App\Mail\Mail($details));
//        $mailText = $queue->parseNotification($queue->notificationEmail, $slot->date, $slot->iorder, $form, false);
//        $mailer = new CMailer();
//        $mailer->addRecipient($form->ownerEmail);
//        $bcc = $form->ownerEmail;
//        if ($bcc) $mailer->addBCC($bcc);
//        $mailer->subject = $queue->parseNotification($queue->notificationSubject, $slot->date, $slot->iorder, $form, false);
//        $mailer->message = $mailText;
//        $mailer->send();

//        dd(mail($form->ownerEmail, 'asd', $mailText, 'From: indrikis38@gmail.com'));

        return json_encode(['success' => 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$time.' riepu servisā '.$office->title.'!</b>']);
    }

    public function fillSlotMobile(Request $request)
    {

        $userID = -1;
        if (Auth::check()) {
            $userID = Auth::user()->id;
        }

        $filiale = $request->filiale;
        $date = $request->date;
        $slot_id = $request->slot_id;

        $_weekDays2 = array(
            1=>'pirmdien',
            2=>'otrdien',
            3=>'trešdien',
            4=>'ceturtdien',
            5=>'piektdien',
            6=>'sestdien',
            7=>'svētdien',
        );

        $car = $request->car;
        $carModel = $request->carModel;
        $licPlate = $request->licPlate;
        $purpose = $request->purpose;
        $storageBin = $request->storageBin;
        $comment = $request->comment;
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;

        $errorText = [];
        if (!$car) $errorText['brand'] = "Jābūt aizpildītam!\n";
        if (!$carModel) $errorText['model'] = "Jābūt aizpildītam!\n";

        if ($filiale === NULL) $errorText['filiale'] = "Izvēlieties filiāli!\n";
        if ($date === NULL) $errorText['reservationDate'] = "Izvēlieties pieraksta datumu!\n";
        if ($slot_id === NULL) $errorText['slotId'] = "Izvēlieties pieraksta laiku!\n";
        if (!$purpose) $errorText['purpose'] = "Laukam \"Es vēlos\" jābūt izvēlētam!\n";
        if (!$phone) $errorText['phone'] = "Jābūt aizpildītam!\n";
        if ($phone && !is_numeric($phone)) $errorText['wrongPhone'] = "Telefona numuram jāsastāv tikai no cipariem!\n";
        if (!Auth::check()) {
            if (!$email) $errorText['email'] = "Jābūt aizpildītam!\n";
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errorText['emptyEmail'] = "Lauks \"Mans e-pasts\" aizpildīts nekorekti!\n";
        } else {
            if (!Auth::user()->hasRole(['administrators', 'moderators'])) {
                if (!$email) $errorText['email'] = "Jābūt aizpildītam!\n";
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errorText['emptyEmail'] = "Lauks \"Mans e-pasts\" aizpildīts nekorekti!\n";
            }
        }

        if (!empty($errorText)) {
            return json_encode(['error' => $errorText]);
        }

        $form = new \stdClass();
        $form->vehicleMake = $car;
        $form->vehicleModel = $carModel;
        $form->vehiclePlate = $licPlate;
        $form->purpose = $purpose;
        $form->storageBin = $storageBin;
        $form->comment = $comment;
        $form->ownerName = $name;
        $form->ownerPhone = $phone;
        $form->ownerEmail = $email;

        $slot = Slot::findOrFail($slot_id);

        $slot->timestamps = false;

        $slot->takenby = json_encode($form);
        $slot->status = 1;

        $slot->createTime = date('Y-m-d H:i:s');
        $slot->createUser = $userID;
        $slot->editTime = date('Y-m-d H:i:s');
        $slot->editUser = $userID;

        $queue = Queue::where('queue_id', $slot->queue_id)->first();
        $office = Office::where('office_id', $queue->office_id)->first();

        $queue->loadWorkingDay($slot->date);
        $queue->loadSlots($slot->date, true);

        $fmtDate = date('d.m.Y',strtotime($slot->date));
        $dayOfWeek2 = $_weekDays2[date('N', strtotime($slot->date.' 00:00:00'))];

        $slot->save();

        return json_encode(['success' => 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$request->slot_time.' riepu servisā '.$office->title.'!</b>']);

    }

  public function pieraksts()
  {
    $offices = Office::all();

    $date = date('Y-m-d');
    $visibleDays = 8;
    $todayDate = strtotime($date);

    foreach ($offices as $office) {
      $office->loadQueues();
      foreach ($office->_queues as $queue){

        $queue->loadWorkingDay($date,true);
        $queue->loadSlots($date,true);
        $slotSizes[] = $queue->_workingDays[$date]->slotSize;
        $workingDays[] = $date;
        for ($i=1;$i<$visibleDays;$i++){
          $ndate = date('Y-m-d',strtotime("+{$i} days",$todayDate));
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

    return view('tests.pieraksts', compact('date', '_weekDays', 'workingDays', 'timeStep', 'visibleDays', 'offices'));
  }

  public function reservations(Request $request) {
    $offices = Office::all();

    $date = $request->date;
    $visibleDays = 1;
    if ($date===null) {
      $date = date('Y-m-d');
      $visibleDays = 14;
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

    return view('records.reservation', compact('offices', 'visibleDays', 'workingDays', 'timeStep', 'date', '_weekDays', 'currentDate', 'services'));
  }

  public function reservations_print($office_id, $date)
  {

    $office = Office::findOrFail($office_id);
    $office->loadQueues();

    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();
    if ($office->office_id == 1) {
        $sheet->setTitle($date . ' Ulbroka');
    } else {
        $sheet->setTitle($date . ' Kalnciema iela');
    }

    $rowArray = [];

    $a = 1;
    $b = 2;

    foreach ($office->_queues as $queue) {
      if ($a > 1) break;
      $queue->loadWorkingDay($date);
      if ($queue->_workingDays[$date]->isVisible($date)) {
        $_weekDays = array(
          1 => 'Pirmdiena',
          2 => 'Otrdiena',
          3 => 'Trešdiena',
          4 => 'Ceturtdiena',
          5 => 'Piektdiena',
          6 => 'Sestdiena',
          7 => 'Svētdiena',
        );


        $dayOfWeek = $_weekDays[date('N', strtotime($date . ' 00:00:00'))];
        $dateFmt = date('d.m.Y', strtotime($date . ' 00:00:00'));

        /// Viss salasīts, sākam zīmēt tabulu...
        $openTime = Office::intervalByTime($queue->getOpenTime($date));
        $closeTime = Office::intervalByTime($queue->getCloseTime($date));

        $maxPageHeight = 320; //30 rindas pa 6;

        //echo $maxPageHeight / $queue->getWorkingDayLength($date);die;

        $slotCellWidth = 175;
        $slotTimeWidth = 15;
        $slotCellHeight = ($maxPageHeight) / $queue->getWorkingDayLength($date);

        $commentCellWidth = 0;


        for ($i = $openTime; $i < $closeTime; $i++) {
          foreach ($office->_queues as $queue) {
            $queue->loadWorkingDay($date);
            $queue->loadSlots($date, false);
            if ($queue->_workingDays[$date]->isVisible($date)) {
              $slotNumber = $queue->getSlotNumberByInterval($date, $i);
              if ($queue->isIntervalBeginning($date, $i)) {
                $slot = $queue->_slots[$date][$slotNumber];


                switch ($slot->status) {
                  case SLOT_STATUS_FREE:
                  {
                    $slotText = $slot->comment . '';
                    break;
                  }
                  case SLOT_STATUS_TAKEN:
                  {
                    $takenBy = json_decode($slot->takenby);
                    $service = Service::where('service_id', $takenBy->purpose)->first();
                    $slotText = $takenBy->ownerPhone . ' ' . $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $service->pdf_title . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                    break;
                  }
                  case SLOT_STATUS_OFFER:
                  {
                    $slotText = $slot->comment;
                    break;
                  }
                  case SLOT_STATUS_CLOSED:
                  {
                    if (trim($slot->comment) == '') {
                      $slotCaption = 'Slēgts!';
                    } else {
                      $slotCaption = $slot->comment;
                    }
                    $slotText = $slotCaption;
                    break;
                  }
                }

		$split = false;
                if ($queue->_workingDays[$date]->secondaryAvailable) {
		  $split = true;
		  $slotTime = Office::timeByInterval($queue->getSlotTime($date, $slot->iorder) + $queue->_workingDays[$date]->slotSize / 2);
                  switch ($slot->status2) {
                    case SLOT_STATUS_FREE:
                    {
		      //var_dump($slot->slot_id);
                      $slotText2 = $slot->comment . '';
                      break;
                    }
                    case SLOT_STATUS_TAKEN:
                    {
		      //var_dump('Aizņemts');
                      $takenBy = json_decode($slot->takenby2);
                      $service = Service::where('service_id', $takenBy->purpose)->first();
                      $slotText2 = $takenBy->ownerPhone . ' ' . $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $service->pdf_title . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                      break;
                    }
                    case SLOT_STATUS_OFFER:
                    {
		      //var_dump('Akcija');
                      $slotText2 = $slot->comment;
                      break;
                    }
                    case SLOT_STATUS_CLOSED:
                    {
		      //var_dump('Aizvērts');
                      if (trim($slot->comment) == '') {
                        $slotCaption = 'Slēgts!';
                      } else {
                        $slotCaption = $slot->comment;
                      }
                      $slotText2 = $slotCaption;
                      break;
                    }

                  }

                }


                $sheet->setCellValue('A1', 'Laiks');
                $sheet->setCellValue('B1', 'Rinda');
                $sheet->setCellValue('C1', 'Pieraksta info');


		/*switch ($slot->queue_id) {
		  case(1):
		    $queue_id = 1;
		    break;
		  case(2):
		    $queue_id = 2;
		    break;
		  case(3):
		    $queue_id = 1;
		    break;
		  case(4):
		    $queue_id = 2;
		    break;
		  case(5):
		    $queue_id = 3;
		    break;
		}*/

  		//if ($slot->queue_id == ) {
                //        $slot->queue_id = 1;
                //} else if ($slot->queue_id == 4) {
                //        $slot->queue_id = 2;
                //} else if ($slot->queue_id == 5) {
                //        $slot->queue_id = 3;
                //}

		$lastRow = $sheet->getHighestRow();

		$rowArray[] = $lastRow;
		//$rowArray = array_slice($rowArray,-1,1);
		if (((int) $i + 2) == $closeTime) {
			$lastRowI = $rowArray[count($rowArray) - 1];
		}

                if (isset($lastRowI)) {

                  //$sheet->setCellValue('A' . $, Office::timeByInterval($i));
                  $sheet->setCellValue('D' . ($lastRowI), $slotTime);
		  //$sheet->setCellValue('B' . $b, $slot->queue_id);
		  $sheet->setCellValue('E' . ($lastRowI), $slot->queue_id);
		  //$sheet->setCellValue('C' . $b, $slotText);
		  $sheet->setCellValue('F' . ($lastRowI), $slotText2);
		  $lastRowI++;
                }

                $sheet->setCellValue('A' . $b, Office::timeByInterval($i));
                $sheet->setCellValue('B' . $b, $slot->queue_id);
                $sheet->setCellValue('C' . $b, $slotText);

                $b++;
              }

            }
          }
        }

      }
      $a++;
    }
    //die;
      // Data; // foreach($slots2 as $row) // { // $queue = Queue::where('queue_id', $row['queue_id'])->first(); // $queue->loadWorkingDay($date); // $slotTime = $queue->getSlotTime($date, $row['iorder']); // 
    //$pdf->Cell($w[0],10,Office::timeByInterval($slotTime),1); // $pdf->Cell($w[1],10,$row['takenby'],1,0,'L'); // $pdf->ln(); // }
    // Closing line // $pdf->Cell(array_sum($w),0,'','T');
    $sheet->setAutoFilter('A:C');
    $lastRow = $sheet->getHighestRow();
    $sheet->getStyle('A2:B' . $lastRow)->getAlignment()->setHorizontal('center');
    $writer = new Xlsx($spreadsheet);
    $filename = 'pieraksts.xlsx';

    $writer->save($filename);

    // Set the content-type:
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filename));
    readfile($filename); // send file
    unlink($filename); // delete file
    exit;

  }

}

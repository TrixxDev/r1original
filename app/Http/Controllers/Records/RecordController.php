<?php

  namespace App\Http\Controllers\Records;

  use App\Events\NewNotification;
  use App\Helper\SmsSender;
  use App\Http\Controllers\EmailController as Mailer;
  use App\Models\Audit;
  use Illuminate\Mail\Message;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
  use App\Http\Controllers\Controller;
  use Illuminate\Support\Facades\Mail;
  use Illuminate\Http\Request;
  use Illuminate\Support\Str;
  use App\Models\Workingday;
  use App\Rules\ReCaptcha;
  use App\Models\Service;
  use App\Models\Office;
  use App\Helper\Tires;
  use App\Models\Queue;
  use App\Models\Slot;
  use App\Models\User;
  use Carbon\Carbon;
  use Auth;

  class RecordController extends Controller
  {

  public $timeToOpen;
  public $timeToClose;
  public $startSendWpp;
  public $endSendWpp;
  public $ursWpp = '120363130984594947@g.us';
  public $krsWpp = '120363150684433547@g.us';
  public $now;
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

//      $notification = 'Hello world!';
//      broadcast(new NewNotification($notification))->toOthers();

    $this->timeToOpen = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 16, 00);
    $this->timeToClose = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 8, 45);
    $this->startSendWpp = $this->timeToClose;
    $this->endSendWpp = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 18, 00);
    $this->now = \Carbon\Carbon::now();
//      $hash = $this->getRandomHash();
//
//      dd($this->isHashTaken($hash));

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
      if ($this->timeToOpen < $this->now) {
        $visibleDays = 9;
      } else {
        $visibleDays = 8;
      }
      $todayDate = strtotime($date);

      foreach ($offices as $office) {
        $office->loadQueues();
        foreach ($office->_queues as $queue){
          $queue->loadWorkingDay($date,false);
          $queue->loadSlots($date,false);
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
//    $timeStep = 1.5;
    $services = Service::orderBy('service_id', 'ASC')->get();

      return view('records.index', compact('date', '_weekDays', 'workingDays', 'timeStep', 'visibleDays', 'offices', 'services'));
    }

    public function fillFiliale()
    {
      return Office::orderBy('office_id', 'DESC')->get();
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
    $service = ($request->service) ? $request->service : false;

      $queue = Queue::where('queue_id', $queue_id)->first();
      $office = Office::where('office_id', $queue->office_id)->first();

    $queue->loadWorkingDay($date);
    $queue->loadSlots($date, true);

    $moto = false;
    if ($service == 'moto') {
      $moto = true;
    }

    $conditioner = false;
    if ($service == 'ac') {
      $conditioner = true;
    }

//    $moto = false;
//    $service = Service::where('f_moto', 1)->first();
//    if (!is_null($service)) {
//      $moto = ($queue->_workingDays[$date]->isHalf()) ? true : false;
//    }
//
//    $conditioner = false;
//    $service = Service::where('f_ac', 1)->first();
//    if (!is_null($service)) {
//      $conditioner = ($queue->_workingDays[$date]->isHalf()) ? true : false;
//    }

      $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slotNumber),true);
      $fmtDate = date('d.m.Y',strtotime($date));
      $dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];

    return json_encode(['dayOfWeek' => $dayOfWeek, 'date' => $fmtDate, 'time' => $time, 'office_title' => $office->title, 'conditioner' => $conditioner, 'moto' => $moto]);
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

      $car = strip_tags($request->car);
      $carModel = strip_tags($request->carModel);
      $licPlate = strip_tags($request->licPlate);
      $purpose = strip_tags($request->purpose);
      $storageBin = strip_tags($request->storageBin);
      $comment = strip_tags($request->comment);
      $name = strip_tags($request->name);
      $phone = strip_tags($request->phone);
      $email = strip_tags($request->email);
      $rimsWith = strip_tags($request->rims_with);

      $randomNumber = $this->getRandomHash();
      if ($this->isHashTaken($randomNumber)) {
        // generate a new random number until it's not taken
        do {
          $randomNumber = $this->getRandomHash();
        } while ($this->isHashTaken($randomNumber));
      }

      $cancelId = $randomNumber;

      $errorText = [];
      if (!$car) $errorText['brand'] = "Jābūt aizpildītam!\n";
      if (!$carModel) $errorText['model'] = "Jābūt aizpildītam!\n";
      if (!$licPlate) $errorText['reg_nr'] = "Jābūt aizpildītam!\n";

      if (!$purpose) $errorText['purpose'] = "Laukam \"Es vēlos\" jābūt aizpildītam!\n";
      if (!Auth::check()) {
        if (!$phone) $errorText['phone'] = "Jābūt aizpildītam!\n";
        if ($phone && !is_numeric($phone)) $errorText['wrongPhone'] = "Telefona numuram jāsastāv tikai no cipariem!\n";
      } else {
        if (!Auth::user()->hasRole(['administrators', 'moderators'])) {
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
      $form->cancelId = $cancelId;
      $form->rimsWith = $rimsWith;

      $queue = Queue::where('queue_id', $queue_id)->first();
      $office = Office::where('office_id', $queue->office_id)->first();

      $queue->loadWorkingDay($date);
      $queue->loadSlots($date, true);

    $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slotNumber),true);
    $fmtDate = date('d.m.Y',strtotime($date));
    $dayOfWeek2 = $_weekDays2[date('N', strtotime($date.' 00:00:00'))];
    $today = date('Y-m-d');

    if ($date == $today && Carbon::parse($time)->subHour() <= Carbon::now()) return json_encode(['taken' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!']);

    $slot = $queue->_slots[$date][$slotNumber];
//      $slot = Slot::find($slot->slot_id);

      if ($slot->status != SLOT_STATUS_FREE && $slot->status == SLOT_STATUS_TAKEN) return json_encode(['taken' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!']);

      $slot->timestamps = false;

      $slot->takenby = json_encode($form);
      $slot->status = 1;

      $slot->createtime = $slot->edittime = NOW();
      $slot->createuser = $slot->edituser = $userID;
      $slot->is_mobile = 0;

      if ($slot->save()) {
        Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Izveidots jauns pieraksts', $slot);
      } else {
        Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id,0, 'Neizdevās izveidot pierakstu', $slot);
      }

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
      case 6:{
        $purpose = 'kondicioniera uzpilde';
        $purposeLong = 'Jūs vēlaties uzpildīt kondicionieri';
        break;
      }
      case 8:{
        $purpose = 'riepu nomaiņa';
        $purposeLong = '';
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
        'longPurpose' => $purposeLong,
        'cancelId' => $cancelId
      ];

//        if (!Mail::to($form->ownerEmail)->bcc('karlis@r1riepas.lv')->send(new \App\Mail\Mail($details))) {
//          return json_encode(['success' => 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$time.' riepu servisā '.$office->title.'!</b>']);
//        }

      $smsText = $queue->parseNotification($queue->getOriginal()['notificationScheduleSMS'], $slot->date, $slot->iorder, $form, false);

      if ($form->ownerEmail) {
        $mailText = $queue->parseNotification($queue->getOriginal()['notificationEmail'], $slot->date, $slot->iorder, $form, false);
//        Mail::to($form->ownerEmail)->send(new \App\Mail\Mail($mailText));
      $mailer = new Mailer();
      $mailer->addRecipient($form->ownerEmail);
      $bcc = 'karlis@r1riepas.lv';
      if ($bcc) $mailer->addBCC($bcc);
      $mailer->subject = $queue->parseNotification($queue->getOriginal()['notificationSubject'], $slot->date, $slot->iorder, $form, false);
      $mailer->message = $mailText;
      $mailer->send();
    }

    (new SmsSender)->sendSchedule((array) $form, $smsText, $slot);
    if ($today == $slot->date && $this->now >= $this->startSendWpp && $this->now < $this->endSendWpp) {
      $service = Service::where('service_id', $form->purpose)->first();
      $vehicle = str_replace(' ', '%20', $form->vehicleMake);
      $model = str_replace(' ', '%20', $form->vehicleModel);
      $service = str_replace(' ', '%20', $service->pdf_title);
      $vehiclePlate = str_replace(' ', '%20', $form->vehiclePlate);

      if (!empty($rimsWith)) {
        if ($rimsWith == 1) {
          $append = '%20-%20Riepas%20bez%20diskiem';
        } else {
          $append = '%20-%20Riepas%20ar%20diskiem';
        }
      } else {
        $append = '';
      }

//      if ($office->office_id == 1) {
//
//        $cURLConnection = curl_init();
//
//        $url = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate . ',%20pakalpojums%20-%20' . $service . $append;
//
//        curl_setopt($cURLConnection, CURLOPT_URL, $url);
//        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
//
//        curl_exec($cURLConnection);
//
//        curl_close($cURLConnection);
//      } else {
//        $cURLConnection = curl_init();
//
//        $url = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate . ',%20pakalpojums%20-%20' . $service . $append;
//
//        curl_setopt($cURLConnection, CURLOPT_URL, $url);
//        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
//
//        curl_exec($cURLConnection);
//
//        curl_close($cURLConnection);
//      }
    }

      return json_encode(['success' => 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$time.' riepu servisā '.$office->title.'!</b><br><br>Pieraksta atcelšanas saite ir pieejama īsziņā.']);
    }

    public function showMobileQueues(Request $request) {
      if ($request->post()) {
        $office = Office::where('office_id', $request->office_id)->first();

        $days = [];

        $date = date('Y-m-d');
        if ($this->timeToOpen < $this->now) {
          $visibleDays = 9;
        } else {
          $visibleDays = 8;
        }
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
            $out .= '<h3>' . $office->title . '<br>' . $dayOfWeek . ' ' . $dateFmt . '</h3>';
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
              $office->loadWorkingDays($date);
              $queue->loadSlots($date);
              $slotNumber = ($queue->getSlotNumberByInterval($date, $i));
              if (($slotNumber !== false) && ($queue->_workingDays[$date]->isVisible()))
                if ($queue->isIntervalBeginning($date, $i)) {
                  $slot = $queue->_slots[$date][$slotNumber];
                  $slot_id = 'data-slot_id=' . $slot->slot_id;
                  switch ($slot->status) {
                    case SLOT_STATUS_FREE: {
                      if ($date == $today) {
                        if (Carbon::parse(Office::timeByInterval($i))->subHour() >= Carbon::now()) {
                          if (trim($slot->comment)=='') {
                            $availability = 'available active';
                            $slotText = 'Brīvs';
                            $discount = false;
                          } else {
                            $availability = 'available discount active';
                            $slotText = $slot->comment;
                            $discount = true;
                          }
                          if ($queue->_workingDays[$date]->isHalf()) {
                            $service = Service::where('f_ac', 1)->first();
                            if (!is_null($service)) {
                              $availability = 'available conditioner active';
                              $slotText = 'AC Uzpilde';
                              $discount = false;
                            } else {
                              $availability = 'available active';
                              $slotText = 'Brīvs';
                              $discount = false;
                            }
                          }
                        } else {
                          $availability = 'unavailable';
                          $slotText = 'Aizņemts';
                          $discount = false;
                        }
                      } else {
                        if (trim($slot->comment)=='') {
                          $availability = 'available active';
                          $slotText = 'Brīvs';
                          $discount = false;
                        } else {
                          $availability = 'available discount active';
                          $slotText = $slot->comment;
                          $discount = true;
                        }
                        if ($queue->_workingDays[$date]->isHalf()) {
                          $service = Service::where('f_ac', 1)->first();
                          if (!is_null($service)) {
                            $availability = 'available conditioner active';
                            $slotText = 'AC Uzpilde';
                            $discount = false;
                          } else {
                            $availability = 'available active';
                            $slotText = 'Brīvs';
                            $discount = false;
                          }
                        }
                      }
                      break;
                    }

                    case SLOT_STATUS_TAKEN: {
                      $availability = 'unavailable';
                      $slotText = 'Aizņemts';
                      $discount = false;
                      break;
                    }

                    case SLOT_STATUS_OFFER: {
                      if ($date == $today) {
                        if (Carbon::parse(Office::timeByInterval($i))->subHour() >= Carbon::now()) {
                          $availability = 'available discount active';
                          $slotText = $slot->comment;
                          $discount = true;
                        } else {
                          $availability = 'unavailable';
                          $slotText = 'Aizņemts';
                          $discount = false;
                        }
                      } else {
                        $availability = 'available discount active';
                        $slotText = $slot->comment;
                        $discount = true;
                      }
                      break;
                    }
                  }
                  $out .= '<div class="time-slot">';
                  $out .= '<div ' . $slot_id . ' class="' . $availability . ' slot"><span class="time-span">' . Office::timeByInterval($i) . '</span><br><span>' . $slotText . '</span></div>';
                  $out .= '</div>';


//                $slots[$queue->getSlotNumberByInterval($date, $i)] = [
//                  'time' => Office::timeByInterval($i),
//                  'slots' => $this->getPrevQueue($office->_workingDays, $queue->getSlotNumberByInterval($date, $i), $date),
//                  'date' => $date
//                ];
//                sort($slots);
                } else {
                  if ($queue->_workingDays[$date]->secondaryAvailable) {

                    $slot = $queue->_slots[$date][$slotNumber];

                    $slot_id = 'data-slot_id=' . $slot->slot_id;
                    switch ($slot->status2) {
                      case SLOT_STATUS_OFFER:
                      case SLOT_STATUS_FREE: {
                        if ($date == $today) {
                          if (Carbon::parse(Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now()) {
                            $service = Service::where('f_moto', 1)->first();
                            if (!is_null($service)) {
                              $availability = 'available moto active';
                              $slotText = 'Moto montāža';
                              $discount = false;
                            } else {
                              $availability = 'unavailable';
                              $slotText = '----------';
                              $discount = false;
                            }
                          } else {
                            $availability = 'unavailable';
                            $slotText = 'Aizņemts';
                            $discount = false;
                          }
                        } else {
                          $service = Service::where('f_moto', 1)->first();
                          if (!is_null($service)) {
                            $availability = 'available moto active';
                            $slotText = 'Moto montāža';
                          } else {
                            $availability = 'unavailable';
                            $slotText = '----------';
                            $discount = false;
                          }
                        }
                        break;
                      }

                      case (SLOT_STATUS_CLOSED): {
                        if (trim($slot->comment) == '') {
                          $slotCaption = 'Slēgts';
                        }else {
                          $slotCaption = $slot->comment;
                        }
                        $availability = 'closed-slot';
                        $slotText = $slotCaption;
                        $discount = false;
                        break;
                      }

                      case (SLOT_STATUS_TAKEN): {
                        $availability = 'unavailable';
                        $slotText = 'Aizņemts';
                        $discount = false;
                        break;
                      }

                    }
                    $out .= '<div class="time-slot">';
                    $out .= '<div ' . $slot_id . ' class="' . $availability . ' slot"><span class="time-span">' . Office::timeByInterval($i) . '</span><br><span>' . $slotText . '</span></div>';
                    $out .= '</div>';

                  }
                }
              }
            }
//            foreach ($queue->_slots[$date] as $slot) {
//              if ($queue->isIntervalBeginning($date, $i)) {
//                $slot_id = 'data-slot_id=' . $slot->slot_id;
//                if (stripos($slot->comment, '% darbam') !== false) {
//                  $slotText = str_replace('!', '', $slot->comment);
//                  $slotText = '<span>' . $slotText . '</span>';
//                  $availability = 'discount available';
//                  $discount = true;
//                } else {
//                  $slotText = 'Brīvs';
//                  $slotText = '<span>' . $slotText . '</span>';
//                  $availability = 'available';
//                  $discount = false;
//                }
//                $out .= '<div class="time-slot">';
//                $out .= '<div ' . $slot_id . ' class="' . $availability . ' slot active"><span class="time-span">' . Office::timeByInterval($i) . '</span><br>' . $slotText . '</div>';
//                $out .= '</div>';
//              }
//            }
//          }

//          if (!empty($slots)) {
//            foreach ($slots as $slot_iorder => $slot_info){
//              if ($date == $slot_info['date']) {
//                $freeSlot = $this->getLastNonNullValue($slot_info['slots']);
//                $slot = $freeSlot[array_key_first($freeSlot)];
//                $slot_id = 'data-slot_id=' . $slot->slot_id;
//                if (stripos($slot->comment, '% darbam') !== false) {
//                  $slotText = str_replace('!', '', $slot->comment);
//                  $slotText = '<span>' . $slotText . '</span>';
//                  $availability = 'discount available';
//                  $discount = true;
//                } else {
//                  $slotText = 'Brīvs';
//                  $slotText = '<span>' . $slotText . '</span>';
//                  $availability = 'available';
//                  $discount = false;
//                }
//                $out .= '<div class="time-slot">';
//                $out .= '<div ' . $slot_id . ' class="' . $availability . ' slot active"><span class="time-span">' . $slot_info['time'] . '</span><br>' . $slotText . '</div>';
//                $out .= '<div class="dots">';
//                if (!empty($slot_info['slots'])) {
//                  foreach ($slot_info['slots'] as $slot) {
//                    if ($slot !== null) {
//                      $workingDay = Workingday::where('date', $slot->date)->where('queue_id', $slot->queue_id)->first();
//
//                        if (stripos($slot->comment, '% darbam') !== false) {
//                          $out .= '<span class="dot-availability text-center">
//                          <span class="dot orange" data-toggle="tooltip" data-html="true" title="Atlaide">
//                            <span class="sort-order">orange</span>
//                          </span>
//                        </span>';
//                        } else {
//                          $out .= '<span class="dot-availability text-center">
//                          <span class="dot green" data-toggle="tooltip" data-html="true" title="Brīvs">
//                            <span class="sort-order">green</span>
//                          </span>
//                        </span>';
//                        }
//                    } else {
//                      $out .= '<span class="dot-availability text-center">
//                        <span class="dot red" data-toggle="tooltip" data-html="true" title="Aizņemts">
//                          <span class="sort-order">red</span>
//                        </span>
//                      </span>';
//                    }
//                  }
//                } else {
//                  $out .= '<span class="dot-availability text-center">
//                  <span class="dot transparent" style="" data-toggle="tooltip" data-html="true" title="Aizņemts">
//                    <span class="sort-order">transparent</span>
//                  </span>
//                </span>';
//                }
//                $out .= '</div></div>';
//              }
//            }
//          }

          $out .= '</div>';

        }
        $out .= '</div></div>';

        return $out;
      }
    }

    public function getPrevQueue($queueList, $iorder, $date)
    {
      $queues = [];
      foreach ($queueList as $queue) {
        $slot = Slot::where('date', $date)->where('iorder', $iorder)->where('queue_id', $queue->queue_id)->first();
        if ($slot) {
          if ($slot->status == 0 || $slot->status == 2) {
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
      $rimsWith = $request->rims_with;
      $cancelId = $this->getRandomHash();

      $errorText = [];
      if (!$car) $errorText['brand'] = "Jābūt aizpildītam!\n";
      if (!$carModel) $errorText['model'] = "Jābūt aizpildītam!\n";
      if (!$licPlate) $errorText['reg_nr'] = "Jābūt aizpildītam!\n";

        if ($filiale === NULL) $errorText['filiale'] = "Izvēlieties filiāli!\n";
        if ($slot_id === NULL) $errorText['slotId'] = "Izvēlieties pieraksta laiku!\n";
        if (!$purpose) $errorText['purpose'] = "Laukam \"Es vēlos\" jābūt izvēlētam!\n";
        if (!$phone) $errorText['phone'] = "Jābūt aizpildītam!\n";
        if ($phone && !is_numeric($phone)) $errorText['wrongPhone'] = "Telefona numuram jāsastāv tikai no cipariem!\n";

      if (!empty($errorText)) {
        return json_encode(['error' => $errorText]);
      }

      $randomNumber = $this->getRandomHash();
      if ($this->isHashTaken($randomNumber)) {
        // generate a new random number until it's not taken
        do {
          $randomNumber = $this->getRandomHash();
        } while ($this->isHashTaken($randomNumber));
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
      $form->cancelId = $cancelId;
      $form->rimsWith = $rimsWith;

      $slot = Slot::findOrFail($slot_id);
      if ($slot->status != SLOT_STATUS_FREE && $slot->status == SLOT_STATUS_TAKEN) return json_encode(['taken' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!']);

      $slot->timestamps = false;

      $slot->takenby = json_encode($form);
      $slot->status = 1;

      $slot->createtime = date('Y-m-d H:i:s');
      $slot->createuser = $userID;
      $slot->edittime = date('Y-m-d H:i:s');
      $slot->edituser = $userID;
      $slot->is_mobile = 1;

      $queue = Queue::where('queue_id', $slot->queue_id)->first();
      $office = Office::where('office_id', $queue->office_id)->first();

      $queue->loadWorkingDay($slot->date);
      $queue->loadSlots($slot->date, true);

      $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slot->iorder),true);
      $fmtDate = date('d.m.Y',strtotime($slot->date));
      $dayOfWeek2 = $_weekDays2[date('N', strtotime($slot->date.' 00:00:00'))];

      $today = date('Y-m-d');

      if ($date == $today && Carbon::parse($time)->subHour() <= Carbon::now()) return json_encode(['taken' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!']);

      if ($slot->save()) {
        Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Izveidots jauns pieraksts', $slot);
      } else {
        Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id,0, 'Neizdevās izveidot pierakstu', $slot);
      }

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
        case 6:{
          $purpose = 'kondicioniera uzpilde';
          $purposeLong = 'Jūs vēlaties uzpildīt kondicionieri';
          break;
        }
        case 8:{
          $purpose = 'riepu nomaiņa';
          $purposeLong = '';
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
        'longPurpose' => $purposeLong,
        'cancelId' => $cancelId
      ];

        $smsText = $queue->parseNotification($queue->getOriginal()['notificationScheduleSMS'], $slot->date, $slot->iorder, $form, false);

        if ($form->ownerEmail) {
          $mailText = $queue->parseNotification($queue->getOriginal()['notificationEmail'], $slot->date, $slot->iorder, $form, false);
//        Mail::to($form->ownerEmail)->send(new \App\Mail\Mail($mailText));
          $mailer = new Mailer();
          $mailer->addRecipient($form->ownerEmail);
          $bcc = 'karlis@r1riepas.lv';
          if ($bcc) $mailer->addBCC($bcc);
          $mailer->subject = $queue->parseNotification($queue->getOriginal()['notificationSubject'], $slot->date, $slot->iorder, $form, false);
          $mailer->message = $mailText;
          $mailer->send();
        }

        (new SmsSender)->sendSchedule((array) $form, $smsText, $slot);
        if ($today == $slot->date && $this->now >= $this->startSendWpp && $this->now < $this->endSendWpp) {
          $service = Service::where('service_id', $form->purpose)->first();
          $vehicle = str_replace(' ', '%20', $form->vehicleMake);
          $model = str_replace(' ', '%20', $form->vehicleModel);
          $service = str_replace(' ', '%20', $service->pdf_title);
          $vehiclePlate = str_replace(' ', '%20', $form->vehiclePlate);

          if (!empty($rimsWith)) {
            if ($rimsWith == 1) {
              $append = '%20-%20Riepas%20bez%20diskiem';
            } else {
              $append = '%20-%20Riepas%20ar%20diskiem';
            }
          } else {
            $append = '';
          }


          if ($office->office_id == 1) {

            $cURLConnection = curl_init();

            $url = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate . ',%20pakalpojums%20-%20' . $service . $append;

            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            curl_exec($cURLConnection);

            curl_close($cURLConnection);
          } else {
            $cURLConnection = curl_init();

            $url = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate . ',%20pakalpojums%20-%20' . $service . $append;

            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            curl_exec($cURLConnection);

            curl_close($cURLConnection);
          }
        }

        return json_encode(['success' => 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$time.' riepu servisā '.$office->title.'!</b><br><br>Pieraksta atcelšanas saite ir pieejama īsziņā.']);

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
//      $timeStep = 1.5;

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
                        $slotDevice = '';
                        $phone = '';
                        $purpose = '';
                        $storageBin = '';
                        $slotText = $slot->comment . '';
                        $created = 'Apmeklētājs';
                        $edited = '';
                        $lastAction = (is_null($slot->edittime)) ? $slot->createtime : $slot->edittime;
                        $lastAction = (is_null($lastAction)) ? '' : $lastAction;
                        break;
                      }
                    case SLOT_STATUS_TAKEN:
                      {
                        $takenBy = json_decode($slot->takenby);
                        $slotDevice = ($slot->is_mobile == 1) ? 'Mobīlā ierīce' : 'Dators';
                        $phone = ($takenBy->ownerPhone) ? $takenBy->ownerPhone : '';
                        $storageBin = (isset($takenBy->storageBin) && !empty($takenBy->storageBin)) ? $takenBy->storageBin : '';
                        $createduser = User::where('id', $slot->createuser)->first();
                        $editeduser = User::where('id', $slot->edituser)->first();
                        $created = ($createduser) ? $createduser->fullName : 'Apmeklētājs';
                        $edited = ($editeduser) ? $editeduser->fullName : '';
                        $lastAction = (is_null($slot->edittime)) ? $slot->createtime : $slot->edittime;
                        $lastAction = (is_null($lastAction)) ? '' : $lastAction;
                        if (!$takenBy->vehicleMake && !$takenBy->vehicleModel) {
                          $purpose = '';
                          $slotText = '';
                        } else {
                          if (!isset($takenBy->purpose)) {
                            $purpose = '';
                            $slotText = $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                          } else {
                            $service = Service::where('service_id', $takenBy->purpose)->first();
                            if (!empty($takenBy->rimsWith)) {
                              if ($takenBy->rimsWith == 1) {
                                $rimsWith = 'Riepas bez diskiem';
                              } else {
                                $rimsWith = 'Riepas ar diskiem';
                              }
                              $purpose = $service->pdf_title . ' - ' . $rimsWith;
                            } else {
                              $purpose = $service->pdf_title;
                            }
                            $slotText = $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                          }
                        }
                        break;
                      }
                    case SLOT_STATUS_OFFER:
                      {
                        $slotDevice = '';
                        $phone = '';
                        $purpose = '';
                        $storageBin = '';
                        $slotText = $slot->comment;
                        $created = 'Apmeklētājs';
                        $edited = '';
                        $lastAction = (is_null($slot->edittime)) ? $slot->createtime : $slot->edittime;
                        $lastAction = (is_null($lastAction)) ? '' : $lastAction;
                        break;
                      }
                    case SLOT_STATUS_CLOSED:
                      {
                        $slotDevice = '';
                        $phone = '';
                        $purpose = '';
                        $storageBin = '';
                        if (trim($slot->comment) == '') {
                          $slotCaption = 'Slēgts!';
                        } else {
                          $slotCaption = $slot->comment;
                        }
                        $slotText = $slotCaption;
                        $created = 'Apmeklētājs';
                        $edited = '';
                        $lastAction = (is_null($slot->edittime)) ? $slot->createtime : $slot->edittime;
                        $lastAction = (is_null($lastAction)) ? '' : $lastAction;
                        break;
                      }
                  }


                  $sheet->setCellValue('A1', 'Laiks');
                  $sheet->setCellValue('B1', 'Rinda');
                  $sheet->setCellValue('C1', 'Iekārta');
                  $sheet->setCellValue('D1', 'Numurs');
                  $sheet->setCellValue('E1', 'Pakalpojums');
                  $sheet->setCellValue('F1', 'Talona nr.');
                  $sheet->setCellValue('G1', 'Pieraksta info');
                  $sheet->setCellValue('H1', 'Izveidots');
                  $sheet->setCellValue('I1', 'Labots');
                  $sheet->setCellValue('J1', 'Pēdējā darbība');


                  switch ($slot->queue_id) {
                    case (3):
                    case(1):
                      $queue_id = 1;
                      break;
                    case (4):
                    case(2):
                      $queue_id = 2;
                      break;
                    case(5):
                      $queue_id = 3;
                      break;
                  }

                  /*if (isset($lastRowI)) {

                    //$sheet->setCellValue('A' . $, Office::timeByInterval($i));
        //$sheet->setCellValue('B' . $b, $slot->queue_id);
        $sheet->setCellValue('E' . ($lastRowI), $queue_id);
        //$sheet->setCellValue('C' . $b, $slotText);
        $sheet->setCellValue('F' . ($lastRowI), $slotText2);
        $lastRowI++;
      }*/

                  $sheet->setCellValue('A' . $b, Office::timeByInterval($i));
                  $sheet->setCellValue('B' . $b, $queue_id);
                  $sheet->setCellValue('C' . $b, $slotDevice);
                  $sheet->setCellValue('D' . $b, $phone);
                  $sheet->setCellValue('E' . $b, $purpose);
                  $sheet->setCellValue('F' . $b, $storageBin);
                  $sheet->setCellValue('G' . $b, $slotText);
                  $sheet->setCellValue('H' . $b, $created);
                  $sheet->setCellValue('I' . $b, $edited);
                  $sheet->setCellValue('J' . $b, $lastAction);

                  $b++;

                  if ($queue->_workingDays[$date]->secondaryAvailable) {
                    $slotTime = Office::timeByInterval($queue->getSlotTime($date, $slot->iorder) + $queue->_workingDays[$date]->slotSize / 2);
                    switch ($slot->status2) {
                      case SLOT_STATUS_FREE:
                        {
                          $slotDevice2 = '';
                          $phone2 = '';
                          $purpose2 = '';
                          $storageBin2 = '';
                          $slotText2 = $slot->comment . '';
                          $created2 = 'Apmeklētājs';
                          $edited2 = '';
                          $lastAction2 = (is_null($slot->edittime2)) ? $slot->createtime2 : $slot->edittime2;
                          $lastAction2 = (is_null($lastAction2)) ? '' : $lastAction2;
                          break;
                        }
                      case SLOT_STATUS_TAKEN:
                        {
                          $takenBy = json_decode($slot->takenby2);
                          $slotDevice2 = ($slot->is_mobile == 1) ? 'Mobīlā ierīce' : 'Dators';
                          $phone2 = ($takenBy->ownerPhone) ? $takenBy->ownerPhone : '';
                          $storageBin2 = (isset($takenBy->storageBin) && !empty($takenBy->storageBin)) ? $takenBy->storageBin : '';
                          $createduser2 = User::where('id', $slot->createuser2)->first();
                          $editeduser2 = User::where('id', $slot->edituser2)->first();
                          $created2 = ($createduser2) ? $createduser2->fullName : 'Apmeklētājs';
                          $edited2 = ($editeduser2) ? $editeduser2->fullName : '';
                          $lastAction2 = (is_null($slot->edittime2)) ? $slot->createtime2 : $slot->edittime2;
                          $lastAction2 = (is_null($lastAction2)) ? '' : $lastAction2;
                          if (!$takenBy->vehicleMake && !$takenBy->vehicleModel) {
                            $purpose2 = '';
                            $slotText2 = '';
                          } else {
                            if (!$takenBy->purpose) {
                              $purpose2 = '';
                              $slotText2 = $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                            } else {
                              $service = Service::where('service_id', $takenBy->purpose)->first();
                              if (!empty($service)) {
                                if (isset($takenBy->rimsWith)) {
                                  if ($takenBy->rimsWith == 1) {
                                    $rimsWith = 'Riepas bez diskiem';
                                  } else {
                                    $rimsWith = 'Riepas ar diskiem';
                                  }
                                  $purpose2 = $service->pdf_title . ' - ' . $rimsWith;
                                } else {
                                  $purpose2 = $service->pdf_title;
                                }
                              } else {
                                $purpose2 = '';
                                $rimsWith = '';
                              }
                              $slotText2 = $takenBy->vehicleMake . ' ' . $takenBy->vehicleModel . ' // ' . $takenBy->vehiclePlate . ' ' . $takenBy->ownerName . ' ' . $takenBy->comment . ' ' . $slot->comment;
                            }
                          }
                          break;
                        }
                      case SLOT_STATUS_OFFER:
                        {
                          $slotDevice2 = '';
                          $phone2 = '';
                          $purpose2 = '';
                          $storageBin2 = '';
                          $slotText2 = $slot->comment;
                          $created2 = 'Apmeklētājs';
                          $edited2 = '';
                          $lastAction2 = (is_null($slot->edittime2)) ? $slot->createtime2 : $slot->edittime2;
                          $lastAction2 = (is_null($lastAction2)) ? '' : $lastAction2;
                          break;
                        }
                      case SLOT_STATUS_CLOSED:
                        {
                          $slotDevice2 = '';
                          $phone2 = '';
                          $purpose2 = '';
                          $storageBin2 = '';
                          if (trim($slot->comment) == '') {
                            $slotCaption = 'Sl ^sgts!';
                          } else {
                            $slotCaption = $slot->comment;
                          }
                          $slotText2 = $slotCaption;
                          $created2 = 'Apmeklētājs';
                          $edited2 = '';
                          $lastAction2 = (is_null($slot->edittime2)) ? $slot->createtime2 : $slot->edittime2;
                          $lastAction2 = (is_null($lastAction2)) ? '' : $lastAction2;
                          break;
                        }

                    }

                    $sheet->setCellValue('A' . ($b), $slotTime);
                    $sheet->setCellValue('B' . ($b), $queue_id);
                    $sheet->setCellValue('C' . ($b), $slotDevice2);
                    $sheet->setCellValue('D' . ($b), $phone2);
                    $sheet->setCellValue('E' . ($b), $purpose2);
                    $sheet->setCellValue('F' . ($b), $storageBin2);
                    $sheet->setCellValue('G' . ($b), $slotText2);
                    $sheet->setCellValue('H' . ($b), $created2);
                    $sheet->setCellValue('I' . ($b), $edited2);
                    $sheet->setCellValue('J' . ($b), $lastAction2);
                  }

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
      $sheet->setAutoFilter('A:J');
      $lastRow = $sheet->getHighestRow();
      $sheet->getStyle('A2:F' . $lastRow)->getAlignment()->setHorizontal('center');
      $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
      $cellIterator->setIterateOnlyExistingCells(true);
      foreach ($cellIterator as $cell) {
        if ($cell->getColumn() == 'F') continue;
        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
      }
//    $sheet->getColumnDimension('E')->setWidth(15);
      $sheet->getColumnDimension('F')->setWidth(12);
//    $sheet->getColumnDimension('C')->setWidth(14);
//    $sheet->getColumnDimension('D')->setWidth(15);
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

    public function cancelSlot(Request $request, $id)
    {
      $slot = Slot::where('takenBy', 'like', '%"cancelId":"' . $id . '"%')->orWhere('takenBy2', 'like', '%"cancelId":"' . $id . '"%')->first();

      $date = date('Y-m-d');
      if (!$slot) return redirect(route('pieraksts'));

      if ($slot->date < $date) return redirect(route('pieraksts'))->with('warning', 'Jūsu pieraksts vairs nav aktuāls');
//      if ($slot->date == $date && $this->timeToClose < $this->now) return redirect(route('pieraksts'))->with('warning', 'Pierakstu atcelt tiešsaistē iespējams līdz <b>8:45</b>, ja vēlaties mainīt pieraksta laiku vēlāk, zvaniet');

      $queue = Queue::where('queue_id', $slot->queue_id)->first();

      $office = Office::where('office_id', $queue->office_id)->first();
      $office->loadQueues();
      foreach ($office->_queues as $queue) {
        $queue->loadWorkingDay($slot->date,false);
      }
      $office->loadWorkingDays($slot->date);

      $day = $office->_workingDays[0];
      $start = Office::intervalByTime($day->opentime);

      $takenBy = json_decode($slot->takenby);
      $takenBy2 = json_decode($slot->takenby2);

      if ($day->isHalf()) {
        if ($takenBy !== null) {
          $startTime = $start + $slot->iorder * ($day->slotSize/2);
          $info = $takenBy;
        }
        if ($takenBy2 !== null) {
          $startTime = $start + $slot->iorder * ($day->slotSize);
          $info = $takenBy2;
        }
      } else {
        if ($takenBy !== null) {
          $info = $takenBy;
        }
        if ($takenBy2 !== null) {
          $info = $takenBy2;
        }
        $startTime = $start + $slot->iorder * ($day->slotSize);
      }
      $time = Office::timeByInterval($startTime);

      if ($request->post()) {
        if ($takenBy !== null) {
          if ($takenBy->cancelId == $id) {
            $slot->status = 0;
            $slot->takenby = '';
            $slot->createtime = NULL;
            $slot->createuser = -1;
            $slot->edittime = NULL;
            $slot->edituser = -1;
            $slot->is_mobile = 0;
          }
        }

        if ($takenBy2 !== null) {
          if ($takenBy2->cancelId == $id) {
            $slot->status2 = 0;
            $slot->takenby2 = '';
            $slot->createtime2 = NULL;
            $slot->createuser2 = -1;
            $slot->edittime2 = NULL;
            $slot->edituser2 = -1;
            $slot->is_mobile2 = 0;
          }
        }

        if ($slot->save()) {

            if ($info->ownerEmail) {
              $mailText = $queue->parseNotification($queue->getOriginal()['notificationCancelEmail'], $slot->date, $slot->iorder, $info, false);

              $mailer = new Mailer();
              $mailer->addRecipient($info->ownerEmail);
              $bcc = 'karlis@r1riepas.lv';
              if ($bcc) $mailer->addBCC($bcc);
              $mailer->subject = 'Tava rezervacija R1 riepu servisā ATCELTA';
              $mailer->message = $mailText;
              $mailer->send();
            }

            $smsText = $queue->parseNotification($queue->getOriginal()['notificationScheduleCancelSMS'], $slot->date, $slot->iorder, $info, false);

            (new SmsSender)->sendSchedule((array) $info, $smsText, $slot);
            if ($date == $slot->date) {

              $vehicle = str_replace(' ', '%20', $info->vehicleMake);
              $model = str_replace(' ', '%20', $info->vehicleModel);
              $vehiclePlate = str_replace(' ', '%20', $info->vehiclePlate);

              if ($office->office_id == 1) {

                $cURLConnection = curl_init();

                curl_setopt($cURLConnection, CURLOPT_URL, 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                curl_exec($cURLConnection);

                curl_close($cURLConnection);
              } else {
                $cURLConnection = curl_init();

                curl_setopt($cURLConnection, CURLOPT_URL, 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $time . '%20' . $vehicle . '%20' . $model . ',%20' . $vehiclePlate);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                curl_exec($cURLConnection);

                curl_close($cURLConnection);
              }
            }

          Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Atcelts pieraksts', $slot);
          return redirect(route('pieraksts'))->with('success', 'Atcelšana ir izdevusies');
        } else {
          Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Neizdevās atcelt pierakstu', $slot);
          return redirect(route('pieraksts'))->with('danger', 'Notikusi kļūda');
        }
      }

      $_weekDays = [
        1 => 'pirmdien',
        2 => 'otrdien',
        3 => 'trešdien',
        4 => 'ceturtdien',
        5 => 'piektdien',
        6 => 'sestdien',
        7 => 'svētdien',
      ];

      $queue = Queue::where('queue_id', $slot->queue_id)->first();
      $office = Office::where('office_id', $queue->office_id)->first();
      return view('records.cancel', compact('slot', '_weekDays', 'info', 'time', 'office'));
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

      $slots = Slot::select('takenby', 'takenby2')->where('takenby', 'like', '%"cancelId":%')->orWhere('takenby2', 'like', '%"cancelId"%')->get();
      foreach ($slots as $slot) {
        $takenBy = json_decode($slot->takenby);
        $takenBy2 = json_decode($slot->takenby2);
        if (!empty($takenBy)) {
          if (property_exists($takenBy, 'cancelId')) {
            $takenHashes[] = $takenBy->cancelId;
          }
        }

        if (!empty($takenBy2)) {
          if (property_exists($takenBy2, 'cancelId')) {
            $takenHashes[] = $takenBy2->cancelId;
          }
        }
      }

      if (in_array($value, $takenHashes)) {
        return true;
      }

      return false;
    }

  }

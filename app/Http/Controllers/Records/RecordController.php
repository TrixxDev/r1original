<?php

  namespace App\Http\Controllers\Records;

  use App\Events\NewNotification;
  use App\Helper\SmsSender;
  use App\Http\Controllers\EmailController as Mailer;
  use App\Models\Audit;
  use App\Models\NewWorkingDay;
  use Carbon\CarbonInterval;
  use Carbon\CarbonPeriod;
  use Illuminate\Mail\Message;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Style\Border;
  use PhpOffice\PhpSpreadsheet\Style\Color;
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
    public $orderWpp = '120363248805017034@g.us';
    //    public $ursWpp = '120363157143688336@g.us';
    //    public $krsWpp = '120363157143688336@g.us';
    public $now;
    public $dayTitles;
    public $timeStep = 15;

    public $startTime = '07:00';
    public $closeTime = '21:00';
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
    $this->startSendWpp = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 8, 00);
    $this->endSendWpp = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 18, 00);
    $this->now = \Carbon\Carbon::now();
    $this->dayTitles = [
      1 => 'Pirmdiena',
      2 => 'Otrdiena',
      3 => 'Trešdiena',
      4 => 'Ceturtdiena',
      5 => 'Piektdiena',
      6 => 'Sestdiena',
      7 => 'Svētdiena',
    ];

//      $hash = $this->getRandomHash();
//
//      dd($this->isHashTaken($hash));

    }

    public function loadWorkingDays()
    {
      $visibleDays = 14;

      $daysToShow = [];

      $workingDayCount = Office::sum('queue_count');
      for ($i = 0; $i <= $visibleDays; $i++) {
        $date = Date('Y-m-d', strtotime('+' . $i . ' days'));
        array_push($daysToShow, $date);

        $workingDay = Workingday::where('date', $date)->get();
        if (empty($workingDay->count()) || $workingDay->count() < $workingDayCount) {
          $l = 1;
          foreach (Office::all() as $office) {
            for ($a = 1; $a <= $office->queue_count; $a++) {
              $wdExist = Workingday::where('date', $date)->where('queue_id', $l)->first();
              if ($wdExist) {
                $l++;
                continue;
              }
              $workingDay = new Workingday();
              $workingDay->timestamps = false;
              $workingDay->queue_id = $l;
              $workingDay->office_id = $office->office_id;
              $workingDay->date = $date;
              $workingDay->weekday = Carbon::parse($date)->format('N');
              $queue = Queue::where('queue_id', $l)->first();
              $workingDay->timeopen = $queue->timeopen;
              $workingDay->timeclose = $queue->timeclose;
              $weekendDay = Carbon::parse($date)->isWeekend();
              if ($weekendDay) {
                $workingDay->timeopen = $queue->wtimeopen;
                $workingDay->timeclose = $queue->wtimeclose;
              }
              $workingDay->is_opened = ($queue->is_visible == 1) ? 1 : 0;
              if ($workingDay->weekday == 7) $workingDay->is_opened = 0;
              $workingDay->save();
              $newWorkingDay = $workingDay->replicate();
              $newWorkingDay->setTable('new_workingdays');
              $newWorkingDay->timestamps = false;
              $newWorkingDay->save();
              $l++;
            }
          }
        }
      }

      return $daysToShow;

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

      $this->loadWorkingDays();
      $offices = Office::all();
      $services = Service::where('enabled', 1)->get();

      $visibleDays = 7;
      $daysToShow = [];

      for ($i = 0; $i <= $visibleDays; $i++) {
        array_push($daysToShow, Date('Y-m-d', strtotime('+' . $i . ' days')));
      }

      $workingDays = Workingday::whereIn('date', $daysToShow)->get();

      $dayTitles = $this->dayTitles;
      $timeStep = $this->timeStep;

      return view('records.index', compact('workingDays', 'visibleDays', 'dayTitles', 'timeStep', 'offices', 'services'));
    }

    public function fillFiliale()
    {
      return Office::orderBy('office_id', 'DESC')->get();
    }

  public function getSlotInfo(Request $request)
  {
    $date = $request->input('date');
    $queue_id = $request->input('queue_id');
    $slotNumber = $request->input('iorder');

    $workingDay = Workingday::where('date', $date)->where('queue_id', $queue_id)->first();
    $office_id = Office::where('office_id', $workingDay->office_id)->first()->office_id;

    $slot = Slot::where('date', $date)->where('queue_id', $queue_id)->where('iorder', $slotNumber)->first();

    if ($slot) {

      $resultArray = (array) json_decode($slot->takenby);

      $created_user = User::find($slot->createuser);
      $edited_user = User::find($slot->edituser);

      $slot->createuser = ($created_user) ? $created_user->fullName : 'Klients';
      $slot->createtime = ($slot->createtime) ? $slot->createtime : '';
      $slot->edituser = ($edited_user) ? $edited_user->fullName : '';
      $slot->edittime = ($slot->edittime) ? $slot->edittime : '';
      $slot->is_mobile = ($slot->is_mobile === 1) ? 'mobilās ierīces' : 'datora';

      if (!empty($resultArray)) {
        return $slot;
      } else {
        if ($slot->comment) {
          return json_encode(['takenby' => 'false', 'office_id' => $office_id, 'discount' => $slot->comment]);
        } else {
          return json_encode(['takenby' => 'false', 'office_id' => $office_id]);
        }
      }
    } else {
      return json_encode(['takenby' => 'false', 'office_id' => $office_id]);
    }
  }

    public function fillSlot(Request $request)
    {

      $userID = -1;
      if (Auth::check()) {
        $userID = Auth::user()->id;
      }

      $dopParams = $request->input('dopParams');

      $dayTitles = $this->dayTitles;
      $today = date('Y-m-d');

      $dayOfWeek2 = $dayTitles[date('N', strtotime($dopParams['date'] . ' 00:00:00'))];
      $fmtDate = date('d.m.Y', strtotime($dopParams['date']));

      $office = Office::where('office_id', $dopParams['office'])->first();
      $time = strip_tags($dopParams['time']);

      $cancelId = $this->getRandomHash() . str_replace(':', '', $time);

      $errors = [];

      $datas = explode('&', $request->input('formData'));

      $result = [];

      foreach ($datas as $data) {
        $test = explode('=', $data);
        $result[$test[0]] = $test[1];
      }


      $result = (object) $result;
      $result->cancelId = $cancelId;

      if (empty($result->car_brand)) $errors['car_brand'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet auto marku!</li>';
      if (empty($result->car_model)) $errors['car_model'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet auto modeli!</li>';
      if (empty($result->lic_plate)) $errors['lic_plate'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet auto reģistrācijas numuru!</li>';
      if ($result->service === 'undefined') $errors['service'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Jāizvēlas viens no pakalpojumiem!</li>';

      if ($result->service == 1 && $result->rimsWith === 'undefined') $errors['rimsWith'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Jāizvēlas viena no opcijām!</li>';

      if (!empty($result->email)) {
        if (!filter_var($result->email, FILTER_VALIDATE_EMAIL)) {
          $errors['email'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet pareizu epasta adresi!</li>';
        }
      }

      if (empty($result->phone_number)) {
        $errors['phone_number'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet telefona numuru!</li>';
      } else {
        if (!is_numeric($result->phone_number)) {
          $errors['phone_number'] = '<li class="w-full text-red-700 px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">Ievadiet pareizu telefona numuru!</li>';
        }
      }

      if (!empty($errors)) return json_encode(['success' => false, 'errors' => $errors]);

      $slot = Slot::where('date', $dopParams['date'])->where('queue_id', $dopParams['queue_id'])->where('iorder', $dopParams['iorder'])->first();

      if (!$slot) {
        $slot = new Slot;
        $slot->status = 1;
      } else if ($slot && !empty($slot->comment) && !empty($slot->takenby)) {
        Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id,0, 'Neizdevās izveidot pierakstu', $slot);
        return json_encode(['success' => false, 'alertMessage' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!', 'finished' => false]);
      } else if ($slot && !empty($slot->takenby)) {
        Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id,0, 'Neizdevās izveidot pierakstu', $slot);
        return json_encode(['success' => false, 'alertMessage' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!', 'finished' => false]);
      } else if ($slot && !empty($slot->comment)) {
        $slot->status = 1;
      } else {
        $slot->status = 1;
      }

      $slot->timestamps = false;
      $slot->queue_id = $dopParams['queue_id'];
      $slot->date = $dopParams['date'];
      $slot->iorder = $dopParams['iorder'];
      $slot->takenby = json_encode($result);
      $slot->createtime = date('Y-m-d H:i:s');
      $slot->createuser = $userID;
      if ($request->input('from_mobile')) {
        $slot->is_mobile = $request->input('from_mobile');
      }

      if ($slot->save()) {
        $returnMessage = 'Paldies par pierakstu<br>Jūsu pieraksts ir piereģistrēts. Gaidīsim jūs <b>'.$dayOfWeek2.', '.$fmtDate.' '.$time.' riepu servisā '.$office->title.'!</b><br><br>Pieraksta atcelšanas saite ir pieejama īsziņā.';
        Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Izveidots jauns pieraksts', $slot);
      }

      $queue = Queue::where('queue_id', $slot->queue_id)->first();

      $smsText = $queue->parseNotification($queue->getOriginal()['notificationScheduleSMS'], $slot->date, $slot->iorder, $result, $time);

      if ($result->email) {
        $mailText = $queue->parseNotification($queue->getOriginal()['notificationEmail'], $slot->date, $slot->iorder, $result, $time);
        //        Mail::to($form->ownerEmail)->send(new \App\Mail\Mail($mailText));
        $mailer = new Mailer();
        $mailer->addRecipient($result->email);
        $bcc = 'karlis@r1riepas.lv';
        if ($bcc) $mailer->addBCC($bcc);
        $mailer->subject = $queue->parseNotification($queue->getOriginal()['notificationSubject'], $slot->date, $slot->iorder, $result, $time);
        $mailer->message = $mailText;
        $mailer->send();
      }

      (new SmsSender)->sendSchedule((array) $result, $smsText, $slot);

      //The URLs that we want to send cURL requests to.
      $urls = [];

      if ($today == $slot->date && $this->now >= $this->startSendWpp && $this->now < $this->endSendWpp) {
        $service = Service::where('service_id', $result->service)->first();
        $vehicle = str_replace(' ', '%20', $result->car_brand);
        $userComment = (!empty($result->user_comment)) ? '%20|%20Piezīmes%20-%20' . str_replace(' ', '%20', $result->user_comment) : '';
        $model = str_replace(' ', '%20', $result->car_model);
        $service = str_replace(' ', '%20', $service->pdf_title);
        $vehiclePlate = str_replace(' ', '%20', $result->lic_plate);
        $discount = str_replace(' ', '%20', $slot->comment);
        $discount = (!empty($slot->comment)) ? '%20|%20(' . $discount . ')' : '';

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
          $urls[] = 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment . $discount;
        } else {
          $urls[] = 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Jauns%20pieraksts%20-%20' . $time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate . '%20|%20Pakalpojums%20-%20' . $service . $append . $userComment . $discount;
        }
      }

      if ($result->service == 3) {
        $userComment = (!empty($result->user_comment)) ? ',%20' . str_replace(' ', '%20', $result->user_comment) : '';
        $urls[] = 'http://api.textmebot.com/send.php?recipient=' . $this->orderWpp . '&apikey=d6nsRWNp1xpc&text=' . $fmtDate . '%20' . $time . ',%20' . $result->phone_number . $userComment;
      }

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

      return json_encode(['success' => true, 'message' => $returnMessage, 'new_slot_client' => true]);
    }

    public function showMobileQueues(Request $request)
    {

      $this->loadWorkingDays();
      $office = Office::where('office_id', $request->office_id)->first();

      $visibleDays = 7;
      $daysToShow = [];

      for ($i = 0; $i <= $visibleDays; $i++) {
        array_push($daysToShow, Date('Y-m-d', strtotime('+' . $i . ' days')));
      }

      $workingDays = Workingday::whereIn('date', $daysToShow)->where('office_id', $office->office_id)->get();

      $dayTitles = $this->dayTitles;
      $timeStep = $this->timeStep;

      $html = '';
      $slots = [];
      $today = date('Y-m-d');

      for ($day = 0; $day <= $visibleDays; $day++) {
        $date = Date('d.m.Y', strtotime('+' . $day . ' days'));
        $dayOfWeek = $dayTitles[date('N', strtotime($date.' 00:00:00'))];

        $html .= '<div class="row" style="margin-left: -8px; margin-bottom: 10px; margin-top: 10px;"><div class="col-sm-6" style=""><span class="day-title">' . $office->title . '<br>' . $dayOfWeek . ' ' . $date . '</span></div><div class="col-sm-6"><button class="btn status-toggle btn-primary" style="text-align: center;border-radius: 5px;width: 100%;height: 42px;display: flex;font-size: 12px;justify-content: center;align-items: center;">Rādīt tikai brīvos laikus</button></div></div>';
        // grid-template-columns: repeat(' . $office->queue_count . ', 1fr)">
        $html .= '<div class="time-list"  data-date="' . date('Y-m-d', strtotime($date)) . '" style="margin-left: 8px;">';
        foreach ($workingDays as $workingDay) {
          if ($workingDay->date == Date('Y-m-d', strtotime('+' . $day . ' days'))) {
            $openTime1 = Workingday::select('timeopen')->where('date', $workingDay->date)->orderBy('timeopen', 'ASC')->first();
            $opentime = Carbon::parse($workingDay->timeopen);
            $openTime1 = Carbon::parse($openTime1->timeopen);
            $closetime = Carbon::parse($workingDay->timeclose)->subMinutes($timeStep);

            $numberOfSteps = ceil($opentime->diffInMinutes($closetime) / $timeStep);

            $workingOffice = Office::where('office_id', $workingDay->office_id)->first();

            if ($workingOffice->office_id == $office->office_id) {
              if ($workingDay->weekday != 7) {
                if ($workingDay->is_opened == 1) {
                  foreach (range(($opentime->diffInMinutes($closetime) / $timeStep - $openTime1->diffInMinutes($closetime) / $timeStep), $numberOfSteps) as $i) {
                    $currentTime = $opentime->copy()->addMinutes($timeStep * $i)->format('H:i');
                    $oddMinutes = (Carbon::parse($currentTime)->format('i') % 2) === 0;
                    $slot = Slot::where('queue_id', $workingDay->queue_id)->where('date', $workingDay->date)->where('iorder', $i)->first();
                    $service = null;
                    $content = '';

                    $free_slot_content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                      . '<div class="available active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">Brīvs</span></div>'
                      . '</div>';

                    $taken_slot_content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                      . '<div class="unavailable slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">Aizņemts</span></div>'
                      . '</div>';

                    $closed_slot_content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                      . '<div class="unavailable slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">Slēgts</span></div>'
                      . '</div>';

                    $ac_slot_content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                      . '<div class="available conditioner active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">AC Uzpilde</span></div>'
                      . '</div>';

                    $moto_slot_content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                      . '<div class="available moto active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">Moto montāža</span></div>'
                      . '</div>';

                    if ($slot) {
                      switch ($slot->status) {
                        case SLOT_STATUS_FREE:
                          if ($workingDay->is_half) {
                            $service = $oddMinutes ? Service::where('f_ac', 1)->first() : Service::where('f_moto', 1)->first();
                          }

                          // Modify content for AC and moto slots if today and currently free
                          if ($workingDay->date == $today) {
                            if (Carbon::parse($currentTime)->subMinutes(30) >= Carbon::now()) {
                              $content = $free_slot_content;
                              if ($service && ($service->f_ac || $service->f_moto)) {
                                $content = $oddMinutes ? $ac_slot_content : $moto_slot_content;
                                if (!is_null($slot->comment) && is_null($slot->takenby)) {
                                  $content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                                    . '<div class="available discount active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">' . $slot->comment . '</span></div>'
                                    . '</div>';
                                }
                              } else if (!is_null($slot->comment) && is_null($slot->takenby)) {
                                $content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                                  . '<div class="available discount active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">' . $slot->comment . '</span></div>'
                                  . '</div>';
                              }
                            }
                          } else {
                            $content = $free_slot_content;
                            if (!is_null($slot->comment) && is_null($slot->takenby)) {
                              $content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                                . '<div class="available discount active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">' . $slot->comment . '</span></div>'
                                . '</div>';
                            }
                          }
                          break;
                        case SLOT_STATUS_OFFER:
                          $content = '<div data-queue-id="' . $workingDay->queue_id . '" data-iorder="' . $i . '" class="time-slot">'
                            . '<div class="available discount active slot"><span class="time-span">' . $currentTime . '</span><br><span class="slot-text">' . $slot->comment . '</span></div>'
                            . '</div>';
                          break;
                        case SLOT_STATUS_TAKEN:
                          $content = $taken_slot_content;
                          break;
                        case SLOT_STATUS_CLOSED:
                          $content = $closed_slot_content;
                          break;
                      }
                    } else {
                      if ($workingDay->date == $today) {
                        if (Carbon::parse($currentTime)->subMinutes(30) >= Carbon::now()) {
                          // Modify content for AC and moto slots if today and within the hour
                          if ($workingDay->is_half) {
                            $service = $oddMinutes ? Service::where('f_ac', 1)->where('enabled', 1)->first() : Service::where('f_moto', 1)->where('enabled', 1)->first();
                            if (!$service && ($i % 2 == 1)) $free_slot_content = $taken_slot_content;
                          }

                          $content = $service && ($service->f_ac || $service->f_moto) ? ($oddMinutes ? $ac_slot_content : $moto_slot_content) : $free_slot_content;
                        } else {
                          $content = $taken_slot_content;
                        }
                      } else {
                        if ($workingDay->is_half) {
                          $service = $oddMinutes ? Service::where('f_ac', 1)->where('enabled', 1)->first() : Service::where('f_moto', 1)->where('enabled', 1)->first();
                          if (!$service && ($i % 2 == 1)) $free_slot_content = $taken_slot_content;
                        }

                        $content = $service ? ($oddMinutes ? $ac_slot_content : $moto_slot_content) : $free_slot_content;
                      }
                    }
                    $slots[$workingDay->date][] = ['content' => $content, 'queue_id' => $workingDay->queue_id, 'iorder' => $i, 'time' => $currentTime];
                  }
                }
              } else {
                $slots[$workingDay->date] = [
                  0 => [
                    'content' => '<div class="time-slot closed">'
                      . '<div class="unavailable slot"><span class="slot-text">Slēgts</span></div>'
                      . '</div>',
                    'queue_id' => 1,
                    'iorder' => 0,
                    'time' => '',
                  ],
                  1 => [
                    'content' => '<div class="time-slot closed">'
                      . '<div class="unavailable slot"><span class="slot-text">Slēgts</span></div>'
                      . '</div>',
                    'queue_id' => 1,
                    'iorder' => 1,
                    'time' => '',
                  ],
                  2 => [
                    'content' => '<div class="time-slot closed">'
                      . '<div class="unavailable slot"><span class="slot-text">Slēgts</span></div>'
                      . '</div>',
                    'queue_id' => 1,
                    'iorder' => 2,
                    'time' => '',
                  ],
                  3 => [
                    'content' => '<div class="time-slot closed">'
                      . '<div class="unavailable slot"><span class="slot-text">Slēgts</span></div>'
                      . '</div>',
                    'queue_id' => 1,
                    'iorder' => 3,
                    'time' => '',
                  ]
                ];
              }
            }
          }

        }

        try {
          $slots[$daysToShow[$day]] = array_filter($slots[$daysToShow[$day]], function ($slot) {
            return $slot['iorder'] >= 0;
          });

          // Now, sort the remaining slots
          usort($slots[$daysToShow[$day]], function ($a, $b) {
            $queueComparison = strcmp($a['time'], $b['time']);

            if ($queueComparison == 0) {
              return $a['queue_id'] - $b['queue_id'];
            }

            return $queueComparison;
          });

          foreach ($slots[$daysToShow[$day]] as $slot) {
            $html .= $slot['content'];
          }
        } catch (\Exception $e) {
          $html .= '</div>';
        }
        $html .= '</div>';
      }

      return $html;
    }

    public function reservations(Request $request) {

      $date = $request->date;
      $visibleDays = 14;

      if ($date == null) {
        $date = date('Y-m-d');
      }
      $currentDate = strtotime($date);

      $isEqual = NewWorkingDay::where('date', '>=', date('Y-m-d'))->get()->diffAssoc(WorkingDay::where('date', '>=', date('Y-m-d'))->get())->isEmpty();

      //        dd(NewWorkingDay::all()->diffAssoc(WorkingDay::all()), WorkingDay::all()->diffAssoc(NewWorkingDay::all()));

      $this->loadWorkingDays();

      $daysToShow = [];

      for ($i = 0; $i <= $visibleDays; $i++) {
        array_push($daysToShow, Date('Y-m-d', strtotime('+' . $i . ' days')));
      }

      $workingDays = NewWorkingday::whereIn('date', $daysToShow)->get();

      if (isset($request->date)) {
        $workingDays = NewWorkingday::where('date', $date)->get();
        $daysToShow[] = $date;
        $visibleDays = 0;
        array_pop($daysToShow);
      }

      $from = $daysToShow[0];
      $to = end($daysToShow);

      $start = Carbon::createFromTimeString($this->startTime);
      $end = Carbon::createFromTimeString($this->closeTime)->subMinutes($this->timeStep);
      //        $start = Carbon::createFromTime(9, 0, 0); // Set the start time to 09:00
      //        $end = Carbon::createFromTime(19, 0, 0); // Set the end time to 19:00
      $interval = CarbonInterval::minutes($this->timeStep); // Set the interval to 20 minutes

      $dateRanges = CarbonPeriod::create($from, $to);
      $timeRanges = CarbonPeriod::create($start, $interval, $end);

      $dayTitles = $this->dayTitles;
      $timeStep = $this->timeStep;

      return view('records.reservation', compact('workingDays', 'visibleDays', 'dayTitles', 'dateRanges', 'timeRanges', 'isEqual', 'currentDate', 'timeStep'));
    }

    public function reservations_print($office_id, $date)
    {

      $this->loadWorkingDays();
      $office = Office::findOrFail($office_id);
      $office->loadQueues();

      $spreadsheet = new Spreadsheet();

      $sheet = $spreadsheet->getActiveSheet();
      if ($office->office_id == 1) {
        $sheet->setTitle($date . ' Ulbroka');
      } else {
        $sheet->setTitle($date . ' Kalnciema iela');
      }

      $a = 1;
      $b = 2;

      $slots = [];

      $workingDays = Workingday::where('date', $date)->where('office_id', $office->office_id)->get();

      foreach ($workingDays as $workingDay) {
        if ($a > 1) break;
        if ($workingDay->is_opened) {
          $openTime1 = Workingday::select('timeopen')->where('date', $workingDay->date)->orderBy('timeopen', 'ASC')->first();
          $opentime = Carbon::parse($workingDay->timeopen);
          $openTime1 = Carbon::parse($openTime1->timeopen);
          $closetime = Carbon::parse($workingDay->timeclose)->subMinutes($this->timeStep);

          $numberOfSteps = ceil($opentime->diffInMinutes($closetime) / $this->timeStep);

          foreach (range(($opentime->diffInMinutes($closetime) / $this->timeStep - $openTime1->diffInMinutes($closetime) / $this->timeStep), $numberOfSteps) as $i) {
            $slotNumber = $i;
            $currentTime = $opentime->copy()->addMinutes($this->timeStep * $i)->format('H:i');
            $slot = Slot::where('date', $date)->where('queue_id', $workingDay->queue_id)->where('iorder', $slotNumber)->first();

            $slots[] = ['content' => $slot, 'queue_id' => $workingDay->queue_id, 'iorder' => $i, 'time' => $currentTime];

          }
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
      try {
        $slots = array_filter($slots, function ($slot) {
          return $slot['iorder'] >= 0;
        });

        // Now, sort the remaining slots
        usort($slots, function ($a, $b) {
          $queueComparison = strcmp($a['time'], $b['time']);

          if ($queueComparison == 0) {
            return $a['queue_id'] - $b['queue_id'];
          }

          return $queueComparison;
        });

        foreach ($slots as $slot) {
          $vars = ['takenBy', 'device', 'phone', 'storageBin', 'createduser', 'editeduser', 'created', 'edited', 'lastAction', 'lastAction', 'purpose', 'slotText'];
          foreach ($vars as $var) $$var = null;
          $currentTime = $slot['time'];
          $queue_id = $slot['queue_id'];
          $slot = $slot['content'];
          if (!is_null($slot)) {
            $takenBy = json_decode($slot->takenby);
            $device = ($slot->is_mobile == 1) ? 'Mobīlā ierīce' : 'Dators';
            $phone = (isset($takenBy->phone_number)) ? $takenBy->phone_number : '';
            $storageBin = (isset($takenBy->temp_nr) && !empty($takenBy->temp_nr)) ? $takenBy->temp_nr : '';
            $createduser = User::where('id', $slot->createuser)->first();
            $editeduser = User::where('id', $slot->edituser)->first();
            $created = ($createduser) ? $createduser->fullName : 'Apmeklētājs';
            $edited = ($editeduser) ? $editeduser->fullName : '';
            $lastAction = (is_null($slot->edittime)) ? $slot->createtime : $slot->edittime;
            $lastAction = (is_null($lastAction)) ? '' : $lastAction;
            if (!isset($takenBy->car_brand) && !isset($takenBy->car_model)) {
              $purpose = '';
              $slotText = '';
              if (!is_null($slot->comment)) {
                $slotText = $slot->comment;
                $discount = true;
              }
            } else {
              if (!isset($takenBy->service)) {
                $purpose = '';
                $slotText = $takenBy->car_brand . ' ' . $takenBy->car_model . ' // ' . $takenBy->lic_plate . ' ' . $takenBy->name . ' ' . $takenBy->user_comment . ' ' . $slot->comment;
              } else {
                $service = Service::where('service_id', $takenBy->service)->first();
                if (!empty($takenBy->rimsWith)) {
                  if ($takenBy->rimsWith != 'undefined') {
                    if ($takenBy->rimsWith == 1) {
                      $rimsWith = ' - Riepas bez diskiem';
                    } else {
                      $rimsWith = ' - Riepas ar diskiem';
                    }
                  } else {
                    $rimsWith = '';
                  }
                  $purpose = $service->pdf_title . $rimsWith;
                } else {
                  $purpose = $service->pdf_title;
                }
                $slotText = $takenBy->car_brand . ' ' . $takenBy->car_model . ' // ' . $takenBy->lic_plate . ' ' . $takenBy->name . ' ' . $takenBy->user_comment . ' ' . $slot->comment;
              }
            }
          }
          $sheet->setCellValue('A' . $b, $currentTime);
          $sheet->setCellValue('B' . $b, $queue_id);
          $sheet->setCellValue('C' . $b, @$device);
          $sheet->setCellValue('D' . $b, @$phone);
          $sheet->setCellValue('E' . $b, @$purpose);
          $sheet->setCellValue('F' . $b, @$storageBin);
          $sheet->setCellValue('G' . $b, @$slotText);
          $sheet->setCellValue('H' . $b, @$created);
          $sheet->setCellValue('I' . $b, @$edited);
          $sheet->setCellValue('J' . $b, @$lastAction);
//          if (isset($discount) && $discount === true) {
//            $spreadsheet
//              ->getActiveSheet()
//              ->getStyle("A$b:J$b")
//              ->getFill()
//              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//              ->getStartColor()
//              ->setARGB('ffc000');
//            $spreadsheet
//              ->getActiveSheet()
//              ->getStyle("A$b:J$b")
//              ->getBorders()
//              ->getVertical()
//              ->setBorderStyle(Border::BORDER_THIN)
//              ->setColor(new Color('DDD9C4'));
//            $discount = false;
//          }
          $b++;
        }
      } catch (\Exception $e) {
        dd($e->getMessage(), $e->getLine());
      }
//      die;
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
      $sheet->getColumnDimension('F')->setWidth(12);
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

    public function getTimes(Request $request)
    {
      $workingDay = NewWorkingDay::where('date', $request->date)->where('queue_id', $request->queue_id)->first();
      $timeopen = Carbon::createFromTimeString($workingDay->timeopen)->format('H:i');
      $timeclose = Carbon::createFromTimeString($workingDay->timeclose)->format('H:i');
      $queue = Queue::where('queue_id', $request->queue_id)->first()->title;
      $_weekDay = $workingDay->weekday;
      $timeStep = $workingDay->timeStep;

      $is_half = ($workingDay->is_half === 1) ? 1 : 0;

      return json_encode(['timeopen' => $timeopen, 'timeclose' => $timeclose, 'title' => $queue, 'timeStep' => $timeStep, 'is_half' => $is_half, 'weekday' => $_weekDay]);
    }

    public function cancelTimeChanges()
    {
      $equals = WorkingDay::where('date', '>=', date('Y-m-d'))->get()->diffAssoc(NewWorkingDay::where('date', '>=', date('Y-m-d'))->get());

      foreach ($equals as $equal) {
        $workingDay = NewWorkingDay::where('workingday_id', $equal->workingday_id)->first();
        $workingDay->queue_id = $equal->queue_id;
        $workingDay->office_id = $equal->office_id;
        $workingDay->date = $equal->date;
        $workingDay->weekday = $equal->weekday;
        $workingDay->timeopen = $equal->timeopen;
        $workingDay->timeclose = $equal->timeclose;
        $workingDay->is_half = $equal->is_half;
        $workingDay->is_opened = $equal->is_opened;
        $workingDay->save();
      }

      return json_encode(['success' => true]);
    }

    public function changeTime(Request $request)
    {
      $item = (object) $request->input('times');

      if ($item->newOpenTime > $item->newCloseTime) {
        return json_encode(['message' => 'Atvēršanas laiks nevar būt lielāks par aizvēršanas laiku']);
      }

      if ($item->newOpenTime == '00:00' && $item->newCloseTime == '00:00') {
        $is_opened = 0;
      } else {
        $is_opened = 1;
        if ($item->newOpenTime == $item->newCloseTime) {
          return json_encode(['message' => 'Atvēršanas un aizvēršanas laiki nevar būt vienādi']);
        }
      }

      $start = Carbon::createFromTimeString($item->newOpenTime);
      $end = Carbon::createFromTimeString($item->oldCloseTime)->subMinutes($item->timeStep);


      if ($item->changeVal == 1) {

        $current = $start;
        $iorder = 0;

        while ($current <= $end) {
          if ($current >= Carbon::createFromTimeString($item->newCloseTime) && $current <= Carbon::createFromTimeString($item->oldCloseTime)) {
            $slot = Slot::where('date', $item->date)->where('queue_id', $item->queue_id)->where('iorder', $iorder)->first();
            if ($slot) {
              return json_encode(['message' => $item->date . ' Laikā no ' . $item->newCloseTime . ' līdz ' . $item->oldCloseTime . ' ir pieraksti']);
            }
          }
          $current->addMinutes(15);
          $iorder++;
        }

        $workingDay = NewWorkingDay::where('date', $item->date)->where('queue_id', $item->queue_id)->first();

        if ($item->newOpenTime !== $item->oldOpenTime) {
          $newOpenTime = Carbon::createFromTimeString($item->newOpenTime);
          $oldOpenTime = Carbon::createFromTimeString($workingDay->timeopen);

          $newIorder = $newOpenTime->diffInMinutes($end) / $this->timeStep - $oldOpenTime->diffInMinutes($end) / $this->timeStep;

          $slots = Slot::where('date', $item->date)->where('queue_id', $item->queue_id)->get();

          foreach ($slots as $slot) {
            $slot->iorder = $slot->iorder + ($newIorder);
            $slot->save();
          }
        }

        if ($is_opened !== 0) {
          $workingDay->timeopen = $item->newOpenTime;
          $workingDay->timeclose = $item->newCloseTime;
          $workingDay->timeStep = $item->timeStep;
          $workingDay->is_half = $item->is_half;
          $workingDay->is_opened = $is_opened;
        } else {
          $workingDay->is_opened = $is_opened;
        }
        $workingDay->save();

        return json_encode(['status' => 'success']);

      } else if ($item->changeVal == 2) {

        $_weekDay = (int) date('N', strtotime($item->date));

        $workingDays = NewWorkingDay::where('date', '>=', $item->date)->where('weekday', $_weekDay)->where('queue_id', $item->queue_id)->get();

        foreach ($workingDays as $workingDay) {
          $start = Carbon::createFromTimeString($item->newOpenTime);
          $iorder = 0;
          $end = Carbon::createFromTimeString($workingDay->timeclose)->subMinutes($this->timeStep);
          for ($current = $start; $current <= $end; $current->addMinutes(15)) {
            if ($current >= Carbon::createFromTimeString($item->newCloseTime) && $current <= $end) {
              $slot = Slot::where('date', $workingDay->date)->where('queue_id', $item->queue_id)->where('iorder', $iorder)->first();
              if ($slot) {
                return json_encode(['message' => $workingDay->date . ' Laikā no ' . $item->newCloseTime . ' līdz ' . Carbon::createFromTimeString($workingDay->timeclose)->format('H:i') . ' ir pieraksti']);
              }
            }
            $iorder++;
          }

          if ($item->newOpenTime !== $item->oldOpenTime) {
            $newOpenTime = Carbon::createFromTimeString($item->newOpenTime);
            $oldOpenTime = Carbon::createFromTimeString($workingDay->timeopen);

            $newIorder = $newOpenTime->diffInMinutes($end) / $this->timeStep - $oldOpenTime->diffInMinutes($end) / $this->timeStep;

            $slots = Slot::where('date', $item->date)->where('queue_id', $item->queue_id)->get();

            foreach ($slots as $slot) {
              $slot->iorder = $slot->iorder + ($newIorder);
              $slot->save();
            }
          }

          if ($is_opened !== 0) {
            $workingDay->timeopen = $item->newOpenTime;
            $workingDay->timeclose = $item->newCloseTime;
            $workingDay->timeStep = $item->timeStep;
            $workingDay->is_half = $item->is_half;
            $workingDay->is_opened = $is_opened;
          } else {
            $workingDay->is_opened = $is_opened;
          }
          $workingDay->save();
        }

        return json_encode(['status' => 'success']);

      } else if ($item->changeVal == 3) {

        $workingDays = NewWorkingDay::where('date', '>=', $item->date)->where('weekday', '!=', 6)->where('weekday', '!=', 7)->where('queue_id', $item->queue_id)->get();

        foreach ($workingDays as $workingDay) {
          $start = Carbon::createFromTimeString($item->newOpenTime);
          $iorder = 0;
          $end = Carbon::createFromTimeString($workingDay->timeclose)->subMinutes($this->timeStep);
          for ($current = $start; $current <= $end; $current->addMinutes(15)) {
            if ($current >= Carbon::createFromTimeString($item->newCloseTime) && $current <= $end) {
              $slot = Slot::where('date', $workingDay->date)->where('queue_id', $item->queue_id)->where('iorder', $iorder)->first();
              if ($slot) {
                return json_encode(['message' => $workingDay->date . ' Laikā no ' . $item->newCloseTime . ' līdz ' . Carbon::createFromTimeString($workingDay->timeclose)->format('H:i') . ' ir pieraksti']);
              }
            }
            $iorder++;
          }

          if ($item->newOpenTime !== $item->oldOpenTime) {
            $newOpenTime = Carbon::createFromTimeString($item->newOpenTime);
            $oldOpenTime = Carbon::createFromTimeString($workingDay->timeopen);

            $newIorder = $newOpenTime->diffInMinutes($end) / $this->timeStep - $oldOpenTime->diffInMinutes($end) / $this->timeStep;

            $slots = Slot::where('date', $item->date)->where('queue_id', $item->queue_id)->get();

            foreach ($slots as $slot) {
              $slot->iorder = $slot->iorder + ($newIorder);
              $slot->save();
            }
          }

          if ($is_opened !== 0) {
            $workingDay->timeopen = $item->newOpenTime;
            $workingDay->timeclose = $item->newCloseTime;
            $workingDay->timeStep = $item->timeStep;
            $workingDay->is_half = $item->is_half;
            $workingDay->is_opened = $is_opened;
          } else {
            $workingDay->is_opened = $is_opened;
          }
          $workingDay->save();
        }

        return json_encode(['status' => 'success']);

      }
    }

    public function saveTimeChanges()
    {
      $equals = NewWorkingDay::where('date', '>=', date('Y-m-d'))->get()->diffAssoc(WorkingDay::where('date', '>=', date('Y-m-d'))->get());

      foreach ($equals as $equal) {
        $workingDay = WorkingDay::where('workingday_id', $equal->workingday_id)->first();

        if ($workingDay->timeopen !== $equal->timeopen) {
          $newOpenTime = Carbon::createFromTimeString($equal->timeopen);
          $oldOpenTime = Carbon::createFromTimeString($workingDay->timeopen);

          $end = Carbon::createFromTimeString($equal->timeclose)->subMinutes($this->timeStep);

          $newIorder = $newOpenTime->diffInMinutes($end) / $this->timeStep - $oldOpenTime->diffInMinutes($end) / $this->timeStep;

          $slots = Slot::where('date', $workingDay->date)->where('queue_id', $workingDay->queue_id)->get();

//          if ($newOpenTime > $oldOpenTime) {
//            foreach ($slots as $slot) {
//              $slot->iorder = $slot->iorder + ($newIorder);
//              $slot->save();
//            }
//          } else {
//            foreach ($slots as $slot) {
//              $slot->iorder = $slot->iorder - ($newIorder);
//              $slot->save();
//            }
//          }
        }

        $workingDay->queue_id = $equal->queue_id;
        $workingDay->office_id = $equal->office_id;
        $workingDay->date = $equal->date;
        $workingDay->weekday = $equal->weekday;
        $workingDay->timeopen = $equal->timeopen;
        $workingDay->timeclose = $equal->timeclose;
        $workingDay->timeStep = $equal->timeStep;
        $workingDay->is_half = $equal->is_half;
        $workingDay->is_opened = $equal->is_opened;
        $workingDay->save();

        $queue = Queue::where('queue_id', $workingDay->queue_id)->first();
        $queue->timestamps = false;
        if ($workingDay->weekday === 6) {
          $queue->wtimeopen = $workingDay->timeopen;
          $queue->wtimeclose = $workingDay->timeclose;
        } else {
          $queue->timeopen = $workingDay->timeopen;
          $queue->timeclose = $workingDay->timeclose;
        }
        $queue->is_visible = $workingDay->is_opened;
        $queue->save();
      }

      return json_encode(['success' => true]);
    }

    public function cancelSlot(Request $request, $id)
    {
      $slot = Slot::where('takenby', 'like', '%"cancelId":"' . $id . '"%')->first();

      $date = date('Y-m-d');
      if (!$slot) return redirect(route('pieraksts'));

      if ($slot->date < $date) return redirect(route('pieraksts'))->with('warning', 'Jūsu pieraksts vairs nav aktuāls');
//      if ($slot->date == $date && $this->timeToClose < $this->now) return redirect(route('pieraksts'))->with('warning', 'Pierakstu atcelt tiešsaistē iespējams līdz <b>8:45</b>, ja vēlaties mainīt pieraksta laiku vēlāk, zvaniet');

      $queue = Queue::where('queue_id', $slot->queue_id)->first();

      $office = Office::where('office_id', $queue->office_id)->first();
//      foreach ($office->_queues as $queue) {
//        $queue->loadWorkingDay($slot->date,false);
//      }

      $takenBy = json_decode($slot->takenby);

      $time = substr($id, -4);
      $time = $this->insertColon($time);

      $_weekDays = [
        1 => 'pirmdien',
        2 => 'otrdien',
        3 => 'trešdien',
        4 => 'ceturtdien',
        5 => 'piektdien',
        6 => 'sestdien',
        7 => 'svētdien',
      ];

      if ($request->post()) {
        if ($takenBy !== null) {

          $licPlateNr = substr($takenBy->lic_plate, -2);
          $inputPlateNr = substr($request->input('deleteNr'), -2);

          if ($licPlateNr != $inputPlateNr) {
            $errorMessage = 'Numurs ievadīts nepareizi.<br>Mēģiniet vēlreiz vai sazinieties ar mums telefoniski.';
            return view('records.cancel', compact('slot', '_weekDays', 'takenBy', 'time', 'office', 'errorMessage'));
          }

          $deletedSlot = $slot;
          if ($slot->delete()) {

            if ($takenBy->email) {
              $mailText = $queue->parseNotification($queue->getOriginal()['notificationCancelEmail'], $deletedSlot->date, $deletedSlot->iorder, $takenBy, $time);

              $mailer = new Mailer();
              $mailer->addRecipient($takenBy->email);
              $bcc = 'karlis@r1riepas.lv';
              if ($bcc) $mailer->addBCC($bcc);
              $mailer->subject = 'Tava rezervacija R1 riepu servisā ATCELTA';
              $mailer->message = $mailText;
              $mailer->send();
            }

            $smsText = $queue->parseNotification($queue->getOriginal()['notificationScheduleCancelSMS'], $deletedSlot->date, $deletedSlot->iorder, $takenBy, $time);

            (new SmsSender)->sendSchedule((array) $takenBy, $smsText, $deletedSlot);
            if ($date == $slot->date) {

              $vehicle = str_replace(' ', '%20', $takenBy->car_brand);
              $model = str_replace(' ', '%20', $takenBy->car_model);
              $vehiclePlate = str_replace(' ', '%20', $takenBy->lic_plate);

              if ($office->office_id == 1) {

                $cURLConnection = curl_init();

                curl_setopt($cURLConnection, CURLOPT_URL, 'http://api.textmebot.com/send.php?recipient=' . $this->ursWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                curl_exec($cURLConnection);

                curl_close($cURLConnection);
              } else {
                $cURLConnection = curl_init();

                curl_setopt($cURLConnection, CURLOPT_URL, 'http://api.textmebot.com/send.php?recipient=' . $this->krsWpp . '&apikey=d6nsRWNp1xpc&text=Atcelts%20pieraksts%20-%20' . $time . '%20|%20' . $vehicle . '%20' . $model . '%20|%20' . $vehiclePlate);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                curl_exec($cURLConnection);

                curl_close($cURLConnection);
              }
            }

            Audit::audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, $deletedSlot->slot_id, 0, 'Atcelts pieraksts', $deletedSlot);
            return redirect(route('pieraksts'))->with('success', 'Atcelšana ir izdevusies');
          } else {
            Audit::audit(AUDIT_SEVERITY_WARNING, AUDIT_FACILITY_MESSAGE, $slot->slot_id, 0, 'Neizdevās atcelt pierakstu', $slot);
            return redirect(route('pieraksts'))->with('danger', 'Notikusi kļūda');
          }
        }
      }

      return view('records.cancel', compact('slot', '_weekDays', 'takenBy', 'time', 'office'));
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

    /**
     *
     * @param string $text Saīsināmais teksts
     * @param type $limit Maksimālais simbolu skaits tekstā
     * @param type $ellipsis Ar ko aizstāt maksimālo simbolu skaitu
     * @param type $strip Par cik saīsināt tekstu, ja pārsniegts maksimālais simbolu skaits (noklusētais = 0)
     * @return string
     */
    public static function truncateCharacters($text,$limit,$ellipsis='...',$strip=0){
      if(strlen($text) > $limit) $text = trim(substr($text, 0, $limit-$strip)).$ellipsis;
      return $text;
    }

    public function insertColon($number)
    {
      // Get the length of the string.
      $length = strlen($number);

      // If the length of the string is less than 3, then there is no need to insert a colon.
      if ($length < 3) {
        return false;
      } else {
        // Insert a colon at the second character of the string.
        return substr($number, 0, 2) . ":" . substr($number, 2);
      }
    }

  }

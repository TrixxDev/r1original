<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Workingday;
use App\Models\Service;
use App\Models\Office;
use App\Helper\Tires;
use App\Models\Queue;
use App\Models\Slot;
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

      $services = Service::orderBy('service_id', 'DESC')->get();

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
        $comment = $request->comment;
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;

        $errorText = [];
        if (!$car) $errorText['brand'] = "Jābūt aizpildītam!\n";
        if (!$carModel) $errorText['model'] = "Jābūt aizpildītam!\n";

        if (!$purpose) $errorText['purpose'] = "Laukam \"Es vēlos\" jābūt aizpildītam!\n";
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

        $form = json_encode(
            [
                'vehicleMake' => $car,
                'vehicleModel' => $carModel,
                'vehiclePlate' => $licPlate,
                'purpose' => $purpose,
                'storageBin' => '',
                'comment' => $comment,
                'ownerName' => $name,
                'ownerPhone' => $phone,
                'ownerEmail' => $email
            ]
        );

        $queue = Queue::where('queue_id', $queue_id)->first();
        $office = Office::where('office_id', $queue->office_id)->first();

        $queue->loadWorkingDay($date);
        $queue->loadSlots($date, true);

        $time = Queue::timeByInterval($queue->getSlotStartInterval($date,$slotNumber),true);
        $fmtDate = date('d.m.Y',strtotime($date));
        $dayOfWeek2 = $_weekDays2[date('N', strtotime($date.' 00:00:00'))];

        if (0==1) $slot = new Slot();
        $slot = $queue->_slots[$date][$slotNumber];
        $slot = Slot::findOrFail($slot->slot_id);

        $slot->timestamps = false;

        $slot->takenby = $form;
        $slot->status = 1;

        $slot->createTime = date('Y-m-d H:i:s');
        $slot->createUser = $userID;
        $slot->editTime = date('Y-m-d H:i:s');
        $slot->editUser = $userID;

        $slot->save();

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

        $form = json_encode(
            [
                'vehicleMake' => $car,
                'vehicleModel' => $carModel,
                'vehiclePlate' => $licPlate,
                'purpose' => $purpose,
                'storageBin' => '',
                'comment' => $comment,
                'ownerName' => $name,
                'ownerPhone' => $phone,
                'ownerEmail' => $email
            ]
        );

        $slot = Slot::findOrFail($slot_id);

        $slot->timestamps = false;

        $slot->takenby = $form;
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

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Tires;

class Queue extends Model
{

    protected $primaryKey = 'queue_id';

    public $_slots;		// ielādētie sloti, Slots tipa objektu saraksts
    public $_workingDays;

    public function __construct()
    {
        $this->_slots = [];				// ielādētie sloti, CQueueSlot tipa objektu saraksts
        $this->_workingDays = [];		// ielādētie darba laiki, CWorkingDay tipa objektu saraksts
    }

    public function isVisible($date){
      $day=$this->_workingDays[$date];
      return ($day->is_visible)!=0;
//      return ($day->isVisible());
    }

    public function loadWorkingDay($date,$allowCreate=false){
      $id = $this->queue_id;
      $workingday = Workingday::where('queue_id', $id)->where('date', $date)->get();

      if (count($workingday) > 0) {
        $day = $workingday[0];
      } else {
        $day = new Workingday();
        $day->timestamps = false;
        $day->queue_id = $this->queue_id;
        $day->date = $date;

        if (!$day->fillWorkingHours()){
          $day->openTime = $this->opentime;
          $day->closeTime = $this->closetime;
          $day->visible = $this->is_visible;
        } else {
        }

        if ($allowCreate) $day->save();
      }
      $this->_workingDays[$date] = $day;
      return $day;
    }

    public function loadSlots($date,$allowCreate=false){
      //$query = new CQuery();
      $list = Slot::where('queue_id', $this->queue_id)->where('date', $date)->get();

      //$this->_slots = array();
      //$slotCount = ceil(1440 / $this->slotSize);
      $slotCount = $this->getWorkingDayLength($date);

      foreach ($list as $object){
        $this->_slots[$date][$object->iorder] = $object;
      }


      //$takenBy = json_encode(new CBookingForm());
      $takenBy = '';
      for ($i = 0; $i<$slotCount;$i++){
        if (!isset($this->_slots[$date][$i])) {
          $slot = new Slot;
          $slot->timestamps = false;
          $slot->queue_id = $this->queue_id;
          $slot->date = $date;
          $slot->iorder = $i;
          $slot->status = 0;
          $slot->status2 = 0;
          $slot->takenBy = $takenBy;
          $slot->takenBy2 = $takenBy;
          if ($allowCreate) $slot->save();
          $this->_slots[$date][$i] = $slot;
        }
      }

    }

    public function getSlots($date){
      $_queues = [];
      $queues = Queue::select('queue_id')->where('office_id', $this->office_id)->get();
      foreach ($queues as $queue) {
        $_queues[] = $queue->queue_id;
      }
      $_queues = array_reverse($_queues);
      $list = Slot::where('date', $date)->where('status', 0)->whereIn('queue_id', $_queues)->orderBy('queue_id', 'DESC')->get();

      foreach ($list as $object){
        $this->_slots[$date][$object->slot_id] = $object;
      }

      $takenBy = '';
      foreach ($this->_slots[$date] as $key => $i) {
        if(!isset($this->_slots[$date][$i->slot_id])) {
          $slot = new Slot();
          $slot->timestamps = false;
          $slot->queue_id = $this->queue_id;
          $slot->date = $date;
          $slot->iorder = $key;
          $slot->status = '';
          $slot->status2 = '';
          $slot->takenBy = $takenBy;
          $slot->takenBy2 = $takenBy;
          $this->_slots[$date][$i->slot_id] = $slot;
        }
      }

    }

    public function loadWeekDays($startdate,$days=false){
      $id = $this->queue_id;
      $date = $startdate;

      if ($days===false){
        $workingDayList = Workingday::where('queue_id', $id)->where('date', '>=', $date)->where('weekday', date('N', strtotime($startdate.' 00:00:00')))->get();
      } else {
        $workingDayList = Workingday::where('queue_id', $id)->where('date', '>=', $date)->whereIn('weekday', $days)->get();
      }


      foreach ($workingDayList as $workingDay){
        $this->_workingDays[$workingDay->date] = $workingDay;
      }
    }

    public static function intervalByTime($time){
        if (strpos($time, ':') !== false){
            list($hours, $minutes, $seconds) = explode(':', $time);

            $minutes = $hours * 60 + $minutes;
            $slotNum = floor($minutes / 10);

            return $slotNum;

        }

    }

    public static function timeByInterval($iorder,$padding=true){
        $minutes = $iorder * 10;
        $hours = floor($minutes / 60);
        $minutes = $minutes - ($hours*60);
        if ($padding){
            $time = Tires::zero_pad($hours,2).':'.Tires::zero_pad($minutes,2);
        } else {
            $time = $hours.':'.Tires::zero_pad($minutes,2);
        }
        return $time;
    }

    public function getOpenTime($date){
        $day=$this->_workingDays[$date];
        return $day['opentime'];
    }

    public function getCloseTime($date){
        $day=$this->_workingDays[$date];
        return $day['closetime'];
    }

    public function getWorkingDayLength($date){
        $start = self::intervalByTime($this->getOpenTime($date));
        $end = self::intervalByTime($this->getCloseTime($date));

        if ($start === NULL) return true;
        if ($end === NULL) return true;

        $length = floor(($end-$start) / $this->_workingDays[$date]['slotSize']);
        return $length;
    }

    public function getSlotNumberByInterval($date,$interval){
        $day = $this->_workingDays[$date];
        $start = self::intervalByTime($day['opentime']);
        $interval = $interval - $start;
        if ($interval < 0) return false;
        $slotNum = floor($interval / $this->_workingDays[$date]->slotSize);
        if ($slotNum < 0) return false;
        if ($slotNum >= $this->getWorkingDayLength($date)) return false;
        return $slotNum;
    }

    public function getSlotStartInterval($date,$slotNumber){
        $day = $this->_workingDays[$date];
        $start = self::intervalByTime($day['opentime']);
        return $start + $slotNumber * $this->_workingDays[$date]->slotSize;
    }

    public static function getSlotTime($date, $slotNumber) {
        $time = new Queue();
        return $time->getSlotStartInterval($date, $slotNumber);
    }

    function getSlotStartTime($date,$slotNumber,$padding=true){
      $startTime = $this->getSlotStartInterval($date, $slotNumber);
      return self::timeByInterval($startTime,$padding);
    }

    function getSlotEndTime($date,$slotNumber,$padding=true){
      $startTime = $this->getSlotStartInterval($date, $slotNumber)+$this->_workingDays[$date]->slotSize;
      return self::timeByInterval($startTime,$padding);
    }

    public function isIntervalBeginning($date,$interval){
        $slotNum = $this->getSlotNumberByInterval($date,$interval);
        return $this->getSlotStartInterval($date,$slotNum) == $interval;
    }

    function moveSlots($date,$delta=0){
      //$query = new CQuery();
//      $list = new CList('CQueueSlot');
//      $list->orderFields = array('iorder ASC');
//      $list->loadByCustomWhere("WHERE queue_id = '{$this->id}' AND date = '{$date}'");

      $list = Slot::where('queue_id', $this->queue_id)->where('date', $date)->orderBy('iorder', 'ASC')->get();

      foreach ($list as $object){
        $object->iorder+=$delta;
        $object->timestamps = false;
        $object->save();
      }
    }

}

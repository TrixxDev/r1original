<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workingday extends Model
{

    protected $primaryKey = 'workingday_id';

    public function fillWorkingHours(){
      // pagaidām neko nedara...
      $this->weekday = date('N', strtotime($this->date.' 00:00:00'));
      $id = $this->queue_id;
      $d = $this->date;

      $weekday = $this->weekday;
      $secondaryAvailable = $this->secondaryAvailable;
      $slotSize = $this->slotSize;

      $workingDayList = Self::where('queue_id', $id)->where('date', '<', $d)->where('weekday', $weekday)->first();

      if ($workingDayList !== NULL){
        $workingDay = $workingDayList;
        $this->openTime = $workingDay->opentime;
        $this->closeTime = $workingDay->closetime;
        $this->is_visible = $workingDay->is_visible;
        return true;
      } else {
        return false;
      }
    }

    public function isVisible(){
      return ($this->toArray()['is_visible']!=0) && ($this->opentime!=$this->closetime);
    }

}

<?php

namespace App\Http\Controllers\Admin\Records;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Queue;
use App\Models\Workingday;
use Illuminate\Http\Request;

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
        $queue->loadSlots($date,false);
        $workingDays[] = $date;
      }
    }

    return view('admin.records.index', compact('offices', 'date', '_weekDays', 'currentDate'));

  }

  public function queue_ajax(Request $request)
  {

    if ($request->method() === 'POST') {

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

            $oldOpenTime = $workingDay->opentime;
            $slotTimeDelta = (Queue::intervalByTime($oldOpenTime) - Queue::intervalByTime($f_opentime)) / $workingDay->slotSize;

            $workingDay->opentime = $f_opentime;
            $workingDay->closetime = $f_closetime;
            if ($f_rows == 1) {
              $workingDay->secondaryAvailable = 0;
              $workingDay->slotSize = 2;
            } else {
              $workingDay->secondaryAvailable = 1;
              $workingDay->slotSize = 4;
            }
            $workingDay->is_visible = ($f_visible)?1:0;
            $workingDay->save();

//            $queue->moveSlots($date, $slotTimeDelta);

            break;
          }
          case 2:{
            $queue->loadWorkingDay($date,true);
            $dayOfWeek = date('N', strtotime($date.' 00:00:00'));
            $queue->loadWeekDays($date,array($dayOfWeek));

            foreach ($queue->_workingDays as $workingdayDate=>$workingDay){
              if ($workingDay->weekday == $dayOfWeek){
                $oldOpenTime = $workingDay->opentime;
                $slotTimeDelta = (Queue::intervalByTime($oldOpenTime) - Queue::intervalByTime($f_opentime)) / $workingDay->slotSize;

                $workingDay->timestamps = false;
                $workingDay->opentime = $f_opentime;
                $workingDay->closetime = $f_closetime;
                if ($f_rows == 1) {
                  $workingDay->secondaryAvailable = 0;
                  $workingDay->slotSize = 2;
                } else {
                  $workingDay->secondaryAvailable = 1;
                  $workingDay->slotSize = 4;
                }
                $workingDay->is_visible = ($f_visible)?1:0;
                $workingDay->save();

                $queue->moveSlots($workingDay->date, $slotTimeDelta);
              }
            }
            break;
          }
          case 3:{
            $queue->loadWorkingDay($date,true);
            $queue->loadWeekDays($date,[1,2,3,4,5]);
            foreach ($queue->_workingDays as $workingdayDate=>$workingDay){
              if (($workingDay->weekday >= 1) && ($workingDay->weekday<=5)){
                $oldOpenTime = $workingDay->opentime;
                $slotTimeDelta = (Queue::intervalByTime($oldOpenTime) - Queue::intervalByTime($f_opentime)) / $workingDay->slotSize;
                $workingDay->timestamps = false;
                $workingDay->opentime = $f_opentime;
                $workingDay->closetime = $f_closetime;
                if ($f_rows == 1) {
                  $workingDay->secondaryAvailable = 0;
                  $workingDay->slotSize = 2;
                } else {
                  $workingDay->secondaryAvailable = 1;
                  $workingDay->slotSize = 4;
                }
                $workingDay->is_visible = ($f_visible)?1:0;
                $workingDay->save();

                $queue->moveSlots($workingDay->date, $slotTimeDelta);
              }
            }
            break;
          }
        }

        $queue->title = $f_title;
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
    dd($request->input());
  }

}

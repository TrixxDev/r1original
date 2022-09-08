<?php

namespace App\Helper;

use App\Models\Office;
use Exception;

class SmsSender {

  public function isValidPhoneNumber($phone, $normalLength = 8) {
    //izvācam atstarpes
    $phone = str_replace(' ','',$phone);
    // Izvācam visu, atskaitot +, - un .
    $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    // Remove "-" from number
    $phone_to_check = str_replace("-", "", $filtered_phone_number);
    // Check the lenght of number
    // This can be customized if you want phone number from a specific country
    if (strlen($phone_to_check) < $normalLength || strlen($phone_to_check) > ($normalLength+4)) {
      return false;
    } else {
      return $phone_to_check;
    }
  }

  public function send() {
    header("Content-type: text/html; charset=UTF-8");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");

    $officeList = Office::all();

    $date = date('Y-m-d',strtotime("+1 day"));

    echo 'Scheduling for: '.$date.'<br/>';

    $sendString = '';
    foreach ($officeList as $office){
      $office->loadQueues();

      foreach ($office->_queues as $queue){
        $queue->loadWorkingDay($date,true);
        $queue->loadSlots($date,true);
        $slotSizes[] = $queue->_workingDays[$date]->slotSize;

        foreach ($queue->_slots[$date] as $slot){
          if ($slot->status==SLOT_STATUS_TAKEN){
            $form = json_decode($slot->takenby);
            $smsText = $queue->parseNotification($queue->notificationSMS, $date, $slot->iorder, $form, false);
            $target = $this->isValidPhoneNumber($form->ownerPhone);
            echo 'SMS: '.$form->ownerPhone.' ('.$target.') :'.nl2br($smsText).'<br/>'."\n";
            if ($target){
              if ($sendString!='') $sendString.=",";
              $sendString .= '["'.$target.'","'.$smsText.'"]';
            }
          }
          if ($queue->secondaryAvailable && $slot->status2==SLOT_STATUS_TAKEN){
            $form = json_decode($slot->takenby2);
            $smsText = $queue->parseNotification($queue->notificationSMS, $date, $slot->iorder, $form, true);
            $target = $this->isValidPhoneNumber($form->ownerPhone);
            echo '*SMS: '.$form->ownerPhone.' ('.$target.') :'.$smsText.'<br/>'."\n";
            if ($target){
              if ($sendString!='') $sendString.=",";
              $sendString .= '["'.$target.'","'.$smsText.'"]';
            }
          }
        }

      }
    }
    $sendString = '['.$sendString.']';

    $object = json_decode($sendString);
//    dd($sendString, $object);

//https://traffic.sales.lv/API:0.14/

//    $sendSMS = P('reservation.sms.enabled',0);
//
//    if (!$sendSMS){
//      audit(AUDIT_SEVERITY_DEBUG, AUDIT_FACILITY_MESSAGE, 0,0, $sendString);
//      die;
//    }
//$sendString = '[[26417776, "Hello"]]';

    $postdata = http_build_query(
      array(
        'APIKey' => '867459d28d672949f49d8f6df81a67d286ea96f9',
        'Command' => 'GetSenders'
      )
    );

    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context = stream_context_create($opts);
    $result = file_get_contents('https://traffic.sales.lv/API:0.14/', false, $context);

    $data = json_decode($result);
    $error = @$data->Error;

    if ($error==''){
      $sender = $data->Senders[0];	// paļaujamies uz to, ka ir vismaz viens atļautais sūtītājs!
    } else {
      throw new Exception("SMS: Neautorizēta IP!");
    }

    if ($sender=='') throw new Exception("SMS: Nav pieejams neviens sūtītājs!");

    $postdata = http_build_query(
      array(
        'APIKey' => '867459d28d672949f49d8f6df81a67d286ea96f9',
        'Command' => 'SendMultiple',
        'Sender' => $sender,
        'Concatenated'=>'1',
        'Unicode'=>'1',
        'Content' => $sendString,
      )
    );
    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context = stream_context_create($opts);
    $result = file_get_contents('https://traffic.sales.lv/API:0.14/', false, $context);

//    audit(AUDIT_SEVERITY_DEBUG,AUDIT_FACILITY_MESSAGE,0,0,'SENT SMS: '.$result);

    dd($result);
  }

}

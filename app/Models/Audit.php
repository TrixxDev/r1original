<?php

namespace App\Models;

use http\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Audit extends Model
{

  public static function get_severity_image($severity): string
  {
    switch ($severity){
      case AUDIT_SEVERITY_CRITICAL: return 'error.gif';
      case AUDIT_SEVERITY_WARNING: return 'asterisk.gif';
      case AUDIT_SEVERITY_INFO: return 'info.gif';
      case AUDIT_SEVERITY_DEBUG: return 'debug.gif';
    }
  }

  public static function get_facility_name($id): string
  {
    switch ($id){
      case AUDIT_FACILITY_LOGIN: return 'Autorizācijas apakšsistēma';
      case AUDIT_FACILITY_DB: return 'Datubāzes apakšsistēma';
      case AUDIT_FACILITY_MESSAGE: return 'Ziņu apakšsistēma';
      case AUDIT_FACILITY_SYSCORE: return 'Sistēmas kodols';
      case AUDIT_FACILITY_USER: return 'Lietotāju apakšsistēma';

      case AUDIT_FACILITY_DOCUMENT: return 'Datu objekts';
      default: return $id;
    }
  }

  public static function audit($severity, $facility, $item, $subitem, $event, $instance = false)
  {
    $ip = @$_SERVER['REMOTE_ADDR'];

    $userId = 0;
    if (Auth::check()) {
      $userId = Auth::user()->id;
    }

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $backtrace = serialize($backtrace);

    $url = @$_SERVER['REQUEST_URI'];
    if ($url == '') {
      $url = '';
    }

    $instance_data = '';
    $instance_class = '';

    if ($instance !== false) {
      if ($instance instanceof \Throwable) {
        $backtrace = serialize($instance->getTrace());
        $instance_data = $instance->__toString();
        $instance_class = get_class($instance);
      } else {
        $instance_data = serialize($instance);
        $instance_class = get_class($instance);
      }
    }

    $data = [
      'audit_time' => NOW(),
      'audit_uid' => $userId,
      'audit_event' => addslashes($event),
      'audit_ip' => addslashes($ip),
      'audit_severity' => addslashes($severity),
      'audit_item' => addslashes($item),
      'audit_facility' => addslashes($facility),
      'audit_subitem' => addslashes($subitem),
      'audit_backtrace' => $backtrace,
      'audit_classname' => $instance_class,
      'audit_instance' => $instance_data,
      'audit_url' => addslashes($url)
    ];

    return DB::table('audits')->insert($data);
  }

}

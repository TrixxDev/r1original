<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stud extends Model
{

  protected $primaryKey = 'stud_id';

  public function getFullNameAttribute()
  {
    $tread = Studtread::where('tread_id', $this->make_id)->first();
    $brand = Studbrand::where('brand_id', $tread->brand_id)->first();

    return $brand->b_title . ' ' . $tread->t_title;
  }

  public function getShowAppAttribute()
  {

    $apps = explode(',', $this->application);

    $applications = [
      1 => 'Apaviem',
      2 => 'Kvadracikliem',
      3 => 'Motocikliem',
      4 => 'Mini traktoriem',
      5 => 'Iekrāvējiem',
      6 => 'Būvniecības tehnikai',
      7 => 'Agro tehnikai',
      8 => '4x4 visurgājēji',
    ];

    $out = '';

    foreach ($apps as $app) {
      if (isset($applications[$app])) {
        if (in_array($applications[$app], $applications)) {
          $out .= $applications[$app] . ', ';
        }
      } else {
        $out .= '<b>(Nav tāda pielietojuma)</b>';
      }
    }

    return substr($out, 0, -2);

  }

}

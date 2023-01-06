<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Studbrand;
use Illuminate\Support\Facades\Auth;

class Stud extends Model
{

  protected $primaryKey = 'stud_id';

  public static function getAllStudBrands($season = 1) {
    return Studbrand::selectRaw('auto_brands.brand_id, auto_brands.title as brand_title')
      ->join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
      ->whereRaw('auto_brands.title <> ""')
      ->where('auto_treads.season', $season)
      ->orderBy('brand_title')
      ->groupBy('auto_brands.title')
      ->get();
  }

  public function getBrandAttribute() {
    $tread = Studtread::where('tread_id', $this->make_id)->first();
    $brand = Studbrand::where('brand_id', $tread->brand_id)->first();

    return $brand->b_title;
  }

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

  public function getAvailableAttribute()
  {
    if ($this->quantity >= 1) {
      return 'Pieejams';
    } else {
      return 'Zvaniet!';
    }
  }

  public function getDotAvailableAttribute()
  {
    if ($this->quantity >= 1) {
      return 'green';
    } else {
      return 'red';
    }

  }

  public function getStockAvailabilityAttribute()
  {
    $stud = Stud::where('stud_id', $this->stud_id)->first();

    if ($stud->urs_quantity >= 1) {
      $availability = '<p>Ulbrokā: 1 un vairāk</p><br>';
    } else {
      $availability = '<p>Ulbrokā: ' . $stud->urs_quantity . '</p><br>';
    }
    if ($stud->krs_quantity >= 1) {
      $availability .= '<p>Kalnciema ielā: 1 un vairāk</p>';
    } else {
      $availability .= '<p>Kalnciema ielā: ' . $stud->krs_quantity . '</p>';
    }

    if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
      $availability = '<p>Ulbrokā: ' . $stud->urs_quantity . '</p><br>';
      $availability .= '<p>Kalnciema ielā: ' . $stud->krs_quantity . '</p>';
    } else {
      $dot = $this->getDotAvailableAttribute();
      if ($dot === 'red') {
        $availability = '<p style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</p>';
      } else if ($dot === 'yellow' || $dot === 'half-yellow') {
        $availability = '<p style="text-align: center;">Riepas pieejamas partneru noliktavās<br>Piegāde 1 darbadienas laikā.</p>';
      }
    }
    $availability .= '';

    return $availability;
  }
}

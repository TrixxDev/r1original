<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Studbrand;

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

  public static function getStudBrand($brand_id) {
    return Studbrand::select('*')->where('brand_id', $brand_id)->first();
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

  public function getStockCount()
  {
    $studs = Stud::where('stud_id', $this->stud_id)->get();

    $count=0;

    foreach ($studs as $stud) {
      if ($stud !== NULL && $stud->quantity >= 1) {
        $count += $stud->quantity;
      }
    }

    return $count;
  }

  public function getDotAvailableAttribute()
  {
    if ($this->quantity < 0 && $this->getStockCount() > 0) {
      if ($this->_includeStock) {
        $count = $this->getStockCount();
        switch ($count){
          case -1:
          case 0: {
            return 'red';
          }
          case 1:
          case 2:
          case 3: {
            return 'half-yellow';
          }
          default:{
            return 'yellow';
          }
        }
      } else {
        return 'red';
      }
    }
    switch ($this->quantity) {
      case 1:
      case 2:
      case 3: {
        return 'half-green';
      }
      case -1:
      case 0: {
        if ($this->_includeStock) {
          $count = $this->getStockCount();
          switch ($count){
            case -1:
            case 0: {
              return 'red';
            }
            case 1:
            case 2:
            case 3: {
              return 'half-yellow';
            }
            default:{
              return 'yellow';
            }
          }
        } else {
          return 'red';
        }
      }
      default: {
        return 'green';
      }
    }

  }

  public function getStockAvailabilityAttribute()
  {
    $stud = Stud::where('stud_id', $this->stud_id)->first();


    if ($stud->urs_quantity >= 4) {
      $availability = '<p>Ulbrokā: 4 un vairāk</p><br>';
    } else {
      $availability = '<p>Ulbrokā: ' . $stud->urs_quantity . '</p><br>';
    }
    if ($stud->krs_quantity >= 4) {
      $availability .= '<p>Kalnciema ielā: 4 un vairāk</p>';
    } else {
      $availability .= '<p>Kalnciema ielā: ' . $stud->krs_quantity . '</p>';
    }

    $availability .= '';

    return $availability;
  }
}

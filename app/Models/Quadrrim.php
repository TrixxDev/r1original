<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quadrrim extends Model
{
  use HasFactory;

  public $_includeStock = true;

  public function setIncludeStockAttribute($value)
  {
    return $this->_includeStock = $value;
  }

  public function getAvailableAttribute()
  {
    switch ($this->quantity) {
      case 1: {
        return 'Pēdējā';
      }
      case 2: {
        return 'Pēdējās 2';
      }
      case 3: {
        return 'Pēdējās 3';
      }
      case -1:
      case 0: {
        if ($this->_includeStock) {
          $count = $this->getStockCount();
          switch ($count){
            case 1: {
              return 'Pēdējā';
            }
            case 2: {
              return 'Pēdējās 2';
            }
            case 3: {
              return 'Pēdējās 3';
            }
            case -1:
            case 0:{
              return 'Zvaniet!';
            }
            default:{
              return 'Pieejams';
            }
          }
        } else {
          return 'Zvaniet!';
        }
      }
      default: {
        return 'Pieejams';
      }
    }
  }

  public function getDotAvailableAttribute()
  {
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

  public function getStockCount()
  {
//    $stocks = Autostock::where('tire_id', $this->rim_id)->get();
//
//    $count=0;
//
//    foreach ($stocks as $stock) {
//        if ($stock !== NULL && $stock->quantity >= 1) {
//          $count += $stock->quantity;
//        }
//      }
//
//    return $count;
  }

  public function getStockAvailabilityAttribute()
  {
//    $tire = Autotire::where('tire_id', $this->tire_id)->first();
//    $stocks = Autostock::where('tire_id', $tire->tire_id)->get();
//
//    $stock_names = [
//      'i3' => 'I3',
//      'gy' => 'GoodYear',
//      'rz' => 'RiepuZona',
//    ];

//    if ($rim->urs_quantity >= 4) {
//      $availability = '<p>Ulbrokā: 4 un vairāk</p><br>';
//    } else {
//      $availability = '<p>Ulbrokā: ' . $rim->urs_quantity . '</p><br>';
//    }
//    if ($rim->krs_quantity >= 4) {
//      $availability .= '<p>Kalnciema ielā: 4 un vairāk</p>';
//    } else {
//      $availability .= '<p>Kalnciema ielā: ' . $rim->krs_quantity . '</p>';
//    }
//
//    if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
//      $availability = '<p>Ulbrokā: ' . $rim->urs_quantity . '</p><br>';
//      $availability .= '<p>Kalnciema ielā: ' . $rim->krs_quantity . '</p>';
//      foreach ($stock_names as $key => $stock_name) {
//        $stock = Autostock::where('itype', $key)->where('tire_id', $rim->tire_id)->first();
//        if ($stock && $stock->quantity > 0) {
//          $availability .= '<br><p>' . $stock_name . ': ' . $stock->quantity . '</p>';
//        } else {
//          $availability .= '<br><p>' . $stock_name . ': 0</p>';
//        }
//      }
//    } else {
//      $dot = $this->getDotAvailableAttribute();
//      if ($dot === 'red') {
//        $availability = '<p style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</p>';
//      } else if ($dot === 'yellow' || $dot === 'half-yellow') {
//        $availability = '<p style="text-align: center;">Riepas pieejamas partneru noliktavās<br>Piegāde 1 darbadienas laikā.</p>';
//      }
//    }
//    $availability .= '';

//    return $availability;
    return '<p style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</p>';
  }
}

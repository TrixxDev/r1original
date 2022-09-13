<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rim extends Model
{
    use HasFactory;

  public $_includeStock = true;

  public function setIncludeStockAttribute($value)
  {
    return $this->_includeStock = $value;
  }

//  public function getAvailableAttribute()
//  {
//    switch ($this->quantity) {
//      case 1: {
//        return 'Pēdējā';
//      }
//      case 2: {
//        return 'Pēdējās 2';
//      }
//      case 3: {
//        return 'Pēdējās 3';
//      }
//      case -1:
//      case 0: {
//        if ($this->_includeStock) {
//          $count = $this->getStockCount();
//          switch ($count){
//            case 1: {
//              return 'Pēdējā';
//            }
//            case 2: {
//              return 'Pēdējās 2';
//            }
//            case 3: {
//              return 'Pēdējās 3';
//            }
//            case -1:
//            case 0:{
//              return 'Zvaniet!';
//            }
//            default:{
//              return 'Pieejams';
//            }
//          }
//        } else {
//          return 'Zvaniet!';
//        }
//      }
//      default: {
//        return 'Pieejams';
//      }
//    }
//  }
//
//  public function getDotAvailableAttribute()
//  {
//    switch ($this->quantity) {
//      case 1:
//      case 2:
//      case 3: {
//        return 'half-green';
//      }
//      case -1:
//      case 0: {
//        if ($this->_includeStock) {
//          $count = $this->getStockCount();
//          switch ($count){
//            case -1:
//            case 0: {
//              return 'red';
//            }
//            case 1:
//            case 2:
//            case 3: {
//              return 'half-yellow';
//            }
//            default:{
//              return 'yellow';
//            }
//          }
//        } else {
//          return 'red';
//        }
//      }
//      default: {
//        return 'green';
//      }
//    }
//
//  }
//
//  public function getStockCount()
//  {
//    $stocks = Autostock::where('tire_id', $this->tire_id)->get();
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
//  }
}

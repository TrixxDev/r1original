<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rim extends Model
{
  use HasFactory;

  protected $primaryKey = 'rim_id';

  public $_includeStock = true;

  public function setIncludeStockAttribute($value)
  {
    return $this->_includeStock = $value;
  }

  public function getOfferPriceAttribute()
  {
    return $this->price3;
  }

  public function getLinkAttribute()
  {
    $rim = Rimmake::selectRaw('rim_makes.*, rim_makes.title as tread_title')
      ->selectRaw('rim_brands.*, rim_brands.title as brand_title')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->where('rim_makes.make_id', $this->make_id)
      ->first();
    if (!isset($rim->brand_title) || !isset($rim->tread_title)) {
      return false;
    } else {
      return route('lietais-disks', [$rim->brand_title, str_replace('/', '_', $rim->tread_title), $this->rim_id]);
    }
  }

  public function getAvailableAttribute()
  {
    switch ($this->quantity) {
      case 1: {
        return 'Pēdējais';
      }
      case 2: {
        return 'Pēdējie 2';
      }
      case 3: {
        return 'Pēdējie 3';
      }
      case -1:
      case 0: {
        if ($this->_includeStock) {
          $count = $this->getStockCount();
          switch ($count){
            case 1: {
              return 'Pēdējais';
            }
            case 2: {
              return 'Pēdējie 2';
            }
            case 3: {
              return 'Pēdējie 3';
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

    if ($this->urs_quantity > 0 && $this->krs_quantity <= 0) {
      $this->quantity = $this->urs_quantity;
    } else if ($this->urs_quantity <= 0 && $this->krs_quantity > 0) {
      $this->quantity = $this->krs_quantity;
    } else if ($this->urs_quantity <= 0 && $this->krs_quantity <= 0) {
      $this->quantity = 0;
    }

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

  public function getStockCount()
  {

    $stocks = Rimstock::where('rim_id', $this->rim_id)->get();

    $count=0;

    foreach ($stocks as $stock) {
      if ($stock !== NULL && $stock->quantity >= 1) {
        $count += $stock->quantity;
      }
    }

    return $count;
  }

  public function getStockAvailabilityAttribute()
  {
    $rim = Rim::where('rim_id', $this->rim_id)->first();
    $stocks = Rimstock::where('rim_id', $this->rim_id)->get();

    $stock_names = [
      'i3' => 'I3',
    ];

    if ($rim->urs_quantity >= 4) {
      $availability = '<p>Ulbrokā: 4 un vairāk</p><br>';
    } else {
      $availability = '<p>Ulbrokā: ' . $rim->urs_quantity . '</p><br>';
    }
    if ($rim->krs_quantity >= 4) {
      $availability .= '<p>Kalnciema ielā: 4 un vairāk</p>';
    } else {
      $availability .= '<p>Kalnciema ielā: ' . $rim->krs_quantity . '</p>';
    }

    if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
      $availability = '<p>Ulbrokā: ' . $rim->urs_quantity . '</p><br>';
      $availability .= '<p>Kalnciema ielā: ' . $rim->krs_quantity . '</p>';
      foreach ($stock_names as $key => $stock_name) {
        $stock = Rimstock::where('itype', $key)->where('rim_id', $rim->rim_id)->first();
        if ($stock && $stock->quantity > 0) {
          $availability .= '<br><p>' . $stock_name . ': ' . $stock->quantity . '</p>';
        } else {
          $availability .= '<br><p>' . $stock_name . ': 0</p>';
        }
      }
    } else {
      $dot = $this->getDotAvailableAttribute();
      if ($dot === 'red') {
        $availability = '<p style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</p>';
      } else if ($dot === 'yellow' || $dot === 'half-yellow') {
        $availability = '<p style="text-align: center;">Diski pieejami partneru noliktavās<br>Piegāde 1 darbadienas laikā.</p>';
      }
    }
    $availability .= '';

    return $availability;
  }

  public function getBrandTitleAttribute()
  {
    $tread = Rimmake::where('make_id', $this->make_id)->first();
    $brand = Rimbrand::where('brand_id', $tread->brand_id)->first();

    return $brand->title;
  }

  public function getTreadTitleAttribute()
  {
    $tread = Rimmake::where('make_id', $this->make_id)->first();

    return $tread->title;
  }

  public function getFullNameAttribute()
  {
    return $this->getBrandTitleAttribute() . ' ' . $this->getTreadTitleAttribute() . ' ' . $this->skr . 'x' . $this->pcd . ' R' . $this->d3 . ' ' . $this->d1 . 'J et' . $this->et . ' ' . $this->dc . ' ' . $this->color;
  }

  public function getFullTitleAttribute()
  {
    return $this->getBrandTitleAttribute() . ' ' . $this->getTreadTitleAttribute();
  }

  public function getBrandCommentAttribute()
  {
    $tread = Rimmake::where('make_id', $this->make_id)->first();
    $brand = Rimbrand::where('brand_id', $tread->brand_id)->first();

    return $brand->comment;
  }

  public function getTreadCommentAttribute()
  {
    $tread = Rimmake::where('make_id', $this->make_id)->first();

    return $tread->comment;
  }

  public function tread()
  {
    return $this->hasOne('App\Models\Rimmake', 'make_id', 'make_id');
  }

}

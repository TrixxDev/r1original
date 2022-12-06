<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Quadr extends Model
{
    use HasFactory;

    protected $table = 'quadr_tires';

    protected $primaryKey = 'tire_id';

    public $_includeStock = true;

    public function setIncludeStockAttribute($value)
    {
        return $this->_includeStock = $value;
    }

    public function getImageAttribute()
    {
      $fileName = '/storage/app/public/quadr/tread/' . $this->tread_id . '.png';
      if (file_exists(dirname(__DIR__, 2) . $fileName)) {
        return $fileName;
      } else {
        return false;
      }
    }

    public function getFullNameAttribute()
    {
      $sql = Quadrtread::selectRaw('quadr_treads.*, quadr_treads.title as tread_title')
        ->selectRaw('quadr_brands.*, quadr_brands.title as brand_title')
        ->leftJoin('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
        ->where('quadr_treads.tread_id', $this->make_id)
        ->first();
      if (!isset($sql->brand_title) || !isset($sql->tread_title)) {
        return false;
      } else {
        return $sql->brand_title . ' ' . $sql->tread_title . ' ' . $this->getFullSizeAttribute() . ' ' . $this->comment . ' ' . $this->getLiSiAttribute();
      }
    }

    public function getFullSizeAttribute(): string
    {
        if (!$this->sep && !$this->d2) {
            $size = $this->d1 . '-' . $this->d3;
        } else {
            $size = $this->d1 . $this->sep . $this->d2 . $this->sep2 . $this->d3;
        }
        return $size;
    }

    public function getOfferPriceAttribute()
    {
        if ($this->price2 == null) {
            return $this->price1;
        } else {
            return $this->price2;
        }
    }

    public function getQuadrCommentAttribute()
    {
        $tire = Quadr::where('tire_id', $this->tire_id)->first();
        return $tire->comment;
    }

    public function getStockCount()
    {
        $stocks = Quadrstock::where('tire_id', $this->tire_id)->get();

        $count=0;

        foreach ($stocks as $stock) {
          if ($stock !== NULL && $stock->quantity >= 1) {
            $count += $stock->quantity;
          }
        }

        return $count;
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
                        case 1:
                        case 2:
                        case 3: {
                            return 'half-yellow';
                        }
                        case -1:
                        case 0:{
                            return 'red';
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

    public function getTitleAttribute()
    {
        $sql = DB::table('quadr_treads')->selectRaw('quadr_treads.*, quadr_treads.title as tread_title')
            ->selectRaw('quadr_brands.*, quadr_brands.title as brand_title')
            ->leftJoin('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($sql->brand_title) || !isset($sql->tread_title)) {
            return false;
        } else {
            return $sql->brand_title . ' ' . $sql->tread_title;
        }
    }

    public function getLinkAttribute()
    {
        $tire = Quadrtread::selectRaw('quadr_treads.*, quadr_treads.title as tread_title')
            ->selectRaw('quadr_brands.*, quadr_brands.slug as brand_title')
            ->leftJoin('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
            return false;
        } else {
            $url = route('kvadraciklu-riepa', [$tire->brand_title, str_replace('/', '_', $tire->tread_title), $this->tire_id]);
            $url = str_replace('&', '$1', $url);
            return $url;
        }
    }

    public function getLiSiAttribute()
    {
        return $this->li . $this->si;
    }

    public function getBrandAttribute()
    {
        $sql = DB::table('quadr_treads')->selectRaw('quadr_treads.*, quadr_treads.title as tread_title')
            ->selectRaw('quadr_brands.*, quadr_brands.title as brand_title')
            ->leftJoin('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
            ->where('quadr_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($sql->brand_title)) {
            return false;
        } else {
            return $sql->brand_title;
        }
    }

    public function getStocksAttribute()
    {
      $tire = Quadr::where('tire_id', $this->tire_id)->first();

      $stock_qty = [];

      $stock_names = [
        'i3' => 'I3',
        'duell' => 'Duell',
        'starco' => 'StarCo',
      ];

      array_push($stock_qty, $tire->quantity);

      foreach ($stock_names as $key => $stock_name) {
        $stock = Quadrstock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
        if ($stock && $stock->quantity != '-1') {
          array_push($stock_qty, $stock->quantity);
        }
      }

      return array_sum($stock_qty);
    }

    public function getStockAvailabilityAttribute()
    {

      $tire = Quadr::where('tire_id', $this->tire_id)->first();
      $stocks = Quadrstock::where('tire_id', $tire->tire_id)->get();

      $stock_names = [
        'i3' => 'I3',
        'duell' => 'Duell',
        'starco' => 'StarCo',
      ];

      $availability = '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
      $availability .= '<p>Kalnciema ielā: ' . $tire->krs_quantity . '</p>';

      if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
        foreach ($stock_names as $key => $stock_name) {
          $stock = Quadrstock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
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
          $availability = '<p style="text-align: center;">Riepas pieejamas partneru noliktavās<br>Piegāde 1 darbadienas laikā.</p>';
        }
      }

      return $availability;
    }

    public function tread()
    {
        return $this->hasOne('App\Models\Quadrtread', 'tread_id', 'make_id');
    }
}

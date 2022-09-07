<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Autotire extends Model
{
    use HasFactory;

    protected $table = 'auto_tires';

    protected $primaryKey = 'tire_id';

    public $_includeStock = true;

    public function setIncludeStockAttribute($value)
    {
        return $this->_includeStock = $value;
    }

    public function getImageAttribute()
    {
        $fileName = '/storage/app/public/auto/tread/' . $this->tread_id . '.png';
        if (file_exists(dirname(__DIR__, 2) . $fileName)) {
          return $fileName;
        } else {
          return false;
        }
    }

    public function getFullSizeAttribute()
    {
        return $this->d1 . '/' . $this->d2 . ' R' . $this->d3;
    }

    public function getOfferPriceAttribute()
    {
        if ($this->price2 == null) {
            return $this->price1;
        } else {
            return $this->price2;
        }
    }

    public function getAutoCommentAttribute()
    {
        $tire = Autotire::where('tire_id', $this->tire_id)->first();
        return $tire->comment;
    }

    public function getStockCount()
    {
        $stock = Autostock::where('tire_id', $this->tire_id)->first();

        $count=0;

        if ($stock !== NULL && $stock->quantity >= 1) {
            $count += $stock->quantity;
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

    public function getTitleAttribute()
    {
        $sql = DB::table('auto_treads')->selectRaw('auto_treads.*, auto_treads.title as tread_title')
                                              ->selectRaw('auto_brands.*, auto_brands.title as brand_title')
                                              ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
                                              ->where('auto_treads.tread_id', $this->make_id)
                                              ->first();
        if (!isset($sql->brand_title) || !isset($sql->tread_title)) {
            return false;
        } else {
            return $sql->brand_title . ' ' . $sql->tread_title;
        }
    }

    public function getLiSiAttribute()
    {
        return $this->li . $this->si;
    }

    public function getBrandAttribute()
    {
        $sql = DB::table('auto_treads')->selectRaw('auto_treads.*, auto_treads.title as tread_title')
            ->selectRaw('auto_brands.*, auto_brands.title as brand_title')
            ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($sql->brand_title)) {
            return false;
        } else {
            return $sql->brand_title;
        }
    }

    public function getLinkAttribute()
    {
        $tire = Autotread::selectRaw('auto_treads.*, auto_treads.slug as tread_title')
            ->selectRaw('auto_brands.*, auto_brands.slug as brand_title')
            ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
            return false;
        } else {
            return route('vasaras-riepa', [$tire->brand_title, $tire->tread_title, $this->tire_id]);
        }
    }

    public function getStockAvailabilityAttribute()
    {
        $tire = Autotire::where('tire_id', $this->tire_id)->first();
        $stocks = Autostock::where('tire_id', $tire->tire_id)->get();

        $stock_names = [
            'i3' => 'I3',
            'gy' => 'GoodYear',
            'rz' => 'RiepuZona',
        ];

        $availability = '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
        $availability .= '<p>Kalnciema ielā: ' . $tire->krs_quantity . '</p>';

        if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
            foreach ($stock_names as $key => $stock_name) {
                $stock = Autostock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
                if ($stock && $stock->quantity > 0) {
                    $availability .= '<br><p>' . $stock_name . ': ' . $stock->quantity . '</p>';
                } else {
                    $availability .= '<br><p>' . $stock_name . ': 0</p>';
                }
            }
        }

        $dot = $this->getDotAvailableAttribute();
        if ($dot === 'red') {
          $availability = '<p style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</p>';
        } else if ($dot === 'yellow' || $dot === 'half-yellow') {
          $availability = '<p style="text-align: center;">Riepas pieejamas partneru noliktavās<br>Piegāde 1 darbadienas laikā.</p>';
        }

        return $availability;
    }

    public static function shippingPrice($data)
    {

      return json_encode($data);

    }

    public function tread()
    {
        return $this->hasOne('App\Models\Autotread', 'tread_id', 'make_id');
    }
}


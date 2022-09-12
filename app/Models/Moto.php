<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Moto extends Model
{
    use HasFactory;

    protected $table = 'moto_tires';

    protected $primaryKey = 'tire_id';

    public $_includeStock = true;

    public function setIncludeStockAttribute($value)
    {
        return $this->_includeStock = $value;
    }

    public function getImageAttribute()
    {
      $fileName = '/storage/app/public/moto/tread/' . $this->tread_id . '.png';
      if (file_exists(dirname(__DIR__, 2) . $fileName)) {
        return $fileName;
      } else {
        return false;
      }
    }

    public function getFullNameAttribute()
    {
      $tire = Mototread::selectRaw('moto_treads.*, moto_treads.title as tread_title')
        ->selectRaw('moto_brands.*, moto_brands.title as brand_title')
        ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
        ->where('moto_treads.tread_id', $this->make_id)
        ->first();
      if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
        return false;
      } else {
        return $tire->brand_title . ' ' . $tire->tread_title . ' ' . $this->d1 . '/' . $this->d2 . 'R' . $this->d3;
      }
    }

    public function getFullSizeAttribute()
    {
        if ($this->d2 != '') {
          return $this->d1 . '/' . $this->d2 . ' ' . $this->d4 . ' ' . $this->d3;
        } else {
          return $this->d1 . ' ' . $this->d4 . ' ' . $this->d3;
        }
    }

    public function getOfferPriceAttribute()
    {
        if ($this->price2 == null) {
            return $this->price1;
        } else {
            return $this->price2;
        }
    }

    public function getMotoCommentAttribute()
    {
        $tire = Moto::where('tire_id', $this->tire_id)->first();
        return $tire->comment;
    }

    public function getStockCount()
    {
        $stock = Motostock::where('tire_id', $this->tire_id)->first();

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
        $sql = Mototread::selectRaw('moto_treads.*, moto_treads.title as tread_title')
            ->selectRaw('moto_brands.*, moto_brands.title as brand_title')
            ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
            ->where('moto_treads.tread_id', $this->make_id)
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
        $sql = Mototread::selectRaw('moto_treads.*, moto_treads.title as tread_title')
            ->selectRaw('moto_brands.*, moto_brands.title as brand_title')
            ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
            ->where('moto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($sql->brand_title) || !isset($sql->tread_title)) {
            return false;
        } else {
            return $sql->brand_title . ' ' . $sql->tread_title;
        }
    }

    public function getLinkAttribute()
    {
        $tire = Mototread::selectRaw('moto_treads.*, moto_treads.slug as tread_title')
            ->selectRaw('moto_brands.*, moto_brands.slug as brand_title')
            ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
            ->where('moto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
            return false;
        } else {
            return route('motociklu-riepa', [$tire->brand_title, $tire->tread_title, $this->tire_id]);
        }
    }

    public function getStocksAttribute()
    {
      $tire = Moto::where('tire_id', $this->tire_id)->first();

      $stock_qty = [];

      $stock_names = [
        'i3' => 'I3',
        'duell' => 'Duell',
      ];

      array_push($stock_qty, $tire->quantity);

      foreach ($stock_names as $key => $stock_name) {
        $stock = Motostock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
        if ($stock && $stock->quantity != '-1') {
          array_push($stock_qty, $stock->quantity);
        }
      }

      return array_sum($stock_qty);
    }

    public function getStockAvailabilityAttribute()
    {
        $tire = Moto::where('tire_id', $this->tire_id)->first();
        $stocks = Motostock::where('tire_id', $tire->tire_id)->get();

        $stock_names = [
            'i3' => 'I3',
            'duell' => 'Duell',
        ];

        $availability = '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
        $availability .= '<p>Kalnciema iela: ' . $tire->krs_quantity . '</p>';

        if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
          foreach ($stock_names as $key => $stock_name) {
            $stock = Motostock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
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

    public function types(): array
    {

      $tipi = [];

      $types = Self::select('type')->get();
      foreach ($types as $type) {
        switch ($type->type) {
          case 'custom':
            $tipi[$type->type] = 'Custom';
            break;
          case 'harley davidson':
            $tipi[$type->type] = 'Harley Davidson';
            break;
          case 'moto cross':
            $tipi[$type->type] = 'Moto Cross';
            break;
          case 'racing':
            $tipi[$type->type] = 'Racing';
            break;
          case 'sport':
            $tipi[$type->type] = 'Sport';
            break;
          case 'sport touring':
            $tipi[$type->type] = 'Sport Touring';
            break;
          case 'trail':
            $tipi[$type->type] = 'Trail';
            break;
        }
      }

      return array_unique($tipi);
    }

    public function getMotoTypeAttribute()
    {
      $type = strtolower($this->type);

      if ($type != '') {
        $arr = [
          'custom' => 'Ct',
          'harley davidson' => 'Hd',
          'moto cross' => 'Mx',
          'racing' => 'Rc',
          'sport' => 'Sp',
          'sport touring' => 'St',
          'trail' => 'Tr',
        ];

        return $arr[$type];
      }

    }

    public function getTypeDescAttribute()
    {
      $type = strtolower($this->type);

      if ($type != '') {
        $arr = [
          'custom' => ['Ct', 'Custom'],
          'harley davidson' => ['Hd', 'Harley Davidson'],
          'moto cross' => ['Mx', 'Moto Cross'],
          'racing' => ['Rc', 'Racing'],
          'sport' => ['Sp', 'Sport'],
          'sport touring' => ['St', 'Sport Touring'],
          'trail' => ['Tr', 'Trail'],
        ];

        return $arr[$type];
      }
    }

    public function tread()
    {
        return $this->hasOne('App\Models\Mototread', 'tread_id', 'make_id');
    }
}

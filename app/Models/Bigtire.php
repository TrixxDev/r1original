<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Bigtire extends Model
{

    protected $table = 'big_tires';

    protected $primaryKey = 'tire_id';

    protected $fillable = ['visible_users', 'visible_list'];

    public $timestamps = false;

    use HasFactory;
  /**
   * @var mixed
   */
  public $_includeStock = true;

  public function setIncludeStockAttribute($value)
  {
    return $this->_includeStock = $value;
  }

  public function getImageAttribute()
  {
    $fileName = '/storage/app/public/industrial/tread/' . $this->tread_id . '.jpg';
    $dirname = dirname(__DIR__, 2) . $fileName;
    if (file_exists($dirname)) {
      if (filesize($dirname) !== 0) {
        return $fileName;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function getFullSizeAttribute()
  {
    if (empty($this->sep2) && empty($this->d2)) {
      return $this->d1 . $this->sep . $this->d3;
    } else {
      return $this->d1 . $this->sep . $this->d2 . $this->sep2 . $this->d3;
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

  public function getAutoCommentAttribute()
  {
    $tire = Bigtire::where('tire_id', $this->tire_id)->first();
    return $tire->comment;
  }

  public function getStockCount()
  {
    $stock = Bigstock::where('tire_id', $this->tire_id)->first();

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
    $sql = DB::table('bigtire_treads')->selectRaw('bigtire_treads.*, bigtire_treads.title as tread_title')
      ->selectRaw('bigtire_brands.*, bigtire_brands.title as brand_title')
      ->leftJoin('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
      ->where('bigtire_treads.tread_id', $this->make_id)
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
    $sql = DB::table('bigtire_treads')->selectRaw('bigtire_treads.*, bigtire_treads.title as tread_title')
      ->selectRaw('bigtire_brands.*, bigtire_brands.title as brand_title')
      ->leftJoin('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
      ->where('bigtire_treads.tread_id', $this->make_id)
      ->first();
    if (!isset($sql->brand_title)) {
      return false;
    } else {
      return $sql->brand_title;
    }
  }

  public function getLinkAttribute()
  {
    $tire = Bigtread::selectRaw('bigtire_treads.*, bigtire_treads.slug as tread_title')
      ->selectRaw('bigtire_brands.*, bigtire_brands.slug as brand_title')
      ->leftJoin('bigtire_brands', 'bigtire_treads.brand_id', '=', 'bigtire_brands.brand_id')
      ->where('bigtire_treads.tread_id', $this->make_id)
      ->first();
    if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
      return false;
    } else {
      return route('lielas-riepa', [$tire->brand_title, $tire->tread_title, $this->tire_id]);
    }
  }

  public function getStockAvailabilityAttribute()
  {
    $tire = Bigtire::where('tire_id', $this->tire_id)->first();
    $stocks = Bigstock::where('tire_id', $tire->tire_id)->get();

    $stock_names = [
      'i3' => 'I3',
      'starco' => 'StarCo',
    ];

    ($tire->quantity) ?? $tire->quantity = 0;
    ($tire->urs_quantity) ?? $tire->urs_quantity = 0;
    ($tire->krs_quantity) ?? $tire->krs_quantity = 0;

    $availability = '<p>R1 Kopā: ' . $tire->quantity . '</p><br>';
    $availability .= '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
    $availability .= '<p>Kalnciema ielā: ' . $tire->krs_quantity . '</p>';

    if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
      foreach ($stock_names as $key => $stock_name) {
        $stock = Bigstock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
        if ($stock && $stock->quantity > 0) {
          $availability .= '<br><p>' . $stock_name . ': ' . $stock->quantity . '</p>';
        } else {
          $availability .= '<br><p>' . $stock_name . ': 0</p>';
        }
      }
    }

    return $availability;
  }

  public function addSecondaryArticle($article, $type, $quantity = 0)
  {

    $list = Bigstock::where('tire_id', $this->tire_id)->where('article', $article)->get();

    if (count($list) == 0) {
      $item = new Bigstock();
      $item->article = $article;
      $item->tire_id = $this->tire_id;
      $item->quantity = $quantity;
      $item->itype = $type;
      $item->save();
    } else {
      foreach ($list as $item) {
        $item->update(['quantity' => $quantity]);
      }
    }

  }

  public function tread()
  {
    return $this->hasOne('App\Models\Bigtread', 'tread_id', 'make_id');
  }
}

<?php

namespace App\Models;

use App\Helper\Image;
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
    public $cbrand;

    public function setIncludeStockAttribute($value)
    {
        return $this->_includeStock = $value;
    }

    public function getImageAttribute()
    {
        return Image::showAd('auto', $this->make_id);
    }

    public function getFullSizeAttribute()
    {
        return $this->d1 . '/' . $this->d2 . ' R' . $this->d3;
    }

    public function getSizeTitleAttribute()
    {
      $this->_includeStock = true;

      $brand = $this->getFullSizeAttribute();
      return '<h4 class="tire-brand-name">' . $brand . '</h4>';
    }

    public function getCodeExplainAttribute()
    {
      $code_array = [];

      $return = '';

      $codes = Code::all();

      foreach ($codes as $code) {
        $code_array[$code->name] = $code->explanation;
      }

      $codes = explode(' ', $this->code);
      foreach ($codes as $code) {
        if (isset($code_array[$code])) {
          $return .= $code_array[$code] . '<br>';
        }
      }

      if (strpos($this->code, 'DOT') !== false) {
        $return .= $code_array['DOT'];
      }

      return $return;
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

        $stocks = Autostock::where('tire_id', $this->tire_id)->get();

        $count=0;

        foreach ($stocks as $stock) {
          if ($stock !== NULL && $stock->quantity >= 1) {
            $count += $stock->quantity;
          }
        }

        return $count;
    }

//    public static function RZLink($article)
//    {
//      $curl = curl_init();
//      curl_setopt_array($curl, array(
//        CURLOPT_URL => 'https://riepuzona.lv/ajax/searchGoods',
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => "",
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => "POST",
//        CURLOPT_POSTFIELDS => "term=$article&data%5Bactive%5D=-1",
//        CURLOPT_HTTPHEADER => array(
//          "cache-control: no-cache",
//          "content-type: application/x-www-form-urlencoded"
//        ),
//      ));
//
//      $response = curl_exec($curl);
//      $err = curl_error($curl);
//
//      curl_close($curl);
//
//      return $response;
//    }

    public static function StockLink($tire)
    {
      $stocks = Autostock::where('tire_id', $tire->tire_id)->get();

      $urls = [];

      foreach ($stocks as $stock) {
        switch ($stock->itype) {
          case 'i3': {
            $urls['Latakko'] = ['link' => 'https://shop.latakko.eu/product/' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
          case 'gy': {
            $urls['Goodyear'] = ['link' => 'https://myway.goodyear.com/p/' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
          case 'rz': {
            $urls['RiepuZona'] = ['link' => 'https://riepuzona.lv/lv/meklet/t-' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
        }
      }

      return $urls;
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

        if ($this->urs_quantity > 0 && $this->krs_quantity <= 0) {
          $this->tire_quantity = $this->urs_quantity;
        } else if ($this->urs_quantity <= 0 && $this->krs_quantity > 0) {
          $this->tire_quantity = $this->krs_quantity;
        } else if ($this->urs_quantity <= 0 && $this->krs_quantity <= 0) {
          $this->tire_quantity = 0;
        } else {
          $this->tire_quantity = $this->urs_quantity + $this->krs_quantity;
        }

//        $this->quantity = (int) $this->quantity;

//        dump($this->quantity);

        switch ($this->tire_quantity) {
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
        $sql = DB::table('auto_treads')->selectRaw('auto_treads.*, auto_treads.t_title as tread_title')
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

    public function getFullNameAttribute()
    {
	    return $this->getTitleAttribute() . ' ' . $this->getFullSizeAttribute() . ' ' . $this->code . ' ' . $this->getLiSiAttribute();
    }

    public function getLiSiAttribute()
    {
        return $this->li . $this->si;
    }

    public function getBrandAttribute()
    {
        $sql = DB::table('auto_treads')->selectRaw('auto_treads.*, auto_treads.t_title as tread_title')
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
        $tire = Autotread::selectRaw('auto_treads.*, auto_treads.t_title as tread_title')
            ->selectRaw('auto_brands.*, auto_brands.slug as brand_title')
            ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
            return false;
        } else {
	        if ($tire->season == 1) {
                return route('vasaras-riepa', [$tire->brand_title, str_replace('/', '_', $tire->tread_title), $this->tire_id]);
	        } else {
                return route('ziemas-riepa', [$tire->brand_title, str_replace('/', '_', $tire->tread_title), $this->tire_id]);
	        }
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

	      if ($tire->urs_quantity >= 4) {
            $availability = '<span>Ulbrokā: 4 un vairāk</span><br>';
	      } else {
            $availability = '<span>Ulbrokā: ' . $tire->urs_quantity . '</span><br>';
	      }
	      if ($tire->krs_quantity >= 4) {
            $availability .= '<span>Kalnciema ielā: 4 un vairāk</span>';
	      } else {
            $availability .= '<span>Kalnciema ielā: ' . $tire->krs_quantity . '</span>';
        }

        if (Auth::check()) {
            $availability = '<span>Ulbrokā: ' . $tire->urs_quantity . '</span><br>';
            $availability .= '<span>Kalnciema ielā: ' . $tire->krs_quantity . '</span>';
            foreach ($stock_names as $key => $stock_name) {
                $stock = Autostock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
                if ($stock && $stock->quantity > 0) {
                    $availability .= '<br><span>' . $stock_name . ': ' . $stock->quantity . '</span>';
                } else {
                    $availability .= '<br><span>' . $stock_name . ': 0</span>';
                }
            }
            if ($tire->acomment !== null) {
              $availability .= '<br><hr class="admin-comments"><span><b>Piezīmes:</b> </span><br><span>' . $tire->acomment . '</span>';
            }
        } else {
          $dot = $this->getDotAvailableAttribute();
          if ($dot === 'red') {
            $availability = '<span style="text-align: center;">Nepieciešams<br>pārbaudīt pieejamību.</span>';
          } else if ($dot === 'yellow' || $dot === 'half-yellow') {
            $availability = '<span style="text-align: center;">Riepas pieejamas partneru noliktavās<br>Piegāde 1 darbadienas laikā.</span>';
          }
        }
        $availability .= '';

        return $availability;
    }

    public function addSecondaryArticle($article, $type, $quantity = 0)
    {

      $list = Autostock::where('tire_id', $this->tire_id)->where('article', $article)->get();

      if (count($list) == 0) {
        $item = new Autostock;
        $item->tire_id = $this->tire_id;
        $item->article = $article;
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
        return $this->hasOne('App\Models\Autotread', 'tread_id', 'make_id');
    }
}


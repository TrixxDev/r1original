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
            $urls['Lattako'] = ['link' => 'https://shop.latakko.eu/product/' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
          case 'gy': {
            $urls['Goodyear'] = ['link' => 'https://myway.goodyear.com/p/' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
//          case 'rz': {
//            dd(Self::RZLink($stock->article));
//            $urls = [$stock->itype => Self::RZLink($stock->article)];
//            break;
//          }
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
          $this->quantity = $this->urs_quantity;
        } else if ($this->urs_quantity <= 0 && $this->krs_quantity > 0) {
          $this->quantity = $this->krs_quantity;
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
            $availability = '<p>Ulbrokā: 4 un vairāk</p><br>';
	} else {
            $availability = '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
	}
	if ($tire->krs_quantity >= 4) {
            $availability .= '<p>Kalnciema ielā: 4 un vairāk</p>';
	} else {
            $availability .= '<p>Kalnciema ielā: ' . $tire->krs_quantity . '</p>';
	}

        if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
            $availability = '<p>Ulbrokā: ' . $tire->urs_quantity . '</p><br>';
            $availability .= '<p>Kalnciema ielā: ' . $tire->krs_quantity . '</p>';
            foreach ($stock_names as $key => $stock_name) {
                $stock = Autostock::where('itype', $key)->where('tire_id', $tire->tire_id)->first();
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
        $availability .= '';

        return $availability;
    }

    public function lisiDesc($weight, $speed)
    {

      $carryCapacity = 'Kravnesības indekss: ';

      $carryCaps = [
        50 => '50 - 190 kg',
        51 => '51 - 195 kg',
        52 => '52 - 200 kg',
        53 => '53 - 206 kg',
        54 => '54 - 212 kg',
        55 => '55 - 218 kg',
        56 => '56 - 224 kg',
        57 => '57 - 230 kg',
        58 => '58 - 236 kg',
        59 => '59 - 243 kg',
        60 => '60 - 250 kg',
        61 => '61 - 257 kg',
        62 => '62 - 265 kg',
        63 => '63 - 272 kg',
        64 => '64 - 280 kg',
        65 => '65 - 290 kg',
        66 => '66 - 300 kg',
        67 => '67 - 307 kg',
        68 => '68 - 315 kg',
        69 => '69 - 325 kg',
        70 => '70 - 335 kg',
        71 => '71 - 345 kg',
        72 => '72 - 355 kg',
        73 => '73 - 365 kg',
        74 => '74 - 375 kg',
        75 => '75 - 387 kg',
        76 => '76 - 400 kg',
        77 => '77 - 412 kg',
        78 => '78 - 425 kg',
        79 => '79 - 437 kg',
        80 => '80 - 450 kg',
        81 => '81 - 462 kg',
        82 => '82 - 475 kg',
        83 => '83 - 487 kg',
        84 => '84 - 500 kg',
        85 => '85 - 515 kg',
        86 => '86 - 530 kg',
        87 => '87 - 545 kg',
        88 => '88 - 560 kg',
        89 => '89 - 580 kg',
        90 => '90 - 600 kg',
        91 => '91 - 615 kg',
        92 => '92 - 630 kg',
        93 => '93 - 650 kg',
        94 => '94 - 670 kg',
        95 => '95 - 690 kg',
        96 => '96 - 710 kg',
        97 => '97 - 730 kg',
        98 => '98 - 750 kg',
        99 => '99 - 775 kg',
        100 => '100 - 800 kg',
        101 => '101 - 825 kg',
        102 => '102 - 850 kg',
        103 => '103 - 875 kg',
        104 => '104 - 900 kg',
        105 => '105 - 925 kg',
        106 => '106 - 950 kg',
        107 => '107 - 975 kg',
        108 => '108 - 1000 kg',
        109 => '109 - 1030 kg',
        110 => '110 - 1060 kg',
        111 => '111 - 1090 kg',
        112 => '112 - 1120 kg',
        113 => '113 - 1150 kg',
        114 => '114 - 1180 kg',
        115 => '115 - 1215 kg',
        116 => '116 - 1250 kg',
        117 => '117 - 1285 kg',
        118 => '118 - 1320 kg',
        119 => '119 - 1360 kg',
        120 => '120 - 1400 kg',
        121 => '121 - 1450 kg',
        122 => '122 - 1500 kg',
        123 => '123 - 1550 kg',
        124 => '124 - 1600 kg',
        125 => '125 - 1650 kg',
        126 => '126 - 1700 kg',
      ];

      $speedCapacity = 'Ātruma indekss: ';

      $speedCaps = [
        'A1' => 'A1 - 5 Km/h',
        'A2' => 'A2 - 10 Km/h',
        'A3' => 'A3 - 15 Km/h',
        'A4' => 'A4 - 20 Km/h',
        'A5' => 'A5 - 25 Km/h',
        'A6' => 'A6 - 30 Km/h',
        'A7' => 'A7 - 35 Km/h',
        'A8' => 'A8 - 40 Km/h',
        'B' => 'B - 50 Km/h',
        'C' => 'C - 60 Km/h',
        'D' => 'D - 65 Km/h',
        'E' => 'E - 70 Km/h',
        'F' => 'F - 80 Km/h',
        'G' => 'G - 90 Km/h',
        'J' => 'J - 100 Km/h',
        'K' => 'K - 110 Km/h',
        'L' => 'L - 120 Km/h',
        'M' => 'M - 130 Km/h',
        'N' => 'N - 140 Km/h',
        'P' => 'P - 150 Km/h',
        'Q' => 'Q - 160 Km/h',
        'R' => 'R - 170 Km/h',
        'S' => 'S - 180 Km/h',
        'T' => 'T - 190 Km/h',
        'U' => 'U - 200 Km/h',
        'H' => 'H - 210 Km/h',
        'V' => 'V - 240 Km/h',
        'VR' => 'VR - Virs 210 Km/h',
        'W' => 'W - 270 Km/h',
        'Z' => 'Z - Virs 240 Km/h',
        'Y' => 'Y - 300 Km/h',
        'ZR' => 'ZR - Virs 240 Km/h',
      ];

      return @$carryCapacity . @$carryCaps[$weight] . '<br>' . @$speedCapacity . @$speedCaps[$speed];

    }

    public function addSecondaryArticle($article, $type, $quantity = 0)
    {

      $list = Self::where('tire_id', $this->tire_id)->where('article', $article)->get();

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


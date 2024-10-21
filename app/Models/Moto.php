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
      return $this->getTitleAttribute() . ' ' . $this->getFullSizeAttribute() . ' ' . $this->code . ' ' . $this->getLiSiAttribute();
    }

    public function getFullSizeAttribute()
    {
        if ($this->d2 != '') {
          return $this->d1 . '/' . $this->d2 . ' ' . $this->d4 . ' ' . $this->d3;
        } else {
          return $this->d1 . ' ' . $this->d4 . ' ' . $this->d3;
        }
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

    public function getMotoCommentAttribute()
    {
        $tire = Moto::where('tire_id', $this->tire_id)->first();
        return $tire->comment;
    }

    public function getStockCount()
    {
        $stocks = Motostock::where('tire_id', $this->tire_id)->orderBy('stock_id', 'DESC')->get();

        $count=0;

        foreach ($stocks as $stock) {
          if ($stock !== NULL && $stock->quantity >= 1) {
            $count += $stock->quantity;
          }
        }

        return $count;
    }

    public static function DuellLink($article)
    {

//      $curl = curl_init();
//
//      $ch = curl_init('https://lv.e-cat.intercars.eu/');
//      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//      curl_setopt($ch, CURLOPT_HEADER, 1);
//      $result = curl_exec($ch);
//
//
//      preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $cookies);
//
//
//      $session = (strpos($cookies[1][2], 'JSESSIONID') !== false) ? $cookies[1][1] . ' ' .$cookies[1][2] : $cookies[1][0] . ' ' . $cookies[1][1];
//
//      curl_close($ch);
//
//      curl_setopt_array($curl, array(
//        CURLOPT_URL => 'https://lv.e-cat.intercars.eu/lv/api/products/search/suggest?query=2055516',
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_HEADER => 1,
//        CURLOPT_ENCODING => "",
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => "GET",
//        CURLOPT_HTTPHEADER => array(
//          "cache-control: no-cache",
//          "content-type: application/json;charset=UTF-8",
//          "Cookie: JSESSIONID=Y13-69097244-5439-4c8f-963a-85f59ad6e4b9.app13"
//        ),
//      ));
//  //      JSESSIONID=Y10-7e7cd814-32f3-4a5c-a5fe-ee66f72d2f2d.app10
//      $response = curl_exec($curl);
//      $err = curl_error($curl);
//
//      curl_close($curl);
//      //dd($response);
//
//      $return = json_decode($response);
//      if (isset($return[0])) {
//	return $return[0]->product_link;
//      } else {
//	return '#';
//      }
//      //return json_decode($response)[0]->product_link;

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.duell.fi/jm/en/search?q=' . $article . '&ajaxSearch=1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/x-www-form-urlencoded"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      return json_decode($response)[0]->product_link;
    }

    public static function StockLink($tire)
    {

      $stocks = Motostock::where('tire_id', $tire->tire_id)->get();

      $urls = [];

      foreach ($stocks as $stock) {
        switch ($stock->itype) {
          case 'i3': {
            $urls['Latakko'] = ['link' => 'https://shop.latakko.eu/product/' . $stock->article, 'remaining' => $stock->quantity];
            break;
          }
          case 'duell': {
            $urls['Duell'] = ['link' => Self::DuellLink($stock->article), 'remaining' => $stock->quantity];
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
            default:{
              return 'yellow';
            }
          }
        } else {
          return 'red';
        }
      }
        switch ($this->quantity) {
            case -1:
            case 0: {
                if ($this->_includeStock) {
                    $count = $this->getStockCount();
                    switch ($count){
                        case -1:
                        case 0: {
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
        $tire = Mototread::selectRaw('moto_treads.*, moto_treads.title as tread_title')
            ->selectRaw('moto_brands.*, moto_brands.title as brand_title')
            ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
            ->where('moto_treads.tread_id', $this->make_id)
            ->first();
        if (!isset($tire->brand_title) || !isset($tire->tread_title)) {
            return false;
        } else {
            return route('motociklu-riepa', [strtolower($tire->brand_title), str_replace('/', '_', $tire->tread_title), $this->tire_id]);
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
        $stock = Motostock::where('itype', $key)->where('tire_id', $tire->tire_id)->orderBy('stock_id', 'DESC')->first();
        if ($stock && $stock->quantity != '-1') {
          array_push($stock_qty, $stock->quantity);
        }
      }

      return array_sum($stock_qty);
    }

    public function getStockAvailabilityAttribute()
    {
        $tire = Moto::where('tire_id', $this->tire_id)->first();

        $stock_names = [
            'i3' => 'I3',
            'duell' => 'Duell',
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

        if (Auth::check() && Auth::user()->hasRole(['administrators', 'moderators'])) {
          $availability = '<span>Ulbrokā: ' . $tire->urs_quantity . '</span><br>';
          $availability .= '<span>Kalnciema ielā: ' . $tire->krs_quantity . '</span>';
          foreach ($stock_names as $key => $stock_name) {
            $stock = Motostock::where('itype', $key)->where('tire_id', $tire->tire_id)->orderBy('stock_id', 'DESC')->first();
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

        return $availability;
    }

    public function types(): array
    {

      $tipi = [];

      $types = Self::select('type')->orderBy('type')->get();
      foreach ($types as $type) {
        switch ($type->type) {
          case 'CUSTOM':
          case 'Custom':
          case 'custom':
            $tipi[$type->type] = 'Custom';
            break;
          case 'SCOOTER':
          case 'Scooter':
          case 'scooter':
            $tipi[$type->type] = 'Scooter';
            break;
          case 'HARLEY DAVIDSON':
          case 'Harley Davidson':
          case 'harley davidson':
            $tipi[$type->type] = 'Harley Davidson';
            break;
          case 'MOTO CROSS':
          case 'Moto Cross':
          case 'moto cross':
            $tipi[$type->type] = 'Moto Cross';
            break;
          case 'RACING':
          case 'Racing':
          case 'racing':
            $tipi[$type->type] = 'Racing';
            break;
          case 'SPORT':
          case 'Sport':
          case 'sport':
            $tipi[$type->type] = 'Sport';
            break;
          case 'SPORT TOURING':
          case 'Sport Touring':
          case 'sport touring':
            $tipi[$type->type] = 'Sport Touring';
            break;
          case 'TRAIL':
          case 'Trail':
          case 'trail':
            $tipi[$type->type] = 'Trail';
            break;
        }
      }

      sort($tipi);

      return array_unique($tipi);
    }

    public function getMotoTypeAttribute()
    {
      $type = strtolower($this->type);

      if ($type == 1) return '';

      if ($type != '') {
        $arr = [
          'custom' => 'Ct',
          'harley davidson' => 'Hd',
          'moto cross' => 'Mx',
          'racing' => 'Rc',
          'sport' => 'Sp',
          'sport touring' => 'St',
          'trail' => 'Tr',
          'scooter' => 'Sc',
        ];

        return $arr[$type];
      } else {

      }

    }

    public function getTypeDescAttribute()
    {
      $type = strtolower($this->type);

      if ($type == '') return ['', 'Nav'];

      if ($type != '') {
        $arr = [
          'custom' => ['Ct', 'Custom'],
          'harley davidson' => ['Hd', 'Harley Davidson'],
          'moto cross' => ['Mx', 'Moto Cross'],
          'racing' => ['Rc', 'Racing'],
          'sport' => ['Sp', 'Sport'],
          'sport touring' => ['St', 'Sport Touring'],
          'trail' => ['Tr', 'Trail'],
	        'scooter' => ['Sc', 'Scooter'],
        ];

        if ($type == 1) return '';

        return $arr[$type];
      } else {
        return ['', ''];
      }
    }

    public function tread()
    {
        return $this->hasOne('App\Models\Mototread', 'tread_id', 'make_id');
    }

  public function addSecondaryArticle($article, $type, $quantity = 0)
  {

    $list = Motostock::where('tire_id', $this->tire_id)->where('article', $article)->get();

    if (count($list) == 0) {
      $item = new Motostock();
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
}

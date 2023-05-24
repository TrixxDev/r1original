<?php

  namespace App\Http\Controllers;

  use App\Models\Autotire;
  use App\Models\Bigbrand;
  use App\Models\Bigstock;
  use App\Models\Bigtire;
  use App\Models\Bigtread;
  use App\Models\Moto;
  use App\Models\Motostock;
  use App\Models\Quadr;
  use App\Models\Quadrstock;
  use App\Models\Rim;
  use App\Models\Rimbrand;
  use App\Models\Rimmake;
  use App\Models\Rimstock;
  use Carbon\Carbon;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Http\Request;
  use App\Models\Autostock;
  use Illuminate\Support\Facades\Session;
  use mysql_xdevapi\Exception;
  use PDO;
  use Illuminate\Support\Str;

  class SyncController extends Controller
  {

    public $accrual;
    public $tire_tables;
    public $stock_tables;
    public $article = '';

    public $urs = 0;
    public $krs = 0;
    private $treadId;
    private $brandId;

    public function __construct()
    {
      set_time_limit(0);
      $this->tire_tables = [
        'auto_tires' => [
          'Autotire',
          'auto_stock'
        ],
        'moto_tires' => [
          'Moto',
          'moto_stock'
        ],
        'quadr_tires' => [
          'Quadr',
          'quadr_stock'
        ],
        'rims' => [
          'Rim',
          ''
        ],
        'quadrims' => [
          'Quadrim',
          'quadrim_stock'
        ],
        'studs' => [
          'Stud',
          ''
        ],
      ];
    }

    // Accrual Sync - (Public) 212.3.218.22 - (Local) 192.168.0.36

    public function accrual(Request $request)
    {

      set_time_limit(0);

      try {
        $this->accrual = new PDO("sqlsrv:Server=" . env('ACCRUAL_IP') . ",1444;Database=accrual", "sa", "cenzors");
      } catch (\PDOException $e) {
        return json_encode(['urs_quantity' => '-100', 'krs_quantity' => '-100']);
//          die("Database connection failed: " . $e->getMessage());
//          exit;
      }

      (isset($request->article)) ? $this->article = $request->article : $this->article = '';

      if (!$this->article) {
        $this->updateArticles();
        DB::table('sync_times')->where('name', 'accrual')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
        echo 'Done';
      } else {

        $productInfo = $this->getAccrualInventory($this->article);
        if (isset($productInfo[$this->article])) {
          $stores = $productInfo['_stores'][$this->article];

          if (isset($stores[1])) {
            $this->urs = $stores[1];
            $this->urs = str_replace('Noliktava: ', '', $this->urs);
            $this->urs = intval($this->urs);
          } else {
            $this->urs = 0;
          }
          if (isset($stores[2])) {
            $this->krs = $stores[2];
            $this->krs = str_replace('Veikals: ', '', $this->krs);
            $this->krs = intval($this->krs);
          } else {
            $this->krs = 0;
          }
        } else {
          $this->urs = 0;
          $this->krs = 0;
        }

        $this->updateArticle($this->article);
        return json_encode(['urs_quantity' => intval($this->urs), 'krs_quantity' => intval($this->krs)]);
      }
    }

    public function getStocks($article)
    {
      foreach ($this->tire_tables as $tire_table => $tire_options) {
        $model = "App\\Models\\" . $tire_options[0];

        if (class_exists($model)) {
          $item = $model::where('article', $article)->first();
          if (!$item) continue;
          return $item->getStockAvailability;
        } else {
          return false;
        }
      }
    }

    public function getStockLinks($article)
    {
      foreach ($this->tire_tables as $tire_table => $tire_options) {
        $model = "App\\Models\\" . $tire_options[0];

        if (class_exists($model)) {
          $item = $model::where('article', $article)->first();
          if (!$item) continue;
          if (method_exists($model, 'StockLink')) {
            return $model::StockLink($item);
          }
        } else {
          return false;
        }
      }
    }

    public function updateArticle($article)
    {

      foreach ($this->tire_tables as $tire_table => $tire_options) {

        $model = "App\\Models\\" . $tire_options[0];

        $product = $model::where('article', $article)->first();
        if (!$product) {
          continue;
        }

        $sql = "SELECT ArticleId as ArtikulaId, Deleted FROM katdetal WHERE Deleted = 0 AND Artikuls = '$article'";
        $result = $this->accrual->query($sql);

        foreach ($result as $row) {
          $article = $row['ArtikulaId'];
        }

        $productInfo = $this->getAccrualInventory($product->article);
        if (isset($productInfo[$product->article])) {
          $total = intval($productInfo[$product->article]);
          $product->quantity = $total;

          $stores = $productInfo['_stores'][$product->article];

          if (isset($stores[1])) {
            $this->urs = $stores[1];
            $this->urs = str_replace('Noliktava: ', '', $this->urs);
            $product->urs_quantity = intval($this->urs);
          } else {
            $product->urs_quantity = 0;
          }
          if (isset($stores[2])) {
            $this->krs = $stores[2];
            $this->krs = str_replace('Veikals: ', '', $this->krs);
            $product->krs_quantity = intval($this->krs);
          } else {
            $product->krs_quantity = 0;
          }

          $product->updated_at = date('Y-m-d H:i:s');
          $product->save();
        } else {
          $product->quantity = 0;
          $product->urs_quantity = 0;
          $product->krs_quantity = 0;
          $product->updated_at = date('Y-m-d H:i:s');
          $product->save();
        }

        $sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.Deleted = 0 AND k.ArticleId = '" . $article . "'";
        $result = $this->accrual->query($sql);
        if ($result->rowCount()) {
          foreach ($result as $rows) {
            set_time_limit(0);
            $veikala_cena = (int) round(round($rows['Cena1'], 5) * 1.21);
            if ($rows['Deleted'] == 1) {
              $akcijas_cena = (int) round(round($rows['Cena3'], 5) * 1.21);
            } else {
              $akcijas_cena = (int)   round(round($rows['Cena'], 5) * 1.21);
            }
          }
          $product->price1 = $veikala_cena;
          $product->price2 = $akcijas_cena;
          $product->updated_at = date('Y-m-d H:i:s');
          $product->save();
        } else {
          $sql = "SELECT * FROM katalogs k WHERE Deleted = 0 AND k.ArticleId = '" . $article . "'";
          $result = $this->accrual->query($sql);
          if ($result->rowCount()) {
            foreach ($result as $rows) {
              set_time_limit(0);
              $veikala_cena = (int) round(round($rows['Cena1'], 5) * 1.21);
              $akcijas_cena = (int) round(round($rows['Cena3'], 5) * 1.21);
            }
            $product->price1 = $veikala_cena;
            $product->price2 = $akcijas_cena;
            $product->updated_at = date('Y-m-d H:i:s');
            $product->save();
          }
        }
      }
    }

    public function updateArticles()
    {

      foreach ($this->tire_tables as $tire_table => $tire_options) {
        $primary_key = app("App\\Models\\$tire_options[0]")->getKeyName();

        $products = DB::table($tire_table)->get();

        foreach ($products as $product) {
//          if ($product->$primary_key != '155300') continue;
          $product = app("App\\Models\\$tire_options[0]")->where($primary_key, $product->$primary_key)->first();
          $article = '';

          $sql = "SELECT ArticleId as ArtikulaId, Deleted FROM katdetal WHERE Deleted = 0 AND Artikuls = '" . $product->article . "'";
          $result = $this->accrual->query($sql);

          foreach ($result as $row) {
            $article = $row['ArtikulaId'];
          }

          $productInfo = $this->getAccrualInventory($product->article);
          if (isset($productInfo[$product->article])) {
            $total = intval($productInfo[$product->article]);
            $product->quantity = $total;

            $stores = $productInfo['_stores'][$product->article];

            if (isset($stores[1])) {
              $this->urs = $stores[1];
              $this->urs = str_replace('Noliktava: ', '', $this->urs);
              $product->urs_quantity = intval($this->urs);
            } else {
              $product->urs_quantity = 0;
            }
            if (isset($stores[2])) {
              $this->krs = $stores[2];
              $this->krs = str_replace('Veikals: ', '', $this->krs);
              $product->krs_quantity = intval($this->krs);
            } else {
              $product->krs_quantity = 0;
            }

            $product->updated_at = date('Y-m-d H:i:s');
            $product->save();
          } else {
            $product->quantity = 0;
            $product->urs_quantity = 0;
            $product->krs_quantity = 0;
            $product->updated_at = date('Y-m-d H:i:s');
            $product->save();
          }

          $sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.Deleted = 0 AND k.ArticleId = '" . $article . "'";
          //$sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.ArticleId = '141309'";
          $result = $this->accrual->query($sql);
          if ($result->rowCount()) {
            foreach ($result as $rows) {
              set_time_limit(0);
              $veikala_cena = (int) round(round($rows['Cena1'], 5) * 1.21);
              if ($rows['Deleted'] == 1) {
                $akcijas_cena = (int) round(round($rows['Cena3'], 5) * 1.21);
              } else {
                $akcijas_cena = (int)   round(round($rows['Cena'], 5) * 1.21);
              }
            }
            $product->price1 = $veikala_cena;
            $product->price2 = $akcijas_cena;
            $product->updated_at = date('Y-m-d H:i:s');
            $product->save();
          } else {
            $sql = "SELECT * FROM katalogs k WHERE Deleted = 0 AND k.ArticleId = '" . $article . "'";
            $result = $this->accrual->query($sql);
            if ($result->rowCount()) {
              foreach ($result as $rows) {
                set_time_limit(0);
                $veikala_cena = (int) round(round($rows['Cena1'], 5) * 1.21);
                $akcijas_cena = (int) round(round($rows['Cena3'], 5) * 1.21);
              }
              $product->price1 = $veikala_cena;
              $product->price2 = $akcijas_cena;
              $product->updated_at = date('Y-m-d H:i:s');
              $product->save();
            }
          }
        }
      }
    }

    public function updateStock($stock)
    {
      $stockCount = [];

      foreach ($stock as $id => $value) {

        $noliktavas = explode(';', $value);
        if (strpos(@$noliktavas[0], 'Noliktava') !== false) {
          $urs = @$noliktavas[0];
          $krs = @$noliktavas[1];
        } else if (strpos(@$noliktavas[0], 'Veikals') !== false) {
          $urs = @$noliktavas[1];
          $krs = @$noliktavas[0];
        }

        @$urs = explode(': ', $urs);
        @$urs_quantity = ((int) $urs[1] <= 0) ? 0 : (int) $urs[1];

        @$krs = explode(': ', $krs);
        @$krs_quantity = ((int) $krs[1] <= 0) ? 0 : (int) $krs[1];

        $total = $urs_quantity + $krs_quantity;

        foreach ($this->tire_tables as $tire_table => $tire_options) {

          $primary_key = app("App\\Models\\$tire_options[0]")->getKeyName();
          $product = DB::table($tire_table)->where($primary_key, $id)->first();

          if ($product) {

            $article = '';

            $sql = "SELECT ArticleId as ArtikulaId, Deleted FROM katdetal WHERE Deleted = 0 AND Artikuls = '" . $product->article . "'";
            //$sql = "SELECT ArticleId as ArtikulaId FROM katdetal WHERE Artikuls = '16205/55NHKPL1094TXL'";
            $result = $this->accrual->query($sql);

            foreach ($result as $row) {
              $article = $row['ArtikulaId'];
            }

            $sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.Deleted = 0 AND u.Deleted = 0 AND k.ArticleId = '" . $article . "'";
            //$sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.ArticleId = '141309'";
            $result = $this->accrual->query($sql);
            if ($result->rowCount()) {
              //var_dump(count($stockCount));
              DB::table($tire_table)->where($primary_key, $id)->update([
                'quantity' => $total,
                'urs_quantity' => @$urs_quantity,
                'krs_quantity' => @$krs_quantity,
                'updated_at' => date('Y-m-d H:i:s')
              ]);
            } else {
              $sql = "SELECT * FROM katalogs k WHERE Deleted = 0 AND k.ArticleId = '" . $article . "'";
              //$sql = "SELECT * FROM katalogs k WHERE k.ArticleId = '141309'";
              $result = $this->accrual->query($sql);
              //dd($result->rowCount());
              if ($result->rowCount()) {
                DB::table($tire_table)->where($primary_key, $id)->update([
                  'quantity' => $total,
                  'urs_quantity' => @$urs_quantity,
                  'krs_quantity' => @$krs_quantity,
                  'updated_at' => date('Y-m-d H:i:s')
                ]);
              } else {
                DB::table($tire_table)->where($primary_key, $id)->update([
                  'quantity' => $total,
                  'urs_quantity' => @$urs_quantity,
                  'krs_quantity' => @$krs_quantity,
                  'updated_at' => date('Y-m-d H:i:s')
                ]);
              }
            }

          }
        }
      }
    }

    //    public function updateStock($stock)
    //    {
    //        foreach ($stock as $id => $value) {
    //
    //            $noliktavas = explode(';', $value);
    //            $urs = @$noliktavas[0];
    //            $krs = @$noliktavas[1];
    //
    //            $urs = explode(': ', $urs);
    //            @$urs_quantity = (int) $urs[1];
    //
    //            $krs = explode(': ', $krs);
    //            @$krs_quantity = (int) $krs[1];
    //
    //            $product = Autotire::findOrFail($id);
    //            if ($product) {
    //                $product->timestamps = false;
    //                $product->quantity = $urs_quantity + $krs_quantity;
    //                $product->save();
    //            }
    //        }
    //    }

    public function getInventory($tire_tables, $article = null)
    {
      $map = $this->getAccrualIdToEntityIdMap($tire_tables, $article);

      //        dd($map);
      $inventory = $this->getAccrualInventory($article);

      //        dd($inventory);

      if ($article && !isset($inventory[$article])){
        $inventory = ['_stores' => [$article => ['0']], $article => 0];
      }

      $stores = $inventory['_stores'];
      unset($inventory['_stores']);
      $articles = array_keys($stores);
      $stores = array_combine($articles, array_map('implode', array_fill(0,count($stores),';'), $stores));

      //        dump($map, $inventory, $stores); die;

      $stock = $this->mapInventory($map, $inventory);
      $storestock = $this->mapInventory($map, $stores);

      //        dump($stock, $storestock);die;
      return [$articles, $stock, $storestock];
    }

    public function getAccrualIdToEntityIdMap($tire_tables, $article = null)
    {
      $result = [];

      foreach ($tire_tables as $tire_table => $tire_options) {

        if ($article) {
          $sql = DB::table($tire_table)->where('article', $article)->get();
        } else {
          $sql = DB::table($tire_table)->get();
        }

        foreach ($sql as $row) {
          $key = app("App\\Models\\$tire_options[0]")->getKeyName();
          array_push($result, ['tire_id' => $row->$key, 'article' => $row->article]);
        }

        //        Jāuztaisa masīvs - [
        //        [
        //              'tire_id' => $tire_id,
        //              'accrual_id' => $accrual_id
        //        ]

      }
      $mapped = array_column($result, 'article', 'tire_id');

      return $mapped;
    }

    public function mapInventory($map, $inventory) {
      $stock = [];

      foreach($map as $entity_id => $accrual_id) {
        if(array_key_exists($accrual_id, $inventory)) {
          $stock[$entity_id] = $inventory[$accrual_id];
        }
      }

      return $stock;
    }

    public function getAccrualInventory($article = null) {

      $stores = $this->getAccrualStores();

      //        $article = '15215/70DECONODRIVE109SC';
      $sql = "SELECT k.Artikuls, a.Atlikums, a.Rezervets, (a.Atlikums - a.Rezervets) AS atl_min_rez, a.StorId
        FROM atlikumi a INNER JOIN katdetal k ON (k.ArticleId = a.ArticleId) WHERE a.FrFirmId = 1";

      if($article) $sql .= " AND k.Artikuls = '$article'";

      $result = $this->accrual->query($sql);

      $inventory = ['_stores'=>[]];

      foreach ($result as $row) {

        if($row['StorId'] == 0) {
          $inventory[$row['Artikuls']] = $row['atl_min_rez'];
        }
        else {
          $storId = $row['StorId'];

          if(!isset($inventory['_stores'][$row['Artikuls']])) $inventory['_stores'][$row['Artikuls']] = array();

          if(isset($stores[$storId])) $inventory['_stores'][$row['Artikuls']][(int)$storId] = $stores[$storId] . ': '. $row['atl_min_rez'];
        }
      }

      //    dd($inventory);

      return $inventory;
    }

    public function getAccrualStores() {

      $sql = "SELECT StorId, Nosaukums FROM unobjekti WHERE Deleted = 0 AND Veids = 1;";

      $result = $this->accrual->query($sql);
      $stores = [];

      foreach ($result as $row) {
        if (strpos($row['Nosaukums'], 'Noliktava') !== false || strpos($row['Nosaukums'], 'Veikals') !== false) {
          $stores[$row['StorId']] = $row['Nosaukums'];
        }
      }

      return $stores;
    }

    // Lattako sync

    public function i3auto()
    {

      $sync = DB::table('sync_times')->where('name', 'i3-auto')->get();
      $sync_time = \Carbon\Carbon::parse($sync[0]->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        $token_url = "api.latakko.eu/Token";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $token_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
          $token = json_decode($response);
        } else {
          throw new \Exception($err);
        }

        $token_bearer = $token->access_token;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.latakko.eu/api/Articles?OnlyStockItems',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '/xml/i3-articles.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Autostock::where('itype', 'i3')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '/xml/i3-articles.txt');
      $content = json_decode($content);

      $out = '';

      foreach ($content as $item) {

        $counted++;

        $stock = Autostock::where('itype', 'i3')->where('article', $item->ArticleId)->orderBy('created_at', 'DESC')->first();
        if (!$stock) {
          continue;
        }

        $quantity = intval($item->QuantityAvailable);
        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';
        $stock->quantity = $quantity;
        $stock->metadata = $metadata;
        if ($stock->save()) {
          $updated++;
        }

      }

      DB::table('sync_times')->where('name', 'i3-auto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";


      //        $stocks = Autostock::where('itype', 'i3')->get();
      //        foreach ($stocks as $stock) {
      //
      //          $counted++;
      //
      //          if (!$err) {
      //            $metadata = '';
      //            $response = json_decode($response);
      //            $metadata = '';
      //            $response = (object) $response;
      //            if (empty(get_object_vars($response)) || isset($response->Message)) continue;
      //            echo '<br><pre>';
      //            var_dump($response);
      //            echo '</pre><br>';
      //            var_dump(get_object_vars($response));
      //            $stock->quantity = intval($response->QuantityAvailable);
      //            $stock->metadata = $metadata;
      //            if ($stock->save()) {
      //              $updated++;
      //            }

      //            if (is_null($response)) {
      //              var_dump($stock->);
      //            }
      //          } else {
      //            throw new \Exception($err);
      //          }
      //
      //
      //        }

      //        $url = "https://api.gummigrossen.se/api/Tyres?username=XmL_r1&password=M20h:2|5";
      //
      //        $opts = ['http' =>
      //            [
      //                'method'  => 'GET',
      //                'timeout'  => 600,
      //            ]
      //        ];
      //
      //        set_time_limit(800);
      //
      //        $context  = stream_context_create($opts);
      //        $xmlString = file_get_contents($url, false, $context);
      //
      //        file_put_contents('i3.auto.xml',$xmlString);
      //
      //        $xml = simplexml_load_string($xmlString);
      //
      //        unset($context);
      //
      //        echo "Auto riepas<br>";
      //        Autostock::where('itype', 'i3')->update(['quantity' => 0]);
      //
      //        $updated = 0;
      //        $counted = 0;
      //        foreach ($xml->Item as $item){
      //            $article = $item->stockcode;
      //            $quantity = intval($item->qty_available);
      //
      //            $metadata = '';
      //            $price = @$item->price; if ($price!='') $metadata.='price: '.$price.'; ';
      //            $pkpcena = @$item->pkpcena; if ($pkpcena!='') $metadata.='pkpcena: '.$pkpcena.'; ';
      //            $baseprice = @$item->Baseprice; if ($baseprice!='') $metadata.='Baseprice: '.$baseprice.'; ';
      //
      //            $list = Autostock::where('article', $article)->where('itype', 'i3')->get();
      //
      //            foreach ($list as $itam){
      //                $itam->quantity = $quantity;
      //                $itam->metadata = $metadata;
      //                $itam->save();
      //                $updated++;
      //            }
      //            $counted++;
      //        }
      //        DB::table('sync_times')->where('name', 'i3-auto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      //        echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

    public function i3autoalloyrims()
    {

      set_time_limit(0);

      $sync = DB::table('sync_times')->where('name', 'i3-alloy-rims')->get();
      $sync_time = \Carbon\Carbon::parse($sync[0]->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        if (!isset($_COOKIE['i3-token'])) {
          $token_url = "api.latakko.eu/Token";
//        $token_url = "api.latakko.eu/Token";

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/x-www-form-urlencoded"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if (!$err)
          {
            $token = json_decode($response);
          } else {
            dd($err);
          }

          setcookie('i3-token', $token->access_token, time() + $token->expires_in, '/');
          $token_bearer = $token->access_token;
        } else {
          $token_bearer = $_COOKIE['i3-token'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.latakko.eu/api/Articles?IncludeCarTyres=false&IncludeMotorcycleTyres=false&IncludeTruckTyres=false&IncludeEarthmoverTyres=false&IncludeAlloyRims=true&OnlyLocalStockItems=true',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '/public/storage/xml/i3-alloy-rims-articles.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Rimstock::where('itype', 'i3')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '/public/storage/xml/i3-alloy-rims-articles.txt');
      $content = json_decode($content);

      $returnText = '';

      foreach ($content as $item) {

        $counted++;

        if (!$item->NumberOfBolts || !$item->BoltCircle || !$item->Diameter || !$item->RetailPrice) continue;

        $rim = Rim::where('article', $item->ArticleId)->first();
        $newRim = false;

        if ($rim == null) {
          $newRim = true;
          $rim = new Rim;
        }



        $rim->timestamps = false;

        $imageId = $item->ImageId;

        $brand = Rimbrand::where('title', $item->BrandName)->first();
        $tread = Rimmake::where('title', $item->PatternModelText)->first();

        if ($brand === null) {
          $brand = new Rimbrand;
          $brand->timestamps = false;
          $brand->title = $item->BrandName;
          $brand->slug = Str::slug($brand->title);
          $brand->save();
        }

        if ($tread === null) {
          $tread = new Rimmake;
          $tread->timestamps = false;
          $tread->brand_id = $brand->brand_id;
          $tread->title = $item->PatternModelText;
          $tread->slug = Str::slug($tread->title);
          $tread->save();
        } else {
          if ($tread->brand_id != $brand->brand_id) {
            $tread = new Rimmake;
            $tread->timestamps = false;
            $tread->brand_id = $brand->brand_id;
            $tread->title = $item->PatternModelText;
            $tread->slug = Str::slug($tread->title);
            $tread->save();
          }
        }

        $treadId = $tread->make_id;

        $quantity = intval($item->QuantityAvailable);
        if ($imageId != null) {
          $outPath = dirname(__DIR__, 3) . '/public/storage/rims/tread/' . $treadId . '-o.jpg';

          if (!file_exists($outPath)) {
            Self::grab_image('https://api.latakko.eu/api/ArticleImages/' . $imageId, $outPath);
          }
        }

        $rim->make_id = $treadId;
        $rim->d1 = $item->Width;
        $rim->d3 = $item->Diameter;
        $rim->dc = $item->CenterBore;
        $rim->used = 0;
        $rim->price1 = ceil((round(($item->NetPrice * 1.21), 2) + 15) / 0.7);
        $rim->price2 = $item->Price;
        $rim->price3 = floor(round($item->RetailPrice * 1.21, 2));
        $rim->offer = 0;
        $rim->priceOffer = 0;
        if ($newRim == true) {
          $rim->comment = '';
          if ($quantity >= 4) {
            $rim->visible_users = 1;
            $rim->visible_list = 1;
          } else {
            $rim->visible_users = 0;
            $rim->visible_list = 0;
          }
        } else {
          if ($quantity < 4) {
            $rim->visible_users = 0;
            $rim->visible_list = 0;
          }
        }
        $rim->available = 0;
        $rim->skr = $item->NumberOfBolts;
        $rim->pcd = $item->BoltCircle;
        $rim->et = $item->Offset;
        $rim->color = $item->Color;
        $rim->article = $item->ArticleId;
        $rim->quantity = 0;
        $rim->urs_quantity = 0;
        $rim->krs_quantity = 0;
        $rim->ordered = 0;
        $rim->reserved = 0;
        $rim->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $rim->save();

        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';

        $stock = Rimstock::where('rim_id', $rim->rim_id)->first();

        if ($stock == null) $stock = new Rimstock;
        $stock->rim_id = $rim->rim_id;
        $stock->article = $rim->article;
        $stock->quantity = $quantity;
        $rimVisible = Rim::where('article', $stock->article)->first();
        if (!is_null($rimVisible)) {
          if ($quantity > 0) {
            if ($quantity > 4) {
              $rimVisible->visible_users = 1;
              $rimVisible->visible_list = 1;
            } else {
              $rimVisible->visible_users = 0;
              $rimVisible->visible_list = 0;
            }
          } else {
            $rimVisible->visible_users = 0;
            $rimVisible->visible_list = 0;
          }
        }
        $stock->itype = 'i3';
        $stock->metadata = $metadata;
        $rimVisible->save();
        if ($stock->save()) {
          $updated++;
        }
        $counted++;

      }

      DB::table('sync_times')->where('name', 'i3-alloy-rims')->update(['updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

    public function i3show()
    {
      echo 'Auto riepas:<br>';
      $stocks = Autostock::where('itype', 'i3')->get();
      foreach ($stocks as $stock) {
        $tire = Autotire::where('tire_id', $stock->tire_id)->first();
        if (!$tire) continue;
        $text = $tire->title . ' ' . $tire->li . $tire->si . ' ' .( $tire->fullSize) . ' [' . $tire->article . ']:[' . $stock->article . ']: ' . $stock->quantity . ' / ' . $stock->metadata . '<br>';
        //dd($text);
        echo $text;
      }
      echo '<br>';

      echo 'Moto riepas:<br>';
      $stocks = Motostock::where('itype', 'i3')->get();
      foreach ($stocks as $stock) {
        $tire = Moto::where('tire_id', $stock->tire_id)->first();
        if (!$tire) continue;
        $text = $tire->title . ' ' . $tire->li . $tire->si . ' ' .( $tire->fullSize) . ' [' . $tire->article . ']:[' . $stock->article . ']: ' . $stock->quantity . ' / ' . $stock->metadata . '<br>';
        //dd($text);
        echo $text;
      }
      echo '<br>';

      echo 'Kvadru riepas:<br>';
      $stocks = Quadrstock::where('itype', 'i3')->get();
      foreach ($stocks as $stock) {
        $tire = Quadr::where('tire_id', $stock->tire_id)->first();
        if (!$tire) continue;
        $text = $tire->title . ' ' . $tire->li . $tire->si . ' ' .( $tire->fullSize) . ' [' . $tire->article . ']:[' . $stock->article . ']: ' . $stock->quantity . ' / ' . $stock->metadata . '<br>';
        //dd($text);
        echo $text;
      }
      echo '<br>';
    }

    public function i3moto()
    {

      $sync = DB::table('sync_times')->where('name', 'i3-moto')->get();
      $sync_time = \Carbon\Carbon::parse($sync[0]->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        $token_url = "api.latakko.eu/Token";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $token_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
          $token = json_decode($response);
        } else {
          throw new \Exception($err);
        }

        $token_bearer = $token->access_token;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.latakko.eu/api/Articles?OnlyStockItems',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '/xml/i3-articles.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Motostock::where('itype', 'i3')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '/xml/i3-articles.txt');
      $content = json_decode($content);

      $out = '';

      foreach ($content as $item) {

        $counted++;

        $stock = Motostock::where('itype', 'i3')->where('article', $item->ArticleId)->orderBy('created_at', 'DESC')->first();
        if (!$stock) {
          continue;
        }

        $quantity = intval($item->QuantityAvailable);
        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';
        $stock->quantity = $quantity;
        $stock->metadata = $metadata;
        if ($stock->save()) {
          $updated++;
        }

      }

      DB::table('sync_times')->where('name', 'i3-moto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function i3quadr()
    {

      $sync = DB::table('sync_times')->where('name', 'i3-quadr')->get();
      $sync_time = \Carbon\Carbon::parse($sync[0]->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        $token_url = "api.latakko.eu/Token";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $token_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
          $token = json_decode($response);
        } else {
          throw new \Exception($err);
        }

        $token_bearer = $token->access_token;

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.latakko.eu/api/Articles?OnlyStockItems',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '/xml/i3-articles.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Quadrstock::where('itype', 'i3')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '/xml/i3-articles.txt');
      $content = json_decode($content);

      $out = '';

      foreach ($content as $item) {

        $counted++;

        $stock = Quadrstock::where('itype', 'i3')->where('article', $item->ArticleId)->orderBy('created_at', 'DESC')->first();
        if (!$stock) {
          continue;
        }

        $quantity = intval($item->QuantityAvailable);
        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';
        $stock->quantity = $quantity;
        $stock->metadata = $metadata;
        if ($stock->save()) {
          $updated++;
        }

      }

      DB::table('sync_times')->where('name', 'i3-quadr')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function duellmoto()
    {

      $url = 'ftp://duellus:WebUpdate!@updateftp.duell.fi/ic.TXT';

      $opts = ['ftp' => []];

      $context = stream_context_create($opts);

      $xmlString = file_get_contents($url, false, $context);

      $lines = explode("\r", $xmlString);
      $articles = [];
      foreach ($lines as $idx => $line) {
        if ($idx > 0) {
          $lineData = explode("\t", trim($line));
          if ((@$lineData[0] != '') && (is_numeric(@$lineData[3]))) {
            $articles[trim($lineData[0])] = trim($lineData[3]);
          }
        }
      }

      unset($context);

      echo "Moto riepas<br>";
      Motostock::where('itype', 'duell')->update(['quantity' => 0]);

      $updated = 0;
      $counted = 0;

      foreach ($articles as $item => $amount) {
        $article = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        $quantity = intval($amount);

        $list = Motostock::where('article', $article)->where('itype', 'duell')->get();

        foreach ($list as $itam){
          $itam->quantity = $quantity;
          $itam->save();
          $updated++;
        }
        $counted++;
      }
      DB::table('sync_times')->where('name', 'duell-moto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

    public function duellquadr()
    {

      $url = 'ftp://duellus:WebUpdate!@updateftp.duell.fi/ic.TXT';

      $opts = ['ftp' => []];

      $context = stream_context_create($opts);

      $xmlString = file_get_contents($url, false, $context);

      $lines = explode("\r", $xmlString);
      $articles = [];
      foreach ($lines as $idx => $line) {
        if ($idx > 0) {
          $lineData = explode("\t", trim($line));
          if ((@$lineData[0] != '') && (is_numeric(@$lineData[3]))) {
            $articles[trim($lineData[0])] = trim($lineData[3]);
          }
        }
      }

      unset($context);

      echo "Kvadraciklu riepas<br>";
      Quadrstock::where('itype', 'duell')->update(['quantity' => 0]);

      $updated = 0;
      $counted = 0;

      foreach ($articles as $item => $amount) {
        $article = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        $quantity = intval($amount);

        $list = Quadrstock::where('article', $article)->where('itype', 'duell')->get();

        foreach ($list as $itam){
          $itam->quantity = $quantity;
          $itam->save();
          $updated++;
        }
        $counted++;
      }
      DB::table('sync_times')->where('name', 'duell-quadr')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

//    public function i3big()
//    {
//      set_time_limit(0);
//
//      $sync = DB::table('sync_times')->where('name', 'i3-big')->get();
//      $sync_time = \Carbon\Carbon::parse($sync[0]->updated_at)->addHour();
//      $time_now = \Carbon\Carbon::now();
//      if ($time_now->diff($sync_time)->invert == 1) {
//        if (!isset($_COOKIE['i3-token'])) {
//          $token_url = "gd-api-test.barnstenit.se/Token";
////        $token_url = "api.latakko.eu/Token";
//
//          $curl = curl_init();
//          curl_setopt_array($curl, array(
//            CURLOPT_URL => $token_url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
//            CURLOPT_HTTPHEADER => array(
//              "cache-control: no-cache",
//              "content-type: application/x-www-form-urlencoded"
//            ),
//          ));
//          $response = curl_exec($curl);
//          $err = curl_error($curl);
//
//          curl_close($curl);
//
//          if (!$err)
//          {
//            $token = json_decode($response);
//          } else {
//            dd($err);
//          }
//
//          setcookie('i3-token', $token->access_token, time() + $token->expires_in, '/');
//          $token_bearer = $token->access_token;
//        } else {
//          $token_bearer = $_COOKIE['i3-token'];
//        }
//
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//          CURLOPT_URL => 'https://gd-api-test.barnstenit.se/api/Articles?IncludeCarTyres=false&IncludeMotorcycleTyres=false&IncludeTruckTyres=true&IncludeEarthmoverTyres=false&OnlyLocalStockItems=true',
//          CURLOPT_RETURNTRANSFER => true,
//          CURLOPT_ENCODING => "",
//          CURLOPT_MAXREDIRS => 10,
//          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//          CURLOPT_CUSTOMREQUEST => "GET",
//          CURLOPT_HTTPHEADER => array(
//            "cache-control: no-cache",
//            "authorization: Bearer " . $token_bearer,
//          ),
//        ));
//        $response = curl_exec($curl);
//
//        $filename = dirname(__DIR__, 3) . '/public/storage/xml/i3-industrial.txt';
//
//        file_put_contents($filename, $response);
//        chmod($filename, 0775);
//
//        $err = curl_error($curl);
//
//        if ($err) throw new \Exception($err);
//
//        curl_close($curl);
//      }
//
//      $counted = 0;
//      $updated = 0;
//
//      Bigstock::where('itype', 'i3')->update(['quantity' => 0]);
//
//      $content = file_get_contents(dirname(__DIR__, 3) . '/public/storage/xml/i3-industrial.txt');
//      $content = json_decode($content);
//
//      $out = '';
//
//      foreach ($content as $item){
//
//        dd($item);
//
//        $item = json_encode($item);
//        $item = (object) json_decode($item, TRUE);
//
//        $type = $item->aplication;
//
//        if (empty($item->description)) {
//          continue;
//        }
//
//        if ($type === 'Bus/Truck' || $type === 'Truck' || $type === 'Bus') {
//
//          $article = $item->stockcode;
//
//          $price = round($item->price);
//
//          if ($price != 0) {
//            if ($price < 100) {
//              $price1 = ($price + 8) / 70 * 100;
//              $price2 = $price + 10;
//            }
//            if ($price >= 100 && $price < 200) {
//              $price1 = ($price + 12) / 70 * 100;
//              $price2 = $price + 15;
//            }
//            if ($price >= 200) {
//              $price1 = ($price + 15) / 70 * 100;
//              $price2 = $price + 20;
//            }
//          }
//
//          $price1 = round($price1);
//          $price2 = round($price2);
//
//          $d1 = $item->width;
//          $d2 = $item->profile;
//          $d3 = $item->diameter;
//
//          if (is_array($item->description)) {
//            continue;
//          }
//          $sizes = preg_split('/ /', $item->description)[0];
//          $sizes = SyncController::multiexplode([$d1, $d2, $d3], $sizes);
//          $sizes = array_values(array_filter($sizes));
//
//          if (count($sizes) < 0) {
//            continue;
//          }
//
//          for ($x = 0; $x < count($sizes); $x++) {
//            $sepNr = $x + 1;
//            ${"sep$sepNr"} = $sizes[$x];
//          }
//
//          $brand = $item->brand;
//          $brand = str_replace(' (KRAVAS)', '', $brand);
//          $brand = str_replace(' (COACH)', '', $brand);
//          $brand = ucfirst(strtolower($brand));
//          $tread = $item->protector;
//
//          if (strpos($brand, 'RIEPAS dažādas') !== false ||
//            strpos($brand, 'Atjaunotas') !== false ||
//            strpos($brand, 'Riepas daŽĀdas') !== false) {
//            $brand = '';
//            $tread = '';
//          }
//
////          if ($item->stockcode !== '385652251417943058TT0R0') continue;
//
//          $lisi = $item->li_si;
//          if (is_array($lisi)) {
//            continue;
//          }
//          if (preg_match('/ [\d]+PR/', $lisi)) {
//            $lisi = preg_replace('/ [0-9]+PR/', '', $lisi);
//          }
//          if (preg_match('/[\d]+PR /', $lisi)) {
//            $lisi = preg_replace('/[\d]+PR /', '', $lisi);
//          }
//
//          if (str_word_count($lisi) > 1) {
//            $lisi = preg_replace("/\([^)]+\)/","",$lisi);
//            $lisi = SyncController::multiexplode([' ', '/'], $lisi);
//          } else {
//            if (preg_match("/([\d]+[a-zA-Z]+)/i", $lisi)) {
//              if (strpos($lisi, '/') !== false) {
//                $lisi = explode('/', $lisi);
//                $si = preg_replace('/[\d]+/', '', $lisi[1]);
//              }
//            }
//          }
//          if (is_array($lisi)) {
//            $li = $lisi[0];
//            if (preg_match('/[a-zA-Z]/i', $li[0])) {
//              $lisi = preg_split('/(?<=[a-zA-Z])/i', $li);
//              $li = $lisi[1];
//              if (!isset($si)) {
//                $si = $lisi[0];
//              }
//            } else {
//              if (isset($lisi[1])) {
//                if (preg_match('/[a-zA-Z]/i', $lisi[1])) {
//                  $si = preg_replace('/[\d]+/i', '', $lisi[1]);
//                }
//                $li = $lisi[0];
//              } else {
//                $lisi = preg_split('/(?=[a-zA-Z])/i', $lisi[0]);
//                $li = $lisi[0];
//                $si = $lisi[1];
//              }
//            }
//          }
//
//          $position = SyncController::getByArticle($article);
//          if ($position === false) {
//            $position = new Bigtire();
//          }
//
//          $returnText = '';
//
//          if (!empty($brand) && !empty($tread)) {
//            $treadId = SyncController::getTreadId($tread, $brand);
//            // Jauns breands - Bigtire_brands
//            $returnText .= 'Jauns brends - ' . $brand . '<br>';
//            if ($treadId === false) {
//              $brandId = SyncController::getBrandId($brand);
//              if ($brandId === false) {
//                $brandId = Bigbrand::insertGetId([
//                  'title' => $brand,
//                  'slug' => Str::slug($brand),
//                ]);
//              }
//              // Jauns protektors - Bigtire_treads
//              $returnText .= 'Jauns protektors - ' . $tread . '<br>';
//              $treadId = Bigtread::insertGetId([
//                'brand_id' => $brandId,
//                'title' => $tread,
//                'slug' => Str::slug($tread),
//              ]);
//            }
//          }
//
//          $position->make_id = $treadId;
//
//          $image = $item->image;
//
//          $outPath = dirname(__DIR__, 3) . '/storage/app/public/industrial/tread/';
//
//          @$image = file_get_contents('http://i3.lattako.lv/images/tyres/' . $image . '-o.jpg');
//          $new_image = $outPath . $treadId . '.jpg';
//
//          if (trim($image) !== false) {
//            file_put_contents($new_image, $image);
//          } else {
//            echo 'Neeksistē - Artikuls (' . $article . ')';
//          }
//
//          $position->d1 = $d1;
//          $position->sep = $sep1;
//          if (is_array($d2)) {
//            $position->d2 = null;
//            $position->sep2 = null;
//            $position->d3 = $d3;
//          } else {
//            $position->d2 = $d2;
//            $position->sep2 = $sep2;
//            $position->d3 = $d3;
//          }
//
//          if ($type === 'Truck') {
//            $type = str_replace('Truck', 'Kravas', $type);
//          } else if ($type === 'Buss') {
//            $type = str_replace('Buss', 'Autobuss', $type);
//          } else if ($type === 'Bus/Truck') {
//            $type = str_replace('Bus/Truck', 'Autobuss/Kravas', $type);
//          }
//
//          $position->type = 'TRUCK';
//          $position->li = $li;
//          $position->si = $si;
//          $position->price1 = $price1;
//          $position->price2 = $price2;
//          $position->implemention = $type;
//          $position->kind = null;
//          (empty($item->buss_possition)) ? $position->axis_bus = null : $position->axis_bus = $item->buss_possition;
//          (empty($item->truck_possition)) ? $position->axis_truck = null : $position->axis_truck = $item->truck_possition;
//          (empty($item->road_for_Buss)) ? $position->conditions_bus = null : $position->conditions_bus = $item->road_for_Buss;
//          (empty($item->road_for_trucks)) ? $position->conditions_truck = null : $position->conditions_truck = $item->road_for_trucks;
//          $position->offer = null;
//          $position->priceoffer = null;
//          $position->comment = null;
//          if ($item->qty_available > 0) {
//            if (!empty($d1) && !empty($d2) && !empty($d3) || !empty($d1) && empty($d2) && !empty($d3)) {
//              $position->visible_users = 1;
//              $position->visible_list = 1;
//            } else {
//              $position->visible_users = 0;
//              $position->visible_list = 0;
//            }
//          } else {
//            $position->visible_users = 0;
//            $position->visible_list = 0;
//          }
//
//          $position->available = 1;
//          $position->article = $article;
//
//          $position->save();
//
//          if ($article !== '') {
//            $position->addSecondaryArticle($article, 'i3');
//          }
//
//          $lists = Bigstock::where('article', $article)->where('itype', 'i3')->get();
//
//          foreach ($lists as $list) {
//            $list->update(['quantity' => $item->qty_available, 'updated_at' => date('Y-m-d H:i:s')]);
//            $updated++;
//          }
//
//          $counted++;
//
//        }
//
//      }
//      DB::table('sync_times')->where('name', 'i3-big')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
//      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
//    }

    public function getBigSizes($tire)
    {
      $fullSize = [];

      $title = explode(' ', $tire->ArticleText)[0];
      $title = str_replace(',', '.', $title);

      if ($tire->Radial == true) {
        if (strpos($title, 'R') === true) return false;
        $delimiters = ['/', 'R', '-'];
        $pattern = '/(' . implode('|', array_map(function($delimiter) {
            return preg_quote($delimiter, '/');
          }, $delimiters)) . ')/';
        $parts = preg_split($pattern, $title, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (count($parts) == 5) {
          $d1 = sprintf('%g', $parts[0]);
          $sep1 = (is_string($parts[1])) ? strtolower($parts[1]) : $parts[1];
          $d2 = sprintf('%g', $parts[2]);
          $sep2 = $parts[3];
          $d3 = (fmod($parts[4], 1) === 0.0) ? (int) $parts[4] : $parts[4];
        } else if (count($parts) == 3) {
          if (preg_match("/[a-zA-Z]/i", $parts[0])){
            $parts[0] = str_replace('L', '', $parts[0]);
            $d1 = sprintf('%g', $parts[0]);
            $d1 = $d1 . 'L';
          } else {
            $d1= sprintf('%g', $parts[0]);
          }
          $sep1 = (is_string($parts[1])) ? strtolower($parts[1]) : $parts[1];
          $d2 = null;
          $sep2 = null;
          $d3 = (fmod($parts[2], 1) === 0.0) ? (int) $parts[2] : $parts[2];
        }
      } else {
        $delimiters = ['/', 'x', 'X', '-'];
        $pattern = '/(' . implode('|', array_map(function($delimiter) {
            return preg_quote($delimiter, '/');
          }, $delimiters)) . ')/';
        $parts = preg_split($pattern, $title, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (count($parts) == 5) {
          $d1 = sprintf('%g', $parts[0]);
          $sep1 = (is_string($parts[1])) ? strtolower($parts[1]) : $parts[1];
          $d2 = sprintf('%g', $parts[2]);
          $sep2 = $parts[3];
          $d3 = sprintf('%g', $parts[4]);
        } else if (count($parts) == 3) {
          if (preg_match("/[a-zA-Z]/i", $parts[0])){
            $parts[0] = str_replace('L', '', $parts[0]);
            $d1 = sprintf('%g', $parts[0]);
            $d1 = $d1 . 'L';
          } else {
            $d1 = sprintf('%g', $parts[0]);
          }
          $sep1 = (is_string($parts[1])) ? strtolower($parts[1]) : $parts[1];
          $d2 = null;
          $sep2 = null;
          $d3 = (fmod($parts[2], 1) === 0.0) ? (int) $parts[2] : $parts[2];
        }
      }

      $fullSize['d1'] = $d1;
      $fullSize['sep1'] = $sep1;
      $fullSize['d2'] = $d2;
      $fullSize['sep2'] = $sep2;
      $fullSize['d3'] = $d3;

      return $fullSize;
    }

    public function getAgroPr($tire)
    {
      $returnText = '';

      $params = explode(' ', $tire);
      foreach ($params as $param) {
        if (stripos($param, 'PR') !== false) {
          $returnText = preg_replace('~\D~', '', $param);
        } else continue;
      }

      return $returnText;
    }

    public function i3agro()
    {
      set_time_limit(0);

      $sync = DB::table('sync_times')->where('name', 'i3-agro')->first();
      $sync_time = \Carbon\Carbon::parse($sync->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        if (!isset($_COOKIE['i3-token'])) {
//        $token_url = "api.latakko.eu/Token";

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => env('I3_TOKEN_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/x-www-form-urlencoded"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if (!$err)
          {
            $token = json_decode($response);
          } else {
            dd($err);
          }

          setcookie('i3-token', $token->access_token, time() + $token->expires_in, '/');
          $token_bearer = $token->access_token;
        } else {
          $token_bearer = $_COOKIE['i3-token'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => env('I3_AGRO_URL'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '\\public\\storage\\xml\\i3-agro.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Bigstock::where('itype', 'i3')->where('type', 'agro')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '\\public\\storage\\xml\\i3-agro.txt');
      $content = json_decode($content);

      $returnText = '';

      foreach ($content as $item) {

        $counted++;

        $tire = Bigtire::where('article', $item->ArticleId)->first();
        $newTire = false;

        if ($tire == null) {
          $newTire = true;
          $tire = new Bigtire();
        }

        $tire->timestamps = false;

        $imageId = $item->ImageId;

        $brand = Bigbrand::where('title', $item->BrandName)->first();
        $tread = Bigtread::where('title', $item->PatternModelText)->first();


        if ($brand === null) {
          $brand = new Bigbrand;
          $brand->timestamps = false;
          $brand->title = $item->BrandName;
          $brand->slug = Str::slug($brand->title);
          $brand->save();
        }

        if ($tread === null) {
          $tread = new Bigtread;
          $tread->timestamps = false;
          $tread->brand_id = $brand->brand_id;
          $tread->title = $item->PatternModelText;
          $tread->slug = Str::slug($tread->title);
          $tread->save();
        } else {
          if ($tread->brand_id != $brand->brand_id) {
            $tread = new Bigtread;
            $tread->timestamps = false;
            $tread->brand_id = $brand->brand_id;
            $tread->title = $item->PatternModelText;
            $tread->slug = Str::slug($tread->title);
            $tread->save();
          }
        }

        $treadId = $tread->tread_id;

        $quantity = intval($item->QuantityAvailable);
        if ($imageId != null) {
          $outPath = dirname(__DIR__, 3) . '/public/storage/industrial/tread/' . $treadId . '-o.jpg';

          if (!file_exists($outPath)) {
            Self::grab_image(env('I3_IMAGE_URL') . $imageId, $outPath);
          }
        }


        $sizes = $this->getBigSizes($item);

        $pr = $this->getAgroPr($item->ArticleText);

        $tire->make_id = $treadId;
        $tire->d1 = $sizes['d1'];
        $tire->sep = $sizes['sep1'];
        $tire->d2 = $sizes['d2'];
        $tire->sep2 = $sizes['sep2'];
        $tire->d3 = $sizes['d3'];
        $tire->type = 'AGRO';
//        $tire->type = ($item->MainGroupName) ? 'AGRO' : 'IND';
        $tire->li = ($item->LoadIndex !== null) ? $item->LoadIndex : null;
        $tire->si = ($item->SpeedIndex !== null) ? $item->SpeedIndex : null;
        $tire->code = $pr; // PR
        $tire->price1 = ceil((round(($item->NetPrice * 1.21), 2) + 15) / 0.7);
        $tire->price2 = $item->Price;
        $tire->price3 = floor(round($item->RetailPrice * 1.21, 2));
        $tire->implemention = null;
        $tire->kind = null;
        $positionText = $item->PositionText;
        $parts = preg_split('/(?=[A-Z])/', $positionText);
        $positionText = implode(' ', $parts);
        $tire->axis = $positionText;
        $tire->conditions = null;
        $tire->visible_users = 1;
        $tire->visible_list = 1;
        $tire->available = 0;
        $tire->article = $item->ArticleId;
        $tire->quantity = 0;
        $tire->urs_quantity = 0;
        $tire->krs_quantity = 0;
        $tire->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $tire->save();

        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';

        $stock = Bigstock::where('tire_id', $tire->tire_id)->first();

        if ($stock == null) $stock = new Bigstock();
        $stock->tire_id = $tire->tire_id;
        $stock->article = $tire->article;
        $stock->quantity = $quantity;
//        $tireVisible = Bigtire::where('article', $stock->article)->first();
//        if (!is_null($tireVisible)) {
//          if ($quantity > 0) {
//            if ($quantity > 4) {
//              $tireVisible->visible_users = 1;
//              $tireVisible->visible_list = 1;
//            } else {
//              $tireVisible->visible_users = 0;
//              $tireVisible->visible_list = 0;
//            }
//          } else {
//            $tireVisible->visible_users = 0;
//            $tireVisible->visible_list = 0;
//          }
//        }
        $stock->itype = 'i3';
        $stock->type = 'agro';
        $stock->metadata = $metadata;
//        $tireVisible->save();
        if ($stock->save()) {
          $updated++;
        }
        $counted++;

      }

      DB::table('sync_times')->where('name', 'i3-agro')->update(['updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function i3big()
    {
      set_time_limit(0);

      $sync = DB::table('sync_times')->where('name', 'i3-big')->first();
      $sync_time = \Carbon\Carbon::parse($sync->updated_at)->addHour();
      $time_now = \Carbon\Carbon::now();
      if ($time_now->diff($sync_time)->invert == 1) {
        if (!isset($_COOKIE['i3-token'])) {
//        $token_url = "api.latakko.eu/Token";

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => env('I3_TOKEN_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/x-www-form-urlencoded"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if (!$err)
          {
            $token = json_decode($response);
          } else {
            dd($err);
          }

          setcookie('i3-token', $token->access_token, time() + $token->expires_in, '/');
          $token_bearer = $token->access_token;
        } else {
          $token_bearer = $_COOKIE['i3-token'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => env('I3_TRUCK_URL'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "authorization: Bearer " . $token_bearer,
          ),
        ));
        $response = curl_exec($curl);

        $filename = dirname(__DIR__, 3) . '\\public\\storage\\xml\\i3-industrial.txt';

        file_put_contents($filename, $response);
        chmod($filename, 0775);

        $err = curl_error($curl);

        if ($err) throw new \Exception($err);

        curl_close($curl);
      }

      $counted = 0;
      $updated = 0;

      Bigstock::where('itype', 'i3')->where('type', 'truck')->update(['quantity' => 0]);

      $content = file_get_contents(dirname(__DIR__, 3) . '\\public\\storage\\xml\\i3-industrial.txt');
      $content = json_decode($content);

      $returnText = '';

      foreach ($content as $item) {

        $counted++;

        $tire = Bigtire::where('article', $item->ArticleId)->first();
        $newTire = false;

        if ($tire == null) {
          $newTire = true;
          $tire = new Bigtire();
        }

        $tire->timestamps = false;

        $imageId = $item->ImageId;

        $brand = Bigbrand::where('title', $item->BrandName)->first();
        $tread = Bigtread::where('title', $item->PatternModelText)->first();


        if ($brand === null) {
          $brand = new Bigbrand;
          $brand->timestamps = false;
          $brand->title = $item->BrandName;
          $brand->slug = Str::slug($brand->title);
          $brand->save();
        }

        if ($tread === null) {
          $tread = new Bigtread;
          $tread->timestamps = false;
          $tread->brand_id = $brand->brand_id;
          $tread->title = $item->PatternModelText;
          $tread->slug = Str::slug($tread->title);
          $tread->save();
        } else {
          if ($tread->brand_id != $brand->brand_id) {
            $tread = new Bigtread;
            $tread->timestamps = false;
            $tread->brand_id = $brand->brand_id;
            $tread->title = $item->PatternModelText;
            $tread->slug = Str::slug($tread->title);
            $tread->save();
          }
        }

        $treadId = $tread->tread_id;

        $quantity = intval($item->QuantityAvailable);
        if ($imageId != null) {
          $outPath = dirname(__DIR__, 3) . '/public/storage/industrial/tread/' . $treadId . '-o.jpg';

          if (!file_exists($outPath)) {
            Self::grab_image(env('I3_IMAGE_URL') . $imageId, $outPath);
          }
        }


        $sizes = $this->getBigSizes($item);

        $tire->make_id = $treadId;
        $tire->d1 = $sizes['d1'];
        $tire->sep = $sizes['sep1'];
        $tire->d2 = $sizes['d2'];
        $tire->sep2 = $sizes['sep2'];
        $tire->d3 = $sizes['d3'];
        $tire->type = 'Truck';
//        $tire->type = ($item->MainGroupName) ? 'AGRO' : 'IND';
        $tire->li = ($item->LoadIndex !== null) ? $item->LoadIndex : '';
        $tire->si = ($item->SpeedIndex !== null) ? $item->SpeedIndex : '';
        $tire->code = null; // PR
        $tire->price1 = ceil((round(($item->NetPrice * 1.21), 2) + 15) / 0.7);
        $tire->price2 = $item->Price;
        $tire->price3 = floor(round($item->RetailPrice * 1.21, 2));
        $tire->implemention = 'Kravas/Autobuss';
        $tire->kind = null;
        $positionText = $item->PositionText;
        $parts = preg_split('/(?=[A-Z])/', $positionText);
        $positionText = implode(' ', $parts);
        $tire->axis = ltrim($positionText, ' ');
        $tire->conditions = null;
        $tire->visible_users = 1;
        $tire->visible_list = 1;
        $tire->available = 0;
        $tire->article = $item->ArticleId;
        $tire->quantity = 0;
        $tire->urs_quantity = 0;
        $tire->krs_quantity = 0;
        $tire->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $tire->save();

        $metadata = 'price: ' . round(($item->Price * 1.21), 2) . '; pkpcena: ' . round(($item->NetPrice * 1.21), 2) . '; Baseprice: ' . round(($item->RetailPrice * 1.21), 2) . ';';

        $stock = Bigstock::where('tire_id', $tire->tire_id)->first();

        if ($stock == null) $stock = new Bigstock();
        $stock->tire_id = $tire->tire_id;
        $stock->article = $tire->article;
        $stock->quantity = $quantity;
//        $tireVisible = Bigtire::where('article', $stock->article)->first();
//        if (!is_null($tireVisible)) {
//          if ($quantity > 0) {
//            if ($quantity > 4) {
//              $tireVisible->visible_users = 1;
//              $tireVisible->visible_list = 1;
//            } else {
//              $tireVisible->visible_users = 0;
//              $tireVisible->visible_list = 0;
//            }
//          } else {
//            $tireVisible->visible_users = 0;
//            $tireVisible->visible_list = 0;
//          }
//        }
        $stock->itype = 'i3';
        $stock->type = 'truck';
        $stock->metadata = $metadata;
//        $tireVisible->save();
        if ($stock->save()) {
          $updated++;
        }
        $counted++;

      }

      DB::table('sync_times')->where('name', 'i3-big')->update(['updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function gy()
    {
      $file = 'GDYR_EE_CONFIDENTIAL_STOCKREPORT_CONSUMER.csv';

      $server = 'ftp.goodyear.eu';
      $acc = 'p22989';
      $passw = 'm4kXUrRWkx8aHDCU';
      $path = 'GDYR_EE_CONFIDENTIAL_STOCKREPORT_CONSUMER.csv';

      $ftp = ftp_connect($server) or die ();
      ftp_login($ftp, $acc, $passw);
      ftp_pasv($ftp, true);
      if(!ftp_get($ftp, $path, $file, FTP_ASCII)) {

        $error = error_get_last();
        ftp_close($ftp);
        throw new Exception('Could not read remote file: '. print_r($error, true));
      }

      ftp_close($ftp);

      $stock = [];

      if (($handle = fopen($path, "r")) !== FALSE)
      {
        $i = 0;
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE)
        {
          $stock[((string) $data[2])] = (int) $data[5];
          $i++;
        }
        fclose($handle);
      }
      unlink($path);

      file_put_contents(dirname(__DIR__, 3) . '/public/storage/xml/GDYR_EE_CONFIDENTIAL_STOCKREPORT_CONSUMER.csv', $stock);

      echo "Auto riepas<br>";
      Autostock::where('itype', 'gy')->update(['quantity' => 0]);

      $keys = array_keys($stock);
      $values = array_values($stock);

      $intKeys = array_map('intval', $keys);

      $stock = array_combine($intKeys, $values);

      unset($stock[0]);

      $updated = 0;
      $counted = 0;
      foreach ($stock as $item => $quantity){
        $article = $item;
        $quantity = intval($quantity);

        $list = Autostock::where('article', $article)->where('itype', 'gy')->get();

        $metadata = '';
        $discount = @$item->discount; if ($discount!='') $metadata.='discount: '.$discount.'; ';

        foreach ($list as $item){
          $item->quantity = $quantity;
          $item->metadata = $metadata;
          $item->save();
          $updated++;
        }
        $counted++;
      }
      DB::table('sync_times')->where('name', 'gy-auto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function rzauto()
    {
      $url = 'https://riepuzona.lv/partnerproducts.xml?email=xml@r1.com.lv&password=R1nok1An';

      $opts = ['http' => [
        'method' => 'GET',
        'timeout' => 100,
      ],
      ];

      $context = stream_context_create($opts);
      $xmlString = file_get_contents($url, false, $context);

      file_put_contents(dirname(__DIR__, 3) . '/public/storage/xml/rz.auto.xml', $xmlString);

      $xml = simplexml_load_string($xmlString);

      unset($context);

      Autostock::where('itype', 'rz')->update(['quantity' => 0]);

      $updated = 0;
      $counted = 0;

      foreach ($xml->item as $item) {
        $article = $item->code;
        $quantity = intval($item->stock_amount);

        $list = Autostock::where('article', $article)->where('itype', 'rz')->get();

        $metadata = '';
        $discount = @$item->discount; if ($discount != '') $metadata.='discount: ' . $discount . '; ';

        foreach ($list as $item){
          $item->quantity = $quantity;
          $item->metadata = $metadata;
          $item->save();
          $updated++;
        }
        $counted++;
      }
      DB::table('sync_times')->where('name', 'rz-auto')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function rzautoshow()
    {
      echo 'Auto riepas:<br>';
      $stocks = Autostock::where('itype', 'rz')->get();
      foreach ($stocks as $stock) {
        $tire = Autotire::where('tire_id', $stock->tire_id)->first();
        if (!$tire) continue;
        $text = $tire->title . ' ' . $tire->li . $tire->si . ' ' .( $tire->fullSize) . ' [' . $tire->article . ']:[' . $stock->article . ']: ' . $stock->quantity . ' / ' . $stock->metadata . '<br>';
        //dd($text);
        echo $text;
      }
    }

    private static function multiexplode($delimiters, $string) {
      @$ready = str_replace($delimiters, $delimiters[0], $string);
      return explode($delimiters[0], $ready);
    }

    private static function getByArticle($article) {
      $size = Bigtire::where('article', $article)->first();
      if ($size !== NULL) {
        return $size;
      } else {
        return false;
      }
    }

    private static function getBrandId($name) {
      $brand = Bigbrand::where('title', $name)->first();
      if ($brand !== NULL) {
        return $brand->brand_id;
      } else {
        return false;
      }
    }

    private static function getTreadId($name, $brand) {
      $brandID = SyncController::getBrandId($brand);
      if ($brandID === false) return false;

      $list = Bigtread::where('title', $name)->where('brand_id', $brandID)->first();
      if (!empty($list)) {
        return $list->tread_id;
      } else {
        return false;
      }
    }

    private static function grab_image($url,$saveto){

      if (!isset($_COOKIE['i3-token'])) {
        $token_url = "gd-api-test.barnstenit.se/Token";
//        $token_url = "api.latakko.eu/Token";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $token_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=password&username=" . env('I3_USERNAME') . "&password=" . env('I3_PASSWORD'),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
          $token = json_decode($response);
        } else {
          dd($err);
        }

        setcookie('i3-token', $token->access_token, time() + $token->expires_in, '/');
      }
      $token_bearer = $_COOKIE['i3-token'];

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "authorization: Bearer " . $token_bearer,
        ),
      ));
      $raw = curl_exec($curl);
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close ($curl);
      if ($status !== 404) {
        if(file_exists($saveto)){
          unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
      }
    }

    private static function starco_image($url,$saveto)
    {
      $ch = curl_init ($url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
      $raw=curl_exec($ch);
      $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close ($ch);
      if ($status !== 404) {
        if (file_exists($saveto)) {
          unlink($saveto);
        }
        $fp = fopen($saveto, 'x');
        fwrite($fp, $raw);
        fclose($fp);
      }
    }

    public function starco()
    {

//      $image = file_get_contents('http://194.19.236.7/Pictures/034788.jpg');

//      $curl = curl_init();
//      curl_setopt_array($curl, array(
//        CURLOPT_URL => 'http://194.19.236.7/Pictures/15721140.jpg',
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => "",
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => "GET",
//        CURLOPT_HTTPHEADER => array(
//          "cache-control: no-cache",
//        ),
//      ));
//      $response = curl_exec($curl);
//      $err = curl_error($curl);
//
//      if (!$err) {
//        $info = curl_getinfo($curl);
//        if ($info['http_code'] == '404') {
//          dd('Nav tādas bildes');
//        } else {
//          dd($response);
//        }
//      } else {
//        throw new \Exception($err);
//      }

//      curl_close($curl);



      $creditals = [
        'login' => '202562',
        'password' => '3R64p1EJuOaYnwct0FQQ'
      ];

      $url = 'http://remote.starco.lv:8153/api.rsc/';
      $tires = $url . 'lva_product_catalog_tyres';
      $stocks = $url . 'current_stock_full';
      $prices = $url . '202562_pl';
      $usernamepw = $creditals['login'] . ':' . $creditals['password'];

      $headers = [
        'Authorization: Basic ' . base64_encode($usernamepw),
      ];

      $opts = ['http' =>
        [
          'header' => $headers,
          'method' => 'GET',
        ]
      ];

      $context = stream_context_create($opts);

      $string = file_get_contents($tires, false, $context);
      $tires = json_decode($string, true)['value'];

      $tiresFile = json_encode($tires);

      file_put_contents('starco.sync.xml', $tiresFile);

      $initial = ["/[0-9.]+/", "/L/", "/S/", "/VF/", "/FI/", "/P/", "/SL/", "/DW/", "/IF/", "/CFO/"];

      $counted = 0;
      $updated = 0;
      foreach ($tires as $item) {

        if (Bigtire::where('article', $item['product_no'])->exists()) continue;

        $type = $item['segment_description'];

        if ($type === 'AGRO' || $type === 'IND' || $type === 'CONSTR') {

          if ($item['enabled'] == 'YES') {

            $article = $item['product_no'];

            $size = $item['Size'];
            $size = str_replace(" ", "", $size);
            $size = str_replace(",", ".", $size);
            $size = str_replace("X", "x", $size);
            $size = str_replace("\\", "", $size);

            $brand = ucfirst(strtolower($item['Brand']));
            $tread = $item['Profil'];

            $exploded = SyncController::multiexplode(["/", "-", "R", "x", "D"], $size);

            $size = preg_replace($initial, "", $size);
            $size = strtr($size, ['(-)' => '']);

            $size = str_split($size);

            for ($x = 0; $x < count($size); $x++) {
              $sepNr = $x + 1;
              ${"sep$sepNr"} = $size[$x];
            }

            $li = $item['LI_1'];
            $si = $item['SI_1'];

            $position = SyncController::getByArticle($article);
            if ($position === false) {
              $position = new Bigtire();
            }

            $returnText = '';

            if (!empty($brand) && !empty($tread)) {
              $treadId = SyncController::getTreadId($tread, $brand);
              // Jauns breands - Bigtire_brands
              $returnText .= 'Jauns brends - ' . $brand . '<br>';
              if ($treadId === false) {
                $brandId = SyncController::getBrandId($brand);
                if ($brandId === false) {
                  $brandId = Bigbrand::insertGetId([
                    'title' => $brand,
                    'slug' => Str::slug($brand),
                  ]);
                }
                // Jauns protektors - Bigtire_treads
                $returnText .= 'Jauns protektors - ' . $tread . '<br>';
                $treadId = Bigtread::insertGetId([
                  'brand_id' => $brandId,
                  'title' => $tread,
                  'slug' => Str::slug($tread),
                ]);
              }
            }

            $position->make_id = $treadId;

            $outPath = dirname(__DIR__, 3) . '/public/storage/industrial/tread/' . $treadId . '-o.jpg';

            Self::starco_image('http://194.19.236.7/Pictures/' . $article . '.jpg', $outPath);

            $exploded[0] = strtr($exploded[0], ['(' => '']);
            $exploded[0] = strtr($exploded[0], [')' => '']);
            $exploded[0] = floatval($exploded[0]);

            $exploded[1] = strtr($exploded[1], ['(' => '']);
            $exploded[1] = strtr($exploded[1], [')' => '']);
            $exploded[1] = floatval($exploded[1]);

            @$exploded[2] = strtr($exploded[2], ['(' => '']);
            @$exploded[2] = strtr($exploded[2], [')' => '']);
            @$exploded[2] = floatval($exploded[2]);

            if ($exploded[0] === floatval(0)) {
              continue;
            }

            $position->d1 = sprintf('%g', $exploded[0]);
            $position->sep = $sep1;
            if (!$exploded[2]) {
              $position->d2 = NULL;
              $position->sep2 = NULL;
              $position->d3 = $exploded[1];
            } else {
              $position->d2 = sprintf('%g', $exploded[1]);
              $position->sep2 = $sep2;
              $position->d3 = $exploded[2];
            }

            $position->type = $type;
            $position->li = $li;
            $position->si = $si;
            $position->code = $item['PR'];
            $position->price1 = NULL;
            $position->price2 = NULL;
            $position->implemention = $item['sub_segment_description'];
            $position->kind = NULL;
            $position->axis = NULL;
            $position->conditions = NULL;
            $position->offer = NULL;
            $position->priceoffer = NULL;
            $position->comment = $item['Radial_Diagonal'];
            $position->visible_users = 1;
            $position->visible_list = 1;
            $position->available = 1;
            $position->article = $article;
            $position->quantity = 0;

            $position->save();

            if ($article !== '') {
              $position->addSecondaryArticle($article, 'starco');
            }

            $updated++;

          }
        }
        $counted++;
      }

      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)<br>";

      $string = file_get_contents($stocks, false, $context);
      $stocks = json_decode($string, true)['value'];

      foreach ($stocks as $item) {

        if ($item['RIG_STOCK'] == 0) {

          if (Bigtire::where('article', $item['product_no'])->exists()) {
            Bigtire::where('article', $item['product_no'])->update(['visible_users' => 0, 'visible_list' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
          } else {
            continue;
          }

        } else {

          if (Bigstock::where('article', $item['product_no'])->exists()) {
            Bigstock::where('article', $item['product_no'])->where('itype', 'starco')->update(['quantity' => $item['RIG_STOCK'], 'updated_at' => date('Y-m-d H:i:s')]);;
          } else {
            continue;
          }

        }
      }

      echo "Preču daudzumi atjaunoti!<br>";

      $string = file_get_contents($prices, false, $context);
      $prices = json_decode($string, true)['value'];

      foreach ($prices as $item) {

        if (Bigtire::where('article', $item['product_no'])->exists()) {
          $itam = Bigtire::where('article', $item['product_no'])->first();
        } else {
          continue;
        }

        if ($itam->article == $item['product_no']) {

          if ($item['price'] < 100) {
            $price1 = ($item['price'] + 8) / 70 * 100;
            $price2 = $item['price'] + 10;
            Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price3' => (int)$price2, 'updated_at' => date('Y-m-d H:i:s')]);
          }
          if ($item['price'] >= 100 && $item['price'] < 200) {
            $price1 = ($item['price'] + 12) / 70 * 100;
            $price2 = $item['price'] + 15;
            Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price3' => (int)$price2, 'updated_at' => date('Y-m-d H:i:s')]);
          }
          if ($item['price'] > 200) {
            $price1 = ($item['price'] + 15) / 70 * 100;
            $price2 = $item['price'] + 20;
            Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price3' => (int)$price2, 'updated_at' => date('Y-m-d H:i:s')]);
          }

        }

        if (Bigstock::where('article', $item['product_no'])->exists()) {
          $stock = Bigstock::where('article', $item['product_no'])->first();
          if (Bigtire::where('article', $item['product_no'])->exists()) {
            if ($stock->quantity == 0) Bigtire::where('article', $item['product_no'])->update(['visible_users' => 0, 'visible_list' => 0]);
          }
        } else {
          continue;
        }

      }

      DB::table('sync_times')->where('name', 'starco-big')->update(['updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);
      echo 'Preču cenas atjaunotas!';

    }

    public function sync_all()
    {
      $this->i3auto();
      $this->gy();
      $this->rzauto();
      $this->i3moto();
      $this->duellmoto();
      $this->i3quadr();
      $this->duellquadr();
//      $this->i3big();
      $this->starco();
      return 'Visas sinhronizācijas notika!';
    }

  }

<?php

namespace App\Http\Controllers;

use App\Models\Autotire;
use App\Models\Bigbrand;
use App\Models\Bigstock;
use App\Models\Bigtire;
use App\Models\Bigtread;
use App\Models\Motostock;
use App\Models\Quadr;
use App\Models\Quadrstock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Autostock;
use Illuminate\Support\Facades\Session;
use PDO;
use Illuminate\Support\Str;

class SyncController extends Controller
{

    public $accrual;
    public $tire_tables;
    public $stock_tables;

    public function __construct()
    {
        $this->tire_tables = [
            'auto_tires' => 'auto_stock',
            'moto_tires' => 'moto_stock',
            'quadr_tires' => 'quadr_stock'
        ];
    }

    // Accrual Sync

    public function accrual()
    {

        try {
          $this->accrual = new PDO("sqlsrv:Server=212.3.218.22,1444;Database=accrual", "sa", "cenzors");
        } catch (\PDOException $e) {
          DB::table('sync_times')->where('name', 'accrual')->update(['updated_at' => NOW()]);
          die("Database connection failed: " . $e->getMessage());
          exit;
        }

        echo 'Go Stock!' . PHP_EOL;

        foreach ($this->tire_tables as $tire_table => $tire_stock) {
            $stock = $this->getInventory($tire_table);


            if($stock === FALSE) {
                echo 'Accrual sync failed!';
                return false;
            }

            DB::table($tire_table)->update(['quantity' => 0, 'krs_quantity' => 0, 'urs_quantity' => 0]);

//          $this->updateStock($stock[1]);
            $this->updateStock($stock[2]);
        }

        DB::table('sync_times')->where('name', 'accrual')->update(['updated_at' => NOW()]);
        return 'Done';

    }

  public function updateStock($stock)
  {
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
      @$urs_quantity = (int) $urs[1];

      @$krs = explode(': ', $krs);
      @$krs_quantity = (int) $krs[1];

      $total = $urs_quantity + $krs_quantity;

      foreach ($this->tire_tables as $tire_table => $tire_stock) {
        $product = DB::table($tire_table)->where('tire_id', $id)->first();

        if ($product) {

          $article = '';

          $sql = "SELECT ArticleId as ArtikulaId FROM katdetal WHERE Artikuls = '" . $product->article . "'";
          $result = $this->accrual->query($sql);

          foreach ($result as $row) {
            $article = $row['ArtikulaId'];
          }

          $sql = "SELECT * FROM katalogs k INNER JOIN unatlgrupas u ON (k.ArticleId = u.ArticleId) WHERE k.ArticleId = '" . $article . "'";
          $result = $this->accrual->query($sql);
          if ($result->rowCount()) {
            foreach ($result as $rows) {
              set_time_limit(0);
              $veikala_cena = (int) round(round($rows['Cena1'], 5) * 1.21);
              if ($rows['Deleted'] == 1) {
                $akcijas_cena = (int) round(round($rows['Cena3'], 5) * 1.21);
              } else {
                $akcijas_cena = (int) round(round($rows['Cena'], 5) * 1.21);
              }
            }
            DB::table($tire_table)->where('tire_id', $id)->update([
              'price1' => $veikala_cena,
              'price2' => $akcijas_cena,
              'quantity' => $total,
              'urs_quantity' => @$urs_quantity,
              'krs_quantity' => @$krs_quantity,
              'updated_at' => date('Y-m-d H:i:s')
            ]);
          } else {
            DB::table($tire_table)->where('tire_id', $id)->update([
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

  public function getInventory($tire_table, $article = null)
  {
    $map = $this->getAccrualIdToEntityIdMap($tire_table);

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

  public function getAccrualIdToEntityIdMap($tire_table)
  {
    $sql = DB::table($tire_table)->get();
    $result = [];

    foreach ($sql as $row) {
      array_push($result, ['tire_id' => $row->tire_id, 'article' => $row->article]);
    }

//        Jāuztaisa masīvs - [
//        [
//              'tire_id' => $tire_id,
//              'accrual_id' => $accrual_id
//        ]

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
        $url = "https://gd-middleware-test.barnstenit.se/api/Tyres?username=XmL_r1&password=M20h:2|5";

        $opts = ['http' =>
            [
                'method'  => 'GET',
                'timeout'  => 600,
            ]
        ];

        set_time_limit(800);

        $context  = stream_context_create($opts);
        $xmlString = file_get_contents($url, false, $context);

        file_put_contents('i3.auto.xml',$xmlString);

        $xml = simplexml_load_string($xmlString);

        unset($context);

        echo "Auto riepas<br>";
        Autostock::where('itype', 'i3')->update(['quantity' => 0]);

        $updated = 0;
        $counted = 0;
        foreach ($xml->Item as $item){
            $article = $item->stockcode;
            $quantity = intval($item->qty_available);

            $metadata = '';
            $price = @$item->price; if ($price!='') $metadata.='price: '.$price.'; ';
            $pkpcena = @$item->pkpcena; if ($pkpcena!='') $metadata.='pkpcena: '.$pkpcena.'; ';
            $baseprice = @$item->Baseprice; if ($baseprice!='') $metadata.='Baseprice: '.$baseprice.'; ';

            $list = Autostock::where('article', $article)->where('itype', 'i3')->get();

            foreach ($list as $itam){
                $itam->quantity = $quantity;
                $itam->metadata = $metadata;
                $itam->save();
                $updated++;
            }
            $counted++;
        }
        DB::table('sync_times')->where('name', 'i3-auto')->update(['updated_at' => NOW()]);
        echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

    public function i3moto()
    {

      $url = "https://gd-middleware-test.barnstenit.se/api/Moto?username=XmL_r1&password=M20h:2|5";

      $opts = ['http' =>
        [
          'method'  => 'GET',
          'timeout'  => 600,
        ]
      ];

      set_time_limit(800);

      $context  = stream_context_create($opts);
      $xmlString = file_get_contents($url, false, $context);

      file_put_contents('i3.moto.xml',$xmlString);

      $xml = simplexml_load_string($xmlString);

      unset($context);

      echo "Moto riepas<br>";
      Motostock::where('itype', 'i3')->update(['quantity' => 0]);

      $updated = 0;
      $counted = 0;
      foreach ($xml->Item as $item){
        $article = $item->stockcode;
        $quantity = intval($item->qty_available);

        $metadata = '';
        $price = @$item->price; if ($price!='') $metadata.='price: '.$price.'; ';
        $pkpcena = @$item->pkpcena; if ($pkpcena!='') $metadata.='pkpcena: '.$pkpcena.'; ';
        $baseprice = @$item->Baseprice; if ($baseprice!='') $metadata.='Baseprice: '.$baseprice.'; ';

        $list = Motostock::where('article', $article)->where('itype', 'i3')->get();

        foreach ($list as $itam){
          $itam->quantity = $quantity;
          $itam->metadata = $metadata;
          $itam->save();
          $updated++;
        }
        $counted++;
      }
      DB::table('sync_times')->where('name', 'i3-moto')->update(['updated_at' => NOW()]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

  public function i3quadr()
  {

    $url = "https://gd-middleware-test.barnstenit.se/api/Moto?username=XmL_r1&password=M20h:2|5";

    $opts = ['http' =>
      [
        'method'  => 'GET',
        'timeout'  => 600,
      ]
    ];

    set_time_limit(800);

    $context  = stream_context_create($opts);
    $xmlString = file_get_contents($url, false, $context);

    file_put_contents('i3.quadr.xml',$xmlString);

    $xml = simplexml_load_string($xmlString);

    unset($context);

    echo "Kvadraciklu riepas<br>";
    Quadrstock::where('itype', 'i3')->update(['quantity' => 0]);

    $updated = 0;
    $counted = 0;
    foreach ($xml->Item as $item){
      $article = $item->stockcode;
      $quantity = intval($item->qty_available);

      $metadata = '';
      $price = @$item->price; if ($price!='') $metadata.='price: '.$price.'; ';
      $pkpcena = @$item->pkpcena; if ($pkpcena!='') $metadata.='pkpcena: '.$pkpcena.'; ';
      $baseprice = @$item->Baseprice; if ($baseprice!='') $metadata.='Baseprice: '.$baseprice.'; ';

      $list = Quadrstock::where('article', $article)->where('itype', 'i3')->get();

      foreach ($list as $itam){
        $itam->quantity = $quantity;
        $itam->metadata = $metadata;
        $itam->save();
        $updated++;
      }
      $counted++;
    }
    DB::table('sync_times')->where('name', 'i3-quadr')->update(['updated_at' => NOW()]);
    echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
  }

    public function duellmoto()
    {

      $url = 'ftp://duellus:WebUpdate!@updateftp.duell.fi/ic.TXT';

      $opts = ['ftp' => []];

      set_time_limit(0);

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
      DB::table('sync_times')->where('name', 'duell_moto')->update(['updated_at' => NOW()]);
      echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

    }

  public function duellquadr()
  {

    $url = 'ftp://duellus:WebUpdate!@updateftp.duell.fi/ic.TXT';

    $opts = ['ftp' => []];

    set_time_limit(0);

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
    DB::table('sync_times')->where('name', 'duell_quadr')->update(['updated_at' => NOW()]);
    echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";

  }

    public function i3big()
    {
      $url = "https://gd-middleware-test.barnstenit.se/api/Truck?username=XmL_r1&password=M20h:2|5";

      $opts = ['http' =>
        [
          'method'  => 'GET',
          'timeout'  => 600,
        ]
      ];

      set_time_limit(800);

      $context  = stream_context_create($opts);
      $xmlString = file_get_contents($url, false, $context);

      file_put_contents('i3.industrial.xml',$xmlString);

      $xml = simplexml_load_string($xmlString);

      unset($context);

      echo "Kravas riepas<br>";
      Bigstock::where('itype', 'i3')->update(['quantity' => 0]);

      $updated = 0;
      $counted = 0;
      foreach ($xml->Item as $item){

        $item = json_encode($item);
        $item = (object) json_decode($item, TRUE);

        $type = $item->aplication;

        if (empty($item->description)) {
          continue;
        }

        if ($type === 'Bus/Truck' || $type === 'Truck' || $type === 'Bus') {

          $article = $item->stockcode;

          $price = round($item->price);

          if ($price != 0) {
            if ($price < 100) {
              $price1 = ($price + 8) / 70 * 100;
              $price2 = $price + 10;
            }
            if ($price >= 100 && $price < 200) {
              $price1 = ($price + 12) / 70 * 100;
              $price2 = $price + 15;
            }
            if ($price >= 200) {
              $price1 = ($price + 15) / 70 * 100;
              $price2 = $price + 20;
            }
          }

          $price1 = round($price1);
          $price2 = round($price2);

          $d1 = $item->width;
          $d2 = $item->profile;
          $d3 = $item->diameter;

          if (is_array($item->description)) {
            continue;
          }
          $sizes = preg_split('/ /', $item->description)[0];
          $sizes = SyncController::multiexplode([$d1, $d2, $d3], $sizes);
          $sizes = array_values(array_filter($sizes));

          if (count($sizes) < 0) {
            continue;
          }

          for ($x = 0; $x < count($sizes); $x++) {
            $sepNr = $x + 1;
            ${"sep$sepNr"} = $sizes[$x];
          }

          $brand = $item->brand;
          $brand = str_replace(' (KRAVAS)', '', $brand);
          $brand = str_replace(' (COACH)', '', $brand);
          $brand = ucfirst(strtolower($brand));
          $tread = $item->protector;

          if (strpos($brand, 'RIEPAS dažādas') !== false ||
              strpos($brand, 'Atjaunotas') !== false ||
              strpos($brand, 'Riepas daŽĀdas') !== false) {
              $brand = '';
              $tread = '';
          }

//          if ($item->stockcode !== '385652251417943058TT0R0') continue;

          $lisi = $item->li_si;
          if (is_array($lisi)) {
            continue;
          }
          if (preg_match('/ [\d]+PR/', $lisi)) {
            $lisi = preg_replace('/ [0-9]+PR/', '', $lisi);
          }
          if (preg_match('/[\d]+PR /', $lisi)) {
            $lisi = preg_replace('/[\d]+PR /', '', $lisi);
          }

          if (str_word_count($lisi) > 1) {
            $lisi = preg_replace("/\([^)]+\)/","",$lisi);
            $lisi = SyncController::multiexplode([' ', '/'], $lisi);
          } else {
            if (preg_match("/([\d]+[a-zA-Z]+)/i", $lisi)) {
              if (strpos($lisi, '/') !== false) {
                $lisi = explode('/', $lisi);
                $si = preg_replace('/[\d]+/', '', $lisi[1]);
              }
            }
          }
          if (is_array($lisi)) {
            $li = $lisi[0];
            if (preg_match('/[a-zA-Z]/i', $li[0])) {
              $lisi = preg_split('/(?<=[a-zA-Z])/i', $li);
              $li = $lisi[1];
              if (!isset($si)) {
                $si = $lisi[0];
              }
            } else {
              if (isset($lisi[1])) {
                if (preg_match('/[a-zA-Z]/i', $lisi[1])) {
                  $si = preg_replace('/[\d]+/i', '', $lisi[1]);
                }
                $li = $lisi[0];
              } else {
                $lisi = preg_split('/(?=[a-zA-Z])/i', $lisi[0]);
                $li = $lisi[0];
                $si = $lisi[1];
              }
            }
          }

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

          $image = $item->image;

          $outPath = dirname(__DIR__, 3) . '/storage/app/public/industrial/tread/';

          @$image = file_get_contents('http://i3.lattako.lv/images/tyres/' . $image . '.jpg');
          $new_image = $outPath . $treadId . '.jpg';

          if (trim($image) !== false) {
            file_put_contents($new_image, $image);
          } else {
            echo 'Neeksistē - Artikuls (' . $article . ')';
          }

          $position->d1 = $d1;
          $position->sep = $sep1;
          if (is_array($d2)) {
            $position->d2 = null;
            $position->sep2 = null;
            $position->d3 = $d3;
          } else {
            $position->d2 = $d2;
            $position->sep2 = $sep2;
            $position->d3 = $d3;
          }

          if ($type === 'Truck') {
            $type = str_replace('Truck', 'Kravas', $type);
          } else if ($type === 'Buss') {
            $type = str_replace('Buss', 'Autobuss', $type);
          } else if ($type === 'Bus/Truck') {
            $type = str_replace('Bus/Truck', 'Autobuss/Kravas', $type);
          }

          $position->type = 'TRUCK';
          $position->li = $li;
          $position->si = $si;
          $position->price1 = $price1;
          $position->price2 = $price2;
          $position->implemention = $type;
          $position->kind = null;
          (empty($item->buss_possition)) ? $position->axis_bus = null : $position->axis_bus = $item->buss_possition;
          (empty($item->truck_possition)) ? $position->axis_truck = null : $position->axis_truck = $item->truck_possition;
          (empty($item->road_for_Buss)) ? $position->conditions_bus = null : $position->conditions_bus = $item->road_for_Buss;
          (empty($item->road_for_trucks)) ? $position->conditions_truck = null : $position->conditions_truck = $item->road_for_trucks;
          $position->offer = null;
          $position->priceoffer = null;
          $position->comment = null;
          if ($item->qty_available > 0) {
            if (!empty($d1) && !empty($d2) && !empty($d3) || !empty($d1) && empty($d2) && !empty($d3)) {
              $position->visible_users = 1;
              $position->visible_list = 1;
            } else {
              $position->visible_users = 0;
              $position->visible_list = 0;
            }
          } else {
            $position->visible_users = 0;
            $position->visible_list = 0;
          }

          $position->available = 1;
          $position->article = $article;

          $position->save();

          if ($article !== '') {
            $position->addSecondaryArticle($article, 'i3');
          }

          $lists = Bigstock::where('article', $article)->where('itype', 'i3')->get();

          foreach ($lists as $list) {
            $list->update(['quantity' => $item->qty_available, 'updated_at' => date('Y-m-d H:i:s')]);
            $updated++;
          }

          $counted++;

        }

      }
      DB::table('sync_times')->where('name', 'i3-big')->update(['updated_at' => NOW()]);
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

        file_put_contents('GDYR_EE_CONFIDENTIAL_STOCKREPORT_CONSUMER.csv', $stock);

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
        DB::table('sync_times')->where('name', 'gy-auto')->update(['updated_at' => NOW()]);
        echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
    }

    public function rzauto()
    {
        set_time_limit(0);
        $url = 'https://riepuzona.lv/partnerproducts.xml?email=xml@r1.com.lv&password=R1nok1An';

        $opts = ['http' => [
            'method' => 'GET',
            'timeout' => 100,
          ],
        ];

        $context = stream_context_create($opts);
        $xmlString = file_get_contents($url, false, $context);

        file_put_contents('rz.auto.xml', $xmlString);

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
        DB::table('sync_times')->where('name', 'rz-auto')->update(['updated_at' => NOW()]);
        echo "Mainīti {$updated} ieraksti (sarakstā {$counted} ieraksti)\n";
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

    public function starco()
    {

      set_time_limit(0);

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

              $outPath = dirname(__DIR__, 3) . '/storage/app/public/industrial/tread/';

              @$image = file_get_contents('http://194.19.236.7/Pictures/' . $article . '.jpg');
              $new_image = $outPath . $treadId . '.jpg';

              if (trim($image) !== false) {
                file_put_contents($new_image, $image);
              } else {
                echo 'Neeksistē - Artikuls (' . $article . ')';
              }

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

              $position->d1 = $exploded[0];
              $position->sep = $sep1;
              if (!$exploded[2]) {
                $position->d2 = NULL;
                $position->sep2 = NULL;
                $position->d3 = $exploded[1];
              } else {
                $position->d2 = $exploded[1];
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
              $position->axis_bus = NULL;
              $position->axis_truck = NULL;
              $position->conditions_bus = NULL;
              $position->conditions_truck = NULL;
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
              Bigtire::where('article', $item['product_no'])->update(['visible_users' => 0, 'visible_list' => 0]);
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
              Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price2' => (int)$price2]);
            }
            if ($item['price'] >= 100 && $item['price'] < 200) {
              $price1 = ($item['price'] + 12) / 70 * 100;
              $price2 = $item['price'] + 15;
              Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price2' => (int)$price2]);
            }
            if ($item['price'] > 200) {
              $price1 = ($item['price'] + 15) / 70 * 100;
              $price2 = $item['price'] + 20;
              Bigtire::where('article', $item['product_no'])->update(['price1' => (int)$price1, 'price2' => (int)$price2]);
            }

          }

          if (Bigstock::where('article', $item['product_no'])->exists()) {
            $stock = Bigstock::where('article', $item['product_no'])->first();
            if (Bigtire::where('article', $item['product_no'])->exists()) {
              if ($stock->quantity === 0) Bigtire::where('article', $item['product_no'])->update(['visible_users' => 0, 'visible_list' => 0]);
            }
          } else {
            continue;
          }

        }

        DB::table('sync_times')->where('name', 'starco-big')->update(['updated_at' => NOW()]);
        echo 'Preču cenas atjaunotas!';

    }

}

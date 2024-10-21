<?php

namespace App\Helper;

use App\Models\Rim;
use App\Models\Rimbrand;
use DB;
use App\Models\Autotire;
use App\Models\Autotread;
use App\Models\Autobrand;
use App\Models\Quadr;
use App\Models\Quadrtread;
use App\Models\Quadrbrand;
use App\Models\Moto;
use App\Models\Mototread;
use App\Models\Motobrand;
use App\Models\Bigtire;
use App\Models\Bigtread;
use App\Models\Bigbrand;
use App\Models\Studbrand;
use App\Models\Studtread;

class Tires
{

    public static function getAllAutoBrands($season = 1) {
        return Autobrand::selectRaw('auto_brands.brand_id, auto_brands.title as brand_title')
                          ->join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
                          ->whereRaw('auto_brands.title <> ""')
                          ->where('auto_treads.season', $season)
                          ->orderBy('brand_title')
                          ->groupBy('auto_brands.title')
                          ->get();
    }

    public static function getAllAutoBrands1($season = 1) {
	$brand_list = [];

	$tires = Autotire::select('make_id')->get();
	foreach ($tires as $tire) {
	  $brand = Autotread::select('auto_treads.brand_id', 'auto_treads.tread_id', 'auto_treads.season', 'auto_brands.*')
			        ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
				->where('tread_id', $tire->make_id)
				->where('season', $season)
				->first();
	  if (!$brand) continue;
	  $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
	}

	$brand_list = array_unique($brand_list);
	asort($brand_list);

	return $brand_list;

    }

//    public static function getAllAutoTreads() {
//        return DB::table('auto_treads')->select('title')->whereRaw('')
//    }

    public static function getAllBigBrands() {
      $tires = Bigtire::with('tread')->where('visible_users', 1)->where('visible_list', 1)->get();
      $brands = [];
      foreach ($tires as $tire) {
        if (Bigbrand::where('brand_id', $tire->tread->brand_id)->exists()) {
          $brand = Bigbrand::select('brand_id as id', 'title')->where('brand_id', $tire->tread->brand_id)->first();
          array_push($brands, $brand);
        } else {
          continue;
        }
      }
      sort($brands);
      $brands = array_values(array_unique($brands));
      return $brands;
    }

    public static function getAllQuadrBrands() {
        //return Quadrbrand::selectRaw('quadr_brands.brand_id as id, title')->whereRaw('title <> ""')->orderBy('brand_id')->groupBy('title')->get();

	$brand_list = [];

        $tires = Quadr::select('make_id')->get();
        foreach ($tires as $tire) {
          $brand = Quadrtread::select('quadr_treads.brand_id', 'quadr_treads.tread_id', 'quadr_brands.*')
				->leftJoin('quadr_brands', 'quadr_treads.brand_id', '=', 'quadr_brands.brand_id')
				->where('tread_id', $tire->make_id)
				->first();
          if (!$brand) continue;
          $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
        }

        $brand_list = array_unique($brand_list);
        asort($brand_list);

        return $brand_list;
    }

    public static function getAllMotoBrands() {
        //return Motobrand::selectRaw('moto_brands.brand_id as id, title')->whereRaw('title <> ""')->orderBy('brand_id')->groupBy('title')->get();

	$brand_list = [];

        $tires = Moto::select('make_id')->get();
        foreach ($tires as $tire) {
          $brand = Mototread::select('moto_treads.brand_id', 'moto_treads.tread_id', 'moto_brands.*')
                                ->leftJoin('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                                ->where('tread_id', $tire->make_id)
				->where('moto_brands.title', '!=', '')
                                ->first();
          if (!$brand) continue;
          $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
        }

        $brand_list = array_unique($brand_list);
        asort($brand_list);

        return $brand_list;
    }

    public static function getAutoTiresSize($column, $season = 1) {
      return Autotire::join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
        ->select($column)
        ->where($column, '<>', '""')
        ->where('auto_tires.visible_users', '<>', 0)
        ->where('auto_treads.season', $season)
        ->orderByRaw("CASE WHEN {$column} >= 100 THEN 0 ELSE 1 END, {$column}")
        ->groupBy($column)
        ->get();
    }

    public static function getQuadrTiresD1() {
        return Quadr::select('d1')->whereRaw('d1 <> ""')->orderByRaw('cast(d1 as decimal(7,2)) ASC')->groupBy('d1')->get();
    }

    public static function getQuadrTiresD2() {
        return Quadr::select('d2')->whereRaw('d2 <> ""')->orderByRaw('cast(d2 as decimal(7,2)) ASC')->groupBy('d2')->get();
    }

    public static function getQuadrTiresD3() {
        return Quadr::select('d3')->whereRaw('d3 <> ""')->orderByRaw('cast(d3 as decimal(7,2)) ASC')->groupBy('d3')->get();
    }

    public static function getMotoTiresD1() {
        return Moto::select('d1')->whereRaw('d1 <> ""')->orderByRaw('cast(d1 as decimal(7,2)) ASC')->groupBy('d1')->get();
    }

    public static function getMotoTiresD2() {
        return Moto::select('d2')->whereRaw('d2 <> ""')->orderByRaw('cast(d2 as decimal(7,2)) ASC')->groupBy('d2')->get();
    }

    public static function getMotoTiresD3() {
        return Moto::select('d3')->whereRaw('d3 <> ""')->orderByRaw('cast(d3 as decimal(7,2)) ASC')->groupBy('d3')->get();
    }

    public static function getBigTiresD1() {
      $tires = Bigtire::select('d1')->where('visible_users', 1)->where('visible_list', 1)->get();
      $sizes = [];
      foreach ($tires as $tire) {
        if ($tire->d1 !== null) {
          array_push($sizes, $tire);
        }
      }
      sort($sizes);
      $sizes = array_values(array_unique($sizes));
      return $sizes;
    }

    public static function getBigTiresD2() {
      $tires = Bigtire::select('d2')->where('visible_users', 1)->where('visible_list', 1)->get();
      $sizes = [];
      foreach ($tires as $tire) {
        if ($tire->d2 !== null) {
          array_push($sizes, $tire);
        }
      }
      sort($sizes);
      $sizes = array_values(array_unique($sizes));
      return $sizes;
    }

    public static function getBigTiresD3() {
      $tires = Bigtire::select('d3')->where('visible_users', 1)->where('visible_list', 1)->get();
      $sizes = [];
      foreach ($tires as $tire) {
        if ($tire->d3 !== null) {
          array_push($sizes, $tire);
        }
      }
      sort($sizes);
      $sizes = array_values(array_unique($sizes));
      return $sizes;
    }

    public static function getStudTread($tread_id) {
      return Studtread::select('*')->where('tread_id', $tread_id)->first();
    }

    public static function getStudBrand($brand_id) {
      return Studbrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public static function getAutoTireTread($tread_id) {
        return Autotread::select('*')->where('tread_id', $tread_id)->first();
    }

    public static function getAutoTireBrand($brand_id) {
        return Autobrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public static function getAutoRimBrand($brand_id) {
      return Rimbrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public static function getQuadrTireTread($tread_id) {
        return Quadrtread::select('*')->where('tread_id', $tread_id)->first();
    }

    public static function getQuadrTireBrand($brand_id) {
        return Quadrbrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public static function getMotoTireTread($tread_id) {
        return Mototread::select('*')->where('tread_id', $tread_id)->first();
    }

    public static function getMotoTireBrand($brand_id) {
        return Motobrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public static function getBigTireTread($tread_id) {
        return Bigtread::select('*')->where('tread_id', $tread_id)->first();
    }

    public static function getBigTireBrand($brand_id) {
        return Bigbrand::select('*')->where('brand_id', $brand_id)->first();
    }

    public function GCD($a, $b)
    {
        if ($a == 0) return $b;
        return $this->GCD($b % $a, $a);
    }

    /**
     * Atrod lielāko kopīgo dalītāju masīvam
     * @param array $array	Masīvs, kam nepieciešams atrast lielāko kopīgo dalītāju
     * @param integer $n	Elementu skaits
     * @return mixed
     */

    public function arrayGCD($array, $n=0)
    {
        $array = array_map('strval', $array);
        $result = $array[0];
        if ($n==0) $n = count($array);
        if ($n==0) return false;
        for ($i = 1; $i < $n; $i++)
            $result = $this->GCD($array[$i], $result);

        return $result;
    }

    /**
     *
     * @param string $text Saīsināmais teksts
     * @param type $limit Maksimālais simbolu skaits tekstā
     * @param type $ellipsis Ar ko aizstāt maksimālo simbolu skaitu
     * @param type $strip Par cik saīsināt tekstu, ja pārsniegts maksimālais simbolu skaits (noklusētais = 0)
     * @return string
     */
    public static function truncateCharacters($text,$limit,$ellipsis='...',$strip=0){
        if(strlen($text) > $limit) $text = trim(substr($text, 0, $limit-$strip)).$ellipsis;
        return $text;
    }

    public static function zero_pad($i,$c){
        while (strlen($i)<$c){
            $i = '0'.$i;
        }
        return $i;
    }

    public static function lisiDesc($weight, $speed)
  {
    $carryCaps = [
      0 => '45 kg',
      1 => '46.2 kg',
      2 => '47.5 kg',
      3 => '48.7 kg',
      4 => '50 kg',
      5 => '51.5 kg',
      6 => '53 kg',
      7 => '54.5 kg',
      8 => '56 kg',
      9 => '58 kg',
      10 => '60 kg',
      11 => '61.5 kg',
      12 => '63 kg',
      13 => '65 kg',
      14 => '67 kg',
      15 => '69 kg',
      16 => '71 kg',
      17 => '73 kg',
      18 => '75 kg',
      19 => '77.5 kg',
      20 => '80 kg',
      21 => '82.5 kg',
      22 => '85 kg',
      23 => '87.5 kg',
      24 => '90 kg',
      25 => '92.5 kg',
      26 => '95 kg',
      27 => '97.5 kg',
      28 => '100 kg',
      29 => '103 kg',
      30 => '106 kg',
      31 => '109 kg',
      32 => '112 kg',
      33 => '115 kg',
      34 => '118 kg',
      35 => '121 kg',
      36 => '125 kg',
      37 => '128 kg',
      38 => '132 kg',
      39 => '136 kg',
      40 => '140 kg',
      41 => '145 kg',
      42 => '150 kg',
      43 => '155 kg',
      44 => '160 kg',
      45 => '165 kg',
      46 => '170 kg',
      47 => '175 kg',
      48 => '180 kg',
      49 => '185 kg',
      50 => '190 kg',
      51 => '195 kg',
      52 => '200 kg',
      53 => '206 kg',
      54 => '212 kg',
      55 => '218 kg',
      56 => '224 kg',
      57 => '230 kg',
      58 => '236 kg',
      59 => '243 kg',
      60 => '250 kg',
      61 => '257 kg',
      62 => '265 kg',
      63 => '272 kg',
      64 => '280 kg',
      65 => '290 kg',
      66 => '300 kg',
      67 => '307 kg',
      68 => '315 kg',
      69 => '325 kg',
      70 => '335 kg',
      71 => '345 kg',
      72 => '355 kg',
      73 => '365 kg',
      74 => '375 kg',
      75 => '387 kg',
      76 => '400 kg',
      77 => '412 kg',
      78 => '425 kg',
      79 => '437 kg',
      80 => '450 kg',
      81 => '462 kg',
      82 => '475 kg',
      83 => '487 kg',
      84 => '500 kg',
      85 => '515 kg',
      86 => '530 kg',
      87 => '545 kg',
      88 => '560 kg',
      89 => '580 kg',
      90 => '600 kg',
      91 => '615 kg',
      92 => '630 kg',
      93 => '650 kg',
      94 => '670 kg',
      95 => '690 kg',
      96 => '710 kg',
      97 => '730 kg',
      98 => '750 kg',
      99 => '775 kg',
      100 => '800 kg',
      101 => '825 kg',
      102 => '850 kg',
      103 => '875 kg',
      104 => '900 kg',
      105 => '925 kg',
      106 => '950 kg',
      107 => '975 kg',
      108 => '1000 kg',
      109 => '1030 kg',
      110 => '1060 kg',
      111 => '1090 kg',
      112 => '1120 kg',
      113 => '1150 kg',
      114 => '1180 kg',
      115 => '1215 kg',
      116 => '1250 kg',
      117 => '1285 kg',
      118 => '1320 kg',
      119 => '1360 kg',
      120 => '1400 kg',
      121 => '1450 kg',
      122 => '1500 kg',
      123 => '1550 kg',
      124 => '1600 kg',
      125 => '1650 kg',
      126 => '1700 kg',
      127 => '1750 kg',
      128 => '1800 kg',
      129 => '1850 kg',
      130 => '1900 kg',
      131 => '1950 kg',
      132 => '2000 kg',
      133 => '2060 kg',
      134 => '2120 kg',
      135 => '2180 kg',
      136 => '2240 kg',
      137 => '2300 kg',
      138 => '2360 kg',
      139 => '2430 kg',
      140 => '2500 kg',
      141 => '2570 kg',
      142 => '2650 kg',
      143 => '2720 kg',
      144 => '2800 kg',
      145 => '2900 kg',
      146 => '3000 kg',
      147 => '3075 kg',
      148 => '3150 kg',
      149 => '3250 kg',
      150 => '3350 kg',
      151 => '3450 kg',
      152 => '3550 kg',
      153 => '3650 kg',
      154 => '3750 kg',
      155 => '3875 kg',
      156 => '4000 kg',
      157 => '4125 kg',
      158 => '4250 kg',
      159 => '4375 kg',
      160 => '4500 kg',
      161 => '4625 kg',
      162 => '4750 kg',
      163 => '4875 kg',
      164 => '5000 kg',
      165 => '5150 kg',
      166 => '5300 kg',
      167 => '5450 kg',
      168 => '5600 kg',
      169 => '5800 kg',
      170 => '6000 kg',
      171 => '6150 kg',
      172 => '6300 kg',
      173 => '6500 kg',
      174 => '6700 kg',
      175 => '6900 kg',
      176 => '7100 kg',
      177 => '7300 kg',
      178 => '7500 kg',
      179 => '7750 kg',
      180 => '8000 kg',
      181 => '8250 kg',
      182 => '8500 kg',
      183 => '8750 kg',
      184 => '9000 kg',
      185 => '9250 kg',
      186 => '9500 kg',
      187 => '9750 kg',
      188 => '10000 kg',
      189 => '10300 kg',
      190 => '10600 kg',
      191 => '10900 kg',
      192 => '11200 kg',
      193 => '11500 kg',
      194 => '11800 kg',
      195 => '12150 kg',
      196 => '12500 kg',
      197 => '12850 kg',
      198 => '13200 kg',
      199 => '13600 kg',
      200 => '14000 kg',
      201 => '14500 kg',
      202 => '15000 kg',
      203 => '15500 kg',
      204 => '16000 kg',
      205 => '16500 kg',
      206 => '17000 kg',
      207 => '17500 kg',
      208 => '18000 kg',
      209 => '18500 kg',
      210 => '19000 kg',
      211 => '19500 kg',
      212 => '20000 kg',
      213 => '20600 kg',
      214 => '21200 kg',
      215 => '21800 kg',
      216 => '22400 kg',
      217 => '23000 kg',
      218 => '23600 kg',
      219 => '24300 kg',
      220 => '25000 kg',
      221 => '25700 kg',
      222 => '26500 kg',
      223 => '27200 kg',
      224 => '28000 kg',
      225 => '29000 kg',
      226 => '30000 kg',
      227 => '30750 kg',
      228 => '31500 kg',
      229 => '32500 kg',
      230 => '33500 kg',
      231 => '34500 kg',
      232 => '35500 kg',
      233 => '36500 kg',
      234 => '37500 kg',
      235 => '38750 kg',
      236 => '40000 kg',
      237 => '41250 kg',
      238 => '42500 kg',
      239 => '43750 kg',
      240 => '45000 kg',
      241 => '46250 kg',
      242 => '47500 kg',
      243 => '48750 kg',
      244 => '50000 kg',
      245 => '51500 kg',
      246 => '53000 kg',
      247 => '54500 kg',
      248 => '56000 kg',
      249 => '58000 kg',
      250 => '60000 kg',
      251 => '61500 kg',
      252 => '63000 kg',
      253 => '65000 kg',
      254 => '67000 kg',
      255 => '69000 kg',
      256 => '71000 kg',
      257 => '73000 kg',
      258 => '75000 kg',
      259 => '77500 kg',
      260 => '80000 kg',
      261 => '82500 kg',
      262 => '85000 kg',
      263 => '87500 kg',
      264 => '90000 kg',
      265 => '92500 kg',
      266 => '95000 kg',
      267 => '97500 kg',
      268 => '100000 kg',
      269 => '103000 kg',
      270 => '106000 kg',
      271 => '109000 kg',
      272 => '112000 kg',
      273 => '115000 kg',
      274 => '118000 kg',
      275 => '121500 kg',
      276 => '125000 kg',
      277 => '128500 kg',
      278 => '132000 kg',
      279 => '136000 kg',
    ];

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

    return sprintf(
      'Kravnesības indekss: %s - %s<br>Ātruma indekss: %s',
      $weight,
      $carryCaps[$weight] ?? 'N/A',
      $speedCaps[$speed] ?? 'N/A'
    );

  }


}

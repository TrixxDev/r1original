<?php

namespace App\Http\Controllers;

use App\Broadcasting\UpdateStockChannel;
use App\Helper\Image;
use App\Http\Controllers\CartController;
use App\Helper\Tires;
use App\Models\Audit;
use App\Models\Autobrand;
use App\Models\Autotire;
use App\Models\Autotread;
use App\Models\Code;
use App\Models\Motobrand;
use App\Models\Mototread;
use App\Models\Quadrbrand;
use App\Models\Quadrtread;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Route;
use View;


class AutoTireController extends Controller
{


    public $brands;
    public $season;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $autoTiresD1;
    public $autoTiresD2;
    public $autoTiresD3;
    public $model = 'Autotire';
    public $tiresSize;
    public $lastYear;
    public $type;
    public $code;
    public $fuel;
    public $wet;
    public $noise;
    public $availability;
    public $code_array = [];
    public $filterCount = 0;

    public $cartQty = 4;

  public function __construct(Request $request)
  {


    // || strpos(str_replace(url('/'), '', \URL::previous()), 'vasaras-riepas') !== false

    if (strpos(url()->current(), 'vasaras-riepas') !== false) {
      $this->season = 1;
      View::share('season', 'Vasaras riepas');
      View::share('current_url', 'vasaras-riepa');
      View::share('season_title', 'vasaras-riepas');
    } else if (strpos(url()->current(), 'ziemas-riepas') !== false || \Request::route()->getName() == 'home') {
      $this->season = 2;
      View::share('season', 'Ziemas riepas');
      View::share('current_url', 'ziemas-riepa');
      View::share('season_title', 'ziemas-riepas');
    }
    View::share('season_id', $this->season);

    $this->brands = $this->tires_getBrands();

    $this->autoTiresD1 = Tires::getAutoTiresSize('d1', $this->season);
    $this->autoTiresD2 = Tires::getAutoTiresSize('d2', $this->season);
    $this->autoTiresD3 = Tires::getAutoTiresSize('d3', $this->season);

    $this->currBrand = ($request->brand == 'Visi') ? 'Visi' : $request->brand;
    $this->currBrand = ($this->currBrand === NULL) ? 'Visi' : $request->brand;

    $this->d1 = ($request->d1 == 'Visi') ? 'Visi' : $request->d1;
    $this->d2 = ($request->d2 == 'Visi') ? 'Visi' : $request->d2;
    $this->d3 = ($request->d3 == NULL) ? '16' : $request->d3;

    $this->types = ($request->types) ? $request->types : [];
    $this->code = ($request->code) ? $request->code : [];
    $this->fuel = ($request->fuel) ? $request->fuel : [];
    $this->wet = ($request->wet) ? $request->wet : [];

    if ($request->d1 == NULL && $this->d1 == NULL) {
      $this->d1 = 205;
    }

    if ($request->d2 == NULL && $this->d2 == NULL) {
      $this->d2 = 55;
    }

    $codes = Code::all();

    foreach ($codes as $code) {
      $this->code_array[$code->name] = $code->explanation;
    }


//        if ($request->d3 == NULL && $this->d3 == NULL) {
//            $this->d3 = 16;
//        }

        View::share('brands', $this->brands);
        View::share('autoTiresD1', $this->autoTiresD1);
        View::share('autoTiresD2', $this->autoTiresD2);
        View::share('autoTiresD3', $this->autoTiresD3);
        View::share('currBrand', $this->currBrand);
        View::share('d1', $this->d1);
        View::share('d2', $this->d2);
        View::share('d3', $this->d3);
        View::share('types', $this->types);
        View::share('type', $this->type);
        View::share('code', $this->code);
        View::share('fuel', $this->fuel);
        View::share('wet', $this->wet);
        View::share('noise', $this->noise);
	      View::share('code_array', $this->code_array);
	      View::share('codes', $this->tires_getCodes());
	      View::share('filterCount', $this->filterCount);
	      View::share('availability', explode(' ', $this->availability));
        View::share('cartQty', $this->cartQty);
  }

  public function tires() {

    $tires = Autotire::leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->when($this->d1, function($query) {
        $query->where('d1', $this->d1);
      })->when($this->d2, function($query) {
        $query->where('d2', $this->d2);
      })->when($this->d3, function($query) {
        $query->where('d3', $this->d3);
      })->where('auto_treads.season', $this->season)
      ->where('auto_tires.visible_users', '<>', 0)
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->orderBy('price2', 'DESC')
      ->groupBy('article')
      ->simplePaginate();

    $code_array = $this->code_array;

    return view('tires.auto.tires', compact('tires', 'code_array'));
  }

  public function generateCombinations(array $array) {
    foreach (array_pop($array) as $value) {
      if (count($array)) {
        foreach ($this->generateCombinations($array) as $combination) {
          yield array_merge([$value], $combination);
        };
      } else {
        yield [$value];
      }
    }
  }

  public function checkArrays($arrays) {

    foreach ($arrays as $array) {
      if (is_array($array)) {
        return true;
      }
    }

    return false;
  }

  public function api_tires(Request $request, $season) {
    try {

      $html = '';

      $page = ($request->page) ? (int) $request->page : 1; // Get the current page from the request, default to 1
      $perPage = 80; // Number of items per page

      $offset = ($page - 1) * $perPage;

      $d1 = ($request->d1 == 'Visi') ? '' : $request->d1;
      $d2 = ($request->d2 == 'Visi') ? '' : $request->d2;
      $d3 = ($request->d3 == 'Visi') ? '' : $request->d3;

      $this->availability = $availability = '';
      if (isset($request->availability)) {
        $availability = explode(' ', $request->availability);
        $this->availability = $availability = implode('+', $availability);
      }

      $currBrand = ($request->brand == 'Ražotājs') ? '' : $request->brand;

      $selectedTires = explode(',', $request->selected);
      $topTires = $request->top;
      $show_selected = $request->show_selected;

      $fastsearch = $request->fastsearch;

      if ($fastsearch) {
        $splited = $this->splitInput($fastsearch);
        $this->d1 = $d1 = $splited['d1'];
        $this->d2 = $d2 = $splited['d2'];
        $this->d3 = $d3 = $splited['d3'];
      }

      if (!is_null($request->code)) {
        $this->code = explode(' ', $request->code);
        $categorizedCodes = [];
        foreach ($this->code as $code) {
          if ($code === 'SOUND') {
            $categorizedCodes['SOUND'] = ['SOUND', 'ACOUSTIC', 'NCS', 'SCT'];
          } elseif ($code === 'XL') {
            $categorizedCodes['XL'] = ['XL', 'HL'];
          } else {
            $categorizedCodes[$code] = $code; // Other codes without subcategories
          }
        }

        $this->code = $categorizedCodes;

        $permutations = [];
        $hasArrays = false; // Flag to check if we need permutations

        $findLike = count($categorizedCodes) === 1;

        foreach ($categorizedCodes as $key => $value) {
          if (is_array($value)) { // If a code has an array of subcategories
            $hasArrays = true;
            $temp = [];

            // Initial permutation if other codes present
            if ($permutations) {
              foreach ($permutations as $perm) {
                foreach ($value as $subcategory) {
                  $temp[] = $perm . '%' . $subcategory;
                }
              }
            } else {
              // First set of permutations
              $temp = $value;
            }

            $permutations = $temp;

          } else {
            // Single codes without subcategories
            if ($permutations) {
              foreach($permutations as &$perm) {
                $perm .= '%' . $value;
              }
            } else {
              $permutations[] = $value;
            }
          }
        }


        if ($hasArrays) {
          $codeCombinations = $permutations;
        } else {
          $codeCombinations = [implode('%', $categorizedCodes)];
        }
        //        $selectedCodes = [];
//        foreach ($this->code as $codes) {
//          $selectedCodes[$codes] = $codes;
//        }
//        if (isset($selectedCodes['SOUND'])) {
//          $selectedCodes['SOUND'][] = 'SOUND';
//          $selectedCodes['SOUND'][] = 'ACOUSTIC';
//          $selectedCodes['SOUND'][] = 'NCS';
//          $selectedCodes['SOUND'][] = 'SCT';
//        }
//        if (isset($selectedCodes['XL'])) $selectedCodes['XL'][] = 'HL';
      }

      $this->type = $selectedTypes = explode(' ', $request->type);

      $typeConditions = [
        1 => ['auto_tires.type', '=', 1],
        2 => ['auto_tires.type', '=', 2],
        3 => ['auto_tires.type', '=', 3],
        4 => ['auto_tires.type', '=', 4],
      ];

      $this->fuel = $selectedFuel = explode(' ', $request->fuel);
      $this->wet = $selectedWet = explode(' ', $request->wet);
      $this->noise = $selectedNoise = explode(' ', $request->noise);

      $fuelConditions = [
        'A' => ['auto_tires.eco', '=', 'A'],
        'B' => ['auto_tires.eco', '=', 'B'],
        'C' => ['auto_tires.eco', '=', 'C'],
        'D' => ['auto_tires.eco', '=', 'D'],
        'E' => ['auto_tires.eco', '=', 'E'],
        'F' => ['auto_tires.eco', '=', 'F'],
        'G' => ['auto_tires.eco', '=', 'G'],
      ];

      $wetConditions = [
        'A' => ['auto_tires.wet', '=', 'A'],
        'B' => ['auto_tires.wet', '=', 'B'],
        'C' => ['auto_tires.wet', '=', 'C'],
        'D' => ['auto_tires.wet', '=', 'D'],
        'E' => ['auto_tires.wet', '=', 'E'],
        'F' => ['auto_tires.wet', '=', 'F'],
        'G' => ['auto_tires.wet', '=', 'G'],
      ];

      $noiseConditions = [
        'A' => ['auto_tires.noise', 'LIKE', '%A%'],
        'B' => ['auto_tires.noise', 'LIKE', '%B%'],
        'C' => ['auto_tires.noise', 'LIKE', '%C%'],
      ];

      $tires = Autotire::selectRaw('auto_tires.*, auto_tires.quantity as tire_quantity, auto_treads.*, (SELECT SUM(quantity) FROM auto_stock WHERE auto_stock.tire_id = auto_tires.tire_id) as stock_quantity')
        ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
        ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
        ->when($currBrand, function ($query) use ($currBrand) {
          $query->where('auto_brands.slug', \Str::slug($currBrand));
        })->when($d1, function ($query) use ($d1) {
          $query->where('d1', $d1);
        })->when($d2, function ($query) use ($d2) {
          $query->where('d2', $d2);
        })->when($d3, function ($query) use ($d3) {
          $query->where('d3', $d3);
        })->when($availability, function ($query) use ($availability) {
          switch ($availability) {
            case 'green':
              {
                $query->where('auto_tires.quantity', '>', 0);
                break;
              }
            case 'green+yellow':
              {
                $query->where(function ($query) {
                  $query->where('auto_tires.quantity', '>', 0)
                    ->orWhere(function ($query) {
                      $query->whereRaw('auto_tires.tire_id IN (SELECT tire_id FROM auto_stock WHERE quantity > 0)')
                            ->where('auto_tires.quantity', '=', 0);
                    });
                });
                break;
              }
            case 'green+red':
              {
                $query->where(function ($query) {
                  $query->where('auto_tires.quantity', '>', 0); // Green dot filter
                  $query->orWhere(function ($query) {
                    $query->where('auto_tires.quantity', '=', 0); // Red dot filter
                    $query->whereRaw('auto_tires.tire_id NOT IN (SELECT tire_id FROM auto_stock WHERE quantity > 0)');
                  });
                });
                break;
              }
            case 'yellow':
              {
                $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '>', 0);
                break;
              }
            case 'yellow+red':
              {
                $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '>=', 0);
                break;
              }
            case 'red':
              {
                $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '<=', 0);
                break;
              }
          }
        })->when($this->code, function ($query) use (&$codeCombinations, &$findLike) {
          $query->where(function ($query) use ($codeCombinations, $findLike) {
            if (count($codeCombinations) > 1) {
              foreach ($codeCombinations as $combination) {
                $query->orWhere('code', 'like', '%' . $combination . '%');
              }
            } else {
              $query->where('code', 'like', '%' . implode(' ', $codeCombinations) . '%');
            }
          });
        })->when($this->type, function ($query) use ($typeConditions, $selectedTypes) {
          $query->where(function ($query) use ($selectedTypes, $typeConditions) {
            $firstCondition = true;

            foreach ($selectedTypes as $type) {
              if (isset($typeConditions[$type])) {
                $condition = $typeConditions[$type];
                if ($firstCondition) {
                  $query->where(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                  $firstCondition = false;
                } else {
                  $query->orWhere(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                }
              }
            }
          });
        })->when($this->fuel, function ($query) use ($fuelConditions, $selectedFuel) {
          $query->where(function ($query) use ($selectedFuel, $fuelConditions) {
            $firstCondition = true;

            foreach ($selectedFuel as $type) {
              if (isset($fuelConditions[$type])) {
                $condition = $fuelConditions[$type];
                if ($firstCondition) {
                  $query->where(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                  $firstCondition = false;
                } else {
                  $query->orWhere(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                }
              }
            }
          });
        })->when($this->wet, function ($query) use ($wetConditions, $selectedWet) {
          $query->where(function ($query) use ($selectedWet, $wetConditions) {
            $firstCondition = true;

            foreach ($selectedWet as $type) {
              if (isset($wetConditions[$type])) {
                $condition = $wetConditions[$type];
                if ($firstCondition) {
                  $query->where(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                  $firstCondition = false;
                } else {
                  $query->orWhere(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                }
              }
            }
          });
        })->when($this->noise, function ($query) use ($noiseConditions, $selectedNoise) {
          $query->where(function ($query) use ($selectedNoise, $noiseConditions) {
            $firstCondition = true;

            foreach ($selectedNoise as $type) {
              if (isset($noiseConditions[$type])) {
                $condition = $noiseConditions[$type];
                if ($firstCondition) {
                  $query->where(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                  $firstCondition = false;
                } else {
                  $query->orWhere(function ($query) use ($condition) {
                    call_user_func_array([$query, 'where'], $condition);
                  });
                }
              }
            }
          });
        })->when($show_selected, function ($query) use ($selectedTires) {
          $query->whereIn('tire_id', $selectedTires);
        })->when($topTires, function ($query) {
          $query->where('top', 1);
        })->where('auto_treads.season', $season)
        ->where('auto_tires.visible_users', '<>', 0)
        ->orderBy('d3', 'ASC')
        ->orderBy('d1', 'ASC')
        ->orderBy('d2', 'ASC')
        ->orderBy('price2', 'DESC')
        ->groupBy('auto_tires.article');

      $totalItems = $tires->count();
      $totalPages = ceil($totalItems / $perPage);

      $tires = $tires->skip($offset)
                     ->take($perPage)
                     ->get();

      if ($season == 1) {
        $type = '';
        $season_title = 'Vasaras riepas';
      } else {
        $type = '<th scope="col" class="hidden-sm-down text-center">Tips</th>';
        $season_title = 'Ziemas riepas';
      }

      $fullSize = '';
      $loopIndex = 0;

      if ($request->table_type === 'list') {
        if ($tires->count() > 0) {

          foreach ($tires as $index => $tire) {

            $tire->includeStock = true;
            $tire->fullName = $tire->getFullNameAttribute();
            $tire->fullSize = $tire->getFullSizeAttribute();
            $current_url = ($season == 1) ? 'vasaras-riepa' : 'ziemas-riepa';
            $tire->getUrl = route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->brand_id)->title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]);
            $tire->fullTitle = $tire->getTitleAttribute();
            $tire->lisiDesc = $tire->lisiDesc($tire->li, $tire->si);
            $tire->codeExplain = $tire->getCodeExplainAttribute();
            $tire->dotAvailable = $tire->getDotAvailableAttribute();
            $tire->stockAvailability = $tire->getStockAvailabilityAttribute();
            $tire->stockCount = $tire->getStockCount();

            if ($index === 0) {
              $html .= '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                      Filtrs
                                    </button>';
              $html .= '<span class="text-uppercase flipped-title tire-brand-name" style="color: black">' . $season_title .  '</span>';
            }
            $index++;
            if ($fullSize !== $tire->fullSize) {
              $loopIndex = 0;
              $html .= '<table id="tires-table" class="table table-striped summer-sorter tires-table table-hover tablesorter">';
              $html .= '<thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          ' . $type . '
                          <th scope="col" class="hidden-sm-down text-center">Kods</th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div>
                          </th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div>
                          </th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-sound" title="Troksnis"></div>
                          </th>

                          <th id="store-price-button" scope="col" class="text-center">
                            Veikala cena
                          </th>

                          <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                          <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                          <th scope="col"></th>
                          <th scope="col">
                            <div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div>
                          </th>

                        </tr>
                        </thead>';
              $html .= '<tbody id="tires-table-body">';
              $html .= '<h4 class="tire-brand-name">' . $tire->fullSize . '</h4>';
            }
            $html .= '<tr class="tire-table-row" role="row">';
            $html .= '<th scope="row" class="tire-table-checkbox"><input type="checkbox" value="' . $tire->tire_id . '" name="product_ids[]" class="tire-table-checkbox" title=""></th>';
            $html .= '<td class="table-tire-name-cell"><a class="tire-table-link tippy image" data-tippy-content="<div><img data-src=\'https://r1riepas.lv/storage/auto/tread/' . $tire->tread_id . '-o.jpg\'></div>" href="' . $tire->getUrl . '" data-content="' . $tire->fullName . '" data-article="' . $tire->article . '" data-quantity="4"><div class="table-link-title">' . $tire->fullTitle . '</div></a></td>';
            $html .= '<td class="hidden-sm-down text-center"><span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px; text-align: left;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->lisiDesc . '</span></div>">' . $tire->li . $tire->si . '</span></td>';
            if ($tire->season == 2) {
              $html .= '<td scope="col" class="hidden-sm-down text-center">';
              if ($tire->type == 1) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="1" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . \App\Helper\Tires::codeExplain('ms tips') . '</span></div>"><img src="/images/ms.png" alt="ms"></span>';
              if ($tire->type == 2) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="2" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . \App\Helper\Tires::codeExplain('radžojamu tips') . '</span></div>"><img src="/images/radzeb.png" alt="ms"></span>';
              if ($tire->type == 3) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="3" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . \App\Helper\Tires::codeExplain('ar radzēm tips') . '</span></div>"><img src="/images/radzea.png" alt="ms"></span>';
              if ($tire->type == 4) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="4" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . \App\Helper\Tires::codeExplain('ziemas tips') . '</span></div>"><img src="/images/parsla.png" alt="ms"></span>';
              $html .= '</td>';
            }
            $html .= '<td class="hidden-sm-down text-center"><span class="tippy lisi-tooltip code-explain" data-tippy-content="<div style=\'padding: 5px; text-align: left;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->codeExplain . '</span></div>">' . $tire->code . '</span></td>';
            $html .= '<td class="hidden-sm-down text-center"><span class="fuel-explain">' . $tire->eco . '</span></td>';
            $html .= '<td class="hidden-sm-down text-center"><span class="wet-explain">' . $tire->wet . '</span></td>';
            $html .= '<td class="hidden-sm-down text-center"><span class="noise-explain">' . $tire->noise . '</span></td>';
            $html .= '<td id="store-price" class="text-center store-price">€ ' . $tire->price1 . '</td>';
            $html .= '<td id="sale-price" class="text-center tire-price-red sale-price">€ ' . $tire->price2 . '</td>';
            if ($tire->comment == 'Izpārdošana!' || $tire->priceoffer == 1) {
              $html .= '<td class="hidden-sm-down text-center sellout">' . $tire->comment . '</td>';
            } else {
              $html .= '<td class="hidden-sm-down text-center">' . $tire->comment . '</td>';
            }
            $html .= '<td class="shopping-cart-col"><div class="clearfix atc_div text-right">';
            if (Auth::check()) {
              if (Auth::user()->hasRole('administrators')) {
                $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#" data-info="' . $tire->tire_id . '"><i class="material-icons">add_shopping_cart</i></button>';
              } else {
                $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#blockcart-modal" data-info="' . $tire->tire_id . '"><i class="material-icons">add_shopping_cart</i></button>';
              }
            } else {
              $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#blockcart-modal" data-info="' . $tire->tire_id . '"><i class="material-icons">add_shopping_cart</i></button>';
            }
            $html .= '</div></td>';
            $html .= '<td class="dot-availability text-center"><span class="tippy lisi-tooltip dot ' . $tire->dotAvailable . '" data-color="' . $tire->dotAvailable . '" data-tippy-content=\'<div style="padding: 5px; text-align: left;"><span style="color: black; font-size: 15px; line-height: 28px;">' . $tire->stockAvailability . '</span></div>\'></span></td>';
            $html .= '</tr>';
            $fullSize = $tire->fullSize;
            if ($fullSize !== $tire->fullSize) {
              $html .= '</tbody>';
              $html .= '</table>';
            }
          }
        }
      } else if ($request->table_type === 'grid') {
        $html .= '<div class="tire-image-container">';
        $cbrand = '';
        $index = 0;
        foreach ($tires as $tire) {

          $tire->fullSize = $tire->getFullSizeAttribute();
          $current_url = ($season == 1) ? 'vasaras-riepa' : 'ziemas-riepa';
          $tire->getUrl = route($current_url, [Str::slug(Tires::getAutoTireBrand($tire->brand_id)->title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]);

          $brand = $tire->fullSize;
          $tire->includeStock = true;
          if ($cbrand != $brand) {
            $html .= '</div><h4 class="tire-brand-name grid-t" style="margin-left: 5px;">' . $brand;
            if ($index == 0) {
                $html .= ' <span class="tire-type-title">' . $season_title . '</span>';
            }
            $html .= '<span style="margin: 0 auto;"></span>';
            $html .= '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                      Filtrs
                                    </button></h4>
                          <div class="row grid-ex pr-1 mobile-tire-container" style="padding-left: 5px;">';
            $cbrand = $brand;
          }
          $html .= '<a href="' . $tire->getUrl . '" class="grid-view-link" data-article="' . $tire->article . '">';
          $html .= '<div class="tire-image-card sort-order">';
          $html .= '<div class="text-center image-grid-overflow">';
          $html .= Image::showGrid('auto', $tire->make_id);
          $html .= '</div>';

          $html .= '<div class="tire-list-caption">';

          $html .= '<div class="card-title-text"><span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->title . '</span></div>">' . $tire->title . '</span></div>';

          $html .= '<div class="tire-tread">';
          $html .= '<b>' . $tire->fullSize . ' </b>';
          $html .= '<span data-toggle="tooltip" title="<span style=\'color: black\'>' . $tire->lisiDesc($tire->li, $tire->si) . '</span>">' . $tire->li . $tire->si . ' </span>';
          $html .= '<span class="tire-image-code">' . $tire->code . '</span>';
          $html .= '</div>';
          $html .= '<div style="display: flex;">';
          $html .= '<input type="checkbox" name="product_ids[]" value="' . $tire->tire_id . '" style="margin-right: 5px;">';
          $html .= '<div class="rim-price-old" style="align-self: center;">€' . $tire->price1 . '</div>';
          $html .= '<div class="rim-price-red" style="align-self: center;">€' . $tire->price2 . '</div>';
          if ($tire->season == 2) {
            $html .= '<div class="hidden-sm-down text-center" style="display: none;">';
            if ($tire->type == 1) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="1"><img src="/images/ms.png" alt="ms"></span>';
            if ($tire->type == 2) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="2"><img src="/images/radzeb.png" alt="ms"></span>';
            if ($tire->type == 3) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="3"><img src="/images/radzea.png" alt="ms"></span>';
            if ($tire->type == 4) $html .= '<span class="tippy lisi-tooltip type-explain" data-type="4"><img src="/images/parsla.png" alt="ms"></span>';
            $html .= '</div>';
          }
          $html .= '<div class="hidden-sm-down text-center" style="display: none;"><span class="fuel-explain">' . $tire->eco . '</span></div>';
          $html .= '<div class="hidden-sm-down text-center" style="display: none;"><span class="wet-explain">' . $tire->wet . '</span></div>';
          $html .= '<div class="hidden-sm-down text-center" style="display: none;"><span class="noise-explain">' . $tire->noise . '</span></div>';
          $html .= '<span style="margin-left: auto;" data-toggle="tooltip" title="<span style=\'color: black\'>Pievienot grozam</span>">';
          if (Auth::check()) {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $tire->tire_id . '" onclick="event.preventDefault()" data-target="#">';
          } else {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $tire->tire_id . '" onclick="event.preventDefault()" data-target="#blockcart-modal">';
          }
          $html .= '<i class="material-icons">add_shopping_cart</i>';
          $html .= '</button>';
          $html .= '</span>';

          $html .= '<span class="tippy lisi-tooltip grid-dot ' . $tire->dotAvailable . $tire->stockCount . '" data-color="' . $tire->dotAvailable . '" data-tippy-content=\'<div style="padding: 5px;"><span style="color: black; font-size: 15px;">' . $tire->stockAvailability . '</span></div>\'></span>';
          $html .= '<span class="sort-order" style="display: none;">' . $tire->dotAvailable . '</span>';
          $html .= '</span>';
          $html .= '</div>';
          $html .= '</div>';
          $html .= '</div>';
          $html .= '</a>';
        }
        $index++;
        $html .= '</div>';
      }

      if ($tires->count() <= 0) {
        $html .= '<div class="container"><div class="col-md-12 mt-1 alert alert-danger">Ar šādiem parametriem nav atrasta neviena pozīcija.</div></div>';
      }

      $html .= $this->generatePagination($page, $totalPages, $offset, $perPage, $totalItems);

      return response()->json($html, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function tires_ajax(Request $request) {

    $tire = Autotire::with('tread')->selectRaw('auto_tires.*, auto_treads.*')
      ->rightJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->where('auto_treads.season', $this->season)
      ->where('auto_tires.tire_id', $request->tire_id)
      ->where('auto_tires.visible_users', '<>', 0)
      ->first();

    if ($request->quantity) {
      $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
    } else {
      $cart = CartController::addProduct($this->model, $tire->tire_id, $this->cartQty);
    }

    $quantity = Cart::count();
    //dd(Cart::subTotal());
    $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
    $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
  }

  public function splitInput($input) {
    $input = str_replace(',', '.', $input);
    $d3_suffix = '';

    if (substr($input, -1) === 'C') {
      $input = substr($input, 0, -1);
      $d3_suffix = 'C';
    }

    if (preg_match('/^(\d{2})(\d{2}\.\d{1,2})(\d{2})$/', $input, $matches)) {
      $d1 = $matches[1];
      $d2 = $matches[2];
      $d3 = $matches[3] . $d3_suffix;
    } else {
      if (preg_match('/^(\d{3})(\d{2})(\d{2})$/', $input, $matches)) {
        $d1 = $matches[1];
        $d2 = $matches[2];
        $d3 = $matches[3] . $d3_suffix;
      } else {
        $d1 = 1;
        $d2 = 1;
        $d3 = 1;
      }
    }

    return compact('d1', 'd2', 'd3');
  }

  public function tires_find(Request $request) {

    return view('tires.auto.tires');
//    'availability' => $this->availability
  }

  public function tires_filter(Request $request) {
    DB::enableQueryLog();

    $this->d1 = $request->d1;
    $this->d2 = $request->d2;
    $this->d3 = $request->d3;

    $where = '`auto_treads`.`season` = ' . $this->season;

    if ($this->d1 !== NULL) {
      $where .= ' AND `auto_tires`.`d1` = ' . $this->d1;
    }
    if ($this->d2 !== NULL) {
      $where .= ' AND `auto_tires`.`d2` = ' . $this->d2;
    }

    $where .= ' AND `auto_tires`.`d3` = ' . $this->d3;

    if ($request->availabilities['green'] == 1) {
      $where .= " AND `auto_tires`.`quantity` > 0";
    }
    if ($request->availabilities['yellow'] == 1) {
      $where .= " AND `auto_stock`.`quantity` > 0";
    }
    if ($request->availabilities['red'] == 1) {
      $where .= ' AND `auto_tires`.`quantity` = 0';
    }

    $tires = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->leftJoin('auto_stock', 'auto_tires.tire_id', '=', 'auto_stock.tire_id')
      ->whereRaw($where)
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->orderBy('price2', 'DESC')
      ->get();

    //dd(DB::getQueryLog());

    $tires_array = [];

    foreach ($tires as $tire) {

      $size = $tire->d1 . '/' . $tire->d2 . 'R' . $tire->d3;

      $tires_array[$size] = $tire;

    }

    return json_encode($tires_array);
  }

  public function tires_search(Request $request) {

    $this->currBrand = ($request->brand == 'Visi') ? '' : $request->brand;

    $code = $request->code;
    $sql = Autotread::selectRaw('auto_treads.*')
                      ->selectRaw('auto_brands.*, auto_brands.title as brand_title')
                      ->leftJoin('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
                      ->where('auto_treads.season', $this->season)
                      ->where('auto_brands.title', $this->currBrand)
                      ->get();
    $makes = [];
    foreach ($sql as $make) {
      $makes[] = $make->tread_id;
    }

    $this->d1 = ($this->d1 == 'Visi') ? '' : $request->d1;
    $this->d2 = ($this->d2 == 'Visi') ? '' : $request->d2;

    $tires = Autotire::join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')->when($makes, function($query) use ($makes) {
      $query->whereIn('make_id', $makes);
    })->when($this->d1, function($query) {
      $query->where('d1', $this->d1);
    })->when($this->d2, function($query) {
      $query->where('d2', $this->d2);
    })->when($code, function($query) use ($code){
      $query->where('code', 'LIKE', '%' . $code . '%');
    })->where('d3', $this->d3)
      ->where('auto_treads.season', $this->season)
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->orderBy('price2', 'DESC')
      ->paginate()->appends($request->query());

    return view('tires.auto.tires',
      compact('tires', 'code')
    );
  }

  public function tires_tread(Request $request, $brand, $tread, $tire) {

    DB::enableQueryLog();

    $selectedTires = [];
    if ($request->input('selected')) {
      $selectedTires = explode(',', $request->input('selected'));
    }

    $brand = Autobrand::where('slug', $brand)->first();

    $tires = Autotire::selectRaw('auto_tires.*, auto_treads.*, auto_brands.*,
                                                auto_brands.title as brands_title')
      ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
      ->where('auto_brands.title', $brand->title)
      ->where('auto_treads.t_title', str_replace('_', '/', $tread))
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('d2', 'ASC')
      ->get();

    $currTire = Autotire::with('tread')->leftJoin('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->where('auto_treads.t_title', str_replace('_', '/', $tread))
      ->where('auto_tires.tire_id', $tire)
      ->first();

    $currBrand = Autobrand::where('brand_id', $currTire->brand_id)->first();

    $currTire->includeStock = true;

    return view('tires.auto.autotread',
      compact('tires', 'currTire', 'currBrand', 'selectedTires')
    );
  }

  public function get_sizes($season)
  {
    try {
      $tireSizes = Autotire::join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
        ->select(DB::raw('CONCAT(D1, D2, D3) as tire_size'))
        ->where('auto_tires.visible_users', '<>', 0)
        ->where('auto_treads.season', $season)
        ->orderBy('d3', 'ASC')
        ->orderBy('d1', 'ASC')
        ->orderBy('d2', 'ASC')
        ->groupBy('auto_tires.article')
        ->distinct()
        ->get();

      return response()->json($tireSizes, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function tires_getCodes()
  {

    $codes = Autotire::join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
      ->selectRaw('MIN(code) AS code')
      ->where('auto_tires.visible_users', '<>', 0)
      ->where('auto_treads.season', 1)
      ->where('code', 'NOT LIKE', '%DOT%')
      ->where('code', 'NOT LIKE', '')
      ->groupBy('code')
      ->get();

    $explodedCodes = [];
    $uniqueCodes = [];

    foreach ($codes as $codeString) {
      $explodedCodes[] = explode(' ', $codeString->code);
    }

    foreach ($explodedCodes as $code) {
      foreach ($code as $code1) {
        $uniqueCodes[] = $code1;
      }
    }

    return array_filter(array_unique($uniqueCodes));
  }

  public function tires_getBrands()
  {
    $brands = Autobrand::join('auto_treads', 'auto_brands.brand_id', '=', 'auto_treads.brand_id')
      ->join('auto_tires', 'auto_treads.tread_id', '=', 'auto_tires.make_id')
      ->where('auto_tires.visible_users', '<>', 0)
      ->where('auto_treads.season', $this->season)
      ->distinct()
      ->pluck('auto_brands.title', 'auto_brands.brand_id')
      ->sort(SORT_NATURAL | SORT_FLAG_CASE);

    return $brands->all();
  }

  public function generatePagination($page, $totalPages, $offset, $perPage, $totalItems)
  {
    $html = '';

    if ($totalPages > 1) {
      $html .= '<div class="col-sm-12 col-md-12 pagination-col">';
      $html .= '<div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">';
      $html .= '<div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">';
      if (($offset + $perPage) > $totalItems) {
        $html .= $offset + 1 . ' līdz ' . $totalItems . ' ieraksti no ' . $totalItems . ' ierakstiem';
      } else {
        $html .= $offset + 1 . ' līdz ' . ($offset + $perPage) . ' ieraksti no ' . $totalItems . ' ierakstiem';
      }
      $html .= '</div>';
      $html .= '<ul class="pagination">';

      // Previous button
      $html .= '<li class="paginate_button page-item previous ';
      $html .= ($page == 1) ? 'disabled' : '';
      $html .= '">';
      $html .= '<a href="#" class="page-link">Atpakaļ</a>';
      $html .= '</li>';

      // Page numbers
      $visiblePages = 8; // Number of visible pages between first and last
      $firstPages = 2; // Number of pages to show at the beginning
      $lastPages = 2; // Number of pages to show at the end

      // Display first pages
      $startFirstPages = 1;
      $endFirstPages = min($firstPages, $totalPages);

      for ($i = $startFirstPages; $i <= $endFirstPages; $i++) {
        $html .= '<li class="paginate_button page-item ';
        $html .= ($i == $page) ? 'active' : '';
        $html .= '">';
        if ($i == $page) {
          $html .= '<span style="pointer-events: none;" class="page-link">' . $i . '</span>';
        } else {
          $html .= '<a href="#" data-page="' . $i . '" class="page-link">' . $i . '</a>';
        }
        $html .= '</li>';
      }

      // Display pages between first and last
      $startPage = max($firstPages + 1, min($page - floor($visiblePages / 2), $totalPages - $visiblePages + 1));
      $endPage = min($totalPages, $startPage + $visiblePages - 1);

      for ($i = $startPage; $i <= $endPage; $i++) {
        $html .= '<li class="paginate_button page-item ';
        $html .= ($i == $page) ? 'active' : '';
        $html .= '">';
        if ($i == $page) {
          $html .= '<span style="pointer-events: none;" class="page-link">' . $i . '</span>';
        } else {
          $html .= '<a href="#" data-page="' . $i . '" class="page-link">' . $i . '</a>';
        }
        $html .= '</li>';
      }

      // Display last pages
      $startLastPages = max($totalPages - $lastPages + 1, $endPage + 1);
      for ($i = $startLastPages; $i <= $totalPages; $i++) {
        $html .= '<li class="paginate_button page-item ';
        $html .= ($i == $page) ? 'active' : '';
        $html .= '">';
        if ($i == $page) {
          $html .= '<span style="pointer-events: none;" class="page-link">' . $i . '</span>';
        } else {
          $html .= '<a href="#" data-page="' . $i . '" class="page-link">' . $i . '</a>';
        }
        $html .= '</li>';
      }

      // Next button
      $html .= '<li class="paginate_button page-item next ';
      $html .= ($page == $totalPages) ? 'disabled' : '';
      $html .= '">';
      $html .= '<a href="#" class="page-link">Uz priekšu</a>';
      $html .= '</li>';

      $html .= '</ul>';
      $html .= '</div>';
      $html .= '</div>';
    }

    return $html;
  }

}

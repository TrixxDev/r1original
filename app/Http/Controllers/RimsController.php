<?php

namespace App\Http\Controllers;

use App\Helper\Image;
use App\Helper\Tires;
use App\Models\FilterCars;
use App\Models\FilterSizes;
use App\Models\FilterModels;
use App\Models\Rim;
use App\Models\Rimbrand;
use App\Models\Rimmake;
use Dflydev\DotAccessData\Data;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RimsController extends Controller
{

  public $currentCar;
  public $currentModel;
  public $currentR1;
  public $currentR2;
  public $currentR;

  public $currentForm;

  public $currentWid;
  public $currentWid2;
  public $currentSkr;
  public $currentPcd;
  public $currentEt;
  public $currentEt2;
  public $currentDia;
  public $currentCenter;

  public $model = 'Rim';
  public $cartQty = 4;

  public $models;

  public function __construct(Request $request)
  {

    $this->d1 = ($request->d1 == 'Visi') ? 'Visi' : $request->d1;

    $this->currentCar = ($request->car) ? $request->car : '';
    $this->currentModel = ($request->model) ? $request->model : '';
    $this->currentR1 = ($request->r1) ? $request->r1 : '';
    $this->currentR2 = ($request->r2) ? $request->r2 : '';
    $this->currentR = $this->currentR1;

    $this->currentForm = ($request->currentForm == 1) ? 1 : 2;

    $this->currentWid = ($request->currentWid) ? $request->currentWid : 6;
    $this->currentWid2 = ($request->currentWid2) ? $request->currentWid2 : 8;
    $this->currentSkr = ($request->currentSkr) ? $request->currentSkr : 5;
    $this->currentPcd = ($request->currentPcd) ? $request->currentPcd : 112;
    $this->currentEt = ($request->currentEt) ? $request->currentEt : '';
    $this->currentEt2 = ($request->currentEt2) ? $request->currentEt2 : '';
    $this->currentDia = ($request->currentDia) ? $request->currentDia : 16;
    $this->currentCenter = ($request->currentCenter) ? $request->currentCenter : '';

    if (($this->currentSkr !== false)||($this->currentPcd !== false)||($this->currentEt !== false)) {
      $this->currentCar = -1;
      $this->currentModel = -1;
      if ($this->currentR2 !== false) $this->currentR = $this->currentR2;
    }

    if ($this->currentCar === -1) $this->currentModel = false;

    View::share('brandList', $this->getBrandList());
    View::share('currentCar', $this->currentCar);
    View::share('currentWid', $this->currentWid);
    View::share('currentWid2', $this->currentWid2);
    View::share('currentEt', $this->currentEt);
    View::share('currentEt2', $this->currentEt2);
    View::share('currentPcd', $this->currentPcd);
    View::share('currentSkr', $this->currentSkr);
    View::share('currentDia', $this->currentDia);
    View::share('currentCenter', $this->currentCenter);
    $options = $this->getRimOptions();
    View::share([
      'widths' => $options['widths'],
      'offsets' => $options['offsets'],
      'centers' => $options['rim_center'],
      'diameters' => $options['diameters'],
      'lugs' => $options['lug_counts'],
      'studs_spread' => $options['stud_spreads']
    ]);
    View::share('makes', $this->getRimMakes());
    View::share('models', $this->getRimModels());
    View::share('cartQty', $this->cartQty);

  }

  public function rims()
  {

    // THESE HARDCODED VALUES SHOULD BE REPLACED WITH DATA FROM API

    $brands = Rimbrand::paginate();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->when($this->currentWid, function($query) {
        $query->where('d1', '>=', $this->currentWid);
      })->when($this->currentWid2, function($query) {
        $query->where('d1', '<=', $this->currentWid2);
      })->when($this->currentSkr, function($query) {
        $query->where('skr', $this->currentSkr);
      })->when($this->currentPcd, function($query) {
        $query->where('pcd', $this->currentPcd);
      })->when($this->currentDia, function($query) {
        $query->where('d3', $this->currentDia);
      })->where('rims.visible_users', '<>', 0)
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })
      ->orderBy('quantity', 'DESC')
      ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
      ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
      ->orderBy('price3', 'DESC')
      ->paginate();

    return view('rims.autorims', compact('rims','brands'));
  }

  public function api_tires(Request $request) {
    try {

      $html = '';

      $page = ($request->page) ? (int) $request->page : 1; // Get the current page from the request, default to 1
      $perPage = 80; // Number of items per page

      $offset = ($page - 1) * $perPage;

      $selectedRims = explode(',', $request->selected);
      $show_selected = $request->show_selected;

      $this->currentWid = ($request->currentWid == 'Visi') ? '' : $request->currentWid;
      $this->currentWid2 = ($request->currentWid2 == 'Visi') ? '' : $request->currentWid2;
      $this->currentEt = ($request->currentEt == 'Visi') ? '' : $request->currentEt;
      $this->currentEt2 = ($request->currentEt2 == 'Visi') ? '' : $request->currentEt2;
      $this->currentPcd = ($request->currentPcd == 'Visi') ? '' : $request->currentPcd;
      $this->currentSkr = ($request->currentSkr == 'Visi') ? '' : $request->currentSkr;
      $this->currentDia = ($request->currentDia == 'Visi') ? '' : $request->currentDia;
      $this->currentCenter = ($request->currentCenter == 'Visi') ? '' : $request->currentCenter;

      $rims = Rim::select('rims.*')
        ->leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
        ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
        ->when($this->currentWid, function($query) {
          $query->where('rims.d1', '>=', $this->currentWid);
        })->when($this->currentWid2, function($query) {
          $query->where('rims.d1', '<=', $this->currentWid2);
        })->when($this->currentEt, function($query) {
          $query->where('rims.et', '>=', $this->currentEt);
        })->when($this->currentEt2, function($query) {
          $query->where('rims.et', '<=', $this->currentEt2);
        })->when($this->currentPcd, function($query) {
          $query->where('rims.pcd', $this->currentPcd);
        })->when($this->currentSkr, function($query) {
          $query->where('rims.skr', $this->currentSkr);
        })->when($this->currentDia, function($query) {
          $query->where('rims.d3', $this->currentDia);
        })->when($this->currentCenter, function($query) {
          $query->where('rims.dc', $this->currentCenter);
        })->when($show_selected, function ($query) use ($selectedRims) {
          $query->whereIn('rim_id', $selectedRims);
        })->where(function ($query) {
          $query->where('rims.quantity', '>', 0)
            ->orWhere(function ($query) {
              $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
                ->where('rims.quantity', '=', 0);
            });
        })->where('rims.visible_users', '<>', 0)
        ->orderBy('rims.quantity', 'DESC')
        ->orderBy('rims.price3', 'DESC')
        ->groupBy('rims.rim_id');

      $totalItems = $rims->count();
      $totalPages = ceil($totalItems / $perPage);

      $rims = $rims->skip($offset)
        ->take($perPage)
        ->get();

      $fullSize = '';
      $loopIndex = 0;

      if ($request->table_type === 'list') {
        if ($rims->count() > 0) {

          foreach ($rims as $index => $rim) {

            $rim->includeStock = true;
            $rim->fullName = $rim->getFullNameAttribute();
            $rim->getUrl = route('lietais-disks', [\Str::slug($rim->brandTitle), strtolower(str_replace('/', '_', $rim->treadTitle)), $rim->rim_id]);
            $rim->dotAvailable = $rim->getDotAvailableAttribute();
            $rim->stockAvailability = $rim->getStockAvailabilityAttribute();
            $rim->stockCount = $rim->getStockCount();

            if ($index === 0) {
              $html .= '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                      Filtrs
                                    </button>';
            }
            $index++;
            if ($fullSize !== $rim->fullSize) {
              $loopIndex = 0;
              $html .= '<table id="tires-table" class="table table-striped rims-sorter tires-table table-hover tablesorter">';
              $html .= '<thead class="tires-thead sticky-top">
                              <tr>
                                <th scope="col"></th>
                                <th scope="col">Nosaukums</th>
                                <th scope="col" class="text-center">Izmērs</th>
                                <th scope="col" class="hidden-sm-down text-center">Skrūvju attālums</th>
                                <th scope="col" class="hidden-sm-down text-center">ET</th>
                                <th scope="col" class="hidden-sm-down text-center">Centrs</th>
                                <th scope="col" class="hidden-sm-down text-center">Krāsa</th>

                                <th id="store-price-button" scope="col" class="text-center">Veikala cena</th>
                                <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>

                                <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                                <th scope="col"></th>
                                <th scope="col"
                                    data-toggle="tooltip"
                                    data-html="true"
                                    title="<span style=\'color: black\'>Pieejamība</span>">
                                  <span class="tire-table-icon icon-question"></span>
                                </th>

                              </tr>
                              </thead>';
              $html .= '<tbody id="tires-table-body">';
              $html .= '<h4 class="tire-brand-name"><span class="text-uppercase tire-brand-name" style="color:black;">Lietie diski</span> R' . $rim->d3 . ' </h4>';
            }
            $html .= '<tr class="tire-table-row" role="row">';
            $html .= '<th scope="row" class="tire-table-checkbox"><input type="checkbox" value="' . $rim->rim_id . '" name="product_ids[]" class="tire-table-checkbox" title=""></th>';
            $html .= '<td class="table-tire-name-cell"><a class="tire-table-link tippy image" data-tippy-content="<div><img data-src=\'https://r1riepas.lv/storage/rims/tread/' . $rim->make_id . '-o.jpg\'></div>" href="' . $rim->getUrl . '" data-content="' . $rim->fullName . '" data-article="' . $rim->article . '" data-quantity="4"><div class="table-link-title">' . $rim->brandTitle . ' ' . $rim->treadTitle . '</div></a></td>';
            $html .= '<td class="text-center">' . $rim->d1 . '*' . $rim->d3 . '</td>';
            $html .= '<td class="text-center hidden-sm-down">' . $rim->skr . 'x' . $rim->pcd . '</td>';
            $html .= '<td class="text-center hidden-sm-down">' . $rim->et . '</td>';
            $html .= '<td class="text-center hidden-sm-down">' . $rim->dc . '</td>';
            $html .= '<td class="text-center hidden-sm-down">' . strtoupper($rim->color) . '</td>';
            $html .= '<td id="store-price" class="text-center store-price">€ ' . $rim->price1 . '</td>';
            $html .= '<td id="sale-price" class="text-center tire-price-red sale-price">€ ' . $rim->price3 . '</td>';
            if ($rim->comment == 'Izpārdošana!' || $rim->priceoffer == 1) {
              $html .= '<td class="hidden-sm-down text-center sellout">' . $rim->comment . '</td>';
            } else {
              $html .= '<td class="hidden-sm-down text-center">' . $rim->comment . '</td>';
            }
            $html .= '<td class="shopping-cart-col"><div class="clearfix atc_div text-right">';
            if (Auth::check()) {
              if (Auth::user()->hasRole('administrators')) {
                $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#" data-info="' . $rim->rim_id . '"><i class="material-icons">add_shopping_cart</i></button>';
              } else {
                $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#blockcart-modal" data-info="' . $rim->rim_id . '"><i class="material-icons">add_shopping_cart</i></button>';
              }
            } else {
              $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#blockcart-modal" data-info="' . $rim->rim_id . '"><i class="material-icons">add_shopping_cart</i></button>';
            }
            $html .= '</div></td>';
            $html .= '<td class="dot-availability text-center"><span class="tippy lisi-tooltip dot ' . $rim->dotAvailable . '" data-color="' . $rim->dotAvailable . '" data-tippy-content=\'<div style="padding: 5px; text-align: left;"><span style="color: black; font-size: 15px; line-height: 28px;">' . $rim->stockAvailability . '</span></div>\'></span></td>';
            $html .= '</tr>';
            $fullSize = $rim->fullSize;
            if ($fullSize !== $rim->fullSize) {
              $html .= '</tbody>';
              $html .= '</table>';
            }
          }
        }
      } else if ($request->table_type === 'grid') {
        $html .= '<div class="tire-image-container">';
        $cbrand = '';
        $index = 0;
        foreach ($rims as $rim) {

          $rim->getUrl = route('lietais-disks', [\Str::slug($rim->brandTitle), strtolower(str_replace('/', '_', $rim->treadTitle)), $rim->rim_id]);

          $brand = 'R' . $rim->d3;
          $rim->includeStock = true;
          if ($cbrand != $brand){
            if ($index == 0) {
              $html .= '</div><h4 class="tire-brand-name grid-t">';
              $html .= '<span class="tire-type-title" style="margin: 0px;">Lietie diski</span>&nbsp;' . $brand . '<span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                          Filtrs
                                  </button></h4></h4><div class="row grid-ex pr-1 mobile-tire-container">';
            } else {
              $html .= '</div><h4 class="tire-brand-name grid-t">' . $brand;
              $html .= '</h4><div class="row grid-ex pr-1 mobile-tire-container">';
            }
            $cbrand = $brand;
          } else {
            $brand = str_replace(" ", "", $brand);
          }
          $html .= '<a href="' . route('lietais-disks', [\Str::slug($rim->brandTitle), strtolower(str_replace('/', '_', $rim->treadTitle)), $rim->rim_id]) . '"
                               class="grid-view-link"
                               data-article="' . $rim->article . '">';
          $html .= '<div class="tire-image-card sort-order">';
          $html .= '<div class="text-center image-grid-overflow">';
          $html .= Image::showGrid('auto-rim', $rim->make_id);
          $html .= '</div>';

          $html .= '<div class="tire-list-caption">';

          $html .= '<div class="card-title-text"><span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . $rim->brandTitle . ' ' . $rim->treadTitle . '</span></div>">' . $rim->brandTitle . ' ' . $rim->treadTitle . '</span></div>';

          $html .= '<div class="rim-tread">';
          $html .= '<b>' . $rim->d1 . '*' . $rim->d3 . ' (' . $rim->skr . '*' . $rim->pcd . ' ET' . $rim->et . ')</b>';
          $html .= '<span class="tire-image-code">' . $rim->code . '</span>';
          $html .= '</div>';
          $html .= '<div style="display: flex;">';
          $html .= '<input type="checkbox" name="product_ids[]" value="' . $rim->rim_id . '" style="margin-right: 5px;">';
          $html .= '<div class="rim-price-old" style="align-self: center;">€' . $rim->price1 . '</div>';
          $html .= '<div class="rim-price-red" style="align-self: center;">€' . $rim->price2 . '</div>';
          $html .= '<span style="margin-left: auto;" data-toggle="tooltip" title="<span style=\'color: black\'>Pievienot grozam</span>">';
          if (Auth::user()->hasRole('administrators')) {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $rim->rim_id . '" onclick="event.preventDefault()" data-target="#">';
          } else {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $rim->rim_id . '" onclick="event.preventDefault()" data-target="#blockcart-modal">';
          }
          $html .= '<i class="material-icons">add_shopping_cart</i>';
          $html .= '</button>';
          $html .= '</span>';

          $html .= '<span class="tippy lisi-tooltip grid-dot ' . $rim->dotAvailable . $rim->stockCount . '" data-color="' . $rim->dotAvailable . '" data-tippy-content=\'<div style="padding: 5px;"><span style="color: black; font-size: 15px;">' . $rim->stockAvailability . '</span></div>\'></span>';
          $html .= '<span class="sort-order" style="display: none;">' . $rim->dotAvailable . '</span>';
          $html .= '</span>';
          $html .= '</div>';
          $html .= '</div>';
          $html .= '</div>';
          $html .= '</a>';
        }
        $index++;
        $html .= '</div>';
      }

      if ($rims->count() <= 0) {
        $html .= '<div class="container"><div class="col-md-12 mt-1 alert alert-danger">Ar šādiem parametriem nav atrasta neviena pozīcija.</div></div>';
      }

      $html .= $this->generatePagination($page, $totalPages, $offset, $perPage, $totalItems);

      return response()->json($html, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }


  public function rims_search(Request $request){

    DB::enableQueryLog();

    $this->currentWid = ($request->currentWid == 'Visi') ? '' : $request->currentWid;
    $this->currentWid2 = ($request->currentWid2 == 'Visi') ? '' : $request->currentWid2;
    $this->currentEt = ($request->currentEt == 'Visi') ? '' : $request->currentEt;
    $this->currentEt2 = ($request->currentEt2 == 'Visi') ? '' : $request->currentEt2;
    $this->currentPcd = ($request->currentPcd == 'Visi') ? '' : $request->currentPcd;
    $this->currentSkr = ($request->currentSkr == 'Visi') ? '' : $request->currentSkr;
    $this->currentDia = ($request->currentDia == 'Visi') ? '' : $request->currentDia;
    $this->currentCenter = ($request->currentCenter == 'Visi') ? '' : $request->currentCenter;

    $rims = Rim::select('rims.*')
      ->leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->when($this->currentWid, function($query) {
        $query->where('rims.d1', '>=', $this->currentWid);
      })->when($this->currentWid2, function($query) {
        $query->where('rims.d1', '<=', $this->currentWid2);
      })->when($this->currentEt, function($query) {
        $query->where('rims.et', '>=', $this->currentEt);
      })->when($this->currentEt2, function($query) {
        $query->where('rims.et', '<=', $this->currentEt2);
      })->when($this->currentPcd, function($query) {
        $query->where('rims.pcd', $this->currentPcd);
      })->when($this->currentSkr, function($query) {
        $query->where('rims.skr', $this->currentSkr);
      })->when($this->currentDia, function($query) {
        $query->where('rims.d3', $this->currentDia);
      })->when($this->currentCenter, function($query) {
        $query->where('rims.dc', $this->currentCenter);
      })
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })->where('rims.visible_users', '<>', 0)
      ->orderBy('quantity', 'DESC')
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->orderBy('price3', 'DESC')
      ->groupBy('rims.rim_id')->paginate()->appends($request->query());


//      dd(DB::getQueryLog());

//    return view('tires.moto.index',
//      ['tires' => $tires, 'filterCount' => $this->filterCount]
//    );

    return view('rims.autorims', compact('rims'));
  }

  public function rims_tread($brand, $tread, $rim)
  {
    $slug = $brand;

    $brand = Rimbrand::where('title', $brand)->first();
    if (is_null($brand)) {
      $brand = Rimbrand::where('slug', $slug)->first();
    }

    $rims = Rim::selectRaw('rims.*, rim_makes.*, rim_brands.*,
                                                rim_brands.title as brands_title,
                                                rims.comment as comment')
      ->join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->join('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->where('rim_brands.title', $brand->title)
      ->where('rim_makes.title', str_replace('_', '/', $tread))
      ->where(function ($query) {
        $query->where('rims.quantity', '>', 0)
          ->orWhere(function ($query) {
            $query->whereRaw('rims.rim_id IN (SELECT rim_id FROM rim_stock WHERE quantity > 0)')
              ->where('rims.quantity', '=', 0);
          });
      })
      ->orderBy('quantity', 'DESC')
      ->orderBy('price3', 'DESC')
      ->orderBy('d3', 'ASC')
      ->orderBy('d1', 'ASC')
      ->get();

    $currRim = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->where('rim_makes.title', str_replace('_', '/', $tread))
      ->where('rims.rim_id', $rim)
      ->first();

    $currBrand = Rimbrand::where('brand_id', $currRim->brand_id)->first();

    $currRim->includeStock = true;

    return view('rims.auto.tread',
      compact('rims', 'currRim', 'currBrand')
    );
  }

  public function quadr_rims_tread($brand, $tread, $rim)
  {
    $brand = Rimbrand::where('slug', $brand)->first();

    $tread = Rimmake::where('slug', $tread)->first();

    $currRim = Rim::join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->where('rim_makes.title', $tread->title)
      ->where('rims.rim_id', $rim)
      ->first();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
      ->where('rims.make_id', $tread->make_id )
      ->paginate(20);

    return view('rims.quadr.tread', compact('rims', 'currRim', 'brand', 'tread'));
//    $brand = Rimbrand::where('slug', $brand)->first();
//
//    $tread = Rimmake::where('slug', $tread)->first();
//
//    $currRim = Rim::join('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
//      ->where('rim_makes.title', $tread->title)
//      ->where('rims.rim_id', $rim)
//      ->first();
//
//    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
//      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
//      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
//      ->where('rims.make_id', $tread->make_id )
//      ->paginate(20);
//
//    return view('rims.auto.tread', compact('rims', 'currRim', 'brand', 'tread'));
  }

  public function rims_ajax(Request $request)
  {
    $rim = Rim::selectRaw('rims.*, rim_makes.*')
                ->rightJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
                ->where('rims.rim_id', $request->rim_id)
                ->first();

    if ($request->quantity) {
      $cart = CartController::addProduct($this->model, $rim->rim_id, $request->quantity);
    } else {
      $cart = CartController::addProduct($this->model, $rim->rim_id, $this->cartQty);
    }

    $quantity = Cart::count();
    $total_sum = str_replace([',', '.00'], '', Cart::total());
    $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

    echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);

  }

  public function quadr_rims()
  {
    // THESE HARDCODED VALUES SHOULD BE REPLACED WITH DATA FROM API
    $makes = ['Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley', 'BMW', 'Cadillac', 'Chevrolet (also form.Daewoo)', 'Chrysler', 'Citroen', 'Dacia', 'Daewoo', 'Daihatsu', 'Dodge', 'DR', 'Ferrari', 'Fiat', 'Ford', 'Great Wall Motor', 'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu', 'Iveco', 'Jaguar', 'Jeep', 'Kia', 'Lada', 'Lamborghini', 'Lancia', 'Land Rover', 'Lexus', 'Lincoln', 'Martin Motors', 'Maserati', 'Mazda', 'Mercedes Benz', 'MG', 'Mini', 'Mitsubishi', 'Nissan', 'Opel', 'Peugeot', 'Pontiac', 'Porsche', 'Renault', 'Rover', 'Saab', 'Seat', 'Shuanghuan', 'Skoda', 'Smart', 'SsangYong', 'Subaru', 'Suzuki', 'Toyota', 'Volkswagen', 'Volvo'];
    $models = ['Acura', 'Aiways', 'Aixam', 'Alfa Romeo', 'Alpine', 'ARO', 'Aston Martin', 'Audi', 'BAIC', 'Bentley', 'BMW', 'BMW Alpina', 'Borgward', 'Brilliance', 'Bugatti', 'Buick', 'BYD', 'Cadillac', 'Changan', 'Chery', 'Chevrolet', 'Chrysler', 'Citroën', 'Cupra', 'Dacia', 'Daewoo', 'Daihatsu', 'Datsun', 'Dodge', 'Dongfeng', 'DS', 'e.GO', 'Eagle', 'Exeed', 'FAW', 'Ferrari', 'Fiat', 'Fisker', 'Force', 'Ford', 'Foton', 'GAC', 'GAZ', 'Geely', 'Genesis', 'GEO', 'GMC', 'Great Wall (GWM)', 'Haval', 'Hindustan', 'Holden', 'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu', 'Iveco', 'JAC', 'Jaguar', 'Jeep', 'Jetour', 'Jinbei', 'JMC', 'Keyton', 'Kia', 'King Long', 'LADA', 'Lamborghini', 'Lancia', 'Land Rover', 'Landwind', 'LDV', 'LEVC', 'Lexus', 'Lifan', 'Ligier', 'Lincoln', 'Lotus', 'Luxgen', 'Mahindra', 'MAN', 'Maruti', 'Maserati', 'Maxus', 'Maybach', 'Mazda', 'McLaren', 'Mercedes-Benz', 'Mercedes-Maybach', 'Mercury', 'MG', 'Microcar', 'MINI', 'Mitsubishi', 'Mosler', 'Nio', 'Nissan', 'Oldsmobile', 'Opel', 'Ora', 'Panoz', 'Perodua', 'Peugeot', 'Plymouth', 'Polaris', 'Polestar', 'Pontiac', 'Porsche', 'Proton', 'Qiantu', 'Ram', 'Ravon', 'Hongqi', 'Renault', 'Renault Samsung', 'Rivian', 'Roewe', 'Rolls-Royce', 'Rover', 'Saab', 'Saturn', 'Scion', 'Seat', 'Sehol', 'Seres', 'Skoda', 'Smart', 'SsangYong', 'Subaru', 'Sunra', 'Suzuki', 'Tata', 'Tesla', 'Toyota', 'Vauxhall', 'VAZ', 'Venucia', 'VinFast', 'Volkswagen', 'Volvo', 'Weichai', 'Wey', 'Wuling', 'XPeng', 'Zedriv', 'Zeekr', 'Zotye', 'ZX'];
    $diameters = ['10', '12', '13', '14', '15', '16', '16.5', '17', '17.5', '18', '19', '19.5', '20', '21', '22', '23', '24'];
    $lug_count = ['3', '4', '5', '6', '8', '12'];

    $brands = Rimbrand::paginate();

    $rims = Rim::leftJoin('rim_makes', 'rims.make_id', '=', 'rim_makes.make_id')
      ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
      ->select('rims.*', 'rim_makes.*', 'rim_brands.brand_id as brand_id', 'rim_brands.title as brand_title')
      ->orderBy('rim_brands.brand_id', 'ASC')
      ->where('rims.price1', '<>' , 0)
      ->where('rims.price3', '<>' , 0)
      ->where('rims.price2', '<>' , 0)
      ->paginate();
//    return view('rims.autorims', compact('rims','brands'));
    return view('rims.quadrim', compact('rims', 'brands', 'makes', 'models', 'diameters', 'lug_count'));
  }

  public function getRimOptions()
  {
    $options = [
      'widths' => [],
      'offsets' => [],
      'diameters' => [],
      'lug_counts' => [],
      'stud_spreads' => [],
      'rim_center' => []
    ];

    $rimProperties = ['d1' => 'widths', 'et' => 'offsets', 'd3' => 'diameters', 'skr' => 'lug_counts', 'pcd' => 'stud_spreads', 'dc' => 'rim_center'];

    foreach (Rim::all() as $rim) {
      foreach ($rimProperties as $property => $optionKey) {
        $value = $rim->$property;

        if ($optionKey !== 'offsets' && empty($value)) continue;

        if (!in_array($value, $options[$optionKey], true)) {
          $options[$optionKey][] = $value;
        }
      }
    }

    foreach ($options as &$optionValues) {
      asort($optionValues, SORT_NATURAL | SORT_FLAG_CASE);
    }

    return $options;
  }

  public function getRimMakes()
  {
    $rim_makes = [];

    foreach (Rim::all() as $rim) {
      array_push($rim_makes, $rim->offset);
    }

    $rim_makes = array_unique($rim_makes);
    $rim_makes = array_values($rim_makes);

    asort($rim_makes, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_makes;
  }

  public function getRimModels()
  {
    $rim_models = [];

    foreach (Rim::all() as $rim) {
      array_push($rim_models, $rim->offset);
    }

    $rim_models = array_unique($rim_models);
    $rim_models = array_values($rim_models);

    asort($rim_models, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_models;
  }

  public function getBrandList()
  {
    $rim_brands = [];

    foreach (Rimbrand::all() as $rim_brand) {
      array_push($rim_brands, $rim_brand->title);
    }

    $rim_brands = array_unique($rim_brands);
    $rim_brands = array_values($rim_brands);

    asort($rim_brands, SORT_NATURAL | SORT_FLAG_CASE);

    return $rim_brands;
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

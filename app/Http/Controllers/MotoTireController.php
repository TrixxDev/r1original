<?php

namespace App\Http\Controllers;

use App\Helper\Image;
use App\Helper\Tires;
use Illuminate\Http\Request;
use App\Models\Moto;
use App\Models\Motobrand;
use App\Models\Mototread;
use App\Models\Code;
use Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use View;
use Auth;

class MotoTireController extends Controller
{

    public $brands;
    public $currBrand;
    public $d1;
    public $d2;
    public $d3;
    public $motoTiresD1;
    public $motoTiresD2;
    public $motoTiresD3;
    public $model = 'Moto';
    public $type;
    public $availability = [];
    public $code_array = [];
    public $filterCount = 0;

    public $cartQty = 1;

    public function __construct(Request $request)
    {
        $this->brands = $this->tires_getBrands();

        $this->motoTiresD1 = Tires::getMotoTiresD1();
        $this->motoTiresD2 = Tires::getMotoTiresD2();
        $this->motoTiresD3 = Tires::getMotoTiresD3();

        ($request->brand == 'Visi') ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;
        ($this->currBrand === NULL) ? $this->currBrand = 'Visi' : $this->currBrand = $request->brand;

        ($request->d1 == 'Visi') ? $this->d1 = 'Visi' : $this->d1 = $request->d1;
        ($request->d2 == 'Visi') ? $this->d2 = 'Visi' : $this->d2 = $request->d2;
        ($request->d3 == NULL) ? $this->d3 = 17 : $this->d3 = $request->d3;

        ($request->type) ? $this->type = $request->type : $this->type = [];

        if ($request->d1 == NULL && $this->d1 == NULL) {
          $this->d1 = 120;
        }

        if ($request->d2 == NULL && $this->d2 == NULL) {
          $this->d2 = 70;
        }

        if ($request->d3 == NULL && $this->d3 == NULL) {
          $this->d3 = 17;
        }

	      $codes = Code::all();

        foreach ($codes as $code) {
            $this->code_array[$code->name] = $code->explanation;
        }

        View::share('brands', $this->brands);
        View::share('motoTiresD1', $this->motoTiresD1);
        View::share('motoTiresD2', $this->motoTiresD2);
        View::share('motoTiresD3', $this->motoTiresD3);
        View::share('currBrand', $this->currBrand);
        View::share('d1', $this->d1);
        View::share('d2', $this->d2);
        View::share('d3', $this->d3);
        View::share('type', $this->type);
        View::share('types', (new Moto)->types());
	      View::share('code_array', $this->code_array);
        View::share('filterCount', $this->filterCount);
        View::share('availability', $this->availability);
        View::share('cartQty', $this->cartQty);
    }

    public function index()
    {

        return view('tires.moto.index');
    }

    public function tires_tread(Request $request, $brand, $tread, $tire)
    {

        $selectedTires = [];
        if ($request->input('selected')) {
          $selectedTires = explode(',', $request->input('selected'));
        }

        $brand = Motobrand::where('title', $brand)->first();

        $tread = str_replace('_', '/', $tread);
        $tread = Mototread::where('title', $tread)->first();

        $tires = Moto::selectRaw('moto_tires.*, moto_treads.*, moto_brands.*,
                                  moto_brands.title as brands_title, moto_treads.title as treads_title')
                                  ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                                  ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                                  ->where('moto_tires.visible_users', '<>', 0)
                                  ->where('moto_brands.title', $brand->title)
                                  ->where('moto_treads.title',  $tread->title)
                                  ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
                                  ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
                                  ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
                                  ->orderBy('d4', 'ASC')
                                  ->get();

        $currTire = Moto::selectRaw('moto_tires.*, moto_treads.*, moto_brands.*,
                                 moto_brands.title as brands_title, moto_treads.title as treads_title')
                                 ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                                 ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
                                 ->where('moto_tires.visible_users', '<>', 0)
                                 ->where('moto_brands.title', $brand->title)
                                 ->where('moto_treads.title', $tread->title)
                                 ->where('moto_tires.tire_id', $tire)
                                 ->first();

        $currBrand = Motobrand::where('brand_id', $currTire->brand_id)->first();

	//dd($tire);
	//$stock = DB::table('moto_stock')->where('tire_id', $currTire->tire_id)->first();
        $currTire->includeStock = true;

        return view('tires.moto.mototread',
            compact('tires', 'currTire', 'currBrand', 'selectedTires')
        );
    }

  public function api_tires(Request $request) {
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
      $show_selected = $request->show_selected;

      $fastsearch = $request->fastsearch;

      if ($fastsearch) {
        $splited = $this->splitInput($fastsearch);
        $this->d1 = $d1 = $splited['d1'];
        $this->d2 = $d2 = $splited['d2'];
        $this->d3 = $d3 = $splited['d3'];
      }

      $selectedTypes = '';
      if (isset($request->type)) {
        $selectedTypes = explode(' ', $request->type);
      }

      $typeConditions = [
        'custom' => ['moto_tires.type', '=', 'custom'],
        'harleydavidson' => ['moto_tires.type', '=', 'harley davidson'],
        'motocross' => ['moto_tires.type', '=', 'moto cross'],
        'racing' => ['moto_tires.type', '=', 'racing'],
        'scooter' => ['moto_tires.type', '=', 'scooter'],
        'sport' => ['moto_tires.type', '=', 'sport'],
        'sporttouring' => ['moto_tires.type', '=', 'sport touring'],
        'trail' => ['moto_tires.type', '=', 'trail'],
      ];

      $tires = Moto::selectRaw('moto_tires.*, moto_treads.title as t_title, moto_tires.quantity as tire_quantity, moto_treads.*, (SELECT SUM(quantity) FROM moto_stock WHERE moto_stock.tire_id = moto_tires.tire_id) as stock_quantity')
        ->join('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
        ->join('moto_brands', 'moto_treads.brand_id', '=', 'moto_brands.brand_id')
        ->when($currBrand, function ($query) use ($currBrand) {
          $query->where('moto_brands.slug', Str::slug($currBrand));
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
                $query->where('moto_tires.quantity', '>', 0);
                break;
              }
            case 'green+yellow':
              {
                $query->where(function ($query) {
                  $query->where('moto_tires.quantity', '>', 0)
                    ->orWhere(function ($query) {
                      $query->whereRaw('moto_tires.tire_id IN (SELECT tire_id FROM moto_stock WHERE quantity > 0)')
                            ->where('moto_tires.quantity', '=', 0);
                    });
                });
                break;
              }
            case 'green+red':
              {
                $query->where(function ($query) {
                  $query->where('moto_tires.quantity', '>', 0); // Green dot filter
                  $query->orWhere(function ($query) {
                    $query->where('moto_tires.quantity', '=', 0); // Red dot filter
                    $query->whereRaw('moto_tires.tire_id NOT IN (SELECT tire_id FROM moto_stock WHERE quantity > 0)');
                  });
                });
                break;
              }
            case 'yellow':
              {
                $query->where('moto_tires.quantity', '<=', 0)->having('stock_quantity', '>', 0);
                break;
              }
            case 'yellow+red':
              {
                $query->where('moto_tires.quantity', '<=', 0)->having('stock_quantity', '>=', 0);
                break;
              }
            case 'red':
              {
                $query->where('moto_tires.quantity', '<=', 0)->having('stock_quantity', '<=', 0);
                break;
              }
          }
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
        })->when($show_selected, function ($query) use ($selectedTires) {
          $query->whereIn('tire_id', $selectedTires);
        })->where('moto_tires.visible_users', '<>', 0)
        ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
        ->orderBy('d4', 'ASC')
        ->orderBy('price2', 'DESC')
        ->groupBy('moto_tires.article');

      $totalItems = count($tires->get());
      $totalPages = ceil($totalItems / $perPage);

      $tires = $tires->skip($offset)
        ->take($perPage)
        ->get();

      $fullSize = '';
      $loopIndex = 0;

      if ($request->table_type === 'list') {
        if ($tires->count() > 0) {

          foreach ($tires as $index => $tire) {

            $tire->includeStock = true;
            $tire->fullName = $tire->getFullNameAttribute();
            $tire->fullSize = $tire->getFullSizeAttribute();
            $current_url = 'motociklu-riepa';
            $tire->getUrl = route($current_url, [Tires::getMotoTireBrand($tire->brand_id)->title, strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]);
            $tire->fullTitle = $tire->getTitleAttribute();
            $tire->lisiDesc = $tire->lisiDesc($tire->li, $tire->si);
            $tire->codeExplain = $tire->getCodeExplainAttribute();
            $tire->dotAvailable = $tire->getDotAvailableAttribute();
            $tire->stockAvailability = $tire->getStockAvailabilityAttribute();
            $tire->stockCount = $tire->getStockCount();

            if ($index === 0) {
              $html .= '<span class="text-uppercase flipped-title tire-brand-name" style="color: black">Motociklu riepas</span>';
            }
            $index++;
            if ($fullSize !== $tire->fullSize) {
              $loopIndex = 0;
              $html .= '<table id="tires-table" class="table table-striped moto-sorter tires-table table-hover tablesorter">';
              $html .= '<thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">Tips</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          <th scope="col" class="hidden-sm-down text-center">Kods</th>

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
            $html .= '<td class="table-tire-name-cell"><a class="tire-table-link tippy image" data-tippy-content="<div><img data-src=\'https://r1riepas.lv/storage/moto/tread/' . $tire->tread_id . '-o.jpg\'></div>" href="' . $tire->getUrl . '" data-content="' . $tire->fullName . '" data-article="' . $tire->article . '" data-quantity="4"><div class="table-link-title">' . $tire->fullTitle . '</div></a></td>';

            $html .= '<td scope="col" class="hidden-sm-down text-center">';
            $html .= '<span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->typeDesc[1] . '</span></div>">' . $tire->motoType . '</span>';
            $html .= '</td>';

            $html .= '<td class="hidden-sm-down text-center"><span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px; text-align: left;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->lisiDesc . '</span></div>">' . $tire->li . $tire->si . '</span></td>';

            $html .= '<td class="hidden-sm-down text-center"><span class="tippy lisi-tooltip" data-tippy-content="<div style=\'padding: 5px; text-align: left;\'><span style=\'color: black; font-size: 15px;\'>' . $tire->codeExplain . '</span></div>">' . $tire->code . '</span></td>';
            $html .= '<td id="store-price" class="text-center store-price">€ ' . $tire->price1 . '</td>';
            $html .= '<td id="sale-price" class="text-center tire-price-red sale-price">€ ' . $tire->price2 . '</td>';
            if ($tire->comment == 'Izpārdošana!' || $tire->priceoffer == 1) {
              $html .= '<td class="hidden-sm-down text-center sellout">' . $tire->comment . '</td>';
            } else {
              $html .= '<td class="hidden-sm-down text-center">' . $tire->comment . '</td>';
            }
            $html .= '<td class="shopping-cart-col"><div class="clearfix atc_div text-right">';
            if (\Illuminate\Support\Facades\Auth::user()->hasRole('administrators')) {
              $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#" data-info="' . $tire->tire_id . '"><i class="material-icons">add_shopping_cart</i></button>';
            } else {
              $html .= '<button class="cart-shopping-button" data-toggle="modal" data-target="#blockcart-modal" data-info="' . $tire->tire_id . '"><i class="material-icons">add_shopping_cart</i></button>';
            }
            $html .= '</div></td>';
            $html .= '<td class="dot-availability text-center"><span class="tippy lisi-tooltip dot ' . $tire->dotAvailable . '" data-tippy-content=\'<div style="padding: 5px; text-align: left;"><span style="color: black; font-size: 15px; line-height: 28px;">' . $tire->stockAvailability . '</span></div>\'></span></td>';
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
          $current_url = 'motociklu-riepa';
          $tire->getUrl = route($current_url, [Str::slug(Tires::getMotoTireBrand($tire->brand_id)->title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]);

          $brand = $tire->fullSize;
          $tire->includeStock = true;
          if ($cbrand != $brand) {
            $html .= '</div><h4 class="tire-brand-name grid-t" style="margin-left: 5px;">' . $brand;
            if ($index == 0) {
              $html .= ' <span class="tire-type-title">Motociklu riepas</span>';
            }
            $html .= '<span style="margin: 0 auto;"></span>';
            $html .= '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                      Filtrs ()
                                    </button></h4>
                          <div class="row grid-ex pr-1 mobile-tire-container" style="padding-left: 5px;">';
            $cbrand = $brand;
          }
          $html .= '<a href="' . $tire->getUrl . '" class="grid-view-link" data-article="' . $tire->article . '">';
          $html .= '<div class="tire-image-card sort-order">';
          $html .= '<div class="text-center image-grid-overflow">';
          $html .= Image::showGrid('moto', $tire->make_id);
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
          $html .= '<span style="margin-left: auto;" data-toggle="tooltip" title="<span style=\'color: black\'>Pievienot grozam</span>">';
          if (\Illuminate\Support\Facades\Auth::user()->hasRole('administrators')) {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $tire->tire_id . '" onclick="event.preventDefault()" data-target="#">';
          } else {
            $html .= '<button class="grid-buy-btn cart-shopping-button" data-toggle="modal" data-info="' . $tire->tire_id . '" onclick="event.preventDefault()" data-target="#blockcart-modal">';
          }
          $html .= '<i class="material-icons">add_shopping_cart</i>';
          $html .= '</button>';
          $html .= '</span>';

          $html .= '<span class="tippy lisi-tooltip grid-dot ' . $tire->dotAvailable . $tire->stockCount . '" data-tippy-content=\'<div style="padding: 5px;"><span style="color: black; font-size: 15px;">' . $tire->stockAvailability . '</span></div>\'></span>';
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
      return response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 500);
    }
  }

  public function tires_ajax(Request $request) {

        $tire = Moto::query()->with('tread')->selectRaw('moto_tires.*, moto_tires.comment as tire_comment, moto_treads.*')
                      ->rightJoin('moto_treads', 'moto_tires.make_id', '=', 'moto_treads.tread_id')
                      ->where('moto_tires.tire_id', $request->tire_id)
                      ->first();

        if ($request->quantity) {
          $cart = CartController::addProduct($this->model, $tire->tire_id, $request->quantity);
        } else {
          $cart = CartController::addProduct($this->model, $tire->tire_id, $this->cartQty);
        }

        $quantity = Cart::count();
        $total_sum = str_replace([',', '.00'], '', Cart::subTotal());
        $bought = ($request->quantity) ? $request->quantity : $this->cartQty;

        echo json_encode(['cart' => $cart, 'total_sum' => $total_sum, 'quantity' => $quantity, 'bought' => $bought]);
    }

  public function splitInput($input) {
    $input = str_replace(',', '.', $input);

    $europeanPattern = '/^(\d{2,3})(\d{2,3})(\d{2})$/';

    $fractionalPattern = '/^([\d.]+)(\d{2})$/';
    $randomPattern = '/^(\d{1}\.\d{2})(\d{2})$/';
    $randomPattern2 = '/^(\d{1}\.\d{2})(\d{1})$/';

    $americanPattern = '/^([A-Z]+\d{2})(\d{2})$/';

    if (preg_match($europeanPattern, $input, $matches)) {
      $matches = preg_split("/(?<=[05])(?=[1-9])/", $input);
      $d1 = $matches[0];
      if (isset($matches[3])) {
        $d2 = $matches[1] . $matches[2];
        $d3 = $matches[3];
      } else {
        $d2 = $matches[1];
        $d3 = $matches[2];
      }
    } else if (preg_match($randomPattern, $input, $matches) ||
               preg_match($randomPattern2, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = '';
      $d3 = $matches[2];
    } elseif (preg_match($fractionalPattern, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = '';
      $d3 = $matches[2];
    } elseif (preg_match($americanPattern, $input, $matches)) {
      $d1 = $matches[1];
      $d2 = '';
      $d3 = $matches[2];
    } else {
      $d1 = 1;
      $d2 = 1;
      $d3 = 1;
    }

    return compact('d1', 'd2', 'd3');
  }

  public function tires_find(Request $request) {
      if ($request->type) {
        $this->filterCount += 1;
        $this->type = $request->type;
      } else {
        $this->type = '';
      }
      return view('tires.moto.index');
  }

  public function get_sizes()
  {
    try {
      $tireSizes = Moto::select(DB::raw('CONCAT(D1, D2, D3) as tire_size'))
        ->where('moto_tires.visible_users', '<>', 0)
        ->groupBy('moto_tires.article')
        ->orderByRaw('cast(d3 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d1 as decimal(7,2)) ASC')
        ->orderByRaw('cast(d2 as decimal(7,2)) ASC')
        ->distinct()
        ->get();


      return response()->json($tireSizes, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function tires_getBrands()
  {
    $brands = [];

    foreach (Motobrand::all() as $brand) {
      $treads = Mototread::where('brand_id', $brand->brand_id)->get();
      foreach ($treads as $tread) {
        $tire = Moto::where('make_id', $tread->tread_id)->where('visible_users', '<>', 0)->first();
        if (!$tire) continue;
        $brand_id = $tread->brand_id;
        array_push($brands, $brand_id);
      }
    }

    $brands = array_unique($brands);
    $brands = array_values($brands);
    $brand_list = [];
    foreach ($brands as $brand) {
      $brand = Motobrand::where('brand_id', $brand)->first();
      $brand_list[$brand->brand_id] = ucwords(strtolower($brand->title));
    }

    //      asort($brand_list);
    asort($brand_list, SORT_NATURAL | SORT_FLAG_CASE);

    return $brand_list;
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

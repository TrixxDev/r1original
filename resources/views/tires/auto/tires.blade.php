@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-14 category-' . $season_title . ' category-id-parent-12 category-depth-level-3')

@section('content')
  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">
        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          <div id="search_filters_wrapper">
            <form method="get" action="/{{ $season_title }}/search">
              <div id="search_filters" class="params">
                <input type="hidden" id="facet_all_val" value="Visi">
                <div class="wrap">

                  <h4 class="text-uppercase h6 hidden-sm-down">
                    <span id="search_filters_auto" class="params auto">Auto</span>
                    <span id="search_filters_params" class="params active">Parametri</span>
                  </h4>

                  <div class="can-collapse">

                    <span class="show_list active" data-dismiss="modal"><i class="material-icons "></i>Saraksts</span>
                    <span class="show_grid" data-dismiss="modal"><i class="material-icons "></i>Bildes</span>

                    <div class="sidebar-top">
                      <div style="width: 100%">
                        <div class="form-group facet mb-0">
                          <h1 class="h6 facet-title">Ražotājs</h1>
                          <select name="brand" class="r1-select select-title tire-brand">
                            <option class="select-list" id="Visi">Visi</option>
                            @foreach ($brands as $brand_id => $brand_title)
                              <option class="select-list" id="{{ $brand_id }}" @if ($brand_title == $currBrand) selected @endif>{{ ucwords(strtolower($brand_title)) }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="r1-select-params">
                        <div style="width: 100%">
                          <div class="form-group facet">
                            <h1 class="h6 facet-title">Platums</h1>
                            <select class="r1-select select-title tire-width" name="d1">
                              <option class="select-list" id="Visi">Visi</option>
                              @foreach ($autoTiresD1 as $tire)
                                <option id="{{ $tire->d1 }}" @if ($tire->d1 == $d1) selected @endif>{{ $tire->d1 }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div style="width: 100%">
                          <div class="form-group facet">
                            <h1 class="h6 facet-title">Augstums</h1>
                            <select name="d2" class="r1-select select-title tire-width">
                              <option class="select-list" id="Visi">Visi</option>
                              @foreach ($autoTiresD2 as $tire)
                                <option class="select-list" id="{{ $tire->d2 }}" @if ($tire->d2 == $d2) selected @endif>{{ $tire->d2 }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div style="width: 100%">
                          <div class="form-group facet">
                            <h1 class="h6 facet-title facet-select">Diametrs</h1>
                            <select name="d3" class="r1-select select-title tire-width">
                              <option class="select-list" id="Visi">Visi</option>
                              @foreach ($autoTiresD3 as $tire)
                                <option class="select-list" id="{{ $tire->d3 }}" @if ($tire->d3 == $d3) selected @endif>{{ $tire->d3 }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <section class="facet clearfix">
                        <button id="autofind_sub" type="submit">
                          Meklēt <i class="material-icons search"></i>
                        </button>
                      </section>

                    </div>
                  </div>
                </div>
                <div class="wrap hidden-sm-down">
                    <div class="sidebar-bottom">
                      <h3 class="text-uppercase h6">Filtrs</h3>
                      <section class="facet clearfix facet--availability">
                        <h1 class="h6 facet-title">Atlase</h1>
                        <ul class="collapse">
                          <li class="show-selected-checkbox-li">
                            <label class="facet-label" for="show-selected-checkbox"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                            <span class="custom-checkbox">
                              <input type="checkbox" class="tire-table-checkbox" id="show-selected-checkbox"
                                     title="Rādīt tikai atzīmētās preces" disabled>
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <span>Rādīt izvēlētos</span>
                            </label>
                          </li>
                        </ul>
                        <h1 class="h6 facet-title">Pieejamība</h1>
                        <ul id="facet_availability" class="collapse">
                          <li>
                            <label class="facet-label" for="facet_availability_0"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                            <span class="custom-checkbox">
                              <input id="facet_availability_0" class="green" {{-- @if (in_array('green', $availability)) checked @endif --}} type="checkbox" name="availability[]"
                                     data-search-url="#" value="green"
                                     data-for="dot" data-value="green" data-color="green">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              Pieejams
                              <span class="dot green" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                          <li>
                            <label class="facet-label" for="facet_availability_1"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                            <span class="custom-checkbox">
                              <input id="facet_availability_1" class="yellow" {{-- @if (in_array('yellow', $availability)) checked @endif --}} type="checkbox" name="availability[]"
                                     data-search-url="#" value="yellow"
                                     data-for="dot" data-value="yellow" data-color="yellow">
                              <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                            </span>
                              Pasūtāms
                              <span class="dot yellow" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                          <li>
                            <label class="facet-label" for="facet_availability_2"
                                   style="width: 100%;text-align: left;cursor: pointer">
                            <span class="custom-checkbox">
                              <input id="facet_availability_2" class="red" {{-- @if (in_array('red', $availability)) checked @endif --}} type="checkbox" name="availability[]"
                                     data-search-url="#" value="red"
                                     data-for="dot" data-value="red" data-color="red">
                              <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                            </span>
                              Zvaniet!
                              <span class="dot red" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                        </ul>
                      </section>

                      <section class="facet clearfix facet--4">
                        <h1 class="h6 facet-title facet-hover code-dropdown-btn">Kods<span
                            class="material-icons code-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                          <h1 class="h6 facet-title">Kods</h1>
                          <span class="float-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add"></i>
                            <i class="material-icons remove"></i>
                          </span>
                        </span>
                        </div>

                        @php
                          $code = (array) $code;
                        @endphp

                        <ul id="facet_code" class="collapse" style="display: none">
                          <li data-label="XL">
                            <label class="facet-label" for="facet_for_xl">
                            <span class="custom-checkbox">
                              <input id="facet_for_xl" data-search-url="" name="code[]"
                                     @if (in_array('XL', $code)) checked="" @endif value="XL"
                                     data-for="prod-code" data-value="XL" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">XL</a>
                            </label>
                          </li>
                          <li data-label="RSC">
                            <label class="facet-label" for="facet_for_rsc">
                            <span class="custom-checkbox">
                              <input id="facet_for_rsc" data-search-url="" name="code[]"
                                     @if (in_array('RSC', $code)) checked="" @endif value="RSC"
                                     data-for="prod-code" data-value="RSC" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">RSC</a>
                            </label>
                          </li>
                          <li data-label="MFS">
                            <label class="facet-label" for="facet_for_mfs">
                            <span class="custom-checkbox">
                              <input id="facet_for_mfs" data-search-url="" name="code[]"
                                     @if (in_array('MFS', $code)) checked="" @endif value="MFS"
                                     data-for="prod-code" data-value="MFS" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">MFS</a>
                            </label>
                          </li>
                          <li data-label="CURRYEAR">
                            <label class="facet-label" for="facet_for_curryear">
                            <span class="custom-checkbox">
                              <input id="facet_for_curryear" data-search-url="" name="code[]"
                                     @if (in_array('CURRYEAR', $code)) checked="" @endif value="CURRYEAR"
                                     data-for="prod-code" data-value="CURRYEAR"
                                     type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Šī
                                gada</a>
                            </label>
                          </li>
                        </ul>
                      </section>

                      @if ($season_title == 'ziemas-riepas')

                        <section class="facet clearfix facet--4">
                          <h1 class="h6 facet-title facet-hover type-dropdown-btn">Tips<span
                              class="material-icons type-dropdown">keyboard_arrow_down</span></h1>
                          <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                            <h1 class="h6 facet-title">Tips</h1>
                            <span class="float-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add"></i>
                            <i class="material-icons remove"></i>
                          </span>
                        </span>
                          </div>

                          <ul id="facet_code" class="collapse" style="display: none">
                            <li data-label="M+S">
                              <label class="facet-label" for="facet_for_ms">
                            <span class="custom-checkbox">
                              <input id="facet_for_ms" data-search-url="" name="types[]"
                                     @if (in_array(1, $types)) checked="" @endif value="1"
                                     data-for="prod-code" data-value="M+S" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link"
                                   rel="nofollow">M+S</a>
                              </label>
                            </li>
                            <li data-label="Studdable">
                              <label class="facet-label" for="facet_for_studdable">
                            <span class="custom-checkbox">
                              <input id="facet_for_studdable" data-search-url="" name="types[]"
                                     @if (in_array(2, $types)) checked="" @endif value="2"
                                     data-for="prod-code" data-value="Studdable" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Radžojama</a>
                              </label>
                            </li>
                            <li data-label="Studs">
                              <label class="facet-label" for="facet_for_studs">
                            <span class="custom-checkbox">
                              <input id="facet_for_studs" data-search-url="" name="types[]"
                                     @if (in_array(3, $types)) checked="" @endif value="3"
                                     data-for="prod-code" data-value="Studs" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Ar
                                  radzēm</a>
                              </label>
                            </li>
                            <li data-label="Winter">
                              <label class="facet-label" for="facet_for_winter">
                            <span class="custom-checkbox">
                              <input id="facet_for_winter" data-search-url="" name="types[]"
                                     @if (in_array(4, $types)) checked="" @endif value="4"
                                     data-for="prod-code" data-value="Winter" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link"
                                   rel="nofollow">Ziemas</a>
                              </label>
                            </li>
                          </ul>
                        </section>

                      @endif

                      <section class="facet clearfix facet--8">
                        <h1 class="h6 facet-title facet-hover fuel-eco-dropdown-btn">Degvielas ekonomija <span
                            class="material-icons fuel-efficiency-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_70638" data-toggle="collapse">
                          <h1 class="h6 facet-title">Degvielas ekonomija</h1>
                          <span class="float-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add"></i>
                            <i class="material-icons remove"></i>
                          </span>
                        </span>
                        </div>
                        <ul id="facet_fuel_eco" class="collapse" style="display: none;">
                          <li data-label="A">
                            <label class="facet-label" for="facet_fuel_eco_a">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_a" data-search-url="" name="fuel[]"
                                     @if (in_array('A', $fuel)) checked="" @endif value="A"
                                     data-for="fuel_efficiency" data-value="A" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">A</a>
                            </label>
                          </li>
                          <li data-label="B">
                            <label class="facet-label" for="facet_fuel_eco_b">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_b" data-search-url="" name="fuel[]"
                                     @if (in_array('B', $fuel)) checked="" @endif value="B"
                                     data-for="fuel_efficiency" data-value="B" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">B</a>
                            </label>
                          </li>
                          <li data-label="C">
                            <label class="facet-label" for="facet_fuel_eco_c">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_c" data-search-url="" name="fuel[]"
                                     @if (in_array('C', $fuel)) checked="" @endif value="C"
                                     data-for="fuel_efficiency" data-value="C" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">C</a>
                            </label>
                          </li>
                          <li data-label="D">
                            <label class="facet-label" for="facet_fuel_eco_d">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_d" data-search-url="" name="fuel[]"
                                     @if (in_array('D', $fuel)) checked="" @endif value="D"
                                     data-for="fuel_efficiency" data-value="D" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">D</a>
                            </label>
                          </li>
                          <li data-label="E">
                            <label class="facet-label" for="facet_fuel_eco_e">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_e" data-search-url="" name="fuel[]"
                                     @if (in_array('E', $fuel)) checked="" @endif value="E"
                                     data-for="fuel_efficiency" data-value="E" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">E</a>
                            </label>
                          </li>


                          <li data-label="F">
                            <label class="facet-label" for="facet_fuel_eco_f">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_f" data-search-url="" name="fuel[]"
                                     @if (in_array('F', $fuel)) checked="" @endif value="F"
                                     data-for="fuel_efficiency" data-value="F" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">F</a>
                            </label>
                          </li>

                          <li data-label="G">
                            <label class="facet-label" for="facet_fuel_eco_g">
                            <span class="custom-checkbox">
                              <input id="facet_fuel_eco_g" data-search-url="" name="fuel[]"
                                     @if (in_array('G', $fuel)) checked="" @endif value="G"
                                     data-for="fuel_efficiency" data-value="G" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">G</a>
                            </label>
                          </li>
                        </ul>
                      </section>


                      <section class="facet clearfix facet--9">
                        <h1 class="h6 facet-title facet-hover wet-surface-dropdown-btn">Slapjš segums <span
                            class="material-icons wet-surface-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_8079"
                             data-toggle="collapse">
                          <h1 class="h6 facet-title">Slapjš segums</h1>
                          <span class="float-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add"></i>
                            <i class="material-icons remove"></i>
                          </span>
                        </span>
                        </div>


                        <ul id="facet_wet" class="collapse" style="display: none;">
                          <li data-label="A">
                            <label class="facet-label" for="facet_wet_a">
                            <span class="custom-checkbox">
                              <input id="facet_wet_a" data-search-url="" name="wet[]"
                                     @if (in_array('A', $wet)) checked="" @endif value="A"
                                     data-for="wet_grip" data-value="A" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">A</a>
                            </label>
                          </li>
                          <li data-label="B">
                            <label class="facet-label" for="facet_wet_b">
                            <span class="custom-checkbox">
                              <input id="facet_wet_b" data-search-url="" name="wet[]"
                                     @if (in_array('B', $wet)) checked="" @endif value="B"
                                     data-for="wet_grip" data-value="B" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">B</a>
                            </label>
                          </li>
                          <li data-label="C">
                            <label class="facet-label" for="facet_wet_c">
                            <span class="custom-checkbox">
                              <input id="facet_wet_c" data-search-url="" name="wet[]"
                                     @if (in_array('C', $wet)) checked="" @endif value="C"
                                     data-for="wet_grip" data-value="C" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">C</a>
                            </label>
                          </li>
                          <li data-label="D">
                            <label class="facet-label" for="facet_wet_d">
                            <span class="custom-checkbox">
                              <input id="facet_wet_d" data-search-url="" name="wet[]"
                                     @if (in_array('D', $wet)) checked="" @endif value="D"
                                     data-for="wet_grip" data-value="D" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">D</a>
                            </label>
                          </li>
                          <li data-label="E">
                            <label class="facet-label" for="facet_wet_e">
                            <span class="custom-checkbox">
                              <input id="facet_wet_e" data-search-url="" name="wet[]"
                                     @if (in_array('E', $wet)) checked="" @endif value="E"
                                     data-for="wet_grip" data-value="E" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">E</a>
                            </label>
                          </li>
                          <li data-label="F">
                            <label class="facet-label" for="facet_wet_f">
                            <span class="custom-checkbox">
                              <input id="facet_wet_f" data-search-url="" name="wet[]"
                                     @if (in_array('F', $wet)) checked="" @endif value="F"
                                     data-for="wet_grip" data-value="F" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">F</a>
                            </label>
                          </li>
                          <li data-label="G">
                            <label class="facet-label" for="facet_wet_g">
                            <span class="custom-checkbox">
                              <input id="facet_wet_g" data-search-url="" name="wet[]"
                                     @if (in_array('G', $wet)) checked="" @endif value="G"
                                     data-for="wet_grip" data-value="G" type="checkbox">
                              <span class="ps-shown-by-js">
                                <i class="material-icons checkbox-checked"></i>
                              </span>
                            </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">G</a>
                            </label>
                          </li>
                        </ul>
                      </section>
                      <button class="filter-button" type="submit">Filtrēt <i class="material-icons search"></i></button>
                    </div>
                  </div>
              </div>
            </form>
          </div>
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products">
              {{--GRID VIEW--}}
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">
                  {{--                <div style="width: auto;">BRAND NAME</div>--}}
                  @php
                    $cbrand = '';
                    $index = 0;
                  @endphp
                  @foreach($tires as $tire)
                    @php
                      $brand = $tire->fullSize;
                      $tire->includeStock = true;
                      if ($cbrand!=$brand){
                        echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                        if ($index == 0){
                          switch ($season_id){
                          case 1:
                            echo ' <span class="tire-type-title">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo ' <span class="tire-type-title">Ziemas riepas</span>';
                            break;
                          }
                        }
                        echo '<span style="margin: 0 auto;"></span>';
                        echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs (' . $filterCount . ')
                                  </button></h4>
                        <div class="row grid-ex pr-1">';
                        $cbrand = $brand;
                        $stripe = 1;
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                    @endphp
                    @if($tire->price1)
                      <a
                        href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->tread->brand_id)->b_title), strtolower(str_replace('/', '_', $tire->tread->t_title)), $tire->tire_id]) }}"
                        class="grid-view-link"
                        data-article="{{ $tire->article }}">
                        <div class="tire-image-card sort-order">
                          <div class="text-center image-grid-overflow">
                            {!! App\Helper\Image::showGrid('auto', $tire->make_id) !!}
                          </div>

                          <div class="tire-list-caption">

                            <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                              {{$tire->title}}
                            </div>

                            <div class="tire-tread">
                              <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                              <span data-toggle="tooltip"
                                    title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}</span>
                              <span class="tire-image-code">{{$tire->code}}</span>
                            </div>
                            <div style="display: flex;">
                              <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}"
                                     style="margin-right: 5px;">
                              <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                              <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>
                              {{--                            <i class="material-icons" style="margin-left: auto;">add_shopping_cart</i>--}}
                              <span style="margin-left: auto;" data-toggle="tooltip"
                                    title="<span style='color: black'>Pievienot grozam</span>">
{{--                              <button class="grid-buy-btn" data-toggle="modal"--}}
                                {{--                                      @hasrole('administrators') data-target=""--}}
                                {{--                                      @else data-target="#blockcart-modal"--}}
                                {{--                                      @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()">--}}
                                {{--                                <i class="material-icons">add_shopping_cart</i>--}}
                                {{--                              </button>--}}

                              <button class="grid-buy-btn cart-shopping-button"
                                      data-toggle="modal"
                                      data-info="{{ $tire->tire_id }}"
                                      {{--                                      data-info="{{ $currTire->tire_id }}--}}
                                      onclick="event.preventDefault()"
                                        data-target="#">
                                  <i class="material-icons">add_shopping_cart</i>
                                  </button>
                            </span>

                              {{--                            <div class="clearfix atc_div text-right">--}}
                              {{--                              <button class="grid-buy-btn" data-toggle="modal"--}}
                              {{--                                      @hasrole('administrators') data-target=""--}}
                              {{--                                      @else data-target="#blockcart-modal"--}}
                              {{--                                      @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()">--}}
                              {{--                              <i class="material-icons">add_shopping_cart</i>--}}
                              {{--                              </button>--}}
                              {{--                            </div>--}}


                              <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}"
                                    data-toggle="tooltip"
                                    data-html="true"
                                    onclick="event.preventDefault()"
                                    title="{{ $tire->stockAvailability }}">
                              <span class="sort-order" style="display: none;">{{ $tire->dotAvailable }}</span>
                            </span>
                            </div>
                          </div>
                          {{--                        <button class="grid-shopping-button grid-cart-btn" data-toggle="modal" data-target="#blockcart-modal" data-info="148204">Pirkt--}}
                          {{--                        </button>--}}


                        </div>
                      </a>
                    @endif
                    @php
                      $index++;
                    @endphp
                  @endforeach
                </div>
              </div>
              {{-- LIST VIEW--}}
              <div id="">
                <div id="js-product-list">
                  <div class="products row hide-price title-flip">

                    @php
                      $cbrand = '';
                      $index = 0;
                    @endphp
                    @foreach ($tires as $tire)
                      @php
                        $brand = $tire->fullSize;
                        $tire->includeStock = true;
                        if ($cbrand!=$brand){

                        if ($cbrand) {
                           echo '<h4 class="tire-brand-name">' . $cbrand;
                        }
                        if ($index == 0){
                          //<h4 style="display: inline-block;">Izvēlētie filtri: </h4>
                          echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs ('. $filterCount .')
                                  </button><div class="filters" style="margin: 0 auto;"></div>';
                          switch ($season_id){
                          case 1:
                            echo '<span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo '<span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Ziemas riepas</span>';
                            break;
                          }
                          echo '</h4>';
                        }
                        echo '';

                        $cbrand = $brand;
                        $stripe = 1;
                      @endphp
                      <table id="tires-table"
                             class="table table-striped summer-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          @if ($season_id == 2)
                            <th scope="col" class="hidden-sm-down text-center">Tips</th>
                          @endif
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
                        </thead>
                        <tbody id="tires-table-body">
                        @php
                          $cbrand = $brand;
                          $stripe = 1;
                      }
                        @endphp
                        @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
                        <tr class="tire-table-row">
                          <th scope="row" class="tire-table-checkbox">
                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                   class="tire-table-checkbox">
                          </th>

                          <td class="table-tire-name-cell">
                            <a class="tire-table-link tippy"
                               data-tippy-content="<div><img data-src='{{ App\Helper\Image::showAd('auto', $tire->make_id) }}'></div>"
                               href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->tread->brand_id)->b_title), strtolower(str_replace('/', '_', $tire->tread->t_title)), $tire->tire_id]) }}"
                               data-content="{{ $tire->fullName }}"
                               data-article="{{ $tire->article }}"
                               data-quantity="{{ $cartQty }}">
                              <div class="table-link-title">{{ $tire->title }}</div>
                            </a>
                          </td>

                          <td class="hidden-sm-down text-center">
                              <span data-toggle="tooltip"
                                    title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}
                              </span>
                          </td>

                          @if ($season_id == 2)
                            <td scope="col" class="hidden-sm-down text-center">

                              @switch($tire->type)
                                @case(1)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/ms.png')}}" alt="ms"
                                       title="<span>Centrāleiropas tipa ziemas riepa</span>" style="margin:0;">
                                </span>

                                @break

                                @case(2)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzeb.png')}}" alt="radzojama"
                                       title="<span>Radžojama</span>" style="margin:0;">
                                </span>

                                @break

                                @case(3)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzea.png')}}" alt="ar radzem"
                                       title="<span>Ar radzēm</span>" style="margin:0;">
                                </span>

                                @break

                                @case(4)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/parsla.png')}}" alt="skandinavijas"
                                       title="<span>Skandināvijas tipa ziemas riepa</span>" style="margin:0;">
                                </span>
                                @break

                              @endswitch

                            </td>
                          @endif

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip" title="<span style='color: black'>
				                    @php $codes = explode(' ', $tire->code); @endphp
                            @foreach ($codes as $code1)
                            @if (isset($code_array[$code1]))
                            {!! $code_array[$code1] . '<br>' !!}
                            @endif
                            @endforeach
                            @if (strpos($tire->code, 'DOT') !== false)
                            {!! $code_array['DOT'] !!}
                            @endif
                              </span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span>
                          </td>

                          <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                          <td id="sale-price" class="text-center tire-price-red sale-price">€ {{ $tire->price2 }}</td>
                          <td class="hidden-sm-down text-center @if($tire->comment == 'Izpārdošana!' || $tire->priceoffer == 1){{ 'sellout' }}@endif">{{$tire->comment}}</td>

                          <td class="shopping-cart-col">
                            <div class="clearfix atc_div text-right">
                              <button class="cart-shopping-button" data-toggle="modal"
                                @hasrole('administrators') data-target="#" @else data-target="#blockcart-modal" @endhasrole data-info="{{ $tire->tire_id }}"><i
                                class="material-icons">add_shopping_cart</i>
                              </button>
                            </div>
                          </td>

                          <td class="dot-availability text-center">
                            <span class="dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip"
                                  data-html="true"
                                  title="{{ $tire->stockAvailability }}">
                              <span class="sort-order">{{ $tire->dotAvailable }}</span>
                            </span>
                          </td>

                        </tr>
                        @php
                          $index++;
                        @endphp
                        @endforeach
                        </tbody>
                      </table>
                  </div>
                  {{ $tires->links() }}
                </div>
              </div>
            </section>
          </section>
        </div>
      </div>
      <div class="hidden-md-up text-xs-right up">
        <a href="#header" class="back-to-top-button">
          <i class="material-icons"></i>
        </a>
      </div>
      <div class="modal fade" id="mobileFilterModal" tabindex="-1" role="dialog"
           aria-labelledby="mobileFilterModalTitle" aria-hidden="true">
        <div class="modal-dialog mobile-filter-modal" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="search_filters" class="params">
                <input type="hidden" id="facet_all_val" value="Visi">

                <form method="get" action="/{{ $season_title }}/search">
                  <div class="wrap ">
                    <div class="search-filter-loader">
                      <div class="search-filter-loader-spinner">
                        <div class="search-filter-loader-spinner"></div>
                      </div>
                    </div>
                    <h4 class="text-uppercase h6 hidden-sm-down">
                      <span id="search_filters_auto" class="params auto">Auto</span>
                      <span id="search_filters_params" class="params active">Parametri</span>
                    </h4>

                    <div class="can-collapse">

                      <span class="show_list active" data-dismiss="modal"><i class="material-icons "></i>Saraksts</span>
                      <span class="show_grid" data-dismiss="modal"><i class="material-icons "></i>Bildes</span>

                      <div class="sidebar-top">
                        <div style="width: 100%">
                          <div class="form-group facet mb-0">
                            <h1 class="h6 facet-title">Ražotājs</h1>
                            <select name="brand" class="r1-select select-title tire-brand">
                              <option class="select-list" id="Visi">Visi</option>
                              @foreach ($brands as $brand_id => $brand_title)
                                <option class="select-list" id="{{ $brand_id }}" @if ($brand_title == $currBrand) selected @endif>{{ ucwords(strtolower($brand_title)) }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="r1-select-params">
                          <div style="width: 100%">
                            <div class="form-group facet">
                              <h1 class="h6 facet-title">Platums</h1>
                              <select class="r1-select select-title tire-width" name="d1">
                                <option>Visi</option>
                                @foreach ($autoTiresD1 as $tire)
                                  <option id="{{ $tire->d1 }}" @if ($tire->d1 == $d1) selected @endif>{{ $tire->d1 }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div style="width: 100%">
                            <div class="form-group facet">
                              <h1 class="h6 facet-title">Augstums</h1>
                              <select name="d2" class="r1-select select-title tire-width">
                                <option class="select-list" id="Visi">Visi</option>
                                @foreach ($autoTiresD2 as $tire)
                                  <option class="select-list" id="{{ $tire->d2 }}" @if ($tire->d2 == $d2) selected @endif>{{ $tire->d2 }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div style="width: 100%">
                            <div class="form-group facet">
                              <h1 class="h6 facet-title facet-select">Diametrs</h1>
                              <select name="d3" class="r1-select select-title tire-width">
                                <option class="select-list" id="Visi">Visi</option>
                                @foreach ($autoTiresD3 as $tire)
                                  <option class="select-list" id="{{ $tire->d3 }}" @if ($tire->d3 == $d3) selected @endif>{{ $tire->d3 }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>

                        <section class="facet clearfix">
                          <button id="autofind_sub" type="submit">
                            Meklēt <i class="material-icons search"></i>
                          </button>
                        </section>

                      </div>
                    </div>
                  </div>
                  <div class="wrap">
                    <div class="sidebar-bottom">
                      <h3 class="text-uppercase h6 hidden-sm-down">Filtrs</h3>
                      <section class="facet clearfix facet--availability">
                        <h1 class="h6 facet-title hidden-sm-down">Atlase</h1>
                        <ul class="collapse">
                          <li class="show-selected-checkbox-li">
                            <label class="facet-label" for="show-selected-checkbox"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input type="checkbox" class="tire-table-checkbox" id="show-selected-checkbox"
                                   title="Rādīt tikai atzīmētās preces" disabled>
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <span>Rādīt izvēlētos</span>
                            </label>
                          </li>
                        </ul>
                        <h1 class="h6 facet-title hidden-sm-down">Pieejamība</h1>
                        <ul id="facet_availability" class="collapse">
                          <li>
                            <label class="facet-label" for="facet_availability_0"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input id="facet_availability_0" class="green" @if (in_array('green', $availability)) checked @endif type="checkbox" name="availability[]"
                                   data-search-url="#" value="green"
                                   data-for="dot" data-value="green" data-color="green">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              Pieejams
                              <span class="dot green" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                          <li>
                            <label class="facet-label" for="facet_availability_1"
                                   style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input id="facet_availability_1" class="yellow" @if (in_array('yellow', $availability)) checked @endif type="checkbox" name="availability[]"
                                   data-search-url="#" value="yellow"
                                   data-for="dot" data-value="yellow" data-color="yellow">
                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                          </span>
                              Pasūtāms
                              <span class="dot yellow" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                          <li>
                            <label class="facet-label" for="facet_availability_2"
                                   style="width: 100%;text-align: left;cursor: pointer">
                          <span class="custom-checkbox">
                            <input id="facet_availability_2" class="red" @if (in_array('red', $availability)) checked @endif type="checkbox" name="availability[]"
                                   data-search-url="#" value="red"
                                   data-for="dot" data-value="red" data-color="red">
                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                          </span>
                              Zvaniet!
                              <span class="dot red" style="float:right;margin-top: 3px;"></span>
                            </label>
                          </li>
                        </ul>
                      </section>

                      <section class="facet clearfix facet--4">
                        <h1 class="h6 facet-title hidden-sm-down facet-hover code-dropdown-btn">Kods<span
                            class="material-icons code-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                          <h1 class="h6 facet-title">Kods</h1>
                          <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                        </div>

                        @php
                          $code = (array) $code;
                        @endphp

                        <ul id="facet_code" class="collapse" style="display: none">
                          <li data-label="XL">
                            <label class="facet-label" for="facet_for_xl">
                          <span class="custom-checkbox">
                            <input id="facet_for_xl" data-search-url="" name="code[]"
                                   @if (in_array('XL', $code)) checked="" @endif value="XL"
                                   data-for="prod-code" data-value="XL" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">XL</a>
                            </label>
                          </li>
                          <li data-label="RSC">
                            <label class="facet-label" for="facet_for_rsc">
                          <span class="custom-checkbox">
                            <input id="facet_for_rsc" data-search-url="" name="code[]"
                                   @if (in_array('RSC', $code)) checked="" @endif value="RSC"
                                   data-for="prod-code" data-value="RSC" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">RSC</a>
                            </label>
                          </li>
                          <li data-label="MFS">
                            <label class="facet-label" for="facet_for_mfs">
                          <span class="custom-checkbox">
                            <input id="facet_for_mfs" data-search-url="" name="code[]"
                                   @if (in_array('MFS', $code)) checked="" @endif value="MFS"
                                   data-for="prod-code" data-value="MFS" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">MFS</a>
                            </label>
                          </li>
                          <li data-label="CURRYEAR">
                            <label class="facet-label" for="facet_for_curryear">
                          <span class="custom-checkbox">
                            <input id="facet_for_curryear" data-search-url="" name="code[]"
                                   @if (in_array('CURRYEAR', $code)) checked="" @endif value="CURRYEAR"
                                   data-for="prod-code" data-value="CURRYEAR"
                                   type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Šī
                                gada</a>
                            </label>
                          </li>
                        </ul>
                      </section>

                      @if ($season_title == 'ziemas-riepas')

                        <section class="facet clearfix facet--4">
                          <h1 class="h6 facet-title hidden-sm-down facet-hover type-dropdown-btn">Tips<span
                              class="material-icons type-dropdown">keyboard_arrow_down</span></h1>
                          <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                            <h1 class="h6 facet-title">Tips</h1>
                            <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                          </div>

                          <ul id="facet_code" class="collapse" style="display: none">
                            <li data-label="M+S">
                              <label class="facet-label" for="facet_for_ms">
                          <span class="custom-checkbox">
                            <input id="facet_for_ms" data-search-url="" name="types[]"
                                   @if (in_array(1, $types)) checked="" @endif value="1"
                                   data-for="prod-code" data-value="M+S" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link"
                                   rel="nofollow">M+S</a>
                              </label>
                            </li>
                            <li data-label="Studdable">
                              <label class="facet-label" for="facet_for_studdable">
                          <span class="custom-checkbox">
                            <input id="facet_for_studdable" data-search-url="" name="types[]"
                                   @if (in_array(2, $types)) checked="" @endif value="2"
                                   data-for="prod-code" data-value="Studdable" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Radžojama</a>
                              </label>
                            </li>
                            <li data-label="Studs">
                              <label class="facet-label" for="facet_for_studs">
                          <span class="custom-checkbox">
                            <input id="facet_for_studs" data-search-url="" name="types[]"
                                   @if (in_array(3, $types)) checked="" @endif value="3"
                                   data-for="prod-code" data-value="Studs" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Ar
                                  radzēm</a>
                              </label>
                            </li>
                            <li data-label="Winter">
                              <label class="facet-label" for="facet_for_winter">
                          <span class="custom-checkbox">
                            <input id="facet_for_winter" data-search-url="" name="types[]"
                                   @if (in_array(4, $types)) checked="" @endif value="4"
                                   data-for="prod-code" data-value="Winter" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                                <a href="javascript:;" class="_gray-darker search-link js-search-link"
                                   rel="nofollow">Ziemas</a>
                              </label>
                            </li>
                          </ul>
                        </section>

                      @endif

                      <section class="facet clearfix facet--8">
                        <h1 class="h6 facet-title hidden-sm-down facet-hover fuel-eco-dropdown-btn">Degvielas ekonomija <span
                            class="material-icons fuel-efficiency-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_70638" data-toggle="collapse">
                          <h1 class="h6 facet-title">Degvielas ekonomija</h1>
                          <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                        </div>
                        <ul id="facet_fuel_eco" class="collapse" style="display: none;">
                          <li data-label="A">
                            <label class="facet-label" for="facet_fuel_eco_a">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_a" data-search-url="" name="fuel[]"
                                   @if (in_array('A', $fuel)) checked="" @endif value="A"
                                   data-for="fuel_efficiency" data-value="A" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">A</a>
                            </label>
                          </li>
                          <li data-label="B">
                            <label class="facet-label" for="facet_fuel_eco_b">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_b" data-search-url="" name="fuel[]"
                                   @if (in_array('B', $fuel)) checked="" @endif value="B"
                                   data-for="fuel_efficiency" data-value="B" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">B</a>
                            </label>
                          </li>
                          <li data-label="C">
                            <label class="facet-label" for="facet_fuel_eco_c">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_c" data-search-url="" name="fuel[]"
                                   @if (in_array('C', $fuel)) checked="" @endif value="C"
                                   data-for="fuel_efficiency" data-value="C" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">C</a>
                            </label>
                          </li>
                          <li data-label="D">
                            <label class="facet-label" for="facet_fuel_eco_d">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_d" data-search-url="" name="fuel[]"
                                   @if (in_array('D', $fuel)) checked="" @endif value="D"
                                   data-for="fuel_efficiency" data-value="D" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">D</a>
                            </label>
                          </li>
                          <li data-label="E">
                            <label class="facet-label" for="facet_fuel_eco_e">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_e" data-search-url="" name="fuel[]"
                                   @if (in_array('E', $fuel)) checked="" @endif value="E"
                                   data-for="fuel_efficiency" data-value="E" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">E</a>
                            </label>
                          </li>


                          <li data-label="F">
                            <label class="facet-label" for="facet_fuel_eco_f">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_f" data-search-url="" name="fuel[]"
                                   @if (in_array('F', $fuel)) checked="" @endif value="F"
                                   data-for="fuel_efficiency" data-value="F" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">F</a>
                            </label>
                          </li>

                          <li data-label="G">
                            <label class="facet-label" for="facet_fuel_eco_g">
                          <span class="custom-checkbox">
                            <input id="facet_fuel_eco_g" data-search-url="" name="fuel[]"
                                   @if (in_array('G', $fuel)) checked="" @endif value="G"
                                   data-for="fuel_efficiency" data-value="G" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">G</a>
                            </label>
                          </li>
                        </ul>
                      </section>


                      <section class="facet clearfix facet--9">
                        <h1 class="h6 facet-title hidden-sm-down facet-hover wet-surface-dropdown-btn">Slapjš segums <span
                            class="material-icons wet-surface-dropdown">keyboard_arrow_down</span></h1>
                        <div class="title hidden-md-up" data-target="#facet_8079"
                             data-toggle="collapse">
                          <h1 class="h6 facet-title">Slapjš segums</h1>
                          <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                        </div>


                        <ul id="facet_wet" class="collapse" style="display: none;">
                          <li data-label="A">
                            <label class="facet-label" for="facet_wet_a">
                          <span class="custom-checkbox">
                            <input id="facet_wet_a" data-search-url="" name="wet[]"
                                   @if (in_array('A', $wet)) checked="" @endif value="A"
                                   data-for="wet_grip" data-value="A" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">A</a>
                            </label>
                          </li>
                          <li data-label="B">
                            <label class="facet-label" for="facet_wet_b">
                          <span class="custom-checkbox">
                            <input id="facet_wet_b" data-search-url="" name="wet[]"
                                   @if (in_array('B', $wet)) checked="" @endif value="B"
                                   data-for="wet_grip" data-value="B" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">B</a>
                            </label>
                          </li>
                          <li data-label="C">
                            <label class="facet-label" for="facet_wet_c">
                          <span class="custom-checkbox">
                            <input id="facet_wet_c" data-search-url="" name="wet[]"
                                   @if (in_array('C', $wet)) checked="" @endif value="C"
                                   data-for="wet_grip" data-value="C" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">C</a>
                            </label>
                          </li>
                          <li data-label="D">
                            <label class="facet-label" for="facet_wet_d">
                          <span class="custom-checkbox">
                            <input id="facet_wet_d" data-search-url="" name="wet[]"
                                   @if (in_array('D', $wet)) checked="" @endif value="D"
                                   data-for="wet_grip" data-value="D" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">D</a>
                            </label>
                          </li>
                          <li data-label="E">
                            <label class="facet-label" for="facet_wet_e">
                          <span class="custom-checkbox">
                            <input id="facet_wet_e" data-search-url="" name="wet[]"
                                   @if (in_array('E', $wet)) checked="" @endif value="E"
                                   data-for="wet_grip" data-value="E" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">E</a>
                            </label>
                          </li>
                          <li data-label="F">
                            <label class="facet-label" for="facet_wet_f">
                          <span class="custom-checkbox">
                            <input id="facet_wet_f" data-search-url="" name="wet[]"
                                   @if (in_array('F', $wet)) checked="" @endif value="F"
                                   data-for="wet_grip" data-value="F" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">F</a>
                            </label>
                          </li>
                          <li data-label="G">
                            <label class="facet-label" for="facet_wet_g">
                          <span class="custom-checkbox">
                            <input id="facet_wet_g" data-search-url="" name="wet[]"
                                   @if (in_array('G', $wet)) checked="" @endif value="G"
                                   data-for="wet_grip" data-value="G" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>

                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">G</a>
                            </label>
                          </li>
                        </ul>
                      </section>
                      <button class="filter-button" type="submit">Filtrēt <i class="material-icons search"></i></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

@endsection

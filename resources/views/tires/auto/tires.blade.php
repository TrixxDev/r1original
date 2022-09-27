@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-14 category-' . $season_title . ' category-id-parent-12 category-depth-level-3')

@section('content')
  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">

        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          <div id="search_filters_wrapper" class="hidden-sm-down">
            <div id="search_filter_controls" class="hidden-md-up">

              <button class="btn btn-secondary ok">
                <i class="material-icons"></i>
                Labi
              </button>
            </div>
            <div id="search_filters" class="params">
              <input type="hidden" id="facet_all_val" value="Visi">

              <div class="wrap">
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

                  <span class="show_list"><i class="material-icons "></i>Saraksts</span>
                  <span class="show_grid"><i class="material-icons "></i>Bildes</span>

                  <form method="get" action="/{{ $season_title }}/search">
                    <div class="sidebar-top">

                      <section class="facet clearfix facet--0 facet-ind-0">
                        <h1 class="h6 facet-title hidden-sm-down">Ražotājs</h1>
                        <div class="title hidden-md-up" data-target="#facet_20294"
                             data-toggle="collapse">
                          <h1 class="h6 facet-title">Ražotājs</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                                <i class="material-icons add"></i>
                                <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>
                        <ul id="facet_20294" class="collapse">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" readonly class="select-title tire-brand" name="brand"
                                     value="{{ $currBrand }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu">
                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($brands as $brand)
                                  <a rel="nofollow" class="select-list" id="{{ $brand->brand_title }}">
                                    {{ $brand->brand_title }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>
                      </section>


                      <section class="facet clearfix facet--1 facet-ind-1">
                        <h1 class="h6 facet-title hidden-sm-down">Platums</h1>
                        <div class="title hidden-md-up" data-target="#facet_78843"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Platums</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_78843" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" readonly class="select-title tire-width" name="d1" pattern="/^\d+$/" maxlength="3" value="{{ $d1 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu width">

                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($autoTiresD1 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d1 }}">
                                    {{ $tire->d1 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>
                      </section>


                      <section class="facet clearfix facet--2 facet-ind-2">
                        <h1 class="h6 facet-title hidden-sm-down">Augstums</h1>
                        <div class="title hidden-md-up" data-target="#facet_15402"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Augstums</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_15402" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" class="select-title tire-height" readonly maxlength="2" pattern="/^\d+$/" name="d2" value="{{ $d2 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu height">

                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($autoTiresD2 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d2 }}">
                                    {{ $tire->d2 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>
                      </section>


                      <section class="facet clearfix facet--3 facet-ind-3">
                        <h1 class="h6 facet-title hidden-sm-down">Diametrs</h1>
                        <div class="title hidden-md-up" data-target="#facet_24954"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Diametrs</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_24954" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" class="select-title tire-radius" readonly name="d3" maxlength="2" pattern="/^\d+$/" value="{{ $d3 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu radius">
                                @foreach ($autoTiresD3 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d3 }}">
                                    {{ $tire->d3 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>

                      </section>
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
                    <h1 class="h6 facet-title hidden-sm-down">Pieejamība</h1>
                    <ul id="facet_availability" class="collapse">
                      <li>
                        <label class="facet-label" for="facet_availability_0"
                               style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input id="facet_availability_0" class="green" type="checkbox"
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
                            <input id="facet_availability_1" class="yellow" type="checkbox"
                                   data-search-url="#" value="yellow"
                                   data-for="dot" data-value="yellow" data-color="yellow">
                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                          </span>
                          Pasutams
                          <span class="dot yellow" style="float:right;margin-top: 3px;"></span>
                        </label>
                      </li>
                      <li>
                        <label class="facet-label" for="facet_availability_2"
                               style="width: 100%;text-align: left;cursor: pointer">
                          <span class="custom-checkbox">
                            <input id="facet_availability_2" class="red" type="checkbox"
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

                  {{--                                    <section class="facet clearfix facet--27">--}}
                  {{--                                        <h1 class="h6 facet-title hidden-sm-down">TOP40</h1>--}}
                  {{--                                        <div class="title hidden-md-up" data-target="#facet_39112"--}}
                  {{--                                             data-toggle="collapse">--}}
                  {{--                                          <h1 class="h6 facet-title">TOP40</h1>--}}
                  {{--                                          <span class="float-xs-right">--}}
                  {{--                                          <span class="navbar-toggler collapse-icons">--}}
                  {{--                                            <i class="material-icons add"></i>--}}
                  {{--                                            <i class="material-icons remove"></i>--}}
                  {{--                                          </span>--}}
                  {{--                                        </span>--}}
                  {{--                                        </div>--}}


                  {{--                                        <ul id="facet_39112" class="collapse">--}}
                  {{--                                          <li data-label="Top-40">--}}
                  {{--                                            <label class="facet-label" for="facet_input_39112_0">--}}
                  {{--                                              <span class="custom-checkbox">--}}
                  {{--                                                <input id="facet_input_39112_0" data-search-url="" type="checkbox">--}}
                  {{--                                                <span class="ps-shown-by-js">--}}
                  {{--                                                  <i class="material-icons checkbox-checked"></i>--}}
                  {{--                                                </span>--}}
                  {{--                                              </span>--}}
                  {{--                                              <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">--}}
                  {{--                                                Top 40--}}
                  {{--                                              </a>--}}
                  {{--                                            </label>--}}
                  {{--                                          </li>--}}
                  {{--                                        </ul>--}}
                  {{--                                    </section>--}}

                  <section class="facet clearfix facet--4">
                    <h1 class="h6 facet-title hidden-sm-down facet-hover">Kods<span class="material-icons code-dropdown">keyboard_arrow_down</span></h1>
                    <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                      <h1 class="h6 facet-title">Kods</h1>
                      <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                    </div>

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
                      <li data-label="CURRYEAR">
                        <label class="facet-label" for="facet_for_curryear">
                          <span class="custom-checkbox">
                            <input id="facet_for_curryear" data-search-url="" name="code[]"
                                   value="CURRYEAR" data-for="prod-code" data-value="CURRYEAR"
                                   type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                          <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Šī gada</a>
                        </label>
                      </li>
                    </ul>
                  </section>

                  @if ($season_title == 'ziemas-riepas')

                    <section class="facet clearfix facet--4">
                      <h1 class="h6 facet-title hidden-sm-down facet-hover">Tips<span class="material-icons code-dropdown">keyboard_arrow_down</span></h1>
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
                            <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">M+S</a>
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
                            <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Ar radzēm</a>
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
                            <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">Ziemas</a>
                          </label>
                        </li>
                      </ul>
                    </section>

                  @endif

                  <section class="facet clearfix facet--8">
                    <h1 class="h6 facet-title hidden-sm-down facet-hover">Degvielas ekonomija <span class="material-icons fuel-efficiency-dropdown">keyboard_arrow_down</span></h1>
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
                    </ul>
                  </section>


                  <section class="facet clearfix facet--9">
                    <h1 class="h6 facet-title hidden-sm-down facet-hover">Slapjš segums <span class="material-icons wet-surface-dropdown">keyboard_arrow_down</span></h1>
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
                    </ul>
                  </section>
                  <button class="filter-button" type="submit">Filtrēt <i class="material-icons search"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products" class="">
              {{--GRID VIEW--}}
              <input type="checkbox" value="only_selected" class="tire-table-checkbox" id="show-selected-checkbox" name="product_ids[]" title="Rādīt tikai atzīmētās preces">
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
                        echo '</div><h4 class="tire-brand-name">' . $brand;
                        if ($index == 0){
                          switch ($season_id){
                          case 1:
                            echo ' <span class="text-uppercase" style="color:black;">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo ' <span class="text-uppercase" style="color:black;">Ziemas riepas</span>';
                            break;
                          }
                        }
                        echo '</h4><div class="row grid-ex pr-1">';
                        $cbrand = $brand;
                        $stripe = 1;
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                    @endphp
                    @if($tire->price1)
                    <a href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->brand_id)->title), strtolower($tire->t_title), $tire->tire_id]) }}" class="grid-view-link">
                      <div class="tire-image-card sort-order">
                        <div class="text-center image-grid-overflow">
                          {!! \Image::showGrid('auto', $tire->make_id) !!}
                        </div>

                        <div class="tire-list-caption">

                          <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                            {{$tire->title}}
                          </div>

                          <div class="tire-tread">
                            <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                            <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}</span>
                            <span class="tire-image-code">{{$tire->code}}</span>
                          </div>
                          <div style="display: flex;">
                            <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}" style="margin-right: 5px;">
                            <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                            <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>
                            <i class="material-icons" style="margin-left: auto;">add_shopping_cart</i>
{{--                            <button class="" data-toggle="modal"--}}
{{--                                    @hasrole('administrators') data-target="#quick-popup" @else data-target="#blockcart-modal"--}}
{{--                            @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()"><span style="letter-spacing: 2px; text-transform: uppercase;"></span>--}}
{{--                            </button>--}}
                            <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip" style="align-self: center;"
                                  data-html="true"
                                  title="{{ $tire->stockAvailability }}">
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
              {{--LIST VIEW--}}
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

                        echo '<h4 class="tire-brand-name">' . $cbrand;
                        if ($index == 0){
                          switch ($season_id){
                          case 1:
                            echo ' <span class="text-uppercase flipped-title" style="color:black;">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo ' <span class="text-uppercase flipped-title" style="color:black;">Ziemas riepas</span>';
                            break;
                          }
                        }
                        echo '</h4>';
                        $cbrand = $brand;
                        $stripe = 1;
                      @endphp
{{--                    TIRES IMAGES--}}
{{--                      <div class="image-list-item">--}}
{{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
{{--                      </div>--}}
{{--                      <div class="image-list-item">--}}
{{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
{{--                      </div>--}}
{{--                      <div class="image-list-item">--}}
{{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
{{--                      </div>--}}

{{--                    TIRES TABLE--}}
                      <table id="tires-table" class="table summer-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead">
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
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                        @endphp
                        @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
                        {{--                                            <article class="product_show_list cat-14 product-miniature js-product-miniature"--}}
                        {{--                                                     id="{{ str_replace(" ", "", $brand) }}"--}}
                        {{--                                                     data-id-product="{{ $tire->tire_id }}" data-id-product-attribute="{{ $tire->tire_id }}" itemscope=""--}}
                        {{--                                                     itemtype="http://schema.org/Product" data-brand="{{ $tire->brand }}"--}}
                        {{--                                                     data-atv="{{ $tire->d1 }}/{{ $tire->d2 }}R{{ $tire->d3 }}">--}}
                        {{--                                                <div class="thumbnail-container">--}}
                        {{--                                                    <a href="{{ route($current_url, [strtolower(\Tires::getAutoTireBrand($tire->brand_id)->title), $tire->slug, $tire->tire_id]) }}"--}}
                        {{--                                                       class="product-thumbnail">--}}
                        {{--                                                    </a>--}}
                        {{--                                                    <div class="product-description">--}}
                        {{--                                                        <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]">--}}
                        {{--                                                        <h1 class="h3 product-title" itemprop="name">--}}
                        {{--                                                            <a data-toggle="tooltip" data-html="true"--}}
                        {{--                                                                @if ($tire->image)--}}
                        {{--                                                                    title="<img src='{{ $tire->image }}' style='width: 280px; height: 280px;'>"--}}
                        {{--                                                                @else--}}
                        {{--                                                                    title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
                        {{--                                                                @endif--}}
                        {{--                                                                href="{{ route($current_url, [strtolower(\Tires::getAutoTireBrand($tire->brand_id)->title), $tire->slug, $tire->tire_id]) }}"--}}
                        {{--                                                                data-content="{{ $tire->title }}">--}}
                        {{--                                                                <div class="product-title-hidden">{{ $tire->title }}</div>--}}
                        {{--                                                            </a>--}}
                        {{--                                                        </h1>--}}
                        {{--                                                        <span class="tire_article" data-article="{{ $tire->article }}" style="display: none;"></span>--}}
                        {{--                                                        <div class="product-price-and-shipping" data-content="{{ $tire->title }}">--}}
                        {{--                                                        @if ($season === 2)--}}
                        {{--                                                          <span class="table-cell">{{ $tire->type }}</span>--}}
                        {{--                                                        @endif--}}
                        {{--                                                        <span class="hidden-sm-down table-cell">--}}
                        {{--                                                          <span data-toggle="tooltip" title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
                        {{--                                                          <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->si }}</span>">{{ $tire->si }}</span>--}}
                        {{--                                                        </span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->eco }}</span>" class="hidden-sm-down table-cell fuel_efficiency">{{ $tire->eco }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->wet }}</span>" class="hidden-sm-down table-cell wet_grip">{{ $tire->wet }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->noise }}</span>" class="hidden-sm-down table-cell tire_noise">{{ $tire->noise }}</span>--}}
                        {{--                                                        <span class="sr-only">Veikala cena</span>--}}
                        {{--                                                        <span class="regular-price">€ {{ $tire->price1 }}</span>--}}
                        {{--                                                        <span class="sr-only">Akcijas cena</span>--}}
                        {{--                                                        <span itemprop="price" class="price">€ {{ $tire->price2 }}</span>--}}
                        {{--                                                        <span class="table-cell notes">--}}
                        {{--&nbsp;                                                          <span class="table-cell top40">Top 40</span>--}}
                        {{--                                                        </span>--}}
                        {{--                                                        <div class="clearfix atc_div">--}}
                        {{--                                                            <button class="btn grid-cart-btn btn-primary" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}"><i--}}
                        {{--                                                                    class="material-icons">add_shopping_cart</i>--}}
                        {{--                                                            </button>--}}
                        {{--                                                            <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"--}}
                        {{--                                                                  data-html="true"--}}
                        {{--                                                                  title="{{ $tire->stockAvailability }}">--}}
                        {{--                                                                <span class="sort-order">{{ $tire->dotAvailable }}</span>--}}
                        {{--                                                            </span>--}}
                        {{--                                                        </div>--}}
                        {{--                                                    </div>--}}
                        {{--                                                    </div>--}}
                        {{--                                                </div>--}}
                        {{--                                            </article>--}}

                        <tr class="tire-table-row">
                          <th scope="row" class="tire-table-checkbox">
                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                   class="tire-table-checkbox">
                          </th>

                          <td class="table-tire-name-cell">
                            <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                               title='{!! \Image::show('auto', $tire->make_id) !!}'
                               href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->brand_id)->title), strtolower($tire->t_title), $tire->tire_id]) }}"
                               data-content="{{ $tire->title . ' ' . $tire->fullSize }}"
                               data-article="{{ $tire->article }}">
                              {{ $tire->title }}
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
                                  <img src="{{asset('images/ms.png')}}" alt="ms" title="<span>Centrāleiropas tipa ziemas riepa</span>" style="margin:0;">
                                </span>

                                @break

                                @case(2)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzeb.png')}}" alt="radzojama" title="<span>Radžojama</span>" style="margin:0;">
                                </span>

                                @break

                                @case(3)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzea.png')}}" alt="ar radzem" title="<span>Ar radzēm</span>" style="margin:0;">
                                </span>

                                @break

                                @case(4)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/parsla.png')}}" alt="skandinavijas" title="<span>Skandināvijas tipa ziemas riepa</span>" style="margin:0;">
                                </span>
                                @break

                              @endswitch

                            </td>
                          @endif

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  @if($tire->code == 'XL')
                                    title="<span style='color: black'>XL ??????????? SUBJECT TO CHANGE</span>"
                                  @else
                                  title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>"
                                  @endif
                                  class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
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
                          <td class="hidden-sm-down text-center">{{$tire->comment}}</td>

                          <td class="shopping-cart-col">
                            <div class="clearfix atc_div text-right">
                              <button class="cart-shopping-button grid-cart-btn" data-toggle="modal"
                                      @hasrole('administrators') data-target="#quick-popup" @else data-target="#blockcart-modal"
                                      @endhasrole data-info="{{ $tire->tire_id }}"><i
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

                  {{--                                  <div class="table-top product_show_list">--}}
                  {{--                                    <table id="tires-table" class="table tires-table table-striped table-hover tablesorter">--}}
                  {{--                                      <thead class="tires-thead">--}}
                  {{--                                      <tr>--}}
                  {{--                                        <th scope="col"></th>--}}
                  {{--                                        <th scope="col" class="table-tire-name-cell">Brends / modelis</th>--}}
                  {{--                                        <th scope="col" class="hidden-sm-down">LI/SI</th>--}}
                  {{--                                        <th scope="col" class="hidden-sm-down text-center">Kods</th>--}}

                  {{--                                        <th scope="col" class="hidden-sm-down">--}}
                  {{--                                          <div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div>--}}
                  {{--                                        </th>--}}

                  {{--                                        <th scope="col" class="hidden-sm-down">--}}
                  {{--                                          <div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div>--}}
                  {{--                                        </th>--}}

                  {{--                                        <th scope="col" class="hidden-sm-down">--}}
                  {{--                                          <div class="tire-table-icon icon-tire-sound" title="Troksnis"></div>--}}
                  {{--                                        </th>--}}

                  {{--                                        <th id="store-price-button" scope="col" class="text-center"><span class="table-cell sortable"--}}
                  {{--                                                                                                          data-filter=".product-price-and-shipping .regular-price"--}}
                  {{--                                                                                                          data-order="DESC"--}}
                  {{--                                          >Veikala cena</span>--}}
                  {{--                                        </th>--}}

                  {{--                                        <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>--}}
                  {{--                                        <th scope="col" class="hidden-sm-down">Piezīmes</th>--}}
                  {{--                                        <th scope="col"></th>--}}
                  {{--                                        <th scope="col"><div class="tire-table-icon icon-question"></div></th>--}}

                  {{--                                      </tr>--}}
                  {{--                                      </thead>--}}
                  {{--                                      <tbody id="tires-table-body">--}}

                  {{--                                      @foreach ($tires as $tire)--}}
                  {{--                                        <tr class="tire-table-row">--}}
                  {{--                                          <th scope="row" class="tire-table-checkbox">--}}
                  {{--                                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]" class="tire-table-checkbox">--}}
                  {{--                                          </th>--}}

                  {{--                                          <td class="table-tire-name-cell">--}}
                  {{--                                            <a data-toggle="tooltip" data-html="true" class="tire-table-link"--}}
                  {{--                                               @if ($tire->image)--}}
                  {{--                                               title="<img src='{{ $tire->image }}' style='width: 280px; height: 280px;'>"--}}
                  {{--                                               @else--}}
                  {{--                                               title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
                  {{--                                               @endif--}}
                  {{--                                               href="{{ route($current_url, [strtolower(\Tires::getAutoTireBrand($tire->brand_id)->title), $tire->slug, $tire->tire_id]) }}"--}}
                  {{--                                               data-content="{{ $tire->title }}">--}}
                  {{--                                              {{ $tire->title }}--}}
                  {{--                                            </a>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="hidden-sm-down">--}}
                  {{--                                            <span>--}}
                  {{--                                              <span data-toggle="tooltip" title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
                  {{--                                              <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->si }}</span>">{{ $tire->si }}</span>--}}
                  {{--                                            </span>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="hidden-sm-down text-center">--}}
                  {{--                                            <span data-toggle="tooltip" title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="hidden-sm-down text-center">--}}
                  {{--                                            <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="hidden-sm-down text-center">--}}
                  {{--                                            <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="hidden-sm-down text-center">--}}
                  {{--                                            <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>--}}
                  {{--                                          <td id="sale-price" class="text-center tire-price-red">€ {{ $tire->price2 }}</td>--}}
                  {{--                                          <td class="hidden-sm-down"></td>--}}

                  {{--                                          <td>--}}
                  {{--                                            <div class="clearfix atc_div text-right">--}}
                  {{--                                              <button class="cart-shopping-button grid-cart-btn" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}"><i--}}
                  {{--                                                  class="material-icons">add_shopping_cart</i>--}}
                  {{--                                              </button>--}}
                  {{--                                            </div>--}}
                  {{--                                          </td>--}}

                  {{--                                          <td class="text-center">--}}
                  {{--                                            <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"--}}
                  {{--                                                  data-html="true"--}}
                  {{--                                                  title="{{ $tire->stockAvailability }}">--}}
                  {{--                                              <span class="sort-order">{{ $tire->dotAvailable }}</span>--}}
                  {{--                                            </span>--}}
                  {{--                                          </td>--}}

                  {{--                                        </tr>--}}
                  {{--                                      @endforeach--}}
                  {{--                                      </tbody>--}}
                  {{--                                    </table>--}}

                  {{-- small devices back to top button--}}
                  <div class="hidden-md-up text-xs-right up">
                    <a href="#header" class="btn btn-secondary">
                      Atpakaļ uz augšu
                      <i class="material-icons"></i>
                    </a>
                  </div>
                </div>
                {{ $tires->links() }}
              </div>
              <div id="js-product-list-bottom">
                <div id="js-product-list-bottom"></div>
              </div>
            </section>
          </section>
        </div>
      </div>
    </div>
  </div>

  @endsection

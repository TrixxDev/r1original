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

      <span class="show_list active" data-dismiss="modal"><i class="material-icons "></i>Saraksts</span>
      <span class="show_grid" data-dismiss="modal"><i class="material-icons "></i>Bildes</span>

      <form method="get" action="/{{ $season_title }}/search">
        <div class="sidebar-top">

{{--          <section class="facet clearfix facet--0 facet-ind-0">--}}
{{--            <h1 class="h6 facet-title hidden-sm-down">Ražotājs</h1>--}}
{{--            <div class="title hidden-md-up" data-target="#facet_20294"--}}
{{--                 data-toggle="collapse">--}}
{{--              <h1 class="h6 facet-title">Ražotājs</h1>--}}
{{--              <span class="float-xs-right">--}}
{{--                            <span class="navbar-toggler collapse-icons">--}}
{{--                                <i class="material-icons add"></i>--}}
{{--                                <i class="material-icons remove"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--            </div>--}}
{{--            <ul id="facet_20294" class="collapse">--}}
{{--              <li>--}}
{{--                <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">--}}
{{--                  <select name="brand" class="select-title tire-brand">--}}
{{--                    <option class="select-list" id="Visi">Visi</option>--}}
{{--                    @foreach ($brands as $brand_id => $brand_title)--}}
{{--                      <option class="select-list" id="{{ $brand_title->brand_id }}" @if ($brand_title->brand_title == $currBrand) selected @endif>{{ ucwords(strtolower($brand_title->brand_title)) }}</option>--}}
{{--                    @endforeach--}}
{{--                  </select>--}}
{{--                  --}}{{--                              <input type="text" readonly class="select-title tire-brand" name="brand"--}}
{{--                  --}}{{--                                     value="{{ $currBrand }}">--}}
{{--                  --}}{{--                              <i class="material-icons float-xs-right"></i>--}}
{{--                  --}}{{--                              <div class="dropdown-menu">--}}
{{--                  --}}{{--                                <a rel="nofollow" id="Visi" class="select-list">--}}
{{--                  --}}{{--                                  Visi--}}
{{--                  --}}{{--                                </a>--}}
{{--                  --}}{{--                                @foreach ($brands as $brand_id => $brand_title)--}}
{{--                  --}}{{--                                  <a rel="nofollow" class="select-list" id="{{ $brand_title }}">--}}
{{--                  --}}{{--                                    {{ ucwords(strtolower($brand_title)) }}--}}
{{--                  --}}{{--                                  </a>--}}
{{--                  --}}{{--                                @endforeach--}}
{{--                  --}}{{--                              </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--            </ul>--}}
{{--          </section>--}}

          <div style="width: 100%">
            <div class="form-group facet mb-0">
              <h1 class="h6 facet-title">Ražotājs</h1>
              <select name="brand" class="r1-select select-title tire-brand">
                <option class="select-list" id="Visi">Visi</option>
                @foreach ($brands as $brand_id => $brand_title)
                  <option class="select-list" id="{{ $brand_title->brand_id }}" @if ($brand_title->brand_title == $currBrand) selected @endif>{{ ucwords(strtolower($brand_title->brand_title)) }}</option>
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
{{--          <section class="facet clearfix facet--1 facet-ind-1">--}}
{{--            <h1 class="h6 facet-title hidden-sm-down">Platums</h1>--}}
{{--            <div class="title hidden-md-up" data-target="#facet_78843"--}}
{{--                 data-toggle="collapse" aria-expanded="true">--}}
{{--              <h1 class="h6 facet-title">Platums</h1>--}}
{{--              <span class="float-xs-right">--}}
{{--                            <span class="navbar-toggler collapse-icons">--}}
{{--                              <i class="material-icons add"></i>--}}
{{--                              <i class="material-icons remove"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--            </div>--}}


{{--            <ul id="facet_78843" class="collapse in">--}}
{{--              <li>--}}
{{--                <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">--}}
{{--                  <select name="d1" class="select-title tire-width">--}}
{{--                    <option class="select-list" id="Visi">Visi</option>--}}
{{--                    @foreach ($autoTiresD1 as $tire)--}}
{{--                      <option class="select-list" id="{{ $tire->d1 }}" @if ($tire->d1 == $d1) selected @endif>{{ $tire->d1 }}</option>--}}
{{--                    @endforeach--}}
{{--                  </select>--}}
{{--                  <!-- pattern="/^\d+$/" maxlength="3" -->--}}
{{--                                                <input type="text" readonly class="select-title tire-width" name="d1" value="{{ $d1 }}">--}}
{{--                                                <i class="material-icons float-xs-right"></i>--}}
{{--                                                <div class="dropdown-menu width">--}}

{{--                                                  <a rel="nofollow" id="Visi" class="select-list">--}}
{{--                                                    Visi--}}
{{--                                                  </a>--}}
{{--                                                  @foreach ($autoTiresD1 as $tire)--}}
{{--                                                    <a rel="nofollow" class="select-list" id="{{ $tire->d1 }}">--}}
{{--                                                      {{ $tire->d1 }}--}}
{{--                                                    </a>--}}
{{--                                                  @endforeach--}}
{{--                                                </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--            </ul>--}}
{{--          </section>--}}


{{--          <section class="facet clearfix facet--2 facet-ind-2">--}}
{{--            <h1 class="h6 facet-title hidden-sm-down">Augstums</h1>--}}
{{--            <div class="title hidden-md-up" data-target="#facet_15402"--}}
{{--                 data-toggle="collapse" aria-expanded="true">--}}
{{--              <h1 class="h6 facet-title">Augstums</h1>--}}
{{--              <span class="float-xs-right">--}}
{{--                            <span class="navbar-toggler collapse-icons">--}}
{{--                              <i class="material-icons add"></i>--}}
{{--                              <i class="material-icons remove"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--            </div>--}}


{{--            <ul id="facet_15402" class="collapse in">--}}
{{--              <li>--}}
{{--                <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">--}}

{{--                  <select name="d2" class="select-title tire-height">--}}
{{--                    <option class="select-list" id="Visi">Visi</option>--}}
{{--                    @foreach ($autoTiresD2 as $tire)--}}
{{--                      <option class="select-list" id="{{ $tire->d2 }}" @if ($tire->d2 == $d2) selected @endif>{{ $tire->d2 }}</option>--}}
{{--                    @endforeach--}}
{{--                  </select>--}}

{{--                                                <input type="text" class="select-title tire-height" readonly maxlength="2"--}}
{{--                                                       pattern="/^\d+$/" name="d2" value="{{ $d2 }}">--}}
{{--                                                <i class="material-icons float-xs-right"></i>--}}
{{--                                                <div class="dropdown-menu height">--}}

{{--                                                  <a rel="nofollow" id="Visi" class="select-list">--}}
{{--                                                    Visi--}}
{{--                                                  </a>--}}
{{--                                                  @foreach ($autoTiresD2 as $tire)--}}
{{--                                                    <a rel="nofollow" class="select-list" id="{{ $tire->d2 }}">--}}
{{--                                                      {{ $tire->d2 }}--}}
{{--                                                    </a>--}}
{{--                                                  @endforeach--}}
{{--                                                </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--            </ul>--}}
{{--          </section>--}}


{{--          <section class="facet clearfix facet--3 facet-ind-3">--}}
{{--            <h1 class="h6 facet-title hidden-sm-down">Diametrs</h1>--}}
{{--            <div class="title hidden-md-up" data-target="#facet_24954"--}}
{{--                 data-toggle="collapse" aria-expanded="true">--}}
{{--              <h1 class="h6 facet-title">Diametrs</h1>--}}
{{--              <span class="float-xs-right">--}}
{{--                            <span class="navbar-toggler collapse-icons">--}}
{{--                              <i class="material-icons add"></i>--}}
{{--                              <i class="material-icons remove"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--            </div>--}}


{{--            <ul id="facet_24954" class="collapse in">--}}
{{--              <li>--}}
{{--                <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">--}}

{{--                  <select name="d3" class="select-title tire-radius">--}}
{{--                    <option class="select-list" id="Visi">Visi</option>--}}
{{--                    @foreach ($autoTiresD3 as $tire)--}}
{{--                      <option class="select-list" id="{{ $tire->d3 }}" @if ($tire->d3 == $d3) selected @endif>{{ $tire->d3 }}</option>--}}
{{--                    @endforeach--}}
{{--                  </select>--}}

{{--                                                <input type="text" class="select-title tire-radius" readonly name="d3" maxlength="2"--}}
{{--                                                       pattern="/^\d+$/" value="{{ $d3 }}">--}}
{{--                                                <i class="material-icons float-xs-right"></i>--}}
{{--                                                <div class="dropdown-menu radius">--}}
{{--                                                  @foreach ($autoTiresD3 as $tire)--}}
{{--                                                    <a rel="nofollow" class="select-list" id="{{ $tire->d3 }}">--}}
{{--                                                      {{ $tire->d3 }}--}}
{{--                                                    </a>--}}
{{--                                                  @endforeach--}}
{{--                                                </div>--}}
{{--                </div>--}}
{{--              </li>--}}
{{--            </ul>--}}

{{--          </section>--}}
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
              Pasūtāms
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

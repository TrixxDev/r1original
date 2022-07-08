<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>R1</title>
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    <meta name="verify-paysera" content="8edf175c7d27ddd50c1f859814a5812f">
    <meta name="description" content="R1Riepas">
    <meta name="keywords" content="riepas, diski, kondicionieris, montāža, balansēšana, riepu diski">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('img/favicon.ico?1515662352') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.ico?1515662352') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/productcomments.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/jquery.ui.theme.min.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/homeslider.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/custom.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/combinationstab.css?rev=' . time()) }}" type="text/css" media="all">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
    <script src="{{ asset('js/loginToggle.js?rev=' . time()) }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      }
    </script>
    @livewireStyles
</head>

<body id="@yield('body-title')" class="@yield('title')">
<main>
    <header id="header">
        <div class="header-banner">
        </div>

        @include('components.navbar')

        <div class="header-top">
            <div class="container">


                <div class="row">
                    <div class="col-md-3 hidden-sm-down" id="_desktop_logo">
                        <a href="/">

                            <img class="logo img-responsive" src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}"
                                 alt="R1">
                        </a>
                    </div>

                    <div class="col-md-9 col-sm-12 position-static">


                        <div class="top-banner-info">
                            <div class="top-banner-info-skew">

                            </div>

                            <div class="top-banner-info-in">
                                <table border="0">
                                    <thead>
                                    <tr>
                                        <th>Ulbroka</th>
                                        <th>Kalnciema</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><a href="tel:+37167910555"><strong>67910555</strong></a></td>
                                        <td><a href="tel:+37167615615"><strong>67615615</strong></a></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div class="popup modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="popup-1">
                                    <div class="popup-inner">

                                        <div id="map"></div>
                                        <script>
                                            function mapLoaded() {
                                                setTimeout(function () {
                                                    document.dispatchEvent(new Event('mapLoaded'));
                                                }, 200);
                                            }
                                            document.addEventListener('mapLoaded', initMap, false);
                                            var map;
                                            var bound;
                                            function initMap() {
                                                bound = new google.maps.LatLngBounds();
                                                const letlongs = [
                                                    {
                                                        coords: new google.maps.LatLng(56.94440000, 24.28898000),
                                                        text: 'Institūta iela 1, Ulbroka, LV-2130<br> Tālr.: <a href="tel:+37167910555"><strong>+371 67910555</strong></a><br><br> <a style="text-transform: uppercase;" href="https://www.google.com/maps/search/?api=1&query=56.94440000,24.28898000" target="_blank"><strong>Atvert karte</strong></a>',
                                                        icon: '{{ asset('images/kartei_u.png') }}'
                                                    },
                                                    {
                                                        coords: new google.maps.LatLng(56.94318810, 24.06548220),
                                                        text: 'Kalnciema ielā 39, Rīga, LV-1046<br> Tālr.: <a href="tel:+37167615615"><strong>+371 67615615</strong></a><br><br> <a style="text-transform: uppercase;" href="https://www.google.com/maps/search/?api=1&query=56.94318810,24.06548220" target="_blank"><strong>Atvert karte</strong></a>',
                                                        icon: '{{ asset('images/kartei_k.png') }}'
                                                    },
                                                ];
                                                map = new google.maps.Map(document.getElementById('map'), {
                                                    zoom: 8,
                                                    center: letlongs[0].coords
                                                });
                                                letlongs.forEach(function(item) {
                                                    const icon = new google.maps.MarkerImage(
                                                        item.icon,
                                                        new google.maps.Size(25, 34)
                                                    );
                                                    const marker = new google.maps.Marker({
                                                        position: item.coords,
                                                        map,
                                                        icon
                                                    });
                                                    const infowindow = new google.maps.InfoWindow({
                                                        content: item.text
                                                    });
                                                    marker.addListener('click', function() {
                                                        infowindow.open(map, marker);
                                                    });
                                                    bound.extend(item.coords);
                                                });
                                                centerMap();
                                            }
                                            function centerMap() {
                                                //map.setCenter(bound.getCenter());
                                                map.fitBounds(bound);
                                            }
                                        </script>
                                        <script async="" defer="" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIpmH3qj7YD36P0uZkTMvYZtaVt_ksF7g&amp;callback=mapLoaded">
                                        </script>

                                        <a class="popup-close" aria-label="close" data-dismiss="modal" href="#">x</a>
                                    </div>
                                </div>

                                <div class="popup" id="quick-popup" data-popup="popup-2" style="display: none;">
                                    <div class="popup-inner">
                                        <div class="busy_bgr"><div class="busy_img"></div></div>
                                        <form id="quick-buy-form">
                                            <input type="hidden" name="article">
                                            <div class="location-wraper">
                                                <div class="radio-field"><input id="loc_URS" type="radio" name="location" value="URS" checked=""><label for="loc_URS">URS</label></div>
                                                <div class="radio-field"><input id="loc_KRS" type="radio" name="location" value="KRS"><label for="loc_KRS">KRS</label></div>
                                            </div>
                                            <div class="top-long-fields">
                                                <input type="text" placeholder="Prece" name="prod" readonly="">
                                                <label for="qty">Sk.</label>
                                                <input type="number" min="1" placeholder="Daudzums" name="qty" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()" style="width: 70px;">
                                                <label for="price">Cena</label>
                                                <input type="text" placeholder="Cena" name="price" style="width: 80px" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()">

                                            </div>
                                            <div class="bottom-long-fields">
                                                <span>Montāža</span>
                                                <input type="checkbox" id="montage" onchange="toggleMontage()" name="montage" value="1"><label for="montage"></label>
                                                <input type="text" name="total" placeholder="Summa" readonly="">
                                                <label for="total">Summa:</label>
                                                <input type="text" placeholder="Cena" name="price_montage" onkeyup="addMontagePrice()" disabled="">
                                            </div>
                                            <div class="bottom-long-fields">
                                              <span style="margin-left: 54px;">Glabāšana</span>
                                              <input type="checkbox" id="safe" onchange="toggleSafe()" name="safe" value="1"><label for="safe"></label>
                                              <input style="width: 100px;" type="text" placeholder="Cena" name="price_safe" onkeyup="addSafePrice()" disabled="">
                                            </div>
                                            <div class="user-fields">
                                                <input type="text" name="user" placeholder="Lietotājs" value=" ">
                                                <textarea type="textarea" name="comments" placeholder="Komentāri"></textarea>
                                            </div>
                                            <a style="margin-left: 0;" class="button" onclick="return sendData(getFormData($('#quick-buy-form')));">Apstiprināt</a>
                                            <a class="popup-close" data-dismiss="popup" aria-hidden="true" aria-label="Close" href="#"></a>
                                        </form>

                                        <style>
                                            /* POPUPS */
                                            .popup .location-wraper {
                                                float: left;
                                                margin: 0 15px 15px 0;
                                            }

                                            .popup .location-wraper input {
                                                height: 15px;
                                                display: inline-block;
                                                margin-right: 7px;
                                            }

                                            .popup .top-long-fields {
                                                height: 51px !important;
                                                width: 100% !important;
                                            }

                                            .popup .top-long-fields input {
                                                float: left;
                                                margin-left: 10px;
                                            }

                                            .popup .top-long-fields input[name="prod"] {
                                                width: 400px;
                                            }

                                            .popup .top-long-fields input[name="qty"] {
                                                width: 25px;
                                                padding: 0;
                                                padding-left: 5px;
                                            }

                                            .popup .top-long-fields input[name="price"] {
                                                width: 100px;
                                            }

                                            .popup .bottom-long-fields {
                                                height: 50px;
                                                width: 547px;
                                            }

                                            .popup .bottom-long-fields span {
                                                float: left;
                                                margin: 0 10px;
                                                font-size: 20px;
                                                font-weight: bold;
                                            }

                                            .popup .bottom-long-fields input {
                                                float: left;
                                                margin-left: 10px;
                                            }

                                            .popup .bottom-long-fields input[name="total"] {
                                                float: right;
                                                margin-left: 10px;
                                            }

                                            .popup .bottom-long-fields input[name="price_montage"] {
                                                width: 100px;
                                            }

                                            .popup .bottom-long-fields input[name="total"] {
                                                width: 100px;
                                            }

                                            .popup .top-long-fields > label {
                                                font-size: 9pt;
                                                font-weight: bold;
                                                position: absolute;
                                                top: 5px;
                                            }

                                            .popup .top-long-fields > label[for='qty'] {
                                                right: 148px;
                                            }

                                            .popup .top-long-fields > label[for='price'] {
                                                right: 60px;
                                            }

                                            .popup label[for='total'] {
                                                position: relative;
                                                left: 162px;
                                                top: 5px;
                                            }

                                            #quick-buy-msg {
                                                display: none;
                                                font-size: 30px;
                                                margin-top: 30px;
                                            }

                                            .popup .user-fields {
                                                margin-top: 15px;
                                            }

                                            .popup.msg #quick-buy-form{
                                                display: none;
                                            }

                                            .popup.msg a.button{
                                                display: none;
                                            }

                                            .popup.msg #quick-buy-msg{
                                                display: block;
                                            }
                                        </style>
                                        <a class="popup-close" data-dismiss="popup" aria-hidden="true" data-popup-close="popup-2" href="#">x</a>
                                    </div>
                                </div>

                                <div class="top-map">
                                    <button type="button" data-toggle="modal" data-target="#popup-1">
                                        karte </button>
                                </div>
                            </div>


                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>


                <div class="row row-menu">

                    <div class="menu js-top-menu position-static hidden-sm-down"
                         id="_desktop_top_menu">
                        <ul class="top-menu" id="top-menu" data-depth="0">
                            <li class="category" id="category-12">
                                <a class="dropdown-item" href="#" data-depth="0">

                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_26942" data-toggle="collapse"
                                              class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Riepas
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_26942"
                                     style="display: none; top: 130px;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="category" id="category-13">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('ziemas-riepas') }}"
                                               data-depth="1">
                                                Ziemas riepas
                                            </a>
                                        </li>
                                        <li class="category" id="category-14">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('vasaras-riepas') }}"
                                               data-depth="1">
                                                Vasaras riepas
                                            </a>
                                        </li>
                                        <li class="category" id="category-16">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('kvadraciklu-riepas') }}"
                                               data-depth="1">
                                                Kvadraciklu riepas
                                            </a>
                                        </li>
                                        <li class="category" id="category-17">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('motociklu-riepas') }}"
                                               data-depth="1">
                                                Motociklu riepas
                                            </a>
                                        </li>
                                        <li class="category" id="category-18">
                                          <a class="dropdown-item dropdown-submenu"
                                             href="{{ route('lielas-riepas') }}"
                                             data-depth="1">
                                                Lielās riepas
                                          </a>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                            <li class="category" id="category-20">
                                <a class="dropdown-item" href="#" data-depth="0">

                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_6650" data-toggle="collapse" class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Diski
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_6650"
                                     style="display: none; top: 130px;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="category" id="category-21">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('lietie-diski') }}"
                                               data-depth="1">
                                                Jauni lietie diski
                                            </a>
                                        </li>
                                        <li class="category" id="category-22">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('kvadraciklu-diski') }}"
                                               data-depth="1">
                                                Kvadru diski
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                            <li class="category" id="cms-category-3">
                                <a class="dropdown-item" href="#" data-depth="0">

                                    <span class="float-xs-right hidden-md-up">
                                    <span data-target="#top_sub_menu_6381" data-toggle="collapse" class="navbar-toggler collapse-icons">
                                      <i class="material-icons add"></i>
                                      <i class="material-icons remove"></i>
                                    </span>
                                    </span>
                                    Serviss
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_6381"
                                     style="display: none; top: 130px;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="cms-page" id="cms-page-8">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('pakalpojumi') }}"
                                               data-depth="1">
                                                Pakalpojumi
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-9">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('kondicionieris') }}"
                                               data-depth="1">
                                                Kondicionieru uzpilde
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                            <li class="category" id="cms-category-2">
                                <a class="dropdown-item" href="#" data-depth="0">

                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_50733" data-toggle="collapse"
                                              class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Info
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_50733"
                                     style="display: none; top: 130px;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="cms-page" id="cms-page-6">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('contacts') }}"
                                               data-depth="1">
                                                Kontakti
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-11">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('terms') }}"
                                               data-depth="1">
                                                Paskaidrojumi
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-12">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('about') }}"
                                               data-depth="1">
                                                Par i-veikalu
                                            </a>
                                        </li>
{{--                                        <li class="cms-page" id="cms-page-13">--}}
{{--                                            <a class="dropdown-item dropdown-submenu"--}}
{{--                                               href="{{ route('moto_trans') }}"--}}
{{--                                               data-depth="1">--}}
{{--                                                Moto pārvadājumi--}}
{{--                                            </a>--}}
{{--                                        </li>--}}
                                    </ul>

                                </div>
                            </li>
                            <li class="category" id="cms-category-4">
                              <a class="dropdown-item" href="{{ route('pieraksts') }}">

                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_50733" data-toggle="collapse"
                                              class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                E-Pieraksts
                              </a>
                            </li>
                        </ul>

                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </header>

    <aside id="notifications">
        <div class="container">


        </div>
    </aside>

    <section id="wrapper">

        @yield('content')

    </section>


    <section class="wrapper-below">
        <div class="container">
            <div class="row">
                <div class="col-md-6">


                    <div class="footer-top-logo">
                        <img src="{{ asset('images/1.png') }}" alt="logo">
                        <img src="{{ asset('images/2.png') }}" alt="logo">
                    </div>

                </div>
                <div class="col-md-6">


                    <div class="payment-icons">
                        <img src="{{ asset('images/3.png') }}" alt="logo">
                    </div>

                </div>


            </div>
        </div>
    </section>

    <footer id="footer">


        <div class="container">
            <div class="row">


            </div>
        </div>


        <div class="footer-container">
            <div class="container">
                <div class="row">

                    <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_linklist/views/templates/hook/linkblock.tpl -->
                    <div class="col-md-4 links">
                        <div class="row">
                            <div class="col-md-6 wrapper">
                                <h3 class="h3 hidden-sm-down"></h3>
                                <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_58482"
                                     data-toggle="collapse">
                                    <span class="h3"></span>
                                    <span class="float-xs-right">
                                        <span class="navbar-toggler collapse-icons">
                                            <i class="material-icons add"></i>
                                            <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                </div>
                                <ul id="footer_sub_menu_58482" class="collapse">
                                    <li>
                                        <a id="link-cms-page-6-1" class="cms-page-link"
                                           href="{{ route('contacts') }}"
                                           title="">
                                            Kontakti
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-11-1" class="cms-page-link"
                                           href="{{ route('terms') }}"
                                           title="">
                                            Paskaidrojumi
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-7-1" class="cms-page-link"
                                           href="{{ route('pieraksts') }}"
                                           title="">
                                            E-pieraksts
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 wrapper">
                                <h3 class="h3 hidden-sm-down"></h3>
                                <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_8206"
                                     data-toggle="collapse">
                                    <span class="h3"></span>
                                    <span class="float-xs-right">
                                        <span class="navbar-toggler collapse-icons">
                                            <i class="material-icons add"></i>
                                            <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                </div>
                                <ul id="footer_sub_menu_8206" class="collapse">
                                    <li>
                                        <a id="link-cms-page-12-2" class="cms-page-link"
                                           href="{{ route('about') }}"
                                           title="">
                                            Par i-veikalu
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-8-2" class="cms-page-link"
                                           href="{{ route('pakalpojumi') }}"
                                           title="">
                                            Pakalpojumi
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-9-2" class="cms-page-link"
                                           href="{{ route('kondicionieris') }}"
                                           title="">
                                            Kondicionieru uzpilde
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="block-social col-lg-4 col-md-12 col-sm-12">
                        <ul>
                            <li class="facebook">
                                <a href="http://www.facebook.com/pages/R1/342512645789558" target="_blank">
                                    Facebook
                                </a>
                            </li>
                            <li class="twitter">
                                <a href="https://twitter.com/#!/R1riepasundiski" target="_blank">
                                    Twitter
                                </a>
                            </li>
                            <li class="draugiem">
                                <a href="http://www.draugiem.lv/r1" target="_blank">
                                    Draugiem
                                </a>
                            </li>
                        </ul>
                    </div>


                    <div class="col-md-4 address">SIA "R1"<br> Juridiskā adrese:Kalnciema iela 39, Rīga, Latvija,
                        LV-1046<br> Reģistrācijas Nr.: LV 40003479731<br> Banka: DnB NORD<br> Kods: RIKOLV2X<br> Konts:
                        LV62RIKO0002010410513
                    </div>


                </div>
                <div class="row">


                </div>


            </div>
        </div>

    </footer>

</main>

@livewireScripts
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-230419920-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-230419920-1');
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ asset('js/responsiveslides.min.js?rev=' . time()) }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.fancybox.js?rev=' . time()) }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.tablesorter.min.js?rev=' . time()) }}"></script>
@if (\Route::currentRouteName() != 'pieraksts')
  <script type="text/javascript" src="{{ asset('js/jquery.tablesorter.min.js?rev=' . time()) }}"></script>
  <script type="text/javascript" src="{{ asset('js/atc.js?rev=' . time()) }}"></script>
@endif
<script type="text/javascript" src="{{ asset('js/homeslider.js?rev=' . time()) }}"></script>
<script type="text/javascript" src="{{ asset('js/custom.js?rev=' . time()) }}"></script>
<script type="text/javascript" src="{{ asset('js/cart.js?rev=' . time()) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-76Y13VND83"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-76Y13VND83');
</script>
<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title h6 text-sm-center" id="myModalLabel"><i class="material-icons"></i>Produkts veiksmīgi pievienots iepirkumu grozam</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 divide-right">
                        <div class="row">
                            <div class="col-md-6 modal-image-preview">
                                <!-- NOT WORKING like needed | RDP -->
                                {{-- IMAGE INSIDE MODAL--}}
                                  <img style="width: 100%;" src alt="riepas_attēls">
{{--                                @if ($tire->image)--}}
{{--                                  <img src="{{ $tire->image }}">--}}
{{--                                @else--}}
{{--                                  <img src="{{ asset('img/p/en-default-home_default.jpg') }}">--}}
{{--                                @endif--}}
{{--                                <img src="{{ asset('img\p\en-default-medium_default.jpg') }}" style="width: 100%;">--}}
                                <!-- NOT WORKING like needed | RDP -->

                            </div>
                            <div class="col-md-6 modal-product-info">
                                <h6 class="h6 product-name"></h6>
                                <p>€ <span class="product-price"></span></p>

                                <span><strong>Platums</strong>: <span class="product-width"></span></span><br>
                                <span><strong>Augstums</strong>: <span class="product-height"></span></span><br>
                                <span><strong>Diametrs</strong>: <span class="product-radius"></span></span><br>
                                <span><strong>Tips</strong>: <span class="product-type"></span></span><br>
                                <span><strong>LI</strong>: <span class="product-li"></span></span><br>
                                <span><strong>SI</strong>: <span class="product-si"></span></span><br>
                                <p><strong>Daudzums:</strong>&nbsp;<span class="product-qty"></span></p>
                            </div>
                        </div>
                    </div>
{{--                    <div class="cart-dialog-button-container">--}}
{{--                      <div class="cart-dialog-button-item" style="background: red;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias aspernatur fugit magni officia quis rem reprehenderit unde? Aperiam autem culpa, cupiditate debitis esse et inventore, ipsum magnam omnis perspiciatis voluptates.</div>--}}
{{--                      <div class="cart-dialog-button-item" style="background: blue;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aut consectetur cum dolor eaque, eius error eveniet exercitationem in laboriosam mollitia nisi nulla obcaecati quam quisquam repudiandae ut veniam voluptas?</div>--}}
{{--                    </div>--}}
                    <div class="col-md-7">
                        <div class="cart-content">
                            <p class="cart-products-count">Jūsu grozā ir <span class="cart-products-count"></span> produkti</p>
                            <p><strong>Kopā:</strong>&nbsp;€ <span class="cart-products-total"></span> (ar PVN)</p>
                            <div class="cart-dialog-button-container">
                                <button type="button" class="btn-secondary cart-dialog-button-item cart-dialog-button" data-dismiss="modal">Turpināt iepirkties</button>
                                <a href="{{ route('cart') }}" class="btn-primary cart-dialog-button-item cart-dialog-button"><i class="material-icons"></i>Turpināt maksājumu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

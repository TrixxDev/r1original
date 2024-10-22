<!DOCTYPE html>
<html lang="lv">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>R1 Riepu Serviss</title>
    <link rel="SHORTCUT ICON" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    <meta name="verify-paysera" content="8efacf3cf88620d4c363c6eb973712bb">
    <meta name="description" content="R1Riepas">
    <meta name="keywords" content="riepas, diski, kondicionieris, montāža, balansēšana, riepu diski">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('img/favicon.ico?1515662352') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.ico?1515662352') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css?rev=' . time()) }}" media="all">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css?rev=' . time()) }}" media="all">
    <link rel="stylesheet" href="{{ asset('css/jquery.ui.theme.min.css?rev=' . time()) }}" media="all">
    <link rel="stylesheet" href="{{ asset('css/custom.css?rev=' . time()) }}" media="all">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://unpkg.com/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
    <script src="{{ asset('js/loginToggle.js?rev=' . time()) }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/magiczoomplus.css?rev=' . time())}}"/>
    <script src="{{asset('js/magic.js')}}"></script>
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/select2@4.0.13/dist/js/i18n/lv.js"></script>
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
    <script>
      (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", "efaujuuqsx");
    </script>
    @livewireStyles
</head>

<body id="@yield('body-title')" class="@yield('title')" style="background-image: url(@if ((int) env('SEASON') === 1)'/images/cover.webp'@else'/images/cover2.webp'@endif)">
{{--<div class="loading-block"><div class="loading-content"><svg class="machine"xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 645 526">--}}
{{--      <defs/>--}}
{{--      <g>--}}
{{--        <path  x="-173,694" y="-173,694" class="large-shadow" d="M645 194v-21l-29-4c-1-10-3-19-6-28l25-14 -8-19 -28 7c-5-8-10-16-16-24L602 68l-15-15 -23 17c-7-6-15-11-24-16l7-28 -19-8 -14 25c-9-3-18-5-28-6L482 10h-21l-4 29c-10 1-19 3-28 6l-14-25 -19 8 7 28c-8 5-16 10-24 16l-23-17L341 68l17 23c-6 7-11 15-16 24l-28-7 -8 19 25 14c-3 9-5 18-6 28l-29 4v21l29 4c1 10 3 19 6 28l-25 14 8 19 28-7c5 8 10 16 16 24l-17 23 15 15 23-17c7 6 15 11 24 16l-7 28 19 8 14-25c9 3 18 5 28 6l4 29h21l4-29c10-1 19-3 28-6l14 25 19-8 -7-28c8-5 16-10 24-16l23 17 15-15 -17-23c6-7 11-15 16-24l28 7 8-19 -25-14c3-9 5-18 6-28L645 194zM471 294c-61 0-110-49-110-110S411 74 471 74s110 49 110 110S532 294 471 294z"/>--}}
{{--      </g>--}}
{{--      <g>--}}
{{--        <path x="-136,996" y="-136,996" class="medium-shadow" d="M402 400v-21l-28-4c-1-10-4-19-7-28l23-17 -11-18L352 323c-6-8-13-14-20-20l11-26 -18-11 -17 23c-9-4-18-6-28-7l-4-28h-21l-4 28c-10 1-19 4-28 7l-17-23 -18 11 11 26c-8 6-14 13-20 20l-26-11 -11 18 23 17c-4 9-6 18-7 28l-28 4v21l28 4c1 10 4 19 7 28l-23 17 11 18 26-11c6 8 13 14 20 20l-11 26 18 11 17-23c9 4 18 6 28 7l4 28h21l4-28c10-1 19-4 28-7l17 23 18-11 -11-26c8-6 14-13 20-20l26 11 11-18 -23-17c4-9 6-18 7-28L402 400zM265 463c-41 0-74-33-74-74 0-41 33-74 74-74 41 0 74 33 74 74C338 430 305 463 265 463z"/>--}}
{{--      </g>--}}
{{--      <g >--}}
{{--        <path x="-100,136" y="-100,136" class="small-shadow" d="M210 246v-21l-29-4c-2-10-6-18-11-26l18-23 -15-15 -23 18c-8-5-17-9-26-11l-4-29H100l-4 29c-10 2-18 6-26 11l-23-18 -15 15 18 23c-5 8-9 17-11 26L10 225v21l29 4c2 10 6 18 11 26l-18 23 15 15 23-18c8 5 17 9 26 11l4 29h21l4-29c10-2 18-6 26-11l23 18 15-15 -18-23c5-8 9-17 11-26L210 246zM110 272c-20 0-37-17-37-37s17-37 37-37c20 0 37 17 37 37S131 272 110 272z"/>--}}
{{--      </g>--}}
{{--      <g>--}}
{{--        <path x="-100,136" y="-100,136" class="small" d="M200 236v-21l-29-4c-2-10-6-18-11-26l18-23 -15-15 -23 18c-8-5-17-9-26-11l-4-29H90l-4 29c-10 2-18 6-26 11l-23-18 -15 15 18 23c-5 8-9 17-11 26L0 215v21l29 4c2 10 6 18 11 26l-18 23 15 15 23-18c8 5 17 9 26 11l4 29h21l4-29c10-2 18-6 26-11l23 18 15-15 -18-23c5-8 9-17 11-26L200 236zM100 262c-20 0-37-17-37-37s17-37 37-37c20 0 37 17 37 37S121 262 100 262z"/>--}}
{{--      </g>--}}
{{--      <g>--}}
{{--        <path x="-173,694" y="-173,694" class="large" d="M635 184v-21l-29-4c-1-10-3-19-6-28l25-14 -8-19 -28 7c-5-8-10-16-16-24L592 58l-15-15 -23 17c-7-6-15-11-24-16l7-28 -19-8 -14 25c-9-3-18-5-28-6L472 0h-21l-4 29c-10 1-19 3-28 6L405 9l-19 8 7 28c-8 5-16 10-24 16l-23-17L331 58l17 23c-6 7-11 15-16 24l-28-7 -8 19 25 14c-3 9-5 18-6 28l-29 4v21l29 4c1 10 3 19 6 28l-25 14 8 19 28-7c5 8 10 16 16 24l-17 23 15 15 23-17c7 6 15 11 24 16l-7 28 19 8 14-25c9 3 18 5 28 6l4 29h21l4-29c10-1 19-3 28-6l14 25 19-8 -7-28c8-5 16-10 24-16l23 17 15-15 -17-23c6-7 11-15 16-24l28 7 8-19 -25-14c3-9 5-18 6-28L635 184zM461 284c-61 0-110-49-110-110S401 64 461 64s110 49 110 110S522 284 461 284z"/>--}}
{{--      </g>--}}
{{--      <g>--}}
{{--        <path x="-136,996" y="-136,996" class="medium" d="M392 390v-21l-28-4c-1-10-4-19-7-28l23-17 -11-18L342 313c-6-8-13-14-20-20l11-26 -18-11 -17 23c-9-4-18-6-28-7l-4-28h-21l-4 28c-10 1-19 4-28 7l-17-23 -18 11 11 26c-8 6-14 13-20 20l-26-11 -11 18 23 17c-4 9-6 18-7 28l-28 4v21l28 4c1 10 4 19 7 28l-23 17 11 18 26-11c6 8 13 14 20 20l-11 26 18 11 17-23c9 4 18 6 28 7l4 28h21l4-28c10-1 19-4 28-7l17 23 18-11 -11-26c8-6 14-13 20-20l26 11 11-18 -23-17c4-9 6-18 7-28L392 390zM255 453c-41 0-74-33-74-74 0-41 33-74 74-74 41 0 74 33 74 74C328 420 295 453 255 453z"/>--}}
{{--      </g>--}}
{{--    </svg></div></div>--}}
<div id="toasts"></div>
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

                            <img loading="lazy" class="logo img-responsive" src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}" fetchpriority="high" alt="R1">
                        </a>
                    </div>

                    <div class="col-md-9 col-sm-12 position-static">


                        <div class="top-banner-info">
                            <div class="top-banner-info-skew">

                            </div>

                            <div class="top-banner-info-in">
                                <table style="border: none;">
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

                                <!-- Karte Popup -->
                                <div class="modal fade" id="popup-1" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-body">
                                          <div id="map"></div>
                                          <script>
                                            function mapLoaded() {
                                              setTimeout(function () {
                                                document.dispatchEvent(new Event('mapLoaded'));
                                              }, 200);
                                            }
                                            document.addEventListener('mapLoaded', initMap, false);
                                            let map;
                                            let bound;
                                            function initMap() {
                                              bound = new google.maps.LatLngBounds();
                                              const letlongs = [
                                                {
                                                  coords: { lat: 56.94440000, lng: 24.28898000 },
                                                  text: 'Acones iela 2A, Ulbroka, LV-2130<br> Tālr.: <a href="tel:+37167910555"><strong>+371 67910555</strong></a><br><br> <a style="text-transform: uppercase;" href="https://www.google.com/maps/search/?api=1&query=56.94440000,24.28898000" target="_blank"><strong>Atvert karte</strong></a>',
                                                  icon: '{{ asset('images/kartei_u.png') }}'
                                                },
                                                {
                                                  coords: { lat: 56.94318810, lng:24.06548220 },
                                                  text: 'Kalnciema ielā 39, Rīga, LV-1046<br> Tālr.: <a href="tel:+37167615615"><strong>+371 67615615</strong></a><br><br> <a style="text-transform: uppercase;" href="https://www.google.com/maps/search/?api=1&query=56.94318810,24.06548220" target="_blank"><strong>Atvert karte</strong></a>',
                                                  icon: '{{ asset('images/kartei_k.png') }}'
                                                }
                                              ];
                                              if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                                                map = new google.maps.Map(document.getElementById('map'), {
                                                  zoom: 10,
                                                  center: centerMap(),
                                                  gestureHandling: 'greedy',
                                                  panControl: false,
                                                  zoomControl: false,
                                                  mapTypeControl: false,
                                                  scaleControl: false,
                                                  streetViewControl: false,
                                                  overviewMapControl: false,
                                                  rotateControl: false
                                                });
                                              } else {
                                                map = new google.maps.Map(document.getElementById('map'), {
                                                  zoom: 11,
                                                  center: centerMap(),
                                                  gestureHandling: 'greedy',
                                                });
                                              }
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
                                              // Uztaisiju dinamisku servisu centrēšanu
                                              function centerMap() {
                                                let totalLat = 0;
                                                let totalLng = 0;
                                                letlongs.forEach(function(serviss) {
                                                  totalLat += serviss.coords.lat;
                                                  totalLng += serviss.coords.lng;
                                                })
                                                return { lat: totalLat / letlongs.length, lng: totalLng / letlongs.length };
                                              }
                                            }

                                          </script>
                                          <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-s4K1G5lDxiMdB7lLapvxcLCxhQ223oA&callback=mapLoaded"></script>

                                          <a class="popup-close cls-btn" aria-label="close" data-dismiss="modal" href="#" data-target="#popup-1" data-dismiss="modal">x</a>
                                      </div>
                                    </div>
                                  </div>
                                </div>

{{--                                <div class="popup modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="popup-1">--}}
{{--                                    <div class="popup-inner">--}}

{{--                                        </div>--}}
{{--                                </div>--}}

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
                                    <button id="map-modal-toggle" type="button" data-toggle="modal" data-target="#popup-1">
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
                            <li class="category" id="cms-category-100">
                                <a class="dropdown-item" href="{{ route('sale-tires') }}">
                                    Akcijas
                                </a>
                            </li>
                            <li class="category" id="category-12">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">

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
                                        @if ((int) env('SEASON') === 1)
                                        <li class="category" id="category-14">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('vasaras-riepas') }}"
                                               data-depth="1">
                                                Vasaras riepas
                                            </a>
                                        </li>
                                        @else
                                        <li class="category" id="category-13">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('ziemas-riepas') }}"
                                               data-depth="1">
                                                Ziemas riepas
                                            </a>
                                        </li>
                                        @endif
                                        <li class="category" id="category-17">
                                          <a class="dropdown-item dropdown-submenu"
                                             href="{{ route('motociklu-riepas') }}"
                                             data-depth="1">
                                            Motociklu riepas
                                          </a>
                                        </li>
                                        <li class="category" id="category-16">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('kvadraciklu-riepas') }}"
                                               data-depth="1">
                                                Kvadraciklu riepas
                                            </a>
                                        </li>
                                        @if ((int) env('SEASON') === 1)
                                        <li class="category" id="category-13">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('ziemas-riepas') }}"
                                               data-depth="1">
                                                Ziemas riepas
                                            </a>
                                        </li>
                                        @else
                                        <li class="category" id="category-14">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('vasaras-riepas') }}"
                                               data-depth="1">
                                                Vasaras riepas
                                            </a>
                                        </li>
                                        @endif
                                        <li class="category" id="category-18">
                                            <a class="dropdown-item dropdown-submenu"
                                               href="{{ route('lielas-riepas') }}"
                                               data-depth="1">
                                                  Lielās riepas
                                            </a>
                                        </li>
                                        <li class="category" id="category-19">
                                          <a class="dropdown-item dropdown-submenu"
                                             href="{{ route('radzes') }}"
                                             data-depth="1">
                                            Skrūvējamas radzes
                                          </a>
                                        </li>
                                  </ul>

                                </div>
                            </li>
                            <li class="category" id="category-20">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">

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
                                                Lietie diski
                                            </a>
                                        </li>
{{--                                        <li class="category" id="category-22">--}}
{{--                                            <a class="dropdown-item dropdown-submenu"--}}
{{--                                               href="{{ route('kvadru-diski') }}"--}}
{{--                                               data-depth="1">--}}
{{--                                                Kvadru diski--}}
{{--                                            </a>--}}
{{--                                        </li>--}}
                                    </ul>

                                </div>
                            </li>
                            <li class="category" id="cms-category-4">
                              <a class="dropdown-item" href="/pakalpojumi">
                                Izcenojumi
                              </a>
                            </li>
                            <li class="category" id="cms-category-2">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">

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
                                               href="/kontakti"
                                               data-depth="1">
                                                Kontakti un darba laiks
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-9">
                                          <a class="dropdown-item dropdown-submenu"
                                             href="{{ url('kondicionieris') }}"
                                             data-depth="1">
                                            Kondicionieru uzpilde
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
                                               href="{{ url('internet-veikals') }}"
                                               data-depth="1">
                                                Par i-veikalu
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-13">
                                            <a class="dropdown-item dropdown-submenu sizeCalc"
                                               href="{{ url('/kalkulators') }}"
                                               data-depth="1">
                                                Riepu izmēru kalkulators
                                            </a>
                                        </li>
                                        <li class="cms-page" id="cms-page-14">
                                          <a class="dropdown-item dropdown-submenu"
                                             href="{{ url('/riepu-atruma-indeksu-tabula') }}"
                                             data-depth="1">
                                              LI un SI indeksu tabula
                                          </a>
                                        </li>
{{--                                        <li class="cms-page" id="cms-page-15">--}}
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
    <div class="contact-card hidden-md-up">
      <div class="contact-card-items">
{{--        <img src="{{ asset('images/facebook.svg') }}" alt="facebook" style="background-color: white;">--}}
        <a aria-label="Chat on WhatsApp" target="_blank" href="https://wa.me/37128336677"><img src="{{ asset('images/whatsapp.svg') }}" alt="whatsapp" style="background-color: #25d366;"></a>
        <a href="tel:67910555"><img loading="lazy" src="{{ asset('images/phone.svg') }}" alt="phone" style="padding: 10px; background-color: #0d86ff; color: white;"></a>
      </div>
      <div id="toggle-contacts">
        <img loading="lazy" id="tc-phone" src="{{ asset('images/phone.svg') }}" alt="phone" style="padding: 10px; background-color: #0d86ff; color: white;">
        <img loading="lazy" id="tc-close" src="{{ asset('images/close.svg') }}" alt="phone" style="display: none;">
      </div>
    </div>

{{--    <div class="marquee hidden-md-down">--}}


    <footer id="footer">

      <section class="wrapper-below">
        <div class="container">
          <div class="row">
            <div class="col-md-6">


              <div class="footer-top-logo">
                <img loading="lazy" src="{{ asset('images/1.png') }}" alt="logo">
                <img loading="lazy" src="{{ asset('images/2.png') }}" alt="logo">
              </div>

            </div>
            <div class="col-md-6">

            </div>


          </div>
        </div>
      </section>

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
                                           href="/kontakti"
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
                                           href="internet-veikals"
                                           title="">
                                            Par i-veikalu
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-8-2" class="cms-page-link"
                                           href="/pakalpojumi"
                                           title="">
                                            Pakalpojumi
                                        </a>
                                    </li>
                                    <li>
                                        <a id="link-cms-page-9-2" class="cms-page-link"
                                           href="kondicionieris"
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
                        </ul>
			<a href="https://www.salidzini.lv/" target="_blank"><img loading="lazy" style="border: none;" alt="Salidzini.lv logotips" id="salidzini-banner" title="Interneta veikali. Labākā cena" src="https://static.salidzini.lv/images/logo_button.gif"/></a>
                    </div>


                    <div class="col-md-4 address">SIA "R1"<br> Juridiskā adrese:Kalnciema iela 39, Rīga, Latvija,
                        LV-1046<br> Reģistrācijas Nr.: LV 40003479731<br> Banka: Luminor Bank AS Latvijas filiāle<br> Kods: RIKOLV2X<br> Konts:
                        LV91RIKO0001060089254
                    </div>


                </div>
                <div class="row">


                </div>


            </div>
        </div>

    </footer>

    @if (!\Illuminate\Support\Facades\Auth::check())
        @if (App\Helper\Image::countBanners() > 0)
            {!! \App\Helper\Image::showBanners() !!}
        @endif
    @endif

</main>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-76Y13VND83"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-76Y13VND83');
</script>
<script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script src="{{ asset('js/scrollTo.js') }}"></script>
@if (\Route::currentRouteName() != 'pieraksts')
  <script src="{{ asset('js/jquery.tablesorter.min.js?rev=' . time()) }}"></script>
  <script src="{{ asset('js/atc.js?rev=' . time()) }}"></script>
@endif
<script src="https://unpkg.com/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tippy.js@4.3.5/umd/index.all.min.js"></script>
<script src="{{ asset('js/custom.js?rev=' . time()) }}"></script>
<script src="{{ asset('js/banner_slider.min.js?rev=' . time()) }}"></script>
<script src="{{ asset('js/cart.js?rev=' . time()) }}"></script>
{{--<script src="https://unpkg.com/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>--}}
<script src="https://unpkg.com/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title h6 text-sm-center" id="myModalLabel"><i class="material-icons"></i>Produkts veiksmīgi pievienots iepirkumu grozam</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 divide-right">
                        <div class="row">
                            <div class="col-md-6 modal-image-preview">
                                <!-- NOT WORKING like needed | RDP -->
                                {{-- IMAGE INSIDE MODAL--}}
                                  <img loading="lazy" style="width: 100%;" alt="riepas_attēls">
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

                                @if (strpos(\Request::route()->getName(), 'radze') !== false)
                                  <span><strong>Radzes garums</strong>: <span class="product-stud-length"></span></span><br>
                                  <span><strong>Daudzums</strong>: <span class="product-stud-count"></span></span><br>
                                  <span><strong>Piezīmes</strong>: <span class="product-comment"></span></span><br>
                                @else
                                  <span><strong>Platums</strong>: <span class="product-width"></span></span><br>
                                  <span><strong>Augstums</strong>: <span class="product-height"></span></span><br>
                                  <span><strong>Diametrs</strong>: <span class="product-radius"></span></span><br>
                                  <span><strong>Tips</strong>: <span class="product-type"></span></span><br>
                                  <span><strong>LI</strong>: <span class="product-li"></span></span><br>
                                  <span><strong>SI</strong>: <span class="product-si"></span></span><br>
                                  <p><strong>Daudzums:</strong>&nbsp;<span class="product-qty"></span></p>
                                @endif
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
                                <button type="button" class="btn-secondary cart-dialog-button-item cart-dialog-button" data-dismiss="modal">Turpināt preču meklēšanu</button>
                                <a href="{{ route('cart') }}" class="btn-primary cart-dialog-button-item cart-dialog-button"><i class="material-icons"></i>Pārlūkot pirkumu grozu</a>
                            </div>
                            <div class="order-info alert" role="alert">
                              <p style="font-weight: bold;">Pasūtīt un iegādāties preci iespējams sekošos veidos:</p>
                              <ul id="order-type-info">
                                <li class="list-style">Noformēt pasūtījumu, apmaksāt to kādā no mūsu servisiem un saņemt preci</li>
                                <br>
                                <li class="list-style">Noformēt pasūtījumu, veikt apmaksu ar bankas pārskaitījumu. Pēc apmaksas preci saņemt kādā no mūsu servisiem vai ar kurjera piegādi.</li>
                                <br>
                                <li class="list-style">Noformēt pasūtījumu, veikt tiešsaistes maksājumu noformējot pasūtījumu. Pēc apmaksas preci varēs saņemt kādā no mūsu servisiem vai ar kurjera piegādi.</li>
                              </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="recaptcha_k" data-value="{{ env('RECAPTCHAV3_SITEKEY') }}" style="display: none;"></div>
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHAV3_SITEKEY') }}&hl=lv"></script>

<script>
  grecaptcha.ready(function() {
    grecaptcha.execute($('#recaptcha_k').data('value'), {action: 'application_form'}).then(function(token) {
      $('#reservation input[name=grecaptcha]').val(token);
      $('#reservation input[name=grecaptcha_app]').val('application_form');
    });
  });
</script>
<!-- Meta Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '847896900037592');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height="1" width="1" loading="lazy" style="display:none" src="https://www.facebook.com/tr?id=847896900037592&ev=PageView&noscript=1"/>
</noscript>
<!-- End Meta Pixel Code -->
<script>
  var loggedIn = {{ auth()->check() ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/toast.js') }}"></script>
</body>
</html>

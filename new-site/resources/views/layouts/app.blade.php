<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $metaTitle = trim($__env->yieldContent('meta_title', 'R1 Riepu Serviss | Riepas un diski'));
        $metaDescription = trim($__env->yieldContent('meta_description', 'R1 Riepu Serviss — riepas, diski, montāža un balansēšana. Online katalogs un e-pieraksts Rīgā un Ulbrokā.'));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:site_name" content="R1 Riepu Serviss">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;0,700;1,400;1,700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    {{-- Дизайн старого сайта: переносится как есть, не редактировать руками --}}
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body id="@yield('body-title')" class="@yield('title')"
      style="background-image: url(@if (config('site.season') === 1)'/images/cover.webp'@else'/images/cover3.webp'@endif)">
<div id="toasts"></div>
<main>
    <header id="header">
        <div class="header-banner"></div>

        @include('components.navbar')

        <div class="header-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 hidden-sm-down" id="_desktop_logo">
                        <a href="/">
                            <img class="logo img-responsive" src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}" fetchpriority="high" alt="R1 Riepu Serviss">
                        </a>
                    </div>

                    <div class="col-md-9 col-sm-12 position-static">
                        <div class="top-banner-info">
                            <div class="top-banner-info-skew"></div>

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
                                        <td><a href="tel:+371{{ config('site.phones.ulbroka') }}"><strong>{{ config('site.phones.ulbroka') }}</strong></a></td>
                                        <td><a href="tel:+371{{ config('site.phones.kalnciema') }}"><strong>{{ config('site.phones.kalnciema') }}</strong></a></td>
                                    </tr>
                                    </tbody>
                                </table>

                                {{-- Karte Popup --}}
                                <div class="modal fade" id="popup-1" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div id="map" style="width: 100%; height: 400px;"></div>
                                                <a class="popup-close cls-btn" aria-label="close" data-dismiss="modal" href="#" data-target="#popup-1">x</a>
                                            </div>
                                        </div>
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
                    <div class="menu js-top-menu position-static hidden-sm-down" id="_desktop_top_menu">
                        <ul class="top-menu" id="top-menu" data-depth="0">
                            <li class="category" id="cms-category-100">
                                <a class="dropdown-item" href="{{ url('/akcijas') }}">
                                    Akcijas
                                </a>
                            </li>
                            <li class="category" id="category-12">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">
                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_26942" data-toggle="collapse" class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Riepas
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_26942" style="display: none;">
                                    <ul class="top-menu" data-depth="1">
                                        @if (config('site.season') === 1)
                                            <li class="category" id="category-14">
                                                <a class="dropdown-item dropdown-submenu" href="{{ url('/vasaras-riepas') }}" data-depth="1">Vasaras riepas</a>
                                            </li>
                                        @else
                                            <li class="category" id="category-13">
                                                <a class="dropdown-item dropdown-submenu" href="{{ url('/ziemas-riepas') }}" data-depth="1">Ziemas riepas</a>
                                            </li>
                                        @endif
                                        <li class="category" id="category-17">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/motociklu-riepas') }}" data-depth="1">Motociklu riepas</a>
                                        </li>
                                        <li class="category" id="category-16">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/kvadru-riepas') }}" data-depth="1">Kvadraciklu riepas</a>
                                        </li>
                                        @if (config('site.season') === 1)
                                            <li class="category" id="category-13">
                                                <a class="dropdown-item dropdown-submenu" href="{{ url('/ziemas-riepas') }}" data-depth="1">Ziemas riepas</a>
                                            </li>
                                        @else
                                            <li class="category" id="category-14">
                                                <a class="dropdown-item dropdown-submenu" href="{{ url('/vasaras-riepas') }}" data-depth="1">Vasaras riepas</a>
                                            </li>
                                        @endif
                                        <li class="category" id="category-18">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/lielas-riepas') }}" data-depth="1">Lielās riepas</a>
                                        </li>
                                        <li class="category" id="category-19">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/radzes') }}" data-depth="1">Skrūvējamas radzes</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="category" id="category-20">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">
                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_6650" data-toggle="collapse" class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Diski
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_6650" style="display: none;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="category" id="category-21">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/lietie-diski') }}" data-depth="1">Lietie diski</a>
                                        </li>
                                        <li class="category" id="category-atv-rims">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/kvadraciklu-diski') }}" data-depth="1">Kvadraciklu diski</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="category" id="cms-category-4">
                                <a class="dropdown-item" href="{{ url('/pakalpojumi') }}">
                                    Izcenojumi
                                </a>
                            </li>
                            <li class="category" id="cms-category-2">
                                <a class="dropdown-item" href="#" onclick="return false;" data-depth="0">
                                    <span class="float-xs-right hidden-md-up">
                                        <span data-target="#top_sub_menu_50733" data-toggle="collapse" class="navbar-toggler collapse-icons">
                                          <i class="material-icons add"></i>
                                          <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                    Info
                                </a>
                                <div class="popover sub-menu js-sub-menu collapse" id="top_sub_menu_50733" style="display: none;">
                                    <ul class="top-menu" data-depth="1">
                                        <li class="cms-page" id="cms-page-6">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/kontakti') }}" data-depth="1">Kontakti un darba laiks</a>
                                        </li>
                                        <li class="cms-page" id="cms-page-9">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/kondicionieris') }}" data-depth="1">Kondicionieru uzpilde</a>
                                        </li>
                                        <li class="cms-page" id="cms-page-11">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/paskaidrojumi') }}" data-depth="1">Paskaidrojumi</a>
                                        </li>
                                        <li class="cms-page" id="cms-page-12">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/internet-veikals') }}" data-depth="1">Par i-veikalu</a>
                                        </li>
                                        <li class="cms-page" id="cms-page-13">
                                            <a class="dropdown-item dropdown-submenu sizeCalc" href="{{ url('/kalkulators') }}" data-depth="1">Riepu izmēru kalkulators</a>
                                        </li>
                                        <li class="cms-page" id="cms-page-14">
                                            <a class="dropdown-item dropdown-submenu" href="{{ url('/riepu-atruma-indeksu-tabula') }}" data-depth="1">LI un SI indeksu tabula</a>
                                        </li>
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
        <div class="container"></div>
    </aside>

    <section id="wrapper">

        @yield('content')

    </section>

    <div class="contact-card hidden-md-up">
        <div class="contact-card-items">
            <a aria-label="Chat on WhatsApp" target="_blank" href="https://wa.me/{{ config('site.whatsapp') }}"><img src="{{ asset('images/whatsapp.svg') }}" alt="whatsapp" style="background-color: #25d366;"></a>
            <a href="tel:{{ config('site.phones.ulbroka') }}"><img loading="lazy" src="{{ asset('images/phone.svg') }}" alt="phone" style="padding: 10px; background-color: #0d86ff; color: white;"></a>
        </div>
        <div id="toggle-contacts">
            <img loading="lazy" id="tc-phone" src="{{ asset('images/phone.svg') }}" alt="phone" style="padding: 10px; background-color: #0d86ff; color: white;">
            <img loading="lazy" id="tc-close" src="{{ asset('images/close.svg') }}" alt="phone" style="display: none;">
        </div>
    </div>

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
                    <div class="col-md-6"></div>
                </div>
            </div>
        </section>

        <div class="footer-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 links">
                        <div class="row">
                            <div class="col-md-6 wrapper">
                                <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_58482" data-toggle="collapse">
                                    <span class="h3"></span>
                                    <span class="float-xs-right">
                                        <span class="navbar-toggler collapse-icons">
                                            <i class="material-icons add"></i>
                                            <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                </div>
                                <ul id="footer_sub_menu_58482" class="collapse">
                                    <li><a class="cms-page-link" href="{{ url('/kontakti') }}">Kontakti</a></li>
                                    <li><a class="cms-page-link" href="{{ url('/paskaidrojumi') }}">Paskaidrojumi</a></li>
                                    <li><a class="cms-page-link" href="{{ route('pieraksts') }}">E-pieraksts</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6 wrapper">
                                <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_8206" data-toggle="collapse">
                                    <span class="h3"></span>
                                    <span class="float-xs-right">
                                        <span class="navbar-toggler collapse-icons">
                                            <i class="material-icons add"></i>
                                            <i class="material-icons remove"></i>
                                        </span>
                                    </span>
                                </div>
                                <ul id="footer_sub_menu_8206" class="collapse">
                                    <li><a class="cms-page-link" href="{{ url('/internet-veikals') }}">Par i-veikalu</a></li>
                                    <li><a class="cms-page-link" href="{{ url('/pakalpojumi') }}">Pakalpojumi</a></li>
                                    <li><a class="cms-page-link" href="{{ url('/kondicionieris') }}">Kondicionieru uzpilde</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="block-social col-lg-4 col-md-12 col-sm-12">
                        <ul>
                            <li class="facebook">
                                <a href="http://www.facebook.com/pages/R1/342512645789558" target="_blank">Facebook</a>
                            </li>
                        </ul>
                        <a href="https://www.salidzini.lv/" target="_blank"><img loading="lazy" style="border: none;" alt="Salidzini.lv logotips" id="salidzini-banner" title="Interneta veikali. Labākā cena" src="https://static.salidzini.lv/images/logo_button.gif"></a>
                    </div>

                    <div class="col-md-4 address">SIA "R1"<br> Juridiskā adrese:Kalnciema iela 39, Rīga, Latvija,
                        LV-1046<br> Reģistrācijas Nr.: LV 40003479731<br> Banka: Luminor Bank AS Latvijas filiāle<br> Kods: RIKOLV2X<br> Konts:
                        LV91RIKO0001060089254
                    </div>
                </div>
            </div>
        </div>
    </footer>
</main>

@stack('scripts')
</body>
</html>

@php
    // Корзина и авторизация появятся со своими модулями; пока пустые значения.
    $cartCount = $cartCount ?? 0;
@endphp
<div class="container">
    <nav class="header-nav">
        <div class="row">
            <div class="hidden-sm-down">
                <div class="col-md-12 right-nav">
                    <div id="_desktop_user_info">
                        <div class="user-info">
                            @guest
                                <a href="{{ url('/login') }}" title="Pierakstīties savā klienta kontā" rel="nofollow">
                                    <i class="material-icons"></i>
                                    <span class="hidden-sm-down">Ienākt</span>
                                </a>
                            @endguest
                            @auth
                                <a class="logout hidden-sm-down" href="{{ url('/logout') }}" rel="nofollow">
                                    <i class="material-icons"></i>
                                    Iziet
                                </a>
                                <a class="account" href="{{ url('/myaccount') }}" title="Skatīt manu klienta kontu" rel="nofollow">
                                    <i class="material-icons hidden-md-up logged"></i>
                                    <span class="hidden-sm-down">{{ Auth::user()->name }}</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div id="_desktop_cart">
                        <div class="blockcart cart-preview @if ($cartCount > 0) active @else inactive @endif">
                            <div class="header">
                                <i class="desktop material-icons shopping-cart">shopping_cart</i>
                                <span class="desktop hidden-sm-down">Grozs:</span>
                                <span class="desktop cart-products-count">({{ $cartCount }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hidden-md-up text-sm-center mobile">
                <button type="button" class="float-xs-left mobile-nav-burger" id="menu-icon"
                        aria-label="Atvērt izvēlni" aria-expanded="false" aria-controls="mobile-nav-drawer">
                    <i class="material-icons mobile-nav-burger__open" aria-hidden="true">menu</i>
                    <i class="material-icons mobile-nav-burger__close" aria-hidden="true">close</i>
                </button>
                <div class="float-xs-right" id="_mobile_cart">
                    <div class="blockcart cart-preview @if ($cartCount > 0) active @else inactive @endif">
                        <div class="header">
                            <i class="mobile material-icons shopping-cart">shopping_cart</i>
                            <span class="mobile hidden-sm-down">Grozs:</span>
                            <span class="mobile cart-products-count">({{ $cartCount }})</span>
                        </div>
                    </div>
                </div>
                <div class="float-xs-right" id="_mobile_user_info">
                    <div class="user-info">
                        @guest
                            <a href="{{ url('/login') }}" title="Pierakstīties savā klienta kontā" rel="nofollow">
                                <i class="material-icons"></i>
                                <span class="hidden-sm-down">Ienākt</span>
                            </a>
                        @endguest
                        @auth
                            <a class="account" href="{{ url('/myaccount') }}" title="Skatīt manu klienta kontu" rel="nofollow">
                                <i class="material-icons hidden-md-up logged"></i>
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="top-logo" id="_mobile_logo">
                    <a href="/">
                        <img class="logo img-responsive" src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}" alt="R1">
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </nav>
</div>

<div id="mobile-nav-overlay" class="mobile-nav-overlay hidden-md-up" aria-hidden="true"></div>

<nav id="mobile-nav-drawer" class="mobile-nav-drawer hidden-md-up" aria-hidden="true" aria-label="Galvenā izvēlne">
    <div class="mobile-nav-drawer__header">
        <a href="/" class="mobile-nav-drawer__logo">
            <img class="logo img-responsive" src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}" alt="R1">
        </a>
        <button type="button" class="mobile-nav-drawer__close" aria-label="Aizvērt izvēlni">
            <i class="material-icons" aria-hidden="true">close</i>
        </button>
    </div>

    <ul class="mobile-nav-list">
        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--primary" href="{{ url('/akcijas') }}">Akcijas</a>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Riepas
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                @if (config('site.season') === 1)
                    <li><a class="mobile-nav-link" href="{{ url('/vasaras-riepas') }}">Vasaras riepas</a></li>
                @else
                    <li><a class="mobile-nav-link" href="{{ url('/ziemas-riepas') }}">Ziemas riepas</a></li>
                @endif
                <li><a class="mobile-nav-link" href="{{ url('/motociklu-riepas') }}">Motociklu riepas</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/kvadru-riepas') }}">Kvadraciklu riepas</a></li>
                @if (config('site.season') === 1)
                    <li><a class="mobile-nav-link" href="{{ url('/ziemas-riepas') }}">Ziemas riepas</a></li>
                @else
                    <li><a class="mobile-nav-link" href="{{ url('/vasaras-riepas') }}">Vasaras riepas</a></li>
                @endif
                <li><a class="mobile-nav-link" href="{{ url('/lielas-riepas') }}">Lielās riepas</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/radzes') }}">Skrūvējamas radzes</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Diski
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                <li><a class="mobile-nav-link" href="{{ url('/lietie-diski') }}">Lietie diski</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/kvadraciklu-diski') }}">Kvadraciklu diski</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--primary" href="{{ url('/pakalpojumi') }}">Izcenojumi</a>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Info
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                <li><a class="mobile-nav-link" href="{{ url('/kontakti') }}">Kontakti un darba laiks</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/riepu-serviss-ulbroka') }}">Riepu serviss Ulbroka</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/riepu-serviss-riga') }}">Riepu serviss Rīga</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/kondicionieris') }}">Kondicionieru uzpilde</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/paskaidrojumi') }}">Paskaidrojumi</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/internet-veikals') }}">Par i-veikalu</a></li>
                <li><a class="mobile-nav-link sizeCalc" href="{{ url('/kalkulators') }}">Riepu izmēru kalkulators</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/riepu-atruma-indeksu-tabula') }}">LI un SI indeksu tabula</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--accent" href="{{ route('pieraksts') }}">E-Pieraksts</a>
        </li>
    </ul>

    <div class="mobile-nav-drawer__footer">
        <a class="mobile-nav-phone" href="tel:+371{{ config('site.phones.ulbroka') }}">
            <i class="material-icons" aria-hidden="true">phone</i>
            Ulbroka {{ config('site.phones.ulbroka') }}
        </a>
        <a class="mobile-nav-phone" href="tel:+371{{ config('site.phones.kalnciema') }}">
            <i class="material-icons" aria-hidden="true">phone</i>
            Kalnciema {{ config('site.phones.kalnciema') }}
        </a>
    </div>
</nav>

<div class="container">
    <nav class="header-nav">
        <div class="row">
            <div class="hidden-sm-down">
                <div class="col-md-12 right-nav">

                    <div id="_desktop_user_info">
                        <div class="user-info">
                            @guest
                                <a href="{{ route('login') }}"
                                   title="Pierakstīties savā klienta kontā" rel="nofollow">
                                    <i class="material-icons"></i>
                                    <span class="hidden-sm-down">Ienākt</span>
                                </a>
                            @endguest
                            @auth
                                <a class="logout hidden-sm-down" href="{{ route('logout') }}" rel="nofollow">
                                    <i class="material-icons"></i>
                                    Iziet
                                </a>
                                <a class="account" href="{{ route('my-account') }}" title="Skatīt manu klienta kontu" data-role="{{ Auth::user()->getRoleNames() }}" data-user="{{ Auth::user()->fullName }}" rel="nofollow">
                                    <i class="material-icons hidden-md-up logged"></i>
                                    <span class="hidden-sm-down">{{ Auth::user()->fullName }}</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div id="_desktop_cart">
                        <div class="blockcart cart-preview @if ($cartCount > 0) active @else inactive @endif">
                            @if ($cartCount > 0)
                                <div class="header">
                                    <a rel="nofollow" href="{{ route('cart') }}">
                                        <i data-url="{{ route('cart') }}" class="desktop material-icons shopping-cart">shopping_cart</i>
                                        <span class="hidden-sm-down">Grozs:</span>
                                        <span class="cart-products-count">({{ $cartCount }})</span>
                                    </a>
                                </div>
                            @else
                                <div class="header">
                                    <i data-url="{{ route('cart') }}" class="desktop material-icons shopping-cart">shopping_cart</i>
                                    <span class="desktop hidden-sm-down">Grozs:</span>
                                    <span class="desktop cart-products-count">(0)</span>
                                </div>
                            @endif
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
                    <div class="blockcart cart-preview @if (\Cart::count()) active @else inactive @endif"
                         data-refresh-url="//r1riepas.lv/index.php?fc=module&amp;module=ps_shoppingcart&amp;controller=ajax&amp;id_lang=2">
                        @if (\Cart::count() > 0)
                            <div class="header">
                                <a rel="nofollow" href="{{ route('cart') }}">
                                    <i class="material-icons shopping-cart">shopping_cart</i>
                                    <span class="hidden-sm-down">Grozs:</span>
                                    <span class="cart-products-count">({{ \Cart::count() }})</span>
                                </a>
                            </div>
                        @else
                            <div class="header">
                                <i class="mobile material-icons shopping-cart">shopping_cart</i>
                                <span class="mobile hidden-sm-down">Grozs:</span>
                                <span class="mobile cart-products-count">(0)</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="float-xs-right" id="_mobile_user_info">
                    <div class="user-info">
                        @auth
                            <a class="account" href="{{ route('my-account') }}" title="Skatīt manu klienta kontu" rel="nofollow">
                                <i class="material-icons hidden-md-up logged"></i>
                                <span class="hidden-sm-down">{{ Auth::user()->fullName }}</span>
                            </a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" title="Pierakstīties savā klienta kontā" rel="nofollow">
                                <i class="material-icons"></i>
                                <span class="hidden-sm-down">Ienākt</span>
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="top-logo" id="_mobile_logo">
                    <a href="/">
                        <img class="logo img-responsive" src="{{ asset('/img/r1-riepas-logo-1515661637.jpg') }}" alt="R1">
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
            <img class="logo img-responsive" src="{{ asset('/img/r1-riepas-logo-1515661637.jpg') }}" alt="R1">
        </a>
        <button type="button" class="mobile-nav-drawer__close" aria-label="Aizvērt izvēlni">
            <i class="material-icons" aria-hidden="true">close</i>
        </button>
    </div>

    <ul class="mobile-nav-list">
        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--primary" href="{{ route('sale-tires') }}">Akcijas</a>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Riepas
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                @if (config('site.season') === 1)
                    <li><a class="mobile-nav-link" href="{{ route('vasaras-riepas') }}">Vasaras riepas</a></li>
                @else
                    <li><a class="mobile-nav-link" href="{{ route('ziemas-riepas') }}">Ziemas riepas</a></li>
                @endif
                <li><a class="mobile-nav-link" href="{{ route('motociklu-riepas') }}">Motociklu riepas</a></li>
                <li><a class="mobile-nav-link" href="{{ route('kvadraciklu-riepas') }}">Kvadraciklu riepas</a></li>
                @if (config('site.season') === 1)
                    <li><a class="mobile-nav-link" href="{{ route('ziemas-riepas') }}">Ziemas riepas</a></li>
                @else
                    <li><a class="mobile-nav-link" href="{{ route('vasaras-riepas') }}">Vasaras riepas</a></li>
                @endif
                <li><a class="mobile-nav-link" href="{{ route('lielas-riepas') }}">Lielās riepas</a></li>
                <li><a class="mobile-nav-link" href="{{ route('radzes') }}">Skrūvējamas radzes</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Diski
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                <li><a class="mobile-nav-link" href="{{ route('lietie-diski') }}">Lietie diski</a></li>
                <li><a class="mobile-nav-link" href="{{ route('kvadraciklu-diski') }}">Kvadraciklu diski</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--primary" href="/pakalpojumi">Izcenojumi</a>
        </li>

        <li class="mobile-nav-item">
            <button type="button" class="mobile-nav-toggle" aria-expanded="false">
                Info
                <i class="material-icons" aria-hidden="true">keyboard_arrow_down</i>
            </button>
            <ul class="mobile-nav-submenu">
                <li><a class="mobile-nav-link" href="/kontakti">Kontakti un darba laiks</a></li>
                <li><a class="mobile-nav-link" href="{{ route('filiale-ulbroka') }}">Riepu serviss Ulbroka</a></li>
                <li><a class="mobile-nav-link" href="{{ route('filiale-riga') }}">Riepu serviss Rīga</a></li>
                <li><a class="mobile-nav-link" href="{{ url('kondicionieris') }}">Kondicionieru uzpilde</a></li>
                <li><a class="mobile-nav-link" href="{{ route('terms') }}">Paskaidrojumi</a></li>
                <li><a class="mobile-nav-link" href="{{ url('internet-veikals') }}">Par i-veikalu</a></li>
                <li><a class="mobile-nav-link sizeCalc" href="{{ url('/kalkulators') }}">Riepu izmēru kalkulators</a></li>
                <li><a class="mobile-nav-link" href="{{ url('/riepu-atruma-indeksu-tabula') }}">LI un SI indeksu tabula</a></li>
            </ul>
        </li>

        <li class="mobile-nav-item">
            <a class="mobile-nav-link mobile-nav-link--accent" href="{{ route('pieraksts') }}">E-Pieraksts</a>
        </li>
    </ul>

    <div class="mobile-nav-drawer__footer">
        <a class="mobile-nav-phone" href="tel:+37167910555">
            <i class="material-icons" aria-hidden="true">phone</i>
            Ulbroka 67910555
        </a>
        <a class="mobile-nav-phone" href="tel:+37167615615">
            <i class="material-icons" aria-hidden="true">phone</i>
            Kalnciema 67615615
        </a>
    </div>
</nav>


@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                        <header class="page-header">
                            <h1>
                                Pasūtījumu vēsture
                            </h1>
                        </header>
                        <section id="content" class="page-content">
                            <aside id="notifications">
                                <div class="container">
                                    <article class="alert alert-warning" role="alert" data-alert="warning">
                                        <ul>
                                            <li>Jums nav neviena pasūtījuma</li>
                                        </ul>
                                    </article>
                                </div>
                            </aside>
                            <h6>Šie ir pasūtījumi, kurus Jūs esiet veicis (veikusi) kopš konta izveides</h6>
                        </section>
                        <footer class="page-footer">
                            <a href="{{ route('my-account') }}" class="account-link">
                                <i class="material-icons"></i>
                                <span>Atpakaļ uz Jūsu kontu</span>
                            </a>
                            <a href="{{ route('home') }}" class="account-link">
                                <i class="material-icons"></i>
                                <span>Sākumlapa</span>
                            </a>
                        </footer>
                    </section>
                </div>
            </div>
            @include('components.right-sidebar')
        </div>
    </div>

@endsection

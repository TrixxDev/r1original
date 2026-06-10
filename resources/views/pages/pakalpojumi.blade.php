@extends('layouts.app')

@section('canonical_url', url('/pakalpojumi'))
@section('meta_title', 'Riepu montāžas cenas un pakalpojumi | R1 Riepu Serviss')
@section('meta_description', 'Riepu maiņas, balansēšanas un montāžas cenas Rīgā un Ulbrokā. Skatiet izcenojumus vieglajiem auto, SUV un disku remontam — R1 Riepu Serviss.')
@section('meta_keywords', config('seo.keywords.pakalpojumi'))

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            Pakalpojumi
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.pakalpojumi')

                    </section>
                    <footer class='page-footer'>
                        <!-- Footer content -->
                    </footer>
                </section>
            <script>$('section#content').find('table').each(function() { if ($(this).parent().is('div')) { $(this).parent().addClass('pak-table') } })</script>
	    </div>
        </div>
        @include('components.right-sidebar')
    </div>
</div>

@endsection

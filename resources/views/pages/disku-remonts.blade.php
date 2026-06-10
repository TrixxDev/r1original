@extends('layouts.app')

@section('canonical_url', url('/disku-remonts'))
@section('meta_title', 'Disku remonts un valcēšana | R1 Riepu Serviss')
@section('meta_description', 'Disku remonts, valcēšana un restaurācija Rīgā un Ulbrokā. Profesionāls serviss ar garantiju — R1 Riepu Serviss.')
@section('meta_keywords', config('seo.keywords.disku_remonts'))

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            Disku remonts
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.disku-remonts')

                    </section>
                    <footer class='page-footer'>
                        <!-- Footer content -->
                    </footer>
                </section>
            </div>
        </div>
        @include('components.right-sidebar')
    </div>
</div>

@endsection
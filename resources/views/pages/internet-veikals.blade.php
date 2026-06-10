@extends('layouts.app')

@section('canonical_url', url('/internet-veikals'))
@section('meta_title', 'Par interneta veikalu | R1 Riepu Serviss')
@section('meta_description', 'Par R1 interneta veikalu — garantija, apmaksa, piegāde visā Latvijā un Baltijā. Ērta riepu un disku pirkšana internetā ar montāžu servisā.')
@section('meta_keywords', config('seo.keywords.internet_veikals'))

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.internet-veikals')

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

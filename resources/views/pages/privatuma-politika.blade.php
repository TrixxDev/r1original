@extends('layouts.app')

@section('canonical_url', url('/privatuma-politika'))
@section('meta_title', 'Privātuma politika | R1 Riepu Serviss')
@section('meta_description', 'SIA R1 privātuma politika — kā apstrādājam personas datus interneta veikalā un e-pierakstā. R1 Riepu Serviss.')
@section('meta_keywords', config('seo.keywords.privatuma_politika'))

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            Privātuma politika
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.privatuma-politika')

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
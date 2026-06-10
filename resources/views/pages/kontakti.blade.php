@extends('layouts.app')

@section('canonical_url', url('/kontakti'))
@section('meta_title', 'Kontakti un darba laiks | R1 Riepu Serviss')
@section('meta_description', 'R1 Riepu Serviss kontakti — filiāles Ulbrokā (Acones iela 2A) un Rīgā (Kalnciema iela 39). Tālr. +371 67910555, +371 67615615. E-pieraksts montāžai.')
@section('meta_keywords', config('seo.keywords.kontakti'))

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            Kontakti un darba laiks
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.kontakti')

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

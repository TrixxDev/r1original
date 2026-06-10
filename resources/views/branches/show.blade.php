@extends('layouts.app')

@section('canonical_url', route($branch['route_name']))
@section('meta_title', $branch['meta_title'])
@section('meta_description', $branch['meta_description'])
@section('meta_keywords', config('seo.keywords.' . $branch['meta_keywords_key']))

@section('json_ld')
    @include('components.seo.json-ld-branch', ['branch' => $branch])
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="main-content clearfix col-md-12 col-xl-10">
            <div id="content-wrapper" class="right-column col-lg-12">
                <section id="main">
                    @include('brands.partials.breadcrumb', [
                        'items' => [
                            ['label' => 'Sākums', 'url' => url('/')],
                            ['label' => 'Kontakti', 'url' => url('/kontakti')],
                            ['label' => $branch['breadcrumb']],
                        ],
                    ])

                    <header class="page-header">
                        <h1>{{ $branch['h1'] }}</h1>
                    </header>

                    <section id="content" class="page-content page-cms">
                        @include('branches.partials.contact-tables')

                        <div class="branch-local-text">
                            @include('branches.partials.content-' . $branch['key'])
                        </div>

                        <p>@include('branches.partials.single-map')</p>
                    </section>

                    <footer class="page-footer"></footer>
                </section>
            </div>
        </div>
        @include('components.right-sidebar')
    </div>
</div>
@endsection

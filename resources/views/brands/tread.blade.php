@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-right-column page-category tax-display-enabled')

@php
    $treadSlug = \App\Http\Controllers\AutoBrandHubController::treadSegment($tread->t_title);
    $seasonLabel = $season === 1 ? 'Vasaras' : 'Ziemas';
    $productTitle = $currTire->fullName ?? $currTire->title ?? 'Riepas';
    $productHeading = trim($brand->title . ' ' . $tread->t_title);
    $productDescriptionSource = $currTire->t_comment ?: ($currBrand->b_comment ?? '');
    $productDescription = trim(\Illuminate\Support\Str::limit(strip_tags($productDescriptionSource), 160));
    $productDescriptionLong = trim(\Illuminate\Support\Str::limit(strip_tags($productDescriptionSource), 500));
    $treadImageUrl = \App\Helper\Image::treadPublicUrl('auto', $currTire->make_id);
    $treadMinPrice = $tires->min('price2');
    $treadMaxPrice = $tires->max('price2');
    $treadCanonical = route('riepu-razotaji-tread', ['brand' => $brand->slug, 'tread' => $treadSlug]);
@endphp

@section('canonical_url', $treadCanonical)
@section('meta_title', $brand->title . ' ' . $tread->t_title . ' — visi izmēri | R1 Riepu Serviss')
@section('meta_description', $productDescription ?: ($seasonLabel . ' riepas ' . $brand->title . ' ' . $tread->t_title . ' — ' . $tires->count() . ' izmēri ar cenām.'))
@section('meta_keywords', config('seo.keywords.brands_tread'))
@section('meta_image', $treadImageUrl ?? asset('images/favicon.png'))
@section('meta_og_type', 'product')

@section('json_ld')
    @include('components.seo.json-ld-brand-tread', [
        'name' => $productHeading,
        'brandName' => $brand->title,
        'description' => $productDescriptionLong ?: ($seasonLabel . ' riepas ' . $productHeading),
        'url' => $treadCanonical,
        'image' => $treadImageUrl,
        'lowPrice' => $treadMinPrice,
        'highPrice' => $treadMaxPrice,
        'offerCount' => $tires->count(),
    ])
@endsection

@section('content')
@include('brands.partials.shell-open')

    @include('tires.auto.partials.tread-product', [
        'hubTreadMode' => true,
        'hubBreadcrumb' => [
            'brand_slug' => $brand->slug,
            'brand_title' => $brand->title,
            'tread_title' => $tread->t_title,
        ],
        'catalogAjaxUrl' => $catalogAjaxUrl,
        'productHeading' => $productHeading,
    ])

@include('brands.partials.shell-close')
@endsection


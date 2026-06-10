@extends('layouts.app')

@section('body-title', 'product')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-right-column page-product tax-display-enabled product-id-351 product-antares-ingens-a1- product-id-category-14 product-id-manufacturer-59 product-id-supplier-0 product-available-for-order')
@php
  $productTitle = $currTire->fullName ?? $currTire->title ?? 'Riepas';
  $productHeading = trim(($currTire->title ?: '') . ' ' . $currTire->fullSize);
  $productDescriptionSource = $currTire->t_comment ?: ($currBrand->b_comment ?? '');
  $productDescription = trim(\Illuminate\Support\Str::limit(strip_tags($productDescriptionSource), 160));
  $productDescriptionLong = trim(\Illuminate\Support\Str::limit(strip_tags($productDescriptionSource), 500));
  $treadImageUrl = \App\Helper\Image::treadPublicUrl('auto', $currTire->make_id);
  $productCanonical = $currTire->link ?: url()->current();
@endphp
@section('canonical_url', $productCanonical)
@section('meta_title', $productTitle . ' | R1 Riepu Serviss')
@section('meta_description', $productDescription ?: 'Riepas un diski — R1 Riepu Serviss katalogs.')
@section('meta_keywords', config('seo.keywords.product_tire'))
@section('meta_image', $treadImageUrl ?? asset('images/favicon.png'))
@section('meta_og_type', 'product')

@section('json_ld')
    @include('components.seo.json-ld-auto-tire', [
        'name' => $productHeading,
        'brandName' => $currBrand->title,
        'description' => $productDescriptionLong ?: ($productTitle . ' — R1 Riepu Serviss katalogs.'),
        'url' => $productCanonical,
        'image' => $treadImageUrl,
        'price' => $currTire->price2,
        'dotAvailable' => $currTire->dotAvailable,
    ])
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-12">
                <div id="content-wrapper" class="right-column col-lg-12">
                    @include('tires.auto.partials.tread-product', [
                        'hubTreadMode' => false,
                        'productHeading' => $productHeading,
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection


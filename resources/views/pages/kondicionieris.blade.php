@extends('layouts.app')

@section('canonical_url', url('/kondicionieris'))
@section('meta_title', 'Kondicionieru uzpilde un apkope | R1 Riepu Serviss')
@section('meta_description', 'Auto kondicionieru uzpilde, diagnostika un apkope Rīgā (Kalnciema 39) un Ulbrokā. Pieraksts E-pierakstā — R1 Riepu Serviss.')
@section('meta_keywords', config('seo.keywords.kondicionieris'))

@section('content')
  @include('pages.components.kondicionieris')
@endsection

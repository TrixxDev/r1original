@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card">
                <form class="form-horizontal" action="{{ route('admin.auto.tire.update', $tire->tire_id) }}" method="post" enctype="multipart/form-data">
                    <div class="card-header">{{ 'Labot riepas info - ' . $tire->title . ' | ' . $tire->d1 . '/' . $tire->d2 . ' R' . $tire->d3 }}
                        <div style="float: right; position: relative; top: -7px;">
                            <button class="btn btn-md btn-primary" type="submit"> Saglabāt</button>
                            <a class="btn btn-md btn-info" href="{{ route('admin.auto.tires.search', $tire->tread->tread_id) }}"> Atpakaļ</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="d1">Platums</label>
                            <div class="col-md-9">
                                <input class="form-control" id="d1" type="number" @if ($tire->d1) value="{{ $tire->d1 }}" @endif name="d1" placeholder="Riepas platums">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="d2">Augstums</label>
                            <div class="col-md-9">
                                <input class="form-control" id="d2" type="number" @if ($tire->d2) value="{{ $tire->d2 }}" @endif name="d2" placeholder="Riepas augstums">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="d3">Radiuss</label>
                            <div class="col-md-9">
                                <input class="form-control" id="d3" type="number" @if ($tire->d3) value="{{ $tire->d3 }}" @endif name="d3" placeholder="Riepas radiuss">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="shop_price">Veikala cena</label>
                            <div class="col-md-9">
                                <input class="form-control" id="shop_price" type="number" @if ($tire->price1) value="{{ $tire->price1 }}" @endif name="price1" placeholder="Veikala cena">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="discount_price">Akcijas cena</label>
                            <div class="col-md-9">
                                <input class="form-control" id="discount_price" type="number" @if ($tire->price2) value="{{ $tire->price2 }}" @endif name="price2" placeholder="Akcijas cena">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="li">Li</label>
                            <div class="col-md-9">
                                <input class="form-control" id="li" type="number" @if ($tire->li) value="{{ $tire->li }}" @endif name="li" placeholder="Kravnesības indeks">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="si">Si</label>
                            <div class="col-md-9">
                                <input class="form-control" id="si" type="text" @if ($tire->si) value="{{ $tire->si }}" @endif name="si" placeholder="Ātruma indeks">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="code">Kods</label>
                            <div class="col-md-9">
                                <input class="form-control" id="code" type="text" @if ($tire->code) value="{{ $tire->code }}" @endif name="code" placeholder="Kods">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="eco">Degvielas ekonomija</label>
                            <div class="col-md-9">
                                <input class="form-control" id="eco" type="text" @if ($tire->code) value="{{ $tire->code }}" @endif name="eco" placeholder="Degvielas ekonomija">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="wet">Slapjšs segums</label>
                            <div class="col-md-9">
                                <input class="form-control" id="wet" type="text" @if ($tire->wet) value="{{ $tire->wet }}" @endif name="wet" placeholder="Slapjšs segums">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="noise">Skaļums</label>
                            <div class="col-md-9">
                                <input class="form-control" id="noise" type="text" @if ($tire->noise) value="{{ $tire->noise }}" @endif name="noise" placeholder="Skaļums">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="comment">Komentārs</label>
                            <div class="col-md-9">
                                <input class="form-control" id="comment" type="text" @if ($tire->comment) value="{{ $tire->comment }}" @endif name="comment" placeholder="Komentārs">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="article">Artikuls</label>
                            <div class="col-md-9">
                                <input class="form-control" id="article" type="text" @if ($tire->article) value="{{ $tire->article }}" @endif name="article" placeholder="Artikuls">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="quantity">Atlikums</label>
                            <div class="col-md-9">
                                <input class="form-control" id="quantity" type="number" @if ($tire->quantity) value="{{ $tire->quantity }}" @endif name="quantity" placeholder="Atlikums">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="urs_quantity">Ulbrokā</label>
                            <div class="col-md-9">
                                <input class="form-control" id="urs_quantity" type="number" @if ($tire->urs_quantity) value="{{ $tire->urs_quantity }}" @endif name="urs_quantity" placeholder="Atlikums ulbrokā">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="krs_quantity">Kalnciema ielā</label>
                            <div class="col-md-9">
                                <input class="form-control" id="krs_quantity" type="number" @if ($tire->krs_quantity) value="{{ $tire->krs_quantity }}" @endif name="krs_quantity" placeholder="Atlikums kalnciema ielā">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-md btn-primary" type="submit"> Saglabāt</button>
                        <a class="btn btn-md btn-info" href="{{ route('admin.auto.tires.search', $tire->tread->tread_id) }}"> Atpakaļ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                        <header class="page-header">
                            <h1>
                                Jauna adrese
                            </h1>
                        </header>
                        <section id="content" class="page-content">
                            <aside id="notifications">
                                <div class="container">
                                </div>
                            </aside>
                            <div class="address-form">
                                <div class="js-address-form">
                                    <form method="POST" action="//r1riepas.lv/index.php?controller=address&amp;id_address=0" data-id-address="0" data-refresh-url="//r1riepas.lv/index.php?controller=address&amp;ajax=1&amp;action=addressForm">
                                        <section class="form-fields">
                                            <input type="hidden" name="id_address" value="">
                                            <input type="hidden" name="id_customer" value="">
                                            <input type="hidden" name="back" value="">
                                            <input type="hidden" name="token" value="d0c0c555cd02d9bf9638d852060175c8">
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label">
                                                    Aizstājvārds
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="alias" type="text" value="" maxlength="32">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                    Optional
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Vārds
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="firstname" type="text" value="Edgars" maxlength="32" required="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Uzvārds
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="lastname" type="text" value="Indrikis" maxlength="32" required="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label">
                                                    Kompānija
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="company" type="text" value="" maxlength="255">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                    Optional
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label">
                                                    PVN numurs
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="vat_number" type="text" value="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                    Optional
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Adrese
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="address1" type="text" value="" maxlength="128" required="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label">
                                                    Address Complement
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="address2" type="text" value="" maxlength="128">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                    Optional
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Pasta indekss
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="postcode" type="text" value="" maxlength="12" required="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Pilsēta
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="city" type="text" value="" maxlength="64" required="">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label required">
                                                    Valsts
                                                </label>
                                                <div class="col-md-6">
                                                    <select class="form-control form-control-select js-country" name="id_country" required="">
                                                        <option value="" disabled="" selected="">-- please choose --</option>
                                                        <option value="177">Krievija</option>
                                                        <option value="125" selected="">Latvija</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-3 form-control-label">
                                                    Telefons
                                                </label>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="phone" type="text" value="" maxlength="32">
                                                </div>
                                                <div class="col-md-3 form-control-comment">
                                                    Optional
                                                </div>
                                            </div>
                                        </section>
                                        <footer class="form-footer clearfix">
                                            <input type="hidden" name="submitAddress" value="1">

                                            <button class="btn btn-primary float-xs-right" type="submit">
                                                Saglabāt
                                            </button>
                                        </footer>
                                    </form>
                                </div>
                            </div>
                        </section>
                        <footer class="page-footer">
                            <a href="http://r1riepas.lv/index.php?controller=my-account" class="account-link">
                                <i class="material-icons"></i>
                                <span>Atpakaļ uz Jūsu kontu</span>
                            </a>
                            <a href="http://r1riepas.lv/index.php" class="account-link">
                                <i class="material-icons"></i>
                                <span>Sākumlapa</span>
                            </a>
                        </footer>
                    </section>
                </div>
            </div>
            @include('components.right-sidebar')
        </div>
    </div>

@endsection

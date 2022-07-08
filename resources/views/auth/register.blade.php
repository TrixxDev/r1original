@extends('layouts.app')

@section('body-title', 'authentication')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-right-column page-authentication tax-display-enabled page-customer-account')

@section('content')
    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                        <header class="page-header">
                            <h1>
                                Izveidot profilu
                            </h1>
                        </header>
                        <section id="content" class="page-content card card-block">
                            <section class="register-form">
                                <p>Already have an account? <a href="{{ route('login') }}">Log in instead!</a></p>
                                <form action="{{ route('register') }}" id="customer-form" class="js-customer-form" method="post">
                                    <section>
                                        @csrf
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label">
                                                Tituls
                                            </label>
                                            <div class="col-md-6 form-control-valign">
                                                <label class="radio-inline">
                                                <span class="custom-radio">
                                                    <input name="id_gender" type="radio" value="1">
                                                    <span></span>
                                                </span>
                                                    Mr.
                                                </label>
                                                <label class="radio-inline">
                                                <span class="custom-radio">
                                                    <input name="id_gender" type="radio" value="2">
                                                    <span></span>
                                                </span>
                                                    Mrs.
                                                </label>
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                Vārds
                                            </label>
                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                Uzvārds
                                            </label>
                                            <div class="col-md-6">
                                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname" autofocus>

                                                @error('surname')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                Epasts
                                            </label>
                                            <div class="col-md-6">
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                Parole
                                            </label>
                                            <div class="col-md-6">
                                                <div class="input-group js-parent-focus">
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                                    <span class="input-group-btn">
                                                        <button tabindex="-1" class="btn toggle" type="button" data-action="show-password" data-text-show="Rādīt" data-text-hide="Hide">
                                                            Rādīt
                                                        </button>
                                                    </span>
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                Parole
                                            </label>
                                            <div class="col-md-6">
                                                <div class="input-group js-parent-focus">
                                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-comment">
                                            </div>
                                        </div>
                                    </section>
                                    <footer class="form-footer clearfix">
                                        <input type="hidden" name="submitCreate" value="1">
                                        <button class="btn btn-primary form-control-submit float-xs-right" data-link-action="save-customer" type="submit">
                                            Saglabāt
                                        </button>
                                    </footer>
                                </form>
                            </section>
                        </section>
                        <footer class="page-footer">
                            <!-- Footer content -->
                        </footer>
                    </section>
                </div>
            </div>
            @include('components.right-sidebar')
        </div>
    </div>
@endsection



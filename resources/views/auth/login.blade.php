@extends('layouts.app')

@section('body-title', 'authentication')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-right-column page-authentication tax-display-enabled page-customer-account')

@section('content')
     <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10" style="background: white; border-radius: 20px; margin-bottom: 50px;">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                        <header class="page-header">
                            <h1>
                                {{ __('Autorizējieties savā kontā') }}
                            </h1>
                        </header>
                        <section id="content" class="page-content card card-block" style="max-width: 640px!important; margin: 0 auto!important;">
                            <section class="login-form">
                                <form id="login-form"
                                      action="{{ route('login') }}"
                                      method="post">
                                    {{ csrf_field() }}
                                    <section>
                                        <div class="form-group row ">
                                            <label class="col-md-3 form-control-label required">
                                                {{ __('Email') }}
                                            </label>
                                            <div class="col-md-6">
                                                <input id="email" type="email"
                                                       class="form-control @error('email') is-invalid @enderror" name="email"
                                                       value="{{ old('email') }}" required autocomplete="email" autofocus>
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
                                                {{ __('Parole') }}
                                            </label>
                                            <div class="col-md-6">

                                                <div class="input-group js-parent-focus">
                                                    <input class="form-control @error('password') is-invalid @enderror js-child-focus js-visible-password"
                                                           name="password" type="password" value="" pattern=".{5,}"
                                                           required="" autocomplete="current-password">
                                                        <span class="input-group-btn">
                                                            <button tabindex="-1" class="btn toggle" type="button" data-action="show-password" data-text-show="Rādīt"
                                                                data-text-hide="Hide" style="height: 38px!important;">
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

                                        @if (Route::has('password.request'))
                                            <div class="forgot-password" style="text-align: center;">
                                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            </div>
                                        @endif
                                    </section>

                                    <footer class="form-footer text-sm-center clearfix">
                                        <input type="hidden" name="submitLogin" value="1">

                                        <button class="btn btn-primary" data-link-action="sign-in" type="submit">
                                            {{ __('Ienākt') }}
                                        </button>
                                    </footer>
                                </form>
                            </section>
                            <hr>
                            <div class="no-account" style="text-align: center;">
                                <a href="{{ route('register') }}"
                                   data-link-action="display-register-form">
                                    {{ __('Jums nav konts? Izveidojiet to') }}
                                </a>
                            </div>
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

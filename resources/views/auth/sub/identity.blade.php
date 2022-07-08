@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                        <header class="page-header">
                            <h1>
                                Jūsu personīgā informācija
                            </h1>
                        </header>
                        <section id="content" class="page-content">
                            <aside id="notifications">
                                <div class="container">
                                </div>
                            </aside>
                            <form action="{{ route('identity') }}" id="customer-form" class="js-customer-form" method="post">
                            @csrf
                                <section>
                                    <div class="form-group row ">
                                        <label class="col-md-3 form-control-label">
                                            Tituls
                                        </label>
                                        <div class="col-md-6 form-control-valign">
                                            <label class="radio-inline">
                                                <span class="custom-radio">
                                                    <input name="id_gender" type="radio" value="1" @if (Auth::user()->gender == 'Mr.') {{ 'checked=""' }} @endif>
                                                    <span></span>
                                                </span>
                                                Mr.
                                            </label>
                                            <label class="radio-inline">
                                                <span class="custom-radio">
                                                    <input name="id_gender" type="radio" value="2" @if (Auth::user()->gender == 'Mrs.') {{ 'checked=""' }} @endif>
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
                                            <input class="form-control" name="firstname" type="text" value="{{ Auth::user()->name }}" required="">
                                        </div>
                                        <div class="col-md-3 form-control-comment">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-3 form-control-label required">
                                            Uzvārds
                                        </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="lastname" type="text" value="{{ Auth::user()->surname }}" required="">
                                        </div>
                                        <div class="col-md-3 form-control-comment">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-3 form-control-label required">
                                            Epasts
                                        </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="email" type="email" value="{{ Auth::user()->email }}" required="">
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
                                                <input class="form-control js-child-focus js-visible-password" name="password" type="password" value="" pattern=".{5,}" required="">
                                                <span class="input-group-btn">
                                                    <button tabindex="-1" class="btn" type="button" data-action="show-password" data-text-show="Rādīt" data-text-hide="Hide">
                                                    Rādīt
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-control-comment">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-3 form-control-label">
                                            Jaunā parole
                                        </label>
                                        <div class="col-md-6">
                                            <div class="input-group js-parent-focus">
                                                <input class="form-control js-child-focus js-visible-password" name="new_password" type="password" value="" pattern=".{5,}">
                                                <span class="input-group-btn">
                                                    <button tabindex="-1" class="btn" type="button" data-action="show-password" data-text-show="Rādīt" data-text-hide="Hide">
                                                    Rādīt
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-control-comment">
                                            Optional
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
                        <footer class="page-footer">
                            <a href="{{ route('my-account') }}" class="account-link">
                                <i class="material-icons"></i>
                                <span>Atpakaļ uz Jūsu kontu</span>
                            </a>
                            <a href="{{ route('home') }}" class="account-link">
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

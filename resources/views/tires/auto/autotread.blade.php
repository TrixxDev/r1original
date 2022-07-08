@extends('layouts.app')

@section('body-title', 'product')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-right-column page-product tax-display-enabled product-id-351 product-antares-ingens-a1- product-id-category-14 product-id-manufacturer-59 product-id-supplier-0 product-available-for-order')

@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-12">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main" itemscope="" itemtype="https://schema.org/Product">
                        <meta itemprop="url" content="{{ url()->full() }}">
                        <div class="row">
                            <div class="col-md-4">
                                <section class="page-content" id="content">
                                    <div class="images-container">
                                        <div class="product-cover">
                                            @if ($currTire->image)
                                                <img src="{{ $currTire->image }}" style="width: 100%;">
                                            @else
                                                <img src="{{ asset('img/p/lv-default-large_default.jpg') }}" style="width:100%;">
                                            @endif
                                        </div>
                                        <div class="js-qv-mask mask">
                                            <ul class="product-images js-qv-product-images">
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="scroll-box-arrows">
                                        <i class="material-icons left"></i>
                                        <i class="material-icons right"></i>
                                    </div>
                                </section>
                            </div>
                            <div class="col-md-8">
                                <div class="product-main-details">
                                    <h1 class="h1" itemprop="name">{{ $currTire->brands_title . ' ' . $currTire->treads_title }}</h1>
                                    <div class="product-information">
                                        <div id="product-description-short-351" itemprop="description"></div>
                                        <div class="product-actions">
                                            <div class="product-variants">
                                                <div class="product-attributes">
                                                    <table>
                                                        <thead>
                                                        <tr>
                                                            <th>Platums</th>
                                                            <th>Augstums</th>
                                                            <th>Diametrs</th>
                                                            <th>Kods</th>
                                                            <th>LI</th>
                                                            <th>SI</th>
                                                            <th class="fuel_efficiency-head">Degvielas ekonomija</th>
                                                            <th class="wet_grip-head">Slapjš segums</th>
                                                            <th class="external_noise-head">Skaļums</th>
                                                            <th>Piezīmes</th>
                                                            <th>Pieejamība</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{ $currTire->d1 }}</td>
                                                            <td>{{ $currTire->d2 }}</td>
                                                            <td>{{ $currTire->d3 }}</td>
                                                            <td>{{ $currTire->code }}</td>
                                                            <td>{{ $currTire->li }}</td>
                                                            <td>{{ $currTire->si }}</td>
                                                            <td>{{ $currTire->eco }}</td>
                                                            <td>{{ $currTire->wet }}</td>
                                                            <td>{{ $currTire->noise }}</td>
                                                            <td>{{ $currTire->autocomment }}</td>
                                                            <td class="inventory">
                                                                <div class="availability">{{ $currTire->available }}</div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="product-prices">
                                                <div class="product-discount">
                                                    <span>Veikala cena:</span>
                                                    <span class="regular-price">€ {{ $currTire->price1 }}</span>
                                                </div>
                                                <div class="product-price h5 has-discount" itemprop="offers" itemscope="" itemtype="https://schema.org/Offer">
                                                    <link itemprop="availability" href="https://schema.org/InStock">
                                                    <meta itemprop="priceCurrency" content="EUR">

                                                    <div class="current-price">
                                                        <span>Akcijas cena:</span>
                                                        <span itemprop="price" content="{{ $currTire->price2 }}">€ {{ $currTire->price2 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-add-to-cart">
                                                <div class="product-quantity clearfix">
                                                    <div class="qty">
                                                        <div class="input-group bootstrap-touchspin">
                                                            <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
                                                            <input type="hidden" name="article" class="tire_article" value="{{ $currTire->article }}">
                                                            <input type="hidden" name="title" class="tire_title" value="{{ $currTire->title }}">
                                                            <input type="text" name="qty" id="quantity_wanted" value="4" class="input-group form-control" min="1" aria-label="Daudzums" style="display: block;">
                                                            <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                                                            <span class="input-group-btn-vertical">
                                                                <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-up" type="button">
                                                                    <i class="material-icons touchspin-up"></i>
                                                                </button>
                                                                <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-down" type="button">
                                                                    <i class="material-icons touchspin-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="add">
                                                        <button class="btn btn-primary add-to-cart" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-button-action="add-to-cart" data-info="{{ $currTire->tire_id }}">
                                                            <i class="material-icons shopping-cart"></i>
                                                            Pirkt
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="product-minimal-quantity">
                                                </p>
                                            </div>
                                            <div class="product-additional-info">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="comments_note">
                        </div>

                        <div class="row">
                            <div class="col-lg-9 col-md-12 float-lg-right">

                                <table id="ct_matrix" class="rwd-table tablesorter tablesorter-default tablesorterd781ad8ff3a5c" role="grid">
                                    <thead>
                                    <tr class="ct_matrix_head tablesorter-headerRow" role="row">
                                        <th data-column="0" class="tablesorter-header tablesorter-headerUnSorted" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Izmērs: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Izmērs</div></th>
                                        <th class="nosort hidden-sm-down tablesorter-header tablesorter-headerUnSorted" data-column="1" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="LI/SI: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">LI/SI</div></th>
                                        <th class="nosort hidden-sm-down tablesorter-header tablesorter-headerUnSorted" data-column="2" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Kods: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Kods</div></th>
                                        <th class="nosort hidden-sm-down fuel_efficiency-head tablesorter-header tablesorter-headerUnSorted" data-column="3" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Degvielas ekonomija: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Degvielas ekonomija</div></th>
                                        <th class="nosort hidden-sm-down wet_grip-head tablesorter-header tablesorter-headerUnSorted" data-column="4" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Slapjš segums: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Slapjš segums</div></th>
                                        <th class="nosort hidden-sm-down external_noise-head tablesorter-header tablesorter-headerUnSorted" data-column="5" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Skaļums: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Skaļums</div></th>
                                        <th class="cth_price tablesorter-header tablesorter-headerUnSorted" data-column="6" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Veikala cena: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Veikala cena</div></th>                            <th class="cth_price tablesorter-header tablesorter-headerUnSorted" data-column="7" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Akcijas cena: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Akcijas cena</div></th>                                        <th class="hidden-sm-down tablesorter-header tablesorter-headerUnSorted" data-column="8" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Piezīmes: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Piezīmes</div></th>
                                        <th class="nosort cth_addtocart tablesorter-header tablesorter-headerUnSorted" data-column="9" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label="Grozs: No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner">Grozs</div></th>                        <th class="cth_availability auto tablesorter-header tablesorter-headerUnSorted" data-column="10" tabindex="0" scope="col" role="columnheader" aria-disabled="false" aria-controls="ct_matrix" unselectable="on" aria-sort="none" aria-label=": No sort applied, activate to apply an ascending sort" style="user-select: none;"><div class="tablesorter-header-inner"></div></th>
                                    </tr>
                                    </thead>
                                    <tbody aria-live="polite" aria-relevant="all">
                                    @foreach ($tires as $tire)

                                    @php
                                        $tire->includeStock = true;
                                    @endphp

                                    <tr class="ct_matrix_row ctr0 @if ($currTire->tire_id == $tire->tire_id) {{ 'current' }} @endif" id="ctrcombid{{ $tire->tire_id }}" role="row">
                                        <form action="https://r1riepas.lv/index.php?controller=cart" method="post" id="ct_matrix_{{ $tire->tire_id }}" name="ct_matrix_{{ $tire->tire_id }}"></form>
                                        <td>
                                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids2[]"> {{ $tire->fullSize }}
                                        </td>
                                        <td class="hidden-sm-down">{{ $tire->lisi }}</td>
                                        <td class="hidden-sm-down">{{ $tire->code }}</td>
                                        <td class="hidden-sm-down">{{ $tire->eco }}</td>
                                        <td class="hidden-sm-down">{{ $tire->wet }}</td>
                                        <td class="hidden-sm-down">{{ $tire->noise }}</td>
                                        <td data-label="Veikala cena" class="ctd_price ctd_attr_group_price">
                                            € {{ $tire->price1 }}
                                        </td>
                                        <td data-label="Akcijas cena" class="ctd_price ctd_attr_group_price">
                                            <strong class="strongprice" data-price="{{ $tire->price2 }}">€ {{ $tire->price2 }}</strong>
                                        </td>
                                        <td class="hidden-sm-down">{{ $tire->autocomment }}</td>
                                        <td class="ctd_addtocart" data-label="Grozs">
                                            <input alt="ct_matrix_{{ $tire->tire_id }}" name="qty" class="qty" id="ct_matrix_{{ $tire->tire_id }}_idQty" value="4 " type="text" style="display:none!important;">
                                            <div class="ct_submit btn btn-sm btn-primary" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif id="ct_matrix_{{ $tire->tire_id }}_submit" data-article="{{ $tire->article }}" data-info="{{ $tire->tire_id }}">
                                                <i class="material-icons">add_shopping_cart</i>
                                            </div>
                                        </td>
                                        <td class="ctd_availability">
                                            <span class="clearfix atc_div">
                                                <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip" data-html="true" title="{{ $tire->stockAvailability }}"><span class="sort-order">6</span></span>
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <nav class="pagination ct_pagination">
                                    <ul id="ct_pagination" class="page-list clearfix text-xs-center"></ul>
                                </nav>
                                <input type="hidden" name="ctab_id_product" class="ctab_id_product" value="351"><!-- end D:\OpenServer\domains\r1old/modules/combinationstab/views/templates/hook/productfooter.tpl -->
                                <script type="text/javascript">
                                    var productcomments_controller_url = 'http://r1riepas.lv/index.php?fc=module&module=productcomments&controller=default&id_lang=2';
                                    var confirm_report_message = 'Are you sure that you want to report this comment?';
                                    var secure_key = 'ecd08b6b11bbf8aa900e405ad158179e';
                                    var productcomments_url_rewrite = '0';
                                    var productcomment_added = 'Your comment has been added!';
                                    var productcomment_added_moderation = 'Your comment has been submitted and will be available once approved by a moderator.';
                                    var productcomment_title = 'New comment';
                                    var productcomment_ok = 'OK';
                                    var moderation_active = 1;
                                </script>

                            </div>
{{--                            <div class="col-lg-3 col-md-12 float-lg-left">--}}
{{--                                <div id="productCommentsBlock">--}}

{{--                                    <div class="tabs">--}}
{{--                                        <div class="clearfix pull-right">--}}
{{--                                            <a class="open-comment-form btn btn-primary" href="#new_comment_form">Rakstīt komentāru</a>--}}
{{--                                        </div>--}}
{{--                                        <div id="new_comment_form_ok" class="alert alert-success" style="display:none;padding:15px 25px"></div>--}}
{{--                                        <div id="product_comments_block_tab">--}}


{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <!-- Fancybox -->--}}
{{--                                    <div style="display:none">--}}
{{--                                        <div id="new_comment_form" style="display: none;">--}}
{{--                                            <form id="id_new_comment_form" action="#">--}}
{{--                                                <div class="new_comment_form_content">--}}
{{--                                                    <h2>Rakstīt komentāru</h2>--}}
{{--                                                    <div id="new_comment_form_error" class="error" style="display:none;padding:15px 25px">--}}
{{--                                                        <ul></ul>--}}
{{--                                                    </div>--}}
{{--                                                    <label>Vārds<sup class="required">*</sup></label>--}}
{{--                                                    <input id="commentCustomerName" name="customer_name" type="text" value="">--}}

{{--                                                    <label for="comment_title">Nosaukums<sup class="required">*</sup></label>--}}
{{--                                                    <input id="comment_title" name="title" type="text" value="">--}}

{{--                                                    <label for="content">Komentārs<sup class="required">*</sup></label>--}}
{{--                                                    <textarea id="content" name="content"></textarea>--}}
{{--                                                    <div id="new_comment_form_footer">--}}
{{--                                                        <input id="id_product_comment_send" name="id_product" type="hidden" value="351">--}}
{{--                                                        <p class="fl required"><sup>*</sup> Obligāts</p>--}}
{{--                                                        <p class="fr">--}}
{{--                                                            <button class="btn btn-primary" id="submitNewMessage" name="submitMessage" type="submit">Sūtīt</button>&nbsp;--}}
{{--                                                            vai&nbsp;<a href="#" onclick="$.fancybox.close();">Aizvert</a>--}}
{{--                                                        </p>--}}
{{--                                                        <div class="clearfix"></div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </form><!-- /end new_comment_form_content -->--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!-- End fancybox -->--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="modal fade js-product-images-modal" id="product-modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <figure>
                                            <img class="js-modal-product-cover product-cover-modal" width="" src="" alt="" title="" itemprop="image">
                                            <figcaption class="image-caption">
                                                <div id="product-description-short" itemprop="description"></div>
                                            </figcaption>
                                        </figure>
                                        <aside id="thumbnails" class="thumbnails js-thumbnails text-sm-center">
                                            <div class="js-modal-mask mask  nomargin ">
                                                <ul class="product-images js-modal-product-images">
                                                </ul>
                                            </div>

                                        </aside>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <!-- /.modal -->
                        <!-- /.modal -->



                        <footer class="page-footer">

                            <!-- Footer content -->

                        </footer>

                    </section>



                </div>

            </div>

        </div>
    </div>

@endsection

@extends('layouts.app')

@section('body-title', 'category')
{{--@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-14 category-' . $season_title . ' category-id-parent-12 category-depth-level-3')--}}

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">
        <div id="content-wrapper" class="right-column col-lg-12">
          <section id="main" itemscope="" itemtype="https://schema.org/Product">
            <meta itemprop="url" content="{{ url()->full() }}">
            <div class="row">
              <div class="col-md-12 col-lg-4">
                <section class="page-content" id="content">
                  <div class="images-container">
                    <div class="product-cover card">
{{--                      @if ($currTire->image)--}}
{{--                        <img src="{{ $currTire->image }}" style="width: 100%;">--}}
{{--                      @else--}}
{{--                        <img src="{{ asset('img/p/lv-default-large_default.jpg') }}" style="width:100%;">--}}
{{--                      @endif--}}
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
              <div class="col-md-12 col-lg-8">
                <div class="row">
                  <div class="col-sm-12 product-main-details">
{{--                    <h1 class="h1 mt-1" itemprop="name">{{ $rims[0]->brands_title.' '.$tires[0]->treads_title }}</h1>--}}
                  </div>
                  <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="product-prices">
                      <div class="product-discount">
                        <span>Veikala cena:</span>
{{--                        <span class="regular-price">€ {{ $currTire->price1 }}</span>--}}
                      </div>
                      <div class="product-price h5 has-discount" itemprop="offers" itemscope="" itemtype="https://schema.org/Offer">
                        <link itemprop="availability" href="https://schema.org/InStock">
                        <meta itemprop="priceCurrency" content="EUR">

                        <div class="current-price">
                          <span>Akcijas cena:</span>
{{--                          <span itemprop="price" content="{{ $currTire->price2 }}">€ {{ $currTire->price2 }}</span>--}}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="product-add-to-cart">
                      <div class="product-quantity clearfix">
                        <div class="qty">
                          <div class="input-group bootstrap-touchspin" style="transform: none;">
                            <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
{{--                            <input type="hidden" name="article" class="tire_article" value="{{ $currTire->article }}">--}}
{{--                            <input type="hidden" name="title" class="tire_title" value="{{ $currTire->title }}">--}}
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
                          <button class="btn btn-primary add-to-cart" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-button-action="add-to-cart"
{{--                                  data-info="{{ $currTire->tire_id }}"--}}
                          >
                            <i class="material-icons shopping-cart"></i>
                            Pirkt
                          </button>
                        </div>
                      </div>
                      {{--                                    <p class="product-minimal-quantity">--}}
                      {{--                                    </p>--}}
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 col-sm-12 col-md-4">
                    <table class="table">
                      <thead>
                      <tr>
                        <th>Platums</th>
                        <td>d1</td>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <th>Augstums</th>
                        <td>d2</td>
                      </tr>
                      <tr>
                        <th>Diametrs</th>
                        <td>d3</td>
                      </tr>
                      <tr>
                        <th>Kods</th>
                        <td>code</td>
                      </tr>
                      <tr>
                        <th>Li</th>
                        <td>{{ $currTire->li }}</td>
                      </tr>
                      <tr>
                        <th>Si</th>
                        <td>{{ $currTire->si }}</td>
                      </tr>
                      <tr>
                        <th>Degvielas ekonomija</th>
                        <td>{{ $currTire->eco }}</td>
                      </tr>
                      <tr>
                        <th>Mitrs segums</th>
                        <td>{{ $currTire->wet }}</td>
                      </tr>
                      <tr>
                        <th>Skaņa</th>
                        <td>{{ $currTire->noise }}</td>
                      </tr>
                      <tr>
                        <th>Piezīmes</th>
                        <td>
                          @php
                            if($currTire->autocomment) {
                                echo $currTire->autocomment;
                            } else {
                                echo '-';
                            }
                          @endphp
                        </td>
                      </tr>
                      <tr>
                        <th>Pieejamība</th>
                        <td>{{ $currTire->available }}</td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-sm-12 col-md-8">
                    <div class="alert" style="border: 1px solid #68c0a8">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam aperiam consequuntur cupiditate dolore ea in laudantium maxime mollitia nihil optio possimus quisquam, quo repudiandae sequi sit vel veritatis voluptates!
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi culpa cum ducimus et, eum ex, exercitationem expedita harum inventore itaque maiores, molestiae nam numquam qui quidem quo repellat reprehenderit veniam.
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit. A accusamus adipisci ducimus eaque eum illo ipsam necessitatibus nulla odit praesentium, similique temporibus velit vero? Aliquid, harum, libero? Atque, eligendi, molestias.
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="comments_note">
            </div>

            <div class="row">
              <div class="">

                <table id="tires-table" class="table summer-sorter tires-table table-hover tablesorter">
                  <thead class="tires-thead" style="position:sticky; top: -1px;">
                  <tr>
                    <th scope="col"></th>
                    <th scope="col" class="text-center">Izmērs</th>
                    <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                    <th scope="col" class="hidden-sm-down text-center">Kods</th>

                    <th scope="col" class="hidden-sm-down">
                      <div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div>
                    </th>

                    <th scope="col" class="hidden-sm-down">
                      <div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div>
                    </th>

                    <th scope="col" class="hidden-sm-down">
                      <div class="tire-table-icon icon-tire-sound" title="Troksnis"></div>
                    </th>

                    <th id="store-price-button" scope="col" class="text-center">
                      Veikala cena
                    </th>

                    <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                    <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                    <th scope="col">Grozs</th>
                    <th scope="col">
                      <div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div>
                    </th>

                  </tr>
                  </thead>
                  <tbody id="tires-table-body">
                  @foreach ($rims as $rim)

{{--                    @php--}}
{{--                      $tire->includeStock = true;--}}
{{--                    @endphp--}}

                    <tr class="tire-table-row">
                      <th scope="row" class="tread-tire-table-checkbox text-center">
                        <input type="checkbox" value="{{ $rim->rim_id }}" name="product_ids[]"
                               class="tire-table-checkbox">
                      </th>

                      <td class="tread-name-cell-size text-center">
{{--                        {{ $tire->fullSize }}--}}
                          rim->fullSize
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
            </div>
          </section>




        </div>

      </div>

    </div>
  </div>

@endsection

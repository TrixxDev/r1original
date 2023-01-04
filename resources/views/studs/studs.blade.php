@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-21 category-jauni-lietie-diski category-id-parent-20 category-depth-level-3')

@section('content')

<div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12">
        <div id="left-column" class="col-md-12 col-lg-3">
          @include('components.studsfilter')
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products" class="">
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">

                </div>
              </div>
              {{-- LIST VIEW --}}
              <div id="js-product-list">
                <div class="products row hide-price title-flip">

                </div>
                <nav class="pagination">
                  <div class="col-md-12">
                  </div>
                </nav>
                <div class="hidden-md-up text-xs-right up">
                  <a href="#header" class="back-to-top-button">
                    <i class="material-icons"></i>
                  </a>
                </div>
                {{--                      {{ $rims->links() }}--}}
              </div>
              <div id="js-product-list-bottom">
                <div id="js-product-list-bottom"></div>
              </div>
            </section>
          </section>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="mobileFilterModal" tabindex="-1" role="dialog"
     aria-labelledby="mobileFilterModalTitle" aria-hidden="true">
  <div class="modal-dialog mobile-filter-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @include('components.studsfilter')
      </div>
    </div>
  </div>
</div>

@endsection

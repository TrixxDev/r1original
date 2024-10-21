$(document).ready(function() {

  $.urlParam = function(name){
    let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null) {
      return null;
    }
    return decodeURI(results[1]) || 0;
  }

  $('.loading-block-content').fadeIn();

  const pathParts = window.location.pathname.split('/');
  window['fastsearch'] = '';
  window['fastsearchInput'] = '';
  let newUrl;
  window['availability'] = ($.urlParam('availability') !== null) ? '&availability=' + $.urlParam('availability') : '';
  window['type'] = ($.urlParam('type') !== null) ? '&type=' + $.urlParam('type') : '';
  window['table_type'] = '&table_type=list';
  let pageNr = 1;
  window['page'] = ($.urlParam('page') !== null) ? '&page=' + $.urlParam('page') : '';
  window['selected_tires'] = ($.urlParam('selected') !== null) ? '&selected=' + $.urlParam('selected') : '';
  window['show_selected'] = ($.urlParam('show_selected') !== null) ? '&show_selected=' + $.urlParam('show_selected') : '';
  let pageLoaded;

  // SHOW LIST VIEW
  $('div.can-collapse span.show_list').on('click', function(){
    $(this).addClass('active');
    $('span.show_grid').removeClass('active');
    localStorage.setItem("show_type", "list");
    window['table_type'] = '&table_type=list';
    loadItems();
  });

// SHOW GRID VIEW
  $('div.can-collapse span.show_grid').on('click', function(){
    $(this).addClass('active');
    $('span.show_list').removeClass('active');
    localStorage.setItem("show_type", "grid");
    window['table_type'] = '&table_type=grid';
    loadItems();
  });

// SHOW VIEW DEPENDING ON LOCAL STORAGE VALUE
  if (localStorage.getItem('show_type') === 'list') {
    $('span.show_list').addClass('active');
    $('span.show_grid').removeClass('active');
    window['table_type'] = '&table_type=list';
  }
  if (localStorage.getItem('show_type') === 'grid') {
    $('span.show_grid').addClass('active');
    $('span.show_list').removeClass('active');
    window['table_type'] = '&table_type=grid';
  }

  // $(document).on('keydown', function(e) {
  //   if (e.ctrlKey && e.key === " ") {
  //     $('.fastsearch-modal').fadeIn();
  //   }
  //   if (e.key === "Escape") {
  //     $('.fastsearch-modal').fadeOut().find('.r1-select-input').val('');
  //   }
  // })

  let brand;
  window['d1'] = 120;
  window['d2'] = 70;
  window['d3'] = 17;

  $('.filter-button').on('click', function(e) {
    e.preventDefault();
    window['availability'] = '';
    window['type'] = '';
    window['fastsearch'] = '';
    window['fastsearchInput'] = '';
    $('#search_filters #show-selected-checkbox:visible').attr('checked', false).prop('checked', false).next().children().css('display', 'none');
    window['selected_tires'] = '';
    window['show_selected'] = '';
    window['page'] = '';
    $('.custom-checkbox input').removeAttr('checked').prop('checked', false);
    pageNr = 1;
    loadItems();
  });

  $('select.r1-select-input').select2(({
    language: 'lv',
    maximumSelectionLength: 1,
    placeholder: "Ātrā meklēšana",
    data: sizes,
  })).on("select2:select", function () {
    $('.loading-block-content').fadeIn();
    $('.select2-container').removeClass('select2-container--focus').removeClass('select2-container--open');
    $('textarea.select2-search__field').blur();
    window['availability'] = '';
    window['type'] = '';
    $('.custom-checkbox input').removeAttr('checked').prop('checked', false);
    window['fastsearchInput'] = $(this).val();
    window['fastsearch'] = '&fastsearch=' + window['fastsearchInput'];
    $('#search_filters #show-selected-checkbox:visible').attr('checked', false).prop('checked', false).next().children().css('display', 'none');
    window['selected_tires'] = '';
    window['show_selected'] = '';
    window['page'] = '';
    pageNr = 1;
    if ($(this).val().length > 0) loadItems();
  });

  function handlePaginationClick(pageId) {
    // Update the current page
    pageNr = pageId;
    window['page'] = '&page=' + pageNr;
    // Call your loadItems function with the new page
    loadItems();
  }
  $(document).on('click', '.pagination a[data-page]', function (e) {
    e.preventDefault();
    pageNr = $(this).data('page');
    handlePaginationClick(pageNr);
  });
  // Previous button click event
  $(document).on('click', '.pagination .previous', function (e) {
    e.preventDefault();
    if (pageNr > 1) {
      pageNr--;
      handlePaginationClick(pageNr);
    }
  });
  // Next button click event
  $(document).on('click', '.pagination .next', function (e) {
    e.preventDefault();
    pageNr++;
    handlePaginationClick(pageNr);
  });

  $('ul#facet_availability:visible li label .custom-checkbox input').on('change', function() {
    let selectedColors = [];

    // Check which color checkboxes are checked and add them to the selectedColors array
    if ($('ul#facet_availability:visible input.green').is(':checked')) {
      selectedColors.push('green');
    }
    if ($('ul#facet_availability:visible input.yellow').is(':checked')) {
      selectedColors.push('yellow');
    }
    if ($('ul#facet_availability:visible input.red').is(':checked')) {
      selectedColors.push('red');
    }

    // Build the availability parameter based on selected colors
    window['availability'] = selectedColors.length > 0 ? '&availability=' + selectedColors.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_type:visible li label .custom-checkbox input').on('change', function() {
    let selectedTypes = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_type:visible input[data-for="prod-type"]:checked').each(function() {
      selectedTypes.push($(this).data('value'));
    });

    // Build a parameter string based on selected values
    window['type'] = selectedTypes.length > 0 ? '&type=' + selectedTypes.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('#search_filters #show-selected-checkbox:visible').on('click', function(e) {
    e.preventDefault();

    $(this).attr('checked', function(i, value) {
      if (value === undefined) {
        window['show_selected'] = '&show_selected=show';
        $(this).next().children().css('display', 'block');
        return 'checked';
      } else {
        window['show_selected'] = '';
        $(this).next().children().css('display', 'none');
        return null;
      }
    });

    loadItems();
  });

  function loadItems()
  {
    $('.loading-block-content').fadeIn();

    brand = $('select.tire-brand option:selected').val();
    let tire_width = $('select.tire-width option:selected').val();
    let tire_height = $('select.tire-height option:selected').val();
    let tire_radius = $('select.tire-radius option:selected').val();

    if (tire_width != window['d1']) window['d1'] = tire_width;
    if (tire_height != window['d2']) window['d2'] = tire_height;
    if (tire_radius != window['d3']) window['d3'] = tire_radius;

    if (fastsearchInput.length > 0) {
      $.get('/api/tires/motoSplitInput/' + fastsearchInput, '', function(data) {
        window['d1'] = data.d1;
        window['d2'] = data.d2;
        window['d3'] = data.d3;
      });

      $('select.tire-width option, select.tire-height option, select.tire-radius option').removeAttr('selected').prop('selected', false);

      $('select.tire-width option[id="' + window['d1'] + '"]').attr('selected', true).prop('selected', true);
      $('select.tire-height option[id="' + window['d2'] + '"]').attr('selected', true).prop('selected', true);
      $('select.tire-radius option[id="' + window['d3'] + '"]').attr('selected', true).prop('selected', true);

      $('select.r1-select-input').val(null).trigger('change');
    }

    brand = (brand == 'Ražotājs') ? '' : 'brand=' + brand + '&';

    let url = '/api/tires/moto/?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['fastsearch'] + window['availability'] + window['type'] + window['table_type'] + window['selected_tires'] + window['show_selected'] + window['page'];

    if (pageLoaded === 0) {
      url = '/api/tires/moto/?' + brand + window['fastsearch'] + location.search.slice(1) + window['table_type'];
    }



    let shopping_button_count = 0;

    $.ajax({
      url: url,
      method: 'GET',
      success: function(data) {
        $('#js-product-list .products').html(data);

        if (window['selected_tires'].length > 0) {
          tires_array = window['selected_tires'].replace('&selected=', '').split(',');
          $(document).find('th.tire-table-checkbox').children().each(function() {
            $(this).removeAttr('checked').removeProp('checked');
            $(this).parent().parent().removeClass('selected');
          });
          $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]').each(function() {
            $(this).removeAttr('checked').removeProp('checked');
            $(this).parent().parent().parent().removeClass('selected');
          });
          $.map(tires_array, function(value, index) {
            $('.tire-table-row .tire-table-checkbox input[value="' + value + '"]').attr('checked', true).prop('checked', true).parent().parent().toggleClass('selected');
            $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox][value="' + value + '"]').attr('checked', true).prop('checked', true).parent().parent().parent().toggleClass('selected');
          });
        }

        $('.moto-sorter').each(function() {
          $(this).tablesorter({
              sortList: [[6,1]],
              headers: {
                0: {sorter: false},
                1: {sorter: true},
                2: {sorter: false},
                3: {sorter: false},
                4: {sorter: false},
                5: {sorter: true},
                6: {sorter: true},
                7: {sorter: false},
                8: {sorter: false},
                9: {sorter: true}
              },
            }
          );
        });

        $($('.pagination-col')).insertAfter($('#tires-table:last-child'));

        // Rādīt izvēlētos start
        $(document).find('th.tire-table-checkbox').children().on('click', function() {
          tires_array = [];
          $(this).parent().parent().toggleClass('selected');
          $(document).find('th.tire-table-checkbox').children(':checked').each(function() {
            tires_array.push($(this).val());
          });

          if (tires_array.length > 0) {
            $('#show-selected-checkbox').removeAttr('disabled').prop('disabled', false);
            window['selected_tires'] = '&selected=' + tires_array.join(',');
          } else {
            $('#show-selected-checkbox').attr('disabled', true).prop('disabled', true);
            window['selected_tires'] = '';
          }
          newUrl = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['type'] + window['selected_tires'] + window['show_selected'] + window['page'];

          if (pageLoaded === 1) {
            history.pushState({ prevUrl: document.referrer }, '', newUrl);
          }
        });
        // Rādīt izvēlētos end

        // Rādīt izvēlētos (saraksts) start
        if ($('#js-product-list .mobile-tire-container').is(':visible')) {
          $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]').on('click', function () {
            tires_array = [];
            $(this).parent().parent().parent().toggleClass('selected');
            $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]:checked').each(function () {
              tires_array.push($(this).val());
            });

            if (tires_array.length > 0) {
              $('#show-selected-checkbox').removeAttr('disabled').prop('disabled', false);
              window['selected_tires'] = '&selected=' + tires_array.join(',');
            } else {
              $('#show-selected-checkbox').attr('disabled', true).prop('disabled', true);
              window['selected_tires'] = '';
            }
            newUrl = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['type'] + window['selected_tires'] + window['show_selected'] + window['page'];

            if (pageLoaded === 1) {
              history.pushState({prevUrl: document.referrer}, '', newUrl);
            }
          });
        }

        $(document).find('.tire-table-checkbox').children().each(function(key, value){
          // PARSE TO INT
          products.push(parseInt($(value).val()));

          // ON SHOPPING CART BUTTON CLICK
          $(this).parent().parent().find('.cart-shopping-button').on('click', function() {

              if (!admin) {
                const tire_id = $(this).data('info');

                let ajaxUrl = '/motociklu-riepas';

                let tire_name = $(this).parent().parent().parent().find('.table-tire-name-cell');
                if (tire_name.attr('data-link')) {
                  ajaxUrl = tire_name.attr('data-link');
                }

                $.ajax({
                  url: ajaxUrl + '/ajax',
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  method: 'POST',
                  data: { tire_id: tire_id },
                  success: function(data)
                  {
                    data = JSON.parse(data);
                    let cart_quantity = data.quantity;
                    cart_quantity = parseInt(cart_quantity);
                    let total_sum = data.total_sum;
                    total_sum = parseInt(total_sum);

                    if (typeof data.cart.options.tire.tread.tread_id === "undefined") {
                      if (data.cart.options.tire.make_id){
                        fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg',
                          { method: 'GET' },)
                          .then(res => {
                            if (res.ok) {
                              $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg');
                            } else {
                              $('.modal-image-preview img').attr('src', '/img/p/en-default-home_default.jpg');
                            }
                          });
                      }
                    } else {
                      fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.tread.tread_id + '-o.jpg',
                        { method: 'GET' },)
                        .then(res => {
                          if (res.ok) {
                            $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.tread.tread_id + '-o.jpg');
                          } else {
                            $('.modal-image-preview img').attr('src', '/img/p/en-default-home_default.jpg');
                          }
                        });
                    }

                    if (data.cart.options.image == 'stud') {
                      // STUD IMAGE INSIDE MODAL
                      $('.modal-product-info .product-name').html(data.cart.name);
                      $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2)).attr('data-price', parseInt(data.cart.options.tire.price2));
                      $('.modal-product-info .product-stud-length').html(data.cart.options.tire.stud_length);
                      $('.modal-product-info .product-stud-count').html(data.cart.options.tire.stud_count);
                      $('.modal-product-info .product-comment').html(data.cart.options.tire.comment);
                      $('.cart-content .cart-products-total').html(total_sum);
                      $('span.cart-products-count').html('(' + cart_quantity + ')');
                      $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
                      $('.blockcart.cart-preview .header').empty();
                      $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
                    } else if (data.cart.options.image == 'rims' || data.cart.options.image == 'quadrims') {
                      // STUD IMAGE INSIDE MODAL
                      $('.modal-product-info .product-name').html(data.cart.name);
                      $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2)).attr('data-price', parseInt(data.cart.options.tire.price2));
                      $('.modal-product-info .product-rim-width').html(data.cart.options.tire.d1);
                      $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
                      $('.modal-product-info .product-lug-distance').html(data.cart.options.tire.skr + 'x' + data.cart.options.tire.pcd);
                      $('.modal-product-info .product-comment').html(data.cart.options.tire.comment);
                      $('.cart-content .cart-products-total').html(total_sum);
                      $('span.cart-products-count').html('(' + cart_quantity + ')');
                      $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
                      $('.blockcart.cart-preview .header').empty();
                      $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
                    } else {
                      // TIRE IMAGE INSIDE MODAL
                      $('.modal-product-info .product-name').html(data.cart.name);
                      if (data.cart.options.tire.price2 != null) {
                        $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2)).attr('data-price', parseInt(data.cart.options.tire.price2));
                      } else {
                        $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price3)).attr('data-price', parseInt(data.cart.options.tire.price3));
                      }
                      $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
                      $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
                      $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
                      $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
                      $('.modal-product-info .product-li').html(data.cart.options.tire.li);
                      $('.modal-product-info .product-si').html(data.cart.options.tire.si);
                      $('.cart-content .cart-products-total').html(total_sum);
                      $('.modal-product-info .product-qty').html($('.modal-product-info .product-qty').attr('data-qty')).attr('data-qty', parseInt(data.quantity));
                      $('span.cart-products-count').html('(' + cart_quantity + ')');
                      $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
                      $('.blockcart.cart-preview .header').empty();
                      $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
                    }

                  }
                });
              } else {
                // IF ADMIN
                const tire_data = $(this).parent().parent().parent();
                // console.log('tire_data: ', tire_data);
                let article = $('.table-tire-name-cell a', tire_data).data('article');
                if (article.length == 0) article = 'no_article';
                $('.popup input[name=prod]').val($('.table-tire-name-cell a', tire_data).data('content'));
                $('.popup input[name=price]').val($('.tire-price-red', tire_data).html().replace('€ ', ''));
                $('.popup input[name=qty]').val($('.table-tire-name-cell a', tire_data).data('quantity'));
                $('.popup input[name=total]').val(parseInt($('.tire-price-red', tire_data).html().replace('€ ', '')) * $('.popup input[name=qty]').val());
                $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
                $('.popup input[name=article]').val($('.table-tire-name-cell a', tire_data).data('article'));

                calcData = {
                  'article': article,
                  'qty': $('.table-tire-name-cell a', tire_data).data('quantity'),
                  'user': user,
                  'prod': $('.table-tire-name-cell a', tire_data).data('content'),
                  'price': $('.tire-price-red', tire_data).html().replace('€', ''),
                }

                addEntry(calcData);

                const urlData = new URLSearchParams(calcData).toString();

                popCalc('/testing3',1200,750);


              }

          })
        });

        $('.tire-table-checkbox').each(function(){
          if($(this).is(':checked')){
            $('input#show-selected-checkbox').prop( "disabled", false );
          }
        })

        $('.loading-block-content').fadeOut();
      }
    });

    newUrl = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['type'] + window['selected_tires'] + window['show_selected'] + window['page'];

    if (pageLoaded === 1) {
      history.pushState({ prevUrl: document.referrer }, '', newUrl);
    }

    pageLoaded = 1;
  }

  function getURLParameters(url) {
    let params = {};
    let urlParts = url.split('?');
    if (urlParts.length > 1) {
      let paramString = urlParts[1];
      let paramPairs = paramString.split('&');
      paramPairs.forEach(pair => {
        let [key, value] = pair.split('=');
        params[key] = value;
      });
    }
    return params;
  }

  window.onpopstate = function (event) {

    pageLoaded = 0;
    // Call your function to load items (assuming this is what loadItems() does)
    loadItems();
  };

  loadItems();
});
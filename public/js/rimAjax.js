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
  let newUrl;
  let rims_array = [];
  window['table_type'] = '&table_type=list';
  window['selected_rims'] = ($.urlParam('selected') !== null) ? '&selected=' + $.urlParam('selected') : '';
  window['show_selected'] = ($.urlParam('show_selected') !== null) ? '&show_selected=' + $.urlParam('show_selected') : '';
  window['page'] = ($.urlParam('page') !== null) ? '&page=' + $.urlParam('page') : '';
  let pageNr = 1;
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

  window['skr'] = 5;
  window['pcd'] = 112;
  window['d3'] = 16;
  window['wid'] = 6;
  window['wid2'] = 8;
  window['et'] = 0;
  window['et2'] = 0;
  window['rim_center'] = 'Visi';

  $('.filter-button').on('click', function(e) {
    e.preventDefault();
    $('#search_filters #show-selected-checkbox').attr('checked', false).prop('checked', false).next().children().css('display', 'none');
    window['selected_rims'] = '';
    window['show_selected'] = '';
    window['page'] = '';
    selectedSize = true;
    pageNr = 1;
    loadItems();
  });

  function handlePaginationClick(pageId) {
    // Update the current page
    pageNr = pageId;
    window['selected_tires'] = '';
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

  $('#search_filters #show-selected-checkbox').on('click', function(e) {
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

    let skr = $('select.select-rim-lugs option:selected').val();
    let pcd = $('select.select-rim-spread option:selected').val();
    let d3 = $('select.select-rim-diameter option:selected').val();
    let wid = $('select.select-rim-width option:selected').val();
    let wid2 = $('select.select-rim-width2 option:selected').val();
    let et = $('select.select-rim-offset option:selected').val();
    let et2 = $('select.select-rim-offset2 option:selected').val();
    let rim_center = $('select.select-rim-center option:selected').val();

    if (skr != window['skr']) window['skr'] = skr;
    if (pcd != window['pcd']) window['pcd'] = pcd;
    if (d3 != window['d3']) window['d3'] = d3;
    if (wid != window['wid']) window['wid'] = wid;
    if (wid2 != window['wid2']) window['wid2'] = wid2;
    if (et != window['et']) window['et'] = et;
    if (et2 != window['et2']) window['et2'] = et2;
    if (rim_center != window['rim_center']) window['rim_center'] = rim_center;

    let url = '/api/rims/auto?' + 'currentSkr=' + window['skr'] + '&currentPcd=' + window['pcd'] + '&currentDia=' + window['d3'] + '&currentWid=' + window['wid'] + '&currentWid2=' + window['wid2'] + '&currentEt=' + window['et'] + '&currentEt2=' + window['et2'] + '&currentCenter=' + window['rim_center'] + window['selected_rims'] + window['show_selected'] + window['table_type'] + window['page'];

    if (pageLoaded === 0) {
      url = '/api/rims/auto?' + window['table_type'];
    }

    let shopping_button_count = 0;

    $.ajax({
      url: url,
      method: 'GET',
      success: function(data) {
        $('#js-product-list .products').html(data);

        if (window['selected_rims'].length > 0) {
          rims_array = window['selected_rims'].replace('&selected=', '').split(',');
          $(document).find('th.tire-table-checkbox').children().each(function() {
            $(this).removeAttr('checked').removeProp('checked');
            $(this).parent().parent().removeClass('selected');
          });
          $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]').each(function() {
            $(this).removeAttr('checked').removeProp('checked');
            $(this).parent().parent().parent().removeClass('selected');
          });
          $.map(rims_array, function(value, index) {
            $('.tire-table-row .tire-table-checkbox input[value="' + value + '"]').attr('checked', true).prop('checked', true).parent().parent().toggleClass('selected');
            $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox][value="' + value + '"]').attr('checked', true).prop('checked', true).parent().parent().parent().toggleClass('selected');
          });
        }

        $('.rims-sorter').each(function() {
          $(this).tablesorter({
              headers: {
                0: {sorter: false},
                1: {sorter: false},
                2: {sorter: false},
                3: {sorter: false},
                4: {sorter: false},
                5: {sorter: false},
                6: {sorter: false},
                7: {sorter: true},
                8: {sorter: true},
                9: {sorter: false},
                10: {sorter: false},
                11: {sorter: true},
              },
            }
          );
        });

        $($('.pagination-col')).insertAfter($('#tires-table:last-child'));

        // Rādīt izvēlētos start
        $(document).find('th.tire-table-checkbox').children().on('click', function() {
          rims_array = [];
          $(this).parent().parent().toggleClass('selected');
          $(document).find('th.tire-table-checkbox').children(':checked').each(function() {
            rims_array.push($(this).val());
          });

          if (rims_array.length > 0) {
            $('#show-selected-checkbox').removeAttr('disabled').prop('disabled', false);
            window['selected_rims'] = '&selected=' + rims_array.join(',');
          } else {
            $('#show-selected-checkbox').attr('disabled', true).prop('disabled', true);
            window['selected_rims'] = '';
          }
          newUrl = '/lietie-diski/search?' + 'currentSkr=' + window['skr'] + '&currentPcd=' + window['pcd'] + '&currentDia=' + window['d3'] + '&currentWid=' + window['wid'] + '&currentWid2=' + window['wid2'] + '&currentEt=' + window['et'] + '&currentEt2=' + window['et2'] + '&currentCenter=' + window['rim_center'] + window['selected_rims'] + window['show_selected'] + window['table_type'] + window['page'];

          if (pageLoaded === 1) {
            history.pushState({ prevUrl: document.referrer }, '', newUrl);
          }
        });
        // Rādīt izvēlētos end

        $(document).find('.tire-table-checkbox').children().each(function(key, value){

          // ON SHOPPING CART BUTTON CLICK
          $(this).parent().parent().find('.cart-shopping-button').on('click', function() {

            if (!admin) {
              const rim_id = $(this).data('info');

              let ajaxUrl = '/lietie-diski';

              let tire_name = $(this).parent().parent().parent().find('.table-tire-name-cell');
              if (tire_name.attr('data-link')) {
                ajaxUrl = tire_name.attr('data-link');
              }

              $.ajax({
                url: ajaxUrl + '/ajax',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                data: { rim_id: rim_id },
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

                  // TIRE IMAGE INSIDE MODAL
                  $('.modal-product-info .product-name').html(data.cart.name);
                  if (data.cart.options.tire.price1 != null) {
                    $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price1)).attr('data-price', parseInt(data.cart.options.tire.price1));
                  } else {
                    $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price3)).attr('data-price', parseInt(data.cart.options.tire.price3));
                  }
                  $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
                  $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
                  $('.cart-content .cart-products-total').html(total_sum);
                  $('.modal-product-info .product-qty').html($('.modal-product-info .product-qty').attr('data-qty')).attr('data-qty', parseInt(data.quantity));
                  $('span.cart-products-count').html('(' + cart_quantity + ')');
                  $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
                  $('.blockcart.cart-preview .header').empty();
                  $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');

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
      },
      complete() {
        let perfEntries = performance.getEntriesByType('navigation');
        if (perfEntries.length > 0) {
          const latestNavigation = perfEntries[0];
          if (latestNavigation.type === 'reload') {
            return false;
          }
        }
      }
    });


    newUrl = '/lietie-diski/search?' + 'currentSkr=' + window['skr'] + '&currentPcd=' + window['pcd'] + '&currentDia=' + window['d3'] + '&currentWid=' + window['wid'] + '&currentWid2=' + window['wid2'] + '&currentEt=' + window['et'] + '&currentEt2=' + window['et2'] + '&currentCenter=' + window['rim_center'] + window['selected_rims'] + window['show_selected'] + window['table_type'] + window['page'];

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
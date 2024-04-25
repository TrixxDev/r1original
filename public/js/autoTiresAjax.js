$(document).ready(function() {

  $.urlParam = function(name){
    let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null) {
      return null;
    }
    return decodeURI(results[1]) || 0;
  }

  function unique(array) {
    return $.grep(array, function(el, index) {
      return index === $.inArray(el, array);
    });
  }

  function addEntry(item) {
    // Parse the JSON stored in allEntries
    let existingEntries = JSON.parse(localStorage.getItem("allEntries"));
    if (existingEntries == null) existingEntries = [];

    // Check if the entry already exists
    // (Modify the criteria based on what constitutes a duplicate)
    const isDuplicate = existingEntries.some(entry =>
      entry.article === item.article &&
      entry.user === item.user
    );

    if (isDuplicate) {
      return false; // Entry already exists
    }

    // If not a duplicate, proceed with adding the entry
    let entry = {
      "article": item.article,
      "qty": item.qty,
      "user": item.user,
      "prod": item.prod,
      "price": item.price,
    };

    existingEntries.push(entry);
    localStorage.setItem("allEntries", JSON.stringify(existingEntries));

    return true; // If you want to return true on successful addition
  }

  $('.loading-block-content').fadeIn();

  const pathParts = window.location.pathname.split('/');
  let season;
  window['fastsearch'] = '';
  window['fastsearchInput'] = '';
  window['newUrl'] = '';
  window['availability'] = ($.urlParam('availability') !== null) ? '&availability=' + $.urlParam('availability') : '';
  window['code'] = ($.urlParam('code') !== null) ? '&code=' + $.urlParam('code') : '';
  window['type'] = ($.urlParam('type') !== null) ? '&type=' + $.urlParam('type') : '';
  window['fuelEco'] = ($.urlParam('fuel') !== null) ? '&fuel=' + $.urlParam('fuel') : '';
  window['wetRoad'] = ($.urlParam('wet') !== null) ? '&wet=' + $.urlParam('wet') : '';
  window['noise'] = ($.urlParam('noise') !== null) ? '&noise=' + $.urlParam('noise') : '';
  window['selected_tires'] = ($.urlParam('selected') !== null) ? '&selected=' + $.urlParam('selected') : '';
  window['table_type'] = '&table_type=list';
  window['top_enabled'] = ($.urlParam('top') !== null) ? '&top=' + $.urlParam('top') : '';
  let pageNr = 1;
  window['page'] = ($.urlParam('page') !== null) ? '&page=' + $.urlParam('page') : '';
  let pageLoaded;
  let tires_array = [];
  window['show_selected'] = ($.urlParam('show_selected') !== null) ? '&show_selected=' + $.urlParam('show_selected') : '';
  let clickedTop = ($.urlParam('top') !== null);

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
  let selectedSize = true;
  window['d1'] = 205;
  window['d2'] = 55;
  window['d3'] = 16;

  $('.filter-button').on('click', function(e) {
    e.preventDefault();
    window['availability'] = '';
    window['code'] = '';
    window['type'] = '';
    window['fuelEco'] = '';
    window['wetRoad'] = '';
    window['noise'] = '';
    $('#search_filters #show-selected-checkbox').attr('checked', false).prop('checked', false).next().children().css('display', 'none');
    window['selected_tires'] = '';
    window['show_selected'] = '';
    window['fastsearch'] = '';
    window['fastsearchInput'] = '';
    $('.custom-checkbox input').not(':first').removeAttr('checked').prop('checked', false);
    window['page'] = '';
    selectedSize = true;
    pageNr = 1;
    loadItems();
    if (window['top_enabled'].length <= 0) {
      if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('.mobile-filter-modal #search_filters #show-top-checkbox').trigger('click').attr('checked', true).prop('checked', true);
      } else {
        $('#search_filters_wrapper #search_filters #show-top-checkbox').trigger('click').attr('checked', true).prop('checked', true);
      }
    }
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
    window['code'] = '';
    window['type'] = '';
    window['fuelEco'] = '';
    window['wetRoad'] = '';
    window['noise'] = '';
    $('#search_filters #show-selected-checkbox').attr('checked', false).prop('checked', false).next().children().css('display', 'none');
    window['selected_tires'] = '';
    window['show_selected'] = '';
    $('.custom-checkbox input').not(':first').removeAttr('checked').prop('checked', false);
    window['fastsearchInput'] = $(this).val();
    window['fastsearch'] = '&fastsearch=' + window['fastsearchInput'];
    window['page'] = '';
    selectedSize = true;
    pageNr = 1;
    if ($(this).val().length > 0) {
      loadItems();
      if (window['top_enabled'].length <= 0) {
        if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
          $('.mobile-filter-modal #search_filters #show-top-checkbox').trigger('click').attr('checked', true).prop('checked', true);
        } else {
          $('#search_filters_wrapper #search_filters #show-top-checkbox').trigger('click').attr('checked', true).prop('checked', true);
        }
      }
    }
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

  $('ul#facet_availability li label .custom-checkbox input').on('change', function() {
    let selectedColors = [];

    // Check which color checkboxes are checked and add them to the selectedColors array
    if ($('ul#facet_availability input.green').is(':checked')) {
      selectedColors.push('green');
    }
    if ($('ul#facet_availability input.yellow').is(':checked')) {
      selectedColors.push('yellow');
    }
    if ($('ul#facet_availability input.red').is(':checked')) {
      selectedColors.push('red');
    }

    // Build the availability parameter based on selected colors
    window['availability'] = selectedColors.length > 0 ? '&availability=' + selectedColors.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_code li label .custom-checkbox input').on('change', function() {
    let selectedCodes = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_code input[data-for="prod-code"]:checked').each(function() {
      selectedCodes.push($(this).val());
    });

    // Build a parameter string based on selected values
    window['code'] = selectedCodes.length > 0 ? '&code=' + selectedCodes.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_type li label .custom-checkbox input').on('change', function() {
    let selectedTypes = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_type input[data-for="prod-type"]:checked').each(function() {
      selectedTypes.push($(this).val());
    });

    // Build a parameter string based on selected values
    window['type'] = selectedTypes.length > 0 ? '&type=' + selectedTypes.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_fuel_eco li label .custom-checkbox input').on('change', function() {
    let selectedFuels = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_fuel_eco input[data-for="fuel_efficiency"]:checked').each(function() {
      selectedFuels.push($(this).val());
    });

    // Build a parameter string based on selected values
    window['fuelEco'] = selectedFuels.length > 0 ? '&fuel=' + selectedFuels.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_wet li label .custom-checkbox input').on('change', function() {
    let selectedWet = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_wet input[data-for="wet_grip"]:checked').each(function() {
      selectedWet.push($(this).val());
    });

    // Build a parameter string based on selected values
    window['wetRoad'] = selectedWet.length > 0 ? '&wet=' + selectedWet.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('ul#facet_noise li label .custom-checkbox input').on('change', function() {
    let selectedNoises = [];

    // Find all checked checkboxes with the class "custom-checkbox" and collect their values
    $('ul#facet_noise input[data-for="noise"]:checked').each(function() {
      selectedNoises.push($(this).val());
    });

    // Build a parameter string based on selected values
    window['noise'] = selectedNoises.length > 0 ? '&noise=' + selectedNoises.join('+') : '';

    window['page'] = '&page=1';
    pageNr = 1;
    loadItems();
  });

  $('#search_filters #show-top-checkbox').on('click', function(e) {
    e.preventDefault();

    $(this).attr('checked', function(i, value) {
      if (value === undefined) {
        window['top_enabled'] = '&top=show';
        $(this).next().children().css('display', 'block');
        return 'checked';
      } else {
        window['top_enabled'] = '';
        $(this).next().children().css('display', 'none');
        return null;
      }
    });

    loadItems();
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

  $('.season-select .winter-tires-link').on('click', function (e) {
    e.preventDefault();
    window.location.href = '/ziemas-riepas/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['page'];
  });

  $('.season-select .summer-tires-link').on('click', function (e) {
    e.preventDefault();
    window.location.href = '/vasaras-riepas/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['page'];
  })

  function loadItems()
  {

    let tire_availability = [];
    let codes = [];
    let tire_types = [];
    let tire_fuels = [];
    let tire_wets = [];
    let tire_noises = [];

    $('#show-top-checkbox').removeAttr('disabled').prop('disabled', false);
    $('.loading-block-content').fadeIn();
    season = (pathParts[1] === 'vasaras-riepas') ? 1 : 2;

    brand = $('select.tire-brand option:selected').val();
    let tire_width = $('select.tire-width option:selected').val();
    let tire_height = $('select.tire-height option:selected').val();
    let tire_radius = $('select.tire-radius option:selected').val();

    if (tire_width != window['d1']) window['d1'] = tire_width;
    if (tire_height != window['d2']) window['d2'] = tire_height;
    if (tire_radius != window['d3']) window['d3'] = tire_radius;

    if (fastsearchInput.length > 0) {
      $.get('/api/tires/autoSplitInput/' + fastsearchInput, '', function(data) {
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

    brand = (brand === 'Ražotājs') ? '' : 'brand=' + brand + '&';

    let url = '/api/tires/auto/' + season + '?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['fastsearch'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['table_type'] + window['page'];

    if (pageLoaded === 0) {
      url = '/api/tires/auto/' + season + '?' + brand + window['fastsearch'] + location.search.slice(1) + window['table_type'] + window['top_enabled'];
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

        if ($('.category-vasaras-riepas').length >= 1){
          $('.summer-sorter').each(function() {
            $(this).tablesorter({
                headers: {
                  0: {sorter: false},
                  1: {sorter: true},
                  2: {sorter: false},
                  3: {sorter: false},
                  4: {sorter: false},
                  5: {sorter: false},
                  6: {sorter: false},
                  9: {sorter: false},
                  10: {sorter: false},
                  11: {sorter: false},
                  12: {sorter: true}
                },
              }
            );
          });
        } else if ($('.category-ziemas-riepas').length >= 1){
          $('.summer-sorter').each(function() {
            $(this).tablesorter({
                headers: {
                  0: {sorter: false},
                  1: {sorter: true},
                  2: {sorter: false},
                  3: {sorter: false},
                  4: {sorter: false},
                  5: {sorter: false},
                  6: {sorter: false},
                  7: {sorter: false},
                  8: {sorter: true},
                  9: {sorter: true},
                  10: {sorter: false},
                  11: {sorter: false},
                  12: {sorter: true}
                },
              }
            );
          });
        }

        $($('.pagination-col')).insertAfter($('#tires-table:last-child'));

          // Rādīt izvēlētos (saraksts) start
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
            window['newUrl'] = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['page'];

            if (pageLoaded === 1) {
              history.pushState({ prevUrl: document.referrer }, '', window['newUrl']);
            }
          });
          // Rādīt izvēlētos (saraksts) end

          // Rādīt izvēlētos (saraksts) start
          if ($('#js-product-list .mobile-tire-container').is(':visible')) {
            $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]').on('click', function() {
              tires_array = [];
              $(this).parent().parent().parent().toggleClass('selected');
              $(document).find('#js-product-list .mobile-tire-container .tire-list-caption input[type=checkbox]:checked').each(function() {
                tires_array.push($(this).val());
              });

              if (tires_array.length > 0) {
                $('#show-selected-checkbox').removeAttr('disabled').prop('disabled', false);
                window['selected_tires'] = '&selected=' + tires_array.join(',');
              } else {
                $('#show-selected-checkbox').attr('disabled', true).prop('disabled', true);
                window['selected_tires'] = '';
              }
              window['newUrl'] = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['page'];

              if (pageLoaded === 1) {
                history.pushState({ prevUrl: document.referrer }, '', window['newUrl']);
              }
            });

            $('#js-product-list .mobile-tire-container').children().each(function(){

              let tire_codes = $(this).find('.tire-image-code').text();
              if (tire_codes.includes('ACOUSTIC') || tire_codes.includes('NCS') || tire_codes.includes('SCT')) {
                codes.push('SOUND');
              }
              if (tire_codes.includes('HL')) {
                codes.push('XL');
              }

              tire_availability.push($(this).find('.grid-dot.green, .grid-dot.yellow, .grid-dot.red').data('color'));
              codes.push(tire_codes);
              tire_types.push($(this).find('.type-explain').data('type'));
              tire_fuels.push($(this).find('.fuel-explain').text());
              tire_wets.push($(this).find('.wet-explain').text());
              tire_noises.push($(this).find('.noise-explain').text().charAt(0));

              // ON SHOPPING CART BUTTON CLICK
              $(this).parent().parent().find('.cart-shopping-button').on('click', function() {

                if (!admin) {
                  const tire_id = $(this).data('info');

                  let ajaxUrl = (season == 1) ? '/vasaras-riepas' : '/ziemas-riepas';

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
          }

          $(document).find('.tire-table-checkbox').children().each(function(key, value){

            let tire_codes = $(this).parent().parent().find('.code-explain').text();
            if (tire_codes.includes('ACOUSTIC') || tire_codes.includes('NCS') || tire_codes.includes('SCT')) {
              codes.push('SOUND');
            }
            if (tire_codes.includes('HL')) {
              codes.push('XL');
            }

            tire_availability.push($(this).parent().parent().find('.dot.green, .dot.yellow, .dot.red').data('color'));
            codes.push(tire_codes);
            tire_types.push($(this).parent().parent().find('.type-explain').data('type'));
            tire_fuels.push($(this).parent().parent().find('.fuel-explain').text());
            tire_wets.push($(this).parent().parent().find('.wet-explain').text());
            tire_noises.push($(this).parent().parent().find('.noise-explain').text().charAt(0));

            // ON SHOPPING CART BUTTON CLICK
            $(this).parent().parent().find('.cart-shopping-button').on('click', function() {

              if (!admin) {
                const tire_id = $(this).data('info');

                let ajaxUrl = (season == 1) ? '/vasaras-riepas' : '/ziemas-riepas';

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

        let uniqueAvailabilityArray = unique(tire_availability);
        let uniqueCodeArray = unique(codes);
        let uniqueTypeArray = unique(tire_types);
        let uniqueFuelArray = unique(tire_fuels);
        let uniqueWetArray = unique(tire_wets);
        let uniqueNoiseArray = unique(tire_noises);

        if (selectedSize === true) {
          $('ul#facet_availability .custom-checkbox input').attr('disabled', true).prop('disabled', true);
          $('ul#facet_code .custom-checkbox input').attr('disabled', true).prop('disabled', true);
          $('ul#facet_type .custom-checkbox input').attr('disabled', true).prop('disabled', true);
          $('ul#facet_fuel_eco .custom-checkbox input').attr('disabled', true).prop('disabled', true);
          $('ul#facet_wet .custom-checkbox input').attr('disabled', true).prop('disabled', true);
          $('ul#facet_noise .custom-checkbox input').attr('disabled', true).prop('disabled', true);

          $.each(uniqueAvailabilityArray, function(index, value) {
            $('ul#facet_availability li').each(function() {
              $(this).find('input[data-value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
            })
          });

          $.each(uniqueCodeArray, function(index, value) {
            let text = value.split(' ');
            $.each(text, function(index, value) {
              $('ul#facet_code li').each(function() {
                $(this).find('input[value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
              })
            });
          });

          $.each(uniqueTypeArray, function(index, value) {
            $('ul#facet_type li').each(function() {
              $(this).find('input[value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
            })
          });

          $.each(uniqueFuelArray, function(index, value) {
            $('ul#facet_fuel_eco li').each(function() {
              $(this).find('input[value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
            })
          });

          $.each(uniqueWetArray, function(index, value) {
            $('ul#facet_wet li').each(function() {
              $(this).find('input[value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
            })
          });

          $.each(uniqueNoiseArray, function(index, value) {
            $('ul#facet_noise li').each(function() {
              $(this).find('input[value="' + value + '"]').removeAttr('disabled').prop('disabled', false);
            })
          });

          $('ul#facet_availability .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          $('ul#facet_code .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          $('ul#facet_type .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          $('ul#facet_fuel_eco .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          $('ul#facet_wet .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          $('ul#facet_noise .custom-checkbox input:checked').removeAttr('disabled').prop('disabled', false);
          selectedSize = false;
        }

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

        if (clickedTop === false) {
          if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $('.mobile-filter-modal #search_filters #show-top-checkbox').trigger('click');
          } else {
            $('#search_filters_wrapper #search_filters #show-top-checkbox').trigger('click');
          }
          clickedTop = true;
        }
      }
    });

    const full = location.protocol + '//' + location.host;

    // $('.season-select-link').each(function() {
    //
    //   let link = new URL(full + '/' + $(this).attr('href').split('/')[1] + '/search?');
    //   let searchParams = new URLSearchParams({ d1: window['d1'], d2: window['d2'], d3: window['d3'] }).toString();
    //
    //   let fullLink = link.href + searchParams;
    //
    //   $(this).attr('href', $(this).attr('href'));
    //
    // });

    window['newUrl'] = '/' + pathParts[1] + '/search?' + brand + 'd1=' + window['d1'] + '&d2=' + window['d2'] + '&d3=' + window['d3'] + window['availability'] + window['code'] + window['type'] + window['fuelEco'] + window['wetRoad'] + window['noise'] + window['selected_tires'] + window['show_selected'] + window['top_enabled'] + window['page'];

    if (pageLoaded === 1) {
      history.pushState({ prevUrl: document.referrer }, '', window['newUrl']);
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

    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      if (clickedTop) {
        $('.mobile-filter-modal #search_filters #show-top-checkbox').attr('checked', true).prop('checked', true);
      } else {
        $('.mobile-filter-modal #search_filters #show-top-checkbox').removeAttr('checked').removeProp('checked');
      }
    } else {
      if (clickedTop) {
        $('#search_filters_wrapper #search_filters #show-top-checkbox').attr('checked', true).prop('checked', true);
      } else {
        $('#search_filters_wrapper #search_filters #show-top-checkbox').removeAttr('checked').removeProp('checked');
      }
    }

    pageLoaded = 0;
    // Call your function to load items (assuming this is what loadItems() does)
    loadItems();
  };

  loadItems();
});
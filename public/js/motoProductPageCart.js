$(document).ready(function() {
  function getSelectedTreadIds() {
    const ids = [];
    $('#tires-table-body input[name="product_ids[]"]:checked').each(function() {
      ids.push($(this).val());
    });
    return ids;
  }

  function buildTreadPageUrl(selectedIds, showSelectedFilter) {
    const params = new URLSearchParams(window.location.search);

    if (selectedIds.length > 0) {
      params.set('selected', selectedIds.join(','));
    } else {
      params.delete('selected');
      params.delete('show_selected');
    }

    if (showSelectedFilter) {
      params.set('show_selected', 'show');
    } else {
      params.delete('show_selected');
    }

    const qs = params.toString();
    return qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
  }

  function updateTreadUrl(options) {
    const replace = !!(options && options.replace);
    const selectedIds = getSelectedTreadIds();
    const showSelectedFilter = $('#show-selected-tread-checkbox').is(':checked');
    const newUrl = buildTreadPageUrl(selectedIds, showSelectedFilter);
    const currentUrl = window.location.pathname + window.location.search;

    if (newUrl === currentUrl) {
      return;
    }

    const state = { treadSelected: selectedIds };
    if (replace) {
      history.replaceState(state, '', newUrl);
    } else {
      history.pushState(state, '', newUrl);
    }
  }

  function syncTreadShowSelectedFilter() {
    const selectedCount = $('#tires-table-body input[name="product_ids[]"]:checked').length;
    const $filter = $('#show-selected-tread-checkbox');

    if (!$filter.length) {
      return;
    }

    if (selectedCount > 0) {
      $filter.removeAttr('disabled').prop('disabled', false);
    } else {
      $filter.attr('disabled', true).prop('disabled', true);
      $filter.prop('checked', false);
      $('#tires-table-body .tire-table-row').show();
      updateTreadUrl({ replace: true });
    }
  }

  function applyTreadShowSelectedFilter(showOnlySelected) {
    $('#tires-table-body .tire-table-row').each(function() {
      const isSelected = $(this).find('input[name="product_ids[]"]').is(':checked');
      $(this).toggle(!showOnlySelected || isSelected);
    });
  }

  if ($('#show-selected-tread-checkbox').length) {
    syncTreadShowSelectedFilter();

    const treadUrlParams = new URLSearchParams(window.location.search);
    if (treadUrlParams.get('show_selected')) {
      const $filter = $('#show-selected-tread-checkbox');
      $filter.removeAttr('disabled').prop('disabled', false);
      $filter.prop('checked', true);
      applyTreadShowSelectedFilter(true);
    }

    $(document).on('click', 'th.tread-tire-table-checkbox input[name="product_ids[]"]', function() {
      $(this).closest('.tire-table-row').toggleClass('selected', this.checked);
      syncTreadShowSelectedFilter();
      if ($('#show-selected-tread-checkbox').is(':checked')) {
        applyTreadShowSelectedFilter(true);
      }
      updateTreadUrl();
    });

    $(document).on('change', '#show-selected-tread-checkbox', function() {
      if (this.disabled) {
        return;
      }
      applyTreadShowSelectedFilter(this.checked);
      updateTreadUrl();
    });
  }

  function normalizeCartImageUrl(image) {
    let imageUrl = image || '/img/p/en-default-home_default.jpg';
    if (String(imageUrl).charAt(0) !== '/' && String(imageUrl).indexOf('http') !== 0) {
      imageUrl = '/' + String(imageUrl).replace(/^\\+/, '');
    }
    return imageUrl;
  }

  function updateBlockcartModal(tire_id, data) {
    const cart_quantity = parseInt(data.quantity, 10);
    const total_sum = parseInt(data.total_sum, 10);
    const tire = data.cart.products[tire_id];
    if (!tire) {
      return;
    }

    $('.modal-image-preview img').attr('src', normalizeCartImageUrl(tire.image));
    $('.modal-product-info .product-name').html(tire.name);
    $('.modal-product-info .product-price').html(parseInt(tire.price, 10)).attr('data-price', parseInt(tire.price, 10));
    $('.modal-product-info .product-width').html(tire.d1);
    $('.modal-product-info .product-height').html(tire.d2);
    $('.modal-product-info .product-radius').html(tire.d3);
    $('.modal-product-info .product-type').html(tire.type);
    $('.modal-product-info .product-li').html(tire.li);
    $('.modal-product-info .product-si').html(tire.si);
    $('.cart-content .cart-products-total').html(total_sum);
    $('.modal-product-info .product-qty').html(tire.quantity).attr('data-qty', tire.quantity);
    $('span.cart-products-count').html('(' + cart_quantity + ')');
    $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
    $('.blockcart.cart-preview .header').empty();
    $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
  }

  function addMotoTireToCart(tire_id, tire_url, quantity) {
    $.ajax({
      url: '/motociklu-riepas/ajax',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      method: 'POST',
      data: {tire_id: tire_id, tire_url: tire_url, quantity: quantity},
      success: function(data) {
        if (data.error) {
          console.error(data.error);
          return;
        }
        updateBlockcartModal(tire_id, data);
      },
      error: function(xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Cart error';
        console.error(message);
      }
    });
  }

  // Кнопка корзины в таблице размеров на странице протектора
  $(document).on('click', '.cart-shopping-button', function() {
    const isAdmin = (typeof admin !== 'undefined' && admin);
    if (!isAdmin) {
      const tire_id = $(this).data('info');
      const tire_url = $(this).data('url') || window.location.href;
      const quantity = parseInt($(this).closest('tr').find('.tire-info').data('quantity'), 10) || 1;
      addMotoTireToCart(tire_id, tire_url, quantity);
      return;
    }

    const $row = $(this).closest('tr');
    const $info = $row.find('.tire-info');
    let article = ($info.data('article') || '').toString().trim();
    let prod = ($info.data('content') || '').toString().trim();
    const qty = parseInt($info.data('quantity'), 10) || 1;
    const price = ($row.find('.sale-price, .tire-price-red').text() || '').replace(/[^\d.,]/g, '').replace(',', '.');
    const currentUser = (typeof user !== 'undefined' ? user : '');

    if (!article) article = 'no_article';
    if (!prod) prod = article;

    const calcData = {
      article: article,
      qty: qty,
      user: currentUser,
      prod: prod,
      price: price
    };

    if (typeof addEntry === 'function') {
      addEntry(calcData);
    }
    if (typeof popCalc === 'function') {
      popCalc('/testing3', 1200, 750);
    }
  });

  // Обработчик кнопки "Pirkt" на странице товара для мотоциклетных шин
  $(document).on('click', '.add-to-cart', function() {
    const isAdmin = (typeof admin !== 'undefined' && admin);
    if (!isAdmin) {
      const tire_id = $(this).data('info');
      const quantity = parseInt($('#quantity_wanted').val(), 10) || 1;
      const tire_url = window.location.href;
      addMotoTireToCart(tire_id, tire_url, quantity);
      return;
    }

    // ADMIN: добавляем в админ-корзину (localStorage allEntries) и открываем quick order
    const qty = parseInt($('#quantity_wanted').val(), 10) || 1;
    const article =
      ($('input.tire_article').val() || $('input[name="article"].tire_article').val() || '').toString().trim() ||
      'no_article';
    const prod =
      ($('input.tire_title').val() || $('input[name="title"].tire_title').val() || $('h1[itemprop="name"]').text() || '').toString().trim() ||
      article;

    const priceFromContent = $('[itemprop="price"]').attr('content');
    const priceFromText = $('[itemprop="price"]').text();
    const price = (priceFromContent || priceFromText || '').toString().replace(/[^\d.,]/g, '').replace(',', '.');

    const currentUser = (typeof user !== 'undefined' ? user : '');
    const calcData = {
      article: article,
      qty: qty,
      user: currentUser,
      prod: prod,
      price: price
    };

    if (typeof addEntry === 'function') {
      addEntry(calcData);
    }
    if (typeof popCalc === 'function') {
      popCalc('/testing3', 1200, 750);
    }
  });
});



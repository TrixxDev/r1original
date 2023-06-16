<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{!! csrf_token() !!}">
  <title>Ātrais pasūtījums</title>
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css?rev=' . time()) }}" type="text/css" media="all">
  <link rel="stylesheet" href="{{ asset('css/jquery.ui.theme.min.css?rev=' . time()) }}" type="text/css" media="all">
  <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

</head>
<body>

<div class="popup" id="quick-popup" data-popup="popup-3" style="display: block;">
  <div class="popup-inner">
    <div class="busy_bgr"><div class="busy_img"></div></div>
    <form id="quick-buy-form">
      @csrf
      <input type="hidden" name="article" value="">
      <div class="location-wraper">
        <div class="radio-field">
          <input id="loc_URS" type="radio" name="location" value="URS" checked="">
          <label for="loc_URS">
            URS
{{--            <span id="urs_quantity">--}}
{{--              <div class="spinner-border text-dark" role="status">--}}
{{--                <span class="sr-only">Loading...</span>--}}
{{--              </div>--}}
{{--            </span>--}}
          </label>
        </div>
        <div class="radio-field">
          <input id="loc_KRS" type="radio" name="location" value="KRS">
          <label for="loc_KRS">
            KRS
{{--            <span id="krs_quantity">--}}
{{--              <div class="spinner-border text-dark" role="status">--}}
{{--                <span class="sr-only">Loading...</span>--}}
{{--              </div>--}}
{{--            </span>--}}
          </label>
        </div>
        @if (isset($links) && count($links) > 0)
          @foreach ($links as $name => $opts)
          <div class="radio-field">
            <a href="{{ $opts['link'] }}" target="_blank">{{ $name }} ({{ $opts['remaining'] }})</a>
          </div>
          @endforeach
        @endif
      </div>
      <div class="inserthere">

      </div>
      <div class="bottom-long-fields">
        <span style="margin-left: 54px;">Montāža</span>
        <input type="checkbox" id="montage" onchange="toggleMontage()" name="montage" value="1"><label for="montage"></label>
        <input type="text" name="total" placeholder="Summa" value="" readonly="">
        <label for="total">Summa:</label>
        <input type="text" placeholder="Cena" name="price_montage" onkeyup="addMontagePrice()" disabled="">
      </div>
      <div class="bottom-long-fields">
        <span style="margin-left: 54px;">Glabāšana</span>
        <input type="checkbox" id="safe" onchange="toggleSafe()" name="safe" value="1"><label for="safe"></label>
        <input style="width: 100px;" type="text" placeholder="Cena" name="price_safe" onkeyup="addSafePrice()" disabled="">
      </div>
      <div class="bottom-long-fields">
        <span style="margin-left: 118px;">Tel. Numurs</span>
        <input type="checkbox" id="mobile" onchange="toggleMobile()" name="mobile" checked="" value="1"><label for="mobile"></label>
        <input style="width: 100px;" type="text" placeholder="Telefona numurs" name="mobile_number">
      </div>
      <div class="user-fields">
        <input type="text" name="user" placeholder="Lietotājs" value="">
        <textarea type="textarea" name="comments" placeholder="Komentāri"></textarea>
      </div>
      <a style="margin-left: 0;" class="button" onclick="return sendData(getFormData($('#quick-buy-form')));">Apstiprināt</a>
    </form>

    <style>

      .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: -4px;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        -webkit-animation: .75s linear infinite spinner-border;
        animation: .75s linear infinite spinner-border;
      }

      /* POPUPS */

      .popup {
	      z-index: 0!important;
      }

      .popup .location-wraper {
        float: left;
        margin: 0 0 15px -13px;
        width: 120px;
        height: 120px;
      }

      .popup .location-wraper input {
        height: 15px;
        display: inline-block;
        margin-right: 7px;
      }

      .popup .top-long-fields {
        height: 51px !important;
        width: 100% !important;
        display: table-header-group;
      }

      .popup .top-long-fields input {
        float: left;
        margin-left: 10px;
      }

      .popup .top-long-fields input[name="prod"] {
        width: 305px;
        margin-left: 10px;
      }

      span.delete_item {
        float: left;
      }

      span.delete_item img {
        width: 10px;
      }

      .popup .top-long-fields input[name="qty"] {
        width: 37px !important;
        padding: 0;
        padding-left: 5px;
      }

      .popup .top-long-fields > label[for='price'] {
        right: 60px;
      }

      .popup .top-long-fields input[name="price"] {
        width: 85px !important;
      }

      .popup .bottom-long-fields {
        height: 50px;
        width: 547px;
        margin-left: 26px;
      }

      input#montage {
        margin-left: 24px;
      }

      .popup .bottom-long-fields span {
        float: left;
        /* margin: 0 0px; */
        font-size: 20px;
        font-weight: bold;
      }

      .popup .bottom-long-fields input {
        float: left;
        margin-left: 10px;
      }

      .popup .bottom-long-fields input[name="total"] {
        float: right;
        margin-left: 10px;
      }

      .popup .bottom-long-fields input[name="price_montage"] {
        width: 100px;
      }

      .popup .bottom-long-fields input[name="total"] {
        width: 85px;
      }

      .popup .top-long-fields > label {
        font-size: 9pt;
        font-weight: bold;
        position: absolute;
        top: 5px;
      }

      .popup .top-long-fields > label[for='qty'] {
        right: 148px;
      }

      .popup .top-long-fields > label[for='price'] {
        right: 60px;
      }

      .popup label[for='total'] {
        position: relative;
        left: 75px;
        top: 5px;
      }

      #quick-buy-msg {
        display: none;
        font-size: 30px;
        margin-top: 30px;
      }

      .popup .radio-field {
        font-size: .875rem;
      }

      .popup .user-fields {
        margin-top: 15px;
      }

      .popup.msg #quick-buy-form{
        display: none;
      }

      .popup.msg a.button{
        display: none;
      }

      .popup.msg #quick-buy-msg{
        display: block;
      }
    </style>
  </div>
</div>

<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
<script>

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

let $items = JSON.parse(localStorage.getItem('allEntries'));

let $price = [];
let $count = [];
let $article = [];
let $articles = [];
let $user = '';

$.each($items, function(index, item) {
  $("<div class='top-long-fields'>" +
    "<span class='delete_item'><img src='/images/cancel.png' style='width: 10px;'></span>" +
    "<span data-article='" + item.article + "'>" +
    "<input type='text' title='URS' name='urs_quantity' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:default;'>" +
    "<input type='text' title='KRS' name='krs_quantity' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:default;'>" +
    "<input type='text' title='Latakko' name='latakko' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:pointer;'>" +
    "<input type='text' title='Goodyear' name='goodyear' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:pointer;'>" +
    "<input type='text' title='RiepuZona' name='riepuzona' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:pointer;'>" +
    "<input type='text' title='Duell' name='duell' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:pointer;'>" +
    "<input type='text' title='StarCo' name='starco' readonly style='width: 26px!important;padding:0!important;text-align:center;cursor:pointer;'>" +
    "</span>" +
    "<input type='text' placeholder='Prece' name='prod' value='" + item.prod + "' readonly=''>" +
    "<label for='qty'>Sk.</label>" +
    "<input type='number' min='1' placeholder='Daudzums' name='qty' value='" + item.qty + "' onchange='calcQuickBuyPrice()' onkeyup='calcQuickBuyPrice()' style='width: 70px;'>" +
    "<label for='price'>Cena</label>" +
    "<input type='text' placeholder='Cena' name='price' style='width: 80px' value='" + item.price + "' onchange='calcQuickBuyPrice()' onkeyup='calcQuickBuyPrice()'>" +
    "</div>").appendTo('.inserthere');

  $article.push(item.article);
  $price.push(parseInt(item.price) * item.qty);
  $count.push(item.qty);
  $user = item.user;
});

$articles = $article;

$price = $price.reduce((a, b) => a + b, 0);
$count = $count.reduce((a, b) => a + b, 0);

let $globalPrice;

$article = $article.join('$');
$('#quick-buy-form input[name=article]').val($article);
$('.user-fields input[name=user]').val($user);
$('#quick-buy-form input[name=total]').val(isNaN($price) ? '' : $price);
$globalPrice = $price;

$price = [];
$count = [];

function calcQuickBuyPrice(addServices = false){
  $('.inserthere .top-long-fields').each(function(index, value) {
    $price.push(parseInt($('input[name=qty]', value).val()) * $('input[name=price]', value).val());
  });
  $price = $price.reduce((a, b) => a + b, 0);
  if (addServices === true) {
    if ($('#quick-buy-form input[name=price_montage]').val() > 0) {
      $price = $price + parseInt($('#quick-buy-form input[name=price_montage]').val());
    } else if ($('#quick-buy-form input[name=price_safe]').val() > 0) {
      $price = $price + parseInt($('#quick-buy-form input[name=price_safe]').val());
    } else if ($('#quick-buy-form input[name=price_montage]').val() > 0 && $('#quick-buy-form input[name=price_safe]').val() > 0) {
      $price = $price + parseInt($('#quick-buy-form input[name=price_montage]').val()) + parseInt($('#quick-buy-form input[name=price_safe]').val());
    }
  }
  $globalPrice = $price;
  $('#quick-buy-form input[name=total]').val(isNaN($price) ? '' : $price);
  $price = [];
}

$('#quick-buy-form').on('click', '.delete_item', function() {
  let posCount = $('.top-long-fields').length;
  let $title = $(this).parent().children('input[name=prod]').val();
  $(this).parent().remove();
  $.each($items, function(index, value) {
    if (value.prod === $title) {
      delete $items[index];
    }
  });
  let existingEntries = [];
  $items = Object.entries($items)
    .filter(([key, value]) => value !== undefined)
    .reduce((obj, [key, value]) => {
      existingEntries.push(value);
      obj[key] = value;
      return obj;
    }, {});
  localStorage.setItem('allEntries', JSON.stringify(existingEntries));
  $article = [];
  $.each($items, function(index, item) {
    $article.push(item.article);
  });
  $article = $article.join('$');
  $('#quick-buy-form input[name=article]').val($article);
  calcQuickBuyPrice(true);
  if (posCount === 1) {
    localStorage.removeItem('allEntries');
    window.close();
  }
});

$.ajax({
  url: '/sync/accrual',
  method: 'GET',
  dataType: 'JSON',
  data: {'articles': $articles},
  success: function(data) {
    $.each(data, function(index, value) {
      let item = JSON.parse(value);
      $('#quick-buy-form span[data-article="' + item.article + '"] input[name=urs_quantity]').val(item.urs_quantity);
      $('#quick-buy-form span[data-article="' + item.article + '"] input[name=krs_quantity]').val(item.krs_quantity);
    });
    // $('#urs_quantity').html('(' + data.urs_quantity + ')');
    // $('#krs_quantity').html('(' + data.krs_quantity + ')');
  }
});

$.ajax({
  url: '/getLinks',
  method: 'POST',
  dataType: 'JSON',
  data: {'articles': $articles},
  success: function(data) {
    $.each(data, function(index, value) {
      let $linkArticle = index;
      let $item = $('#quick-buy-form span[data-article="' + $linkArticle + '"]');
      if (value.Latakko) {
        $item.find('input[name=latakko]').val(value.Latakko.remaining).on('click', function() {
          window.open(value.Latakko.link);
        });
      } else {
        $item.find('input[name=latakko]').css('visibility', 'hidden');
      }

      if (value.Goodyear) {
        $item.find('input[name=goodyear]').val(value.Goodyear.remaining).on('click', function() {
          window.open(value.Goodyear.link);
        });
      } else {
        $item.find('input[name=goodyear]').css('visibility', 'hidden');
      }

      if (value.RiepuZona) {
        $item.find('input[name=riepuzona]').val(value.RiepuZona.remaining).on('click', function() {
          window.open(value.RiepuZona.link);
        });
      } else {
        $item.find('input[name=riepuzona]').css('visibility', 'hidden');
      }

      if (value.Duell) {
        $item.find('input[name=duell]').val(value.Duell.remaining).on('click', function() {
          window.open(value.Duell.link);
        });
      } else {
        $item.find('input[name=duell]').css('visibility', 'hidden');
      }

      if (value.Starco) {
        $item.find('input[name=starco]').val(value.Starco.remaining).on('click', function() {
          window.open(value.Starco.link);
        });
      } else {
        $item.find('input[name=starco]').css('visibility', 'hidden');
      }
    });
  }
});

function getFormData(form){
  var paramObj = {};
  $.each(form.serializeArray(), function(_, kv) {
    //console.log(kv);
    if (paramObj.hasOwnProperty(kv.name)) {
      paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
      paramObj[kv.name].push(kv.value);
    }
    else {
      paramObj[kv.name] = kv.value;
    }
  });
  return paramObj;
}

function sendData(data){
  $('#quick-buy-form').parent().addClass('busy');
  $.ajax({
    type: 'POST',
    url: '/accrualOrder',
    data: {info: data, '_token': data._token},
    timeout: 10000,
    success: function(resp){
      resp = JSON.parse(resp);
      if (resp.success) {
        Swal.fire({
          title: 'Paziņojums',
          html: resp.success,
          icon: 'success',
          confirmButtonText: 'OK',
          showDenyButton: true,
          denyButtonText: 'SMS',
        }).then((result) => {
          if (result.isConfirmed) {
            localStorage.removeItem('allEntries');
            window.close();
          } else if (result.isDenied) {
            $.ajax({
              method: 'POST',
              url: '/orderSMS',
              data: {info: data, orderId: resp.orderId, '_token': data._token},
              timeout: 10000,
              success: function(response) {
                Swal.fire({
                title: 'Paziņojums',
                html: response.success,
                icon: 'success',
                confirmButtonText: 'OK',
                }).then((result) => {
                  if (result.isConfirmed) {
                    localStorage.removeItem('allEntries');
                    window.close();
                  }
                })
              }
            });
          }
        });
      } else if (resp.danger) {
        Swal.fire({
          title: 'Kļūda!',
          html: resp.danger,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },
    error: function(jqXHR, textStatus){
      if (textStatus === 'timeout') {
        Swal.fire({
          title: 'Kļūda!',
          text: 'Pasūtījums nav pieņemts!',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },
    complete: function(){
      $('#quick-buy-form').parent().removeClass('busy');
    }
  })
}

$(document).ready(function() {
  $('.swal2-confirm').on('click', function() { window.close(); })
});

let montage = '';
let safe = '';

function toggleMontage(){
  if ($('#montage').prop('checked')) {
    $('#quick-buy-form input[name=price_montage]').removeAttr('disabled').focus();
  } else {
    $('#quick-buy-form input[name=price_montage]').attr('disabled','disabled').val('');
  }
  addMontagePrice();
}

function addMontagePrice(){
  if (!isNaN(parseFloat($('#quick-buy-form input[name=price]').val())) && !isNaN(parseFloat($('#quick-buy-form input[name=qty]').val()))) {
    montage = isNaN(parseInt($('#quick-buy-form input[name=price_montage]').val())) ? 0 : parseInt($('#quick-buy-form input[name=price_montage]').val());
    $('#quick-buy-form input[name=total]').val($globalPrice + montage);
    if (montage > 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val($globalPrice + montage + safe);
    } else if (montage === 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val($globalPrice + safe);
    } else if (montage === 0) {
      calcQuickBuyPrice(true);
    }
  }
}

function toggleSafe(){
  if ($('#safe').prop('checked')) {
    $('#quick-buy-form input[name=price_safe]').removeAttr('disabled').focus();
  } else {
    $('#quick-buy-form input[name=price_safe]').attr('disabled','disabled').val('');
  }
  addSafePrice();
}

function toggleMobile(){
  if ($('#mobile').prop('checked')) {
    $('#quick-buy-form input[name=mobile_number]').removeAttr('disabled').focus();
  } else {
    $('#quick-buy-form input[name=mobile_number]').attr('disabled','disabled').val('');
  }
}

function addSafePrice(){
  if (!isNaN(parseFloat($('#quick-buy-form input[name=price]').val())) && !isNaN(parseFloat($('#quick-buy-form input[name=qty]').val()))) {
    safe = isNaN(parseFloat($('#quick-buy-form input[name=price_safe]').val())) ? 0 : parseFloat($('#quick-buy-form input[name=price_safe]').val());
    $('#quick-buy-form input[name=total]').val(safe + $globalPrice);
    if (safe > 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(safe + montage + $globalPrice);
    } else if (safe === 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(montage + $globalPrice);
    } else if (safe === 0) {
      calcQuickBuyPrice(true);
    }
  }
}

function showQuickBuyForm(id) {
  $('#quick-buy-form input[name=prod_id]').val(id);
  $('#quick-buy-form input[name=price]').val($('#js-product-list article[data-id-product-attribute="'+id+'"]').find('.price').text().substr(2));
  $('#quick-buy-form input[name=prod]').val($('#js-product-list article[data-id-product-attribute="'+id+'"]').find('.product-title-hidden').text());
  calcQuickBuyPrice();
};
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script></body>
</html>

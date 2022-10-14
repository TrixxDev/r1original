<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Ātrais pasūtījums</title>
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

</head>
<body>

<div class="popup" id="quick-popup" data-popup="popup-3" style="display: block;">
  <div class="popup-inner">
    <div class="busy_bgr"><div class="busy_img"></div></div>
    <form id="quick-buy-form">
      @csrf
      <input type="hidden" name="article" value="{{ $param->article }}">
      <div class="location-wraper">
        <div class="radio-field"><input id="loc_URS" type="radio" name="location" value="URS" checked=""><label for="loc_URS">URS</label></div>
        <div class="radio-field"><input id="loc_KRS" type="radio" name="location" value="KRS"><label for="loc_KRS">KRS</label></div>
      </div>
      <div class="top-long-fields">
        <input type="text" placeholder="Prece" name="prod" value="{{ $param->prod }}" readonly="">
        <label for="qty">Sk.</label>
        <input type="number" min="1" placeholder="Daudzums" name="qty" value="{{ $param->qty }}" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()" style="width: 70px;">
        <label for="price">Cena</label>
        <input type="text" placeholder="Cena" name="price" style="width: 80px" value="{{ $param->price }}" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()">

      </div>
      <div class="bottom-long-fields">
        <span>Montāža</span>
        <input type="checkbox" id="montage" onchange="toggleMontage()" name="montage" value="1"><label for="montage"></label>
        <input type="text" name="total" placeholder="Summa" value="{{ $param->qty * $param->price }}" readonly="">
        <label for="total">Summa:</label>
        <input type="text" placeholder="Cena" name="price_montage" onkeyup="addMontagePrice()" disabled="">
      </div>
      <div class="bottom-long-fields">
        <span style="margin-left: 54px;">Glabāšana</span>
        <input type="checkbox" id="safe" onchange="toggleSafe()" name="safe" value="1"><label for="safe"></label>
        <input style="width: 100px;" type="text" placeholder="Cena" name="price_safe" onkeyup="addSafePrice()" disabled="">
      </div>
      <div class="user-fields">
        <input type="text" name="user" placeholder="Lietotājs" value="{{ $param->user }}">
        <textarea type="textarea" name="comments" placeholder="Komentāri"></textarea>
      </div>
      <a style="margin-left: 0;" class="button" onclick="return sendData(getFormData($('#quick-buy-form')));">Apstiprināt</a>
    </form>

    <style>
      /* POPUPS */

      .popup {
	z-index: 0!important;
      }

      .popup .location-wraper {
        float: left;
        margin: 0 15px 15px 0;
      }

      .popup .location-wraper input {
        height: 15px;
        display: inline-block;
        margin-right: 7px;
      }

      .popup .top-long-fields {
        height: 51px !important;
        width: 100% !important;
      }

      .popup .top-long-fields input {
        float: left;
        margin-left: 10px;
      }

      .popup .top-long-fields input[name="prod"] {
        width: 400px;
      }

      .popup .top-long-fields input[name="qty"] {
        width: 25px;
        padding: 0;
        padding-left: 5px;
      }

      .popup .top-long-fields input[name="price"] {
        width: 100px;
      }

      .popup .bottom-long-fields {
        height: 50px;
        width: 547px;
      }

      .popup .bottom-long-fields span {
        float: left;
        margin: 0 10px;
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
        width: 100px;
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
        left: 162px;
        top: 5px;
      }

      #quick-buy-msg {
        display: none;
        font-size: 30px;
        margin-top: 30px;
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
function calcQuickBuyPrice(){
  var total = parseFloat($('#quick-buy-form input[name=qty]').val()) * parseFloat($('#quick-buy-form input[name=price]').val());
  $('#quick-buy-form input[name=total]').val(isNaN(total) ? '' : total);
}

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
      $('#quick-buy-form').parent().find('.popup-close').click();
      $('.popup input[name=montage]').prop('checked', false);
      $('.popup input[name=price_montage]').attr('disabled', 'disabled');
      $('.popup input[name=price_montage]').val('');
      $('.popup input[name=safe]').prop('checked', false);
      $('.popup input[name=price_safe]').attr('disabled', 'disabled');
      $('.popup input[name=price_safe]').val('');
      $('.popup textarea[name=comments]').val('');
      Swal.fire({
        title: 'Paziņojums',
        text: 'Pasūtījums ir pieņemts!',
        icon: 'success',
        confirmButtonText: 'OK'
      });
      //$.ajax({
      //  type: 'GET',
      //  url: '/sync/accrual',
      //
      //})
    },
    error: function(jqXHR, textStatus){
      if (textStatus === 'timeout') {
        Swal.fire({
          title: 'Kļūda!',
          text: 'Pasūtījums nav pieņemts!',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        // toastr.error('Pastūtījums nav pieņemts!', 'Kļūda');
      }
    },
    complete: function(){
      $('#quick-buy-form').parent().removeClass('busy');
    }
  })
}

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
    montage = isNaN(parseFloat($('#quick-buy-form input[name=price_montage]').val())) ? 0 : parseFloat($('#quick-buy-form input[name=price_montage]').val());
    $('#quick-buy-form input[name=total]').val(montage+parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    if (montage > 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val(montage + safe + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    } else if (montage === 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val(safe + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
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

function addSafePrice(){
  if (!isNaN(parseFloat($('#quick-buy-form input[name=price]').val())) && !isNaN(parseFloat($('#quick-buy-form input[name=qty]').val()))) {
    safe = isNaN(parseFloat($('#quick-buy-form input[name=price_safe]').val())) ? 0 : parseFloat($('#quick-buy-form input[name=price_safe]').val());
    $('#quick-buy-form input[name=total]').val(safe+parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    if (safe > 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(safe + montage + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    } else if (safe === 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(montage + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

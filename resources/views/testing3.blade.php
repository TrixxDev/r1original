<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class="popup" id="quick-popup" data-popup="popup-3">
  <div class="popup-inner">
    <div class="busy_bgr"><div class="busy_img"></div></div>
    <form id="quick-buy-form">
      <input type="hidden" name="article">
      <div class="location-wraper">
        <div class="radio-field"><input id="loc_URS" type="radio" name="location" value="URS" checked=""><label for="loc_URS">URS</label></div>
        <div class="radio-field"><input id="loc_KRS" type="radio" name="location" value="KRS"><label for="loc_KRS">KRS</label></div>
      </div>
      <div class="top-long-fields">
        <input type="text" placeholder="Prece" name="prod" readonly="">
        <label for="qty">Sk.</label>
        <input type="number" min="1" placeholder="Daudzums" name="qty" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()" style="width: 70px;">
        <label for="price">Cena</label>
        <input type="text" placeholder="Cena" name="price" style="width: 80px" onchange="calcQuickBuyPrice()" onkeyup="calcQuickBuyPrice()">

      </div>
      <div class="bottom-long-fields">
        <span>Montāža</span>
        <input type="checkbox" id="montage" onchange="toggleMontage()" name="montage" value="1"><label for="montage"></label>
        <input type="text" name="total" placeholder="Summa" readonly="">
        <label for="total">Summa:</label>
        <input type="text" placeholder="Cena" name="price_montage" onkeyup="addMontagePrice()" disabled="">
      </div>
      <div class="bottom-long-fields">
        <span style="margin-left: 54px;">Glabāšana</span>
        <input type="checkbox" id="safe" onchange="toggleSafe()" name="safe" value="1"><label for="safe"></label>
        <input style="width: 100px;" type="text" placeholder="Cena" name="price_safe" onkeyup="addSafePrice()" disabled="">
      </div>
      <div class="user-fields">
        <input type="text" name="user" placeholder="Lietotājs" value=" ">
        <textarea type="textarea" name="comments" placeholder="Komentāri"></textarea>
      </div>
      <a style="margin-left: 0;" class="button" onclick="return sendData(getFormData($('#quick-buy-form')));">Apstiprināt</a>
      <a class="popup-close" data-dismiss="popup" aria-hidden="true" aria-label="Close" href="#"></a>
    </form>

    <style>
      /* POPUPS */
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
    <a class="popup-close" data-dismiss="popup" aria-hidden="true" data-popup-close="popup-2" href="#">x</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

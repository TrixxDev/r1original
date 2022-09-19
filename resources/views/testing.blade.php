<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>Hello Analytics Reporting API V4</title>
{{--  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">--}}
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
{{--  <script src="https://cdn.jsdelivr.net/npm/js-image-zoom@0.7.0/js-image-zoom.min.js"></script>--}}
  <link type="text/css" rel="stylesheet" href="public/css/magiczoomplus.css"/>
  <script src="public/js/magic.js"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: sans-serif;
      background-color: #f1f1f1;
    }

    .container .h-card .icon {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #2c73df;
    }

    .container .h-card .icon .fa {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 80px;
      color: #fff;
    }

    .container .h-card .slide {
      width: 300px;
      height: 200px;
      transition: 0.5s;
    }

    .container .h-card .slide.slide1 {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1;
      transition: .7s;
      transform: translateY(0px);
    }

    .container .h-card:hover .slide.slide1{
      transform: translateY(0px);
    }

    .container .h-card .slide.slide2 {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      box-sizing: border-box;
      transition: .8s;
      transform: translateY(-200px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    .container .h-card:hover .slide.slide2{
      transform: translateY(0);
    }

    .container .h-card .slide.slide2::after{

      content: "";
      position: absolute;
      width: 30px;
      height: 4px;
      bottom: 15px;
      left: 50%;
      left: 50%;
      transform: translateX(-50%);
      background: #2c73df;
    }

    .container .h-card .slide.slide2 .content p {
      margin: 0;
      padding: 0;
      text-align: center;
      color: #414141;
    }

    .container .h-card .slide.slide2 .content h3 {
      margin: 0 0 10px 0;
      padding: 0;
      font-size: 24px;
      text-align: center;
      color: #414141;

    }
  </style>

</head>
<body>

{{--<div class="zoom-section" style="float: left; width: 300px;">--}}
{{--  <div class="zoom-small-image">--}}
{{--    <a class="MagicZoom"--}}
{{--       data-options="expand: window; zoomWidth:600px; zoomHeight:600px"--}}
{{--       href="https://i.imgur.com/5PQcTIS.jpeg"--}}
{{--    >--}}
{{--      <img class="magic-image" src="https://i.imgur.com/ITZlrPP.jpeg" alt=""/>--}}
{{--    </a>--}}
{{--  </div>--}}
{{--</div>--}}

{{--<div class="big-mamma">--}}
{{--  <div class="container">--}}

{{--    <div class="item">--}}
{{--      <img src="http://localhost/public/storage/auto/tread/3969-o.jpg" alt="img">--}}
{{--      <div class="item-item">--}}
{{--        <p class="card-title-text">NOKIAN HKPL 10</p>--}}
{{--        <p>205 / 55 / 16</p>--}}
{{--        <p>€246 <span style="color: indianred">€175</span></p>--}}
{{--      </div>--}}
{{--    </div>--}}

{{--    <div class="item">--}}
{{--      <img src="http://localhost/public/storage/auto/tread/3969-o.jpg" alt="img">--}}
{{--      <div class="item-item">--}}
{{--        <p class="card-title-text">GOODYEAR ULTRA GRIP PERFORMANCE+</p>--}}
{{--        <p>205 / 55 / 16</p>--}}
{{--        <p>€246 <span style="color: indianred">€175</span></p>--}}
{{--      </div>--}}
{{--    </div>--}}

{{--  </div>--}}
{{--</div>--}}

<div class="container">

  <div class="h-card">
    <div class="slide slide1">
      <div class="content">
        <div class="icon">
          <a href="http://localhost/ziemas-riepas/yokohama/ig60%20ice%20guard/146001" class="">
            <div class="tire-image-card sort-order">
              <div class="text-center image-grid-overflow">
                <img src="http://localhost/public/storage/auto/tread/3969-o.jpg" alt="img" width="100px" height="100px">
              </div>

              <div class="tire-list-caption">

                <div class="card-title-text" data-toggle="tooltip" title="<div>Yokohama IG60 ICE GUARD</div>">
                  Yokohama IG60 ICE GUARD
                </div>

                <div class="tire-tread">
                  <div>205 / 55 / 16 <b>94Q</b></div> <span class="tire-image-code">XL</span>
                </div>
                <div style="display: inline-flex">
                  <div class="rim-price-old">€119</div>
                  <div class="rim-price-red">€89</div>
                </div>
              </div>

            </div>
          </a>
        </div>
      </div>
    </div>
    <div class="slide slide2">
      <div class="content">
        <h3>
          Hello there!
        </h3>
        <p>Trust yourself and keep going.</p>
      </div>
    </div>
  </div>

</div>

</body>
</html>

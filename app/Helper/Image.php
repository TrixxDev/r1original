<?php

  namespace App\Helper;

  use App\Models\Bannerimage;

  class Image {

    public static function image($type, $image) {

      $path = 'storage';

      switch ($type) {
        // AUTO TIRES
        case 'auto':
          $dir = $path . '/auto/tread/' . $image . '-o.jpg';
          break;
        case 'auto-rim':
          $dir = $path . '/rims/tread/' . $image . '-o.jpg';
          break;

        // QUAD TIRES
        case 'quadr':
          $dir = $path . '/quadr/tread/' . $image . '-o.jpg';
          break;
        case 'quadr-rim':
          $dir = $path . '/quadrims/tread/' . $image . '-o.jpg';
          break;

        // MOTO TIRES
        case 'moto':
          $dir = $path . '/moto/tread/' . $image . '-o.jpg';
          break;
        case 'moto-rim':
          $dir = $path . '/motorims/tread/' . $image . 'jpg';
          break;

        // INDUSTRIAL TIRES
        case 'big':
          $dir = $path . '/industrial/tread/' . $image . '-o.jpg';
          break;
        case 'big-rim':
          $dir = $path . '/industrialrims/tread/' . $image . '.jpg';
          break;

        // STUDS
        case 'studs':
          $dir = $path . '/stud/tread/' . $image . '-o.jpg';
          break;

        // BANNERS
        case 'banners':
          $dir = $path . '/banners/' . $image . '.jpg';
          break;
      }

      return $dir;
    }

    public static function exists($type, $image) {

      $image = Self::image($type, $image);

      if (file_exists($image)) {
        return true;
      } else {
        $image = str_replace('.jpg', '.png', $image);
        if (file_exists($image)) {
          return true;
        } else {
          return false;
        }
      }

    }

    public static function showAd($type, $image) {

      $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

      if (Self::exists($type, $image)) {
        if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
          $img = str_replace('.jpg', '.png', $img);
        }
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['APP_URL'] . '/' . $img;
      } else {
        return asset('img/p/r1-logo.svg');
      }

    }

    public static function show($type, $image) {

      $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

      if (Self::exists($type, $image)) {
        if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
          $img = str_replace('.jpg', '.png', $img);
        }
        return '<img style="width:280px;" src="' . asset($img) . '">';
      } else {
        return '<img src=' . asset('img/p/en-default-home_default.jpg') . '>';
      }

    }

    public static function countBanners() {
      $banners = Bannerimage::where('enabled', 1)->get();
      $count = count($banners);

      return $count;
    }

    public static function showBanners(){
      if (Self::countBanners() == 0) {
        return false;
      }
      $banners = Bannerimage::where('enabled', 1)->get();
      $count = count($banners);
      $maxBanners = 8;
      if ($count == 3) {
        $maxBanners = 12;
      }

      $return = '';
      $return .= '<div id="scroll-container">';
      $return .= '<div class="scroll-content">';

      for ($i = 0; $i < ($maxBanners / $count); $i++) {
        foreach ($banners as $banner) {
          if (!empty($banner->url)) {
            $return .= '<a href="' . url('//' . $banner->url) . '" target="_blank"><img class="banner-image" src="/storage/banners/' . $banner->name . '"></a>';
          } else {
            $return .= '<img class="banner-image" src="/storage/banners/' . $banner->name . '">';
          }
        }
      }

      $return .= '</div>';
      $return .= '</div>';

      return $return;
    }

    public static function showBanner($image){
      $img = str_replace(dirname(__DIR__, 2), '', Self::image('banners', $image));
      if (Self::exists('banners', $image)) {
        if (file_exists(str_replace('.jpg', '.png', Self::image('banners', $image)))) {
          $img = str_replace('.jpg', '.png', $img);
        }
        return '<img class="banner-image" src="' . asset($img) . '">';
      } else {
        return '<img src=' . asset('img/p/en-default-home_default.jpg') . '>';
      }
    }

    public static function showGrid($type, $image, $style = '') {

      $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

      if (Self::exists($type, $image)) {
        if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
          $img = str_replace('.jpg', '.png', $img);
        }
        if (empty($style)) {
          return '<img class="grid-tire-image" src=' . asset($img) . '>';
        } else {
          return '<img class="grid-tire-image" style="' . $style . '" src=' . asset($img) . '>';
        }
      } else {
        if (empty($style)) {
          return '<img class="grid-tire-image" src=' . asset('img/p/r1-logo.svg') . '>';
        } else {
          return '<img class="grid-tire-image" style="' . $style . '" src=' . asset('img/p/r1-logo.svg') . '>';
        }
      }

    }
    public static function treadZoom($type, $image) {

      $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

      if (Self::exists($type, $image)) {
        if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
          $img = str_replace('.jpg', '.png', $img);
        }
        $html = '<div class="zoom-section product-cover card text-center" style="padding: 10px">';
        $html .= '<div class="zoom-small-image">';
        $html .= '<a class="MagicZoom" data-options="expand: window;" href="/' . $img . '">';
        $html .= '<img class="magic-image" src="/' . $img . '" alt=""/>';
        $html .= '</a>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
      } else {
        return '<img style="width:100%; padding: 10px;" class="card product-cover" src=' . asset('img/p/r1-logo.svg') . '>';
      }

    }

  }

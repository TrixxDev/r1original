<?php

namespace App\Helper;

class Image {

  public static function image($type, $image) {

    $path = dirname(__DIR__, 2) . '/public/storage';

    switch ($type) {
      // AUTO TIRES
      case 'auto':
        $dir = $path . '/auto/tread/' . $image . '.jpg';
        break;
      case 'auto-rim':
        $dir = $path . '/auto/rims/rim-' . $image . '.jpg';
        break;

      // QUAD TIRES
      case 'quadr':
        $dir = $path . '/quadr/tread/' . $image . '.jpg';
        break;
      case 'quadr-rim':
        $dir = $path . '/quadr/rims/rim-' . $image . '.jpg';
        break;

      // MOTO TIRES
      case 'moto':
        $dir = $path . '/moto/tread/' . $image . '.jpg';
        break;
      case 'moto-rim':
        $dir = $path . '/moto/rims/rim-' . $image . 'jpg';
        break;

      // INDUSTRIAL TIRES
      case 'big':
        $dir = $path . '/industrial/tread/' . $image . '.jpg';
        break;
      case 'big-rim':
        $dir = $path . '/industrial/rims/rim-' . $image . '.jpg';
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

  public static function show($type, $image) {

    $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

    if (Self::exists($type, $image)) {
      if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
        $img = str_replace('.jpg', '.png', $img);
      }
      return '<img style="width:280px;" src="' . $img . '">';
    } else {
      return '<img src=' . asset('img/p/en-default-home_default.jpg') . '>';
    }

  }

  public static function showGrid($type, $image) {

    $img = str_replace(dirname(__DIR__, 2), '', Self::image($type, $image));

    if (Self::exists($type, $image)) {
      if (file_exists(str_replace('.jpg', '.png', Self::image($type, $image)))) {
        $img = str_replace('.jpg', '.png', $img);
      }
      return '<img style="width:130px;" src=' . $img . '>';
    } else {
      return '<img style="width:130px;" src=' . asset('img/p/r1-logo.svg') . '>';
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
      $html .= '<a class="MagicZoom" data-options="expand: window;" href="' . $img . '">';
      $html .= '<img class="magic-image" src="' . $img . '" alt=""/>';
      $html .= '</a>';
      $html .= '</div>';
      $html .= '</div>';

      return $html;
    } else {
      return '<img style="width:350px; padding: 10px;" class="card product-cover" src=' . asset('img/p/r1-logo.svg') . '>';
    }

  }

}

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
      return $img;
    } else {
      return '<img src=' . asset('img/p/en-default-home_default.jpg') . '>';
    }

  }

}

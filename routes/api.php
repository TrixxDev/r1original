<?php

use App\Models\FilterCars;
use App\Models\FilterSizes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\FilterModels;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->get('/company/{code}', function(Request $request, $code) {

  if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    if ($request->csrf != $_SERVER['HTTP_X_CSRF_TOKEN']) {
      abort(500);
    }
  } else {
    abort(500);
  }

  $token_url = 'https://oauth.lursoft.lv/authorize/token';

  if (Str::length($code) != 11) {
    return abort(500);
  }

  $url = 'https://b2b.lursoft.lv/?r=company&code=' . $code;

  $postdata = http_build_query(
    array(
      'grant_type' => 'password',
      'username' => 'r1sia_xml',
      'password' => 'ds8hlAza',
      'client_id' => 'fe4f5380',
      'client_secret' => '1feda647'
    )
  );

  $opts = array('http' =>
    array(
      'method'  => 'POST',
      'header'  => 'Content-Type: application/x-www-form-urlencoded',
      'content' => $postdata,
    )
  );

  $context  = stream_context_create($opts);

  $token = file_get_contents($token_url, false, $context);
  $response = json_decode($token, true);
  $token = $response['access_token'];

  unset($context);


  $opts = array('http' =>
    array(
      'method'  => 'GET',
      'header'  => "Content-Type: application/json en\r\n" .
                   "Authorization: Bearer ".$token."\r\n",
    )
  );

  $context  = stream_context_create($opts);

  $response = file_get_contents($url, false, $context);
  $response = (object) json_decode($response, true);
  $response = (object) $response->Answer;

  @$pvn_code = $response->Vat['vat'];
  if (empty($pvn_code)) {
    $pvn_code = '';
  }
  $code = $response->regcode;
  $name = $response->firm;
  $address = $response->Address['address'];

  $encode = ['pvn_code' => $pvn_code, 'code' => $code, 'name' => $name, 'address' => $address];
  return json_encode($encode);

});

Route::middleware('api')->get('/wheels/{car}/{model}', function($car, $model) {

  function decode($encoded) {
    $encoded = base64_decode($encoded);
    $decoded = "";
    for( $i = 0; $i < strlen($encoded); $i++ ) {
      $b = ord($encoded[$i]);
      $a = $b ^ 10;
      $decoded .= chr($a);
    }
    return base64_decode(base64_decode($decoded));
  }

  $model = FilterModels::where('title', decode($model))->first();
  $sizes = FilterSizes::where('modelId', $model->model_id)->orderBy('r', 'asc')->get();
  $size_opts = [];

  foreach ($sizes as $item) {
    array_push($size_opts, $item->r);
  }

  $size_opts = array_unique($size_opts);
  $size_opts = json_encode(array_values($size_opts));

  return $size_opts;

});

Route::middleware('api')->get('/wheels/{car}', function($car) {
  return FilterModels::where('carId', $car)->orderBy('title', 'asc')->get()->toJson();
});

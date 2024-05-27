<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as View;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//if (user_ip == '212.3.218.22') {
  //dd(123);
  //return View::make('maintenance');
  //return view('maintenance');
//}

Auth::routes();
Route::get('/register', function() { return abort(404); })->name('register');

Route::prefix('api')->name('admin.')->group(function() {
  Route::get('/tires/auto/{season}', [App\Http\Controllers\AutoTireController::class, 'api_tires']);
  Route::get('/tires/moto', [App\Http\Controllers\MotoTireController::class, 'api_tires']);
  Route::get('/tires/quadr', [App\Http\Controllers\QuadTireController::class, 'api_tires']);
  Route::get('/rims/auto', [App\Http\Controllers\RimsController::class, 'api_tires']);
  Route::get('/tires/autoSplitInput/{input}', [App\Http\Controllers\AutoTireController::class, 'splitInput']);
  Route::get('/tires/motoSplitInput/{input}', [App\Http\Controllers\MotoTireController::class, 'splitInput']);
  Route::get('/tires/quadrSplitInput/{input}', [App\Http\Controllers\QuadTireController::class, 'splitInput']);
});

// Administrācijas panelis

Route::namespace('Admin')->prefix('admin')->name('admin.')->middleware(['auth'])->group(function() {
  Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'home'])->name('home');

  // Auto riepas
  Route::match(['GET', 'POST'], '/auto', [App\Http\Controllers\Admin\AutoTireController::class, 'index'])->name('auto.tires');
  Route::get('/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_edit'])->name('auto.tire.edit');
  Route::post('/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_update'])->name('auto.tire.update');
  Route::get('/auto/delete/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_destroy'])->name('auto.tire.destroy');
  Route::post('/auto/ajaxUpdateTreads', [App\Http\Controllers\Admin\AutoTireController::class, 'ajaxUpdateTreads'])->name('auto.tires.ajaxUpdateTreads');
  Route::post('/auto/ajaxUpdateTires', [App\Http\Controllers\Admin\AutoTireController::class, 'ajaxUpdateTires'])->name('auto.tires.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/auto/tread/{tread_id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tires_search'])->name('auto.tires.search');
  Route::get('/auto/tread/{tread_id}/create', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_create'])->name('auto.tires.create');
  Route::post('/auto/tread/{tread_id}/delete', [App\Http\Controllers\Admin\AutoTireController::class, 'tires_destroy'])->name('auto.tires.delete_all');
  Route::post('/auto/tread/{tread_id}/store', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_store'])->name('auto.tires.store');
  Route::post('/auto/tread/{tread_id}/image', [App\Http\Controllers\Admin\AutoTireController::class, 'tire_image'])->name('auto.tires.image');
  Route::post('/auto/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\AutoTireController::class, 'ajaxUpdateTreads'])->name('auto.tires.ajaxUpdateTreads');
  Route::post('/auto/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\AutoTireController::class, 'ajaxUpdateTires'])->name('auto.tires.ajaxUpdateTires');

  // Auto riepu imports
  Route::get('/import/auto', [App\Http\Controllers\Admin\Import\AutoTireImportController::class, 'index'])->name('auto.import');
  Route::post('/import/auto', [App\Http\Controllers\Admin\Import\AutoTireImportController::class, 'import'])->name('auto.import.post');

  // Kvadru riepu imports
  Route::get('/import/quadr', [App\Http\Controllers\Admin\Import\QuadrTireImportController::class, 'index'])->name('quadr.import');
  Route::post('/import/quadr', [App\Http\Controllers\Admin\Import\QuadrTireImportController::class, 'import'])->name('quadr.import.post');

  // Moto riepu imports
  Route::get('/import/moto', [App\Http\Controllers\Admin\Import\MotoTireImportController::class, 'index'])->name('moto.import');
  Route::post('/import/moto', [App\Http\Controllers\Admin\Import\MotoTireImportController::class, 'import'])->name('moto.import.post');

  // Lielo riepu imports
  Route::get('/import/big', [App\Http\Controllers\Admin\Import\BigTireImportController::class, 'index'])->name('big.import');
  Route::post('/import/big', [App\Http\Controllers\Admin\Import\BigTireImportController::class, 'import'])->name('big.import.post');

  // Auto riepu brendi
  Route::get('/brands/auto', [App\Http\Controllers\Admin\AutoTireController::class, 'brands_list'])->name('auto.brands');
  Route::get('/brands/auto/add', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_add'])->name('auto.brands.add');
  Route::post('/brands/auto/add', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_store'])->name('auto.brands.store');
  Route::get('/brands/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_edit'])->name('auto.brands.edit');
  Route::post('/brands/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_update'])->name('auto.brands.update');
  Route::get('/brands/auto/{id}/delete', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_delete'])->name('auto.brands.delete');
  Route::get('/brands/auto/{per_page}', [App\Http\Controllers\Admin\AutoTireController::class, 'brands_list'])->name('auto.brands.per_page');
  Route::match(['get', 'post'],'/brands/auto/search', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_search'])->name('auto.brands.search');
  Route::match(['get', 'post'],'/brands/auto/search/{per_page}', [App\Http\Controllers\Admin\AutoTireController::class, 'brand_search'])->name('auto.brands.search.per_page');

  // Auto riepu modeļi
  Route::get('/treads/moto', [App\Http\Controllers\Admin\MotoTireController::class, 'treads_list'])->name('moto.treads');
  Route::get('/treads/moto/add', [App\Http\Controllers\Admin\MotoTireController::class, 'tread_add'])->name('moto.treads.add');
  Route::post('/treads/moto/add', [App\Http\Controllers\Admin\MotoTireController::class, 'tread_store'])->name('moto.treads.store');
  Route::get('/treads/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tread_edit'])->name('moto.treads.edit');
  Route::post('/treads/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tread_update'])->name('moto.treads.update');
  Route::get('/treads/moto/{id}/delete', [App\Http\Controllers\Admin\MotoTireController::class, 'tread_delete'])->name('moto.treads.delete');
  Route::get('/treads/moto/{per_page}', [App\Http\Controllers\Admin\MotoTireController::class, 'treads_list'])->name('moto.treads.per_page');
  Route::match(['get', 'post'],'/treads/moto/search', [App\Http\Controllers\Admin\MotoTireController::class, 'treads_search'])->name('moto.treads.search');
  Route::match(['get', 'post'],'/treads/moto/search/{per_page}', [App\Http\Controllers\Admin\MotoTireController::class, 'treads_search'])->name('moto.treads.search.per_page');

  // Moto riepas
  Route::match(['GET', 'POST'], '/moto', [App\Http\Controllers\Admin\MotoTireController::class, 'index'])->name('moto.tires');
  Route::get('/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_edit'])->name('moto.tire.edit');
  Route::post('/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_update'])->name('moto.tire.update');
  Route::get('/moto/delete/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_destroy'])->name('moto.tire.destroy');
  Route::post('/moto/ajaxUpdateTreads', [App\Http\Controllers\Admin\MotoTireController::class, 'ajaxUpdateTreads'])->name('moto.tires.ajaxUpdateTreads');
  Route::post('/moto/ajaxUpdateTires', [App\Http\Controllers\Admin\MotoTireController::class, 'ajaxUpdateTires'])->name('moto.tires.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/moto/tread/{tread_id}', [App\Http\Controllers\Admin\MotoTireController::class, 'tires_search'])->name('moto.tires.search');
  Route::get('/moto/tread/{tread_id}/create', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_create'])->name('moto.tires.create');
  Route::post('/moto/tread/{tread_id}/delete', [App\Http\Controllers\Admin\MotoTireController::class, 'tires_destroy'])->name('moto.tires.delete_all');
  Route::post('/moto/tread/{tread_id}/store', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_store'])->name('moto.tires.store');
  Route::post('/moto/tread/{tread_id}/image', [App\Http\Controllers\Admin\MotoTireController::class, 'tire_image'])->name('moto.tires.image');
  Route::post('/moto/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\MotoTireController::class, 'ajaxUpdateTreads'])->name('moto.tires.ajaxUpdateTreads');
  Route::post('/moto/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\MotoTireController::class, 'ajaxUpdateTires'])->name('moto.tires.ajaxUpdateTires');

  // Lielās riepas
  Route::match(['GET', 'POST'], '/big', [App\Http\Controllers\Admin\BigTireController::class, 'index'])->name('big.tires');
  Route::get('/big/edit/{id}', [App\Http\Controllers\Admin\BigTireController::class, 'tire_edit'])->name('big.tire.edit');
  Route::post('/big/edit/{id}', [App\Http\Controllers\Admin\BigTireController::class, 'tire_update'])->name('big.tire.update');
  Route::get('/big/delete/{id}', [App\Http\Controllers\Admin\BigTireController::class, 'tire_destroy'])->name('big.tire.destroy');
  Route::post('/big/ajaxUpdateTreads', [App\Http\Controllers\Admin\BigTireController::class, 'ajaxUpdateTreads'])->name('big.tires.ajaxUpdateTreads');
  Route::post('/big/ajaxUpdateTires', [App\Http\Controllers\Admin\BigTireController::class, 'ajaxUpdateTires'])->name('big.tires.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/big/tread/{tread_id}', [App\Http\Controllers\Admin\BigTireController::class, 'tires_search'])->name('big.tires.search');
  Route::get('/big/tread/{tread_id}/create', [App\Http\Controllers\Admin\BigTireController::class, 'tire_create'])->name('big.tires.create');
  Route::post('/big/tread/{tread_id}/delete', [App\Http\Controllers\Admin\BigTireController::class, 'tires_destroy'])->name('big.tires.delete_all');
  Route::post('/big/tread/{tread_id}/store', [App\Http\Controllers\Admin\BigTireController::class, 'tire_store'])->name('big.tires.store');
  Route::post('/big/tread/{tread_id}/image', [App\Http\Controllers\Admin\BigTireController::class, 'tire_image'])->name('big.tires.image');
  Route::post('/big/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\BigTireController::class, 'ajaxUpdateTreads'])->name('big.tires.ajaxUpdateTreads');
  Route::post('/big/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\BigTireController::class, 'ajaxUpdateTires'])->name('big.tires.ajaxUpdateTires');

  // Moto riepu brendi
  Route::get('/brands/moto', [App\Http\Controllers\Admin\MotoTireController::class, 'brands_list'])->name('moto.brands');
  Route::get('/brands/moto/add', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_add'])->name('moto.brands.add');
  Route::post('/brands/moto/add', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_store'])->name('moto.brands.store');
  Route::get('/brands/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_edit'])->name('moto.brands.edit');
  Route::post('/brands/moto/edit/{id}', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_update'])->name('moto.brands.update');
  Route::get('/brands/moto/{id}/delete', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_delete'])->name('moto.brands.delete');
  Route::get('/brands/moto/{per_page}', [App\Http\Controllers\Admin\MotoTireController::class, 'brands_list'])->name('moto.brands.per_page');
  Route::match(['get', 'post'],'/brands/moto/search', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_search'])->name('moto.brands.search');
  Route::match(['get', 'post'],'/brands/moto/search/{per_page}', [App\Http\Controllers\Admin\MotoTireController::class, 'brand_search'])->name('moto.brands.search.per_page');

  // Moto riepu modeļi
  Route::get('/treads/auto', [App\Http\Controllers\Admin\AutoTireController::class, 'treads_list'])->name('auto.treads');
  Route::get('/treads/auto/add', [App\Http\Controllers\Admin\AutoTireController::class, 'tread_add'])->name('auto.treads.add');
  Route::post('/treads/auto/add', [App\Http\Controllers\Admin\AutoTireController::class, 'tread_store'])->name('auto.treads.store');
  Route::get('/treads/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tread_edit'])->name('auto.treads.edit');
  Route::post('/treads/auto/edit/{id}', [App\Http\Controllers\Admin\AutoTireController::class, 'tread_update'])->name('auto.treads.update');
  Route::get('/treads/auto/{id}/delete', [App\Http\Controllers\Admin\AutoTireController::class, 'tread_delete'])->name('auto.treads.delete');
  Route::get('/treads/auto/{per_page}', [App\Http\Controllers\Admin\AutoTireController::class, 'treads_list'])->name('auto.treads.per_page');
  Route::match(['get', 'post'],'/treads/auto/search', [App\Http\Controllers\Admin\AutoTireController::class, 'treads_search'])->name('auto.treads.search');
  Route::match(['get', 'post'],'/treads/auto/search/{per_page}', [App\Http\Controllers\Admin\AutoTireController::class, 'treads_search'])->name('auto.treads.search.per_page');

  // Kvadraciklu riepas
  Route::match(['GET', 'POST'], '/quadr', [App\Http\Controllers\Admin\QuadrTireController::class, 'index'])->name('quadr.tires');
  Route::get('/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_edit'])->name('quadr.tire.edit');
  Route::post('/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_update'])->name('quadr.tire.update');
  Route::get('/quadr/delete/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_destroy'])->name('quadr.tire.destroy');
  Route::post('/quadr/ajaxUpdateTreads', [App\Http\Controllers\Admin\QuadrTireController::class, 'ajaxUpdateTreads'])->name('quadr.tires.ajaxUpdateTreads');
  Route::post('/quadr/ajaxUpdateTires', [App\Http\Controllers\Admin\QuadrTireController::class, 'ajaxUpdateTires'])->name('quadr.tires.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/quadr/tread/{tread_id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tires_search'])->name('quadr.tires.search');
  Route::get('/quadr/tread/{tread_id}/create', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_create'])->name('quadr.tires.create');
  Route::post('/quadr/tread/{tread_id}/delete', [App\Http\Controllers\Admin\QuadrTireController::class, 'tires_destroy'])->name('quadr.tires.delete_all');
  Route::post('/quadr/tread/{tread_id}/store', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_store'])->name('quadr.tires.store');
  Route::post('/quadr/tread/{tread_id}/image', [App\Http\Controllers\Admin\QuadrTireController::class, 'tire_image'])->name('quadr.tires.image');
  Route::post('/quadr/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\QuadrTireController::class, 'ajaxUpdateTreads'])->name('quadr.tires.ajaxUpdateTreads');
  Route::post('/quadr/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\QuadrTireController::class, 'ajaxUpdateTires'])->name('quadr.tires.ajaxUpdateTires');

  // Moto riepu brendi
  Route::get('/brands/quadr', [App\Http\Controllers\Admin\QuadrTireController::class, 'brands_list'])->name('quadr.brands');
  Route::get('/brands/quadr/add', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_add'])->name('quadr.brands.add');
  Route::post('/brands/quadr/add', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_store'])->name('quadr.brands.store');
  Route::get('/brands/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_edit'])->name('quadr.brands.edit');
  Route::post('/brands/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_update'])->name('quadr.brands.update');
  Route::get('/brands/quadr/{id}/delete', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_delete'])->name('quadr.brands.delete');
  Route::get('/brands/quadr/{per_page}', [App\Http\Controllers\Admin\QuadrTireController::class, 'brands_list'])->name('quadr.brands.per_page');
  Route::match(['get', 'post'],'/brands/quadr/search', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_search'])->name('quadr.brands.search');
  Route::match(['get', 'post'],'/brands/quadr/search/{per_page}', [App\Http\Controllers\Admin\QuadrTireController::class, 'brand_search'])->name('quadr.brands.search.per_page');

  // Moto riepu modeļi
  Route::get('/treads/quadr', [App\Http\Controllers\Admin\QuadrTireController::class, 'treads_list'])->name('quadr.treads');
  Route::get('/treads/quadr/add', [App\Http\Controllers\Admin\QuadrTireController::class, 'tread_add'])->name('quadr.treads.add');
  Route::post('/treads/quadr/add', [App\Http\Controllers\Admin\QuadrTireController::class, 'tread_store'])->name('quadr.treads.store');
  Route::get('/treads/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tread_edit'])->name('quadr.treads.edit');
  Route::post('/treads/quadr/edit/{id}', [App\Http\Controllers\Admin\QuadrTireController::class, 'tread_update'])->name('quadr.treads.update');
  Route::get('/treads/quadr/{id}/delete', [App\Http\Controllers\Admin\QuadrTireController::class, 'tread_delete'])->name('quadr.treads.delete');
  Route::get('/treads/quadr/{per_page}', [App\Http\Controllers\Admin\QuadrTireController::class, 'treads_list'])->name('quadr.treads.per_page');
  Route::match(['get', 'post'],'/treads/quadr/search', [App\Http\Controllers\Admin\QuadrTireController::class, 'treads_search'])->name('quadr.treads.search');
  Route::match(['get', 'post'],'/treads/quadr/search/{per_page}', [App\Http\Controllers\Admin\QuadrTireController::class, 'treads_search'])->name('quadr.treads.search.per_page');

  // Lietie diski
  Route::match(['GET', 'POST'], '/rims', [App\Http\Controllers\Admin\RimsController::class, 'index'])->name('rims');
  Route::get('/rims/edit/{id}', [App\Http\Controllers\Admin\RimsController::class, 'rims_edit'])->name('rims.edit');
  Route::post('/rims/edit/{id}', [App\Http\Controllers\Admin\RimsController::class, 'rims_update'])->name('rims.update');
  Route::get('/rims/delete/{id}', [App\Http\Controllers\Admin\RimsController::class, 'rims_destroy'])->name('rims.destroy');
  Route::post('/rims/ajaxUpdateTreads', [App\Http\Controllers\Admin\RimsController::class, 'ajaxUpdateTreads'])->name('rims.ajaxUpdateTreads');
  Route::post('/rims/ajaxUpdateTires', [App\Http\Controllers\Admin\RimsController::class, 'ajaxUpdateTires'])->name('rims.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/rims/tread/{tread_id}', [App\Http\Controllers\Admin\RimsController::class, 'rims_search'])->name('rims.search');
  Route::get('/rims/tread/{tread_id}/create', [App\Http\Controllers\Admin\RimsController::class, 'rims_create'])->name('rims.create');
  Route::post('/rims/tread/{tread_id}/delete', [App\Http\Controllers\Admin\RimsController::class, 'allrims_destroy'])->name('rims.delete_all');
  Route::post('/rims/tread/{tread_id}/store', [App\Http\Controllers\Admin\RimsController::class, 'rims_store'])->name('rims.store');
  Route::post('/rims/tread/{tread_id}/image', [App\Http\Controllers\Admin\RimsController::class, 'rims_image'])->name('rims.image');
  Route::post('/rims/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\RimsController::class, 'ajaxUpdateTreads'])->name('rims.ajaxUpdateTreads');
  Route::post('/rims/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\RimsController::class, 'ajaxUpdateTires'])->name('rims.ajaxUpdateTires');

  // Kvadru diski
  Route::match(['GET', 'POST'], '/quadrims', [App\Http\Controllers\Admin\QuadrRimController::class, 'index'])->name('quadrims');
  Route::get('/quadrims/edit/{id}', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_edit'])->name('quadrims.edit');
  Route::post('/quadrims/edit/{id}', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_update'])->name('quadrims.update');
  Route::get('/quadrims/delete/{id}', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_destroy'])->name('quadrims.destroy');
  Route::post('/quadrims/ajaxUpdateTreads', [App\Http\Controllers\Admin\QuadrRimController::class, 'ajaxUpdateTreads'])->name('quadrims.ajaxUpdateTreads');
  Route::post('/quadrims/ajaxUpdateTires', [App\Http\Controllers\Admin\QuadrRimController::class, 'ajaxUpdateTires'])->name('quadrims.ajaxUpdateTires');
  Route::match(['GET', 'POST'], '/quadrims/tread/{tread_id}', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_search'])->name('quadrims.search');
  Route::get('/quadrims/tread/{tread_id}/create', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_create'])->name('quadrims.create');
  Route::post('/quadrims/tread/{tread_id}/delete', [App\Http\Controllers\Admin\QuadrRimController::class, 'allquadrims_destroy'])->name('quadrims.delete_all');
  Route::post('/quadrims/tread/{tread_id}/store', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_store'])->name('quadrims.store');
  Route::post('/quadrims/tread/{tread_id}/image', [App\Http\Controllers\Admin\QuadrRimController::class, 'quadrims_image'])->name('quadrims.image');
  Route::post('/quadrims/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\QuadrRimController::class, 'ajaxUpdateTreads'])->name('quadrims.ajaxUpdateTreads');
  Route::post('/quadrims/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\QuadrRimController::class, 'ajaxUpdateTires'])->name('quadrims.ajaxUpdateTires');

  // Radzes
  Route::match(['GET', 'POST'], '/studs', [App\Http\Controllers\Admin\StudsController::class, 'index'])->name('studs.index');
  Route::get('/studs/edit/{id}', [App\Http\Controllers\Admin\StudsController::class, 'studs_edit'])->name('studs.edit');
  Route::post('/studs/edit/{id}', [App\Http\Controllers\Admin\StudsController::class, 'studs_update'])->name('studs.update');
  Route::get('/studs/delete/{id}', [App\Http\Controllers\Admin\StudsController::class, 'studs_destroy'])->name('studs.destroy');
  Route::match(['GET', 'POST'], '/studs/tread/{tread_id}', [App\Http\Controllers\Admin\StudsController::class, 'tires_search'])->name('studs.search');
  Route::get('/studs/tread/{tread_id}/create', [App\Http\Controllers\Admin\StudsController::class, 'studs_create'])->name('studs.create');
  Route::post('/studs/tread/{tread_id}/delete', [App\Http\Controllers\Admin\StudsController::class, 'allstuds_destroy'])->name('studs.delete_all');
  Route::post('/studs/tread/{tread_id}/store', [App\Http\Controllers\Admin\StudsController::class, 'studs_store'])->name('studs.store');
  Route::post('/studs/tread/{tread_id}/image', [App\Http\Controllers\Admin\StudsController::class, 'studs_image'])->name('studs.image');
  Route::post('/studs/tread/{tread_id}/ajaxUpdateTreads', [App\Http\Controllers\Admin\StudsController::class, 'ajaxUpdateTreads'])->name('studs.ajaxUpdateTreads');
  Route::post('/studs/tread/{tread_id}/ajaxUpdateTires', [App\Http\Controllers\Admin\StudsController::class, 'ajaxUpdateTires'])->name('studs.ajaxUpdateTires');

  // Interneta-veikals
  Route::match(['GET', 'POST'], '/orders', [App\Http\Controllers\Admin\ShopController::class, 'orders'])->name('orders');
  Route::match(['GET', 'POST'], '/orders/print', [App\Http\Controllers\Admin\ShopController::class, 'orders_print'])->name('orders_print');
  Route::match(['GET', 'POST'], '/order/{id}/update', [App\Http\Controllers\Admin\ShopController::class, 'order_update'])->name('order.update');
  Route::match(['GET', 'POST'], '/order/{id}/delete', [App\Http\Controllers\Admin\ShopController::class, 'delete'])->name('order.delete');
  Route::get('/order/{id}', [App\Http\Controllers\Admin\ShopController::class, 'order'])->name('order');

  // Pieraksts
  Route::get('/pieraksts/date={date}', [App\Http\Controllers\Admin\Records\RecordController::class, 'index'])->name('records.date');
  Route::get('/pieraksts/', [App\Http\Controllers\Admin\Records\RecordController::class, 'index'])->name('records');

  Route::match(['GET', 'POST'], '/pieraksts/queue_ajax/{queue_id}/{date}', [App\Http\Controllers\Admin\Records\RecordController::class, 'queue_ajax']);
  Route::match(['GET', 'POST'], '/pieraksts/slot_ajax/{queue_id}/{date}/{slot_id}', [App\Http\Controllers\Admin\Records\RecordController::class, 'slot_ajax']);
  Route::post('/pieraksts/discount', [App\Http\Controllers\Admin\Records\RecordController::class, 'discount']);

  Route::get('/rezervacijas/date={date}', [App\Http\Controllers\Admin\Records\RecordController::class, 'reservations'])->name('reservations.date');
  Route::get('/rezervacijas/', [App\Http\Controllers\Admin\Records\RecordController::class, 'reservations'])->name('reservations');

  Route::match(['GET', 'POST'], '/rezervacijas/editSlot', [App\Http\Controllers\Admin\Records\RecordController::class, 'reservations_ajax'])->name('reservations_ajax');

  Route::match(['GET', 'POST'],'/audits', [App\Http\Controllers\Admin\MainController::class, 'audits'])->name('audits');
  Route::get('/audit/{id}', [App\Http\Controllers\Admin\MainController::class, 'audit'])->name('audit');

  // Iestatījumi
  // Pakalpojumi
  Route::get('/settings/services', [App\Http\Controllers\Admin\SettingsController::class, 'services'])->name('settings.services');
  Route::post('/settings/services/add', [App\Http\Controllers\Admin\SettingsController::class, 'services_store'])->name('settings.services.add');
  Route::post('/settings/services/{id}/enable', [App\Http\Controllers\Admin\SettingsController::class, 'services_enable'])->name('settings.services.enable');
  Route::post('/settings/services/{id}/active', [App\Http\Controllers\Admin\SettingsController::class, 'services_active'])->name('settings.services.active');
  Route::post('/settings/services/{id}/edit', [App\Http\Controllers\Admin\SettingsController::class, 'services_edit'])->name('settings.services.edit');
  Route::get('/settings/services/{id}/delete', [App\Http\Controllers\Admin\SettingsController::class, 'services_destroy'])->name('settings.services.destroy');

  // Administratori
  Route::get('/settings/users', [App\Http\Controllers\Admin\SettingsController::class, 'users'])->name('settings.users');
  Route::get('/settings/users/create', [App\Http\Controllers\Admin\SettingsController::class, 'users_create'])->name('settings.users.create');
  Route::post('/settings/users/store', [App\Http\Controllers\Admin\SettingsController::class, 'users_store'])->name('settings.users.store');
  Route::get('/settings/users/{id}/edit', [App\Http\Controllers\Admin\SettingsController::class, 'users_edit'])->name('settings.users.edit');
  Route::post('/settings/users/{id}/update', [App\Http\Controllers\Admin\SettingsController::class, 'users_update'])->name('settings.users.update');
  Route::match(['GET', 'POST'], '/settings/user/{id}/pwdChange', [App\Http\Controllers\Admin\SettingsController::class, 'user_pwdChange'])->name('settings.user.pwdChange');
  Route::get('/settings/users/{id}/delete', [App\Http\Controllers\Admin\SettingsController::class, 'users_destroy'])->name('settings.users.destroy');

  // Lomas
  Route::get('/settings/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('settings.roles');
  Route::get('/settings/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('settings.roles.create');
  Route::post('/settings/roles/insert', [App\Http\Controllers\Admin\RoleController::class, 'insert'])->name('settings.roles.insert');
  Route::get('/settings/roles/{id}/delete', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('settings.roles.destroy');
  Route::post('/settings/roles/togglePermission', [App\Http\Controllers\Admin\RoleController::class, 'togglePermission'])->name('settings.roles.togglePermission');

  Route::get('/settings/permissions/{id}/delete', [App\Http\Controllers\Admin\RoleController::class, 'removePermission'])->name('settings.roles.removePermission');

  // Sinhronizācijas
  Route::get('/settings/syncs', [App\Http\Controllers\Admin\SettingsController::class, 'syncs'])->name('settings.syncs');

  // Lapas
  Route::get('/settings/pages', [App\Http\Controllers\Admin\SettingsController::class, 'pages'])->name('settings.pages');
  Route::get('/settings/pages/create', [App\Http\Controllers\Admin\SettingsController::class, 'pages_create'])->name('settings.pages.create');
  Route::post('/settings/pages/store', [App\Http\Controllers\Admin\SettingsController::class, 'pages_store'])->name('settings.pages.store');
  Route::get('/settings/pages/{id}/edit', [App\Http\Controllers\Admin\SettingsController::class, 'pages_edit'])->name('settings.pages.edit');
  Route::post('/settings/pages/{id}/update', [App\Http\Controllers\Admin\SettingsController::class, 'pages_update'])->name('settings.pages.update');
  Route::get('/settings/pages/{id}/delete', [App\Http\Controllers\Admin\SettingsController::class, 'pages_destroy'])->name('settings.pages.destroy');

  // Skrienošā josla
  Route::get('/settings/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('settings.banners');
  Route::post('/settings/banners', [App\Http\Controllers\Admin\BannerController::class, 'upload'])->name('settings.upload');
  Route::post('/settings/banners/{id}/update', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('settings.banners.update');
  Route::post('/settings/banners/{id}/enable', [App\Http\Controllers\Admin\BannerController::class, 'enable'])->name('settings.banners.enable');
  Route::post('/settings/banners/{id}/delete', [App\Http\Controllers\Admin\BannerController::class, 'delete'])->name('settings.banners.delete');

  // Riepu kodu paskaidrojumi
  Route::get('/settings/codes', [App\Http\Controllers\Admin\SettingsController::class, 'codes'])->name('settings.codes');
  Route::get('/settings/codes/create', [App\Http\Controllers\Admin\SettingsController::class, 'codes_create'])->name('settings.codes.create');
  Route::post('/settings/codes/store', [App\Http\Controllers\Admin\SettingsController::class, 'codes_store'])->name('settings.codes.store');
  Route::get('/settings/codes/{id}/edit', [App\Http\Controllers\Admin\SettingsController::class, 'codes_edit'])->name('settings.codes.edit');
  Route::post('/settings/codes/{id}/update', [App\Http\Controllers\Admin\SettingsController::class, 'codes_update'])->name('settings.codes.update');
  Route::delete('/settings/codes/{id}/delete', [App\Http\Controllers\Admin\SettingsController::class, 'codes_destroy'])->name('settings.codes.destroy');

  // Montāžu/Piegāžu cenas
  Route::get('/settings/prices', [App\Http\Controllers\Admin\SettingsController::class, 'prices'])->name('settings.prices');
  Route::post('/settings/prices/{id}/update', [App\Http\Controllers\Admin\SettingsController::class, 'price_update'])->name('settings.prices.update');

  Route::get('/promo', [App\Http\Controllers\Admin\PromoCodeController::class, 'index'])->name('promo.index');
  Route::get('/promo/create', [App\Http\Controllers\Admin\PromoCodeController::class, 'create'])->name('promo.create');
  Route::post('/promo/store', [App\Http\Controllers\Admin\PromoCodeController::class, 'store'])->name('promo.store');
  Route::get('/promo/delete/{id}', [App\Http\Controllers\Admin\PromoCodeController::class, 'destroy'])->name('promo.delete');
});

Route::get('/sendSMS', function() {
  (new \App\Helper\SmsSender())->send();
});

Route::post('/orderSMS', [App\Http\Controllers\HomeController::class, 'sendOrderSMS']);

Route::middleware('checksession')->group(function() {

  // Sākumlapa/Iziešana no konta


  Route::get('/', function() {
   return redirect('/pieraksts');
  })->name('home');
  Route::get('/callback', function() {
   require_once(dirname(__DIR__) . '/app/Paysera/callback.php');
  });
  Route::get('/logout', function() {
    Auth::logout();
    Session::flush();
    return redirect()->back();
  })->name('logout');

//Valodas maiņa

  Route::get('/lang/{lang}', [App\Http\Controllers\LocalizationController::class, 'index'])->name('lang');

//Riepas

  Route::post('/auto/tires_filter/', [App\Http\Controllers\AutoTireController::class, 'tires_filter'])->name('tires_filter');

// Ziemas riepas
  Route::get('/ziemas-riepas/', [App\Http\Controllers\AutoTireController::class, 'tires'])->name('ziemas-riepas');
  Route::post('/ziemas-riepas', [App\Http\Controllers\AutoTireController::class, 'tires_search'])->name('ziemas-riepas');
  Route::get('/ziemas-riepas/search/api/getSizes/{season}', [App\Http\Controllers\AutoTireController::class, 'get_sizes']);
  Route::get('/ziemas-riepas/{brand}/{tread}/{tire}', [App\Http\Controllers\AutoTireController::class, 'tires_tread'])->name('ziemas-riepa');
  Route::post('/ziemas-riepas/ajax', [App\Http\Controllers\AutoTireController::class, 'tires_ajax'])->name('ziemas-riepas-ajax');
  Route::post('/ziemas-riepas/search/ajax', [App\Http\Controllers\AutoTireController::class, 'tires_ajax'])->name('ziemas-riepas-ajax');
  Route::get('/ziemas-riepas/search', [App\Http\Controllers\AutoTireController::class, 'tires_find'])->name('ziemas-riepas-meklet');
  Route::get('/ziemas-riepas/getBrandList', [App\Http\Controllers\AutoTireController::class, 'tires_getBrands']);

// Vasaras riepas
  Route::get('/vasaras-riepas', [App\Http\Controllers\AutoTireController::class, 'tires'])->name('vasaras-riepas');
  Route::post('/vasaras-riepas', [App\Http\Controllers\AutoTireController::class, 'tires_search'])->name('vasaras-riepas');
  Route::get('/vasaras-riepas/search/api/getSizes/{season}', [App\Http\Controllers\AutoTireController::class, 'get_sizes']);
  Route::get('/vasaras-riepas/{brand}/{tread}/{tire}', [App\Http\Controllers\AutoTireController::class, 'tires_tread'])->name('vasaras-riepa');
  Route::post('/vasaras-riepas/ajax', [App\Http\Controllers\AutoTireController::class, 'tires_ajax'])->name('vasaras-riepas-ajax');
  Route::post('/vasaras-riepas/search/ajax', [App\Http\Controllers\AutoTireController::class, 'tires_ajax'])->name('vasaras-riepas-ajax');
  Route::get('/vasaras-riepas/search', [App\Http\Controllers\AutoTireController::class, 'tires_find'])->name('vasaras-riepas-meklet');
  Route::get('/vasaras-riepas/getBrandList', [App\Http\Controllers\AutoTireController::class, 'tires_getBrands']);

// Kvadraciklu riepas
  Route::get('/kvadru-riepas', [App\Http\Controllers\QuadTireController::class, 'index'])->name('kvadraciklu-riepas');
  Route::post('/kvadru-riepas', [App\Http\Controllers\QuadTireController::class, 'tires_search'])->name('kvadraciklu-riepas');
  Route::get('/kvadru-riepas/search/api/getSizes', [App\Http\Controllers\QuadTireController::class, 'get_sizes']);
  Route::get('/kvadru-riepas/{brand}/{tread}/{tire}', [App\Http\Controllers\QuadTireController::class, 'tires_tread'])->name('kvadraciklu-riepa');
  Route::post('/kvadru-riepas/ajax', [App\Http\Controllers\QuadTireController::class, 'tires_ajax'])->name('kvadraciklu-riepas-ajax');
  Route::post('/kvadru-riepas/search/ajax', [App\Http\Controllers\QuadTireController::class, 'tires_ajax'])->name('kvadraciklu-riepas-ajax');
  Route::get('/kvadru-riepas/search', [App\Http\Controllers\QuadTireController::class, 'tires_find'])->name('kvadraciklu-riepas-meklet');

// Motociklu riepas
  Route::get('/motociklu-riepas', [App\Http\Controllers\MotoTireController::class, 'index'])->name('motociklu-riepas');
  Route::post('/motociklu-riepas', [App\Http\Controllers\MotoTireController::class, 'tires_search'])->name('motociklu-riepas');
  Route::get('/motociklu-riepas/search/api/getSizes', [App\Http\Controllers\MotoTireController::class, 'get_sizes']);
  Route::get('/motociklu-riepas/{brand}/{tread}/{tire}', [App\Http\Controllers\MotoTireController::class, 'tires_tread'])->name('motociklu-riepa');
  Route::post('/motociklu-riepas/ajax', [App\Http\Controllers\MotoTireController::class, 'tires_ajax'])->name('motociklu-riepas-ajax');
  Route::post('/motociklu-riepas/search/ajax', [App\Http\Controllers\MotoTireController::class, 'tires_ajax'])->name('motociklu-riepas-ajax');
  Route::get('/motociklu-riepas/search', [App\Http\Controllers\MotoTireController::class, 'tires_find'])->name('motociklu-riepas-meklet');

//Lielās riepas
  Route::get('/lielas-riepas', [App\Http\Controllers\BigTireController::class, 'index'])->name('lielas-riepas');
  Route::post('/lielas-riepas', [App\Http\Controllers\BigTireController::class, 'tires_search'])->name('lielas-riepas');
  Route::get('/lielas-riepas/{brand}/{tread}/{tire}', [App\Http\Controllers\BigTireController::class, 'tires_tread'])->name('lielas-riepa');
  Route::post('/lielas-riepas/ajax', [App\Http\Controllers\BigTireController::class, 'tires_ajax'])->name('lielas-riepas-ajax');
  Route::post('/lielas-riepas/search/ajax', [App\Http\Controllers\BigTireController::class, 'tires_ajax'])->name('lielas-riepas-ajax');
  Route::get('/lielas-riepas/search', [App\Http\Controllers\BigTireController::class, 'tires_search'])->name('lielas-riepas-meklet');
  Route::get('/lielas-riepas/getBrandList', [App\Http\Controllers\BigTireController::class, 'tires_getBrands']);

  // LIETIE DISKI

  Route::get('/lietie-diski', [App\Http\Controllers\RimsController::class, 'rims'])->name('lietie-diski');
  Route::post('/lietie-diski', [App\Http\Controllers\RimsController::class, 'rims_search'])->name('lietie-diski');
  Route::get('/lietie-diski/{brand}/{tread}/{tire}', [App\Http\Controllers\RimsController::class, 'rims_tread'])->name('lietais-disks');
  Route::post('/lietie-diski/ajax', [App\Http\Controllers\RimsController::class, 'rims_ajax'])->name('lietie-diski-ajax');
  Route::post('/lietie-diski/search/ajax', [App\Http\Controllers\RimsController::class, 'rims_ajax'])->name('lietie-diski-ajax');
  Route::get('/lietie-diski/search', [App\Http\Controllers\RimsController::class, 'rims_search'])->name('lietie-diski-meklet');
  Route::get('/lietie-diski/getBrandList', [App\Http\Controllers\RimsController::class, 'rims_getBrands']);

  // KVADRU DISKI

  Route::get('/kvadru-diski', [App\Http\Controllers\QuadrRimsController::class, 'rims'])->name('kvadru-diski');
  Route::post('/kvadru-diski', [App\Http\Controllers\QuadrRimsController::class, 'rims_search'])->name('kvadru-diski');
  Route::get('/kvadru-diski/{brand}/{tread}/{rim}', [App\Http\Controllers\QuadrRimsController::class, 'rims_tread'])->name('kvadru-disks');
  Route::post('/kvadru-diski/ajax', [App\Http\Controllers\QuadrRimsController::class, 'rims_ajax'])->name('kvadru-diski-ajax');
  Route::post('/kvadru-diski/search/ajax', [App\Http\Controllers\QuadrRimsController::class, 'rims_ajax'])->name('kvadru-diski-ajax');
  Route::get('/kvadru-diski/search', [App\Http\Controllers\QuadrRimsController::class, 'rims_search'])->name('kvadru-diski-meklet');
  Route::get('/kvadru-diski/getBrandList', [App\Http\Controllers\QuadrRimsController::class, 'rims_getBrands']);


  //Radzes
  Route::get('/radzes', [App\Http\Controllers\StudsController::class, 'studs'])->name('radzes');
  Route::post('/radzes', [App\Http\Controllers\StudsController::class, 'studs_search'])->name('radzes');
  Route::get('/radzes/{brand}/{tread}/{stud}', [App\Http\Controllers\StudsController::class, 'studs_tread'])->name('radze');
  Route::post('/radzes/ajax', [App\Http\Controllers\StudsController::class, 'studs_ajax'])->name('radzes-ajax');
  Route::post('/radzes/search/ajax', [App\Http\Controllers\StudsController::class, 'studs_ajax'])->name('radzes-ajax');
  Route::get('/radzes/search', [App\Http\Controllers\StudsController::class, 'studs_search'])->name('radzes-meklet');
  Route::get('/radzes/getBrandList', [App\Http\Controllers\StudsController::class, 'studs_getBrands']);

  //SALE POSITIONS
  Route::get('/akcijas', [App\Http\Controllers\TopTireController::class, 'index'])->name('sale-tires');
  Route::get('/akcijas/category/{ct}', [App\Http\Controllers\TopTireController::class, 'filter'])->name('sale-tires-search');

  Route::post('/changeSeason', [App\Http\Controllers\TopTireController::class, 'changeSeason']);

// Noklusējuma lapas

// Pieraksts

  Route::get('/testpage', function() {
    $dat = strtotime("2023-10-30 46");
    return date('d.m.Y', $dat);
  });

  Route::get('/pieraksts', [App\Http\Controllers\Records\RecordController::class, 'index'])->name('pieraksts');
  Route::match(['GET', 'POST'], '/pieraksts/cancel={id}', [App\Http\Controllers\Records\RecordController::class, 'cancelSlot'])->name('cancelSlot');
  Route::post('/pieraksts/getSlotInfo', [App\Http\Controllers\Records\RecordController::class, 'getSlotInfo']);
  Route::post('/pieraksts/fillSlot', [App\Http\Controllers\Records\RecordController::class, 'fillSlot']);
  Route::post('/pieraksts/showMobileQueues', [App\Http\Controllers\Records\RecordController::class, 'showMobileQueues']);

  Route::middleware('auth')->group(function() {
    Route::get('/pieraksts/print/{office}/{date}', [App\Http\Controllers\Records\RecordController::class, 'reservations_print'])->name('pieraksts.print');
    Route::get('/pieraksts/rezervacijas/date={date}', [App\Http\Controllers\Records\RecordController::class, 'reservations'])->name('rezervacijas.date');
    Route::get('/pieraksts/rezervacijas', [App\Http\Controllers\Records\RecordController::class, 'reservations'])->name('rezervacijas');
    Route::post('/pieraksts/rezervacijas/editSlot', [App\Http\Controllers\Admin\Records\RecordController::class, 'reservations_ajax']);
    Route::get('/pieraksts/darba-laiki', [App\Http\Controllers\Records\RecordController::class, 'times'])->name('laiki');
    Route::post('/pieraksts/rezervacijas/getTimes', [App\Http\Controllers\Records\RecordController::class, 'getTimes']);
    Route::post('/pieraksts/rezervacijas/changeTimes', [App\Http\Controllers\Records\RecordController::class, 'changeTime'])->name('changeTime');
    Route::get('/pieraksts/rezervacijas/cancelTimeChanges', [App\Http\Controllers\Records\RecordController::class, 'cancelTimeChanges'])->name('cancelTimeChanges');
    Route::get('/pieraksts/rezervacijas/saveTimeChanges', [App\Http\Controllers\Records\RecordController::class, 'saveTimeChanges'])->name('saveTimeChanges');
  });

//

  //Route::get('/pakalpojumi', [App\Http\Controllers\HomeController::class, 'services'])->name('pakalpojumi');
//  Route::get('/kondicionieris', [App\Http\Controllers\HomeController::class, 'conditioner'])->name('kondicionieris');

//  Route::get('/kontakti', [App\Http\Controllers\HomeController::class, 'contacts'])->name('contacts');
  Route::get('/paskaidrojumi', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
//  Route::get('/internet-veikals', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
  Route::get('/kalkulators', function() {
    return view('components.calculator');
  });
//Route::get('/moto_trans', [App\Http\Controllers\HomeController::class, 'moto_terms'])->name('moto_trans');

// Klienta daļa

  Route::middleware('auth')->prefix('my-account')->group(function() {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'my_account'])->name('my-account');
    Route::get('/identity', [App\Http\Controllers\HomeController::class, 'identity'])->name('identity');
    Route::get('/address', [App\Http\Controllers\HomeController::class, 'address'])->name('address');
    Route::get('/history', [App\Http\Controllers\HomeController::class, 'history'])->name('history');
    Route::get('/order-slip', [App\Http\Controllers\HomeController::class, 'order_slip'])->name('order-slip');
  });

// Sinhronizācijas

  Route::get('/sync/all', [App\Http\Controllers\SyncController::class, 'sync_all'])->name('sync-all');
  Route::get('/sync/accrual', [App\Http\Controllers\SyncController::class, 'accrual'])->name('accrual-sync');
  Route::get('/sync/goodyear', [App\Http\Controllers\SyncController::class, 'gy'])->name('gy-sync');
  Route::get('/sync/i3-auto', [App\Http\Controllers\SyncController::class, 'i3auto'])->name('i3-sync');
  Route::get('/sync/i3-alloy-rims', [App\Http\Controllers\SyncController::class, 'i3autoalloyrims'])->name('i3-alloy-rims');
  Route::get('/sync/i3-moto', [App\Http\Controllers\SyncController::class, 'i3moto'])->name('i3-moto');
  Route::get('/sync/i3-quadr', [App\Http\Controllers\SyncController::class, 'i3quadr'])->name('i3-quadr');
  Route::get('/sync/i3-big', [App\Http\Controllers\SyncController::class, 'i3big'])->name('i3-big');
  Route::get('/sync/i3-agro', [App\Http\Controllers\SyncController::class, 'i3agro'])->name('i3-agro');
  Route::get('/sync/starco', [App\Http\Controllers\SyncController::class, 'starco'])->name('starco');
  Route::get('/sync/rz-auto', [App\Http\Controllers\SyncController::class, 'rzauto'])->name('rz-auto');
  Route::get('/sync/duell-moto', [App\Http\Controllers\SyncController::class, 'duellmoto'])->name('duellmoto');
  Route::get('/sync/duell-quadr', [App\Http\Controllers\SyncController::class, 'duellquadr'])->name('duellquadr');
  Route::get('/sync/rz-auto/show', [App\Http\Controllers\SyncController::class, 'rzautoshow']);
  Route::get('/sync/i3/show', [App\Http\Controllers\SyncController::class, 'i3show']);

// XML Ģenerēšana (Salidzini.lv/Kurpirkt.lv)

//Route::get('')
//Route::get('')

// Grozs

  Route::middleware('checkcart')->group(function() {
    Route::match(['GET', 'POST'],'/grozs', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
    Route::match(['GET', 'POST'], '/pasutijums', [App\Http\Controllers\CartController::class, 'order'])->name('order');
    Route::get('/pasutijums/success/{id}', [App\Http\Controllers\CartController::class, 'order_success'])->name('order.success');
  });
  Route::get('/pasutijums/done', [App\Http\Controllers\CartController::class, 'order_done'])->name('order.done');
  Route::get('/grozs/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
  Route::get('/pasutijums/print/{id}', [App\Http\Controllers\CartController::class, 'printCart'])->name('order.printCart');
  Route::post('/checkShipping', [App\Http\Controllers\CartController::class, 'checkShipping']);
  Route::post('/checkFitting', [App\Http\Controllers\CartController::class, 'checkFitting']);
  Route::get('/cart/empty', function() {
    \Cart::destroy();

    return redirect()->back();
  })->name('cart.empty');
  Route::get('/cart/ajaxRefresh', [App\Http\Controllers\CartController::class, 'ajaxRefresh'])->name('cart.ajaxRefresh');
  Route::post('/cart/ajaxChangeQty', [App\Http\Controllers\CartController::class, 'ajaxChangeQty'])->name('cart.ajaxChangeQty');
  Route::post('/cart/ajaxQtyUp', [App\Http\Controllers\CartController::class, 'ajaxQtyUp'])->name('cart.ajaxQtyUp');
  Route::post('/cart/ajaxQtyDown', [App\Http\Controllers\CartController::class, 'ajaxQtyDown'])->name('cart.ajaxQtyDown');

  Route::post('/accrualOrder', [App\Http\Controllers\HomeController::class, 'accrualOrder'])->name('accrualOrder');

// Salidzini.lv / Kurpirkt.lv XML Ģenerators
  Route::get('/xml/salidzini', [App\Http\Controllers\Admin\SettingsController::class, 'salidzini'])->name('xml.salidzini');
  Route::get('/xml/kurpirkt', [App\Http\Controllers\Admin\SettingsController::class, 'kurpirkt'])->name('xml.kurpirkt');

  Route::get('/checkPromos', [App\Http\Controllers\Admin\PromoCodeController::class, 'checkPromos']);

  Route::get('/analytics', function() {
    return view('analytics');
  });

  Route::get('/countSchedule', function() {
    $group = '120363157143688336@g.us';
    $slots = \App\Models\Slot::where('date', date('Y-m-d'))->where('status', 1)->count();

    $text = ($slots > 1) ? 'mašīnas' : 'mašīna';

    $cURLConnection = curl_init();
    $url = 'http://api.textmebot.com/send.php?recipient=' . $group . '&apikey=d6nsRWNp1xpc&text=' . date('Y-m-d') . '%20-%20Pierakstā%20' . $slots . '%20' . $text;
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_exec($cURLConnection);
    curl_close($cURLConnection);

    dd($slots);
  });

  //  ROUTES FOR TESTING PURPOSES
  Route::get('/testing', function() {
    return view('testing');
  });

  Route::get('/testing4', [App\Http\Controllers\HomeController::class, 'login']);

  Route::get('/testing1', [App\Http\Controllers\HomeController::class, 'dragNdrop']);
  Route::post('/testing1', [App\Http\Controllers\HomeController::class, 'dragNdrop']);
  Route::get('/testing2', [App\Http\Controllers\HomeController::class, 'changeArticles']);
  Route::post('/testing2', [App\Http\Controllers\HomeController::class, 'changeArticles']);
  Route::middleware('auth')->get('/testing3', [App\Http\Controllers\HomeController::class, 'fastOrder']);
  Route::middleware('auth')->post('/getLinks', [App\Http\Controllers\HomeController::class, 'getLinks']);

  Route::get('queuetest', [App\Http\Controllers\HomeController::class, 'queuetest']);
  Route::post('queuetest', [App\Http\Controllers\HomeController::class, 'queuetest']);

  // END ROUTES FOR TESTING PURPOSES
  Route::get('/{page}', [App\Http\Controllers\HomeController::class, 'pages']);

});

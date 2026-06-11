<?php

use App\Http\Controllers\Admin\PierakstsAdminController;
use App\Http\Controllers\PierakstsController;
use App\Http\Controllers\SlotReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Публичная запись на услуги (бывш. /pieraksts в старом web.php)
Route::prefix('pieraksts')->group(function () {
    Route::get('/', [PierakstsController::class, 'index'])->name('pieraksts');
    Route::get('/calendar', [PierakstsController::class, 'calendar'])->name('pieraksts.calendar');
    Route::post('/', [PierakstsController::class, 'store'])->name('pieraksts.store');

    // Отмена по коду из SMS/email (бывш. /pieraksts/cancel={id})
    Route::get('/cancel/{code}', [PierakstsController::class, 'cancelForm'])->name('pieraksts.cancel');
    Route::post('/cancel/{code}', [PierakstsController::class, 'cancelConfirm'])->name('pieraksts.cancel.confirm');

    // Мягкая резервация слота на время заполнения формы
    Route::post('/reserve-slot', [SlotReservationController::class, 'reserve'])->name('pieraksts.reserve');
    Route::post('/extend-reservation', [SlotReservationController::class, 'extend'])->name('pieraksts.extend');
    Route::post('/cancel-reservation', [SlotReservationController::class, 'cancel'])->name('pieraksts.release');
    Route::post('/check-slot-availability', [SlotReservationController::class, 'checkAvailability'])->name('pieraksts.check');
});

// Админка (HTTP Basic через ADMIN_USER/ADMIN_PASSWORD, см. AdminBasicAuth)
Route::prefix('admin')->middleware('admin.basic')->group(function () {
    Route::get('/pieraksts', [PierakstsAdminController::class, 'index'])->name('admin.pieraksts');
    Route::get('/pieraksts/date={date}', [PierakstsAdminController::class, 'index'])->name('admin.pieraksts.date');
    Route::post('/pieraksts/block', [PierakstsAdminController::class, 'block'])->name('admin.pieraksts.block');
    Route::post('/pieraksts/unblock', [PierakstsAdminController::class, 'unblock'])->name('admin.pieraksts.unblock');
    Route::post('/pieraksts/comment', [PierakstsAdminController::class, 'comment'])->name('admin.pieraksts.comment');
    Route::post('/pieraksts/cancel-booking', [PierakstsAdminController::class, 'cancelBooking'])->name('admin.pieraksts.cancel');
});

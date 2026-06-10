<?php

use App\Services\SlotReservationService;
use Illuminate\Support\Facades\Schedule;

// Очистка истёкших резерваций слотов (бывш. slots:clear-expired-reservations)
Schedule::call(fn () => app(SlotReservationService::class)->releaseExpired())
    ->everyMinute()
    ->name('slots-release-expired')
    ->withoutOverlapping();

<?php

namespace App\Support;

use App\Models\Service;
use App\Models\WorkingDay;

/**
 * «Половинные» очереди: день делится между AC (легковые) и moto слотами.
 * Перенос halfSlotDisplayRole / validateHalfSlotBooking из старого RecordController.
 *
 * Семантика сохранена: ac_toggle/moto_toggle считаются включёнными,
 * когда значение НЕ NULL (так хранила старая БД).
 */
class HalfSlotRules
{
    public const ROLE_BLOCKED = 'blocked';
    public const ROLE_AC = 'ac';
    public const ROLE_MOTO = 'moto';
    public const ROLE_LEGACY_EVEN_FREE = 'legacy_even_free';

    /** Роль слота по позиции (1-based) или null для обычного дня. */
    public static function role(int $position, WorkingDay $day): ?string
    {
        if (! $day->is_half) {
            return null;
        }

        $hasAc = $day->ac_toggle !== null;
        $hasMoto = $day->moto_toggle !== null;

        if ($hasAc && $hasMoto) {
            if ($position % 2 === 1) {
                return self::ROLE_BLOCKED;
            }

            return match ($position % 4) {
                0 => self::ROLE_AC,
                2 => self::ROLE_MOTO,
                default => self::ROLE_BLOCKED,
            };
        }

        if ($position % 2 === 1) {
            return $hasMoto ? self::ROLE_MOTO : self::ROLE_BLOCKED;
        }

        return $hasAc ? self::ROLE_AC : self::ROLE_LEGACY_EVEN_FREE;
    }

    /** @return string|null текст ошибки, если бронь в этот слот запрещена */
    public static function validateBooking(int $position, ?WorkingDay $day, ?int $serviceId): ?string
    {
        if (! $day) {
            return null;
        }

        $role = self::role($position, $day);
        if ($role === null || $role === self::ROLE_LEGACY_EVEN_FREE) {
            return null;
        }

        if ($role === self::ROLE_BLOCKED) {
            return 'Šis laiks nav pieejams pierakstam.';
        }

        $service = $serviceId ? Service::find($serviceId) : null;

        if ($role === self::ROLE_AC && (! $service || ! $service->allows_car)) {
            return 'Izvēlētais pakalpojums neatbilst AC laikam.';
        }
        if ($role === self::ROLE_MOTO && (! $service || ! $service->allows_moto)) {
            return 'Izvēlētais pakalpojums neatbilst moto laikam.';
        }

        return null;
    }
}

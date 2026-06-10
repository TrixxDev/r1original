<?php

namespace App\Helper;

use Illuminate\Database\Eloquent\Builder;

class TireAvailabilityFilter
{
    public static function parseSelected(string $availability): array
    {
        if ($availability === '') {
            return [];
        }

        return array_values(array_intersect(
            ['green', 'yellow', 'red'],
            preg_split('/[\s+]+/', $availability, -1, PREG_SPLIT_NO_EMPTY)
        ));
    }

    public static function applyToAutoQuery(Builder $query, string $availability, string $ownStock, string $partnerStock): void
    {
        self::applyOrFilter($query, $availability, [
            'green' => function ($subQuery) use ($ownStock) {
                $subQuery->whereRaw("{$ownStock} > 0");
            },
            'yellow' => function ($subQuery) use ($ownStock, $partnerStock) {
                $subQuery->whereRaw("{$ownStock} <= 0")->whereRaw("{$partnerStock} > 0");
            },
            'red' => function ($subQuery) use ($ownStock, $partnerStock) {
                $subQuery->whereRaw("{$ownStock} <= 0")->whereRaw("{$partnerStock} <= 0");
            },
        ]);
    }

    public static function applyToMotoQuery(Builder $query, string $availability, string $partnerStock): void
    {
        self::applyOrFilter($query, $availability, [
            'green' => function ($subQuery) {
                $subQuery->where('moto_tires.quantity', '>', 0);
            },
            'yellow' => function ($subQuery) use ($partnerStock) {
                $subQuery->where('moto_tires.quantity', '<=', 0)->whereRaw("{$partnerStock} > 0");
            },
            'red' => function ($subQuery) use ($partnerStock) {
                $subQuery->where('moto_tires.quantity', '<=', 0)->whereRaw("{$partnerStock} <= 0");
            },
        ]);
    }

    public static function applyToQuadrQuery(Builder $query, string $availability): void
    {
        self::applyOrFilter($query, $availability, [
            'green' => function ($subQuery) {
                $subQuery->where('quadr_tires.quantity', '>', 0);
            },
            'yellow' => function ($subQuery) {
                $subQuery->where('quadr_tires.quantity', '<=', 0)
                    ->whereRaw('quadr_tires.tire_id IN (SELECT tire_id FROM quadr_stock WHERE quantity > 0)');
            },
            'red' => function ($subQuery) {
                $subQuery->where('quadr_tires.quantity', '<=', 0)
                    ->whereRaw('quadr_tires.tire_id NOT IN (SELECT tire_id FROM quadr_stock WHERE quantity > 0)');
            },
        ]);
    }

    private static function applyOrFilter(Builder $query, string $availability, array $conditions): void
    {
        $parts = self::parseSelected($availability);

        if ($parts === [] || count($parts) === 3) {
            return;
        }

        $query->where(function ($groupQuery) use ($parts, $conditions) {
            $first = true;

            foreach ($parts as $part) {
                if (!isset($conditions[$part])) {
                    continue;
                }

                if ($first) {
                    $groupQuery->where(function ($subQuery) use ($conditions, $part) {
                        $conditions[$part]($subQuery);
                    });
                    $first = false;
                    continue;
                }

                $groupQuery->orWhere(function ($subQuery) use ($conditions, $part) {
                    $conditions[$part]($subQuery);
                });
            }
        });
    }
}


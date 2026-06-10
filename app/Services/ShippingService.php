<?php

namespace App\Services;

class ShippingService
{
    /**
     * Получить все варианты доставки для разных типов шин
     *
     * @return array
     */
    public static function getOptions(): array
    {
        $shippingOptions = [
            'Autotire' => [
                'shipping' => self::getShippingOptions('autotire'),
                'fitting' => self::getFittingOptions('autotire'),
                'fitting_suv' => self::getFittingOptions('suv_autotire'),
            ],
            'Moto' => [
                'shipping' => self::getShippingOptions('moto'),
                'fitting' => self::getFittingOptions('moto'),
            ],
            'Quadr' => [
                'shipping' => self::getShippingOptions('autotire'),
                'fitting' => self::getFittingOptions('quadr'),
            ],
            'Bigtire' => [
                'shipping' => self::getShippingOptions('industrial'),
            ],
        ];

        return array_merge(['shippingDef' => config('app.settings.shippingDef')], $shippingOptions);
    }

    /**
     * Получить опции доставки для определенного типа шин
     *
     * @param string $type
     * @return array
     */
    private static function getShippingOptions(string $type): array
    {
            if ($type == 'moto') {
                $array = [
                    1 => config("app.settings.shipping_{$type}_one"),
                    2 => config("app.settings.shipping_{$type}_two"),
                    3 => config("app.settings.shipping_{$type}_many"),
		];
	    } else {
		$array = [
		    1 => config("app.settings.shipping_{$type}_one"),
                    2 => config("app.settings.shipping_{$type}_two"),
                    4 => config("app.settings.shipping_{$type}_four"),
                    5 => config("app.settings.shipping_{$type}_many"),
	        ];
	    }
	    return $array;
    }

    /**
     * Получить опции монтажа для определенного типа шин
     *
     * @param string $type
     * @return array
     */
    private static function getFittingOptions(string $type): array
    {
        $fittingConfig = [];
	    if ($type == 'autotire' || $type == 'suv_autotire') {
            for ($i = 16; $i <= 21; $i++) {
                $fittingConfig[$i] = [
                    1 => config("app.settings.fitting_{$type}_{$i}_one"),
                    2 => config("app.settings.fitting_{$type}_{$i}_two"),
                    4 => config("app.settings.fitting_{$type}_{$i}_four"),
                ];
            }
        } else if ($type == 'moto') {
            $fittingConfig = [
                1 => config("app.settings.fitting_{$type}_one"),
                2 => config("app.settings.fitting_{$type}_two"),
            ];
        } else {
            $fittingConfig = [
                1 => config("app.settings.fitting_{$type}_one"),
                2 => config("app.settings.fitting_{$type}_two"),
                4 => config("app.settings.fitting_{$type}_four"),
            ];
        }
        return $fittingConfig;
    }
    
    /**
     * Определить, является ли шина внедорожной (SUV)
     *
     * @param int $width Ширина шины
     * @param int $height Высота шины
     * @param int $size Диаметр шины
     * @param float $radiusBorder Граничное значение радиуса
     * @return bool
     */
    public function isSuvTire($width, $height, $size, $radiusBorder = 360.7): bool
    {
        $radius = (($width * ($height / 100)) * 2) + ($size * 25.4);
        return ($radius / 2) >= $radiusBorder;
    }
    
    /**
     * Рассчитать стоимость монтажа
     *
     * @param string $category Категория шины
     * @param int $size Размер шины
     * @param int $quantity Количество шин
     * @param bool $suvTire Флаг внедорожной шины
     * @return float Стоимость монтажа
     */
    /**
     * Тарифные группы монтажа auto: в конфиге только R16–R21.
     * Меньшие диаметры (R13–R15) — по цене R16.
     */
    private static function resolveAutotireFittingSize(int $size): int
    {
        if ($size <= 16) {
            return 16;
        }
        if ($size <= 18) {
            return 17;
        }
        if ($size <= 20) {
            return 19;
        }

        return 21;
    }

    public function calculateFittingPrice($category, $size, $quantity, $suvTire)
    {
        $options = self::getOptions();

        if ($category == 'Autotire') {
            $size = (int) $size;
            $quantity = (int) $quantity;
            $fittingKey = $suvTire ? 'fitting_suv' : 'fitting';
            $sizeBucket = self::resolveAutotireFittingSize($size);

            return (float) ($options[$category][$fittingKey][$sizeBucket][$quantity] ?? 0.0);
        }

        if (!isset($options[$category]['fitting'])) {
            return 0.0;
        }

        return (float) ($options[$category]['fitting'][(int) $quantity] ?? 0.0);
    }
} 

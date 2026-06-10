<?php

namespace App\Services\Shop;

use App\Models\Autotire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Filtrēšanas loģika kā AutoTireController::api_tires, JSON API bez HTML.
 */
class AutoTireCatalogService
{
    public function paginate(Request $request, int $season, int $perPage = 40): LengthAwarePaginator
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(120, max(1, (int) $request->input('per_page', $perPage)));

        $codePayload = $this->parseCodeFilter($request);

        $d1 = ($request->d1 === 'Visi' || $request->d1 === null) ? '' : $request->d1;
        $d2 = ($request->d2 === 'Visi' || $request->d2 === null) ? '' : $request->d2;
        $d3 = ($request->d3 === 'Visi' || $request->d3 === null) ? '' : $request->d3;

        $availability = '';
        if ($request->filled('availability')) {
            $availability = implode('+', array_filter(explode(' ', $request->availability)));
        }

        $currBrand = ($request->brand === 'Ražotājs' || $request->brand === null || $request->brand === '') ? '' : $request->brand;

        $selectedTires = array_filter(array_map('trim', explode(',', (string) $request->input('selected', ''))));
        $topTires = filter_var($request->input('top'), FILTER_VALIDATE_BOOLEAN);
        $show_selected = filter_var($request->input('show_selected'), FILTER_VALIDATE_BOOLEAN);

        if ($request->filled('fastsearch')) {
            $splited = $this->splitInput($request->fastsearch);
            $d1 = $splited['d1'];
            $d2 = $splited['d2'];
            $d3 = $splited['d3'];
        }

        $selectedTypes = array_filter(explode(' ', (string) $request->input('type', '')));

        $typeConditions = [
            1 => ['auto_tires.type', '=', 1],
            2 => ['auto_tires.type', '=', 2],
            3 => ['auto_tires.type', '=', 3],
            4 => ['auto_tires.type', '=', 4],
        ];

        $selectedFuel = array_filter(explode(' ', (string) $request->input('fuel', '')));
        $selectedWet = array_filter(explode(' ', (string) $request->input('wet', '')));
        $selectedNoise = array_filter(explode(' ', (string) $request->input('noise', '')));

        $fuelConditions = [
            'A' => ['auto_tires.eco', '=', 'A'],
            'B' => ['auto_tires.eco', '=', 'B'],
            'C' => ['auto_tires.eco', '=', 'C'],
            'D' => ['auto_tires.eco', '=', 'D'],
            'E' => ['auto_tires.eco', '=', 'E'],
            'F' => ['auto_tires.eco', '=', 'F'],
            'G' => ['auto_tires.eco', '=', 'G'],
        ];

        $wetConditions = [
            'A' => ['auto_tires.wet', '=', 'A'],
            'B' => ['auto_tires.wet', '=', 'B'],
            'C' => ['auto_tires.wet', '=', 'C'],
            'D' => ['auto_tires.wet', '=', 'D'],
            'E' => ['auto_tires.wet', '=', 'E'],
            'F' => ['auto_tires.wet', '=', 'F'],
            'G' => ['auto_tires.wet', '=', 'G'],
        ];

        $noiseConditions = [
            'A' => ['auto_tires.noise', 'LIKE', '%A%'],
            'B' => ['auto_tires.noise', 'LIKE', '%B%'],
            'C' => ['auto_tires.noise', 'LIKE', '%C%'],
        ];

        $tires = Autotire::selectRaw('auto_tires.*, auto_tires.quantity as tire_quantity, auto_treads.*, (SELECT SUM(quantity) FROM auto_stock WHERE auto_stock.tire_id = auto_tires.tire_id) as stock_quantity, auto_brands.title as api_brand_title, auto_brands.slug as api_brand_slug')
            ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->when($currBrand, function ($query) use ($currBrand) {
                $query->where('auto_brands.slug', Str::slug($currBrand));
            })->when($d1, function ($query) use ($d1) {
                $query->where('d1', $d1);
            })->when($d2, function ($query) use ($d2) {
                $query->where('d2', $d2);
            })->when($d3, function ($query) use ($d3) {
                $query->where('d3', $d3);
            })->when($availability, function ($query) use ($availability) {
                switch ($availability) {
                    case 'green':
                        $query->where('auto_tires.quantity', '>', 0);
                        break;
                    case 'green+yellow':
                        $query->where(function ($query) {
                            $query->where('auto_tires.quantity', '>', 0)
                                ->orWhere(function ($query) {
                                    $query->whereRaw('auto_tires.tire_id IN (SELECT tire_id FROM auto_stock WHERE quantity > 0)')
                                        ->where('auto_tires.quantity', '=', 0);
                                });
                        });
                        break;
                    case 'green+red':
                        $query->where(function ($query) {
                            $query->where('auto_tires.quantity', '>', 0);
                            $query->orWhere(function ($query) {
                                $query->where('auto_tires.quantity', '=', 0);
                                $query->whereRaw('auto_tires.tire_id NOT IN (SELECT tire_id FROM auto_stock WHERE quantity > 0)');
                            });
                        });
                        break;
                    case 'yellow':
                        $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '>', 0);
                        break;
                    case 'yellow+red':
                        $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '>=', 0);
                        break;
                    case 'red':
                        $query->where('auto_tires.quantity', '<=', 0)->having('stock_quantity', '<=', 0);
                        break;
                }
            })->when(! empty($codePayload['combinations']), function ($query) use ($codePayload) {
                $codeCombinations = $codePayload['combinations'];
                $query->where(function ($query) use ($codeCombinations) {
                    if (count($codeCombinations) > 1) {
                        foreach ($codeCombinations as $combination) {
                            $query->orWhere('code', 'like', '%'.$combination.'%');
                        }
                    } else {
                        $query->where('code', 'like', '%'.$codeCombinations[0].'%');
                    }
                });
            })->when($selectedTypes, function ($query) use ($typeConditions, $selectedTypes) {
                $query->where(function ($query) use ($selectedTypes, $typeConditions) {
                    $firstCondition = true;
                    foreach ($selectedTypes as $type) {
                        if (isset($typeConditions[(int) $type])) {
                            $condition = $typeConditions[(int) $type];
                            if ($firstCondition) {
                                $query->where(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                                $firstCondition = false;
                            } else {
                                $query->orWhere(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                            }
                        }
                    }
                });
            })->when($selectedFuel, function ($query) use ($fuelConditions, $selectedFuel) {
                $query->where(function ($query) use ($selectedFuel, $fuelConditions) {
                    $firstCondition = true;
                    foreach ($selectedFuel as $type) {
                        if (isset($fuelConditions[$type])) {
                            $condition = $fuelConditions[$type];
                            if ($firstCondition) {
                                $query->where(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                                $firstCondition = false;
                            } else {
                                $query->orWhere(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                            }
                        }
                    }
                });
            })->when($selectedWet, function ($query) use ($wetConditions, $selectedWet) {
                $query->where(function ($query) use ($selectedWet, $wetConditions) {
                    $firstCondition = true;
                    foreach ($selectedWet as $type) {
                        if (isset($wetConditions[$type])) {
                            $condition = $wetConditions[$type];
                            if ($firstCondition) {
                                $query->where(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                                $firstCondition = false;
                            } else {
                                $query->orWhere(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                            }
                        }
                    }
                });
            })->when($selectedNoise, function ($query) use ($noiseConditions, $selectedNoise) {
                $query->where(function ($query) use ($selectedNoise, $noiseConditions) {
                    $firstCondition = true;
                    foreach ($selectedNoise as $type) {
                        if (isset($noiseConditions[$type])) {
                            $condition = $noiseConditions[$type];
                            if ($firstCondition) {
                                $query->where(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                                $firstCondition = false;
                            } else {
                                $query->orWhere(function ($query) use ($condition) {
                                    call_user_func_array([$query, 'where'], $condition);
                                });
                            }
                        }
                    }
                });
            })->when($show_selected && count($selectedTires), function ($query) use ($selectedTires) {
                $query->whereIn('tire_id', $selectedTires);
            })->when($topTires, function ($query) {
                $query->where('top', 1);
            })->where('auto_treads.season', $season)
            ->where('auto_tires.visible_users', '<>', 0)
            ->orderBy('d3', 'ASC')
            ->orderBy('d1', 'ASC')
            ->orderBy('d2', 'ASC')
            ->orderBy('price2', 'DESC');

        return $tires->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Fasetu izmēri: katrai dimensijai atļautās vērtības, kas pastāv kopā ar izvēlētajām pārējām (vai bez tām).
     *
     * @return array{d1: array<int, string>, d2: array<int, string>, d3: array<int, string>}
     */
    public function facetSizes(?string $selD1, ?string $selD2, ?string $selD3, int $season): array
    {
        $norm = static function (?string $v): ?string {
            if ($v === null || $v === '' || $v === 'Visi') {
                return null;
            }

            return (string) $v;
        };

        $f1 = $norm($selD1);
        $f2 = $norm($selD2);
        $f3 = $norm($selD3);

        $d1Values = $this->distinctFacetValues('d1', $season, null, $f2, $f3);
        $d2Values = $this->distinctFacetValues('d2', $season, $f1, null, $f3);
        $d3Values = $this->distinctFacetValues('d3', $season, $f1, $f2, null);

        $prependVisi = static function (array $values): array {
            return array_values(array_merge(['Visi'], $values));
        };

        return [
            'd1' => $prependVisi($d1Values),
            'd2' => $prependVisi($d2Values),
            'd3' => $prependVisi($d3Values),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function distinctFacetValues(string $column, int $season, ?string $filterD1, ?string $filterD2, ?string $filterD3): array
    {
        $colRef = 'auto_tires.'.$column;

        $q = $this->catalogFacetBaseQuery($season)
            ->when($filterD1 !== null, function ($query) use ($filterD1) {
                $query->where('auto_tires.d1', $filterD1);
            })
            ->when($filterD2 !== null, function ($query) use ($filterD2) {
                $query->where('auto_tires.d2', $filterD2);
            })
            ->when($filterD3 !== null, function ($query) use ($filterD3) {
                $query->where('auto_tires.d3', $filterD3);
            });

        $q->where($colRef, '<>', '')
            ->whereNotNull($colRef);

        return $q->select($colRef)
            ->orderByRaw("CASE WHEN {$colRef} >= 100 THEN 0 ELSE 1 END, {$colRef}")
            ->groupBy($colRef)
            ->get()
            ->pluck($column)
            ->map(function ($v) {
                return (string) $v;
            })
            ->values()
            ->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Autotire>
     */
    private function catalogFacetBaseQuery(int $season): \Illuminate\Database\Eloquent\Builder
    {
        return Autotire::query()
            ->join('auto_treads', 'auto_tires.make_id', '=', 'auto_treads.tread_id')
            ->join('auto_brands', 'auto_treads.brand_id', '=', 'auto_brands.brand_id')
            ->where('auto_treads.season', $season)
            ->where('auto_tires.visible_users', '<>', 0);
    }

    /**
     * @return array{combinations: array<int, string>|null}
     */
    private function parseCodeFilter(Request $request): array
    {
        if (! $request->filled('code')) {
            return ['combinations' => null];
        }

        $rawCodes = preg_split('/\s+/', trim((string) $request->code), -1, PREG_SPLIT_NO_EMPTY);
        $categorizedCodes = [];
        foreach ($rawCodes as $code) {
            if ($code === '') {
                continue;
            }
            if ($code === 'SOUND') {
                $categorizedCodes['SOUND'] = ['SOUND', 'ACOUSTIC', 'NCS', 'SCT'];
            } elseif ($code === 'XL') {
                $categorizedCodes['XL'] = ['XL', 'HL'];
            } else {
                $categorizedCodes[$code] = $code;
            }
        }

        $permutations = [];
        $hasArrays = false;

        foreach ($categorizedCodes as $key => $value) {
            if (is_array($value)) {
                $hasArrays = true;
                $temp = [];
                if ($permutations) {
                    foreach ($permutations as $perm) {
                        foreach ($value as $subcategory) {
                            $temp[] = $perm.'%'.$subcategory;
                        }
                    }
                } else {
                    $temp = $value;
                }
                $permutations = $temp;
            } else {
                if ($permutations) {
                    foreach ($permutations as &$perm) {
                        $perm .= '%'.$value;
                    }
                } else {
                    $permutations[] = $value;
                }
            }
        }

        if (count($categorizedCodes) === 0) {
            return ['combinations' => null];
        }

        if ($hasArrays) {
            $codeCombinations = $permutations;
        } else {
            $codeCombinations = [implode('%', $categorizedCodes)];
        }

        return ['combinations' => $codeCombinations];
    }

    public function hydrateTire(Autotire $tire, int $season): void
    {
        $tire->includeStock = true;
        $tire->fullTitle = $tire->api_brand_title.' '.$tire->t_title;
        $tire->fullSize = $tire->getFullSizeAttribute();
        $tire->fullName = $tire->fullTitle.' '.$tire->getFullSizeAttribute().' '.$tire->code.' '.$tire->li.$tire->si;
        $current_url = ($season == 1) ? 'vasaras-riepa' : 'ziemas-riepa';
        $tire->getUrl = route($current_url, [Str::slug($tire->api_brand_title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]);
        $tire->setAttribute('hydrated_cart_link', route($current_url, [$tire->api_brand_slug, str_replace('/', '_', $tire->t_title), $tire->tire_id]));
        $tire->lisiDesc = $tire->lisiDesc($tire->li, $tire->si);
        $tire->codeExplain = $tire->getCodeExplainAttribute();
        $tire->dotAvailable = $tire->getDotAvailableAttribute();
        $tire->stockAvailability = $tire->getStockAvailabilityAttribute();
        $tire->stockCount = $tire->getStockCount();
    }

    public function mapTireToListItem(Autotire $t, int $season): array
    {
        $this->hydrateTire($t, $season);

        return [
            'id' => (int) $t->tire_id,
            'full_title' => $t->fullTitle,
            'full_size' => $t->fullSize,
            'brand' => $t->api_brand_title,
            'tread_title' => $t->t_title,
            'code' => $t->code,
            'li_si' => $t->li.$t->si,
            'eco' => $t->eco,
            'wet' => $t->wet,
            'noise' => $t->noise,
            'price_store' => (string) $t->price1,
            'price_sale' => (string) $t->price2,
            'comment' => $t->comment,
            'availability_dot' => $t->dotAvailable,
            'stock_hint' => strip_tags((string) $t->stockAvailability),
            'image_url' => $this->treadImageAbsoluteUrl((int) $t->make_id),
            'detail_url' => url('/api/v1/shop/tires/'.$t->tire_id.'?season='.$season),
        ];
    }

    public function mapTireToDetail(Autotire $t, int $season): array
    {
        $row = $this->mapTireToListItem($t, $season);
        $row['code_explain_html'] = $t->codeExplain;
        $row['lisi_desc'] = $t->lisiDesc;

        return $row;
    }

    public function treadImageAbsoluteUrl(int $makeId): string
    {
        $jpg = public_path('storage/auto/tread/'.$makeId.'-o.jpg');
        $png = public_path('storage/auto/tread/'.$makeId.'-o.png');
        if (is_file($png)) {
            return asset('storage/auto/tread/'.$makeId.'-o.png');
        }
        if (is_file($jpg)) {
            return asset('storage/auto/tread/'.$makeId.'-o.jpg');
        }

        return asset('img/p/r1-logo.svg');
    }

    /**
     * @return array{d1: mixed, d2: mixed, d3: mixed}
     */
    public function splitInput(string $input): array
    {
        $input = str_replace(',', '.', $input);
        $d3_suffix = '';

        if (substr($input, -1) === 'C') {
            $input = substr($input, 0, -1);
            $d3_suffix = 'C';
        }

        if (preg_match('/^(\d{2})(\d{2}\.\d{1,2})(\d{2})$/', $input, $matches)) {
            $d1 = $matches[1];
            $d2 = $matches[2];
            $d3 = $matches[3].$d3_suffix;
        } else {
            if (preg_match('/^(\d{3})(\d{2})(\d{2})$/', $input, $matches)) {
                $d1 = $matches[1];
                $d2 = $matches[2];
                $d3 = $matches[3].$d3_suffix;
            } else {
                $d1 = 1;
                $d2 = 1;
                $d3 = 1;
            }
        }

        return compact('d1', 'd2', 'd3');
    }
}


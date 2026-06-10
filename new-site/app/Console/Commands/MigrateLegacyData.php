<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Перенос данных из старой БД r1 (соединение 'legacy', только чтение)
 * в новую схему. Команду можно запускать многократно — все записи
 * привязаны к старым ID (legacy_id / legacy_source) и обновляются, а не дублируются.
 *
 *   php artisan legacy:migrate                 # всё
 *   php artisan legacy:migrate --module=booking
 *   php artisan legacy:migrate --module=catalog --module=orders
 */
class MigrateLegacyData extends Command
{
    protected $signature = 'legacy:migrate
        {--module=* : users|booking|catalog|orders|misc (по умолчанию все)}
        {--chunk=500 : размер пачки чтения}';

    protected $description = 'Перенос данных из старой БД r1 в новую схему (идемпотентно)';

    private int $chunk = 500;

    /** Карта старых user.id → новых users.id */
    private array $userMap = [];

    public function handle(): int
    {
        $this->chunk = (int) $this->option('chunk');
        $modules = $this->option('module') ?: ['users', 'booking', 'catalog', 'orders', 'misc'];

        foreach ($modules as $module) {
            $method = 'migrate' . ucfirst($module);
            if (! method_exists($this, $method)) {
                $this->error("Неизвестный модуль: {$module}");

                return self::FAILURE;
            }
        }

        // users всегда первыми — на них ссылаются брони и заказы
        if (in_array('users', $modules) || array_intersect(['booking', 'orders'], $modules)) {
            $this->migrateUsers();
        }
        $this->loadUserMap();

        foreach (array_diff($modules, ['users']) as $module) {
            $this->{'migrate' . ucfirst($module)}();
        }

        $this->info('Готово.');

        return self::SUCCESS;
    }

    private function legacy(string $table)
    {
        return DB::connection('legacy')->table($table);
    }

    // ------------------------------------------------------------------
    // USERS
    // ------------------------------------------------------------------

    private function migrateUsers(): void
    {
        $count = 0;
        $this->legacy('users')->orderBy('id')->chunk($this->chunk, function ($rows) use (&$count) {
            foreach ($rows as $u) {
                DB::table('users')->updateOrInsert(
                    ['legacy_id' => $u->id],
                    [
                        'name' => $u->name,
                        'surname' => $u->surname ?: null,
                        'username' => $u->username ?: null,
                        'email' => $u->email,
                        'email_verified_at' => $u->email_verified_at,
                        'phone_number' => $u->phone_number,
                        'password' => $u->password, // bcrypt-хэши совместимы
                        'remember_token' => $u->remember_token,
                        'enabled' => (bool) $u->enabled,
                        'last_activity_at' => $u->lastActivityTime,
                        'created_at' => $u->created_at,
                        'updated_at' => $u->updated_at,
                    ]
                );
                $count++;
            }
        });
        $this->info("users: {$count}");
    }

    private function loadUserMap(): void
    {
        $this->userMap = DB::table('users')->whereNotNull('legacy_id')
            ->pluck('id', 'legacy_id')->all();
    }

    private function mapUser(?int $legacyId): ?int
    {
        if ($legacyId === null || $legacyId <= 0) {
            return null; // -1 = гость в старой системе
        }

        return $this->userMap[$legacyId] ?? null;
    }

    // ------------------------------------------------------------------
    // BOOKING: offices, services, queues, working_days, slots + bookings
    // ------------------------------------------------------------------

    private function migrateBooking(): void
    {
        foreach ($this->legacy('offices')->orderBy('office_id')->get() as $o) {
            DB::table('offices')->updateOrInsert(
                ['id' => $o->office_id],
                ['title' => $o->title, 'shipping' => $o->shipping]
            );
        }
        $this->info('offices: перенесены');

        foreach ($this->legacy('office_mobile_prefs')->get() as $p) {
            DB::table('office_mobile_prefs')->updateOrInsert(
                ['office_id' => $p->office_id],
                ['lift_slot_count' => $p->lift_slot_count, 'updated_at' => $p->updated_at]
            );
        }

        foreach ($this->legacy('services')->orderBy('service_id')->get() as $s) {
            DB::table('services')->updateOrInsert(
                ['id' => $s->service_id],
                [
                    'title' => $s->title,
                    'pdf_title' => $s->pdf_title,
                    'allows_storage' => (bool) $s->f_save,
                    'allows_car' => (bool) $s->f_ac,
                    'allows_moto' => (bool) $s->f_moto,
                    'enabled' => (bool) $s->enabled,
                ]
            );
        }
        $this->info('services: перенесены');

        foreach ($this->legacy('queues')->orderBy('queue_id')->get() as $q) {
            DB::table('queues')->updateOrInsert(
                ['id' => $q->queue_id],
                [
                    'office_id' => $q->office_id,
                    'title' => $q->title,
                    'is_visible' => (bool) $q->is_visible,
                    'is_public' => (bool) $q->is_public,
                    'sort_order' => (int) $q->iorder,
                    'time_open' => $this->toTime($q->timeopen),
                    'time_close' => $this->toTime($q->timeclose),
                    'weekend_time_open' => $this->toTime($q->wtimeopen),
                    'weekend_time_close' => $this->toTime($q->wtimeclose),
                    'notification_subject' => $q->notificationSubject,
                    'notification_email' => $q->notificationEmail,
                    'notification_cancel_email' => $q->notificationCancelEmail,
                    'notification_sms' => $q->notificationSMS,
                    'notification_schedule_sms' => $q->notificationScheduleSMS,
                    'notification_schedule_cancel_sms' => $q->notificationScheduleCancelSMS,
                ]
            );
        }
        $this->info('queues: перенесены');

        // workingdays → опубликованные, new_workingdays → черновики
        foreach ([['workingdays', false], ['new_workingdays', true]] as [$table, $isDraft]) {
            $count = 0;
            $this->legacy($table)->orderBy('workingday_id')->chunk($this->chunk, function ($rows) use ($isDraft, &$count) {
                foreach ($rows as $w) {
                    if (! $w->date || ! $w->queue_id) {
                        continue;
                    }
                    DB::table('working_days')->updateOrInsert(
                        ['queue_id' => $w->queue_id, 'date' => $w->date, 'is_draft' => $isDraft],
                        [
                            'time_open' => $this->toTime($w->timeopen),
                            'time_close' => $this->toTime($w->timeclose),
                            'time_step' => (int) ($w->timeStep ?: 15),
                            'is_opened' => (bool) $w->is_opened,
                            'is_half' => (bool) $w->is_half,
                            'ac_toggle' => $w->ac_toggle !== null ? (bool) $w->ac_toggle : null,
                            'moto_toggle' => $w->moto_toggle !== null ? (bool) $w->moto_toggle : null,
                        ]
                    );
                    $count++;
                }
            });
            $this->info("{$table}: {$count}");
        }

        $this->migrateSlots();
    }

    private function migrateSlots(): void
    {
        $validQueues = DB::table('queues')->pluck('id')->flip()->all();
        $stats = ['slots' => 0, 'bookings' => 0, 'skipped' => 0, 'conflicts' => 0];

        $this->legacy('slots')->orderBy('slot_id')->chunk($this->chunk, function ($rows) use ($validQueues, &$stats) {
            foreach ($rows as $s) {
                if (! $s->date || ! $s->queue_id || $s->iorder === null || ! isset($validQueues[$s->queue_id])) {
                    $stats['skipped']++;
                    continue;
                }

                $hasBooking = ! empty($s->takenby);
                if (! $hasBooking && empty($s->comment) && ! (int) $s->status) {
                    $stats['skipped']++; // пустая строка-мусор
                    continue;
                }

                // В старой slots не было UNIQUE — возможны дубли позиции.
                $existing = DB::table('slots')
                    ->where('date', $s->date)
                    ->where('queue_id', $s->queue_id)
                    ->where('position', $s->iorder)
                    ->first();

                if ($existing && (int) $existing->legacy_id !== (int) $s->slot_id) {
                    $stats['conflicts']++;
                    $this->warn("Дубль слота {$s->date} q{$s->queue_id} p{$s->iorder}: legacy {$s->slot_id} пропущен (оставлен {$existing->legacy_id})");
                    continue;
                }

                $slotId = DB::table('slots')->updateOrInsert(
                    ['legacy_id' => $s->slot_id],
                    [
                        'queue_id' => $s->queue_id,
                        'date' => $s->date,
                        'position' => $s->iorder,
                        'status' => $hasBooking ? Slot::STATUS_BOOKED : (int) $s->status,
                        'comment' => $s->comment,
                        'created_by' => $this->mapUser((int) $s->createuser),
                        'updated_by' => $this->mapUser((int) $s->edituser),
                        'created_at' => $this->toDateTime($s->createtime),
                        'updated_at' => $this->toDateTime($s->edittime),
                    ]
                );
                $newSlotId = DB::table('slots')->where('legacy_id', $s->slot_id)->value('id');
                $stats['slots']++;

                if ($hasBooking) {
                    $this->migrateBookingRow($s, $newSlotId);
                    $stats['bookings']++;
                }
            }
        });

        $this->info(sprintf(
            'slots: %d, bookings: %d, пропущено пустых: %d, конфликтов позиций: %d',
            $stats['slots'], $stats['bookings'], $stats['skipped'], $stats['conflicts']
        ));
    }

    private function migrateBookingRow(object $s, int $slotId): void
    {
        $data = json_decode($s->takenby, true) ?: [];

        $cancelCode = $s->cancel_id ?: ($data['cancelId'] ?? null) ?: ('legacy-' . $s->slot_id);
        $serviceId = isset($data['service']) && is_numeric($data['service']) ? (int) $data['service'] : null;
        if ($serviceId !== null && ! DB::table('services')->where('id', $serviceId)->exists()) {
            $serviceId = null;
        }

        DB::table('bookings')->updateOrInsert(
            ['slot_id' => $slotId],
            [
                'service_id' => $serviceId,
                'cancel_code' => $cancelCode,
                'car_brand' => $this->strOrNull($data['car_brand'] ?? null, 100),
                'car_model' => $this->strOrNull($data['car_model'] ?? null, 100),
                'license_plate' => $this->strOrNull($data['lic_plate'] ?? null, 20),
                'rims_with' => isset($data['rimsWith']) && in_array((string) $data['rimsWith'], ['1', '2'], true)
                    ? (int) $data['rimsWith'] : null,
                'phone_number' => $this->strOrNull($data['phone_number'] ?? null, 30),
                'email' => $this->strOrNull($data['email'] ?? null, 255),
                'customer_comment' => $data['user_comment'] ?? $data['comment'] ?? null,
                'discount' => $this->strOrNull($data['discount'] ?? null, 50),
                'is_mobile' => (bool) $s->is_mobile,
                'work_status' => (int) ($s->mobile_status ?? 0),
                'ic_status' => $s->ic_status,
                'planned_tasks' => $s->ic_planned_tasks,
                'lift_spot' => $s->lift_spot,
                'car_info' => $s->car_info_json,
                'car_info_vnr' => $s->car_info_vnr,
                'car_info_fetched_at' => $s->car_info_fetched_at,
                'car_info_source' => $s->car_info_source,
                // Исходный JSON целиком — формат старых записей менялся годами
                'legacy_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'created_by' => $this->mapUser((int) $s->createuser),
                'created_at' => $this->toDateTime($s->createtime),
            ]
        );
    }

    // ------------------------------------------------------------------
    // CATALOG: 7 старых семейств → единый каталог
    // ------------------------------------------------------------------

    /**
     * Конфигурация маппинга на категорию:
     * [brands_table, brand_pk, brand_title, models_table, model_pk, model_title,
     *  products_table, product_pk, stock_table|null, stock_fk, attrs-колонки]
     */
    private function catalogConfig(): array
    {
        return [
            'auto' => [
                'brands' => ['auto_brands', 'brand_id', 'title'],
                'models' => ['auto_treads', 'tread_id', 't_title', 'season' => 'season', 'type' => 't_type', 'comment' => 't_comment'],
                'products' => ['auto_tires', 'tire_id'],
                'stock' => ['auto_stock', 'tire_id'],
                'attrs' => ['eco', 'wet', 'noise', 'type'],
            ],
            'moto' => [
                'brands' => ['moto_brands', 'brand_id', 'title'],
                'models' => ['moto_treads', 'tread_id', 'title', 'comment' => 't_comment'],
                'products' => ['moto_tires', 'tire_id'],
                'stock' => ['moto_stock', 'tire_id'],
                'attrs' => ['is_camera', 'type', 'sep'],
            ],
            'quadr' => [
                'brands' => ['quadr_brands', 'brand_id', 'b_title'],
                'models' => ['quadr_treads', 'tread_id', 't_title', 'comment' => 't_comment'],
                'products' => ['quadr_tires', 'tire_id'],
                'stock' => ['quadr_stock', 'tire_id'],
                'attrs' => ['is_camera', 'sep', 'sep2'],
            ],
            'big' => [
                'brands' => ['bigtire_brands', 'brand_id', 'title'],
                'models' => ['bigtire_treads', 'tread_id', 'title', 'season' => 'season', 'comment' => 't_comment'],
                'products' => ['big_tires', 'tire_id'],
                'stock' => ['bigtire_stock', 'tire_id'],
                'attrs' => ['implemention', 'kind', 'axis', 'conditions', 'type', 'sep', 'sep2'],
            ],
            'rim' => [
                'brands' => ['rim_brands', 'brand_id', 'title'],
                'models' => ['rim_makes', 'make_id', 'title', 'season' => 'season', 'type' => 'type', 'comment' => 't_comment'],
                'products' => ['rims', 'rim_id'],
                'stock' => ['rim_stock', 'rim_id'],
                'attrs' => ['skr', 'pcd', 'et', 'dc', 'color'],
            ],
            'quadrim' => [
                'brands' => ['quadrim_brands', 'brand_id', 'b_title'],
                'models' => ['quadrim_makes', 'make_id', 't_title', 'season' => 'season', 'type' => 'type', 'comment' => 't_comment'],
                'products' => ['quadrims', 'rim_id'],
                'stock' => null,
                'attrs' => ['skr', 'pcd', 'et', 'color'],
            ],
            'stud' => [
                'brands' => ['studs_brands', 'brand_id', 'b_title'],
                'models' => ['studs_treads', 'tread_id', 't_title', 'comment' => 't_comment'],
                'products' => ['studs', 'stud_id'],
                'stock' => null,
                'attrs' => ['application', 'stud_length', 'stud_count'],
            ],
        ];
    }

    private function migrateCatalog(): void
    {
        foreach ($this->catalogConfig() as $category => $cfg) {
            $this->migrateCatalogCategory($category, $cfg);
        }
    }

    private function migrateCatalogCategory(string $category, array $cfg): void
    {
        [$brandTable, $brandPk, $brandTitle] = $cfg['brands'];
        [$modelTable, $modelPk, $modelTitle] = $cfg['models'];
        [$productTable, $productPk] = $cfg['products'];

        // Бренды
        foreach ($this->legacy($brandTable)->orderBy($brandPk)->get() as $b) {
            $title = $b->{$brandTitle} ?? '';
            DB::table('brands')->updateOrInsert(
                ['legacy_source' => "{$category}:{$b->{$brandPk}}"],
                [
                    'category' => $category,
                    'title' => $title,
                    'slug' => $this->uniqueSlug('brands', $category, $b->slug ?? null, $title, "{$category}:{$b->{$brandPk}}"),
                    'image' => $b->image ?? null,
                    'description' => $b->b_comment ?? null,
                    'sort_order' => (int) ($b->iorder ?? 0),
                ]
            );
        }
        $brandMap = $this->legacyMap('brands', $category);

        // Модели (протекторы)
        foreach ($this->legacy($modelTable)->orderBy($modelPk)->get() as $m) {
            $brandId = $brandMap["{$category}:{$m->brand_id}"] ?? null;
            if (! $brandId) {
                continue;
            }
            $title = $m->{$modelTitle} ?? '';
            DB::table('product_models')->updateOrInsert(
                ['legacy_source' => "{$category}:{$m->{$modelPk}}"],
                [
                    'brand_id' => $brandId,
                    'category' => $category,
                    'title' => $title,
                    'slug' => $this->uniqueSlug('product_models', $category, $m->slug ?? null, $title, "{$category}:{$m->{$modelPk}}"),
                    'season' => isset($cfg['models']['season']) ? $m->{$cfg['models']['season']} : null,
                    'vehicle_type' => isset($cfg['models']['type']) ? $m->{$cfg['models']['type']} : null,
                    'description' => isset($cfg['models']['comment']) ? $m->{$cfg['models']['comment']} : null,
                    'image' => $m->image ?? null,
                    'sort_order' => (int) ($m->iorder ?? 0),
                ]
            );
        }
        $modelMap = $this->legacyMap('product_models', $category);

        // Товары
        $count = 0;
        $this->legacy($productTable)->orderBy($productPk)
            ->chunk($this->chunk, function ($rows) use ($category, $cfg, $productPk, $modelMap, &$count) {
                foreach ($rows as $p) {
                    $modelId = $modelMap["{$category}:{$p->make_id}"] ?? null;
                    if (! $modelId) {
                        continue;
                    }

                    $attrs = [];
                    foreach ($cfg['attrs'] as $attr) {
                        if (isset($p->{$attr}) && $p->{$attr} !== '' && $p->{$attr} !== null) {
                            $attrs[$attr] = $p->{$attr};
                        }
                    }

                    DB::table('products')->updateOrInsert(
                        ['legacy_source' => "{$category}:{$p->{$productPk}}"],
                        [
                            'model_id' => $modelId,
                            'category' => $category,
                            'article' => $p->article ?? null,
                            'width' => $this->strOrNull($p->d1 ?? null, 30),
                            'profile' => $this->strOrNull($p->d2 ?? null, 30),
                            'diameter' => $this->strOrNull($p->d3 ?? null, 30),
                            'extra_dim' => $this->strOrNull($p->d4 ?? null, 30),
                            'load_index' => $this->strOrNull($p->li ?? null, 11),
                            'speed_index' => $this->strOrNull($p->si ?? null, 10),
                            'price_retail' => $this->toCents($p->price1 ?? null),
                            'price_partner' => $this->toCents($p->price2 ?? null),
                            'price_extra' => $this->toCents($p->price3 ?? null),
                            'is_offer' => (bool) ($p->offer ?? false),
                            'is_price_offer' => (bool) ($p->priceoffer ?? false),
                            'offer_price' => $this->strOrNull($p->offerPrice ?? null, 30),
                            'offer_text' => $this->strOrNull($p->offerText ?? null, 30),
                            'is_top' => (bool) ($p->top ?? false),
                            'is_used' => (bool) ($p->used ?? false),
                            'visible_users' => (int) ($p->visible_users ?? 1),
                            'visible_list' => (int) ($p->visible_list ?? 1),
                            'available' => (bool) ($p->available ?? true),
                            'comment' => $p->comment ?? null,
                            'admin_comment' => $p->acomment ?? null,
                            'code' => $this->strOrNull($p->code ?? null, 255),
                            'attrs' => $attrs ? json_encode($attrs, JSON_UNESCAPED_UNICODE) : null,
                            'ordered' => (int) ($p->ordered ?? 0),
                            'reserved' => (int) ($p->reserved ?? 0),
                            'updated_at' => $p->updated_at ?? now(),
                        ]
                    );

                    $productId = DB::table('products')
                        ->where('legacy_source', "{$category}:{$p->{$productPk}}")->value('id');

                    // Свои склады: NULL-офис (центральный) + Ulbroka(1) + Kalnciema(2)
                    foreach ([0 => $p->quantity ?? 0, 1 => $p->urs_quantity ?? 0, 2 => $p->krs_quantity ?? 0] as $officeId => $qty) {
                        DB::table('product_warehouse_stock')->updateOrInsert(
                            ['product_id' => $productId, 'office_id' => $officeId],
                            ['quantity' => (int) $qty]
                        );
                    }
                    $count++;
                }
            });
        $productMap = $this->legacyMap('products', $category);
        $this->info("{$category}: товаров {$count}");

        // Остатки поставщиков
        if ($cfg['stock']) {
            [$stockTable, $stockFk] = $cfg['stock'];
            $stockCount = 0;
            $this->legacy($stockTable)->orderBy('stock_id')
                ->chunk($this->chunk, function ($rows) use ($category, $stockFk, $productMap, &$stockCount) {
                    foreach ($rows as $st) {
                        $productId = $productMap["{$category}:{$st->{$stockFk}}"] ?? null;
                        if (! $productId) {
                            continue;
                        }
                        DB::table('supplier_stock')->updateOrInsert(
                            ['product_id' => $productId, 'supplier' => (string) $st->itype, 'article' => $st->article],
                            [
                                'quantity' => (int) $st->quantity,
                                'metadata' => $this->jsonOrNull($st->metadata ?? null),
                                'created_at' => $st->created_at,
                                'updated_at' => $st->updated_at,
                            ]
                        );
                        $stockCount++;
                    }
                });
            $this->info("{$category}: остатков поставщиков {$stockCount}");
        }
    }

    // ------------------------------------------------------------------
    // ORDERS
    // ------------------------------------------------------------------

    private function migrateOrders(): void
    {
        $count = 0;
        $this->legacy('orders_')->orderBy('id')->chunk($this->chunk, function ($rows) use (&$count) {
            foreach ($rows as $o) {
                DB::table('orders')->updateOrInsert(
                    ['legacy_id' => $o->id],
                    [
                        'order_number' => $o->order_number ?? $o->id,
                        'user_id' => $this->mapUser($o->user_id ? (int) $o->user_id : null),
                        'session_id' => $o->session_id,
                        'status' => (int) ($o->order_status ?? 0),
                        'customer_name' => $o->customer_name,
                        'customer_surname' => $o->customer_surname,
                        'email' => $o->email,
                        'phone_country_code' => $o->phone_country_code,
                        'phone_number' => $o->phone_number,
                        'company_reg_nr' => $o->company_reg_nr,
                        'company_pvn_nr' => $o->company_pvn_nr,
                        'company_name' => $o->company_name,
                        'company_address' => $o->company_address,
                        'comments' => $o->comments,
                        'car_details' => $o->car_details,
                        'email_notification' => $o->email_notification === '1',
                        'promo_code' => $o->promo_code ?: null,
                        'discount_type' => $o->discount_type,
                        'discount_value' => $o->discount_value,
                        // total_price в старой БД уже int (центы)
                        'total_price' => (int) ($o->total_price ?? 0),
                        'delivery_price' => $o->delivery_price !== null ? (int) $o->delivery_price : null,
                        'mounting_price' => $o->mounting_price !== null ? (int) $o->mounting_price : null,
                        'delivery_method' => $o->delivery_method !== null ? (int) $o->delivery_method : null,
                        'delivery_city' => $o->delivery_city,
                        'delivery_address' => $o->delivery_address,
                        'door_code' => $o->door_code,
                        'mounting_office_id' => $o->mounting_office !== null ? (int) $o->mounting_office : null,
                        'admin_info' => $o->admin_info,
                        'edited_by' => $this->mapUser($o->edituser ? (int) $o->edituser : null),
                        'legacy_details' => $this->jsonOrNull($o->order_details),
                        'expires_at' => $o->delete_at,
                        'created_at' => $o->created_at,
                        'updated_at' => $o->updated_at,
                    ]
                );

                $orderId = DB::table('orders')->where('legacy_id', $o->id)->value('id');
                $this->migrateOrderItems($orderId, $o->order_details);
                $this->migrateOrderPayment($orderId, $o);
                $count++;
            }
        });
        $this->info("orders: {$count}");
    }

    private function migrateOrderItems(int $orderId, ?string $detailsJson): void
    {
        $details = json_decode((string) $detailsJson, true);
        $products = $details['products'] ?? null;
        if (! is_array($products)) {
            return; // сырой JSON сохранён в orders.legacy_details
        }

        DB::table('order_items')->where('order_id', $orderId)->delete();

        foreach ($products as $p) {
            if (! is_array($p)) {
                continue;
            }
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => null, // связка с новым каталогом — отдельным шагом при необходимости
                'title' => $this->strOrNull($p['title'] ?? $p['name'] ?? null, 255) ?? 'Prece',
                'article' => $this->strOrNull($p['article'] ?? null, 255),
                'unit_price' => $this->toCents($p['price'] ?? 0) ?? 0,
                'quantity' => max(1, (int) ($p['quantity'] ?? 1)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function migrateOrderPayment(int $orderId, object $o): void
    {
        if ($o->payment_method === null && ! $o->paysera_transaction_id) {
            return;
        }

        DB::table('payments')->updateOrInsert(
            ['order_id' => $orderId, 'method' => (int) ($o->payment_method ?? 3)],
            [
                'status' => $o->payment_status ?: 'pending',
                'amount' => (int) ($o->total_price ?? 0),
                'provider' => $o->paysera_transaction_id ? 'paysera' : null,
                'transaction_id' => $o->paysera_transaction_id,
                'callback_data' => $this->jsonOrNull($o->paysera_callback_data),
                'created_at' => $o->created_at,
                'updated_at' => $o->updated_at,
            ]
        );
    }

    // ------------------------------------------------------------------
    // MISC: promo_codes, site_settings, sync_times, banners, codes, filters
    // ------------------------------------------------------------------

    private function migrateMisc(): void
    {
        foreach ($this->legacy('promo_codes')->orderBy('promo_id')->get() as $p) {
            DB::table('promo_codes')->updateOrInsert(
                ['code' => $p->code],
                [
                    'name' => $p->name,
                    'end_date' => $p->end_date,
                    'type' => $p->status === '1' ? 'percentage' : 'fixed',
                    'value' => (int) $p->value,
                    'active' => $p->active === '1',
                    'max_uses' => $p->can_use,
                    'used' => (int) $p->used,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ]
            );
        }

        foreach ($this->legacy('cart_config')->get() as $c) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $c->name],
                ['value' => $c->value, 'label' => $c->abbr]
            );
        }

        foreach ($this->legacy('sync_times')->get() as $s) {
            DB::table('sync_times')->updateOrInsert(
                ['name' => $s->name],
                ['updated_at' => $s->updated_at]
            );
        }

        foreach ($this->legacy('bannerimages')->get() as $b) {
            DB::table('banners')->updateOrInsert(
                ['id' => $b->id],
                ['name' => $b->name, 'url' => $b->url, 'enabled' => (bool) $b->enabled]
            );
        }

        foreach ($this->legacy('code')->get() as $c) {
            DB::table('tire_codes')->updateOrInsert(
                ['id' => $c->code_id],
                ['name' => $c->name, 'explanation' => $c->explanation]
            );
        }

        foreach ($this->legacy('filter_cars')->get() as $c) {
            DB::table('car_makes')->updateOrInsert(['id' => $c->car_id], ['title' => (string) $c->title]);
        }
        foreach ($this->legacy('filter_models')->get() as $m) {
            if (! DB::table('car_makes')->where('id', $m->carId)->exists()) {
                continue;
            }
            DB::table('car_models')->updateOrInsert(
                ['id' => $m->model_id],
                ['car_make_id' => $m->carId, 'title' => (string) $m->title]
            );
        }
        foreach ($this->legacy('filter_sizes')->get() as $s) {
            if (! DB::table('car_models')->where('id', $s->modelId)->exists()) {
                continue;
            }
            DB::table('car_wheel_sizes')->updateOrInsert(
                ['id' => $s->size_id],
                [
                    'car_model_id' => $s->modelId,
                    'r' => $s->r, 'j' => $s->j, 'j_min' => $s->jMin,
                    'et' => $s->et, 'skr' => $s->skr, 'pcd' => $s->pcd, 'd' => $s->d,
                ]
            );
        }

        $this->info('misc: promo_codes, site_settings, sync_times, banners, tire_codes, car_* перенесены');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function legacyMap(string $table, string $category): array
    {
        return DB::table($table)
            ->where('legacy_source', 'like', "{$category}:%")
            ->pluck('id', 'legacy_source')->all();
    }

    private function uniqueSlug(string $table, string $category, ?string $existing, string $title, string $legacySource): string
    {
        $slug = trim((string) $existing) ?: Str::slug($title);
        if ($slug === '') {
            $slug = 'item';
        }

        $taken = DB::table($table)
            ->where('category', $category)
            ->where('slug', $slug)
            ->where('legacy_source', '!=', $legacySource)
            ->exists();

        if ($taken) {
            $slug .= '-' . Str::afterLast($legacySource, ':');
        }

        return $slug;
    }

    /** Цена double EUR → int центы. В старой БД цены хранились как 45.5 (EUR). */
    private function toCents(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) $value * 100);
    }

    private function toTime(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (Throwable) {
            return null;
        }
    }

    private function toDateTime(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000')) {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (Throwable) {
            return null;
        }
    }

    private function strOrNull(mixed $value, int $max): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }

    private function jsonOrNull(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $value;
        }

        return json_encode(['raw' => $value], JSON_UNESCAPED_UNICODE);
    }
}

# R1 Riepas — новый сайт (r1new)

Переписывание сайта шиномонтажа R1 Riepas (Латвия) с нуля на Laravel 11.
Старый проект — Laravel 8, лежит отдельно (репозиторий `r1original`, на ПК — `E:\r1original`);
его БД `r1` (MySQL 5.7) подключена сюда как соединение **`legacy`** (только чтение).

Языки интерфейса: латышский (основной), русский, английский. Деньги — **int в центах**.
Два филиала: office 1 = Ulbroka (URS), office 2 = Kalnciema iela, Rīga (KRS).

## Документация (прочитай перед работой)

- `docs/БИЗНЕС_ЛОГИКА.md` — вся бизнес-логика старой системы (источник требований)
- `docs/КАРТА_ФУНКЦИОНАЛА.md` — все маршруты/модули старого сайта
- `docs/БАЗА_ДАННЫХ.md` — реальная схема старой БД (62 таблицы)
- `docs/НОВАЯ_СХЕМА_БД.md` — спроектированная новая схема (~25 таблиц) и маппинг старое→новое

## Что уже сделано

- **Миграции новой схемы** (`database/migrations/2026_06_10_*`): booking-ядро
  (offices, services, queues, working_days, slots, bookings), единый каталог
  (brands, product_models, products, product_warehouse_stock, supplier_stock),
  заказы (orders, order_items, payments), магазин (carts, promo_codes, site_settings),
  служебные (audits, sync_times, banners, tire_codes, car_makes/car_models/car_wheel_sizes).
- **Модели** в `app/Models` с отношениями и castами.
- **`app/Services/SlotReservationService.php`** — мягкая резервация слота:
  5 минут, максимум 2 продления, транзакции + lockForUpdate + version.
  Очистка истёкших — каждую минуту через `routes/console.php`.
- **`php artisan legacy:migrate`** (`app/Console/Commands/MigrateLegacyData.php`) —
  идемпотентный перенос данных из старой БД по модулям:
  `--module=users|booking|catalog|orders|misc`. Привязка к старым ID через
  `legacy_id` / `legacy_source`; сырые JSON старых записей сохраняются в
  `bookings.legacy_data` и `orders.legacy_details` — ничего не теряется.

## Ключевые решения (не менять без причины)

- `slots` — только сетка времени и резервация; данные клиента — в `bookings` (1:1 по slot_id).
- UNIQUE(date, queue_id, position) на slots и UNIQUE(provider, transaction_id) на payments —
  защита от дублей на уровне СУБД.
- Каталог: одна иерархия brands → product_models → products для 7 категорий
  (`auto|moto|quadr|big|rim|quadrim|stud`); специфика категории — в JSON `products.attrs`.
- Всё InnoDB + utf8mb4; FK везде.

- **Модуль записи `/pieraksts`** (публичная часть):
  - `PierakstsController` — календарь (HTML + `/pieraksts/calendar` JSON),
    создание брони, страница отмены по `cancel_code` (проверка — последние
    2 символа номера авто).
  - `SlotReservationController` — reserve/extend/cancel/check-availability (JSON).
  - `app/Services/BookingService.php` — создание/отмена брони в транзакции
    с lockForUpdate; `app/Services/BookingNotifier.php` — email по шаблонам
    очереди (`queues.notification_*`, плейсхолдеры %TIME% %DATE% … как в старом
    `Queue::parseNotification`), SMS/WhatsApp пока заглушки в лог.
  - `app/Services/WorkingDaysProvisioner.php` — автосоздание working_days
    на 8 дней вперёд (published + draft, воскресенье закрыто).
  - `app/Support/HalfSlotRules.php` — «половинные» дни AC/moto (роль слота
    по позиции + валидация услуги).
  - `app/Http/Requests/StoreBookingRequest.php` — валидация формы
    (rims_with обязателен только для service_id=1).
  - Вьюхи `resources/views/pieraksts/{index,cancel}.blade.php` — минимальный
    рабочий фронт (резервация, таймер, форма, отмена); дизайн — позже.
  - ВАЖНО: `Slot`/`WorkingDay` хранят `date` строго `Y-m-d` (мутатор),
    иначе сравнения дат ломаются на SQLite.

## План дальнейшей работы (по модулям)

1. **Запись на услуги** (остатки): уведомления по-настоящему (SMS-драйвер,
   WhatsApp textmebot — см. БИЗНЕС_ЛОГИКА.md §1), Meta CAPI / Google Ads
   конверсии, API мастеров (`/api/v1/mobile/*`, Bearer-токен), роли
   (spatie/laravel-permission ещё не установлен), нормальный фронт календаря.
2. Каталог + корзина + заказы + Paysera (callback идемпотентен через payments).
3. Синхронизации поставщиков (единый пайплайн вместо 7 контроллеров; см. supplier_stock).
4. Админка.

## Запуск

```bash
composer install
cp .env.example .env && php artisan key:generate
# .env: DB_* — новая БД, LEGACY_DB_* — старая r1 (read-only)
php artisan migrate
php artisan legacy:migrate              # перенос данных из старой БД
php artisan legacy:migrate --module=booking   # либо помодульно
```

После переноса сверить количества: слоты/брони/заказы против старой БД
(`SELECT COUNT(*)` по таблицам из docs/БАЗА_ДАННЫХ.md).

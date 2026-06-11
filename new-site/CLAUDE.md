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
  - Вьюхи `resources/views/pieraksts/{index,cancel}.blade.php` — в дизайне
    старого сайта (см. ниже «Перенос дизайна»).
  - ВАЖНО: `Slot`/`WorkingDay` хранят `date` строго `Y-m-d` (мутатор),
    иначе сравнения дат ломаются на SQLite.

- **Перенос дизайна старого сайта** (выполнено для layout + /pieraksts):
  - Статика в `public/`: `css/theme.css`, `css/custom.css`, `css/schedule.css`,
    `css/fonts/`, `images/` — скопированы из старого репо КАК ЕСТЬ, руками
    не править (это «дизайн»). custom2/custom3.css — мусор, не переносить.
  - ⚠ В старом гите НЕТ части картинок (`public/img/` с логотипом,
    `images/cover*.webp`, иконки телефонов/спрайты) — они только на проде.
    Пути сохранены 1:1: достаточно скопировать с прода `public/img` и
    недостающее из `public/images` — всё встанет само.
  - `layouts/app.blade.php` + `components/navbar.blade.php` — разметка шапки,
    меню (десктоп + мобильный drawer) и футера 1:1 со старого; маркетинг,
    recaptcha, pusher вырезаны. Ссылки на ещё не перенесённые разделы —
    литеральные `url('/...')` со старыми адресами.
  - Сборка — **Vite** (`npm run build`): `resources/js/{app,layout,pieraksts}.js`,
    `resources/css/app.css`. Tailwind отключён сознательно (preflight ломает
    легаси-вёрстку) — удалён из postcss.config.js. jQuery не используется:
    поведение шапки и календаря переписано на vanilla JS с теми же
    CSS-классами состояний (is-open, mobile-nav-open, is-visible).
  - `#mobile-reservation-form` по умолчанию скрыт в custom.css — JS открывает
    его инлайн-стилем (как старый client.js).
  - Временно: на <1024px показывается та же сетка расписания
    (override в resources/css/app.css); отдельный мобильный сценарий
    старого сайта (выбор филиала → времена, showMobileQueues) ещё не перенесён.
  - `config/site.php` — season (фон body, порядок riepu-меню), телефоны.

## План дальнейшей работы (по модулям)

1. **Запись на услуги** (остатки): мобильный сценарий /pieraksts (филиал →
   времена), уведомления по-настоящему (SMS-драйвер, WhatsApp textmebot —
   см. БИЗНЕС_ЛОГИКА.md §1), Meta CAPI / Google Ads конверсии, API мастеров
   (`/api/v1/mobile/*`, Bearer-токен), роли (spatie/laravel-permission ещё
   не установлен).
2. Каталог + корзина + заказы + Paysera (callback идемпотентен через payments).
3. Синхронизации поставщиков (единый пайплайн вместо 7 контроллеров; см. supplier_stock).
4. Админка.

## Запуск

```bash
composer install
npm install && npm run build              # фронт (Vite)
cp .env.example .env && php artisan key:generate
# .env: DB_* — новая БД, LEGACY_DB_* — старая r1 (read-only)
php artisan migrate
php artisan legacy:migrate              # перенос данных из старой БД
php artisan legacy:migrate --module=booking   # либо помодульно
```

После переноса сверить количества: слоты/брони/заказы против старой БД
(`SELECT COUNT(*)` по таблицам из docs/БАЗА_ДАННЫХ.md).

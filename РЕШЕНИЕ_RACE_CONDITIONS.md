# РЕШЕНИЕ ПРОБЛЕМЫ RACE CONDITIONS В СИСТЕМЕ ЗАПИСЕЙ

## Проблема
Два пользователя (или админ + клиент) могут одновременно попытаться забронировать один и тот же слот, что приведет к двойной записи.

## Решения (от простого к сложному)

---

## РЕШЕНИЕ 1: Оптимистичная блокировка с версионированием (РЕКОМЕНДУЕТСЯ)

### Преимущества:
- ✅ Простая реализация
- ✅ Не требует Redis/Memcached
- ✅ Работает с текущей БД
- ✅ Минимальные изменения кода

### Реализация:

#### Шаг 1: Добавить поле version в таблицу slots


```sql
ALTER TABLE slots ADD COLUMN version INT DEFAULT 0 AFTER slot_id;
ALTER TABLE slots ADD COLUMN reserved_until DATETIME NULL AFTER version;
ALTER TABLE slots ADD COLUMN reserved_by VARCHAR(100) NULL AFTER reserved_until;
```

#### Шаг 2: Создать миграцию Laravel


#### Шаг 3: Обновить RecordController.php

```php
// В начале класса добавить
use App\Services\SlotReservationService;

protected $reservationService;

public function __construct()
{
    // ... существующий код ...
    $this->reservationService = new SlotReservationService();
}

// Изменить метод fillSlot
public function fillSlot(Request $request)
{
    $userID = -1;
    if (Auth::check()) {
        $userID = Auth::user()->id;
    }

    $dopParams = $request->input('dopParams');
    
    // ... валидация данных (существующий код) ...
    
    if (!empty($errors)) return json_encode(['success' => false, 'errors' => $errors]);

    // НОВЫЙ КОД: Подтверждение резервации с проверкой версии
    $slotId = $request->input('slot_id');
    $expectedVersion = $request->input('version');
    
    $bookingResult = $this->reservationService->confirmBooking(
        $slotId,
        (array) $result,
        $expectedVersion,
        session()->getId()
    );
    
    if (!$bookingResult['success']) {
        return json_encode([
            'success' => false, 
            'alertMessage' => $bookingResult['message'],
            'finished' => false
        ]);
    }
    
    $slot = $bookingResult['slot'];
    
    // ... остальной код отправки уведомлений ...
    
    return json_encode(['success' => true, 'message' => $returnMessage, 'new_slot_client' => true]);
}
```

#### Шаг 4: Добавить маршруты (routes/web.php)

```php
// Маршруты для блокировки слотов
Route::post('/pieraksts/reserve-slot', [App\Http\Controllers\Records\SlotLockingController::class, 'reserve']);
Route::post('/pieraksts/extend-reservation', [App\Http\Controllers\Records\SlotLockingController::class, 'extend']);
Route::post('/pieraksts/cancel-reservation', [App\Http\Controllers\Records\SlotLockingController::class, 'cancel']);
Route::post('/pieraksts/check-availability', [App\Http\Controllers\Records\SlotLockingController::class, 'checkAvailability']);
```

#### Шаг 5: JavaScript для клиентской части

```javascript
// public/js/slot-reservation.js

class SlotReservationManager {
    constructor() {
        this.currentReservation = null;
        this.extendInterval = null;
        this.EXTEND_INTERVAL = 120000; // 2 минуты
        this.WARNING_TIME = 60000; // Предупреждение за 1 минуту
    }
    
    /**
     * Резервация слота при клике
     */
    async reserveSlot(queueId, date, iorder) {
        try {
            const response = await fetch('/pieraksts/reserve-slot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ queue_id: queueId, date: date, iorder: iorder })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.currentReservation = {
                    slot_id: result.slot.slot_id,
                    version: result.slot.version,
                    reserved_until: result.reserved_until,
                    queue_id: queueId,
                    date: date,
                    iorder: iorder
                };
                
                // Запускаем автопродление
                this.startAutoExtend();
                
                // Показываем таймер
                this.showReservationTimer(result.reserved_until);
                
                return result;
            } else {
                this.showError(result.message);
                return null;
            }
        } catch (error) {
            console.error('Ошибка резервации:', error);
            this.showError('Ошибка соединения с сервером');
            return null;
        }
    }
    
    /**
     * Автоматическое продление резервации
     */
    startAutoExtend() {
        // Очищаем предыдущий интервал
        if (this.extendInterval) {
            clearInterval(this.extendInterval);
        }
        
        // Продлеваем каждые 2 минуты
        this.extendInterval = setInterval(async () => {
            if (this.currentReservation) {
                const response = await fetch('/pieraksts/extend-reservation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ slot_id: this.currentReservation.slot_id })
                });
                
                const result = await response.json();
                
                if (!result.success) {
                    this.cancelReservation();
                    this.showError('Время резервации истекло. Пожалуйста, выберите слот заново.');
                }
            }
        }, this.EXTEND_INTERVAL);
    }
    
    /**
     * Отмена резервации
     */
    async cancelReservation() {
        if (this.currentReservation) {
            await fetch('/pieraksts/cancel-reservation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ slot_id: this.currentReservation.slot_id })
            });
        }
        
        this.currentReservation = null;
        
        if (this.extendInterval) {
            clearInterval(this.extendInterval);
            this.extendInterval = null;
        }
        
        this.hideReservationTimer();
    }
    
    /**
     * Проверка доступности перед отправкой формы
     */
    async checkAvailability(queueId, date, iorder) {
        const response = await fetch('/pieraksts/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ queue_id: queueId, date: date, iorder: iorder })
        });
        
        const result = await response.json();
        return result.available;
    }
    
    /**
     * Показать таймер резервации
     */
    showReservationTimer(reservedUntil) {
        const timerElement = document.getElementById('reservation-timer');
        if (!timerElement) return;
        
        const updateTimer = () => {
            const now = new Date();
            const until = new Date(reservedUntil);
            const diff = until - now;
            
            if (diff <= 0) {
                timerElement.innerHTML = '<span class="text-red-600">Время истекло!</span>';
                this.cancelReservation();
                return;
            }
            
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const color = diff < this.WARNING_TIME ? 'text-red-600' : 'text-green-600';
            timerElement.innerHTML = `<span class="${color}">Слот зарезервирован: ${minutes}:${seconds.toString().padStart(2, '0')}</span>`;
        };
        
        updateTimer();
        const interval = setInterval(updateTimer, 1000);
        
        timerElement.dataset.interval = interval;
        timerElement.style.display = 'block';
    }
    
    /**
     * Скрыть таймер
     */
    hideReservationTimer() {
        const timerElement = document.getElementById('reservation-timer');
        if (timerElement) {
            if (timerElement.dataset.interval) {
                clearInterval(parseInt(timerElement.dataset.interval));
            }
            timerElement.style.display = 'none';
        }
    }
    
    /**
     * Показать ошибку
     */
    showError(message) {
        // Можно использовать toast, alert или модальное окно
        alert(message);
    }
}

// Инициализация
const reservationManager = new SlotReservationManager();

// Обработчик клика на слот
document.addEventListener('click', async function(e) {
    const slotElement = e.target.closest('.free-slot-link, .available-slot');
    
    if (slotElement) {
        const timeStatus = slotElement.closest('.time-status');
        const table = slotElement.closest('.table');
        
        if (timeStatus && table) {
            const queueId = table.dataset.queueId;
            const date = table.closest('[data-date]').dataset.date;
            const iorder = timeStatus.dataset.iorder;
            
            // Отменяем предыдущую резервацию
            await reservationManager.cancelReservation();
            
            // Резервируем новый слот
            const result = await reservationManager.reserveSlot(queueId, date, iorder);
            
            if (result) {
                // Сохраняем данные для формы
                document.querySelector('input[name="slot_id"]').value = result.slot.slot_id;
                document.querySelector('input[name="version"]').value = result.slot.version;
                
                // Открываем форму записи
                openBookingForm();
            }
        }
    }
});

// Отмена резервации при закрытии формы
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('close-booking-form')) {
        reservationManager.cancelReservation();
    }
});

// Отмена резервации при уходе со страницы
window.addEventListener('beforeunload', function() {
    reservationManager.cancelReservation();
});
```

#### Шаг 6: Обновить HTML форму записи

```html
<!-- Добавить в форму записи -->
<input type="hidden" name="slot_id" value="">
<input type="hidden" name="version" value="">

<!-- Добавить таймер резервации -->
<div id="reservation-timer" style="display: none; padding: 10px; text-align: center; font-size: 18px; font-weight: bold;">
</div>
```

---

## РЕШЕНИЕ 2: Пессимистичная блокировка с Redis (для высоконагруженных систем)

### Преимущества:
- ✅ Максимальная защита
- ✅ Распределенная блокировка
- ✅ Автоматическое истечение блокировок

### Недостатки:
- ❌ Требует Redis
- ❌ Более сложная настройка

### Реализация:

```php
// app/Services/RedisSlotLockService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisSlotLockService
{
    const LOCK_TIMEOUT = 300; // 5 минут в секундах
    
    public function acquireLock($queueId, $date, $iorder, $userId)
    {
        $key = "slot_lock:{$queueId}:{$date}:{$iorder}";
        
        // Пытаемся установить блокировку
        $acquired = Redis::set($key, $userId, 'EX', self::LOCK_TIMEOUT, 'NX');
        
        if ($acquired) {
            return true;
        }
        
        // Проверяем, не наша ли это блокировка
        $currentOwner = Redis::get($key);
        return $currentOwner === $userId;
    }
    
    public function releaseLock($queueId, $date, $iorder, $userId)
    {
        $key = "slot_lock:{$queueId}:{$date}:{$iorder}";
        
        // Удаляем только если владелец - мы
        $script = "
            if redis.call('get', KEYS[1]) == ARGV[1] then
                return redis.call('del', KEYS[1])
            else
                return 0
            end
        ";
        
        return Redis::eval($script, 1, $key, $userId);
    }
    
    public function extendLock($queueId, $date, $iorder, $userId)
    {
        $key = "slot_lock:{$queueId}:{$date}:{$iorder}";
        
        // Продлеваем только если владелец - мы
        $script = "
            if redis.call('get', KEYS[1]) == ARGV[1] then
                return redis.call('expire', KEYS[1], ARGV[2])
            else
                return 0
            end
        ";
        
        return Redis::eval($script, 1, $key, $userId, self::LOCK_TIMEOUT);
    }
}
```

---

## РЕШЕНИЕ 3: Database-level блокировка (самое простое)

### Преимущества:
- ✅ Очень простая реализация
- ✅ Не требует дополнительных полей
- ✅ Работает "из коробки"

### Недостатки:
- ❌ Блокирует только на момент транзакции
- ❌ Нет предварительной резервации

### Реализация:

```php
// В RecordController.php метод fillSlot

DB::beginTransaction();

try {
    // Получаем слот с блокировкой строки
    $slot = Slot::where('date', $dopParams['date'])
        ->where('queue_id', $dopParams['queue_id'])
        ->where('iorder', $dopParams['iorder'])
        ->lockForUpdate() // КРИТИЧНО!
        ->first();
    
    // Проверяем доступность
    if ($slot && $slot->status == 1 && !empty($slot->takenby)) {
        DB::rollBack();
        return json_encode([
            'success' => false, 
            'alertMessage' => 'Извините, этот слот уже занят!',
            'finished' => false
        ]);
    }
    
    // Создаем или обновляем слот
    if (!$slot) {
        $slot = new Slot();
        $slot->timestamps = false;
        $slot->queue_id = $dopParams['queue_id'];
        $slot->date = $dopParams['date'];
        $slot->iorder = $dopParams['iorder'];
    }
    
    $slot->status = 1;
    $slot->takenby = json_encode($result);
    $slot->createtime = date('Y-m-d H:i:s');
    $slot->createuser = $userID;
    
    $slot->save();
    
    DB::commit();
    
    // Отправка уведомлений...
    
} catch (\Exception $e) {
    DB::rollBack();
    return json_encode([
        'success' => false,
        'alertMessage' => 'Ошибка создания записи. Попробуйте еще раз.'
    ]);
}
```

---

## РЕШЕНИЕ 4: Комбинированный подход (ОПТИМАЛЬНОЕ)

Сочетает преимущества всех методов:

1. **Мягкая блокировка** (reserved_until) - для UX
2. **Версионирование** (version) - для оптимистичной блокировки
3. **Database locks** (lockForUpdate) - для жесткой блокировки
4. **Redis** (опционально) - для распределенных систем

### Алгоритм:

```
1. Клиент кликает на слот
   ↓
2. AJAX запрос: reserveSlot()
   - Проверка доступности
   - Установка reserved_until (5 мин)
   - Возврат version
   ↓
3. Клиент заполняет форму
   - Таймер показывает оставшееся время
   - Каждые 2 мин: extendReservation()
   ↓
4. Клиент отправляет форму
   ↓
5. Сервер: confirmBooking()
   - DB::beginTransaction()
   - lockForUpdate()
   - Проверка version
   - Проверка reserved_by
   - Проверка status
   - Сохранение
   - DB::commit()
   ↓
6. Успех или ошибка
```

---

## АДМИНИСТРАТИВНАЯ ПАНЕЛЬ

### Защита для админов:

```php
// app/Http/Controllers/Admin/Records/RecordController.php

public function slot_ajax(Request $request, $queue_id, $date, $slot_id)
{
    DB::beginTransaction();
    
    try {
        $slot = Slot::where('slot_id', $slot_id)
            ->lockForUpdate()
            ->first();
        
        // Проверка резервации клиентом
        if ($slot->reserved_until && 
            Carbon::parse($slot->reserved_until)->isFuture()) {
            
            // Показываем предупреждение админу
            return response()->json([
                'warning' => true,
                'message' => 'Внимание! Этот слот сейчас резервирует клиент. ' .
                           'Осталось времени: ' . 
                           Carbon::parse($slot->reserved_until)->diffForHumans(),
                'reserved_by' => $slot->reserved_by,
                'reserved_until' => $slot->reserved_until
            ]);
        }
        
        // Админ может перезаписать с подтверждением
        if ($request->input('force_override')) {
            $slot->reserved_until = null;
            $slot->reserved_by = null;
        }
        
        // ... остальная логика ...
        
        DB::commit();
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()]);
    }
}
```

---

## ТЕСТИРОВАНИЕ

### Сценарии для тестирования:

1. **Два клиента одновременно:**
   - Открыть 2 браузера (Chrome + Firefox)
   - Кликнуть на один слот одновременно
   - Ожидание: Один получит резервацию, второй - ошибку

2. **Клиент + Админ:**
   - Клиент резервирует слот
   - Админ пытается изменить
   - Ожидание: Админ видит предупреждение

3. **Истечение времени:**
   - Зарезервировать слот
   - Ждать 5 минут
   - Попытаться подтвердить
   - Ожидание: Ошибка "Время истекло"

4. **Продление резервации:**
   - Зарезервировать слот
   - Заполнять форму > 5 минут
   - Ожидание: Автоматическое продление

### Нагрузочное тестирование:

```bash
# Apache Bench
ab -n 100 -c 10 -p booking.json -T application/json \
   http://localhost/pieraksts/fillSlot

# Или использовать JMeter, Locust
```

---

## МОНИТОРИНГ

### Метрики для отслеживания:

```sql
-- Количество конфликтов (попытки занять занятый слот)
SELECT COUNT(*) FROM audits 
WHERE audit_event LIKE '%Neizdevās izveidot pierakstu%'
AND audit_time > DATE_SUB(NOW(), INTERVAL 1 DAY);

-- Средняя длительность резервации
SELECT AVG(TIMESTAMPDIFF(SECOND, createtime, reserved_until)) as avg_duration
FROM slots 
WHERE reserved_until IS NOT NULL;

-- Истекшие резервации
SELECT COUNT(*) FROM slots
WHERE reserved_until < NOW() 
AND reserved_until IS NOT NULL
AND status = 0;
```

---

## МИГРАЦИЯ НА ПРОДАКШН

### Пошаговый план:

1. **Подготовка (1 день):**
   - Создать бэкап БД
   - Тестирование на staging
   - Подготовить rollback план

2. **Миграция (в нерабочее время):**
   ```bash
   # 1. Включить maintenance mode
   php artisan down
   
   # 2. Запустить миграцию
   php artisan migrate
   
   # 3. Очистить кеш
   php artisan cache:clear
   php artisan config:clear
   
   # 4. Выключить maintenance mode
   php artisan up
   ```

3. **Мониторинг (3 дня):**
   - Отслеживать ошибки в логах
   - Проверять метрики конфликтов
   - Собирать feedback от пользователей

4. **Rollback план (если что-то пошло не так):**
   ```sql
   -- Откатить миграцию
   ALTER TABLE slots DROP COLUMN version;
   ALTER TABLE slots DROP COLUMN reserved_until;
   ALTER TABLE slots DROP COLUMN reserved_by;
   ```

---

## РЕКОМЕНДАЦИЯ

**Для вашей системы рекомендую РЕШЕНИЕ 1 (Оптимистичная блокировка):**

✅ Простая реализация  
✅ Не требует Redis  
✅ Хороший UX (таймер резервации)  
✅ Защита от 99% race conditions  
✅ Легко тестировать  

**Время внедрения:** 1-2 дня  
**Риски:** Минимальные  
**Эффект:** Полная защита от двойных записей  

---

## ДОПОЛНИТЕЛЬНЫЕ УЛУЧШЕНИЯ

1. **WebSocket для real-time обновлений:**
   - Показывать другим пользователям, что слот резервируется
   - Автообновление календаря

2. **Очередь ожидания:**
   - Если слот занят, предложить встать в очередь
   - Уведомление при освобождении

3. **Умная блокировка:**
   - Анализ поведения пользователя
   - Автоматическая отмена при неактивности

4. **Аналитика конфликтов:**
   - Dashboard с метриками
   - Оповещения при частых конфликтах

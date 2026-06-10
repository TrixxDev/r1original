# 🐳 Docker для R1 Riepas

## 📋 Что включено:

- **PHP 8.1** с Apache
- **MySQL 8.0** база данных
- **phpMyAdmin** для управления БД
- **Composer** для зависимостей
- **Laravel** оптимизированный для production

---

## 🚀 Быстрый старт:

### 1. Установить Docker Desktop (если еще нет):
Скачать: https://www.docker.com/products/docker-desktop

### 2. Скопировать .env файл:
```bash
copy .env.docker .env
```

### 3. Сгенерировать APP_KEY:
```bash
docker-compose run --rm app php artisan key:generate
```

### 4. Запустить контейнеры:
```bash
docker-compose up -d
```

### 5. Выполнить миграции:
```bash
docker-compose exec app php artisan migrate
```

### 6. Открыть в браузере:
- **Сайт**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

---

## 📦 Структура:

```
r1riepaspieraksts/
├── Dockerfile              ← Образ PHP + Apache
├── docker-compose.yml      ← Конфигурация сервисов
├── .dockerignore          ← Исключения для Docker
├── .env.docker            ← Настройки для Docker
└── DOCKER_ИНСТРУКЦИЯ.md   ← Эта инструкция
```

---

## 🔧 Полезные команды:

### Запуск:
```bash
docker-compose up -d
```

### Остановка:
```bash
docker-compose down
```

### Перезапуск:
```bash
docker-compose restart
```

### Просмотр логов:
```bash
docker-compose logs -f app
```

### Выполнить команду в контейнере:
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan migrate
docker-compose exec app composer install
```

### Зайти в контейнер:
```bash
docker-compose exec app bash
```

### Пересобрать образ:
```bash
docker-compose build --no-cache
docker-compose up -d
```

---

## 🗄️ База данных:

### Подключение из приложения:
- **Host**: `db` (имя сервиса)
- **Port**: `3306`
- **Database**: `r1`
- **Username**: `newr1`
- **Password**: `J569klll`

### Подключение извне (например, MySQL Workbench):
- **Host**: `localhost` или `127.0.0.1`
- **Port**: `3306`
- **Database**: `r1`
- **Username**: `newr1`
- **Password**: `J569klll`

### phpMyAdmin:
- **URL**: http://localhost:8081
- **Username**: `newr1`
- **Password**: `J569klll`

---

## 📝 Первоначальная настройка:

### 1. Скопировать .env:
```bash
copy .env.docker .env
```

### 2. Сгенерировать ключ:
```bash
docker-compose run --rm app php artisan key:generate
```

### 3. Запустить контейнеры:
```bash
docker-compose up -d
```

### 4. Установить зависимости:
```bash
docker-compose exec app composer install
```

### 5. Выполнить миграции:
```bash
docker-compose exec app php artisan migrate
```

### 6. Добавить поля для блокировки слотов:
```bash
docker-compose exec app php artisan migrate
```

Или через phpMyAdmin (http://localhost:8081):
```sql
ALTER TABLE slots 
ADD COLUMN version INT NOT NULL DEFAULT 0 AFTER slot_id,
ADD COLUMN reserved_until DATETIME NULL AFTER version,
ADD COLUMN reserved_by VARCHAR(100) NULL AFTER reserved_until,
ADD INDEX idx_slot_lookup (date, queue_id, iorder),
ADD INDEX idx_reserved_until (reserved_until);
```

### 7. Очистить кеш:
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### 8. Установить права:
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

---

## 🔄 Обновление проекта:

### 1. Остановить контейнеры:
```bash
docker-compose down
```

### 2. Обновить код (git pull или скопировать файлы)

### 3. Пересобрать образ:
```bash
docker-compose build --no-cache
```

### 4. Запустить:
```bash
docker-compose up -d
```

### 5. Обновить зависимости:
```bash
docker-compose exec app composer install --no-dev --optimize-autoloader
```

### 6. Выполнить миграции:
```bash
docker-compose exec app php artisan migrate --force
```

### 7. Очистить кеш:
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

---

## 🐛 Troubleshooting:

### Порт 8080 уже занят:
Изменить в `docker-compose.yml`:
```yaml
ports:
  - "8888:80"  # Вместо 8080
```

### Ошибка подключения к БД:
```bash
# Проверить что контейнер БД запущен
docker-compose ps

# Посмотреть логи БД
docker-compose logs db

# Перезапустить БД
docker-compose restart db
```

### Ошибки прав доступа:
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Очистить всё и начать заново:
```bash
docker-compose down -v
docker-compose up -d --build
```

### Посмотреть логи Apache:
```bash
docker-compose exec app tail -f /var/log/apache2/error.log
```

---

## 📊 Порты:

| Сервис | Порт | URL |
|--------|------|-----|
| Laravel | 8080 | http://localhost:8080 |
| MySQL | 3306 | localhost:3306 |
| phpMyAdmin | 8081 | http://localhost:8081 |

---

## 🎯 Production деплой:

### 1. Изменить .env:
```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Оптимизировать:
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app composer install --no-dev --optimize-autoloader
```

### 3. Настроить SSL (опционально):
Добавить nginx-proxy или Traefik для HTTPS

---

## 💾 Backup базы данных:

### Создать backup:
```bash
docker-compose exec db mysqldump -u newr1 -pJ569klll r1 > backup.sql
```

### Восстановить backup:
```bash
docker-compose exec -T db mysql -u newr1 -pJ569klll r1 < backup.sql
```

---

## 🔒 Безопасность:

1. Изменить пароли в `.env` и `docker-compose.yml`
2. Не коммитить `.env` в git
3. Использовать HTTPS в production
4. Ограничить доступ к phpMyAdmin

---

**Готово!** Проект запущен в Docker 🎉

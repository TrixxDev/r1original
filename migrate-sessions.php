<?php

/**
 * Перенос file-сессий Laravel → таблица sessions (MySQL).
 * Без Artisan. Запуск из браузера или CLI.
 *
 * Браузер:
 *   /migrate-sessions.php?token=ВАШ_ТОКЕН&dry=1
 *   /migrate-sessions.php?token=ВАШ_ТОКЕН
 *   /migrate-sessions.php?token=ВАШ_ТОКЕН&delete=1
 *
 * CLI:
 *   php migrate-sessions.php ВАШ_ТОКЕН
 *   php migrate-sessions.php ВАШ_ТОКЕН --dry
 *   php migrate-sessions.php ВАШ_ТОКЕН --delete
 *
 * В .env: SESSION_MIGRATE_TOKEN=случайная_строка
 * После переноса: SESSION_DRIVER=database, php artisan config:clear (или удалите config cache вручную)
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Support\FileSessionMigrator;

$isCli = PHP_SAPI === 'cli';
$token = $isCli ? ($argv[1] ?? '') : (string) ($_GET['token'] ?? '');
$expected = (string) env('SESSION_MIGRATE_TOKEN', '');

if ($expected === '' || ! hash_equals($expected, $token)) {
    if ($isCli) {
        fwrite(STDERR, "Неверный или пустой токен.\nПример: php migrate-sessions.php YOUR_TOKEN --dry\n");
        exit(1);
    }
    http_response_code(404);
    exit('Not found');
}

$dryRun = $isCli
    ? in_array('--dry', $argv, true)
    : isset($_GET['dry']);

$deleteFiles = $isCli
    ? in_array('--delete', $argv, true)
    : isset($_GET['delete']);

$result = FileSessionMigrator::run($dryRun, $deleteFiles);
$output = FileSessionMigrator::formatResult($result);

if (! $dryRun && $result['ok'] ?? false) {
    $output .= "\n\nГотово. Теперь в .env: SESSION_DRIVER=database\n";
}

if ($isCli) {
    echo $output."\n";
    exit(($result['ok'] ?? false) ? 0 : 1);
}

header('Content-Type: text/plain; charset=UTF-8');
echo $output;

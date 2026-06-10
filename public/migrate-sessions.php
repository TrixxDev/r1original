<?php

/**
 * Перенос file-сессий → MySQL (без Artisan, без web middleware).
 * URL: /migrate-sessions.php?token=ТОКЕН&dry=1
 */

define('LARAVEL_START', microtime(true));

header('Content-Type: text/plain; charset=UTF-8');

try {
    require __DIR__.'/../vendor/autoload.php';

    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    if (! class_exists(\App\Support\FileSessionMigrator::class)) {
        http_response_code(500);
        exit("ОШИБКА: класс App\\Support\\FileSessionMigrator не найден.\nЗалейте app/Support/FileSessionMigrator.php на сервер.\n");
    }

    $token = (string) ($_GET['token'] ?? '');
    $expected = (string) config('session.migrate_token', '');

    if ($expected === '' || ! hash_equals($expected, $token)) {
        http_response_code(404);
        exit("Not found\n");
    }

    $result = \App\Support\FileSessionMigrator::run(
        isset($_GET['dry']),
        isset($_GET['delete'])
    );

    $output = \App\Support\FileSessionMigrator::formatResult($result);

    if (! isset($_GET['dry']) && ($result['ok'] ?? false)) {
        $output .= "\n\nГотово. Теперь в .env: SESSION_DRIVER=database\n";
    }

    if (! ($result['ok'] ?? false)) {
        http_response_code(500);
    }

    echo $output;
} catch (\Throwable $e) {
    http_response_code(500);
    echo "FATAL: ".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}

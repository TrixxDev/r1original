<?php

/**
 * Обслуживание file-сессий (4GB+). Пакетами, без Artisan.
 *
 * stats:         ?token=...&action=stats
 * cleanup-old:   ?token=...&action=cleanup-old&maxdays=2&batch=5000&dry=1
 * migrate-login: ?token=...&action=migrate-login&batch=2000&dry=1
 * cleanup:       ?token=...&action=cleanup  (по SESSION_LIFETIME — у вас слишком большой)
 */

define('LARAVEL_START', microtime(true));
header('Content-Type: text/plain; charset=UTF-8');

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    $token = (string) ($_GET['token'] ?? '');
    $expected = (string) config('session.migrate_token', '');
    if ($expected === '' || ! hash_equals($expected, $token)) {
        http_response_code(404);
        exit("Not found\n");
    }

    if (! class_exists(\App\Support\FileSessionMaintenance::class)) {
        http_response_code(500);
        exit("Залейте app/Support/FileSessionMaintenance.php\n");
    }

    $action = (string) ($_GET['action'] ?? 'stats');
    $dry = isset($_GET['dry']);
    $maxDays = max(1, (int) ($_GET['maxdays'] ?? 2));
    $batch = max(1, min(20000, (int) ($_GET['batch'] ?? 0)));

    switch ($action) {
        case 'stats':
            $result = \App\Support\FileSessionMaintenance::stats();
            break;
        case 'cleanup-old':
            $result = \App\Support\FileSessionMaintenance::cleanupOldBatch(
                $batch > 0 ? $batch : 5000,
                $dry,
                $maxDays
            );
            break;
        case 'migrate-login':
            $result = \App\Support\FileSessionMaintenance::migrateLoggedInBatch(
                $batch > 0 ? $batch : 200,
                $dry
            );
            break;
        case 'cleanup':
            $result = \App\Support\FileSessionMaintenance::cleanupExpiredBatch(
                $batch > 0 ? $batch : 3000,
                $dry
            );
            break;
        case 'migrate':
            $result = \App\Support\FileSessionMaintenance::migrateActiveBatch(
                $batch > 0 ? $batch : 500,
                $dry
            );
            break;
        default:
            http_response_code(400);
            exit("action=stats|cleanup-old|migrate-login|cleanup|migrate\n");
    }

    echo \App\Support\FileSessionMaintenance::format($result);
    if (! ($result['ok'] ?? false)) {
        http_response_code(500);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'FATAL: '.$e->getMessage()."\n";
}


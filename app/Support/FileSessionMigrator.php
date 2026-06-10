<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class FileSessionMigrator
{
    private const MAX_DETAIL_LINES = 80;

    public static function run(bool $dryRun = false, bool $deleteFiles = false): array
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $table = config('session.table', 'sessions');

        if (! self::tableExists($table)) {
            return [
                'ok' => false,
                'error' => "Таблица `{$table}` не найдена. Сначала database/sessions-table.sql",
            ];
        }

        $dir = config('session.files', storage_path('framework/sessions'));
        if (! is_dir($dir)) {
            return [
                'ok' => false,
                'error' => "Каталог сессий не найден: {$dir}",
            ];
        }

        $lifetimeSeconds = (int) config('session.lifetime', 120) * 60;
        $cutoff = time() - $lifetimeSeconds;

        $result = [
            'ok' => true,
            'dry_run' => $dryRun,
            'migrated' => 0,
            'logged_in' => 0,
            'skipped_expired' => 0,
            'skipped_empty' => 0,
            'skipped_short' => 0,
            'errors' => 0,
            'error_messages' => [],
            'lines' => [],
            'lines_truncated' => 0,
        ];

        $entries = @scandir($dir);
        if ($entries === false) {
            return ['ok' => false, 'error' => "Не удалось прочитать каталог: {$dir}"];
        }

        foreach ($entries as $name) {
            if ($name === '.' || $name === '..' || $name === '.gitignore') {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path)) {
                continue;
            }

            $sessionId = $name;
            if (strlen($sessionId) < 10) {
                $result['skipped_short']++;
                continue;
            }

            $raw = @file_get_contents($path);
            if ($raw === false || $raw === '') {
                $result['skipped_empty']++;
                continue;
            }

            $lastActivity = (int) @filemtime($path);
            if ($lastActivity < $cutoff) {
                $result['skipped_expired']++;
                continue;
            }

            $userId = self::extractUserId($raw);
            if ($userId !== null) {
                $result['logged_in']++;
            }

            if (count($result['lines']) < self::MAX_DETAIL_LINES) {
                $result['lines'][] = sprintf(
                    '[%s] %s | user_id=%s | %s',
                    $dryRun ? 'dry-run' : 'ok',
                    substr($sessionId, 0, 16).'…',
                    $userId ?? '-',
                    date('Y-m-d H:i:s', $lastActivity)
                );
            } else {
                $result['lines_truncated']++;
            }

            if ($dryRun) {
                $result['migrated']++;
                continue;
            }

            try {
                DB::table($table)->updateOrInsert(
                    ['id' => $sessionId],
                    [
                        'user_id' => $userId,
                        'ip_address' => null,
                        'user_agent' => null,
                        'payload' => base64_encode($raw),
                        'last_activity' => $lastActivity,
                    ]
                );
                $result['migrated']++;

                if ($deleteFiles) {
                    @unlink($path);
                }
            } catch (\Throwable $e) {
                $result['errors']++;
                if (count($result['error_messages']) < 20) {
                    $result['error_messages'][] = $sessionId.': '.$e->getMessage();
                }
            }
        }

        if ($result['errors'] > 0) {
            $result['ok'] = false;
        }

        return $result;
    }

    public static function formatResult(array $result): string
    {
        if (! empty($result['error'])) {
            return 'ОШИБКА: '.$result['error']."\n";
        }

        $out = [];
        if (! empty($result['lines'])) {
            $out[] = implode("\n", $result['lines']);
        }
        if (($result['lines_truncated'] ?? 0) > 0) {
            $out[] = '… ещё '.$result['lines_truncated'].' строк скрыто';
        }
        $out[] = '';
        $out[] = 'Перенесено: '.($result['migrated'] ?? 0);
        $out[] = 'Залогиненных (user_id): '.($result['logged_in'] ?? 0);
        $out[] = 'Пропущено (просрочено): '.($result['skipped_expired'] ?? 0);
        $out[] = 'Пропущено (пустые): '.($result['skipped_empty'] ?? 0);
        if (($result['skipped_short'] ?? 0) > 0) {
            $out[] = 'Пропущено (короткие имена): '.$result['skipped_short'];
        }
        if (($result['errors'] ?? 0) > 0) {
            $out[] = 'Ошибок: '.$result['errors'];
            $out[] = implode("\n", $result['error_messages'] ?? []);
        }
        if (! empty($result['dry_run'])) {
            $out[] = '';
            $out[] = 'Это dry-run. Запустите без dry=1 для записи в БД.';
        }

        return implode("\n", $out);
    }

    private static function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function extractUserId(string $raw): ?int
    {
        if (PHP_VERSION_ID >= 70000) {
            $data = @unserialize($raw, ['allowed_classes' => false]);
        } else {
            $data = @unserialize($raw);
        }

        if (! is_array($data)) {
            return null;
        }

        foreach ($data as $key => $value) {
            if (is_string($key) && strpos($key, 'login_web_') === 0 && is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}

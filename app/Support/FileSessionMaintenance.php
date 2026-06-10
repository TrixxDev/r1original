<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class FileSessionMaintenance
{
    public static function stats(): array
    {
        $dir = config('session.files', storage_path('framework/sessions'));
        if (! is_dir($dir)) {
            return ['ok' => false, 'error' => "Каталог не найден: {$dir}"];
        }

        $lifetimeMin = (int) config('session.lifetime', 120);
        $cutoff = time() - ($lifetimeMin * 60);

        $total = 0;
        $expired = 0;
        $active = 0;
        $bytes = 0;

        $handle = @opendir($dir);
        if ($handle === false) {
            return ['ok' => false, 'error' => "Не удалось открыть каталог: {$dir}"];
        }

        while (($name = readdir($handle)) !== false) {
            if ($name === '.' || $name === '..' || $name === '.gitignore' || $name[0] === '.') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path)) {
                continue;
            }
            $total++;
            $size = (int) @filesize($path);
            $bytes += $size;
            if ((int) @filemtime($path) < $cutoff) {
                $expired++;
            } else {
                $active++;
            }

            if ($total >= 50000) {
                break;
            }
        }
        closedir($handle);

        return [
            'ok' => true,
            'dir' => $dir,
            'sampled_files' => $total,
            'sample_limited' => $total >= 50000,
            'active_in_sample' => $active,
            'expired_in_sample' => $expired,
            'bytes_in_sample' => $bytes,
            'mb_in_sample' => round($bytes / 1024 / 1024, 1),
            'lifetime_minutes' => $lifetimeMin,
            'hint' => '4GB — в основном просроченные file-сессии. Сначала cleanup, потом migrate.',
        ];
    }

    /**
     * Удалить file-сессии старше N дней (не зависит от SESSION_LIFETIME в .env).
     */
    public static function cleanupOldBatch(int $batchSize, bool $dryRun, int $maxDays = 2): array
    {
        @set_time_limit(600);

        $dir = config('session.files', storage_path('framework/sessions'));
        $cutoff = time() - ($maxDays * 86400);
        $statePath = storage_path('framework/sessions/.cleanup_old_state.json');
        $state = self::loadState($statePath);

        $result = [
            'ok' => true,
            'dry_run' => $dryRun,
            'max_days' => $maxDays,
            'scanned' => 0,
            'deleted' => 0,
            'skipped_recent' => 0,
            'done' => false,
            'resume' => true,
        ];

        $handle = @opendir($dir);
        if ($handle === false) {
            return ['ok' => false, 'error' => "Не удалось открыть каталог: {$dir}"];
        }

        $skip = (int) ($state['skip'] ?? 0);
        $skipped = 0;
        $name = null;

        while (($name = readdir($handle)) !== false) {
            if ($name === '.' || $name === '..' || $name === '.gitignore' || $name[0] === '.') {
                continue;
            }
            if ($skipped < $skip) {
                $skipped++;
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path)) {
                continue;
            }

            $result['scanned']++;

            if ((int) @filemtime($path) >= $cutoff) {
                $result['skipped_recent']++;
            } elseif ($dryRun) {
                $result['deleted']++;
            } elseif (@unlink($path)) {
                $result['deleted']++;
            }

            if ($result['scanned'] >= $batchSize) {
                break;
            }
        }

        if ($name === false) {
            $result['done'] = true;
            $result['resume'] = false;
            @unlink($statePath);
        } else {
            $state['skip'] = $skip + $result['scanned'];
            self::saveState($statePath, $state);
        }

        closedir($handle);

        return $result;
    }

    /**
     * Перенести только залогиненных (user_id), пакетами.
     */
    public static function migrateLoggedInBatch(int $batchSize, bool $dryRun): array
    {
        @set_time_limit(600);

        $table = config('session.table', 'sessions');
        if (! self::tableExists($table)) {
            return ['ok' => false, 'error' => "Таблица `{$table}` не найдена."];
        }

        $dir = config('session.files', storage_path('framework/sessions'));
        $statePath = storage_path('framework/sessions/.migrate_logged_in_state.json');
        $state = self::loadState($statePath);

        $result = [
            'ok' => true,
            'dry_run' => $dryRun,
            'scanned' => 0,
            'migrated' => 0,
            'skipped_guest' => 0,
            'errors' => 0,
            'done' => false,
            'resume' => true,
        ];

        $handle = @opendir($dir);
        if ($handle === false) {
            return ['ok' => false, 'error' => "Не удалось открыть каталог: {$dir}"];
        }

        $skip = (int) ($state['skip'] ?? 0);
        $skipped = 0;
        $processed = 0;
        $name = null;

        while (($name = readdir($handle)) !== false) {
            if ($name === '.' || $name === '..' || $name === '.gitignore' || $name[0] === '.') {
                continue;
            }
            if ($skipped < $skip) {
                $skipped++;
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path) || strlen($name) < 10) {
                continue;
            }

            $result['scanned']++;
            $raw = @file_get_contents($path);
            if ($raw === false || $raw === '') {
                $processed++;
                if ($processed >= $batchSize) {
                    break;
                }
                continue;
            }

            $userId = self::extractUserId($raw);
            if ($userId === null) {
                $result['skipped_guest']++;
                $processed++;
                if ($processed >= $batchSize) {
                    break;
                }
                continue;
            }

            $lastActivity = (int) @filemtime($path);

            if ($dryRun) {
                $result['migrated']++;
            } else {
                try {
                    DB::table($table)->updateOrInsert(
                        ['id' => $name],
                        [
                            'user_id' => $userId,
                            'ip_address' => null,
                            'user_agent' => null,
                            'payload' => base64_encode($raw),
                            'last_activity' => $lastActivity,
                        ]
                    );
                    $result['migrated']++;
                } catch (\Throwable $e) {
                    $result['errors']++;
                    $result['ok'] = false;
                }
            }

            $processed++;
            if ($processed >= $batchSize) {
                break;
            }
        }

        if ($name === false) {
            $result['done'] = true;
            $result['resume'] = false;
            @unlink($statePath);
        } else {
            $state['skip'] = $skip + $result['scanned'];
            self::saveState($statePath, $state);
        }

        closedir($handle);

        return $result;
    }

    /**
     * Пакетная очистка просроченных file-сессий (для огромных каталогов).
     * Вызывать повторно, пока deleted=0 или done=true.
     */
    public static function cleanupExpiredBatch(int $batchSize = 3000, bool $dryRun = false): array
    {
        @set_time_limit(600);

        $dir = config('session.files', storage_path('framework/sessions'));
        $cutoff = time() - ((int) config('session.lifetime', 120) * 60);
        $statePath = storage_path('framework/sessions/.cleanup_state.json');
        $state = self::loadState($statePath);

        $result = [
            'ok' => true,
            'dry_run' => $dryRun,
            'scanned' => 0,
            'deleted' => 0,
            'skipped_active' => 0,
            'done' => false,
            'resume' => true,
        ];

        $handle = @opendir($dir);
        if ($handle === false) {
            return ['ok' => false, 'error' => "Не удалось открыть каталог: {$dir}"];
        }

        $skip = (int) ($state['skip'] ?? 0);
        $skipped = 0;

        while (($name = readdir($handle)) !== false) {
            if ($name === '.' || $name === '..' || $name === '.gitignore' || $name[0] === '.') {
                continue;
            }

            if ($skipped < $skip) {
                $skipped++;
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path)) {
                continue;
            }

            $result['scanned']++;

            if ((int) @filemtime($path) >= $cutoff) {
                $result['skipped_active']++;
            } elseif ($dryRun) {
                $result['deleted']++;
            } else {
                if (@unlink($path)) {
                    $result['deleted']++;
                }
            }

            if ($result['scanned'] >= $batchSize) {
                break;
            }
        }

        if ($name === false) {
            $result['done'] = true;
            $result['resume'] = false;
            @unlink($statePath);
        } else {
            $state['skip'] = $skip + $result['scanned'];
            self::saveState($statePath, $state);
        }

        closedir($handle);

        return $result;
    }

    /**
     * Перенос только активных (не просроченных) сессий — пакетами.
     */
    public static function migrateActiveBatch(int $batchSize = 500, bool $dryRun = false): array
    {
        @set_time_limit(600);

        $table = config('session.table', 'sessions');
        if (! self::tableExists($table)) {
            return ['ok' => false, 'error' => "Таблица `{$table}` не найдена."];
        }

        $dir = config('session.files', storage_path('framework/sessions'));
        $cutoff = time() - ((int) config('session.lifetime', 120) * 60);
        $statePath = storage_path('framework/sessions/.migrate_state.json');
        $state = self::loadState($statePath);

        $result = [
            'ok' => true,
            'dry_run' => $dryRun,
            'scanned' => 0,
            'migrated' => 0,
            'logged_in' => 0,
            'skipped_expired' => 0,
            'errors' => 0,
            'done' => false,
            'resume' => true,
        ];

        $handle = @opendir($dir);
        if ($handle === false) {
            return ['ok' => false, 'error' => "Не удалось открыть каталог: {$dir}"];
        }

        $skip = (int) ($state['skip'] ?? 0);
        $skipped = 0;
        $processed = 0;

        while (($name = readdir($handle)) !== false) {
            if ($name === '.' || $name === '..' || $name === '.gitignore' || $name[0] === '.') {
                continue;
            }

            if ($skipped < $skip) {
                $skipped++;
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$name;
            if (! is_file($path) || strlen($name) < 10) {
                continue;
            }

            $result['scanned']++;

            $lastActivity = (int) @filemtime($path);
            if ($lastActivity < $cutoff) {
                $result['skipped_expired']++;
                $processed++;
                if ($processed >= $batchSize) {
                    break;
                }
                continue;
            }

            $raw = @file_get_contents($path);
            if ($raw === false || $raw === '') {
                $processed++;
                if ($processed >= $batchSize) {
                    break;
                }
                continue;
            }

            $userId = self::extractUserId($raw);
            if ($userId !== null) {
                $result['logged_in']++;
            }

            if ($dryRun) {
                $result['migrated']++;
            } else {
                try {
                    DB::table($table)->updateOrInsert(
                        ['id' => $name],
                        [
                            'user_id' => $userId,
                            'ip_address' => null,
                            'user_agent' => null,
                            'payload' => base64_encode($raw),
                            'last_activity' => $lastActivity,
                        ]
                    );
                    $result['migrated']++;
                } catch (\Throwable $e) {
                    $result['errors']++;
                    $result['ok'] = false;
                }
            }

            $processed++;
            if ($processed >= $batchSize) {
                break;
            }
        }

        if ($name === false) {
            $result['done'] = true;
            $result['resume'] = false;
            @unlink($statePath);
        } else {
            $state['skip'] = $skip + $result['scanned'];
            self::saveState($statePath, $state);
        }

        closedir($handle);

        return $result;
    }

    public static function format(array $result): string
    {
        if (! empty($result['error'])) {
            return 'ОШИБКА: '.$result['error']."\n";
        }

        $lines = [];
        foreach ($result as $key => $value) {
            if ($key === 'ok' || is_array($value)) {
                continue;
            }
            $lines[] = $key.': '.$value;
        }

        if ($result['resume'] ?? false) {
            $lines[] = '';
            $lines[] = 'Обновите страницу (F5) — следующий пакет.';
        }
        if ($result['done'] ?? false) {
            $lines[] = '';
            $lines[] = 'Готово. Можно SESSION_DRIVER=database в .env';
        }

        return implode("\n", $lines)."\n";
    }

    private static function loadState(string $path): array
    {
        if (! is_file($path)) {
            return ['skip' => 0];
        }
        $data = json_decode((string) @file_get_contents($path), true);

        return is_array($data) ? $data : ['skip' => 0];
    }

    private static function saveState(string $path, array $state): void
    {
        @file_put_contents($path, json_encode($state));
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
        $data = @unserialize($raw, ['allowed_classes' => false]);
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


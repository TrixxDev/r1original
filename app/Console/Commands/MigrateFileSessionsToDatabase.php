<?php

namespace App\Console\Commands;

use App\Support\FileSessionMigrator;
use Illuminate\Console\Command;

class MigrateFileSessionsToDatabase extends Command
{
    protected $signature = 'sessions:migrate-files-to-database
                            {--dry-run : Показать, что будет перенесено, без записи в БД}
                            {--delete-files : Удалить file-сессии после успешного переноса}';

    protected $description = 'Перенос активных Laravel file-сессий в таблицу sessions';

    public function handle(): int
    {
        $result = FileSessionMigrator::run(
            (bool) $this->option('dry-run'),
            (bool) $this->option('delete-files')
        );

        $this->line(FileSessionMigrator::formatResult($result));

        return ($result['ok'] ?? false) ? 0 : 1;
    }
}

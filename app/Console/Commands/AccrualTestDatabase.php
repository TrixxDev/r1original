<?php

namespace App\Console\Commands;

use App\Services\AccrualDatabaseService;
use Illuminate\Console\Command;
use PDO;
use PDOException;

class AccrualTestDatabase extends Command
{
    protected $signature = 'accrual:test-db';

    protected $description = 'Test connection to Accrual MSSQL database';

    public function handle(AccrualDatabaseService $accrualDb): int
    {
        $this->line('PDO drivers: ' . implode(', ', PDO::getAvailableDrivers()));
        $this->line('Host: ' . env('ACCRUAL_IP') . ':1444');
        $this->line('Database: accrual');

        $errors = [];

        if (in_array('sqlsrv', PDO::getAvailableDrivers(), true)) {
            $dsn = 'sqlsrv:Server=' . env('ACCRUAL_IP') . ',1444;Database=accrual';
            $this->line('Trying sqlsrv: ' . $dsn);

            try {
                $pdo = new PDO($dsn, 'sa', 'cenzors', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                $row = $this->fetchLatestPzh($pdo);
                $this->info('Accrual connection OK (sqlsrv)');
                $this->printPzhRow($row);

                return self::SUCCESS;
            } catch (PDOException $e) {
                $errors[] = 'sqlsrv: ' . $e->getMessage();
                $this->warn('sqlsrv failed: ' . $e->getMessage());
            }
        } else {
            $this->line('sqlsrv driver not available, skipping.');
        }

        $this->line('Trying fallback via AccrualDatabaseService (sqlsrv/odbc)...');

        try {
            $info = $accrualDb->connectionInfo();
            $row = $accrualDb->testQuery();

            $this->info('Accrual connection OK (' . ($info['driver'] ?? 'unknown') . ')');
            if (!empty($info['dsn'])) {
                $this->line('DSN: ' . $info['dsn']);
            }
            $this->printPzhRow($row);

            return self::SUCCESS;
        } catch (PDOException $e) {
            $errors[] = 'fallback: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $errors[] = 'fallback: ' . $e->getMessage();
        }

        $this->error('Accrual connection failed');
        foreach ($errors as $error) {
            $this->line($error);
        }

        return self::FAILURE;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchLatestPzh(PDO $pdo): ?array
    {
        $stmt = $pdo->query('SELECT TOP 1 PZId, PZNr, PZType, Datums FROM pzh WHERE Deleted = 0 ORDER BY PZId DESC');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param  array<string, mixed>|null  $row
     */
    private function printPzhRow(?array $row): void
    {
        if (!$row) {
            $this->warn('Connected, but pzh table returned no rows.');

            return;
        }

        $this->line('Latest pzh row: PZId=' . $row['PZId'] . ', PZNr=' . $row['PZNr'] . ', PZType=' . $row['PZType']);
    }
}

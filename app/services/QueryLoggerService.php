<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class QueryLoggerService
{
    public function boot(): void
    {
        if (config('app.debug') === true && config('logging.query.debug') === true) {
            DB::listen(function ($query) {
                $logPath = storage_path('logs/query.log');
                $newEntry = $query->sql . ' [' . implode(' ,', $query->bindings) . '] ' . PHP_EOL;

                // Lê as linhas existentes
                $lines = file_exists($logPath) ? file($logPath, FILE_IGNORE_NEW_LINES) : [];

                // Adiciona a nova linha ao final
                $lines[] = $newEntry;

                // Mantém só as últimas linhas definidas
                if (count($lines) > 1000) {
                    $lines = array_slice($lines, -1000);
                }

                // Salva novamente o arquivo
                file_put_contents($logPath, implode(PHP_EOL, $lines) . PHP_EOL);
            });
        }
    }
}

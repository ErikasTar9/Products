<?php

namespace App\Console\Commands;

use App\Jobs\ImportProductJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class ImportProductsCommand extends Command
{
    protected $signature = 'import:products {path=files/products.json}';

    protected $description = 'Import products from JSON file and dispatch jobs to the queue';

    public function handle(): int
    {
        $path = (string) $this->argument('path');

        if (! $this->fileExists($path)) {
            return self::FAILURE;
        }

        $rows = $this->loadJson($path);
        if ($rows === null) {
            return self::FAILURE;
        }

        $count = $this->dispatchProducts($rows);

        $this->info("Dispatched {$count} product(s) to queue.");

        return self::SUCCESS;
    }

    private function fileExists(string $path): bool
    {
        if (! Storage::exists($path)) {
            $this->error("File not found: {$path}");

            return false;
        }

        return true;
    }

    private function loadJson(string $path): ?array
    {
        $data = json_decode(Storage::get($path), true);

        if (! is_array($data)) {
            $this->error('Invalid JSON structure.');

            return null;
        }

        return $data;
    }

    private function dispatchProducts(array $rows): int
    {
        $validRows = array_filter($rows, fn ($row) => $this->isValidRow($row));

        array_walk($validRows, fn ($row) => ImportProductJob::dispatch($row));

        return count($validRows);
    }

    private function isValidRow(mixed $row): bool
    {
        return is_array($row) && !empty($row['sku']);
    }
}

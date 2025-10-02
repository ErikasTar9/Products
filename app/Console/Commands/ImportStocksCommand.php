<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportStocksCommand extends Command
{
    protected $signature = 'import:stocks {path=public/files/stocks.json}';

    protected $description = 'Import stocks from JSON file (upsert by product+city)';

    public function handle(): int
    {
        $path = (string) $this->argument('path');

        if (! $this->fileExists($path)) {
            return self::FAILURE;
        }

        $rows = $this->loadRows($path);
        if ($rows === null) {
            $this->error('Invalid JSON');

            return self::FAILURE;
        }

        $valid = $this->filterValidRows($rows);
        if ($valid === []) {
            $this->info('Stocks imported. 0 row(s) processed.');

            return self::SUCCESS;
        }

        $productMap = $this->fetchProductMap($valid);
        $processed = $this->processRows($valid, $productMap);

        $this->info("Stocks imported. {$processed} row(s) processed.");

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

    private function loadRows(string $path): ?array
    {
        $json = Storage::get($path);

        return $this->decodeJson($json);
    }

    private function decodeJson(string $json): ?array
    {
        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    private function filterValidRows(array $rows): array
    {
        return array_values(array_filter($rows, static function ($r): bool {
            return is_array($r)
                && isset($r['sku'], $r['city'])
                && $r['sku'] !== ''
                && $r['city'] !== ''
                && array_key_exists('stock', $r);
        }));
    }

    private function fetchProductMap(array $rows): array
    {
        $skus = array_values(array_unique(array_map(
            static fn ($r) => (string) $r['sku'],
            $rows
        )));

        return Product::query()
            ->whereIn('sku', $skus)
            ->pluck('id', 'sku')
            ->all();
    }

    private function processRows(array $rows, array $productMap): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $productId = $productMap[(string) $row['sku']] ?? null;
            if ($productId === null) {
                continue;
            }

            $this->upsertStock($productId, (string) $row['city'], (int) $row['stock']);
            $count++;
        }

        return $count;
    }

    private function upsertStock(int $productId, string $city, int $stock): void
    {
        Stock::updateOrCreate(
            ['product_id' => $productId, 'city' => $city],
            ['stock' => $stock]
        );
    }
}

<?php

namespace Tests\Feature\Commands;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportStocksCommandTest extends TestCase
{
    public function testImportStocksCommandSuccess(): void
    {
        $p1 = Product::create(['sku' => 'KF-AAA', 'description' => 'A']);
        $p2 = Product::create(['sku' => 'KF-BBB', 'description' => 'B']);

        Storage::fake('local');

        $rows = [
            ['sku' => 'KF-AAA', 'stock' => 10, 'city' => 'Vilnius'],
            ['sku' => 'KF-AAA', 'stock' => 5, 'city' => 'Kaunas'],
            ['sku' => 'KF-CCC', 'stock' => 7, 'city' => 'Klaipeda'],
            ['sku' => 'KF-BBB', 'stock' => 1, 'city' => 'Vilnius'],
        ];

        Storage::put('files/stocks.json', json_encode($rows));

        $this->artisan('import:stocks')
            ->assertExitCode(0);

        $this->assertDatabaseHas('stocks', ['product_id' => $p1->id, 'city' => 'Vilnius', 'stock' => 10]);
        $this->assertDatabaseHas('stocks', ['product_id' => $p1->id, 'city' => 'Kaunas', 'stock' => 5]);
        $this->assertDatabaseHas('stocks', ['product_id' => $p2->id, 'city' => 'Vilnius', 'stock' => 1]);
        $this->assertDatabaseMissing('stocks', ['city' => 'Klaipeda']);

        $rows2 = [
            ['sku' => 'KF-AAA', 'stock' => 12, 'city' => 'Vilnius'],
        ];
        Storage::put('files/stocks.json', json_encode($rows2));
        $this->artisan('import:stocks')->assertExitCode(0);

        $this->assertEquals(12, Stock::where(['product_id' => $p1->id, 'city' => 'Vilnius'])->value('stock'));
    }

    public function testImportStocksCommandFailsWhenFileMissing(): void
    {
        Storage::fake('local');

        $this->artisan('import:stocks files/missing.json')
            ->expectsOutputToContain('File not found')
            ->assertExitCode(1);
    }

    public function testImportStocksCommandFailsOnInvalidJsonSyntax(): void
    {
        Storage::fake('local');
        Storage::put('files/stocks.json', '{invalid-json');

        $this->artisan('import:stocks')
            ->expectsOutputToContain('Invalid JSON')
            ->assertExitCode(1);
    }
}

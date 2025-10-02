<?php

namespace Tests\Feature\Commands;

use App\Jobs\ImportProductJob;
use App\Models\Product;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportProductsCommandTest extends TestCase
{
    public function testImportProductsCommandSuccess(): void
    {
        Storage::fake('local');

        $payload = [
            [
                'sku' => 'KF-TEST-1',
                'description' => 'Test product 1',
                'size' => 'M',
                'photo' => 'http://example.com/p1.png',
                'tags' => [['title' => 'Alpha'], ['title' => 'Beta']],
                'updated_at' => '2022-01-01',
            ],
            [
                'description' => 'Invalid row',
            ],
            [
                'sku' => 'KF-TEST-2',
                'description' => 'Test product 2',
                'size' => 'L',
                'photo' => 'http://example.com/p2.png',
                'tags' => [],
                'updated_at' => '2022-01-02',
            ],
        ];
        Storage::put('public/files/products.json', json_encode($payload));

        Queue::fake();

        $this->artisan('import:products')
            ->assertExitCode(0);

        Queue::assertPushed(ImportProductJob::class, 2);

        $job = new ImportProductJob($payload[0]);
        $job->handle();

        $this->assertDatabaseHas('products', [
            'sku' => 'KF-TEST-1',
            'description' => 'Test product 1',
            'size' => 'M',
            'photo' => 'http://example.com/p1.png',
        ]);

        $product = Product::where('sku', 'KF-TEST-1')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->tags);
    }

    public function testImportProductsCommandFailsWhenFileMissing(): void
    {
        Storage::fake('local');
        Queue::fake();

        $this->artisan('import:products public/files/missing.json')
            ->expectsOutputToContain('File not found')
            ->assertExitCode(1);

        Queue::assertNothingPushed();
    }

    public function testImportProductsCommandFailsOnInvalidJson(): void
    {
        Storage::fake('local');
        Queue::fake();

        Storage::put('public/files/products.json', '{not-a-valid-json');

        $this->artisan('import:products')
            ->expectsOutputToContain('Invalid JSON')
            ->assertExitCode(1);

        Queue::assertNothingPushed();
    }
}

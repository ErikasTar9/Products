<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Tag;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiProductsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.api_token' => 'secret-token']);
    }

    public function testApiRequiresBearerToken(): void
    {
        $this->getJson('/api/products')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }

    public function testApiReturnsPaginatedProductsWithTotalStockAndTags(): void
    {
        $p1 = Product::factory()->create(['sku' => 'KF-API-1', 'description' => 'D1']);
        $p2 = Product::factory()->create(['sku' => 'KF-API-2', 'description' => 'D2']);

        $t1 = Tag::factory()->create(['title' => 'Alpha']);
        $t2 = Tag::factory()->create(['title' => 'Beta']);
        $p1->tags()->sync([$t1->id, $t2->id]);
        $p2->tags()->sync([$t2->id]);

        Stock::factory()
            ->count(3)
            ->sequence(
                ['product_id' => $p1->id, 'city' => 'Vilnius', 'stock' => 3],
                ['product_id' => $p1->id, 'city' => 'Kaunas', 'stock' => 2],
                ['product_id' => $p2->id, 'city' => 'Vilnius', 'stock' => 7],
            )
            ->create();

        $res = $this->withHeader('Authorization', 'Bearer secret-token')
            ->getJson('/api/products?per_page=10')
            ->assertOk()
            ->json();

        $this->assertArrayHasKey('data', $res);
        $this->assertCount(2, $res['data']);

        $first = collect($res['data'])->firstWhere('sku', 'KF-API-1');
        $this->assertNotNull($first);
        $this->assertEquals(5, (int) $first['total_stock']);
        $this->assertIsArray($first['tags']);
        $this->assertTrue(collect($first['tags'])->pluck('title')->contains('Alpha'));
    }
}

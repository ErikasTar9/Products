<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WebProductsTest extends TestCase
{
    public function testProductsIndexPageRenders(): void
    {
        $p = Product::factory()->create(['sku' => 'KF-WEB-1', 'description' => 'Desc']);
        $tag = Tag::factory()->create(['title' => 'Popular']);
        $p->tags()->sync([$tag->id]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('Products');
    }

    public function testProductShowPageRenders(): void
    {
        $p = Product::create(['sku' => 'KF-WEB-2', 'description' => 'Desc', 'photo' => 'http://example.com/img.png']);
        $tag = Tag::firstOrCreate(['title' => 'Rel']);
        $p->tags()->sync([$tag->id]);

        Stock::create(['product_id' => $p->id, 'city' => 'Vilnius', 'stock' => 1]);

        $this->get('/products/' . $p->sku)
            ->assertOk()
            ->assertSee('Total stock:');

        $stock = Stock::where(['product_id' => $p->id, 'city' => 'Vilnius'])->first();
        $stock->update(['stock' => 5]);

        $this->get('/products/' . $p->sku)
            ->assertOk()
            ->assertSee('Total stock:')
            ->assertSeeText('5');
    }
}

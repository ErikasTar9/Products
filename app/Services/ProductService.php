<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class ProductService
{
    public function paginateWithTotals(int $perPage = 12): LengthAwarePaginator
    {
        return Product::query()
            ->with('tags')
            ->withSum('stocks as total_stock', 'stock')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function popularTags(int $limit = 10): Collection
    {
        return Tag::query()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit($limit)
            ->get();
    }

    public function productDetails(string $sku): array
    {
        $key = "product:{$sku}:details";

        $data = Cache::remember($key, 600, function () use ($sku) {
            $product = Product::query()
                ->with('tags')
                ->where('sku', $sku)
                ->firstOrFail();

            $tagIds = $product->tags->pluck('id');

            $related = Product::query()
                ->whereKeyNot($product->id)
                ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds))
                ->limit(8)
                ->get();

            return compact('product', 'related');
        });

        $data['product']->loadSum('stocks as live_stock', 'stock');

        return $data;
    }
}

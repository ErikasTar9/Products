<?php

namespace App\Services\Api;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductService
{
    public function paginateWithTotals(int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('tags')
            ->withSum('stocks as total_stock', 'stock')
            ->orderBy('id')
            ->paginate($perPage);
    }
}

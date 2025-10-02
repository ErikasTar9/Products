<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Services\ProductService;

class ProductsController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(IndexRequest $request)
    {
        $products = $this->productService->paginateWithTotals($request->perPage());
        $popularTags = $this->productService->popularTags();

        return view('products.index', compact('products', 'popularTags'));
    }

    public function show(string $sku)
    {
        $data = $this->productService->productDetails($sku);
        $popularTags = $this->productService->popularTags();

        return view('products.show', [
            'product' => $data['product'],
            'related' => $data['related'],
            'liveStock' => ($data['product']->live_stock ?? 0),
            'popularTags' => $popularTags,
        ]);
    }
}

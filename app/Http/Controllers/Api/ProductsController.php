<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexRequest;
use App\Services\Api\ProductService;

class ProductsController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(IndexRequest $request)
    {
        $paginated = $this->productService->paginateWithTotals($request->perPage());

        return response()->json($paginated);
    }
}

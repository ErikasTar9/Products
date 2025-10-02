@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Products</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Refresh</a>
    </div>

    @if($products->count())
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($products as $product)
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ $product->photo }}" class="card-img-top" alt="{{ $product->sku }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->sku }}</h5>
                            <p class="card-text">{{ Str::limit($product->description, 120) }}</p>
                            <p class="mb-1"><strong>Size:</strong> {{ isset($product->size) ? $product->size : 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Total
                                    stock:</strong> {{ (int)(isset($product->total_stock) ? $product->total_stock : 0) }}
                            </p>
                            <div>
                                @foreach($product->tags as $tag)
                                    <span class="badge text-bg-light border">#{{ $tag->title }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a class="btn btn-primary w-100" href="{{ route('products.show', $product->sku) }}">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
    @else
        <div class="alert alert-info">No products found.</div>
    @endif
@endsection

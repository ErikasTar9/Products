@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ $product->photo }}" class="img-fluid rounded-start w-100 h-100 object-fit-cover"
                             alt="{{ $product->sku }}">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h3 class="card-title">{{ $product->sku }}</h3>
                            <p class="card-text">{{ $product->description }}</p>
                            <p class="mb-1"><strong>Size:</strong> {{ isset($product->size) ? $product->size : 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Updated
                                    at:</strong> {{ isset($product->updated_at) ? Carbon::parse($product->updated_at)->toDateString() : 'N/A' }}
                            </p>
                            <div class="mb-2">
                                @foreach($product->tags as $tag)
                                    <span class="badge text-bg-light border">#{{ $tag->title }}</span>
                                @endforeach
                            </div>
                            <div class="alert alert-info py-2">
                                Stock is shown in real time and is not cached.
                            </div>
                            <p class="fs-5"><strong>Total stock:</strong> {{ (int)(isset($liveStock) ? $liveStock : 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($related) && count($related))
                <div class="mt-4">
                    <h4>Related products</h4>
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        @foreach($related as $rel)
                            <div class="col">
                                <div class="card h-100">
                                    <img src="{{ $rel->photo }}" class="card-img-top" alt="{{ $rel->sku }}">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $rel->sku }}</h6>
                                        <p class="card-text small">{{ Str::limit($rel->description, 80) }}</p>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <a class="btn btn-outline-primary w-100 btn-sm"
                                           href="{{ route('products.show', $rel->sku) }}">View</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-lg-4">
            @if(!empty($popularTags) && count($popularTags))
                <div class="card">
                    <div class="card-header">Most popular tags</div>
                    <ul class="list-group list-group-flush">
                        @foreach($popularTags as $pt)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>#{{ $pt['title'] }}</span>
                                <span class="badge text-bg-secondary">{{ (int)$pt['products_count'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection

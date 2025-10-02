<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'description' => $this->description,
            'total_stock' => (int) ($this->total_stock ?? 0),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}

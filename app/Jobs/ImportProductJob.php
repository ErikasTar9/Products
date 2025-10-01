<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload)
    {
        $this->onQueue('products');
    }

    public function handle(): void
    {
        $data = $this->payload;

        $product = Product::updateOrCreate(
            ['sku' => $data['sku']],
            [
                'description' => $data['description'] ?? null,
                'size' => $data['size'] ?? null,
                'photo' => $data['photo'] ?? null,
                'source_updated_at' => $data['updated_at'] ?? null,
            ]
        );

        $product->tags()->sync($this->resolveTagIds($data['tags'] ?? []));
    }

    private function resolveTagIds(array $tags): array
    {
        $ids = [];

        foreach ($tags as $tag) {
            $title = is_array($tag) ? ($tag['title'] ?? null) : (is_string($tag) ? trim($tag) : null);
            if (! $title) {
                continue;
            }

            $ids[] = Tag::firstOrCreate(['title' => $title])->id;
        }

        return $ids;
    }
}

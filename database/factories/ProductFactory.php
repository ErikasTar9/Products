<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => 'KF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(8),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL']),
            'photo' => $this->faker->imageUrl(200, 150, 'products', true),
            'source_updated_at' => $this->faker->date(),
        ];
    }
}

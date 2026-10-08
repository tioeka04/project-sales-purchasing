<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_code' => strtoupper(fake()->unique()->bothify('PRD-#####')),
            'product_name' => ucfirst(fake()->unique()->words(3, true)),
            'price' => fake()->numberBetween(1, 100) * 1000,
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}

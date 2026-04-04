<?php

namespace Database\Factories;

use App\Models\Store\Product;
use App\Models\Store\ProductInstance;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInstanceFactory extends Factory
{
    protected $model = ProductInstance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price' => $this->faker->randomFloat(2, 5, 150),
            'stock' => $this->faker->numberBetween(0, 60),
            'redline' => $this->faker->numberBetween(2, 10),
            'active' => $this->faker->boolean(85),
            'store' => $this->faker->numberBetween(1, 3),
        ];
    }
}

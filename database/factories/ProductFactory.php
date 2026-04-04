<?php

namespace Database\Factories;

use App\Models\Store\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'regulated_mod',
            'mech_mod',
            'tank',
            'rda',
            'coil',
            'battery',
            'accessory',
            'eliquid',
        ];

        return [
            'name' => ucwords($this->faker->unique()->words(2, true)),
            'sku' => strtoupper($this->faker->unique()->bothify('??##??')),
            'category' => $this->faker->randomElement($categories),
            'cost' => $this->faker->randomFloat(2, 1, 80),
        ];
    }
}

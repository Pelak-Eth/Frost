<?php

namespace Database\Factories;

use App\Models\Store\LiquidProduct;
use App\Models\Store\Recipe;
use App\Models\Store\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiquidProductFactory extends Factory
{
    protected $model = LiquidProduct::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_order_id' => ShopOrder::factory(),
            'recipe_id' => Recipe::factory(),
            'store' => $this->faker->numberBetween(1, 3),
            'size' => $this->faker->randomElement([10, 30, 50, 100]),
            'nicotine' => $this->faker->randomElement([0, 3, 6, 12, 18, 24]),
            'vg' => $this->faker->randomElement([50, 60, 70, 80, 100]),
            'menthol' => $this->faker->numberBetween(0, 5),
            'extra' => $this->faker->boolean(20),
            'mixed' => $this->faker->boolean(95),
            'salt' => $this->faker->boolean(15),
        ];
    }
}

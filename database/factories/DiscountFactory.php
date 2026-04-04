<?php

namespace Database\Factories;

use App\Models\Store\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['amount', 'percent']);
        $filter = $this->faker->randomElement(['none', 'product', 'liquid']);
        $redeemable = $this->faker->boolean(30);

        return [
            'name' => $this->faker->words(2, true).' Discount',
            'type' => $type,
            'filter' => $filter,
            'amount' => $type === 'percent'
                ? $this->faker->numberBetween(5, 50)
                : $this->faker->randomFloat(2, 1, 25),
            'approval' => $this->faker->boolean(40),
            'redeemable' => $redeemable,
            'value' => $redeemable ? $this->faker->numberBetween(50, 600) : null,
        ];
    }
}

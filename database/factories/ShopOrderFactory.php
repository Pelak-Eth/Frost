<?php

namespace Database\Factories;

use App\Models\Auth\User;
use App\Models\Store\Customer;
use App\Models\Store\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopOrderFactory extends Factory
{
    protected $model = ShopOrder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 5, 250);
        $tax = round($subtotal * 0.06, 2);

        return [
            'store' => $this->faker->numberBetween(1, 3),
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'subtotal' => $subtotal,
            'total' => $subtotal + $tax,
            'complete' => $this->faker->boolean(85),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn (array $attrs) => $attrs['created_at'],
        ];
    }
}

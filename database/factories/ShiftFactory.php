<?php

namespace Database\Factories;

use App\Models\Auth\User;
use App\Models\Store\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-30 days', 'now');
        $end = (clone $start)->modify('+8 hours');

        return [
            'user_id' => User::factory(),
            'store' => $this->faker->numberBetween(1, 3),
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'in' => $start->format('H:i:s'),
            'out' => $end->format('H:i:s'),
        ];
    }
}

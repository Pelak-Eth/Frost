<?php

namespace Database\Factories;

use App\Models\Store\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $adjectives = ['Wild', 'Frozen', 'Sweet', 'Sour', 'Tropical', 'Midnight', 'Berry', 'Cosmic'];
        $nouns = ['Blast', 'Storm', 'Dream', 'Punch', 'Mist', 'Crush', 'Burst', 'Wave'];

        return [
            'name' => $this->faker->randomElement($adjectives).' '.$this->faker->randomElement($nouns),
            'sku' => strtoupper($this->faker->unique()->bothify('R###')),
            'active' => $this->faker->boolean(90),
        ];
    }
}

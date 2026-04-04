<?php

namespace Database\Factories;

use App\Models\Store\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vendors = ['TPA', 'Capella', 'FlavorWest', 'Inawera', 'FlavourArt', 'Real Flavors'];
        $flavors = [
            'Blueberry', 'Strawberry', 'Watermelon', 'Mango', 'Vanilla',
            'Custard', 'Menthol', 'Caramel', 'Peach', 'Raspberry',
            'Apple', 'Cinnamon', 'Coconut', 'Pineapple', 'Lemon',
            'Lime', 'Grape', 'Cherry', 'Banana', 'Honey',
        ];

        return [
            'name' => $this->faker->randomElement($flavors).' '.ucfirst($this->faker->unique()->word()),
            'vendor' => $this->faker->randomElement($vendors),
        ];
    }
}

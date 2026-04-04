<?php

namespace Database\Seeders;

use App\Models\Store\Ingredient;
use App\Models\Store\Recipe;
use Illuminate\Database\Seeder;

class DemoRecipeSeeder extends Seeder
{
    /**
     * Create 20 recipes, each with 2-5 ingredients (with a pivot 'amount' in
     * drops/percent — matching the recipe_ingredient.amount column).
     */
    public function run(): void
    {
        $ingredientIds = Ingredient::pluck('id')->all();

        Recipe::factory()
            ->count(20)
            ->create()
            ->each(function (Recipe $recipe) use ($ingredientIds): void {
                $picked = collect($ingredientIds)
                    ->shuffle()
                    ->take(random_int(2, 5));

                $pivot = $picked->mapWithKeys(fn (int $id): array => [
                    $id => [
                        'amount' => random_int(2, 25),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ])->all();

                $recipe->ingredients()->attach($pivot);
            });
    }
}

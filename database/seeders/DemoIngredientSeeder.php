<?php

namespace Database\Seeders;

use App\Models\Store\Ingredient;
use Illuminate\Database\Seeder;

class DemoIngredientSeeder extends Seeder
{
    public function run(): void
    {
        Ingredient::factory()->count(30)->create();
    }
}

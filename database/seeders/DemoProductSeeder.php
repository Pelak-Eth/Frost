<?php

namespace Database\Seeders;

use App\Models\Store\Product;
use App\Models\Store\ProductInstance;
use Illuminate\Database\Seeder;

class DemoProductSeeder extends Seeder
{
    /**
     * Create 100 products and stock each one in all three stores via
     * ProductInstance rows.
     */
    public function run(): void
    {
        Product::factory()
            ->count(100)
            ->create()
            ->each(function (Product $product): void {
                foreach ([1, 2, 3] as $store) {
                    ProductInstance::factory()->create([
                        'product_id' => $product->id,
                        'store' => $store,
                    ]);
                }
            });
    }
}

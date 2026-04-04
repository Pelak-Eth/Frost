<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Store\Customer;
use App\Models\Store\Discount;
use App\Models\Store\LiquidProduct;
use App\Models\Store\Payment;
use App\Models\Store\ProductInstance;
use App\Models\Store\Recipe;
use App\Models\Store\ShopOrder;
use Illuminate\Database\Seeder;

class DemoShopOrderSeeder extends Seeder
{
    /**
     * Create 200 shop orders. Each order has:
     *   - 1-5 product line items (pivot 'quantity')
     *   - 0-2 liquid products (custom mixed bottles)
     *   - 0-2 discounts applied (pivot 'applied' dollar amount)
     *   - 1 payment that totals the order
     */
    public function run(): void
    {
        $customerIds = Customer::pluck('id')->all();
        $userIds = User::pluck('id')->all();
        $productInstanceIds = ProductInstance::pluck('id')->all();
        $recipeIds = Recipe::pluck('id')->all();
        $discountIds = Discount::pluck('id')->all();

        $orderCount = 200;

        for ($i = 0; $i < $orderCount; $i++) {
            $order = ShopOrder::factory()->create([
                'customer_id' => $customerIds[array_rand($customerIds)],
                'user_id' => $userIds[array_rand($userIds)],
            ]);

            $picked = collect($productInstanceIds)
                ->shuffle()
                ->take(random_int(1, 5));

            $pivot = $picked->mapWithKeys(fn (int $id): array => [
                $id => ['quantity' => random_int(1, 4)],
            ])->all();

            $order->productInstances()->attach($pivot);

            $liquidCount = random_int(0, 2);
            for ($l = 0; $l < $liquidCount; $l++) {
                LiquidProduct::factory()->create([
                    'shop_order_id' => $order->id,
                    'recipe_id' => $recipeIds[array_rand($recipeIds)],
                    'store' => $order->store,
                ]);
            }

            if (random_int(1, 100) <= 30 && ! empty($discountIds)) {
                $discountPicks = collect($discountIds)
                    ->shuffle()
                    ->take(random_int(1, 2));

                $discountPivot = $discountPicks->mapWithKeys(fn (int $id): array => [
                    $id => ['applied' => round(random_int(100, 1500) / 100, 2)],
                ])->all();

                $order->discounts()->attach($discountPivot);
            }

            Payment::factory()->create([
                'shop_order_id' => $order->id,
                'amount' => $order->total,
            ]);
        }
    }
}

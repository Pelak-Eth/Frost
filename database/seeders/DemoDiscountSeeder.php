<?php

namespace Database\Seeders;

use App\Models\Store\Discount;
use Illuminate\Database\Seeder;

class DemoDiscountSeeder extends Seeder
{
    /**
     * Seed a curated set of discounts that mirror real shop usage, plus a
     * few Faker-generated ones for variety.
     */
    public function run(): void
    {
        $curated = [
            ['name' => '10% Off',         'type' => 'percent', 'filter' => 'none',    'amount' => 10,    'approval' => false, 'redeemable' => false, 'value' => null],
            ['name' => '50% Off Eliquid', 'type' => 'percent', 'filter' => 'liquid',  'amount' => 50,    'approval' => true,  'redeemable' => false, 'value' => null],
            ['name' => '10ml Discount',   'type' => 'amount',  'filter' => 'liquid',  'amount' => 4.49,  'approval' => true,  'redeemable' => true,  'value' => 100],
            ['name' => '30ml Discount',   'type' => 'amount',  'filter' => 'liquid',  'amount' => 11.49, 'approval' => true,  'redeemable' => true,  'value' => 200],
            ['name' => '50ml Discount',   'type' => 'amount',  'filter' => 'liquid',  'amount' => 18.49, 'approval' => true,  'redeemable' => true,  'value' => 300],
            ['name' => '100ml Discount',  'type' => 'amount',  'filter' => 'liquid',  'amount' => 34.49, 'approval' => true,  'redeemable' => true,  'value' => 600],
        ];

        foreach ($curated as $row) {
            Discount::create($row);
        }

        Discount::factory()->count(5)->create();
    }
}

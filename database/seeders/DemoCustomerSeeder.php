<?php

namespace Database\Seeders;

use App\Models\Store\Customer;
use Illuminate\Database\Seeder;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()
            ->count(50)
            ->create([
                'points' => fn () => random_int(0, 1500),
            ]);
    }
}

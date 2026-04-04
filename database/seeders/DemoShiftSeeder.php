<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Store\Shift;
use Illuminate\Database\Seeder;

class DemoShiftSeeder extends Seeder
{
    /**
     * Give each non-admin user 3-7 historical shift records.
     */
    public function run(): void
    {
        $users = User::where('email', '!=', 'admin@frostpos.com')->get();

        foreach ($users as $user) {
            Shift::factory()
                ->count(random_int(3, 7))
                ->create(['user_id' => $user->id]);
        }
    }
}

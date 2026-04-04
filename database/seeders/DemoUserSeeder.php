<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Create a handful of staff users across roles, in addition to the
     * canonical admin from UserTableSeeder.
     */
    public function run(): void
    {
        $managerRole = Role::where('name', 'manager')->firstOrFail();
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        User::factory()
            ->count(3)
            ->create()
            ->each(fn (User $user) => $user->roles()->syncWithoutDetaching([$managerRole->id]));

        User::factory()
            ->count(6)
            ->create();
    }
}

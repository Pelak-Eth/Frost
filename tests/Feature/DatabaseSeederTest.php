<?php

namespace Tests\Feature;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Store\Customer;
use App\Models\Store\Discount;
use App\Models\Store\Ingredient;
use App\Models\Store\LiquidProduct;
use App\Models\Store\Payment;
use App\Models\Store\Product;
use App\Models\Store\ProductInstance;
use App\Models\Store\Recipe;
use App\Models\Store\Shift;
use App\Models\Store\ShopOrder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_calls_user_table_seeder()
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@frostpos.com',
            'name' => 'Admin',
        ]);
    }

    public function test_user_table_seeder_creates_default_admin_with_roles()
    {
        $this->seed(UserTableSeeder::class);

        $admin = User::where('email', 'admin@frostpos.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasRole('manager'));
    }

    public function test_user_table_seeder_is_idempotent()
    {
        $this->seed(UserTableSeeder::class);
        $this->seed(UserTableSeeder::class);

        $this->assertEquals(1, User::where('email', 'admin@frostpos.com')->count());
        $this->assertEquals(1, Role::where('name', 'admin')->count());
        $this->assertEquals(1, Role::where('name', 'manager')->count());
    }

    public function test_seeded_admin_password_is_hashed_via_cast()
    {
        $this->seed(UserTableSeeder::class);

        $admin = User::where('email', 'admin@frostpos.com')->first();

        $this->assertNotEquals('password', $admin->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $admin->password));
    }

    public function test_database_seeder_populates_full_demo_dataset(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(10, User::count(), 'expected 1 admin + 9 demo users');
        $this->assertSame(30, Ingredient::count());
        $this->assertSame(20, Recipe::count());
        $this->assertSame(100, Product::count());
        $this->assertSame(300, ProductInstance::count(), '100 products x 3 stores');
        $this->assertSame(11, Discount::count(), '6 curated + 5 faker');
        $this->assertSame(50, Customer::count());
        $this->assertSame(200, ShopOrder::count());
        $this->assertSame(200, Payment::count());
    }

    public function test_demo_orders_reference_valid_foreign_keys(): void
    {
        $this->seed(DatabaseSeeder::class);

        $userIds = User::pluck('id')->all();
        $customerIds = Customer::pluck('id')->all();

        foreach (ShopOrder::all() as $order) {
            $this->assertContains($order->user_id, $userIds);
            $this->assertContains($order->customer_id, $customerIds);
            $this->assertContains($order->store, [1, 2, 3]);
            $this->assertGreaterThanOrEqual(1, $order->productInstances()->count());
        }
    }

    public function test_demo_orders_have_attached_payments_and_some_discounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            ShopOrder::count(),
            Payment::count(),
            'every order should have exactly one payment'
        );

        $this->assertGreaterThan(
            0,
            DB::table('order_discount')->count(),
            'some orders should have discounts attached'
        );

        $this->assertGreaterThan(
            0,
            LiquidProduct::count(),
            'some orders should have liquid products'
        );
    }

    public function test_demo_recipes_have_attached_ingredients(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (Recipe::all() as $recipe) {
            $this->assertGreaterThanOrEqual(2, $recipe->ingredients()->count());
            $this->assertLessThanOrEqual(5, $recipe->ingredients()->count());
        }
    }

    public function test_demo_shift_seeder_creates_shifts_for_non_admin_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Shift::count());

        $adminId = User::where('email', 'admin@frostpos.com')->value('id');
        $this->assertSame(0, Shift::where('user_id', $adminId)->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function as(string $role): static
    {
        return $this->actingAs(User::factory()->create()->assignRole($role));
    }

    public function test_admin_can_open_every_menu(): void
    {
        $this->as('Admin');

        foreach ([
            'admin.dashboard', 'admin.master.products.index', 'admin.master.suppliers.index',
            'admin.master.customers.index', 'admin.purchasing.purchase-orders.index',
            'admin.sales.sales-orders.index', 'admin.settings.users.index', 'admin.settings.roles.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_purchasing_staff_is_blocked_from_sales_and_settings(): void
    {
        $this->as('Purchasing Staff');

        $this->get(route('admin.purchasing.purchase-orders.index'))->assertOk();
        $this->get(route('admin.sales.sales-orders.index'))->assertForbidden();
        $this->get(route('admin.settings.users.index'))->assertForbidden();
        $this->get(route('admin.settings.roles.index'))->assertForbidden();
    }

    public function test_sales_staff_is_blocked_from_purchasing(): void
    {
        $this->as('Sales Staff');

        $this->get(route('admin.sales.sales-orders.index'))->assertOk();
        $this->get(route('admin.purchasing.purchase-orders.index'))->assertForbidden();
        $this->get(route('admin.purchasing.purchase-orders.create'))->assertForbidden();
    }

    public function test_master_data_write_access_follows_the_matrix(): void
    {
        $this->as('Purchasing Staff');

        $this->get(route('admin.master.products.index'))->assertOk();
        $this->get(route('admin.master.products.create'))->assertForbidden();
        $this->post(route('admin.master.products.store'), [])->assertForbidden();
        $this->get(route('admin.master.suppliers.create'))->assertOk();
        $this->get(route('admin.master.customers.create'))->assertForbidden();
    }

    public function test_manager_is_read_only_except_for_approvals(): void
    {
        $this->as('Manager');

        $this->get(route('admin.purchasing.purchase-orders.index'))->assertOk();
        $this->get(route('admin.purchasing.purchase-orders.create'))->assertForbidden();
        $this->get(route('admin.sales.sales-orders.create'))->assertForbidden();
        $this->delete(route('admin.master.suppliers.destroy', Supplier::factory()->create()))->assertForbidden();
    }

    public function test_sidebar_hides_menus_the_user_may_not_open(): void
    {
        $this->as('Sales Staff');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sales Orders')
            ->assertDontSee('Purchase Orders')
            ->assertDontSee('Roles &amp; Permissions', false);
    }

    public function test_products_used_in_transactions_cannot_be_deleted(): void
    {
        $admin = User::factory()->create()->assignRole('Admin');
        $product = Product::factory()->create();

        $this->actingAs($admin)->post(route('admin.purchasing.purchase-orders.store'), [
            'supplier_id' => Supplier::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [['product_id' => $product->id, 'quantity' => 1, 'price' => 10, 'discount' => 0]],
        ]);
        $this->assertSame(1, PurchaseOrder::count());

        $this->delete(route('admin.master.products.destroy', $product))->assertSessionHas('error');
        $this->assertModelExists($product);
    }

    public function test_master_data_validation(): void
    {
        $this->as('Admin');
        Product::factory()->create(['product_code' => 'DUP-1']);

        $this->post(route('admin.master.products.store'), [
            'product_code' => 'DUP-1', 'product_name' => '', 'price' => -5, 'stock' => -1,
        ])->assertSessionHasErrors(['product_code', 'product_name', 'price', 'stock']);

        $this->post(route('admin.master.suppliers.store'), [
            'supplier_code' => 'S 1!', 'name' => 'X', 'phone' => 'abc', 'email' => 'not-an-email',
        ])->assertSessionHasErrors(['supplier_code', 'phone', 'email']);
    }

    public function test_user_management_and_admin_role_protection(): void
    {
        $admin = User::factory()->create()->assignRole('Admin');
        $this->actingAs($admin);

        $this->post(route('admin.settings.users.store'), [
            'name' => 'New Staff', 'email' => 'staff@example.com',
            'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1', 'role' => 'Sales Staff',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(User::where('email', 'staff@example.com')->firstOrFail()->hasRole('Sales Staff'));

        $this->delete(route('admin.settings.users.destroy', $admin))->assertSessionHas('error');

        $adminRole = Role::findByName('Admin');
        $this->delete(route('admin.settings.roles.destroy', $adminRole))->assertSessionHas('error');
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'password'])->assignRole('Admin');

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}

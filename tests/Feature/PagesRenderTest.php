<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_create_edit_and_show_page_renders(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create()->assignRole('Admin');
        $product = Product::factory()->create(['stock' => 50]);

        $this->actingAs($admin)->post(route('admin.purchasing.purchase-orders.store'), [
            'supplier_id' => Supplier::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [['product_id' => $product->id, 'quantity' => 2, 'price' => 10, 'discount' => 0]],
        ]);
        $this->post(route('admin.sales.sales-orders.store'), [
            'customer_id' => Customer::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [['product_id' => $product->id, 'quantity' => 2, 'price' => 10, 'discount' => 0]],
        ]);

        $purchaseOrder = PurchaseOrder::firstOrFail();
        $salesOrder = SalesOrder::firstOrFail();

        $pages = [
            route('admin.master.products.create'),
            route('admin.master.products.edit', $product),
            route('admin.master.suppliers.create'),
            route('admin.master.suppliers.edit', Supplier::first()),
            route('admin.master.customers.create'),
            route('admin.master.customers.edit', Customer::first()),
            route('admin.purchasing.purchase-orders.create'),
            route('admin.purchasing.purchase-orders.show', $purchaseOrder),
            route('admin.purchasing.purchase-orders.edit', $purchaseOrder),
            route('admin.sales.sales-orders.create'),
            route('admin.sales.sales-orders.show', $salesOrder),
            route('admin.sales.sales-orders.edit', $salesOrder),
            route('admin.settings.users.create'),
            route('admin.settings.users.edit', $admin),
            route('admin.settings.roles.create'),
            route('admin.settings.roles.edit', Role::findByName('Manager')),
        ];

        foreach ($pages as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_old_input_and_line_errors_are_shown_when_validation_fails(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create()->assignRole('Admin');
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->from(route('admin.purchasing.purchase-orders.create'))
            ->post(route('admin.purchasing.purchase-orders.store'), [
                'supplier_id' => Supplier::factory()->create()->id,
                'order_date' => now()->toDateString(),
                'lines' => [['product_id' => $product->id, 'quantity' => 0, 'price' => 10, 'discount' => 150]],
            ])
            ->assertRedirect(route('admin.purchasing.purchase-orders.create'));

        $this->get(route('admin.purchasing.purchase-orders.create'))
            ->assertOk()
            ->assertSee('Quantity field must be at least 1', false)
            ->assertSee('Discount field must be between 0 and 100', false);
    }
}

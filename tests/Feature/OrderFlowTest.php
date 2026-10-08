<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create()->assignRole($role);
    }

    private function purchasePayload(Product $product, array $line = []): array
    {
        return [
            'supplier_id' => Supplier::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [array_merge([
                'product_id' => $product->id,
                'quantity' => 10,
                'price' => 1000.50,
                'discount' => 10,
            ], $line)],
        ];
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/master/products')->assertRedirect('/login');
    }

    public function test_purchase_order_totals_are_computed_on_the_server(): void
    {
        $product = Product::factory()->create();
        $staff = $this->userWithRole('Purchasing Staff');

        $payload = $this->purchasePayload($product) + ['grand_total' => 1];
        $payload['lines'][0]['total'] = 1;

        $this->actingAs($staff)->post(route('admin.purchasing.purchase-orders.store'), $payload)->assertSessionHasNoErrors();

        $order = PurchaseOrder::with('lines')->firstOrFail();
        // 10 x 1000.50 = 10005.00, minus 10% = 9004.50 (decimal price/discount must not be truncated)
        $this->assertSame('9004.50', $order->lines->first()->total);
        $this->assertSame('9004.50', $order->grand_total);
        $this->assertSame(OrderStatus::Draft, $order->status);
        $this->assertMatchesRegularExpression('/^PO-\d{8}-0001$/', $order->po_number);
    }

    public function test_purchase_order_lifecycle_adds_stock_on_receive(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $staff = $this->userWithRole('Purchasing Staff');
        $manager = $this->userWithRole('Manager');

        $this->actingAs($staff)->post(route('admin.purchasing.purchase-orders.store'), $this->purchasePayload($product));
        $order = PurchaseOrder::firstOrFail();

        // Staff may not approve; the manager can.
        $this->actingAs($staff)->patch(route('admin.purchasing.purchase-orders.approve', $order))->assertForbidden();
        $this->actingAs($manager)->patch(route('admin.purchasing.purchase-orders.approve', $order))->assertSessionHas('success');

        // Receiving a draft is impossible, receiving an approved order adds the stock exactly once.
        $this->actingAs($staff)->patch(route('admin.purchasing.purchase-orders.receive', $order));
        $this->assertSame(15, $product->fresh()->stock);

        $this->actingAs($staff)->patch(route('admin.purchasing.purchase-orders.receive', $order))->assertSessionHas('error');
        $this->assertSame(15, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
    }

    public function test_approved_orders_cannot_be_edited(): void
    {
        $product = Product::factory()->create();
        $admin = $this->userWithRole('Admin');

        $this->actingAs($admin)->post(route('admin.purchasing.purchase-orders.store'), $this->purchasePayload($product));
        $order = PurchaseOrder::firstOrFail();
        $this->actingAs($admin)->patch(route('admin.purchasing.purchase-orders.approve', $order));

        $this->actingAs($admin)
            ->put(route('admin.purchasing.purchase-orders.update', $order), $this->purchasePayload($product))
            ->assertSessionHas('error');
    }

    public function test_sales_order_is_rejected_when_quantity_exceeds_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);
        $staff = $this->userWithRole('Sales Staff');

        $this->actingAs($staff)->post(route('admin.sales.sales-orders.store'), [
            'customer_id' => Customer::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [['product_id' => $product->id, 'quantity' => 4, 'price' => 100, 'discount' => 0]],
        ])->assertSessionHasErrors('lines.0.quantity');

        $this->assertSame(0, SalesOrder::count());
    }

    public function test_sales_order_delivery_reduces_stock_and_rechecks_availability(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $staff = $this->userWithRole('Sales Staff');
        $manager = $this->userWithRole('Manager');

        $this->actingAs($staff)->post(route('admin.sales.sales-orders.store'), [
            'customer_id' => Customer::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [['product_id' => $product->id, 'quantity' => 8, 'price' => 100, 'discount' => 0]],
        ])->assertSessionHasNoErrors();
        $order = SalesOrder::firstOrFail();

        $this->actingAs($manager)->patch(route('admin.sales.sales-orders.approve', $order));

        // Stock drops after the order was created (e.g. another sale).
        $product->update(['stock' => 5]);
        $this->actingAs($staff)->patch(route('admin.sales.sales-orders.deliver', $order))->assertSessionHas('error');
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Approved, $order->fresh()->status);

        $product->update(['stock' => 10]);
        $this->actingAs($staff)->patch(route('admin.sales.sales-orders.deliver', $order))->assertSessionHas('success');
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
    }

    #[DataProvider('invalidOrderLines')]
    public function test_purchase_order_validation_rejects_bad_input(array $override, string $errorKey): void
    {
        $product = Product::factory()->create();
        $admin = $this->userWithRole('Admin');
        $payload = array_replace_recursive($this->purchasePayload($product), $override);

        $this->actingAs($admin)
            ->post(route('admin.purchasing.purchase-orders.store'), $payload)
            ->assertSessionHasErrors($errorKey);

        $this->assertSame(0, PurchaseOrder::count());
    }

    public static function invalidOrderLines(): array
    {
        return [
            'discount above 100' => [['lines' => [['discount' => 150]]], 'lines.0.discount'],
            'zero quantity' => [['lines' => [['quantity' => 0]]], 'lines.0.quantity'],
            'negative price' => [['lines' => [['price' => -1]]], 'lines.0.price'],
            'future date' => [['order_date' => '2999-01-01'], 'order_date'],
            'unknown supplier' => [['supplier_id' => 99999], 'supplier_id'],
            'unknown product' => [['lines' => [['product_id' => 99999]]], 'lines.0.product_id'],
        ];
    }

    public function test_purchase_order_requires_at_least_one_line_and_no_duplicate_products(): void
    {
        $product = Product::factory()->create();
        $admin = $this->userWithRole('Admin');

        $this->actingAs($admin)->post(route('admin.purchasing.purchase-orders.store'), [
            'supplier_id' => Supplier::factory()->create()->id,
            'order_date' => now()->toDateString(),
        ])->assertSessionHasErrors('lines');

        $line = ['product_id' => $product->id, 'quantity' => 1, 'price' => 10, 'discount' => 0];
        $this->actingAs($admin)->post(route('admin.purchasing.purchase-orders.store'), [
            'supplier_id' => Supplier::factory()->create()->id,
            'order_date' => now()->toDateString(),
            'lines' => [$line, $line],
        ])->assertSessionHasErrors('lines.0.product_id');
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesOrderService extends OrderService
{
    public function create(array $data, User $user): SalesOrder
    {
        return DB::transaction(function () use ($data, $user) {
            $lines = $this->buildLines($data['lines']);

            $order = SalesOrder::create([
                'so_number' => $this->nextNumber(SalesOrder::class, 'so_number', 'SO'),
                'customer_id' => $data['customer_id'],
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
                'status' => OrderStatus::Draft,
                'grand_total' => $this->sumLines($lines),
                'created_by' => $user->id,
            ]);

            $order->lines()->createMany($lines);

            return $order;
        });
    }

    public function update(SalesOrder $order, array $data): SalesOrder
    {
        return DB::transaction(function () use ($order, $data) {
            $order = $this->lock($order);
            $this->ensure($order->status->isEditable(), __('Only draft orders can be edited.'));

            $lines = $this->buildLines($data['lines']);

            $order->update([
                'customer_id' => $data['customer_id'],
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
                'grand_total' => $this->sumLines($lines),
            ]);

            $order->lines()->delete();
            $order->lines()->createMany($lines);

            return $order;
        });
    }

    public function approve(SalesOrder $order, User $user): SalesOrder
    {
        return DB::transaction(function () use ($order, $user) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeApproved(), __('Only draft orders can be approved.'));

            $order->update([
                'status' => OrderStatus::Approved,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            return $order;
        });
    }

    /**
     * Goods shipped to the customer. Stock is re-checked here because it
     * may have changed since the order was created.
     */
    public function deliver(SalesOrder $order): SalesOrder
    {
        return DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeCompleted(), __('Only approved orders can be delivered.'));

            $required = $order->lines->groupBy('product_id')->map->sum('quantity');
            $products = Product::whereKey($required->keys())->lockForUpdate()->get()->keyBy('id');

            foreach ($required as $productId => $quantity) {
                $product = $products->get($productId);

                $this->ensure($product !== null && $product->stock >= $quantity, __('Stock :name only :stock left.', [
                    'name' => $product?->product_name,
                    'stock' => $product?->stock ?? 0,
                ]));
            }

            foreach ($required as $productId => $quantity) {
                Product::whereKey($productId)->decrement('stock', $quantity);
            }

            $order->update([
                'status' => OrderStatus::Completed,
                'completed_at' => now(),
            ]);

            return $order;
        });
    }

    public function cancel(SalesOrder $order): SalesOrder
    {
        return DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeCancelled(), __('This order can no longer be cancelled.'));

            $order->update(['status' => OrderStatus::Cancelled]);

            return $order;
        });
    }

    public function delete(SalesOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeDeleted(), __('Only draft or cancelled orders can be deleted.'));

            $order->delete();
        });
    }
}

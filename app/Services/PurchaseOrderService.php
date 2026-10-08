<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService extends OrderService
{
    public function create(array $data, User $user): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $user) {
            $lines = $this->buildLines($data['lines']);

            $order = PurchaseOrder::create([
                'po_number' => $this->nextNumber(PurchaseOrder::class, 'po_number', 'PO'),
                'supplier_id' => $data['supplier_id'],
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

    public function update(PurchaseOrder $order, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $data) {
            $order = $this->lock($order);
            $this->ensure($order->status->isEditable(), __('Only draft orders can be edited.'));

            $lines = $this->buildLines($data['lines']);

            $order->update([
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
                'grand_total' => $this->sumLines($lines),
            ]);

            $order->lines()->delete();
            $order->lines()->createMany($lines);

            return $order;
        });
    }

    public function approve(PurchaseOrder $order, User $user): PurchaseOrder
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
     * Goods received from the supplier: add every line quantity to stock.
     */
    public function receive(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeCompleted(), __('Only approved orders can be received.'));

            foreach ($order->lines as $line) {
                Product::whereKey($line->product_id)->increment('stock', $line->quantity);
            }

            $order->update([
                'status' => OrderStatus::Completed,
                'completed_at' => now(),
            ]);

            return $order;
        });
    }

    public function cancel(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeCancelled(), __('This order can no longer be cancelled.'));

            $order->update(['status' => OrderStatus::Cancelled]);

            return $order;
        });
    }

    public function delete(PurchaseOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order = $this->lock($order);
            $this->ensure($order->status->canBeDeleted(), __('Only draft or cancelled orders can be deleted.'));

            $order->delete();
        });
    }
}

<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared behaviour for header + line documents (purchase & sales orders).
 * Totals are always computed on the server, never trusted from the form.
 */
abstract class OrderService
{
    public function calculateLineTotal(int $qty, float $price, float $discountPercent): float
    {
        $subtotal = $qty * $price;

        return round($subtotal - ($subtotal * $discountPercent / 100), 2);
    }

    /**
     * Normalise validated line input and attach the server-side total.
     *
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    protected function buildLines(array $lines): array
    {
        return array_map(function (array $line) {
            $quantity = (int) $line['quantity'];
            $price = (float) $line['price'];
            $discount = (float) ($line['discount'] ?? 0);

            return [
                'product_id' => (int) $line['product_id'],
                'quantity' => $quantity,
                'price' => $price,
                'discount' => $discount,
                'total' => $this->calculateLineTotal($quantity, $price, $discount),
            ];
        }, array_values($lines));
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines  output of buildLines()
     */
    protected function sumLines(array $lines): float
    {
        return round(array_sum(array_column($lines, 'total')), 2);
    }

    /**
     * Next document number for today, e.g. PO-20261008-0001.
     * Must run inside a transaction: the row lock keeps two concurrent
     * requests from receiving the same number.
     *
     * @param  class-string<Model>  $model
     */
    protected function nextNumber(string $model, string $column, string $prefix): string
    {
        $base = $prefix.'-'.now()->format('Ymd').'-';

        $last = $model::query()
            ->where($column, 'like', $base.'%')
            ->lockForUpdate()
            ->orderByDesc($column)
            ->value($column);

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $base.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Re-read the order with a row lock so status checks are race-free.
     *
     * @template T of Model
     *
     * @param  T  $order
     * @return T
     */
    protected function lock(Model $order): Model
    {
        return $order->newQuery()->whereKey($order->getKey())->lockForUpdate()->firstOrFail();
    }

    protected function ensure(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new BusinessRuleException($message);
        }
    }
}

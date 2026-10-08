<?php

namespace App\Enums;

/**
 * Lifecycle shared by purchase orders and sales orders.
 *
 * draft ──approve──▶ approved ──receive/deliver──▶ completed
 *   └──────────────cancel──────────────┘──▶ cancelled
 */
enum OrderStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __(ucfirst($this->value));
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'label-default',
            self::Approved => 'label-info',
            self::Completed => 'label-success',
            self::Cancelled => 'label-danger',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function canBeApproved(): bool
    {
        return $this === self::Draft;
    }

    public function canBeCompleted(): bool
    {
        return $this === self::Approved;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::Draft, self::Approved], true);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this, [self::Draft, self::Cancelled], true);
    }

    /**
     * @return array<string, string> value => label, for select inputs.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}

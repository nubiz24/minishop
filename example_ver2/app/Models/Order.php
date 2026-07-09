<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'note', 'status', 'total_amount'])]
class Order extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Cho xu ly',
            self::STATUS_PROCESSING => 'Dang xu ly',
            self::STATUS_COMPLETED => 'Hoan thanh',
            self::STATUS_CANCELLED => 'Da huy',
        ];
    }

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }
}

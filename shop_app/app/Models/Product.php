<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Database\Factories\ProductFactory;

#[UseFactory(ProductFactory::class)]
#[Fillable(['name', 'slug', 'description', 'price', 'stock', 'image_url', 'is_active'])]
class Product extends Model
{
    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
}

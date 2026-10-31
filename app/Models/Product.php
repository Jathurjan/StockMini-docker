<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'sku', 'category', 'quantity', 'price', 'min_stock'])]
class Product extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'min_stock' => 'integer',
        ];
    }

    /**
     * Get the current stock status.
     */
    protected function stockStatus(): Attribute
    {
        return Attribute::get(fn (): string => match (true) {
            $this->quantity === 0 => 'Out of Stock',
            $this->quantity <= $this->min_stock => 'Low Stock',
            default => 'In Stock',
        });
    }
}

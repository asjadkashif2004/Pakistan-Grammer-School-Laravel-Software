<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'description',
        'cost_price',
        'sale_price',
        'unit_price',
        'stock_qty',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_qty === 0) {
            return 'Out of Stock';
        }

        if ($this->stock_qty <= 10) {
            return 'Low Stock';
        }

        return 'In Stock';
    }
}

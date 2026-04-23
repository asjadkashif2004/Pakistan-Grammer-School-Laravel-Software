<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeVoucherPayment extends Model
{
    protected $fillable = [
        'fee_collection_id',
        'amount',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    public function feeCollection(): BelongsTo
    {
        return $this->belongsTo(FeeCollection::class);
    }
}

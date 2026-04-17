<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'transaction_type',
        'status',
        'amount',
        'hours',
        'transaction_month',
        'payment_method',
        'bank_name',
        'branch_code',
        'iban',
        'account_title',
        'account_number',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'hours' => 'decimal:2',
            'transaction_month' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }
}

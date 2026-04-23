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
        'base_salary',
        'total_advance',
        'total_overtime',
        'absence_deduction',
        'absent_days',
        'hours',
        'overtime_rate',
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
            'base_salary' => 'decimal:2',
            'total_advance' => 'decimal:2',
            'total_overtime' => 'decimal:2',
            'absence_deduction' => 'decimal:2',
            'absent_days' => 'decimal:2',
            'hours' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'transaction_month' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }
}

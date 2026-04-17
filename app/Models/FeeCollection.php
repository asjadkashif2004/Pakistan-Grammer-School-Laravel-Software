<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_number',
        'student_id',
        'amount',
        'arrears',
        'fine',
        'discount',
        'status',
        'billing_month',
        'due_date',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'arrears' => 'decimal:2',
            'fine' => 'decimal:2',
            'discount' => 'decimal:2',
            'billing_month' => 'date',
            'due_date' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

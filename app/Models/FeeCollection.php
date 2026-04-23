<?php

namespace App\Models;

use App\Support\FeeVoucherEngine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class FeeCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_number',
        'student_id',
        'amount',
        'gross_amount',
        'sibling_discount_percentage',
        'sibling_discount_amount',
        'arrears',
        'fine',
        'status',
        'billing_month',
        'due_date',
        'notes',
        'paid_at',
        'voucher_generated_at',
        'rolled_into_fee_collection_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'sibling_discount_percentage' => 'decimal:2',
            'sibling_discount_amount' => 'decimal:2',
            'arrears' => 'decimal:2',
            'fine' => 'decimal:2',
            'billing_month' => 'date',
            'due_date' => 'date',
            'paid_at' => 'date',
            'voucher_generated_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeeVoucherPayment::class)->orderByDesc('paid_at')->orderByDesc('id');
    }

    public function rolledInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rolled_into_fee_collection_id');
    }

    public function scopeActiveVoucher($query)
    {
        return $query->whereNull('rolled_into_fee_collection_id');
    }

    public function totalPaidAmount(): float
    {
        return round((float) $this->payments()->sum('amount'), 2);
    }

    public function remainingAmount(): float
    {
        return FeeVoucherEngine::previewRemaining($this);
    }

    public function syncPaymentStatus(): void
    {
        if ($this->rolled_into_fee_collection_id !== null) {
            return;
        }

        if ($this->status === 'Paid') {
            return;
        }

        FeeVoucherEngine::refreshVoucher($this);
        $this->refresh();

        $total = round((float) $this->amount, 2);
        $paid = $this->totalPaidAmount();
        $remaining = round(max(0, $total - $paid), 2);

        if ($total <= 0.0001 || $remaining <= 0.009) {
            $lastPaid = $this->payments()->orderByDesc('paid_at')->orderByDesc('id')->first();
            $paidAt = $lastPaid ? Carbon::parse($lastPaid->paid_at) : now();

            $this->forceFill([
                'status' => 'Paid',
                'paid_at' => $paidAt,
            ])->saveQuietly();

            return;
        }

        $isPastDue = $this->due_date && $this->due_date->lt(today());

        if ($isPastDue && $remaining > 0.009) {
            $this->forceFill([
                'status' => 'Overdue',
                'paid_at' => null,
            ])->saveQuietly();

            return;
        }

        if ($paid > 0.009) {
            $this->forceFill([
                'status' => 'Partial',
                'paid_at' => null,
            ])->saveQuietly();

            return;
        }

        $this->forceFill([
            'status' => 'Unpaid',
            'paid_at' => null,
        ])->saveQuietly();
    }

    /**
     * Move Unpaid/Partial vouchers with past due dates to Overdue (or Paid if settled).
     */
    public static function syncPastDueStatuses(): void
    {
        FeeVoucherEngine::syncAllOpenVouchersAndDefaulters();
    }
}

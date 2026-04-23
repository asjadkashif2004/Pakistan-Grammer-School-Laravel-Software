<?php

namespace App\Support;

use App\Models\FeeCollection;
use App\Models\Student;
use Illuminate\Support\Carbon;

/**
 * Automated fee: daily fine after due date, sibling discount on subtotal,
 * arrears carry when issuing a new month voucher, defaulter flag sync.
 */
final class FeeVoucherEngine
{
    public const FINE_PER_DAY = 100.0;

    /**
     * @return array{percentage: float, discount_amount: float, final_payable: float, eligible: bool}
     */
    public static function siblingDiscount(Student $student, float $subtotalBeforeDiscount): array
    {
        $cnic = trim((string) ($student->father_cnic ?? ''));
        if ($cnic === '') {
            return [
                'eligible' => false,
                'percentage' => 0.0,
                'discount_amount' => 0.0,
                'final_payable' => round(max(0, $subtotalBeforeDiscount), 2),
            ];
        }

        $siblingCount = Student::query()->where('father_cnic', $cnic)->count();
        $eligible = $siblingCount > 1;
        $percentage = $eligible ? 10.0 : 0.0;
        $discountAmount = $eligible ? round($subtotalBeforeDiscount * 0.10, 2) : 0.0;

        return [
            'eligible' => $eligible,
            'percentage' => $percentage,
            'discount_amount' => $discountAmount,
            'final_payable' => round(max(0, $subtotalBeforeDiscount - $discountAmount), 2),
        ];
    }

    public static function accruedFine(FeeCollection $voucher): float
    {
        if ($voucher->rolled_into_fee_collection_id !== null) {
            return 0.0;
        }
        if ($voucher->status === 'Paid') {
            return 0.0;
        }
        if (! $voucher->due_date) {
            return 0.0;
        }

        $due = Carbon::parse($voucher->due_date)->startOfDay();
        $today = now()->startOfDay();
        if ($due->gte($today)) {
            return 0.0;
        }

        $daysLate = (int) $due->diffInDays($today);

        return round(max(0, $daysLate) * self::FINE_PER_DAY, 2);
    }

    public static function subtotalBeforeDiscount(FeeCollection $voucher): float
    {
        $gross = round((float) ($voucher->gross_amount ?? 0), 2);
        if ($gross <= 0.009) {
            $gross = round((float) $voucher->amount, 2);
        }
        $fine = self::accruedFine($voucher);

        return round($gross + $fine, 2);
    }

    public static function finalPayable(FeeCollection $voucher): float
    {
        $voucher->loadMissing('student');
        $student = $voucher->student;
        if (! $student) {
            return round((float) $voucher->amount, 2);
        }

        $subtotal = self::subtotalBeforeDiscount($voucher);
        $ctx = self::siblingDiscount($student, $subtotal);

        return (float) $ctx['final_payable'];
    }

    public static function previewRemaining(FeeCollection $voucher): float
    {
        if ($voucher->rolled_into_fee_collection_id !== null) {
            return 0.0;
        }

        if ($voucher->status === 'Paid') {
            return 0.0;
        }

        $final = self::finalPayable($voucher);
        $voucher->unsetRelation('payments');
        $paid = round((float) $voucher->payments()->sum('amount'), 2);

        return round(max(0, $final - $paid), 2);
    }

    /**
     * Sum of remaining balance on all active open vouchers strictly before $newBillingStart.
     */
    public static function computeAutoArrears(Student $student, Carbon $newBillingStart): float
    {
        $newKey = $newBillingStart->copy()->startOfMonth()->toDateString();
        $total = 0.0;

        $olds = FeeCollection::query()
            ->where('student_id', $student->id)
            ->whereNull('rolled_into_fee_collection_id')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->whereDate('billing_month', '<', $newKey)
            ->with(['student', 'payments'])
            ->get();

        foreach ($olds as $old) {
            $total += self::previewRemaining($old);
        }

        return round(max(0, $total), 2);
    }

    /**
     * Roll prior open balances into a new voucher (marks old rows as rolled).
     */
    public static function rollPriorOpenVouchersInto(Student $student, Carbon $newBillingStart, FeeCollection $newVoucher): void
    {
        $newKey = $newBillingStart->copy()->startOfMonth()->toDateString();

        FeeCollection::query()
            ->where('student_id', $student->id)
            ->whereNull('rolled_into_fee_collection_id')
            ->where('id', '!=', $newVoucher->id)
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->whereDate('billing_month', '<', $newKey)
            ->update(['rolled_into_fee_collection_id' => $newVoucher->id]);
    }

    public static function refreshVoucher(FeeCollection $voucher): void
    {
        if ($voucher->rolled_into_fee_collection_id !== null) {
            return;
        }

        if ($voucher->status === 'Paid') {
            return;
        }

        $voucher->loadMissing('student');
        $student = $voucher->student;
        if (! $student) {
            return;
        }

        $fine = self::accruedFine($voucher);
        $subtotal = self::subtotalBeforeDiscount($voucher);
        $ctx = self::siblingDiscount($student, $subtotal);

        $voucher->forceFill([
            'fine' => $fine,
            'sibling_discount_percentage' => (float) $ctx['percentage'],
            'sibling_discount_amount' => (float) $ctx['discount_amount'],
            'amount' => (float) $ctx['final_payable'],
        ])->saveQuietly();
    }

    public static function studentIsDefaulter(Student $student): bool
    {
        $today = now()->startOfDay();

        $open = FeeCollection::query()
            ->where('student_id', $student->id)
            ->whereNull('rolled_into_fee_collection_id')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->with(['student', 'payments'])
            ->get();

        foreach ($open as $v) {
            $remaining = self::previewRemaining($v);
            if ($remaining <= 0.009) {
                continue;
            }

            $billingEnd = $v->billing_month
                ? Carbon::parse($v->billing_month)->copy()->endOfMonth()->startOfDay()
                : null;
            $pastMonthEnd = $billingEnd && $today->gt($billingEnd);

            $pastDue = $v->due_date && $today->gt(Carbon::parse($v->due_date)->startOfDay());

            if ($pastMonthEnd || $pastDue) {
                return true;
            }
        }

        return false;
    }

    public static function syncAllOpenVouchersAndDefaulters(): void
    {
        FeeCollection::query()
            ->whereNull('rolled_into_fee_collection_id')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->with('student')
            ->chunkById(100, function ($chunk): void {
                foreach ($chunk as $voucher) {
                    self::refreshVoucher($voucher);
                    $voucher->unsetRelation('payments');
                    $voucher->syncPaymentStatus();
                }
            });

        Student::query()->chunkById(100, function ($students): void {
            foreach ($students as $student) {
                $flag = self::studentIsDefaulter($student);
                if ((bool) $student->is_defaulter !== $flag) {
                    $student->forceFill(['is_defaulter' => $flag])->saveQuietly();
                }
            }
        });
    }
}

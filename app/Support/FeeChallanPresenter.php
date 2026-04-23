<?php

namespace App\Support;

use App\Models\FeeCollection;
use App\Models\Student;
use Illuminate\Support\Carbon;

/**
 * Builds challan line items for print/PDF from stored voucher + student.
 * Does not mutate the database.
 */
final class FeeChallanPresenter
{
    /**
     * @return array{
     *   admission: float,
     *   annual: float,
     *   tuition: float,
     *   arrears: float,
     *   fine: float,
     *   other: float,
     *   previous: float,
     *   gross_total: float,
     *   discount_percentage: float,
     *   discount_amount: float,
     *   final_payable: float,
     *   total: float,
     *   is_first_month: bool,
     *   defaulter_status: string
     * }
     */
    public static function feeLines(FeeCollection $voucher): array
    {
        $student = $voucher->student;
        $billing = $voucher->billing_month
            ? Carbon::parse($voucher->billing_month)->copy()->startOfMonth()
            : null;

        $isFirstMonth = $student && $billing && self::isAdmissionBillingMonth($student, $billing);

        $admission = $isFirstMonth ? round((float) ($student->admission_fee ?? 0), 2) : 0.0;
        $annual = 0.0;
        $monthly = $student ? round((float) ($student->monthly_fee ?? 0), 2) : 0.0;
        $arrears = round((float) $voucher->arrears, 2);
        $fine = FeeVoucherEngine::accruedFine($voucher);
        $grossStored = round((float) ($voucher->gross_amount ?? 0), 2);
        if ($grossStored <= 0.009) {
            $grossStored = round(max(0, $admission + $monthly + $arrears), 2);
        }

        $subtotal = FeeVoucherEngine::subtotalBeforeDiscount($voucher);
        $discountCtx = $student
            ? FeeVoucherEngine::siblingDiscount($student, $subtotal)
            : ['percentage' => 0.0, 'discount_amount' => 0.0, 'final_payable' => $subtotal];
        $discountPercentage = (float) $discountCtx['percentage'];
        $discountAmount = round((float) $discountCtx['discount_amount'], 2);
        $finalPayable = round((float) FeeVoucherEngine::finalPayable($voucher), 2);

        $defaulterStatus = ($student && (bool) $student->is_defaulter) ? 'Yes' : 'No';

        return [
            'admission' => $admission,
            'annual' => $annual,
            'tuition' => $monthly,
            'arrears' => $arrears,
            'fine' => $fine,
            'other' => 0.0,
            'previous' => $arrears,
            'gross_total' => $grossStored,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_payable' => $finalPayable,
            'total' => $finalPayable,
            'is_first_month' => $isFirstMonth,
            'defaulter_status' => $defaulterStatus,
        ];
    }

    public static function isAdmissionBillingMonth(Student $student, Carbon $billingMonthStart): bool
    {
        if (! $student->admission_date) {
            return false;
        }

        $admissionMonth = Carbon::parse($student->admission_date)->copy()->startOfMonth();

        return $billingMonthStart->equalTo($admissionMonth);
    }

    public static function challanDateLabel(FeeCollection $voucher): string
    {
        return optional($voucher->voucher_generated_at)->format('d-m-Y')
            ?? now()->format('d-m-Y');
    }
}

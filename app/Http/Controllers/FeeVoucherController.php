<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\FeeVoucherPayment;
use App\Models\Student;
use App\Support\ActivityLogger;
use App\Support\FeeChallanPresenter;
use App\Support\FeeVoucherEngine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FeeVoucherController extends Controller
{
    public function index(Request $request): View
    {
        FeeCollection::syncPastDueStatuses();

        $selectedStudentCode = strtoupper(trim((string) $request->query('student_code', '')));
        $selectedStudent = $selectedStudentCode !== ''
            ? Student::whereRaw('UPPER(student_code) = ?', [$selectedStudentCode])->first()
            : null;

        $billingMonth = (string) $request->query('month', now()->format('Y-m'));
        $selectedVoucherId = (int) $request->query('voucher', 0);

        $previewVoucher = null;
        if ($selectedVoucherId > 0) {
            $previewVoucher = FeeCollection::with(['student', 'payments'])
                ->where('id', $selectedVoucherId)
                ->first();
        }

        $listCounts = [
            'pending' => 0,
            'paid' => 0,
        ];
        $feeStatusSummary = [
            'total_fee' => 0.0,
            'received' => 0.0,
            'remaining' => 0.0,
            'label' => '—',
            'tone' => 'neutral',
        ];
        $monthBreakdown = collect();

        if ($selectedStudent) {
            $baseQuery = FeeCollection::query()
                ->where('student_id', $selectedStudent->id)
                ->with(['student', 'payments']);

            $listCounts['pending'] = (clone $baseQuery)
                ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
                ->count();

            $listCounts['paid'] = (clone $baseQuery)->where('status', 'Paid')->count();

            $openVoucherIds = FeeCollection::query()
                ->where('student_id', $selectedStudent->id)
                ->whereNull('rolled_into_fee_collection_id')
                ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
                ->pluck('id');

            $totalFee = (float) FeeCollection::query()
                ->whereIn('id', $openVoucherIds)
                ->sum('amount');

            $received = (float) FeeVoucherPayment::query()
                ->whereIn('fee_collection_id', $openVoucherIds)
                ->sum('amount');

            $remaining = round(max(0, $totalFee - $received), 2);

            $feeStatusSummary = [
                'total_fee' => $totalFee,
                'received' => $received,
                'remaining' => $remaining,
                'label' => $this->aggregateFeeStatusLabel($openVoucherIds, $remaining),
                'tone' => $this->aggregateFeeStatusTone($openVoucherIds, $remaining),
            ];

            $monthBreakdown = FeeCollection::where('student_id', $selectedStudent->id)
                ->whereNull('rolled_into_fee_collection_id')
                ->orderByDesc('billing_month')
                ->get()
                ->groupBy(fn (FeeCollection $item) => optional($item->billing_month)->format('Y-m'))
                ->map(function ($items, $month) {
                    $paidCount = $items->where('status', 'Paid')->count();
                    $unpaidItems = $items->whereIn('status', ['Unpaid', 'Partial', 'Overdue']);

                    return [
                        'month_key' => $month,
                        'month_label' => $month ? Carbon::createFromFormat('Y-m', $month)->format('F Y') : 'N/A',
                        'paid_count' => $paidCount,
                        'unpaid_count' => $unpaidItems->count(),
                        'unpaid_amount' => (float) $unpaidItems->sum('amount'),
                    ];
                })
                ->values();
        }

        return view('fee-vouchers.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'selectedStudent' => $selectedStudent,
            'billingMonth' => $billingMonth,
            'previewVoucher' => $previewVoucher,
            'listCounts' => $listCounts,
            'feeStatusSummary' => $feeStatusSummary,
            'selectedStudentCode' => $selectedStudentCode,
            'monthBreakdown' => $monthBreakdown,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'billing_month' => ['required', 'date_format:Y-m'],
            'due_date' => ['required', 'date'],
            'arrears' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:800'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $billingStart = Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth();
        $autoCarry = FeeVoucherEngine::computeAutoArrears($student, $billingStart);
        $arrears = max($autoCarry, (float) ($validated['arrears'] ?? 0));
        $admissionPart = FeeChallanPresenter::isAdmissionBillingMonth($student, $billingStart)
            ? (float) ($student->admission_fee ?? 0)
            : 0.0;
        $grossAmount = max(0, $admissionPart + (float) $student->monthly_fee + $arrears);

        $voucher = DB::transaction(function () use ($validated, $student, $arrears, $grossAmount, $billingStart) {
            $feeCollection = FeeCollection::create([
                'voucher_number' => null,
                'student_id' => $student->id,
                'amount' => 0,
                'gross_amount' => $grossAmount,
                'sibling_discount_percentage' => 0,
                'sibling_discount_amount' => 0,
                'arrears' => $arrears,
                'fine' => 0,
                'status' => 'Unpaid',
                'billing_month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth(),
                'due_date' => Carbon::parse($validated['due_date']),
                'notes' => $validated['notes'] ?? null,
                'voucher_generated_at' => now(),
            ]);

            $feeCollection->update([
                'voucher_number' => sprintf('FV-%s-%05d', now()->format('Y'), $feeCollection->id),
            ]);

            FeeVoucherEngine::rollPriorOpenVouchersInto($student, $billingStart, $feeCollection);
            $feeCollection = $feeCollection->fresh(['student', 'payments']);
            $feeCollection->syncPaymentStatus();

            return $feeCollection->fresh(['student']);
        });

        ActivityLogger::log(
            'voucher.created',
            "Voucher {$voucher->voucher_number} generated for {$student->full_name}.",
            'fee_collection',
            $voucher->id
        );

        $redirectParams = [
            'student_code' => $student->student_code,
            'voucher' => $voucher->id,
            'month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->format('Y-m'),
        ];

        if ($request->expectsJson()) {
            session()->flash('status', 'Fee challan created.');

            return response()->json([
                'print_url' => route('fee-vouchers.print', $voucher),
                'redirect_url' => route('fee-vouchers.index', $redirectParams),
                'message' => 'Fee challan created.',
            ], 201);
        }

        session()->flash('open_print_challan', $voucher->id);

        return redirect()
            ->route('fee-vouchers.index', $redirectParams)
            ->with('status', 'Fee challan created.');
    }

    public function storePayment(Request $request, FeeCollection $feeCollection): RedirectResponse|JsonResponse
    {
        $studentCode = $feeCollection->student?->student_code;
        $workspace = $studentCode !== null && $studentCode !== ''
            ? fn () => redirect()->route('fee-vouchers.index', [
                'student_code' => $studentCode,
                'voucher' => $feeCollection->id,
            ])
            : fn () => redirect()->route('fee-vouchers.list.pending');

        if (! $feeCollection->voucher_generated_at) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Generate the voucher before recording payments.'], 422);
            }

            return $workspace()->with('status', 'Generate the voucher before recording payments.');
        }

        if ($feeCollection->status === 'Paid') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This voucher is already fully paid.'], 422);
            }

            return $workspace()->with('status', 'This voucher is already fully paid.');
        }

        $feeCollection->unsetRelation('payments');
        $remaining = $feeCollection->remainingAmount();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ((float) $validated['amount'] > $remaining + 0.009) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Amount exceeds remaining balance (Rs '.number_format($remaining, 2).').',
                    'errors' => ['amount' => ['Amount exceeds remaining balance (Rs '.number_format($remaining, 2).').']],
                ], 422);
            }

            return $workspace()
                ->withErrors(['amount' => 'Amount exceeds remaining balance (Rs '.number_format($remaining, 2).').'])
                ->withInput();
        }

        DB::transaction(function () use ($feeCollection, $validated) {
            FeeVoucherPayment::create([
                'fee_collection_id' => $feeCollection->id,
                'amount' => $validated['amount'],
                'paid_at' => Carbon::parse($validated['paid_at'])->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $feeCollection->unsetRelation('payments');
            $feeCollection->syncPaymentStatus();
        });

        $feeCollection->refresh();

        ActivityLogger::log(
            'voucher.payment',
            "Payment recorded on voucher {$feeCollection->voucher_number}.",
            'fee_collection',
            $feeCollection->id
        );

        $message = $feeCollection->status === 'Paid'
            ? 'Voucher is fully paid and moved to paid records.'
            : 'Payment recorded successfully.';

        if ($request->expectsJson()) {
            session()->flash('status', $message);

            return response()->json([
                'print_url' => route('fee-vouchers.print', $feeCollection),
                'redirect_url' => $workspace()->getTargetUrl(),
                'message' => $message,
            ]);
        }

        session()->flash('open_print_challan', $feeCollection->id);

        return $workspace()->with('status', $message);
    }

    public function edit(FeeCollection $feeCollection): View|RedirectResponse
    {
        if ($feeCollection->status === 'Paid') {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'Paid vouchers cannot be edited.');
        }

        if ($feeCollection->rolled_into_fee_collection_id !== null) {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'This voucher balance was moved to a newer fee challan.');
        }

        $feeCollection->load('student');

        return view('fee-vouchers.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'voucher' => $feeCollection,
        ]);
    }

    public function update(Request $request, FeeCollection $feeCollection): RedirectResponse
    {
        if ($feeCollection->status === 'Paid') {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'Paid vouchers cannot be edited.');
        }

        if ($feeCollection->rolled_into_fee_collection_id !== null) {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'This voucher balance was moved to a newer fee challan.');
        }

        $validated = $request->validate([
            'billing_month' => ['required', 'date_format:Y-m'],
            'due_date' => ['required', 'date'],
            'arrears' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:800'],
        ]);

        $feeCollection->load('student');
        $student = $feeCollection->student;
        $arrears = (float) ($validated['arrears'] ?? 0);
        $billingStart = Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth();
        $admissionPart = FeeChallanPresenter::isAdmissionBillingMonth($student, $billingStart)
            ? (float) ($student->admission_fee ?? 0)
            : 0.0;
        $grossAmount = max(0, $admissionPart + (float) $student->monthly_fee + $arrears);

        $feeCollection->unsetRelation('payments');
        $feeCollection->fill([
            'gross_amount' => $grossAmount,
            'arrears' => $arrears,
            'billing_month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth(),
            'due_date' => Carbon::parse($validated['due_date']),
            'notes' => $validated['notes'] ?? null,
        ]);
        $previewAmount = FeeVoucherEngine::finalPayable($feeCollection);
        if ($previewAmount + 0.009 < $feeCollection->totalPaidAmount()) {
            return redirect()
                ->route('fee-vouchers.edit', $feeCollection)
                ->withErrors(['amount' => 'Total due cannot be less than the amount already collected (Rs '.number_format($feeCollection->totalPaidAmount(), 2).').']);
        }

        $feeCollection->update([
            'gross_amount' => $grossAmount,
            'arrears' => $arrears,
            'billing_month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth(),
            'due_date' => Carbon::parse($validated['due_date']),
            'notes' => $validated['notes'] ?? null,
        ]);

        $feeCollection->unsetRelation('payments');
        $feeCollection->syncPaymentStatus();

        ActivityLogger::log(
            'voucher.updated',
            "Voucher {$feeCollection->voucher_number} updated.",
            'fee_collection',
            $feeCollection->id
        );

        return redirect()
            ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code, 'voucher' => $feeCollection->id])
            ->with('status', 'Voucher updated successfully.');
    }

    public function destroy(FeeCollection $feeCollection): RedirectResponse
    {
        if ($feeCollection->payments()->exists()) {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'Vouchers with recorded payments cannot be deleted.');
        }

        if ($feeCollection->status === 'Paid') {
            return redirect()
                ->route('fee-vouchers.index', ['student_code' => $feeCollection->student?->student_code])
                ->with('status', 'Paid vouchers cannot be deleted.');
        }

        $studentCode = $feeCollection->student?->student_code;
        $voucherNumber = $feeCollection->voucher_number;
        $voucherId = $feeCollection->id;
        $feeCollection->delete();

        ActivityLogger::log(
            'voucher.deleted',
            "Voucher {$voucherNumber} deleted.",
            'fee_collection',
            $voucherId
        );

        return redirect()
            ->route('fee-vouchers.index', ['student_code' => $studentCode])
            ->with('status', 'Voucher deleted successfully.');
    }

    public function print(FeeCollection $feeCollection): View
    {
        $this->assertVoucherDocumentReady($feeCollection);

        FeeCollection::syncPastDueStatuses();
        $feeCollection->refresh();

        $feeCollection->load(['student', 'payments']);
        $lines = FeeChallanPresenter::feeLines($feeCollection);

        return view('fee-vouchers.print', [
            'voucher' => $feeCollection,
            'isDownload' => false,
            'lines' => $lines,
            'totalPaid' => $feeCollection->totalPaidAmount(),
            'remaining' => $feeCollection->remainingAmount(),
        ]);
    }

    public function download(FeeCollection $feeCollection): Response
    {
        $this->assertVoucherDocumentReady($feeCollection);

        FeeCollection::syncPastDueStatuses();
        $feeCollection->refresh();

        $feeCollection->load(['student', 'payments']);
        $lines = FeeChallanPresenter::feeLines($feeCollection);

        $pdf = Pdf::loadView('fee-vouchers.pdf', [
            'voucher' => $feeCollection,
            'lines' => $lines,
            'totalPaid' => $feeCollection->totalPaidAmount(),
            'remaining' => $feeCollection->remainingAmount(),
        ])->setPaper('a4', 'portrait');

        $fileName = 'fee-challan-'.$feeCollection->voucher_number.'.pdf';

        return $pdf->download($fileName);
    }

    private function assertVoucherDocumentReady(FeeCollection $feeCollection): void
    {
        abort_unless($feeCollection->voucher_generated_at !== null, 404);
    }

    /**
     * @param  Collection<int, int>  $openVoucherIds
     */
    private function aggregateFeeStatusLabel($openVoucherIds, float $remaining): string
    {
        if ($openVoucherIds->isEmpty()) {
            return $remaining <= 0.009 ? 'All clear' : 'No open vouchers';
        }

        if ($remaining <= 0.009) {
            return 'Settled';
        }

        if (FeeCollection::query()->whereIn('id', $openVoucherIds)->where('status', 'Overdue')->exists()) {
            return 'Overdue';
        }

        if (FeeCollection::query()->whereIn('id', $openVoucherIds)->where('status', 'Partial')->exists()) {
            return 'Partial';
        }

        return 'Unpaid';
    }

    /**
     * @param  Collection<int, int>  $openVoucherIds
     */
    private function aggregateFeeStatusTone($openVoucherIds, float $remaining): string
    {
        if ($openVoucherIds->isEmpty() || $remaining <= 0.009) {
            return 'success';
        }

        if (FeeCollection::query()->whereIn('id', $openVoucherIds)->where('status', 'Overdue')->exists()) {
            return 'danger';
        }

        if (FeeCollection::query()->whereIn('id', $openVoucherIds)->where('status', 'Partial')->exists()) {
            return 'warning';
        }

        return 'info';
    }

}

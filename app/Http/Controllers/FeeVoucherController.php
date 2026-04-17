<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\Student;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeeVoucherController extends Controller
{
    public function index(Request $request): View
    {
        FeeCollection::where('status', 'Pending')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'Overdue']);

        $selectedStudentId = (int) $request->query('student_id', 0);
        $selectedStudent = $selectedStudentId > 0 ? Student::find($selectedStudentId) : null;

        $billingMonth = (string) $request->query('month', now()->format('Y-m'));
        $selectedVoucherId = (int) $request->query('voucher', 0);

        $previewVoucher = null;
        if ($selectedVoucherId > 0) {
            $previewVoucher = FeeCollection::with('student')
                ->where('id', $selectedVoucherId)
                ->first();
        }

        $paidCollections = collect();
        $pendingDues = collect();
        $totals = [
            'paid_amount' => 0,
            'pending_amount' => 0,
            'pending_count' => 0,
        ];
        $monthBreakdown = collect();

        if ($selectedStudent) {
            $paidCollections = FeeCollection::with('student')
                ->where('student_id', $selectedStudent->id)
                ->where('status', 'Paid')
                ->latest('paid_at')
                ->take(10)
                ->get();

            $pendingDues = FeeCollection::with('student')
                ->where('student_id', $selectedStudent->id)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->latest('due_date')
                ->take(10)
                ->get();

            $totals['paid_amount'] = (float) FeeCollection::where('student_id', $selectedStudent->id)
                ->where('status', 'Paid')
                ->sum('amount');

            $totals['pending_amount'] = (float) FeeCollection::where('student_id', $selectedStudent->id)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->sum('amount');

            $totals['pending_count'] = (int) FeeCollection::where('student_id', $selectedStudent->id)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->count();

            $monthBreakdown = FeeCollection::where('student_id', $selectedStudent->id)
                ->orderByDesc('billing_month')
                ->get()
                ->groupBy(fn (FeeCollection $item) => optional($item->billing_month)->format('Y-m'))
                ->map(function ($items, $month) {
                    $paidCount = $items->where('status', 'Paid')->count();
                    $unpaidItems = $items->whereIn('status', ['Pending', 'Overdue']);

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
            'paidCollections' => $paidCollections,
            'pendingDues' => $pendingDues,
            'totals' => $totals,
            'selectedStudentId' => $selectedStudentId,
            'monthBreakdown' => $monthBreakdown,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'billing_month' => ['required', 'date_format:Y-m'],
            'due_date' => ['required', 'date'],
            'arrears' => ['nullable', 'numeric', 'min:0'],
            'fine' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:800'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $arrears = (float) ($validated['arrears'] ?? 0);
        $fine = (float) ($validated['fine'] ?? 0);
        $discount = (float) ($validated['discount'] ?? 0);
        $amount = max(0, ((float) $student->monthly_fee + $arrears + $fine) - $discount);

        $voucher = DB::transaction(function () use ($validated, $student, $amount, $arrears, $fine, $discount) {
            $feeCollection = FeeCollection::create([
                'voucher_number' => null,
                'student_id' => $student->id,
                'amount' => $amount,
                'arrears' => $arrears,
                'fine' => $fine,
                'discount' => $discount,
                'status' => 'Pending',
                'billing_month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth(),
                'due_date' => Carbon::parse($validated['due_date']),
                'notes' => $validated['notes'] ?? null,
            ]);

            $feeCollection->update([
                'voucher_number' => sprintf('FV-%s-%05d', now()->format('Y'), $feeCollection->id),
            ]);

            return $feeCollection;
        });
        ActivityLogger::log(
            'voucher.created',
            "Voucher {$voucher->voucher_number} generated for {$student->full_name}.",
            'fee_collection',
            $voucher->id
        );

        return redirect()
            ->route('fee-vouchers.index', [
                'student_id' => $student->id,
                'voucher' => $voucher->id,
                'month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->format('Y-m'),
            ])
            ->with('status', 'Fee voucher generated successfully.');
    }

    public function collect(FeeCollection $feeCollection): RedirectResponse
    {
        $feeCollection->update([
            'status' => 'Paid',
            'paid_at' => now(),
        ]);
        ActivityLogger::log(
            'voucher.paid',
            "Voucher {$feeCollection->voucher_number} marked paid.",
            'fee_collection',
            $feeCollection->id
        );

        return redirect()
            ->route('fee-vouchers.index', ['student_id' => $feeCollection->student_id])
            ->with('status', "Voucher {$feeCollection->voucher_number} marked as paid.");
    }

    public function edit(FeeCollection $feeCollection): View
    {
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
                ->route('fee-vouchers.index', ['student_id' => $feeCollection->student_id])
                ->with('status', 'Paid vouchers cannot be edited.');
        }

        $validated = $request->validate([
            'billing_month' => ['required', 'date_format:Y-m'],
            'due_date' => ['required', 'date'],
            'arrears' => ['nullable', 'numeric', 'min:0'],
            'fine' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:800'],
        ]);

        $student = $feeCollection->student;
        $arrears = (float) ($validated['arrears'] ?? 0);
        $fine = (float) ($validated['fine'] ?? 0);
        $discount = (float) ($validated['discount'] ?? 0);
        $amount = max(0, ((float) $student->monthly_fee + $arrears + $fine) - $discount);

        $feeCollection->update([
            'amount' => $amount,
            'arrears' => $arrears,
            'fine' => $fine,
            'discount' => $discount,
            'billing_month' => Carbon::createFromFormat('Y-m', $validated['billing_month'])->startOfMonth(),
            'due_date' => Carbon::parse($validated['due_date']),
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogger::log(
            'voucher.updated',
            "Voucher {$feeCollection->voucher_number} updated.",
            'fee_collection',
            $feeCollection->id
        );

        return redirect()
            ->route('fee-vouchers.index', ['student_id' => $feeCollection->student_id, 'voucher' => $feeCollection->id])
            ->with('status', 'Voucher updated successfully.');
    }

    public function destroy(FeeCollection $feeCollection): RedirectResponse
    {
        if ($feeCollection->status === 'Paid') {
            return redirect()
                ->route('fee-vouchers.index', ['student_id' => $feeCollection->student_id])
                ->with('status', 'Paid vouchers cannot be deleted.');
        }

        $studentId = (int) $feeCollection->student_id;
        $voucherNumber = $feeCollection->voucher_number;
        $feeCollection->delete();

        ActivityLogger::log(
            'voucher.deleted',
            "Voucher {$voucherNumber} deleted.",
            'fee_collection',
            $feeCollection->id
        );

        return redirect()
            ->route('fee-vouchers.index', ['student_id' => $studentId])
            ->with('status', 'Voucher deleted successfully.');
    }

    public function print(FeeCollection $feeCollection): View
    {
        $feeCollection->load('student');

        return view('fee-vouchers.print', [
            'voucher' => $feeCollection,
            'isDownload' => false,
        ]);
    }

    public function download(FeeCollection $feeCollection)
    {
        $feeCollection->load('student');

        $html = view('fee-vouchers.print', [
            'voucher' => $feeCollection,
            'isDownload' => true,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="voucher-'.$feeCollection->voucher_number.'.html"',
        ]);
    }
}

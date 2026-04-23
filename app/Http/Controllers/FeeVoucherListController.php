<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeVoucherListController extends Controller
{
    public function pending(Request $request): View
    {
        return $this->renderList($request, 'pending', 'Pending vouchers (includes overdue)');
    }

    public function paid(Request $request): View
    {
        return $this->renderList($request, 'paid', 'Paid vouchers');
    }

    private function renderList(Request $request, string $kind, string $title): View
    {
        FeeCollection::syncPastDueStatuses();

        $studentCode = trim((string) $request->query('student_code', ''));
        $className = trim((string) $request->query('class_name', ''));
        $billingMonth = trim((string) $request->query('billing_month', ''));
        $statusFilter = strtolower(trim((string) $request->query('status', '')));

        $query = FeeCollection::query()
            ->whereNull('rolled_into_fee_collection_id')
            ->with(['student', 'payments'])
            ->orderByDesc('due_date')
            ->orderByDesc('id');

        if ($kind === 'pending') {
            $query->whereIn('status', ['Unpaid', 'Partial', 'Overdue']);
            if ($statusFilter === 'unpaid') {
                $query->where('status', 'Unpaid');
            } elseif ($statusFilter === 'partial') {
                $query->where('status', 'Partial');
            }
        } else {
            $query->where('status', 'Paid');
        }

        if ($studentCode !== '') {
            $query->whereHas('student', function ($q) use ($studentCode): void {
                $q->whereRaw('UPPER(student_code) = ?', [strtoupper($studentCode)]);
            });
        }

        if ($className !== '') {
            $query->whereHas('student', fn ($q) => $q->where('class_name', $className));
        }

        if ($billingMonth !== '' && preg_match('/^\d{4}-\d{2}$/', $billingMonth)) {
            $query->whereYear('billing_month', (int) substr($billingMonth, 0, 4))
                ->whereMonth('billing_month', (int) substr($billingMonth, 5, 2));
        }

        $vouchers = $query->paginate(25)->withQueryString();

        $classNames = Student::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return view('fee-vouchers.lists.show', [
            'todayLabel' => now()->format('l, d F Y'),
            'kind' => $kind,
            'title' => $title,
            'vouchers' => $vouchers,
            'filters' => [
                'student_code' => $studentCode,
                'class_name' => $className,
                'billing_month' => $billingMonth,
                'status' => $statusFilter,
            ],
            'classNames' => $classNames,
        ]);
    }
}

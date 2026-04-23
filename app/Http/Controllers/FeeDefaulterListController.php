<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\Student;
use App\Support\FeeVoucherEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeDefaulterListController extends Controller
{
    public function index(Request $request): View
    {
        FeeCollection::syncPastDueStatuses();

        $className = trim((string) $request->query('class_name', ''));

        $students = Student::query()
            ->where('is_defaulter', true)
            ->when($className !== '', fn ($q) => $q->where('class_name', $className))
            ->orderBy('class_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(30)
            ->withQueryString();

        $students->getCollection()->transform(function (Student $student) {
            $open = FeeCollection::query()
                ->where('student_id', $student->id)
                ->whereNull('rolled_into_fee_collection_id')
                ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
                ->with(['student', 'payments'])
                ->orderByDesc('billing_month')
                ->get();

            $feeStatus = $open->pluck('status')->unique()->implode(' / ') ?: '—';
            $arrears = round((float) $open->sum('arrears'), 2);
            $fine = round((float) $open->sum(fn (FeeCollection $v) => FeeVoucherEngine::accruedFine($v)), 2);
            $totalPayable = round((float) $open->sum(fn (FeeCollection $v) => FeeVoucherEngine::previewRemaining($v)), 2);

            return (object) [
                'student' => $student,
                'fee_status' => $feeStatus,
                'arrears' => $arrears,
                'fine' => $fine,
                'total_payable' => $totalPayable,
            ];
        });

        $classNames = Student::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return view('fee-vouchers.lists.defaulters', [
            'todayLabel' => now()->format('l, d F Y'),
            'rows' => $students,
            'filters' => [
                'class_name' => $className,
            ],
            'classNames' => $classNames,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\StaffMember;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();

        $totalStudents = Student::count();
        $activeStaff = StaffMember::where('is_active', true)->count();
        $newHires = StaffMember::where('is_active', true)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $feeCollected = (float) FeeCollection::where('status', 'Paid')
            ->whereYear('billing_month', $now->year)
            ->whereMonth('billing_month', $now->month)
            ->sum('amount');

        $pendingFees = FeeCollection::query()
            ->whereNull('rolled_into_fee_collection_id')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->count();

        $fromDate = $now->copy()->subMonths(9)->startOfMonth();
        $toDate = $now->copy()->endOfMonth();

        $monthTotals = FeeCollection::selectRaw("DATE_FORMAT(billing_month, '%Y-%m') as ym, SUM(amount) as total")
            ->where('status', 'Paid')
            ->whereBetween('billing_month', [$fromDate, $toDate])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $monthlyCollection = collect(range(9, 0))->map(function (int $offset) use ($now, $monthTotals) {
            $month = $now->copy()->subMonths($offset)->startOfMonth();
            $key = $month->format('Y-m');

            return [
                'label' => $month->format('M'),
                'month' => $month->format('F Y'),
                'total' => (float) ($monthTotals[$key] ?? 0),
                'is_current' => $month->isSameMonth($now),
            ];
        });

        $maxMonthlyTotal = max(1, (int) ceil($monthlyCollection->max('total')));
        $monthlyCollection = $monthlyCollection->map(function (array $item) use ($maxMonthlyTotal) {
            $height = (int) round(($item['total'] / $maxMonthlyTotal) * 100);
            $item['height'] = max(18, $height);

            return $item;
        });

        $recentAdmissions = Student::latest()->take(6)->get();

        return view('dashboard', [
            'todayLabel' => $now->format('l, d F Y'),
            'totalStudents' => $totalStudents,
            'feeCollected' => $feeCollected,
            'pendingFees' => $pendingFees,
            'activeStaff' => $activeStaff,
            'newHires' => $newHires,
            'monthlyCollection' => $monthlyCollection,
            'recentAdmissions' => $recentAdmissions,
        ]);
    }
}

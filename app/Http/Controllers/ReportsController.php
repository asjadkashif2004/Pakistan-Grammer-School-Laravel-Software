<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FeeCollection;
use App\Models\PayrollTransaction;
use App\Models\SalesInvoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportsController extends Controller
{
    private const REPORT_KEYS = [
        'invoices',
        'fees',
        'admissions',
        'salaries',
        'expenses',
        'income',
        'profit-loss',
    ];

    public function index(Request $request): View
    {
        $payload = $this->buildReportsPayload($request);

        return view('reports.index', $payload);
    }

    public function print(Request $request, string $report): View
    {
        abort_unless(in_array($report, self::REPORT_KEYS, true), 404);

        $payload = $this->buildReportsPayload($request);
        $payload['reportKey'] = $report;
        $payload['reportTitle'] = $this->reportTitle($report);

        return view('reports.print', $payload);
    }

    private function buildReportsPayload(Request $request): array
    {
        [$fromDate, $toDate, $month, $year, $periodLabel] = $this->resolvePeriod($request);

        $invoiceBase = SalesInvoice::query()
            ->whereBetween('invoice_date', [$fromDate, $toDate]);
        $invoiceTotal = (float) (clone $invoiceBase)->sum('total_amount');
        $invoiceCount = (int) (clone $invoiceBase)->count();
        $monthlyInvoices = (clone $invoiceBase)
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as ym, COUNT(*) as invoices_count, SUM(total_amount) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        $recentInvoices = (clone $invoiceBase)->latest('invoice_date')->limit(10)->get();

        $feePaidBase = FeeCollection::query()
            ->with('student')
            ->where('status', 'Paid')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('paid_at', [$fromDate, $toDate])
                    ->orWhere(function ($subQuery) use ($fromDate, $toDate) {
                        $subQuery->whereNull('paid_at')
                            ->whereBetween('billing_month', [$fromDate, $toDate]);
                    });
            });
        $feeTotal = (float) (clone $feePaidBase)->sum('amount');
        $feeCount = (int) (clone $feePaidBase)->count();
        $monthlyFees = FeeCollection::query()
            ->where('status', 'Paid')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('paid_at', [$fromDate, $toDate])
                    ->orWhere(function ($subQuery) use ($fromDate, $toDate) {
                        $subQuery->whereNull('paid_at')
                            ->whereBetween('billing_month', [$fromDate, $toDate]);
                    });
            })
            ->selectRaw("DATE_FORMAT(COALESCE(paid_at, billing_month), '%Y-%m') as ym, COUNT(*) as vouchers_count, SUM(amount) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        $recentFees = (clone $feePaidBase)
            ->orderByDesc(DB::raw('COALESCE(paid_at, billing_month)'))
            ->limit(10)
            ->get();
        $pendingFeeAmount = (float) FeeCollection::query()
            ->whereIn('status', ['Pending', 'Overdue'])
            ->whereBetween('billing_month', [$fromDate, $toDate])
            ->sum('amount');

        $admissionBase = Student::query()
            ->whereBetween('admission_date', [$fromDate, $toDate]);
        $admissionsCount = (int) (clone $admissionBase)->count();
        $admissionIncome = (float) (clone $admissionBase)->sum('admission_fee');
        $admissionsByClass = (clone $admissionBase)
            ->selectRaw('class_name, COUNT(*) as students_count, SUM(admission_fee) as admission_total')
            ->groupBy('class_name')
            ->orderBy('class_name')
            ->get();
        $recentAdmissions = (clone $admissionBase)->latest('admission_date')->limit(10)->get();

        $salaryPaidBase = PayrollTransaction::query()
            ->with('staffMember')
            ->whereIn('transaction_type', ['wage', 'advance', 'extra_hours'])
            ->where(function ($query) {
                $query->where('status', 'Paid')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNull('status')
                            ->whereNotNull('paid_at');
                    });
            })
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('paid_at', [$fromDate, $toDate])
                    ->orWhere(function ($subQuery) use ($fromDate, $toDate) {
                        $subQuery->whereNull('paid_at')
                            ->whereBetween('transaction_month', [$fromDate, $toDate]);
                    });
            });
        $salaryTotal = (float) (clone $salaryPaidBase)->sum('amount');
        $salaryByType = (clone $salaryPaidBase)
            ->selectRaw('transaction_type, COUNT(*) as records_count, SUM(amount) as total')
            ->groupBy('transaction_type')
            ->get();
        $recentSalaryPayments = (clone $salaryPaidBase)
            ->orderByDesc(DB::raw('COALESCE(paid_at, transaction_month)'))
            ->limit(12)
            ->get();
        $pendingWages = PayrollTransaction::query()
            ->where('transaction_type', 'wage')
            ->where('status', 'Unpaid')
            ->whereBetween('transaction_month', [$fromDate, $toDate]);
        $pendingWagesCount = (int) (clone $pendingWages)->count();
        $pendingWagesAmount = (float) (clone $pendingWages)->sum('amount');

        $expenseBase = Expense::query()
            ->whereBetween('expense_date', [$fromDate, $toDate]);
        $expenseTotal = (float) (clone $expenseBase)->sum('amount');
        $expenseCount = (int) (clone $expenseBase)->count();
        $expensesByCategory = (clone $expenseBase)
            ->selectRaw('category, COUNT(*) as expenses_count, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('category')
            ->get();
        $recentExpenses = (clone $expenseBase)->latest('expense_date')->limit(10)->get();

        $otherIncome = $admissionIncome;
        $totalIncome = $invoiceTotal + $feeTotal + $otherIncome;
        $totalExpenses = $salaryTotal + $expenseTotal;
        $netProfitLoss = $totalIncome - $totalExpenses;

        $invoiceByMonth = $this->asMonthMap(
            SalesInvoice::query()
                ->whereBetween('invoice_date', [$fromDate, $toDate])
                ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as ym, SUM(total_amount) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym')
        );
        $feeByMonth = $this->asMonthMap(
            FeeCollection::query()
                ->where('status', 'Paid')
                ->where(function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('paid_at', [$fromDate, $toDate])
                        ->orWhere(function ($subQuery) use ($fromDate, $toDate) {
                            $subQuery->whereNull('paid_at')
                                ->whereBetween('billing_month', [$fromDate, $toDate]);
                        });
                })
                ->selectRaw("DATE_FORMAT(COALESCE(paid_at, billing_month), '%Y-%m') as ym, SUM(amount) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym')
        );
        $admissionByMonth = $this->asMonthMap(
            Student::query()
                ->whereBetween('admission_date', [$fromDate, $toDate])
                ->selectRaw("DATE_FORMAT(admission_date, '%Y-%m') as ym, SUM(admission_fee) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym')
        );
        $salaryByMonth = $this->asMonthMap(
            PayrollTransaction::query()
                ->whereIn('transaction_type', ['wage', 'advance', 'extra_hours'])
                ->where(function ($query) {
                    $query->where('status', 'Paid')
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereNull('status')
                                ->whereNotNull('paid_at');
                        });
                })
                ->where(function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('paid_at', [$fromDate, $toDate])
                        ->orWhere(function ($subQuery) use ($fromDate, $toDate) {
                            $subQuery->whereNull('paid_at')
                                ->whereBetween('transaction_month', [$fromDate, $toDate]);
                        });
                })
                ->selectRaw("DATE_FORMAT(COALESCE(paid_at, transaction_month), '%Y-%m') as ym, SUM(amount) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym')
        );
        $expenseByMonth = $this->asMonthMap(
            Expense::query()
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")
                ->groupBy('ym')
                ->pluck('total', 'ym')
        );

        $monthlyPnL = collect();
        for ($cursor = $fromDate->copy()->startOfMonth(); $cursor->lte($toDate); $cursor->addMonth()) {
            $key = $cursor->format('Y-m');
            $income = (float) ($invoiceByMonth[$key] ?? 0) + (float) ($feeByMonth[$key] ?? 0) + (float) ($admissionByMonth[$key] ?? 0);
            $expenses = (float) ($salaryByMonth[$key] ?? 0) + (float) ($expenseByMonth[$key] ?? 0);
            $monthlyPnL->push([
                'month' => $cursor->format('M Y'),
                'income' => $income,
                'expenses' => $expenses,
                'net' => $income - $expenses,
            ]);
        }

        return [
            'todayLabel' => now()->format('l, d F Y'),
            'fromDate' => $fromDate->toDateString(),
            'toDate' => $toDate->toDateString(),
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'periodLabel' => $periodLabel,
            'reports' => [
                'invoices' => [
                    'title' => 'Monthly Invoice Report',
                    'icon' => '🧾',
                    'total' => $invoiceTotal,
                    'count' => $invoiceCount,
                    'monthly' => $monthlyInvoices,
                    'rows' => $recentInvoices,
                ],
                'fees' => [
                    'title' => 'Monthly Fee Collection Report',
                    'icon' => '🎓',
                    'total' => $feeTotal,
                    'count' => $feeCount,
                    'pending_amount' => $pendingFeeAmount,
                    'monthly' => $monthlyFees,
                    'rows' => $recentFees,
                ],
                'admissions' => [
                    'title' => 'Admission Report',
                    'icon' => '🧑‍🎓',
                    'count' => $admissionsCount,
                    'admission_income' => $admissionIncome,
                    'classes' => $admissionsByClass,
                    'rows' => $recentAdmissions,
                ],
                'salaries' => [
                    'title' => 'Salaries Report',
                    'icon' => '👨‍🏫',
                    'total_paid' => $salaryTotal,
                    'pending_count' => $pendingWagesCount,
                    'pending_amount' => $pendingWagesAmount,
                    'types' => $salaryByType,
                    'rows' => $recentSalaryPayments,
                ],
                'expenses' => [
                    'title' => 'Expense Report',
                    'icon' => '📉',
                    'total' => $expenseTotal,
                    'count' => $expenseCount,
                    'categories' => $expensesByCategory,
                    'rows' => $recentExpenses,
                ],
                'income' => [
                    'title' => 'Total Income Report',
                    'icon' => '💰',
                    'invoice_income' => $invoiceTotal,
                    'fee_income' => $feeTotal,
                    'other_income' => $otherIncome,
                    'other_income_label' => 'Admission Fees',
                    'total_income' => $totalIncome,
                ],
                'profit-loss' => [
                    'title' => 'Profit & Loss Report',
                    'icon' => '📊',
                    'total_income' => $totalIncome,
                    'salary_expense' => $salaryTotal,
                    'other_expense' => $expenseTotal,
                    'total_expenses' => $totalExpenses,
                    'net' => $netProfitLoss,
                    'monthly' => $monthlyPnL,
                ],
            ],
        ];
    }

    private function resolvePeriod(Request $request): array
    {
        $year = (int) $request->query('year', (int) now()->year);
        $month = (int) $request->query('month', (int) now()->month);
        $month = max(1, min(12, $month));
        $year = max(2000, min((int) now()->year + 5, $year));

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($dateFrom && $dateTo) {
            $fromDate = Carbon::parse($dateFrom)->startOfDay();
            $toDate = Carbon::parse($dateTo)->endOfDay();
            if ($fromDate->greaterThan($toDate)) {
                [$fromDate, $toDate] = [$toDate, $fromDate];
            }
            $periodLabel = $fromDate->format('d M Y').' - '.$toDate->format('d M Y');

            return [$fromDate, $toDate, $month, $year, $periodLabel];
        }

        $fromDate = Carbon::create($year, $month, 1)->startOfMonth();
        $toDate = $fromDate->copy()->endOfMonth();
        $periodLabel = $fromDate->format('F Y');

        return [$fromDate, $toDate, $month, $year, $periodLabel];
    }

    private function reportTitle(string $report): string
    {
        return [
            'invoices' => 'Monthly Invoice Report',
            'fees' => 'Monthly Fee Collection Report',
            'admissions' => 'Admission Report',
            'salaries' => 'Salaries Report',
            'expenses' => 'Expense Report',
            'income' => 'Total Income Report',
            'profit-loss' => 'Profit & Loss Report',
        ][$report];
    }

    private function asMonthMap(Collection $dataset): array
    {
        return $dataset->mapWithKeys(fn ($total, $key) => [(string) $key => (float) $total])->all();
    }
}

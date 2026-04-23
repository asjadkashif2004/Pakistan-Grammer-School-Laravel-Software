<?php

namespace App\Http\Controllers;

use App\Models\PayrollTransaction;
use App\Models\StaffMember;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffSalaryController extends Controller
{
    public function index(Request $request): View
    {
        $month = (string) $request->query('month', now()->format('Y-m'));
        $monthStart = $this->parseMonthStart($month);
        $search = trim((string) $request->query('q', ''));

        $workers = StaffMember::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $workerIds = $workers->getCollection()->pluck('id');
        $monthTransactions = PayrollTransaction::query()
            ->whereIn('staff_member_id', $workerIds)
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get()
            ->groupBy('staff_member_id');

        $workers->getCollection()->transform(function (StaffMember $worker) use ($monthTransactions) {
            $rows = $monthTransactions->get($worker->id, collect());
            $advances = $rows->where('transaction_type', 'advance')->values();
            $overtime = $rows->where('transaction_type', 'extra_hours')->values();
            $wage = $rows->where('transaction_type', 'wage')->sortByDesc('id')->first();

            $baseSalary = round((float) $worker->monthly_wage, 2);
            $advanceTotal = round((float) $advances->sum('amount'), 2);
            $overtimeTotal = round((float) $overtime->sum('amount'), 2);
            $dailySalary = round($baseSalary / 30, 4);

            $absentDays = (float) ($wage?->absent_days ?? 0);
            $absenceDeduction = $wage && $wage->absence_deduction !== null
                ? round((float) $wage->absence_deduction, 2)
                : round($absentDays * $dailySalary, 2);

            $finalPayable = $wage && $wage->status === 'Paid'
                ? round((float) $wage->amount, 2)
                : round(max(0, $baseSalary + $overtimeTotal - $absenceDeduction - $advanceTotal), 2);

            $worker->setAttribute('month_advances', $advances);
            $worker->setAttribute('month_overtime', $overtime);
            $worker->setAttribute('month_wage', $wage);
            $worker->setAttribute('advance_total', $advanceTotal);
            $worker->setAttribute('overtime_total', $overtimeTotal);
            $worker->setAttribute('absence_deduction', $absenceDeduction);
            $worker->setAttribute('absent_days', $absentDays);
            $worker->setAttribute('base_salary', $baseSalary);
            $worker->setAttribute('daily_salary', $dailySalary);
            $worker->setAttribute('final_payable', $finalPayable);
            $worker->setAttribute('is_locked', (bool) ($wage && $wage->status === 'Paid'));
            $worker->setAttribute('remaining_advance_limit', round(max(0, $baseSalary - $advanceTotal), 2));

            return $worker;
        });

        $summary = [
            'active_workers' => (int) StaffMember::query()->where('is_active', true)->count(),
            'monthly_wage_bill' => (float) StaffMember::query()->where('is_active', true)->sum('monthly_wage'),
            'advances_month' => (float) PayrollTransaction::query()
                ->where('transaction_type', 'advance')
                ->whereDate('transaction_month', $monthStart->toDateString())
                ->sum('amount'),
            'wages_paid' => (float) PayrollTransaction::query()
                ->where('transaction_type', 'wage')
                ->where('status', 'Paid')
                ->whereDate('transaction_month', $monthStart->toDateString())
                ->sum('amount'),
        ];

        return view('staff-salaries.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'month' => $monthStart->format('Y-m'),
            'search' => $search,
            'workers' => $workers,
            'summary' => $summary,
        ]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'phone' => ['required', 'regex:/^03[0-9]{9}$/'],
            'role' => ['required', 'string', 'max:100'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'overtime_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $phone = $this->normalizePakPhone($validated['phone']);

        $worker = DB::transaction(function () use ($validated, $phone) {
            $member = StaffMember::create([
                'name' => $validated['name'],
                'phone' => $phone,
                'contact_number' => $phone,
                'role' => $validated['role'],
                'designation' => $validated['role'],
                'monthly_wage' => $validated['monthly_salary'],
                'overtime_rate' => $validated['overtime_rate'],
                'is_active' => $validated['status'] === 'active',
                'joining_date' => now()->toDateString(),
                'hired_at' => now()->toDateString(),
                'payment_method' => 'bank',
                'cnic' => null,
            ]);

            $member->update([
                'employee_code' => sprintf('WK-%05d', $member->id),
            ]);

            return $member;
        });

        ActivityLogger::log(
            'worker.created',
            "Worker {$worker->name} ({$worker->employee_code}) created.",
            'staff_member',
            $worker->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $request->input('month', now()->format('Y-m'))])
            ->with('status', 'Worker added successfully.');
    }

    public function editEmployee(StaffMember $staffMember): View
    {
        return view('staff-salaries.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'staffMember' => $staffMember,
        ]);
    }

    public function updateEmployee(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'phone' => ['required', 'regex:/^03[0-9]{9}$/'],
            'role' => ['required', 'string', 'max:100'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'overtime_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);
        $phone = $this->normalizePakPhone($validated['phone']);

        $staffMember->update([
            'name' => $validated['name'],
            'phone' => $phone,
            'contact_number' => $phone,
            'role' => $validated['role'],
            'designation' => $validated['role'],
            'monthly_wage' => $validated['monthly_salary'],
            'overtime_rate' => $validated['overtime_rate'],
            'is_active' => $validated['status'] === 'active',
        ]);

        ActivityLogger::log(
            'worker.updated',
            "Worker {$staffMember->name} ({$staffMember->employee_code}) updated.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month'] ?? now()->format('Y-m')])
            ->with('status', 'Worker updated successfully.');
    }

    public function destroyEmployee(Request $request, StaffMember $staffMember): RedirectResponse
    {
        if ($staffMember->payrollTransactions()->exists()) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $request->input('month', now()->format('Y-m'))])
                ->with('status', 'Cannot delete worker with payroll history. Set status to inactive instead.');
        }

        $name = $staffMember->name;
        $code = $staffMember->employee_code;
        $staffMember->delete();

        ActivityLogger::log(
            'worker.deleted',
            "Worker {$name} ({$code}) deleted.",
            'staff_member'
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $request->input('month', now()->format('Y-m'))])
            ->with('status', 'Worker deleted successfully.');
    }

    public function addAdvance(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $monthStart = $this->parseMonthStart($validated['month']);
        $lockReason = $this->monthMutationLockReason($staffMember, $monthStart);
        if ($lockReason !== null) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', $lockReason);
        }

        $advanceSoFar = (float) PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'advance')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->sum('amount');

        $salary = (float) $staffMember->monthly_wage;
        if ($advanceSoFar + (float) $validated['amount'] > $salary + 0.009) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', 'Advance exceeds monthly salary limit.');
        }

        PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'advance',
            'status' => 'Paid',
            'amount' => $validated['amount'],
            'transaction_month' => $monthStart,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => Carbon::parse($validated['date'])->endOfDay(),
        ]);

        ActivityLogger::log(
            'payroll.advance',
            "Advance of Rs ".number_format((float) $validated['amount'], 0)." added for {$staffMember->name}.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', 'Advance added successfully.');
    }

    public function addExtraHours(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'hours' => ['required', 'numeric', 'min:0.01'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $monthStart = $this->parseMonthStart($validated['month']);
        $lockReason = $this->monthMutationLockReason($staffMember, $monthStart);
        if ($lockReason !== null) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', $lockReason);
        }

        $rate = (float) ($validated['rate'] ?? $staffMember->overtime_rate ?? 0);
        if ($rate <= 0) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', 'Overtime rate must be greater than zero.');
        }

        $hours = (float) $validated['hours'];
        $amount = round($hours * $rate, 2);

        PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'extra_hours',
            'status' => 'Paid',
            'amount' => $amount,
            'hours' => $hours,
            'overtime_rate' => $rate,
            'transaction_month' => $monthStart,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => Carbon::parse($validated['date'])->endOfDay(),
        ]);

        ActivityLogger::log(
            'payroll.extra_hours',
            "{$hours} overtime hour(s) added for {$staffMember->name}.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', 'Overtime entry added successfully.');
    }

    public function payWage(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'absent_days' => ['required', 'numeric', 'min:0', 'max:30'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $monthStart = $this->parseMonthStart($validated['month']);
        $lockReason = $this->monthMutationLockReason($staffMember, $monthStart);
        if ($lockReason !== null) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', $lockReason);
        }

        $baseSalary = round((float) $staffMember->monthly_wage, 2);
        $advanceTotal = round((float) PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'advance')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->sum('amount'), 2);
        $overtimeTotal = round((float) PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'extra_hours')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->sum('amount'), 2);

        $absentDays = round((float) $validated['absent_days'], 2);
        $dailySalary = round($baseSalary / 30, 4);
        $absenceDeduction = round($absentDays * $dailySalary, 2);
        $finalSalary = round($baseSalary + $overtimeTotal - $absenceDeduction - $advanceTotal, 2);

        if ($finalSalary < -0.009) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', 'Final salary cannot be negative. Adjust advances/overtime/absent days.');
        }
        $finalSalary = max(0, $finalSalary);

        $wageRecord = PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'wage')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->first();

        if ($wageRecord && $wageRecord->status === 'Paid') {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', 'Salary already paid for this worker in this month.');
        }

        $payload = [
            'status' => 'Paid',
            'amount' => $finalSalary,
            'base_salary' => $baseSalary,
            'total_advance' => $advanceTotal,
            'total_overtime' => $overtimeTotal,
            'absence_deduction' => $absenceDeduction,
            'absent_days' => $absentDays,
            'transaction_month' => $monthStart,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => Carbon::parse($validated['date'])->endOfDay(),
            'payment_method' => 'cash',
        ];

        if ($wageRecord) {
            $wageRecord->update($payload);
        } else {
            PayrollTransaction::create([
                'staff_member_id' => $staffMember->id,
                'transaction_type' => 'wage',
                ...$payload,
            ]);
        }

        ActivityLogger::log(
            'payroll.wage_paid',
            "Salary paid for {$staffMember->name} ({$validated['month']}).",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', 'Salary paid successfully.');
    }

    public function undoPayment(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $monthStart = $this->parseMonthStart($validated['month']);
        $wageRecord = PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'wage')
            ->where('status', 'Paid')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->first();

        if (! $wageRecord) {
            return redirect()
                ->route('staff-salaries.index', ['month' => $validated['month']])
                ->with('status', 'No paid salary found for this worker in selected month.');
        }

        $wageRecord->update([
            'status' => 'Unpaid',
            'paid_at' => null,
        ]);

        ActivityLogger::log(
            'payroll.undo_payment',
            "Salary payment undone for {$staffMember->name} ({$validated['month']}).",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', 'Salary payment undone. Month entries unlocked.');
    }

    private function parseMonthStart(string $month): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    private function monthMutationLockReason(StaffMember $staffMember, Carbon $monthStart): ?string
    {
        if (! $staffMember->is_active) {
            return 'Only active workers are eligible for payroll operations.';
        }

        $paidWageExists = PayrollTransaction::query()
            ->where('staff_member_id', $staffMember->id)
            ->where('transaction_type', 'wage')
            ->where('status', 'Paid')
            ->whereDate('transaction_month', $monthStart->toDateString())
            ->exists();

        if ($paidWageExists) {
            return 'This month is locked because salary is already paid.';
        }

        return null;
    }

    private function normalizePakPhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? $phone;
    }
}


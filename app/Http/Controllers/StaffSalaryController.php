<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayPayrollRecordRequest;
use App\Http\Requests\StorePayrollRecordRequest;
use App\Http\Requests\StoreStaffMemberRequest;
use App\Http\Requests\UpdatePayrollRecordRequest;
use App\Http\Requests\UpdateStaffMemberRequest;
use App\Models\PayrollTransaction;
use App\Models\StaffMember;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
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
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('q', ''));
        $statusFilter = $request->query('status');

        $staff = StaffMember::query()
            ->withSum(['payrollTransactions as advances_month' => function ($query) use ($monthStart) {
                $query->where('transaction_type', 'advance')
                    ->whereDate('transaction_month', $monthStart);
            }], 'amount')
            ->withSum(['payrollTransactions as wages_paid_month' => function ($query) use ($monthStart) {
                $query->where('transaction_type', 'wage')
                    ->whereDate('transaction_month', $monthStart)
                    ->where('status', 'Paid');
            }], 'amount')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'staff_page')
            ->withQueryString();

        $payrollBase = PayrollTransaction::query()
            ->with('staffMember')
            ->where('transaction_type', 'wage')
            ->whereDate('transaction_month', $monthStart)
            ->when($dateFrom, fn ($query) => $query->whereDate('transaction_month', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('transaction_month', '<=', $dateTo))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('staffMember', function ($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            });

        $unpaidRecords = (clone $payrollBase)
            ->when($statusFilter && $statusFilter !== 'Unpaid', fn ($query) => $query->whereRaw('1=0'))
            ->where('status', 'Unpaid')
            ->latest('created_at')
            ->paginate(10, ['*'], 'unpaid_page')
            ->withQueryString();

        $paidRecords = (clone $payrollBase)
            ->when($statusFilter && $statusFilter !== 'Paid', fn ($query) => $query->whereRaw('1=0'))
            ->where('status', 'Paid')
            ->latest('paid_at')
            ->paginate(10, ['*'], 'paid_page')
            ->withQueryString();

        $activeWorkers = StaffMember::where('is_active', true)->count();
        $monthlyWageBill = StaffMember::where('is_active', true)->sum('monthly_wage');
        $advancesMonth = (float) PayrollTransaction::query()
            ->where('transaction_type', 'advance')
            ->whereDate('transaction_month', $monthStart)
            ->sum('amount');
        $wagesPaid = (float) PayrollTransaction::query()
            ->where('transaction_type', 'wage')
            ->whereDate('transaction_month', $monthStart)
            ->where('status', 'Paid')
            ->sum('amount');

        $staffOptions = StaffMember::where('is_active', true)->orderBy('name')->get();

        return view('staff-salaries.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'month' => $month,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'staff' => $staff,
            'staffOptions' => $staffOptions,
            'unpaidRecords' => $unpaidRecords,
            'paidRecords' => $paidRecords,
            'activeWorkers' => $activeWorkers,
            'monthlyWageBill' => (float) $monthlyWageBill,
            'advancesMonth' => $advancesMonth,
            'wagesPaid' => $wagesPaid,
        ]);
    }

    public function storeEmployee(StoreStaffMemberRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $prepared = $this->prepareStaffPaymentDetails($validated);

        $staffMember = DB::transaction(function () use ($prepared) {
            $member = StaffMember::create([
                'cnic' => $prepared['cnic'],
                'name' => $prepared['name'],
                'phone' => $prepared['contact_number'],
                'contact_number' => $prepared['contact_number'],
                'role' => null,
                'designation' => $prepared['designation'] ?? null,
                'monthly_wage' => $prepared['monthly_wage'],
                'joining_date' => $prepared['joining_date'],
                'hired_at' => $prepared['joining_date'],
                'is_active' => (bool) ($prepared['is_active'] ?? true),
                'payment_method' => $prepared['payment_method'],
                'bank_name' => $prepared['bank_name'] ?? null,
                'branch_code' => $prepared['branch_code'] ?? null,
                'iban' => $prepared['iban'] ?? null,
                'account_number' => $prepared['account_number'] ?? null,
                'online_wallet_type' => $prepared['online_wallet_type'] ?? null,
                'online_wallet_number' => $prepared['online_wallet_number'] ?? null,
            ]);

            $member->update([
                'employee_code' => sprintf('EMP-%05d', $member->id),
            ]);

            return $member;
        });

        ActivityLogger::log(
            'employee.created',
            "Employee {$staffMember->name} ({$staffMember->employee_code}) added.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index')
            ->with('status', "Employee {$staffMember->employee_code} created successfully.");
    }

    public function editEmployee(StaffMember $staffMember): View
    {
        return view('staff-salaries.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'staffMember' => $staffMember,
        ]);
    }

    public function updateEmployee(UpdateStaffMemberRequest $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validated();
        $prepared = $this->prepareStaffPaymentDetails($validated);

        $staffMember->update([
            'cnic' => $prepared['cnic'],
            'name' => $prepared['name'],
            'phone' => $prepared['contact_number'],
            'contact_number' => $prepared['contact_number'],
            'role' => null,
            'designation' => $prepared['designation'] ?? null,
            'monthly_wage' => $prepared['monthly_wage'],
            'joining_date' => $prepared['joining_date'],
            'hired_at' => $prepared['joining_date'],
            'is_active' => (bool) ($prepared['is_active'] ?? true),
            'payment_method' => $prepared['payment_method'],
            'bank_name' => $prepared['bank_name'] ?? null,
            'branch_code' => $prepared['branch_code'] ?? null,
            'iban' => $prepared['iban'] ?? null,
            'account_number' => $prepared['account_number'] ?? null,
            'online_wallet_type' => $prepared['online_wallet_type'] ?? null,
            'online_wallet_number' => $prepared['online_wallet_number'] ?? null,
        ]);

        ActivityLogger::log(
            'employee.updated',
            "Employee {$staffMember->name} ({$staffMember->employee_code}) updated.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index')
            ->with('status', "Employee {$staffMember->employee_code} updated successfully.");
    }

    public function destroyEmployee(StaffMember $staffMember): RedirectResponse
    {
        $code = $staffMember->employee_code;
        $name = $staffMember->name;
        $staffMember->delete();

        ActivityLogger::log(
            'employee.deleted',
            "Employee {$name} ({$code}) deleted.",
            'staff_member'
        );

        return redirect()
            ->route('staff-salaries.index')
            ->with('status', "Employee {$code} deleted.");
    }

    public function storePayroll(StorePayrollRecordRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $staffMember = StaffMember::findOrFail((int) $validated['staff_member_id']);
        $status = 'Unpaid';
        $paymentDetails = $this->preparePayrollPaymentDetails($validated, false);

        $record = PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'wage',
            'status' => $status,
            'amount' => (float) $validated['salary_amount'],
            'hours' => null,
            'transaction_month' => Carbon::createFromFormat('Y-m', $validated['payroll_month'])->startOfMonth(),
            ...$paymentDetails,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => $status === 'Paid'
                ? Carbon::parse($validated['payment_date'] ?? now())
                : null,
        ]);

        ActivityLogger::log(
            'payroll.record_created',
            "Payroll record {$record->id} created for {$staffMember->name} with status {$status}.",
            'payroll_transaction',
            $record->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['payroll_month']])
            ->with('status', "Payroll record created for {$staffMember->employee_code}.");
    }

    public function updatePayroll(UpdatePayrollRecordRequest $request, PayrollTransaction $payrollTransaction): RedirectResponse
    {
        if ($payrollTransaction->transaction_type !== 'wage') {
            return redirect()
                ->route('staff-salaries.index')
                ->with('status', 'Only wage payroll records can be updated.');
        }

        $validated = $request->validated();
        $status = $validated['status'];
        $paymentDetails = $this->preparePayrollPaymentDetails($validated, $status === 'Paid');

        $payrollTransaction->update([
            'amount' => (float) $validated['salary_amount'],
            'transaction_month' => Carbon::createFromFormat('Y-m', $validated['payroll_month'])->startOfMonth(),
            'status' => $status,
            ...$paymentDetails,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => $status === 'Paid'
                ? Carbon::parse($validated['payment_date'] ?? now())
                : null,
        ]);

        ActivityLogger::log(
            'payroll.record_updated',
            "Payroll record {$payrollTransaction->id} updated.",
            'payroll_transaction',
            $payrollTransaction->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => Carbon::parse($payrollTransaction->transaction_month)->format('Y-m')])
            ->with('status', 'Payroll record updated successfully.');
    }

    public function destroyPayroll(PayrollTransaction $payrollTransaction): RedirectResponse
    {
        if ($payrollTransaction->transaction_type !== 'wage') {
            return redirect()
                ->route('staff-salaries.index')
                ->with('status', 'Only wage payroll records can be deleted.');
        }

        $id = $payrollTransaction->id;
        $payrollTransaction->delete();

        ActivityLogger::log(
            'payroll.record_deleted',
            "Payroll record {$id} deleted.",
            'payroll_transaction'
        );

        return redirect()
            ->route('staff-salaries.index')
            ->with('status', 'Payroll record deleted successfully.');
    }

    public function payPayroll(PayPayrollRecordRequest $request, PayrollTransaction $payrollTransaction): RedirectResponse|JsonResponse
    {
        if ($payrollTransaction->transaction_type !== 'wage') {
            return response()->json(['message' => 'Only wage payroll records can be paid.'], 422);
        }

        $validated = $request->validated();
        $paymentDetails = $this->preparePayrollPaymentDetails($validated, true);
        $paidAt = Carbon::parse($validated['payment_date'] ?? now());

        $payrollTransaction->update([
            'status' => 'Paid',
            ...$paymentDetails,
            'notes' => $validated['notes'] ?? $payrollTransaction->notes,
            'paid_at' => $paidAt,
        ]);
        $payrollTransaction->load('staffMember');

        ActivityLogger::log(
            'payroll.wage_paid',
            "Payroll record {$payrollTransaction->id} marked paid for {$payrollTransaction->staffMember?->name}.",
            'payroll_transaction',
            $payrollTransaction->id
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Payroll marked as paid.',
                'record' => [
                    'id' => $payrollTransaction->id,
                    'employee_code' => $payrollTransaction->staffMember?->employee_code,
                    'name' => $payrollTransaction->staffMember?->name,
                    'month' => optional($payrollTransaction->transaction_month)->format('M Y'),
                    'amount' => (float) $payrollTransaction->amount,
                    'payment_method' => strtoupper((string) $payrollTransaction->payment_method),
                    'paid_at' => optional($payrollTransaction->paid_at)->format('d M Y'),
                ],
            ]);
        }

        return redirect()
            ->route('staff-salaries.index')
            ->with('status', 'Payroll marked as paid.');
    }

    public function addAdvance(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'month' => ['required', 'date_format:Y-m'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'advance',
            'status' => 'Paid',
            'amount' => $validated['amount'],
            'transaction_month' => Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth(),
            'payment_method' => 'bank',
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        ActivityLogger::log(
            'payroll.advance',
            "Advance of Rs ".number_format((float) $validated['amount'], 0)." added for {$staffMember->name}.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', "Advance recorded for {$staffMember->employee_code}.");
    }

    public function addExtraHours(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'hours' => ['nullable', 'numeric', 'min:0'],
            'month' => ['required', 'date_format:Y-m'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'extra_hours',
            'status' => 'Paid',
            'amount' => $validated['amount'],
            'hours' => $validated['hours'] ?? null,
            'transaction_month' => Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth(),
            'payment_method' => 'bank',
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        ActivityLogger::log(
            'payroll.extra_hours',
            "Extra hours payment of Rs ".number_format((float) $validated['amount'], 0)." added for {$staffMember->name}.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', "Extra hours recorded for {$staffMember->employee_code}.");
    }

    public function payWage(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'month' => ['required', 'date_format:Y-m'],
            'payment_method' => ['required', 'in:bank,wallet'],
            'wallet_type' => ['nullable', 'in:easypaisa,jazzcash'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'branch_code' => ['nullable', 'regex:/^\d{3,10}$/'],
            'iban' => ['nullable', 'regex:/^PK[A-Z0-9]{22}$/'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $paymentDetails = $this->preparePayrollPaymentDetails($validated, true);

        PayrollTransaction::create([
            'staff_member_id' => $staffMember->id,
            'transaction_type' => 'wage',
            'status' => 'Paid',
            'amount' => (float) $validated['amount'],
            'transaction_month' => Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth(),
            ...$paymentDetails,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        ActivityLogger::log(
            'payroll.wage_paid',
            "Wage payment of Rs ".number_format((float) $validated['amount'], 0)." paid to {$staffMember->name}.",
            'staff_member',
            $staffMember->id
        );

        return redirect()
            ->route('staff-salaries.index', ['month' => $validated['month']])
            ->with('status', "Wage paid for {$staffMember->employee_code}.");
    }

    private function prepareStaffPaymentDetails(array $validated): array
    {
        if ($validated['payment_method'] === 'bank') {
            $validated['online_wallet_type'] = null;
            $validated['online_wallet_number'] = null;
        } else {
            $validated['bank_name'] = null;
            $validated['branch_code'] = null;
            $validated['iban'] = null;
            $validated['account_number'] = null;
        }

        return $validated;
    }

    private function preparePayrollPaymentDetails(array $validated, bool $isPaid): array
    {
        if (! $isPaid) {
            return [
                'payment_method' => null,
                'bank_name' => null,
                'branch_code' => null,
                'iban' => null,
                'account_number' => null,
            ];
        }

        if (($validated['payment_method'] ?? null) === 'wallet') {
            $method = $validated['wallet_type'] ?? 'easypaisa';

            return [
                'payment_method' => $method,
                'bank_name' => null,
                'branch_code' => null,
                'iban' => null,
                'account_number' => $validated['account_number'] ?? null,
            ];
        }

        return [
            'payment_method' => 'bank',
            'bank_name' => $validated['bank_name'] ?? null,
            'branch_code' => $validated['branch_code'] ?? null,
            'iban' => $validated['iban'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
        ];
    }
}


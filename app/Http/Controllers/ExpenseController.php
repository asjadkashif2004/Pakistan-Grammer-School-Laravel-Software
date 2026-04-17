<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->query('month', now()->format('Y-m'));
        $monthStart = now()->createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $expenses = Expense::query()
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->latest('expense_date')
            ->paginate(12)
            ->withQueryString();

        $summary = Expense::query()
            ->selectRaw('category, SUM(amount) as total')
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('expenses.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'month' => $month,
            'expenses' => $expenses,
            'summaryTotal' => (float) $summary->sum(),
            'summaryUtilities' => (float) ($summary['Utilities'] ?? 0),
            'summaryMaintenance' => (float) ($summary['Maintenance'] ?? 0),
            'summaryMisc' => (float) (($summary['Stationery'] ?? 0) + ($summary['Miscellaneous'] ?? 0)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateExpense($request);
        $expense = Expense::create($validated);

        ActivityLogger::log(
            'expense.created',
            "Expense '{$expense->title}' of Rs ".number_format((float) $expense->amount, 0)." recorded.",
            'expense',
            $expense->id
        );

        return redirect()
            ->route('expenses.index', ['month' => $expense->expense_date->format('Y-m')])
            ->with('status', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense): View
    {
        return view('expenses.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'expense' => $expense,
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $this->validateExpense($request);
        $expense->update($validated);

        ActivityLogger::log(
            'expense.updated',
            "Expense '{$expense->title}' updated.",
            'expense',
            $expense->id
        );

        return redirect()
            ->route('expenses.index', ['month' => $expense->expense_date->format('Y-m')])
            ->with('status', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $title = $expense->title;
        $expense->delete();

        ActivityLogger::log(
            'expense.deleted',
            "Expense '{$title}' deleted.",
            'expense'
        );

        return redirect()
            ->route('expenses.index')
            ->with('status', 'Expense deleted successfully.');
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'in:Utilities,Maintenance,Stationery,Miscellaneous'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:Cash,Bank,Cheque,Online'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);
    }
}

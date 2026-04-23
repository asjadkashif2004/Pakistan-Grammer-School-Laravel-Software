@extends('layouts.school')

@section('title', 'Expenses & Cash Flow | Pakistan Grammar School')
@section('page_heading', 'Expenses & Cash Flow')

@push('styles')
    <style>
        .cashflow-wrap { display: grid; gap: 12px; }

        .cashflow-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cashflow-title h2 {
            margin: 0;
            font-size: 24px;
            line-height: 1.12;
            color: #172337;
            font-weight: 900;
        }

        .cashflow-title p {
            margin: 4px 0 0;
            color: #6f7f98;
            font-size: 14px;
            font-weight: 600;
        }

        .head-actions { display: inline-flex; gap: 8px; align-items: center; flex-wrap: wrap; }

        .cf-btn {
            border: 1px solid #dbe6ea;
            border-radius: 10px;
            padding: 8px 12px;
            background: #fff;
            color: #344255;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            transition: all .18s ease;
        }

        .cf-btn svg { width: 15px; height: 15px; display: block; }
        .cf-btn.success { background: #0f7a35; border-color: #0f7a35; color: #fff; }
        .cf-btn.danger { background: #ef1010; border-color: #ef1010; color: #fff; }
        .cf-btn:hover { border-color: #c7d4de; box-shadow: 0 2px 8px rgba(13, 35, 61, 0.08); }
        .cf-btn.success:hover { border-color: #0d6e30; background: #0d6e30; }
        .cf-btn.danger:hover { border-color: #e20d0d; background: #e20d0d; }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-card {
            border: 1px solid #e2e8ee;
            border-radius: 12px;
            background: #fff;
            padding: 12px;
            position: relative;
            min-height: 102px;
            box-shadow: 0 3px 10px rgba(17, 30, 52, 0.04);
        }

        .summary-card .k {
            color: #5f7088;
            font-size: 13px;
            font-weight: 700;
        }

        .summary-card .v {
            margin-top: 8px;
            font-size: 22px;
            font-weight: 900;
            color: #182338;
            line-height: 1;
        }

        .summary-card .s {
            margin-top: 5px;
            color: #96a3b6;
            font-size: 11px;
            font-weight: 600;
        }

        .summary-card .ico {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .summary-card .ico svg { width: 15px; height: 15px; }
        .summary-card .ico.green { background: #e7f9ee; color: #0f9b45; }
        .summary-card .ico.red { background: #ffecec; color: #e22e2e; }
        .summary-card .ico.pink { background: #ffeef4; color: #e3125b; }
        .summary-card.negative .v { color: #e3125b; }

        .flow-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .flow-card {
            border: 1px solid #e2e8ee;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(17, 30, 52, 0.04);
        }

        .flow-head {
            padding: 10px 12px;
            border-bottom: 1px solid #edf1f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            color: #1a273b;
            font-weight: 800;
            font-size: 15px;
        }

        .flow-head .meta { color: #98a5b8; font-size: 11px; font-weight: 700; }
        .flow-body { padding: 8px 10px; }

        .empty {
            min-height: 94px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca8b8;
            font-weight: 600;
            font-size: 14px;
        }

        .flow-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 9px 8px;
            border-bottom: 1px solid #eef2f4;
        }

        .flow-item:last-child { border-bottom: 0; }
        .flow-name { color: #1e2d41; font-size: 14px; font-weight: 700; line-height: 1.2; }
        .flow-amount { color: #df1010; font-size: 18px; font-weight: 900; line-height: 1; white-space: nowrap; }
        .flow-sub { margin-top: 4px; display: inline-flex; align-items: center; gap: 7px; color: #9ca8b8; font-size: 12px; font-weight: 600; }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .pill.blue { background: #e8f0ff; color: #2352a0; border-color: #c9dbff; }
        .pill.pink { background: #ffe8f4; color: #a53a73; border-color: #ffc7e3; }
        .pill.green { background: #e9f8ee; color: #1f7c47; border-color: #bfe9ce; }
        .pill.orange { background: #fff3de; color: #9f6116; border-color: #ffdba2; }

        .records-card {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(17, 30, 52, 0.04);
        }

        .records-head {
            padding: 10px 12px;
            border-bottom: 1px solid #e7f3e7;
            background: #f8fdf8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            font-weight: 800;
            color: #1f3f24;
        }

        .records-title {
            display: inline-flex;
            flex-direction: column;
            gap: 4px;
        }

        .records-metrics {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .records-metrics span {
            display: inline-flex;
            border: 1px solid #d8e8d9;
            background: #ffffff;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 11px;
            color: #4f6554;
            font-weight: 700;
        }

        .records-head form {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .records-head input {
            border: 1px solid #dbe8fb;
            border-radius: 9px;
            padding: 7px 9px;
            font-size: 12px;
        }

        .records-body { padding: 10px; }
        .table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2eee3; border-radius: 10px; }
        .table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .table th, .table td { text-align: left; padding: 8px 8px; border-top: 1px solid #e8f3e8; font-size: 12px; }
        .table th { border-top: 0; background: #f8fdf8; color: #56735a; font-size: 11px; letter-spacing: .06em; text-transform: uppercase; font-weight: 800; }
        .table tbody tr:hover { background: #fbfefc; }

        .actions { display: inline-flex; gap: 6px; align-items: center; }
        .actions form { margin: 0; }
        .actions .cf-btn { padding: 6px 9px; font-size: 11px; border-radius: 8px; }
        .category-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #e6f0ff;
            color: #2f4d8a;
        }

        dialog#expenseModal,
        dialog#editExpenseModal {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 0;
            width: min(620px, 96vw);
        }

        dialog#expenseModal::backdrop,
        dialog#editExpenseModal::backdrop { background: rgba(15, 40, 20, 0.35); }

        .modal-head {
            padding: 10px 12px;
            border-bottom: 1px solid #e7f3e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .modal-head h4 { margin: 0; font-size: 16px; color: #1f3f24; }
        .modal-body { padding: 12px; }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .field-grid .field.full { grid-column: span 2; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 10px; font-weight: 700; color: #1d4589; letter-spacing: 1px; text-transform: uppercase; }
        .field input, .field select, .field textarea { border: 1px solid #dbe8fb; border-radius: 9px; padding: 8px 9px; font-size: 13px; background: #fcfdff; width: 100%; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; flex-wrap: wrap; }

        .error-box {
            margin-bottom: 10px;
            border: 1px solid #ffd6d6;
            background: #fff6f6;
            color: #8e2d2d;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box ul { margin: 6px 0 0; padding-left: 16px; }

        @media (max-width: 980px) {
            .summary-grid { grid-template-columns: 1fr; }
            .flow-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .cashflow-title h2 { font-size: 26px; }
            .flow-name { font-size: 13px; }
            .flow-amount { font-size: 16px; }
            .field-grid { grid-template-columns: 1fr; }
            .field-grid .field.full { grid-column: auto; }
        }
    </style>
@endpush

@section('content')
    <div class="cashflow-wrap">
        <section class="cashflow-head">
            <div class="cashflow-title">
                <h2>Expenses &amp; Cash Flow</h2>
                <p>Track earnings and expenses for today</p>
            </div>
            <div class="head-actions">
                @php($selectedMonth = \Illuminate\Support\Carbon::createFromFormat('Y-m', $month))
                <a
                    class="cf-btn"
                    href="{{ route('reports.print', ['report' => 'expenses', 'year' => $selectedMonth->year, 'month' => $selectedMonth->month, 'auto_print' => 1]) }}"
                    target="_blank"
                    rel="noopener"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Generate Report
                </a>
                <button type="button" class="cf-btn success" id="openExpenseModal">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Add Expense
                </button>
            </div>
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <span class="ico green">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 17 10 12l3 3 6-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="k">Today's Earnings</div>
                <div class="v">PKR {{ number_format($todayEarnings, 0) }}</div>
                <div class="s">{{ number_format($todayInvoicesCount) }} invoices issued</div>
            </article>
            <article class="summary-card">
                <span class="ico red">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h10v18H7z" stroke="currentColor" stroke-width="1.8"/><path d="M10 8h4M10 12h4M10 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div class="k">Today's Expenses</div>
                <div class="v">PKR {{ number_format($todayExpensesTotal, 0) }}</div>
                <div class="s">{{ number_format($todayExpensesCount) }} expenses recorded</div>
            </article>
            <article class="summary-card {{ $netBalance < 0 ? 'negative' : '' }}">
                <span class="ico pink">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 16 5-5 4 3 7-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div class="k">Net Balance</div>
                <div class="v">{{ $netBalance < 0 ? '-' : '' }}PKR {{ number_format(abs($netBalance), 0) }}</div>
                <div class="s">{{ $netBalance < 0 ? 'Deficit for today' : 'Surplus for today' }}</div>
            </article>
        </section>

        <section class="flow-grid">
            <article class="flow-card">
                <div class="flow-head">
                    <span>Today's Earnings</span>
                    <span class="meta">{{ number_format($todayInvoicesCount) }} invoices</span>
                </div>
                <div class="flow-body">
                    @forelse ($todayInvoices as $invoice)
                        <div class="flow-item">
                            <div>
                                <div class="flow-name">{{ $invoice->customer_name ?: ($invoice->student?->full_name ?? 'Invoice') }}</div>
                                <div class="flow-sub">
                                    <span class="pill green">Invoice</span>
                                    {{ $invoice->created_at?->format('h:i a') }}
                                </div>
                            </div>
                            <div class="flow-amount" style="color:#0f7a35;">PKR {{ number_format((float) $invoice->total_amount, 0) }}</div>
                        </div>
                    @empty
                        <div class="empty">No invoices today</div>
                    @endforelse
                </div>
            </article>

            <article class="flow-card">
                <div class="flow-head">
                    <span>Today's Expenses</span>
                    <span class="meta">{{ number_format($todayExpensesCount) }} entries</span>
                </div>
                <div class="flow-body">
                    @forelse ($todayExpenses as $expense)
                        <div class="flow-item">
                            <div>
                                <div class="flow-name">{{ $expense->title }}</div>
                                <div class="flow-sub">
                                    <span class="pill {{ match($expense->category){'Utilities' => 'blue','Maintenance' => 'orange','Stationery' => 'pink', default => 'green'} }}">{{ $expense->category }}</span>
                                    {{ $expense->created_at?->format('h:i a') }}
                                </div>
                            </div>
                            <div class="flow-amount">PKR {{ number_format((float) $expense->amount, 0) }}</div>
                        </div>
                    @empty
                        <div class="empty">No expenses today</div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="records-card">
            <div class="records-head">
                <div class="records-title">
                    <span>Expense Records</span>
                    <div class="records-metrics">
                        <span>Month Total: PKR {{ number_format((float) $summaryTotal, 0) }}</span>
                        <span>Total Entries: {{ number_format($expenses->total()) }}</span>
                    </div>
                </div>
                <form method="GET">
                    <input type="month" name="month" value="{{ $month }}">
                    <button class="cf-btn" type="submit">Filter</button>
                </form>
            </div>
            <div class="records-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->title }}</td>
                                    <td><span class="category-pill">{{ $expense->category }}</span></td>
                                    <td>{{ optional($expense->expense_date)->format('d M') }}</td>
                                    <td>PKR {{ number_format((float) $expense->amount, 0) }}</td>
                                    <td>{{ $expense->payment_method }}</td>
                                    <td>
                                        <div class="actions">
                                            <button
                                                type="button"
                                                class="cf-btn"
                                                data-open-edit-expense
                                                data-expense-id="{{ $expense->id }}"
                                                data-update-url="{{ route('expenses.update', $expense) }}"
                                                data-title="{{ e($expense->title) }}"
                                                data-category="{{ e($expense->category) }}"
                                                data-amount="{{ number_format((float) $expense->amount, 2, '.', '') }}"
                                                data-expense-date="{{ optional($expense->expense_date)->toDateString() }}"
                                                data-payment-method="{{ e($expense->payment_method) }}"
                                                data-notes="{{ e((string) ($expense->notes ?? '')) }}"
                                            >Edit</button>
                                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete expense?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="cf-btn" style="border-color:#ffd3d3;color:#9f3131;background:#fff7f7;" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No expenses found for selected month.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="list-pagination">{{ $expenses->links() }}</div>
            </div>
        </section>

        <dialog id="expenseModal">
            <div class="modal-head">
                <h4>Add Expense</h4>
                <button class="cf-btn" type="button" id="closeExpenseModal">Close</button>
            </div>
            <div class="modal-body">
                @if ($errors->any() && old('_form_mode', 'add') === 'add')
                    <div class="error-box">
                        <strong>Please correct the fields below.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <input type="hidden" name="_form_mode" value="add">
                    <div class="field-grid">
                        <div class="field full">
                            <label>Expense Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" required>
                        </div>
                        <div class="field">
                            <label>Category</label>
                            <select name="category" required>
                                @foreach (['Utilities', 'Maintenance', 'Stationery', 'Miscellaneous'] as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
                        </div>
                        <div class="field">
                            <label>Date</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method" required>
                                @foreach (['Cash', 'Bank', 'Cheque', 'Online'] as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full">
                            <label>Notes / Remarks</label>
                            <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="cf-btn" type="button" id="cancelExpenseModal">Cancel</button>
                        <button class="cf-btn success" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="editExpenseModal">
            <div class="modal-head">
                <h4>Edit Expense</h4>
                <button class="cf-btn" type="button" id="closeEditExpenseModal">Close</button>
            </div>
            <div class="modal-body">
                @if ($errors->any() && old('_form_mode') === 'edit')
                    <div class="error-box">
                        <strong>Please correct the fields below.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('expenses.update', old('expense_id', 0)) }}" id="editExpenseForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_mode" value="edit">
                    <input type="hidden" name="expense_id" id="edit_expense_id" value="{{ old('expense_id') }}">
                    <div class="field-grid">
                        <div class="field full">
                            <label>Expense Title</label>
                            <input type="text" name="title" id="edit_title" value="{{ old('title') }}" required>
                        </div>
                        <div class="field">
                            <label>Category</label>
                            <select name="category" id="edit_category" required>
                                @foreach (['Utilities', 'Maintenance', 'Stationery', 'Miscellaneous'] as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="edit_amount" value="{{ old('amount') }}" required>
                        </div>
                        <div class="field">
                            <label>Date</label>
                            <input type="date" name="expense_date" id="edit_expense_date" value="{{ old('expense_date') }}" required>
                        </div>
                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method" id="edit_payment_method" required>
                                @foreach (['Cash', 'Bank', 'Cheque', 'Online'] as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full">
                            <label>Notes / Remarks</label>
                            <textarea name="notes" id="edit_notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="cf-btn" type="button" id="cancelEditExpenseModal">Cancel</button>
                        <button class="cf-btn danger" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5 10 17l9-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const addModal = document.getElementById('expenseModal');
    const editModal = document.getElementById('editExpenseModal');
    const openAddBtn = document.getElementById('openExpenseModal');
    const closeAddBtn = document.getElementById('closeExpenseModal');
    const cancelAddBtn = document.getElementById('cancelExpenseModal');
    const closeEditBtn = document.getElementById('closeEditExpenseModal');
    const cancelEditBtn = document.getElementById('cancelEditExpenseModal');
    const editForm = document.getElementById('editExpenseForm');

    const openDialog = (dialog) => {
        if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
    };
    const closeDialog = (dialog) => {
        if (dialog && typeof dialog.close === 'function') dialog.close();
    };

    openAddBtn?.addEventListener('click', () => openDialog(addModal));
    closeAddBtn?.addEventListener('click', () => closeDialog(addModal));
    cancelAddBtn?.addEventListener('click', () => closeDialog(addModal));
    closeEditBtn?.addEventListener('click', () => closeDialog(editModal));
    cancelEditBtn?.addEventListener('click', () => closeDialog(editModal));

    document.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-open-edit-expense]');
        if (!btn || !editForm) return;

        const setValue = (id, value) => {
            const field = document.getElementById(id);
            if (field) field.value = value || '';
        };

        editForm.setAttribute('action', btn.getAttribute('data-update-url') || editForm.getAttribute('action') || '');
        setValue('edit_expense_id', btn.getAttribute('data-expense-id'));
        setValue('edit_title', btn.getAttribute('data-title'));
        setValue('edit_category', btn.getAttribute('data-category'));
        setValue('edit_amount', btn.getAttribute('data-amount'));
        setValue('edit_expense_date', btn.getAttribute('data-expense-date'));
        setValue('edit_payment_method', btn.getAttribute('data-payment-method'));
        setValue('edit_notes', btn.getAttribute('data-notes'));
        openDialog(editModal);
    });

    @if ($errors->any() && old('_form_mode') === 'edit')
        openDialog(editModal);
    @elseif ($errors->any())
        openDialog(addModal);
    @endif
})();
</script>
@endpush

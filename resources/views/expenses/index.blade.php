@extends('layouts.school')

@section('title', 'Expenses Ledger | Pakistan Grammar School')
@section('page_heading', 'Expenses Ledger')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="#expense-form" class="action-chip primary" title="Record expense" aria-label="Record expense">➕ <span class="header-action-text">Expense</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .stat {
            background: #fff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 12px;
        }

        .stat small {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #5f7b63;
            font-weight: 700;
        }

        .stat h3 {
            margin: 4px 0 0;
            font-size: 36px;
            color: #19341e;
            line-height: 1;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
            gap: 12px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .head {
            padding: 12px 14px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 20px;
            color: #1f3f24;
            font-weight: 800;
        }

        .body {
            padding: 12px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .field input,
        .field select,
        .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 14px;
            background: #fcfdff;
            width: 100%;
        }

        .btn {
            border: 1px solid #d4ead4;
            border-radius: 9px;
            padding: 8px 10px;
            background: #ffffff;
            font-size: 13px;
            font-weight: 700;
            color: #355538;
            text-decoration: none;
            cursor: pointer;
        }

        .btn.primary {
            width: 100%;
            background: #0f7a35;
            color: #fff;
            border-color: #0f7a35;
        }

        .btn.danger {
            border-color: #ffd3d3;
            color: #9f3131;
            background: #fff7f7;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 10px 8px;
            border-top: 1px solid #e8f3e8;
            font-size: 14px;
        }

        .table th {
            color: #56735a;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
            border-top: none;
            padding-top: 0;
        }

        .category-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #e6f0ff;
            color: #2f4d8a;
        }

        .actions {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        @media (max-width: 1180px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <section class="stats">
        <article class="stat"><small>Total Expenses</small><h3>Rs {{ number_format($summaryTotal, 0) }}</h3></article>
        <article class="stat"><small>Utilities</small><h3>Rs {{ number_format($summaryUtilities, 0) }}</h3></article>
        <article class="stat"><small>Maintenance</small><h3>Rs {{ number_format($summaryMaintenance, 0) }}</h3></article>
        <article class="stat"><small>Miscellaneous</small><h3>Rs {{ number_format($summaryMisc, 0) }}</h3></article>
    </section>

    <section class="grid">
        <article class="panel" id="expense-form">
            <header class="head">Record Expense</header>
            <div class="body">
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="field"><label>Expense Title</label><input type="text" name="title" value="{{ old('title') }}" required></div>
                    <div class="field"><label>Category</label>
                        <select name="category" required>
                            @foreach (['Utilities', 'Maintenance', 'Stationery', 'Miscellaneous'] as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field"><label>Amount</label><input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required></div>
                    <div class="field"><label>Date</label><input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required></div>
                    <div class="field"><label>Payment Method</label>
                        <select name="payment_method" required>
                            @foreach (['Cash', 'Bank', 'Cheque', 'Online'] as $method)
                                <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field"><label>Notes / Remarks</label><textarea name="notes" rows="3">{{ old('notes') }}</textarea></div>
                    <button class="btn primary" type="submit">+ Record Expense</button>
                </form>
            </div>
        </article>

        <article class="panel">
            <header class="head">Expense Ledger</header>
            <div class="body">
                <form method="GET" style="display:flex; justify-content:end; gap:8px; margin-bottom:10px;">
                    <input type="month" name="month" value="{{ $month }}" style="border:1px solid #dbe8fb; border-radius:9px; padding:8px 10px;">
                    <button class="btn" type="submit">Filter</button>
                </form>
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
                                <td>Rs {{ number_format((float) $expense->amount, 0) }}</td>
                                <td>{{ $expense->payment_method }}</td>
                                <td>
                                    <div class="actions">
                                        <a class="btn" href="{{ route('expenses.edit', $expense) }}">Edit</a>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete expense?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No expenses found for selected month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="list-pagination">{{ $expenses->links() }}</div>
            </div>
        </article>
    </section>
@endsection

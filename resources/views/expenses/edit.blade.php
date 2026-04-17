@extends('layouts.school')

@section('title', 'Edit Expense | Pakistan Grammar School')
@section('page_heading', 'Edit Expense')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('expenses.index') }}" class="action-chip" title="Back to expenses" aria-label="Back to expenses">← <span class="header-action-text">Expenses</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            max-width: 760px;
            margin: 0 auto;
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
        }

        .btn {
            border: 0;
            border-radius: 9px;
            width: 100%;
            padding: 10px;
            background: #0f7a35;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <section class="card">
        <header class="head">Edit Expense</header>
        <div class="body">
            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @csrf
                @method('PUT')
                <div class="field"><label>Expense Title</label><input type="text" name="title" value="{{ old('title', $expense->title) }}" required></div>
                <div class="field"><label>Category</label>
                    <select name="category" required>
                        @foreach (['Utilities', 'Maintenance', 'Stationery', 'Miscellaneous'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $expense->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>Amount</label><input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', (float) $expense->amount) }}" required></div>
                <div class="field"><label>Date</label><input type="date" name="expense_date" value="{{ old('expense_date', optional($expense->expense_date)->toDateString()) }}" required></div>
                <div class="field"><label>Payment Method</label>
                    <select name="payment_method" required>
                        @foreach (['Cash', 'Bank', 'Cheque', 'Online'] as $method)
                            <option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method) === $method)>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="4">{{ old('notes', $expense->notes) }}</textarea></div>
                <button class="btn" type="submit">Update Expense</button>
            </form>
        </div>
    </section>
@endsection

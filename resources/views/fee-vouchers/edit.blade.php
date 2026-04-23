@extends('layouts.school')

@section('title', 'Edit Voucher | Pakistan Grammar School')
@section('page_heading', 'Edit Fee Voucher')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('fee-vouchers.index', ['student_code' => $voucher->student?->student_code]) }}" class="action-chip" title="Back to fee vouchers" aria-label="Back to fee vouchers">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="header-action-text">Vouchers</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .edit-card {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .edit-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 22px;
            color: #1f3f24;
            font-weight: 800;
        }

        .edit-body {
            padding: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 10px;
        }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .field input,
        .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fcfdff;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            width: 100%;
            padding: 11px;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <section class="edit-card">
        <header class="edit-head">Edit Voucher - {{ $voucher->voucher_number }}</header>
        <div class="edit-body">
            <form method="POST" action="{{ route('fee-vouchers.update', $voucher) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Student</label>
                    <input type="text" readonly value="{{ $voucher->student?->full_name }} ({{ $voucher->student?->student_code }})">
                </div>
                <div class="field">
                    <label for="billing_month">Month</label>
                    <input id="billing_month" type="month" name="billing_month" value="{{ old('billing_month', optional($voucher->billing_month)->format('Y-m')) }}" required>
                </div>
                <div class="field">
                    <label for="due_date">Due Date</label>
                    <input id="due_date" type="date" name="due_date" value="{{ old('due_date', optional($voucher->due_date)->toDateString()) }}" required>
                </div>
                <div class="field">
                    <label for="arrears">Arrears</label>
                    <input id="arrears" type="number" step="0.01" min="0" name="arrears" value="{{ old('arrears', (float) $voucher->arrears) }}">
                </div>
                <div class="field">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3">{{ old('notes', $voucher->notes) }}</textarea>
                </div>

                <button class="btn" type="submit">Update Voucher</button>
            </form>
        </div>
    </section>
@endsection


@extends('layouts.school')

@section('title', 'Edit Employee | Pakistan Grammar School')
@section('page_heading', 'Edit Employee')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('staff-salaries.index') }}" class="action-chip" title="Back to payroll" aria-label="Back to payroll">← <span class="header-action-text">Payroll</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            max-width: 800px;
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
        .field select {
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
    @php
        $channel = old('payment_method', $staffMember->payment_method ?? ($staffMember->online_wallet_type ? 'wallet' : 'bank'));
    @endphp
    <section class="card">
        <header class="head">Edit Employee - {{ $staffMember->employee_code }}</header>
        <div class="body">
            <form method="POST" action="{{ route('staff-salaries.employees.update', $staffMember) }}">
                @csrf
                @method('PUT')
                <div class="field"><label>Name</label><input type="text" name="name" value="{{ old('name', $staffMember->name) }}" required></div>
                <div class="field"><label>CNIC</label><input type="text" name="cnic" value="{{ old('cnic', $staffMember->cnic) }}" placeholder="12345-1234567-1" pattern="\d{5}-\d{7}-\d" required></div>
                <div class="field"><label>Contact Number</label><input type="text" name="contact_number" value="{{ old('contact_number', $staffMember->contact_number ?? $staffMember->phone) }}" placeholder="03XX-XXXXXXX" pattern="03\d{2}-\d{7}" required></div>
                <div class="field"><label>Designation</label><input type="text" name="designation" value="{{ old('designation', $staffMember->designation) }}"></div>
                <div class="field"><label>Monthly Wage</label><input type="number" step="0.01" min="0" name="monthly_wage" value="{{ old('monthly_wage', (float) $staffMember->monthly_wage) }}" required></div>
                <div class="field"><label>Joining Date</label><input type="date" name="joining_date" value="{{ old('joining_date', optional($staffMember->joining_date)->toDateString()) }}" required></div>
                <div class="field"><label>Status</label>
                    <select name="is_active" required>
                        <option value="1" @selected(old('is_active', $staffMember->is_active) == 1)>Active</option>
                        <option value="0" @selected(old('is_active', $staffMember->is_active) == 0)>Inactive</option>
                    </select>
                </div>
                <div class="field">
                    <label>Payment Method</label>
                    <select name="payment_method" class="salary-channel" required>
                        <option value="bank" @selected($channel === 'bank')>Bank Transfer</option>
                        <option value="wallet" @selected($channel === 'wallet')>Online Wallet</option>
                    </select>
                </div>
                <div class="field bank-only">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $staffMember->bank_name) }}">
                </div>
                <div class="field bank-only">
                    <label>Branch Code</label>
                    <input type="text" name="branch_code" value="{{ old('branch_code', $staffMember->branch_code) }}">
                </div>
                <div class="field bank-only">
                    <label>IBAN</label>
                    <input type="text" name="iban" value="{{ old('iban', $staffMember->iban) }}" placeholder="PK00AAAA0000000000000000">
                </div>
                <div class="field bank-only">
                    <label>Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $staffMember->account_number) }}">
                </div>
                <div class="field wallet-only" style="display:none;">
                    <label>Wallet Type</label>
                    <select name="online_wallet_type">
                        <option value="">Select Wallet</option>
                        <option value="easypaisa" @selected(old('online_wallet_type', $staffMember->online_wallet_type) === 'easypaisa')>Easypaisa</option>
                        <option value="jazzcash" @selected(old('online_wallet_type', $staffMember->online_wallet_type) === 'jazzcash')>JazzCash</option>
                    </select>
                </div>
                <div class="field wallet-only" style="display:none;">
                    <label>Wallet Number</label>
                    <input type="text" name="online_wallet_number" value="{{ old('online_wallet_number', $staffMember->online_wallet_number) }}" placeholder="03XX-XXXXXXX" pattern="03\d{2}-\d{7}">
                </div>
                <button class="btn" type="submit">Update Employee</button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const select = document.querySelector('.salary-channel');
            if (!select) return;
            const form = select.closest('form');
            const update = () => {
                const isBank = select.value === 'bank';
                form.querySelectorAll('.bank-only').forEach((el) => el.style.display = isBank ? 'block' : 'none');
                form.querySelectorAll('.wallet-only').forEach((el) => el.style.display = isBank ? 'none' : 'block');
            };
            select.addEventListener('change', update);
            update();
        })();
    </script>
@endpush


@extends('layouts.school')

@section('title', 'Staff Salaries & Payroll | Pakistan Grammar School')
@section('page_heading', 'Staff Salaries & Payroll')

@push('styles')
    <style>
        .stats-grid {
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
            color: #5f7b63;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .stat strong {
            display: block;
            margin-top: 5px;
            color: #19341e;
            font-size: 28px;
            font-weight: 900;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 2fr);
            gap: 12px;
        }

        .panel {
            background: #fff;
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
            color: #1d4589;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
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
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            color: #355538;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn.primary {
            background: #0f7a35;
            color: #fff;
            border-color: #0f7a35;
        }

        .btn.warning {
            background: #d9bc63;
            color: #2f2b1e;
            border-color: #d9bc63;
        }

        .btn.danger {
            color: #9f3131;
            border-color: #ffd3d3;
            background: #fff7f7;
        }

        .btn.block {
            width: 100%;
        }

        .tabs {
            display: inline-flex;
            gap: 6px;
            border: 1px solid #d4ead4;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 10px;
            background: #fff;
        }

        .tab-btn {
            border: 0;
            border-radius: 9px;
            padding: 8px 11px;
            background: transparent;
            color: #58735c;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .tab-btn.active {
            background: #0f7a35;
            color: #fff;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border-top: 1px solid #e8f3e8;
            text-align: left;
            padding: 9px 8px;
            font-size: 13px;
            vertical-align: top;
        }

        .table th {
            border-top: none;
            color: #56735a;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .status-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .status-pill.paid {
            background: #ddf8e4;
            color: #0f7a35;
            border-color: #b7f1ce;
        }

        .status-pill.unpaid {
            background: #fff2da;
            color: #966113;
            border-color: #ffe8b8;
        }

        .muted {
            color: #6d886f;
            font-size: 12px;
        }

        .search-row {
            display: grid;
            grid-template-columns: 1.2fr repeat(4, minmax(0, 1fr)) auto;
            gap: 8px;
            margin-bottom: 12px;
        }

        .actions-row {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .inline-pay {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 6px;
            margin-top: 8px;
        }

        .inline-pay .field {
            margin-bottom: 0;
        }

        .staff-list {
            margin-top: 12px;
        }

        .info-box {
            margin-bottom: 12px;
            border: 1px solid #d4ead4;
            border-radius: 10px;
            background: #fbfffb;
            padding: 10px;
        }

        @media (max-width: 1280px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .search-row,
            .inline-pay {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .stats-grid {
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
    <section class="stats-grid">
        <article class="stat"><small>Active Workers</small><strong>{{ number_format($activeWorkers) }}</strong></article>
        <article class="stat"><small>Monthly Wage Bill</small><strong>Rs {{ number_format($monthlyWageBill, 0) }}</strong></article>
        <article class="stat"><small>Advances This Month</small><strong>Rs {{ number_format($advancesMonth, 0) }}</strong></article>
        <article class="stat"><small>Wages Paid</small><strong>Rs {{ number_format($wagesPaid, 0) }}</strong></article>
    </section>

    <section class="panel" style="margin-bottom:12px;">
        <header class="head">Search & Filters</header>
        <div class="body">
            <form method="GET" class="search-row">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search staff by name / ID / CNIC / contact">
                <input type="month" name="month" value="{{ $month }}">
                <input type="date" name="date_from" value="{{ $dateFrom }}">
                <input type="date" name="date_to" value="{{ $dateTo }}">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="Unpaid" @selected($statusFilter === 'Unpaid')>Unpaid</option>
                    <option value="Paid" @selected($statusFilter === 'Paid')>Paid</option>
                </select>
                <button class="btn primary" type="submit">Apply</button>
            </form>
        </div>
    </section>

    <section class="layout">
        <article class="panel" id="employee-form">
            <header class="head">Staff Information Management</header>
            <div class="body">
                <form method="POST" action="{{ route('staff-salaries.employees.store') }}">
                    @csrf
                    <div class="field"><label>Auto Staff ID</label><input type="text" value="Auto generated by system" readonly></div>
                    <div class="field"><label>Name</label><input type="text" name="name" value="{{ old('name') }}" required></div>
                    <div class="field"><label>CNIC</label><input type="text" name="cnic" value="{{ old('cnic') }}" placeholder="12345-1234567-1" required></div>
                    <div class="field"><label>Contact Number</label><input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="03XX-XXXXXXX" pattern="03\d{2}-\d{7}" required></div>
                    <div class="field"><label>Designation</label><input type="text" name="designation" value="{{ old('designation') }}"></div>
                    <div class="field"><label>Monthly Wage</label><input type="number" step="0.01" min="0" name="monthly_wage" value="{{ old('monthly_wage') }}" required></div>
                    <div class="field"><label>Joining Date</label><input type="date" name="joining_date" value="{{ old('joining_date', now()->toDateString()) }}" required></div>
                    <div class="field">
                        <label>Payment Method</label>
                        <select name="payment_method" class="staff-payment-method" required>
                            <option value="bank">Bank Transfer</option>
                            <option value="wallet">Online Wallet</option>
                        </select>
                    </div>
                    <div class="field staff-bank-only"><label>Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name') }}"></div>
                    <div class="field staff-bank-only"><label>Branch Code</label><input type="text" name="branch_code" value="{{ old('branch_code') }}"></div>
                    <div class="field staff-bank-only"><label>IBAN</label><input type="text" name="iban" value="{{ old('iban') }}" placeholder="PK00AAAA0000000000000000"></div>
                    <div class="field staff-bank-only"><label>Account Number</label><input type="text" name="account_number" value="{{ old('account_number') }}"></div>
                    <div class="field staff-wallet-only" style="display:none;">
                        <label>Wallet Type</label>
                        <select name="online_wallet_type">
                            <option value="">Select Wallet</option>
                            <option value="easypaisa" @selected(old('online_wallet_type') === 'easypaisa')>Easypaisa</option>
                            <option value="jazzcash" @selected(old('online_wallet_type') === 'jazzcash')>JazzCash</option>
                        </select>
                    </div>
                    <div class="field staff-wallet-only" style="display:none;"><label>Wallet Number</label><input type="text" name="online_wallet_number" value="{{ old('online_wallet_number') }}" placeholder="03XX-XXXXXXX" pattern="03\d{2}-\d{7}"></div>
                    <input type="hidden" name="is_active" value="1">
                    <button class="btn primary block" type="submit">Save Staff Info</button>
                </form>

                <div class="staff-list">
                    <h4 style="margin: 0 0 8px; color:#1f3f24;">Staff List</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staff as $member)
                                <tr>
                                    <td>{{ $member->employee_code }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->cnic }}</td>
                                    <td>{{ $member->contact_number }}</td>
                                    <td><a class="btn" href="{{ route('staff-salaries.employees.edit', $member) }}">Update Info</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No staff found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="list-pagination">{{ $staff->links() }}</div>
                </div>
            </div>
        </article>

        <article class="panel">
            <header class="head">Payroll Management (CRUD)</header>
            <div class="body">
                <div class="info-box">
                    <form method="POST" action="{{ route('staff-salaries.payroll.store') }}">
                        @csrf
                        <div class="search-row" style="margin-bottom:8px;">
                            <select name="staff_member_id" required>
                                <option value="">Select Staff</option>
                                @foreach ($staffOptions as $staffOption)
                                    <option value="{{ $staffOption->id }}">{{ $staffOption->employee_code }} - {{ $staffOption->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" min="0" name="salary_amount" placeholder="Salary Amount" required>
                            <input type="month" name="payroll_month" value="{{ $month }}" required>
                            <input type="text" value="Status: Unpaid (default)" readonly>
                            <input type="text" name="notes" placeholder="Optional notes">
                            <button class="btn primary" type="submit">Create Payroll Entry</button>
                        </div>
                    </form>
                </div>

                <div class="tabs">
                    <button class="tab-btn active" type="button" data-tab="unpaidTab">Unpaid Salaries</button>
                    <button class="tab-btn" type="button" data-tab="paidTab">Paid Salaries</button>
                </div>

                <div id="unpaidTab" class="tab-pane active">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Operations</th>
                            </tr>
                        </thead>
                        <tbody id="unpaidRows">
                            @forelse ($unpaidRecords as $record)
                                <tr data-payroll-id="{{ $record->id }}">
                                    <td>
                                        <strong>{{ $record->staffMember?->name }}</strong>
                                        <div class="muted">{{ $record->staffMember?->employee_code }}</div>
                                    </td>
                                    <td>{{ optional($record->transaction_month)->format('M Y') }}</td>
                                    <td>Rs {{ number_format((float) $record->amount, 0) }}</td>
                                    <td><span class="status-pill unpaid">Unpaid</span></td>
                                    <td>
                                        <div class="actions-row">
                                            <button type="button" class="btn toggle-pay">Pay Wages</button>
                                            <form method="POST" action="{{ route('staff-salaries.payroll.destroy', $record) }}" onsubmit="return confirm('Delete payroll record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                        <details style="margin-top:8px;">
                                            <summary class="btn">Update Record</summary>
                                            <form method="POST" action="{{ route('staff-salaries.payroll.update', $record) }}" style="margin-top:8px;">
                                                @csrf
                                                @method('PUT')
                                                <div class="inline-pay">
                                                    <div class="field"><label>Amount</label><input type="number" step="0.01" min="0" name="salary_amount" value="{{ (float) $record->amount }}" required></div>
                                                    <div class="field"><label>Month</label><input type="month" name="payroll_month" value="{{ optional($record->transaction_month)->format('Y-m') }}" required></div>
                                                    <div class="field"><label>Status</label><select name="status"><option value="Unpaid" selected>Unpaid</option><option value="Paid">Paid</option></select></div>
                                                    <div class="field"><label>Payment Method</label><select name="payment_method"><option value="bank">Bank</option><option value="wallet">Online Wallet</option></select></div>
                                                    <div class="field"><label>Account/Wallet</label><input type="text" name="account_number" value="{{ $record->account_number }}"></div>
                                                </div>
                                                <button class="btn primary" type="submit">Update</button>
                                            </form>
                                        </details>

                                        <form class="js-pay-form" method="POST" action="{{ route('staff-salaries.payroll.pay', $record) }}" style="display:none; margin-top:8px;">
                                            @csrf
                                            <div class="inline-pay">
                                                <div class="field">
                                                    <label>Method</label>
                                                    <select name="payment_method" class="pay-method-select">
                                                        <option value="bank">Bank Transfer</option>
                                                        <option value="wallet">Online Wallet</option>
                                                    </select>
                                                </div>
                                                <div class="field wallet-only" style="display:none;">
                                                    <label>Wallet</label>
                                                    <select name="wallet_type">
                                                        <option value="easypaisa">Easypaisa</option>
                                                        <option value="jazzcash">JazzCash</option>
                                                    </select>
                                                </div>
                                                <div class="field bank-only"><label>Bank Name</label><input type="text" name="bank_name" value="{{ $record->staffMember?->bank_name }}"></div>
                                                <div class="field bank-only"><label>Branch Code</label><input type="text" name="branch_code" value="{{ $record->staffMember?->branch_code }}"></div>
                                                <div class="field bank-only"><label>IBAN</label><input type="text" name="iban" value="{{ $record->staffMember?->iban }}"></div>
                                                <div class="field"><label>Account / Wallet</label><input type="text" name="account_number" value="{{ $record->staffMember?->account_number ?? $record->staffMember?->online_wallet_number }}"></div>
                                                <div class="field"><label>Payment Date</label><input type="date" name="payment_date" value="{{ now()->toDateString() }}"></div>
                                                <div class="field"><label>Notes</label><input type="text" name="notes" value="{{ $record->notes }}"></div>
                                            </div>
                                            <button class="btn primary" type="submit">Confirm Pay</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No unpaid salaries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="list-pagination">{{ $unpaidRecords->links() }}</div>
                </div>

                <div id="paidTab" class="tab-pane">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Details</th>
                            </tr>
                        </thead>
                        <tbody id="paidRows">
                            @forelse ($paidRecords as $record)
                                <tr data-payroll-id="{{ $record->id }}">
                                    <td>
                                        <strong>{{ $record->staffMember?->name }}</strong>
                                        <div class="muted">{{ $record->staffMember?->employee_code }}</div>
                                    </td>
                                    <td>{{ optional($record->transaction_month)->format('M Y') }}</td>
                                    <td>Rs {{ number_format((float) $record->amount, 0) }}</td>
                                    <td><span class="status-pill paid">Paid</span></td>
                                    <td>
                                        <div><strong>{{ strtoupper($record->payment_method ?? '-') }}</strong></div>
                                        <div class="muted">{{ $record->account_number ?: '-' }}</div>
                                        <div class="muted">{{ optional($record->paid_at)->format('d M Y') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No paid salaries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="list-pagination">{{ $paidRecords->links() }}</div>
                </div>
            </div>
        </article>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const syncStaffMethod = () => {
                const select = document.querySelector('.staff-payment-method');
                if (!select) return;
                const form = select.closest('form');
                const update = () => {
                    const isBank = select.value === 'bank';
                    form.querySelectorAll('.staff-bank-only').forEach((el) => el.style.display = isBank ? 'block' : 'none');
                    form.querySelectorAll('.staff-wallet-only').forEach((el) => el.style.display = isBank ? 'none' : 'block');
                };
                select.addEventListener('change', update);
                update();
            };

            const bindTabs = () => {
                const buttons = document.querySelectorAll('.tab-btn');
                const panes = document.querySelectorAll('.tab-pane');
                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        buttons.forEach((b) => b.classList.remove('active'));
                        panes.forEach((p) => p.classList.remove('active'));
                        btn.classList.add('active');
                        document.getElementById(btn.dataset.tab)?.classList.add('active');
                    });
                });
            };

            const bindTogglePayPanels = () => {
                document.querySelectorAll('.toggle-pay').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const row = btn.closest('tr');
                        const form = row?.querySelector('.js-pay-form');
                        if (!form) return;
                        form.style.display = form.style.display === 'block' ? 'none' : 'block';
                    });
                });
            };

            const bindPayMethodSelectors = () => {
                document.querySelectorAll('.pay-method-select').forEach((select) => {
                    const form = select.closest('form');
                    const update = () => {
                        const isBank = select.value === 'bank';
                        form.querySelectorAll('.bank-only').forEach((el) => el.style.display = isBank ? 'block' : 'none');
                        form.querySelectorAll('.wallet-only').forEach((el) => el.style.display = isBank ? 'none' : 'block');
                    };
                    select.addEventListener('change', update);
                    update();
                });
            };

            const toCurrency = (value) => `Rs ${Number(value || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 })}`;

            const bindAjaxPay = () => {
                document.querySelectorAll('.js-pay-form').forEach((form) => {
                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) submitBtn.disabled = true;
                        try {
                            const fd = new FormData(form);
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                body: fd,
                            });
                            const data = await response.json();
                            if (!response.ok) {
                                alert(data.message || 'Unable to mark payroll as paid.');
                                if (submitBtn) submitBtn.disabled = false;
                                return;
                            }

                            const row = form.closest('tr');
                            const paidBody = document.getElementById('paidRows');
                            if (!row || !paidBody) return;

                            const record = data.record;
                            row.remove();
                            const paidRow = document.createElement('tr');
                            paidRow.innerHTML = `
                                <td><strong>${record.name || '-'}</strong><div class="muted">${record.employee_code || '-'}</div></td>
                                <td>${record.month || '-'}</td>
                                <td>${toCurrency(record.amount)}</td>
                                <td><span class="status-pill paid">Paid</span></td>
                                <td><div><strong>${record.payment_method || '-'}</strong></div><div class="muted">${record.paid_at || '-'}</div></td>
                            `;
                            paidBody.prepend(paidRow);
                        } catch (_) {
                            alert('Network error while updating salary status.');
                        } finally {
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    });
                });
            };

            syncStaffMethod();
            bindTabs();
            bindTogglePayPanels();
            bindPayMethodSelectors();
            bindAjaxPay();
        })();
    </script>
@endpush


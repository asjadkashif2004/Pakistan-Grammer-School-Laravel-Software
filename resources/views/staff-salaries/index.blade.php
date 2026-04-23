@extends('layouts.school')

@section('title', 'Workers Salaries & Payroll | Pakistan Grammar School')
@section('page_heading', 'Staff Salaries & Payroll')

@section('header_actions')
    <div class="header-actions-slot">
        <button type="button" class="action-chip primary" data-open-modal="modal-add-worker" title="Add Worker">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="header-action-text">Add Worker</span>
        </button>
    </div>
@endsection

@push('styles')
    <style>
        .payroll-wrap { display: flex; flex-direction: column; gap: 14px; }

        .toolbar-card, .stat-card, .worker-card {
            background: #fff;
            border: 1px solid #d8ead8;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(15, 41, 21, 0.04);
        }

        .toolbar-card {
            padding: 12px;
            display: grid;
            grid-template-columns: minmax(170px, 220px) 1fr auto;
            gap: 10px;
            align-items: end;
            background: linear-gradient(180deg, #ffffff 0%, #fbfffc 100%);
        }

        .toolbar-card .field { margin: 0; }
        .toolbar-search-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: end;
        }
        .field { display: flex; flex-direction: column; gap: 4px; }
        .field label { font-size: 10px; font-weight: 800; letter-spacing: .08em; color: #4d6950; text-transform: uppercase; }
        .field input, .field select, .field textarea {
            width: 100%; border: 1px solid #d6e6d8; border-radius: 10px; padding: 9px 10px; font-size: 13px; background: #fdfffd;
        }

        .btn {
            border: 1px solid #d4ead4; border-radius: 10px; padding: 8px 11px; background: #fff; color: #355538; font-size: 12px;
            font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none;
            transition: all .16s ease;
        }
        .btn.primary { background: #0f7a35; border-color: #0f7a35; color: #fff; }
        .btn.soft { background: #f6fff7; }
        .btn.warn { background: #fff9ed; color: #8c6116; border-color: #ffe5b1; }
        .btn.danger { background: #fff7f7; color: #9f3131; border-color: #ffd3d3; }
        .btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

        .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 12px rgba(15, 40, 20, 0.10); }
        .btn:disabled:hover { transform: none; box-shadow: none; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .stat-card { padding: 11px 12px; position: relative; overflow: hidden; }
        .stat-card::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 3px;
            background: linear-gradient(90deg, #0f7a35 0%, #37a85f 100%);
            opacity: .18;
        }
        .stat-card .k { font-size: 10px; color: #5f7b63; letter-spacing: .08em; font-weight: 800; text-transform: uppercase; }
        .stat-card .v { margin-top: 7px; font-size: 22px; line-height: 1.05; color: #19341e; font-weight: 900; }
        .stat-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .stat-ico {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f7a35;
            background: #e9f7ee;
        }
        .stat-ico svg { width: 14px; height: 14px; }

        .worker-list { display: flex; flex-direction: column; gap: 10px; }
        .worker-card { padding: 12px; }
        .worker-grid { display: grid; grid-template-columns: 1.35fr 1fr; gap: 10px; align-items: start; }
        .worker-head { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .avatar {
            width: 42px; height: 42px; border-radius: 999px; color: #fff; font-weight: 800; font-size: 13px;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(145deg, #0f7a35, #25a356);
        }
        .worker-name { font-size: 17px; font-weight: 800; color: #15361a; line-height: 1.15; }
        .worker-meta { margin-top: 3px; display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap; color: #5f7863; font-weight: 700; font-size: 12px; }
        .role-pill { font-size: 9px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; border-radius: 999px; padding: 2px 7px; }
        .role-pill.r1 { background: #e8eefc; color: #3d5fa9; }
        .role-pill.r2 { background: #ebf8ec; color: #2e8e4b; }
        .role-pill.r3 { background: #fff3df; color: #a77914; }
        .role-pill.r4 { background: #f0f2f6; color: #58616f; }

        .worker-metrics { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .metric {
            text-align: left;
            background: #f8fcf9;
            border: 1px solid #d9eadb;
            border-radius: 10px;
            padding: 8px;
        }
        .metric .k { font-size: 9px; letter-spacing: .08em; font-weight: 800; color: #6b846f; text-transform: uppercase; }
        .metric .v { margin-top: 4px; font-size: 16px; line-height: 1.05; font-weight: 900; color: #233d28; }
        .metric .v.minus { color: #af4c4c; }
        .metric .v.plus { color: #2f9b5b; }
        .metric .v.net { color: #0f7a35; }
        .worker-actions { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 7px; }
        .worker-actions .btn { min-height: 34px; }
        .lock-note { margin-left: auto; display: inline-flex; align-items: center; gap: 6px; color: #0f7a35; font-weight: 800; font-size: 11px; }
        .dialog {
            border: 1px solid #d4ead4; border-radius: 14px; padding: 0; width: min(560px, calc(100vw - 18px));
            max-height: calc(100vh - 24px); overflow: auto;
        }
        .dialog::backdrop { background: rgba(15, 40, 20, .35); }
        .dialog-head { padding: 11px 13px; border-bottom: 1px solid #e7f3e7; display: flex; justify-content: space-between; align-items: center; gap: 8px; background: #f8fdf9; }
        .dialog-title { font-size: 15px; color: #15361a; font-weight: 900; }
        .dialog-body { padding: 12px 13px; }
        .dialog-actions { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
        .breakdown-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 8px; }
        .box { border: 1px solid #d9eadb; border-radius: 10px; padding: 9px 10px; background: #fbfffb; }
        .box .k { font-size: 9px; text-transform: uppercase; color: #5f7b63; letter-spacing: .08em; font-weight: 800; }
        .box .v { margin-top: 3px; font-size: 15px; font-weight: 900; color: #1d3f23; }
        .details-list { margin-top: 6px; border-top: 1px dashed #d6e7d8; padding-top: 6px; display: grid; gap: 6px; }
        .details-row { display: flex; justify-content: space-between; gap: 8px; font-size: 13px; }
        .empty { border: 1px dashed #d4ead4; border-radius: 12px; padding: 18px; text-align: center; color: #6c866f; font-weight: 700; background: #fbfffb; }

        @media (max-width: 1200px) {
            .worker-grid { grid-template-columns: 1fr; }
            .worker-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 980px) {
            .toolbar-card { grid-template-columns: 1fr 1fr; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .worker-name { font-size: 15px; }
            .metric .v { font-size: 16px; }
        }
        @media (max-width: 640px) {
            .toolbar-card { grid-template-columns: 1fr; }
            .stats-grid, .worker-metrics, .breakdown-grid { grid-template-columns: 1fr; }
            .worker-actions { flex-direction: column; align-items: stretch; }
            .lock-note { margin-left: 0; }
        }
    </style>
@endpush

@section('content')
    @php
        $palette = ['r1', 'r2', 'r3', 'r4'];
    @endphp
    <div class="payroll-wrap">
        <section class="toolbar-card">
            <form method="GET" class="field" style="grid-column: span 1;">
                <label for="month">Month</label>
                <select id="month" name="month" onchange="this.form.submit()">
                    @php
                        $start = now()->copy()->subMonths(12)->startOfMonth();
                    @endphp
                    @for ($i = 0; $i < 25; $i++)
                        @php
                            $m = $start->copy()->addMonths($i)->format('Y-m');
                        @endphp
                        <option value="{{ $m }}" @selected($m === $month)>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}</option>
                    @endfor
                </select>
            </form>
            <form method="GET" class="field" style="grid-column: span 1;">
                <label for="q">Search Worker</label>
                <div class="toolbar-search-row">
                    <input id="q" type="text" name="q" value="{{ $search }}" placeholder="Search by name / code / phone / role">
                    <button class="btn soft" type="submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        Search
                    </button>
                </div>
                <input type="hidden" name="month" value="{{ $month }}">
            </form>
            <button type="submit" class="btn soft" form="add-worker-form-trigger" disabled style="display:none;">x</button>
            <button type="button" class="btn primary" data-open-modal="modal-add-worker">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Add Worker
            </button>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <div class="stat-top">
                    <div class="k">Active Workers</div>
                    <span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/><path d="M4 20a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                </div>
                <div class="v">{{ number_format((int) $summary['active_workers']) }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-top">
                    <div class="k">Monthly Wage Bill</div>
                    <span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v16M7 8h7a3 3 0 1 0 0-6H9a3 3 0 0 0 0 6h6a3 3 0 1 1 0 6H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                </div>
                <div class="v">Rs {{ number_format((float) $summary['monthly_wage_bill'], 0) }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-top">
                    <div class="k">Advances This Month</div>
                    <span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                </div>
                <div class="v">Rs {{ number_format((float) $summary['advances_month'], 0) }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-top">
                    <div class="k">Wages Paid</div>
                    <span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5 10 17l9-9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>
                <div class="v">Rs {{ number_format((float) $summary['wages_paid'], 0) }}</div>
            </article>
        </section>

        <section class="worker-list">
            @forelse ($workers as $index => $worker)
                @php
                    $isLocked = (bool) $worker->is_locked;
                    $isActive = (bool) $worker->is_active;
                    $roleTone = $palette[$index % count($palette)];
                    $initials = collect(explode(' ', (string) $worker->name))->take(2)->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('');
                @endphp
                <article class="worker-card">
                    <div class="worker-grid">
                        <div class="worker-head">
                            <span class="avatar">{{ $initials ?: 'WK' }}</span>
                            <div style="min-width:0;">
                                <div class="worker-name">{{ $worker->name }}</div>
                                <div class="worker-meta">
                                    <span class="role-pill {{ $roleTone }}">{{ $worker->role ?: 'worker' }}</span>
                                    <span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="vertical-align:-2px;" aria-hidden="true"><path d="M6.6 10.8a14.4 14.4 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.08.36 2.24.56 3.44.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.9 21 3 13.1 3 3.2a1 1 0 0 1 1-1h3.48a1 1 0 0 1 1 1c0 1.2.2 2.36.56 3.44a1 1 0 0 1-.24 1z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $worker->contact_number ?: $worker->phone ?: '—' }}
                                    </span>
                                    <span style="color: {{ $isActive ? '#0f7a35' : '#a93b3b' }};">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                                    <span>{{ $worker->employee_code }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="worker-metrics">
                            <div class="metric">
                                <div class="k">Monthly Wage</div>
                                <div class="v">Rs {{ number_format((float) $worker->base_salary, 0) }}</div>
                            </div>
                            <div class="metric">
                                <div class="k">Advances</div>
                                <div class="v minus">- Rs {{ number_format((float) $worker->advance_total, 0) }}</div>
                            </div>
                            <div class="metric">
                                <div class="k">Extra Hrs</div>
                                <div class="v plus">{{ (float) $worker->overtime_total > 0.009 ? '+ Rs '.number_format((float) $worker->overtime_total, 0) : '—' }}</div>
                            </div>
                            <div class="metric">
                                <div class="k">Net Payable</div>
                                <div class="v net">Rs {{ number_format((float) $worker->final_payable, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="worker-actions">
                        <button class="btn primary" type="button" data-open-modal="modal-pay-{{ $worker->id }}" @disabled(! $isActive || $isLocked)>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h18v10H3zM3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Pay Wages
                        </button>
                        <button class="btn soft" type="button" data-open-modal="modal-overtime-{{ $worker->id }}" @disabled(! $isActive || $isLocked)>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 7v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Extra Hrs
                        </button>
                        <button class="btn soft" type="button" data-open-modal="modal-advance-{{ $worker->id }}" @disabled(! $isActive || $isLocked)>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Advance
                        </button>
                        <button class="btn" type="button" data-open-modal="modal-details-{{ $worker->id }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Details
                        </button>
                        <button class="btn" type="button" data-open-modal="modal-edit-{{ $worker->id }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4zM14 6l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Edit
                        </button>
                        <form method="POST" action="{{ route('staff-salaries.employees.destroy', $worker) }}" onsubmit="return confirm('Delete this worker?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button class="btn danger" type="submit">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2m-8 0l1 12h8l1-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Delete
                            </button>
                        </form>
                        @if ($isLocked)
                            <form method="POST" action="{{ route('staff-salaries.undo-payment', $worker) }}" onsubmit="return confirm('Undo salary payment for this month?');">
                                @csrf
                                <input type="hidden" name="month" value="{{ $month }}">
                                <button class="btn warn" type="submit">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7M3 3v6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Undo Payment
                                </button>
                            </form>
                            <span class="lock-note">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3M5 10h14v10H5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Month locked (paid)
                            </span>
                        @endif
                    </div>
                </article>

                <dialog class="dialog" id="modal-advance-{{ $worker->id }}">
                    <div class="dialog-head">
                        <div class="dialog-title">Add Advance - {{ $worker->name }}</div>
                        <button class="btn" type="button" data-close-modal>&times;</button>
                    </div>
                    <div class="dialog-body">
                        <div class="box">
                            <div class="k">Remaining allowable advance</div>
                            <div class="v">Rs {{ number_format((float) $worker->remaining_advance_limit, 0) }}</div>
                        </div>
                        <form method="POST" action="{{ route('staff-salaries.advance', $worker) }}">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <div class="field"><label>Amount</label><input type="number" name="amount" step="0.01" min="0.01" max="{{ number_format((float) $worker->remaining_advance_limit, 2, '.', '') }}" required></div>
                            <div class="field"><label>Date</label><input type="date" name="date" value="{{ now()->toDateString() }}" required></div>
                            <div class="field"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
                            <div class="dialog-actions">
                                <button class="btn primary" type="submit">Save Advance</button>
                                <button class="btn" type="button" data-close-modal>Cancel</button>
                            </div>
                        </form>
                    </div>
                </dialog>

                <dialog class="dialog" id="modal-overtime-{{ $worker->id }}">
                    <div class="dialog-head">
                        <div class="dialog-title">Add Overtime - {{ $worker->name }}</div>
                        <button class="btn" type="button" data-close-modal>&times;</button>
                    </div>
                    <div class="dialog-body">
                        <form method="POST" action="{{ route('staff-salaries.overtime', $worker) }}" data-overtime-form>
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <div class="field"><label>Hours</label><input type="number" name="hours" step="0.25" min="0.25" required></div>
                            <div class="field"><label>Rate per hour</label><input type="number" name="rate" step="0.01" min="0" value="{{ number_format((float) $worker->overtime_rate, 2, '.', '') }}" required></div>
                            <div class="box">
                                <div class="k">Auto overtime amount</div>
                                <div class="v" data-overtime-preview>Rs 0</div>
                            </div>
                            <div class="field"><label>Date</label><input type="date" name="date" value="{{ now()->toDateString() }}" required></div>
                            <div class="field"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
                            <div class="dialog-actions">
                                <button class="btn primary" type="submit">Save Overtime</button>
                                <button class="btn" type="button" data-close-modal>Cancel</button>
                            </div>
                        </form>
                    </div>
                </dialog>

                <dialog class="dialog" id="modal-pay-{{ $worker->id }}">
                    <div class="dialog-head">
                        <div class="dialog-title">Pay Salary - {{ $worker->name }}</div>
                        <button class="btn" type="button" data-close-modal>&times;</button>
                    </div>
                    <div class="dialog-body">
                        <form method="POST" action="{{ route('staff-salaries.pay-wage', $worker) }}" data-pay-form data-base="{{ number_format((float) $worker->base_salary, 2, '.', '') }}" data-advance="{{ number_format((float) $worker->advance_total, 2, '.', '') }}" data-overtime="{{ number_format((float) $worker->overtime_total, 2, '.', '') }}">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <div class="breakdown-grid">
                                <div class="box"><div class="k">Base salary</div><div class="v">Rs {{ number_format((float) $worker->base_salary, 0) }}</div></div>
                                <div class="box"><div class="k">Daily salary</div><div class="v">Rs {{ number_format((float) $worker->daily_salary, 2) }}</div></div>
                                <div class="box"><div class="k">Total advance</div><div class="v">- Rs {{ number_format((float) $worker->advance_total, 0) }}</div></div>
                                <div class="box"><div class="k">Total overtime</div><div class="v">+ Rs {{ number_format((float) $worker->overtime_total, 0) }}</div></div>
                            </div>
                            <div class="field" style="margin-top: 8px;"><label>Absent days</label><input type="number" name="absent_days" step="0.5" min="0" max="30" value="{{ number_format((float) $worker->absent_days, 2, '.', '') }}" required></div>
                            <div class="box">
                                <div class="k">Absence deduction</div>
                                <div class="v" data-absence-preview>Rs {{ number_format((float) $worker->absence_deduction, 2) }}</div>
                            </div>
                            <div class="box" style="margin-top:8px;">
                                <div class="k">Final salary payable</div>
                                <div class="v" data-final-preview>Rs {{ number_format((float) $worker->final_payable, 2) }}</div>
                            </div>
                            <div class="field"><label>Payment Date</label><input type="date" name="date" value="{{ now()->toDateString() }}" required></div>
                            <div class="field"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
                            <div class="dialog-actions">
                                <button class="btn primary" type="submit">Confirm Payment</button>
                                <button class="btn" type="button" data-close-modal>Cancel</button>
                            </div>
                        </form>
                    </div>
                </dialog>

                <dialog class="dialog" id="modal-edit-{{ $worker->id }}">
                    <div class="dialog-head">
                        <div class="dialog-title">Edit Worker - {{ $worker->name }}</div>
                        <button class="btn" type="button" data-close-modal>&times;</button>
                    </div>
                    <div class="dialog-body">
                        <form method="POST" action="{{ route('staff-salaries.employees.update', $worker) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="month" value="{{ $month }}">
                            <div class="field"><label>Name</label><input type="text" name="name" value="{{ $worker->name }}" required></div>
                            <div class="field"><label>Phone</label><input type="text" name="phone" value="{{ $worker->contact_number ?: $worker->phone }}" maxlength="11" pattern="03[0-9]{9}" inputmode="numeric" placeholder="03XXXXXXXXX" required></div>
                            <div class="field"><label>Role</label><input type="text" name="role" value="{{ $worker->role }}" required></div>
                            <div class="field"><label>Monthly Salary</label><input type="number" name="monthly_salary" step="0.01" min="0" value="{{ number_format((float) $worker->monthly_wage, 2, '.', '') }}" required></div>
                            <div class="field"><label>Overtime Rate</label><input type="number" name="overtime_rate" step="0.01" min="0" value="{{ number_format((float) $worker->overtime_rate, 2, '.', '') }}" required></div>
                            <div class="field">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="active" @selected($worker->is_active)>Active</option>
                                    <option value="inactive" @selected(! $worker->is_active)>Inactive</option>
                                </select>
                            </div>
                            <div class="dialog-actions">
                                <button class="btn primary" type="submit">Save Changes</button>
                                <button class="btn" type="button" data-close-modal>Cancel</button>
                            </div>
                        </form>
                    </div>
                </dialog>

                <dialog class="dialog" id="modal-details-{{ $worker->id }}">
                    <div class="dialog-head">
                        <div class="dialog-title">Worker Details - {{ $worker->name }}</div>
                        <button class="btn" type="button" data-close-modal>&times;</button>
                    </div>
                    <div class="dialog-body">
                        <div class="breakdown-grid">
                            <div class="box"><div class="k">Monthly Salary</div><div class="v">Rs {{ number_format((float) $worker->base_salary, 0) }}</div></div>
                            <div class="box"><div class="k">Overtime Rate</div><div class="v">Rs {{ number_format((float) $worker->overtime_rate, 2) }}/hr</div></div>
                            <div class="box"><div class="k">Total Advance</div><div class="v">Rs {{ number_format((float) $worker->advance_total, 0) }}</div></div>
                            <div class="box"><div class="k">Total Overtime</div><div class="v">Rs {{ number_format((float) $worker->overtime_total, 0) }}</div></div>
                        </div>
                        <div class="details-list">
                            <strong style="color:#1e3f24;">Advance history ({{ $month }})</strong>
                            @forelse ($worker->month_advances as $entry)
                                <div class="details-row">
                                    <span>{{ optional($entry->paid_at)->format('d M Y') ?? '—' }}</span>
                                    <span>Rs {{ number_format((float) $entry->amount, 0) }}</span>
                                </div>
                            @empty
                                <div class="details-row"><span>No advances this month.</span><span>—</span></div>
                            @endforelse
                        </div>
                        <div class="details-list">
                            <strong style="color:#1e3f24;">Overtime history ({{ $month }})</strong>
                            @forelse ($worker->month_overtime as $entry)
                                <div class="details-row">
                                    <span>{{ optional($entry->paid_at)->format('d M Y') ?? '—' }} · {{ number_format((float) ($entry->hours ?? 0), 2) }} hr @ Rs {{ number_format((float) ($entry->overtime_rate ?? 0), 2) }}</span>
                                    <span>Rs {{ number_format((float) $entry->amount, 0) }}</span>
                                </div>
                            @empty
                                <div class="details-row"><span>No overtime this month.</span><span>—</span></div>
                            @endforelse
                        </div>
                        <div class="details-list">
                            <strong style="color:#1e3f24;">Salary payment breakdown</strong>
                            @if ($worker->month_wage)
                                <div class="details-row"><span>Status</span><span>{{ $worker->month_wage->status }}</span></div>
                                <div class="details-row"><span>Base Salary</span><span>Rs {{ number_format((float) ($worker->month_wage->base_salary ?? $worker->base_salary), 0) }}</span></div>
                                <div class="details-row"><span>Total Advance</span><span>- Rs {{ number_format((float) ($worker->month_wage->total_advance ?? $worker->advance_total), 0) }}</span></div>
                                <div class="details-row"><span>Total Overtime</span><span>+ Rs {{ number_format((float) ($worker->month_wage->total_overtime ?? $worker->overtime_total), 0) }}</span></div>
                                <div class="details-row"><span>Absent Days</span><span>{{ number_format((float) ($worker->month_wage->absent_days ?? 0), 2) }}</span></div>
                                <div class="details-row"><span>Absence Deduction</span><span>- Rs {{ number_format((float) ($worker->month_wage->absence_deduction ?? 0), 2) }}</span></div>
                                <div class="details-row"><strong>Final Paid</strong><strong>Rs {{ number_format((float) $worker->month_wage->amount, 0) }}</strong></div>
                                <div class="details-row"><span>Paid At</span><span>{{ optional($worker->month_wage->paid_at)->format('d M Y') ?? '—' }}</span></div>
                            @else
                                <div class="details-row"><span>No salary payment record this month.</span><span>—</span></div>
                            @endif
                        </div>
                    </div>
                </dialog>
            @empty
                <div class="empty">No workers found for the selected filters.</div>
            @endforelse
        </section>
        <div class="list-pagination">{{ $workers->links() }}</div>
    </div>

    <dialog class="dialog" id="modal-add-worker">
        <div class="dialog-head">
            <div class="dialog-title">Add Worker</div>
            <button class="btn" type="button" data-close-modal>&times;</button>
        </div>
        <div class="dialog-body">
            <form method="POST" action="{{ route('staff-salaries.employees.store') }}" id="add-worker-form">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="field"><label>Name</label><input type="text" name="name" required></div>
                <div class="field"><label>Phone</label><input type="text" name="phone" maxlength="11" pattern="03[0-9]{9}" inputmode="numeric" placeholder="03XXXXXXXXX" required></div>
                <div class="field"><label>Role</label><input type="text" name="role" required></div>
                <div class="field"><label>Monthly Salary</label><input type="number" name="monthly_salary" step="0.01" min="0" required></div>
                <div class="field"><label>Overtime Rate</label><input type="number" name="overtime_rate" step="0.01" min="0" value="0" required></div>
                <div class="field">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="dialog-actions">
                    <button class="btn primary" type="submit">Save Worker</button>
                    <button class="btn" type="button" data-close-modal>Cancel</button>
                </div>
            </form>
        </div>
    </dialog>

    <form id="add-worker-form-trigger" style="display:none;"></form>
@endsection

@push('scripts')
    <script>
        (() => {
            const dialogs = document.querySelectorAll('dialog.dialog');
            const openModal = (id) => {
                const d = document.getElementById(id);
                if (!d || typeof d.showModal !== 'function') return;
                d.showModal();
            };
            const closeModal = (btn) => {
                const dialog = btn.closest('dialog');
                if (dialog && typeof dialog.close === 'function') dialog.close();
            };

            document.addEventListener('click', (e) => {
                const opener = e.target.closest('[data-open-modal]');
                if (opener) {
                    e.preventDefault();
                    openModal(opener.getAttribute('data-open-modal'));
                    return;
                }
                const closer = e.target.closest('[data-close-modal]');
                if (closer) {
                    e.preventDefault();
                    closeModal(closer);
                }
            });

            dialogs.forEach((dialog) => {
                dialog.addEventListener('click', (e) => {
                    const rect = dialog.getBoundingClientRect();
                    const inside = e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom;
                    if (!inside && typeof dialog.close === 'function') dialog.close();
                });
            });

            const toCurrency = (n) => `Rs ${Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 })}`;
            document.querySelectorAll('[data-overtime-form]').forEach((form) => {
                const hours = form.querySelector('input[name="hours"]');
                const rate = form.querySelector('input[name="rate"]');
                const preview = form.querySelector('[data-overtime-preview]');
                const update = () => {
                    const h = parseFloat(hours?.value || '0') || 0;
                    const r = parseFloat(rate?.value || '0') || 0;
                    preview.textContent = toCurrency(h * r);
                };
                hours?.addEventListener('input', update);
                rate?.addEventListener('input', update);
                update();
            });

            document.querySelectorAll('[data-pay-form]').forEach((form) => {
                const absent = form.querySelector('input[name="absent_days"]');
                const absPrev = form.querySelector('[data-absence-preview]');
                const finalPrev = form.querySelector('[data-final-preview]');
                const base = parseFloat(form.getAttribute('data-base') || '0') || 0;
                const advance = parseFloat(form.getAttribute('data-advance') || '0') || 0;
                const overtime = parseFloat(form.getAttribute('data-overtime') || '0') || 0;
                const daily = base / 30;

                const update = () => {
                    const days = parseFloat(absent?.value || '0') || 0;
                    const ded = Math.max(0, days * daily);
                    const finalSalary = Math.max(0, base + overtime - ded - advance);
                    absPrev.textContent = toCurrency(ded);
                    finalPrev.textContent = toCurrency(finalSalary);
                };
                absent?.addEventListener('input', update);
                update();
            });
        })();
    </script>
@endpush


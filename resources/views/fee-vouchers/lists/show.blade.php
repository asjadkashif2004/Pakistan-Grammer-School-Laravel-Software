@extends('layouts.school')

@section('title', $title.' | Pakistan Grammar School')
@section('page_heading', $title)

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('fee-vouchers.index', array_filter(['student_code' => $filters['student_code']])) }}" class="action-chip" title="Student fee workspace">
            <span class="header-action-text">Fee workspace</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .fv-panel { background: #fff; border: 1px solid #d4ead4; border-radius: 14px; overflow: hidden; margin-bottom: 12px; box-shadow: 0 10px 24px -18px rgba(10, 90, 42, 0.35); }
        .fv-panel-head { padding: 10px 12px; border-bottom: 1px solid #e7f3e7; font-weight: 800; color: #1f3f24; display: flex; align-items: center; gap: 8px; background: linear-gradient(180deg, #fbfefb 0%, #f4fbf5 100%); }
        .fv-panel-head svg { width: 16px; height: 16px; color: #0f7a35; flex: 0 0 auto; }
        .fv-panel-body { padding: 10px; }
        .fv-kpi { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
        .fv-chip { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #d4ead4; background: #f8fdf8; color: #2a4a2f; border-radius: 999px; padding: 5px 9px; font-size: 11px; font-weight: 800; letter-spacing: .2px; }
        .fv-chip svg { width: 13px; height: 13px; color: #0f7a35; }
        .fv-filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); gap: 8px; align-items: end; }
        .fv-field label { display: block; font-size: 11px; font-weight: 700; color: #1d4589; text-transform: uppercase; margin-bottom: 4px; }
        .fv-field input, .fv-field select { width: 100%; border: 1px solid #dbe8fb; border-radius: 9px; padding: 8px 9px; font-size: 13px; }
        .fv-actions { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 10px; }
        .fv-btn { border-radius: 9px; padding: 8px 11px; font-weight: 700; cursor: pointer; border: 1px solid #d4ead4; background: #fff; text-decoration: none; color: #36563a; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; position: relative; }
        .fv-btn.primary { background: #0f7a35; color: #fff; border-color: #0f7a35; }
        .fv-btn svg { width: 14px; height: 14px; display: block; }
        .fv-ib { width: 32px; height: 32px; padding: 0; border-radius: 8px; }
        .fv-ib.primary { background: #0f7a35; border-color: #0f7a35; color: #fff; }
        .fv-ib.ghost { background: #f7fbf8; color: #26482b; }
        .fv-ib.warn { background: #fff4dc; border-color: #ffd999; color: #8a5a13; }
        .fv-ib.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
        .fv-ib[data-tip]::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: #17361d; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 6px; border-radius: 6px; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity .12s ease; z-index: 3; }
        .fv-ib[data-tip]:hover::after { opacity: 1; }
        .table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2eee3; border-radius: 10px; }
        .fv-table { width: 100%; border-collapse: collapse; min-width: 860px; }
        .fv-table th, .fv-table td { text-align: left; padding: 8px 7px; border-top: 1px solid #e8f3e8; font-size: 13px; vertical-align: top; }
        .fv-table th { border-top: none; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #56735a; font-weight: 800; position: sticky; top: 0; z-index: 1; background: #f8fdf8; }
        .fv-table tbody tr:hover { background: #fbfefb; }
        .fv-voucher { font-weight: 800; color: #204426; }
        .fv-sub { margin-top: 2px; color: #6a816d; font-size: 11px; font-weight: 700; }
        .fv-money { font-weight: 800; color: #16361c; white-space: nowrap; }
        .fv-money.warn { color: #8d5312; }
        .fv-money.danger { color: #a93b3b; }
        .status { display: inline-flex; border-radius: 999px; padding: 2px 8px; font-size: 11px; font-weight: 700; }
        .status.paid { background: #ddf8e4; color: #0f7a35; border: 1px solid #b6f1cc; }
        .status.unpaid { background: #eef2ff; color: #1d4589; border: 1px solid #c9d6ff; }
        .status.partial { background: #fff2da; color: #966113; border: 1px solid #ffe8b8; }
        .status.overdue { background: #ffe3e3; color: #a93b3b; border: 1px solid #ffd0d0; }
        .err { border: 1px solid #ffd6d6; background: #fff6f6; color: #8e2d2d; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px; font-size: 13px; }
        @media (max-width: 760px) {
            .fv-panel-body { padding: 9px; }
            .fv-filters { grid-template-columns: 1fr; }
            .fv-chip { font-size: 10px; }
        }
    </style>
@endpush

@section('content')
    @php
        $activeFilters = collect($filters)->filter(fn ($value) => trim((string) $value) !== '')->count();
    @endphp
    @if ($errors->any())
        <div class="err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="fv-panel">
        <div class="fv-panel-head">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5h16M7 12h10M10 19h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Filters
        </div>
        <div class="fv-panel-body">
            <div class="fv-kpi">
                <span class="fv-chip">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12h16M12 4l8 8-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ ucfirst($kind) }} List
                </span>
                <span class="fv-chip">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    {{ $vouchers->total() }} Records
                </span>
                <span class="fv-chip">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5h16M7 12h10M10 19h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    {{ $activeFilters }} Active Filters
                </span>
            </div>
            <form method="GET" action="{{ url()->current() }}" class="fv-filters">
                <div class="fv-field">
                    <label for="f_student_code">Student ID</label>
                    <input id="f_student_code" name="student_code" value="{{ $filters['student_code'] }}" placeholder="e.g. PGS-00025">
                </div>
                <div class="fv-field">
                    <label for="f_class">Class</label>
                    <select id="f_class" name="class_name">
                        <option value="">All classes</option>
                        @foreach ($classNames as $cn)
                            <option value="{{ $cn }}" @selected($filters['class_name'] === $cn)>{{ $cn }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fv-field">
                    <label for="f_month">Billing month</label>
                    <input id="f_month" type="month" name="billing_month" value="{{ $filters['billing_month'] }}">
                </div>
                @if ($kind === 'pending')
                    <div class="fv-field">
                        <label for="f_status">Status</label>
                        <select id="f_status" name="status">
                            <option value="">All pending</option>
                            <option value="unpaid" @selected(($filters['status'] ?? '') === 'unpaid')>Unpaid</option>
                            <option value="partial" @selected(($filters['status'] ?? '') === 'partial')>Partial</option>
                        </select>
                    </div>
                @endif
                <div class="fv-actions" style="margin-top:0;">
                    <button type="submit" class="fv-btn primary">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12h16M12 4l8 8-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Apply
                    </button>
                    <a href="{{ url()->current() }}" class="fv-btn">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 11a8 8 0 1 1-2.3-5.7M20 4v7h-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="fv-panel">
        <div class="fv-panel-head">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Results ({{ $vouchers->total() }})
        </div>
        <div class="fv-panel-body">
            <div class="table-wrap">
                @include('fee-vouchers.partials.voucher-list-table', ['vouchers' => $vouchers])
            </div>
            <div class="list-pagination">{{ $vouchers->links() }}</div>
        </div>
    </div>

    @include('fee-vouchers.partials.record-payment-dialog')
@endsection

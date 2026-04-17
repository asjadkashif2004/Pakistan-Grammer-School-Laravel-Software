@extends('layouts.school')

@section('title', 'Reports | Pakistan Grammar School')
@section('page_heading', 'Reports')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="#reportsFilter" class="action-chip primary" title="Filters" aria-label="Report filters">⚙️ <span class="header-action-text">Filters</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .reports-grid {
            display: grid;
            gap: 14px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-head {
            padding: 12px 14px;
            border-bottom: 1px solid #e7f3e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .panel-title {
            margin: 0;
            font-size: 19px;
            color: #1f3f24;
            font-weight: 800;
        }

        .panel-body {
            padding: 12px;
        }

        .chips {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .chip {
            border: 1px solid #d4ead4;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #315233;
            background: #f7fcf7;
            text-decoration: none;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 11px;
            color: #56735a;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
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
            border: 1px solid #d4ead4;
            border-radius: 9px;
            padding: 9px 11px;
            background: #ffffff;
            font-size: 13px;
            font-weight: 700;
            color: #355538;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn.primary {
            border: 0;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #fff;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .stat {
            border: 1px solid #e7f3e7;
            border-radius: 12px;
            background: #fbfffb;
            padding: 10px;
        }

        .stat label {
            display: block;
            font-size: 11px;
            color: #58715b;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-weight: 700;
        }

        .stat strong {
            display: block;
            margin-top: 4px;
            font-size: 19px;
            color: #18331d;
            font-weight: 800;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 9px 8px;
            border-top: 1px solid #e8f3e8;
            font-size: 13px;
        }

        .table th {
            border-top: 0;
            color: #56735a;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 800;
        }

        .net-positive {
            color: #0f7a35;
            font-weight: 800;
        }

        .net-negative {
            color: #b63f3f;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .filter-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 780px) {
            .filter-grid,
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
    @php
        $printQuery = request()->only(['year', 'month', 'date_from', 'date_to']);
    @endphp

    <div class="reports-grid">
        <section class="panel" id="reportsFilter">
            <header class="panel-head">
                <h3 class="panel-title">📅 Report Filters</h3>
                <div class="chips">
                    <span class="chip">Period: {{ $periodLabel }}</span>
                </div>
            </header>
            <div class="panel-body">
                <form method="GET" action="{{ route('reports.index') }}" class="filter-grid">
                    <div class="field">
                        <label for="year">Year</label>
                        <select id="year" name="year">
                            @for ($y = now()->year; $y >= now()->year - 6; $y--)
                                <option value="{{ $y }}" @selected((int) $selectedYear === (int) $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="field">
                        <label for="month">Month</label>
                        <select id="month" name="month">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected((int) $selectedMonth === (int) $m)>{{ \Illuminate\Support\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="field">
                        <label for="date_from">From Date</label>
                        <input id="date_from" type="date" name="date_from" value="{{ request('date_from', $fromDate) }}">
                    </div>
                    <div class="field">
                        <label for="date_to">To Date</label>
                        <input id="date_to" type="date" name="date_to" value="{{ request('date_to', $toDate) }}">
                    </div>
                    <div class="field" style="justify-content:flex-end;">
                        <label>&nbsp;</label>
                        <div class="chips">
                            <button class="btn primary" type="submit">📊 Apply</button>
                            <a class="btn" href="{{ route('reports.index') }}">↺ Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['invoices']['icon'] }} {{ $reports['invoices']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'invoices'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Total Invoices</label><strong>{{ number_format((int) $reports['invoices']['count']) }}</strong></div>
                    <div class="stat"><label>Total Amount</label><strong>Rs {{ number_format((float) $reports['invoices']['total'], 2) }}</strong></div>
                    <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Month</th><th>Invoices</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($reports['invoices']['monthly'] as $row)
                            <tr><td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y') }}</td><td>{{ (int) $row->invoices_count }}</td><td>Rs {{ number_format((float) $row->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3">No invoice data in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['fees']['icon'] }} {{ $reports['fees']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'fees'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Collected Vouchers</label><strong>{{ number_format((int) $reports['fees']['count']) }}</strong></div>
                    <div class="stat"><label>Collected Amount</label><strong>Rs {{ number_format((float) $reports['fees']['total'], 2) }}</strong></div>
                    <div class="stat"><label>Pending Amount</label><strong>Rs {{ number_format((float) $reports['fees']['pending_amount'], 2) }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Month</th><th>Vouchers</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($reports['fees']['monthly'] as $row)
                            <tr><td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y') }}</td><td>{{ (int) $row->vouchers_count }}</td><td>Rs {{ number_format((float) $row->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3">No fee collection data in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['admissions']['icon'] }} {{ $reports['admissions']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'admissions'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Total Admissions</label><strong>{{ number_format((int) $reports['admissions']['count']) }}</strong></div>
                    <div class="stat"><label>Admission Fee Total</label><strong>Rs {{ number_format((float) $reports['admissions']['admission_income'], 2) }}</strong></div>
                    <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Class</th><th>Students</th><th>Admission Fees</th></tr></thead>
                    <tbody>
                        @forelse ($reports['admissions']['classes'] as $row)
                            <tr><td>{{ $row->class_name }}</td><td>{{ (int) $row->students_count }}</td><td>Rs {{ number_format((float) $row->admission_total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3">No admissions found in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['salaries']['icon'] }} {{ $reports['salaries']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'salaries'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Salaries Paid</label><strong>Rs {{ number_format((float) $reports['salaries']['total_paid'], 2) }}</strong></div>
                    <div class="stat"><label>Pending Wages</label><strong>{{ number_format((int) $reports['salaries']['pending_count']) }}</strong></div>
                    <div class="stat"><label>Pending Amount</label><strong>Rs {{ number_format((float) $reports['salaries']['pending_amount'], 2) }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Type</th><th>Records</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($reports['salaries']['types'] as $row)
                            <tr><td>{{ str_replace('_', ' ', ucfirst($row->transaction_type)) }}</td><td>{{ (int) $row->records_count }}</td><td>Rs {{ number_format((float) $row->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3">No salary payments in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['expenses']['icon'] }} {{ $reports['expenses']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'expenses'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Expense Records</label><strong>{{ number_format((int) $reports['expenses']['count']) }}</strong></div>
                    <div class="stat"><label>Total Expense</label><strong>Rs {{ number_format((float) $reports['expenses']['total'], 2) }}</strong></div>
                    <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Category</th><th>Entries</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($reports['expenses']['categories'] as $row)
                            <tr><td>{{ $row->category }}</td><td>{{ (int) $row->expenses_count }}</td><td>Rs {{ number_format((float) $row->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3">No expenses in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['income']['icon'] }} {{ $reports['income']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'income'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <table class="table">
                    <thead><tr><th>Income Source</th><th>Amount</th></tr></thead>
                    <tbody>
                        <tr><td>Invoices</td><td>Rs {{ number_format((float) $reports['income']['invoice_income'], 2) }}</td></tr>
                        <tr><td>Fee Collection</td><td>Rs {{ number_format((float) $reports['income']['fee_income'], 2) }}</td></tr>
                        <tr><td>{{ $reports['income']['other_income_label'] }}</td><td>Rs {{ number_format((float) $reports['income']['other_income'], 2) }}</td></tr>
                        <tr><td><strong>Total Income</strong></td><td><strong>Rs {{ number_format((float) $reports['income']['total_income'], 2) }}</strong></td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <h3 class="panel-title">{{ $reports['profit-loss']['icon'] }} {{ $reports['profit-loss']['title'] }}</h3>
                <a class="btn" target="_blank" href="{{ route('reports.print', array_merge(['report' => 'profit-loss'], $printQuery)) }}">🖨️</a>
            </header>
            <div class="panel-body">
                <div class="stats">
                    <div class="stat"><label>Total Income</label><strong>Rs {{ number_format((float) $reports['profit-loss']['total_income'], 2) }}</strong></div>
                    <div class="stat"><label>Total Expenses</label><strong>Rs {{ number_format((float) $reports['profit-loss']['total_expenses'], 2) }}</strong></div>
                    <div class="stat"><label>Net</label><strong class="{{ (float) $reports['profit-loss']['net'] >= 0 ? 'net-positive' : 'net-negative' }}">Rs {{ number_format((float) $reports['profit-loss']['net'], 2) }}</strong></div>
                </div>
                <table class="table">
                    <thead><tr><th>Month</th><th>Income</th><th>Expenses</th><th>Net</th></tr></thead>
                    <tbody>
                        @forelse ($reports['profit-loss']['monthly'] as $row)
                            <tr>
                                <td>{{ $row['month'] }}</td>
                                <td>Rs {{ number_format((float) $row['income'], 2) }}</td>
                                <td>Rs {{ number_format((float) $row['expenses'], 2) }}</td>
                                <td class="{{ (float) $row['net'] >= 0 ? 'net-positive' : 'net-negative' }}">Rs {{ number_format((float) $row['net'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No monthly data in selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

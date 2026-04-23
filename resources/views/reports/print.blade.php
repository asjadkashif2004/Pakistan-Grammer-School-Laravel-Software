<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} | Pakistan Grammar School</title>
    <style>
        :root {
            --ink: #1b1b1b;
            --muted: #666;
            --line: #dcdcdc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background: #f3f4f6;
        }

        .tools {
            max-width: 980px;
            margin: 0 auto 10px;
            display: flex;
            gap: 8px;
        }

        .tools button {
            border: 1px solid #d0d7d0;
            border-radius: 8px;
            background: #fff;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .sheet {
            max-width: 980px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 22px;
        }

        .head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .head h1 {
            margin: 0 0 5px;
            font-size: 24px;
        }

        .meta {
            font-size: 13px;
            color: var(--muted);
            margin: 2px 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 8px;
        }

        .stat label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .stat strong {
            display: block;
            margin-top: 3px;
            font-size: 17px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background: #f7f7f7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        .right { text-align: right; }
        .positive { color: #0d7a35; font-weight: 700; }
        .negative { color: #bd1f1f; font-weight: 700; }

        @media print {
            @page { size: A4; margin: 10mm; }
            body { background: #fff; padding: 0; }
            .tools { display: none; }
            .sheet { border: 0; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="tools">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <section class="sheet">
        <header class="head">
            <div>
                <h1>{{ $reportTitle }}</h1>
                <p class="meta">Pakistan Grammar School ERP</p>
                <p class="meta">Report Period: {{ $periodLabel }}</p>
            </div>
            <div>
                <p class="meta"><strong>Addsmint.com</strong></p>
                <p class="meta">Generated: {{ now()->format('d M Y h:i A') }}</p>
            </div>
        </header>

        @if ($reportKey === 'invoices')
            <div class="stats">
                <div class="stat"><label>Total Invoices</label><strong>{{ number_format((int) $reports['invoices']['count']) }}</strong></div>
                <div class="stat"><label>Total Amount</label><strong>Rs {{ number_format((float) $reports['invoices']['total'], 2) }}</strong></div>
                <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Month</th><th class="right">Invoices</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse ($reports['invoices']['monthly'] as $row)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y') }}</td>
                            <td class="right">{{ (int) $row->invoices_count }}</td>
                            <td class="right">Rs {{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportKey === 'fees')
            <div class="stats">
                <div class="stat"><label>Collected Vouchers</label><strong>{{ number_format((int) $reports['fees']['count']) }}</strong></div>
                <div class="stat"><label>Collected Amount</label><strong>Rs {{ number_format((float) $reports['fees']['total'], 2) }}</strong></div>
                <div class="stat"><label>Pending Amount</label><strong>Rs {{ number_format((float) $reports['fees']['pending_amount'], 2) }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Month</th><th class="right">Vouchers</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse ($reports['fees']['monthly'] as $row)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y') }}</td>
                            <td class="right">{{ (int) $row->vouchers_count }}</td>
                            <td class="right">Rs {{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportKey === 'admissions')
            <div class="stats">
                <div class="stat"><label>Total Admissions</label><strong>{{ number_format((int) $reports['admissions']['count']) }}</strong></div>
                <div class="stat"><label>Admission Income</label><strong>Rs {{ number_format((float) $reports['admissions']['admission_income'], 2) }}</strong></div>
                <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Class</th><th class="right">Students</th><th class="right">Admission Fee Total</th></tr></thead>
                <tbody>
                    @forelse ($reports['admissions']['classes'] as $row)
                        <tr>
                            <td>{{ $row->class_name }}</td>
                            <td class="right">{{ (int) $row->students_count }}</td>
                            <td class="right">Rs {{ number_format((float) $row->admission_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportKey === 'salaries')
            <div class="stats">
                <div class="stat"><label>Salaries Paid</label><strong>Rs {{ number_format((float) $reports['salaries']['total_paid'], 2) }}</strong></div>
                <div class="stat"><label>Pending Wages</label><strong>{{ number_format((int) $reports['salaries']['pending_count']) }}</strong></div>
                <div class="stat"><label>Pending Amount</label><strong>Rs {{ number_format((float) $reports['salaries']['pending_amount'], 2) }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Type</th><th class="right">Records</th><th class="right">Total Paid</th></tr></thead>
                <tbody>
                    @forelse ($reports['salaries']['types'] as $row)
                        <tr>
                            <td>{{ str_replace('_', ' ', ucfirst($row->transaction_type)) }}</td>
                            <td class="right">{{ (int) $row->records_count }}</td>
                            <td class="right">Rs {{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportKey === 'expenses')
            <div class="stats">
                <div class="stat"><label>Expense Records</label><strong>{{ number_format((int) $reports['expenses']['count']) }}</strong></div>
                <div class="stat"><label>Total Expense</label><strong>Rs {{ number_format((float) $reports['expenses']['total'], 2) }}</strong></div>
                <div class="stat"><label>Period</label><strong>{{ $periodLabel }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Category</th><th class="right">Entries</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse ($reports['expenses']['categories'] as $row)
                        <tr>
                            <td>{{ $row->category }}</td>
                            <td class="right">{{ (int) $row->expenses_count }}</td>
                            <td class="right">Rs {{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportKey === 'income')
            <div class="stats">
                <div class="stat"><label>Invoice Income</label><strong>Rs {{ number_format((float) $reports['income']['invoice_income'], 2) }}</strong></div>
                <div class="stat"><label>Fee Income</label><strong>Rs {{ number_format((float) $reports['income']['fee_income'], 2) }}</strong></div>
                <div class="stat"><label>Total Income</label><strong>Rs {{ number_format((float) $reports['income']['total_income'], 2) }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Income Source</th><th class="right">Amount</th></tr></thead>
                <tbody>
                    <tr><td>Invoices</td><td class="right">Rs {{ number_format((float) $reports['income']['invoice_income'], 2) }}</td></tr>
                    <tr><td>Fee Collection</td><td class="right">Rs {{ number_format((float) $reports['income']['fee_income'], 2) }}</td></tr>
                    <tr><td>{{ $reports['income']['other_income_label'] }}</td><td class="right">Rs {{ number_format((float) $reports['income']['other_income'], 2) }}</td></tr>
                    <tr><td><strong>Total Income</strong></td><td class="right"><strong>Rs {{ number_format((float) $reports['income']['total_income'], 2) }}</strong></td></tr>
                </tbody>
            </table>
        @elseif ($reportKey === 'profit-loss')
            <div class="stats">
                <div class="stat"><label>Total Income</label><strong>Rs {{ number_format((float) $reports['profit-loss']['total_income'], 2) }}</strong></div>
                <div class="stat"><label>Total Expenses</label><strong>Rs {{ number_format((float) $reports['profit-loss']['total_expenses'], 2) }}</strong></div>
                <div class="stat"><label>Net Result</label><strong class="{{ (float) $reports['profit-loss']['net'] >= 0 ? 'positive' : 'negative' }}">Rs {{ number_format((float) $reports['profit-loss']['net'], 2) }}</strong></div>
            </div>
            <table>
                <thead><tr><th>Month</th><th class="right">Income</th><th class="right">Expenses</th><th class="right">Net</th></tr></thead>
                <tbody>
                    @forelse ($reports['profit-loss']['monthly'] as $row)
                        <tr>
                            <td>{{ $row['month'] }}</td>
                            <td class="right">Rs {{ number_format((float) $row['income'], 2) }}</td>
                            <td class="right">Rs {{ number_format((float) $row['expenses'], 2) }}</td>
                            <td class="right {{ (float) $row['net'] >= 0 ? 'positive' : 'negative' }}">Rs {{ number_format((float) $row['net'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No data in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </section>
</body>
@if (request()->boolean('auto_print'))
<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
@endif
</html>

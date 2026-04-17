@extends('layouts.school')

@section('title', 'Fee Vouchers | Pakistan Grammar School')
@section('page_heading', 'Fee Vouchers')

@push('styles')
    <style>
        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 22px;
            color: #1f3f24;
            font-weight: 800;
        }

        .panel-body {
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
        .field select,
        .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fcfdff;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn {
            border: 1px solid #d4ead4;
            border-radius: 10px;
            padding: 10px 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            color: #36563a;
            background: #ffffff;
            cursor: pointer;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn.primary {
            background: #0f7a35;
            color: #ffffff;
            border-color: #0f7a35;
        }

        .btn.warning {
            background: #d7b55f;
            color: #2f2a1c;
            border-color: #d7b55f;
        }

        .btn.danger {
            background: #fff7f7;
            color: #9f3131;
            border-color: #ffd3d3;
        }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            color: #36563a;
            background: #f5fff6;
            border: 1px solid #d4ead4;
        }

        .status.paid {
            background: #ddf8e4;
            color: #0f7a35;
            border-color: #b6f1cc;
        }

        .status.pending {
            background: #fff2da;
            color: #966113;
            border-color: #ffe8b8;
        }

        .status.overdue {
            background: #ffe3e3;
            color: #a93b3b;
            border-color: #ffd0d0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .breakdown-chip {
            border: 1px solid #d4ead4;
            background: #f9fffa;
            border-radius: 10px;
            padding: 9px 10px;
        }

        .breakdown-chip .m {
            font-size: 12px;
            color: #47624a;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .breakdown-chip .x {
            font-size: 12px;
            color: #6d836f;
            font-weight: 700;
        }

        .card {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 14px;
        }

        .card .k {
            color: #47624a;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .card .v {
            margin-top: 6px;
            font-size: 26px;
            font-weight: 900;
            color: #0f7a35;
        }

        .card .s {
            margin-top: 4px;
            color: #6f8570;
            font-weight: 600;
            font-size: 12px;
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
            font-size: 14px;
        }

        .table th {
            color: #56735a;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 800;
            border-top: none;
        }

        details.generate {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        details.generate > summary {
            padding: 12px 14px;
            cursor: pointer;
            font-weight: 900;
            color: #1f3f24;
            background: linear-gradient(180deg, rgba(15, 122, 53, 0.08), rgba(15, 122, 53, 0));
        }

        details.generate .details-body {
            padding: 14px;
        }

        .error-box {
            margin-bottom: 12px;
            border: 1px solid #ffd6d6;
            background: #fff6f6;
            color: #8e2d2d;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box ul {
            margin: 6px 0 0;
            padding-left: 16px;
        }

        @media (max-width: 900px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .breakdown-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @if ($errors->any())
        <div class="error-box">
            <strong>Please correct the highlighted fields before submitting.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel" style="margin-bottom: 12px;">
        <div class="panel-head">Search Student Fee</div>
        <div class="panel-body">
            <form method="GET" action="{{ route('fee-vouchers.index') }}">
                <div class="field">
                    <label for="student_id">Student ID</label>
                    <input
                        id="student_id"
                        name="student_id"
                        value="{{ old('student_id', $selectedStudentId) }}"
                        placeholder="Enter Student DB ID (e.g. 1)"
                        required>
                </div>
                <div class="actions">
                    <button type="submit" class="btn primary">Show Fee Status</button>
                </div>
            </form>
        </div>
    </div>

    @if (! $selectedStudent)
        <div class="panel">
            <div class="panel-body" style="color:#6f8570; font-weight:700;">
                Enter a valid `student_id` to view the fee status and vouchers.
            </div>
        </div>
    @else
        <div class="panel" style="margin-bottom: 12px;">
            <div class="panel-head">Fee Status</div>
            <div class="panel-body">
                <div style="margin-bottom: 10px; font-weight: 900; color:#1f3f24;">
                    {{ $selectedStudent->full_name }} <span style="color:#6f8570; font-weight:800;">({{ $selectedStudent->student_code }})</span>
                </div>

                <div class="cards">
                    <div class="card">
                        <div class="k">Pending / Overdue Amount</div>
                        <div class="v">Rs {{ number_format((float) $totals['pending_amount'], 0) }}</div>
                        <div class="s">{{ (int) $totals['pending_count'] }} voucher(s) pending</div>
                    </div>
                    <div class="card">
                        <div class="k">Paid Amount</div>
                        <div class="v">Rs {{ number_format((float) $totals['paid_amount'], 0) }}</div>
                        <div class="s">Total paid vouchers</div>
                    </div>
                    <div class="card">
                        <div class="k">Monthly Fee</div>
                        <div class="v">Rs {{ number_format((float) $selectedStudent->monthly_fee, 0) }}</div>
                        <div class="s">Used for new voucher</div>
                    </div>
                </div>

                <div style="font-weight:900; color:#1f3f24; margin: 10px 0 4px;">Month-wise Paid / Unpaid Breakdown</div>
                <div class="breakdown-grid">
                    @forelse ($monthBreakdown as $bucket)
                        <div class="breakdown-chip">
                            <div class="m">{{ $bucket['month_label'] }}</div>
                            <div class="x">Paid: {{ $bucket['paid_count'] }} | Unpaid: {{ $bucket['unpaid_count'] }}</div>
                            <div class="x">Unpaid Amount: Rs {{ number_format((float) $bucket['unpaid_amount'], 0) }}</div>
                        </div>
                    @empty
                        <div class="breakdown-chip">
                            <div class="m">No month-wise records available.</div>
                        </div>
                    @endforelse
                </div>

                @if ($previewVoucher)
                    <div class="actions" style="justify-content: space-between; align-items: center;">
                        <div style="font-weight:900; color:#1f3f24;">Preview Voucher</div>
                        <div class="actions" style="margin-top:0;">
                            <a href="{{ route('fee-vouchers.print', $previewVoucher) }}" target="_blank" class="btn primary">Print</a>
                            <a href="{{ route('fee-vouchers.download', $previewVoucher) }}" target="_blank" class="btn">Download</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="panel" style="margin-bottom: 12px;">
            <div class="panel-head">Voucher Operations</div>
            <div class="panel-body">
                <details class="generate" {{ $previewVoucher ? 'open' : '' }}>
                    <summary>Generate New Voucher</summary>
                    <div class="details-body">
                        <form method="POST" action="{{ route('fee-vouchers.store') }}">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

                            <div class="field">
                                <label for="billing_month">Month</label>
                                <input id="billing_month" type="month" name="billing_month" value="{{ old('billing_month', $billingMonth) }}" required>
                            </div>

                            <div class="field">
                                <label for="due_date">Due Date</label>
                                <input id="due_date" type="date" name="due_date" value="{{ old('due_date', now()->addDays(15)->toDateString()) }}" required>
                            </div>

                            <div class="field">
                                <label for="monthly_fee">Monthly Fee</label>
                                <input id="monthly_fee" type="text" readonly value="Rs {{ number_format((float) $selectedStudent->monthly_fee, 0) }}">
                            </div>

                            <div class="field">
                                <label for="arrears">Arrears</label>
                                <input id="arrears" type="number" step="0.01" min="0" name="arrears" value="{{ old('arrears', 0) }}">
                            </div>

                            <div class="field">
                                <label for="fine">Fine / Late Fee</label>
                                <input id="fine" type="number" step="0.01" min="0" name="fine" value="{{ old('fine', 0) }}">
                            </div>

                            <div class="field">
                                <label for="discount">Discount</label>
                                <input id="discount" type="number" step="0.01" min="0" name="discount" value="{{ old('discount', 0) }}">
                            </div>

                            <div class="field">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>

                            <div class="actions">
                                <button type="submit" class="btn warning">Generate Voucher</button>
                                <button type="button" class="btn" onclick="window.location.href='{{ route('fee-vouchers.index', ['student_id' => $selectedStudent->id]) }}'">
                                    Refresh
                                </button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>
        </div>

        <div class="panel" style="margin-bottom: 12px;">
            <div class="panel-head">Pending / Overdue Vouchers</div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Voucher #</th>
                            <th>Month</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingDues as $due)
                            <tr>
                                <td>{{ $due->voucher_number ?? '-' }}</td>
                                <td>{{ optional($due->billing_month)->format('M Y') }}</td>
                                <td>{{ optional($due->due_date)->format('d M Y') }}</td>
                                <td>Rs {{ number_format((float) $due->amount, 0) }}</td>
                                <td>
                                    @if ($due->status === 'Overdue')
                                        <span class="status overdue">{{ $due->status }}</span>
                                    @else
                                        <span class="status pending">{{ $due->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions" style="margin:0;">
                                        <a href="{{ route('fee-vouchers.edit', $due) }}" class="btn">Edit</a>
                                        <a href="{{ route('fee-vouchers.print', $due) }}" target="_blank" class="btn primary">Print</a>
                                        <a href="{{ route('fee-vouchers.download', $due) }}" target="_blank" class="btn">Download</a>
                                    </div>
                                    <div class="actions" style="margin-top:8px;">
                                        <form method="POST" action="{{ route('fee-vouchers.collect', $due) }}">
                                            @csrf
                                            <button type="submit" class="btn primary">Collect</button>
                                        </form>
                                        <form method="POST" action="{{ route('fee-vouchers.destroy', $due) }}" onsubmit="return confirm('Delete this voucher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No pending/overdue vouchers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">Paid Vouchers</div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Voucher #</th>
                            <th>Month</th>
                            <th>Paid Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paidCollections as $col)
                            <tr>
                                <td>{{ $col->voucher_number ?? '-' }}</td>
                                <td>{{ optional($col->billing_month)->format('M Y') }}</td>
                                <td>{{ optional($col->paid_at)->format('d M Y') }}</td>
                                <td>Rs {{ number_format((float) $col->amount, 0) }}</td>
                                <td><span class="status paid">{{ $col->status }}</span></td>
                                <td>
                                    <div class="actions" style="margin:0;">
                                        <a href="{{ route('fee-vouchers.print', $col) }}" target="_blank" class="btn primary">Print</a>
                                        <a href="{{ route('fee-vouchers.download', $col) }}" target="_blank" class="btn">Download</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No paid vouchers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection


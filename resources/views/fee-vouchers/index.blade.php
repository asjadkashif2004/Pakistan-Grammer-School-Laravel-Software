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

        .btn.ghost {
            background: #f5fff6;
            border-color: #c8e6c8;
        }

        .btn.icon-only {
            min-width: 42px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .btn:disabled,
        .btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #36563a;
            background: #f5fff6;
            border: 1px solid #d4ead4;
        }

        .status.paid { background: #ddf8e4; color: #0f7a35; border-color: #b6f1cc; }
        .status.unpaid { background: #eef2ff; color: #1d4589; border-color: #c9d6ff; }
        .status.partial { background: #fff2da; color: #966113; border-color: #ffe8b8; }
        .status.overdue { background: #ffe3e3; color: #a93b3b; border-color: #ffd0d0; }

        .info-fee-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .info-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 8px 0;
            border-bottom: 1px solid #e8f3e8;
        }

        .info-row:last-child { border-bottom: none; }

        .info-row .lbl {
            font-size: 11px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .info-row .val {
            font-size: 15px;
            font-weight: 800;
            color: #1f3f24;
        }

        .info-row .val.muted { color: #6f8570; font-weight: 700; }

        .fee-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .fee-summary-card {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 12px;
            padding: 12px;
        }

        .fee-summary-card .k {
            color: #47624a;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .fee-summary-card .v {
            margin-top: 5px;
            font-size: 20px;
            font-weight: 900;
            color: #0f7a35;
        }

        .fee-summary-card .v.warn { color: #966113; }
        .fee-summary-card .v.danger { color: #a93b3b; }
        .fee-summary-card .v.neutral { color: #1f3f24; }

        .fee-summary-card .s {
            margin-top: 4px;
            color: #6f8570;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .voucher-actions-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            background: #f5fff6;
            border: 1px solid #d4ead4;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .voucher-actions-row .va-label { font-size: 13px; font-weight: 800; color: #1f3f24; }
        .voucher-actions-row .va-sub { font-size: 11px; color: #6f8570; font-weight: 600; margin-top: 2px; }

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

        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 9px 8px;
            border-top: 1px solid #e8f3e8;
            font-size: 14px;
            vertical-align: top;
        }

        .table th {
            color: #56735a;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 800;
            border-top: none;
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

        .error-box ul { margin: 6px 0 0; padding-left: 16px; }

        .flash-ok {
            margin-bottom: 12px;
            border: 1px solid #b6f1cc;
            background: #f0fff4;
            color: #1f3f24;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 700;
        }

        .section-divider {
            font-size: 11px;
            font-weight: 800;
            color: #47624a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 12px 0 6px;
        }

        dialog.fv-modal {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 0;
            max-width: min(520px, 100vw - 24px);
            width: 100%;
        }

        dialog.fv-modal::backdrop { background: rgba(15, 40, 20, 0.35); }

        .fv-modal-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-weight: 900;
            color: #1f3f24;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .fv-modal-body { padding: 14px 16px 16px; }

        .ledger-mini {
            font-size: 12px;
            color: #47624a;
            margin-top: 4px;
        }

        @media (max-width: 900px) {
            .info-fee-grid { grid-template-columns: 1fr; }
            .breakdown-grid { grid-template-columns: 1fr; }
            .fee-summary-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .hide-mobile { display: none; }
        }
    </style>
@endpush

@section('content')
    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

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
            <form method="GET" action="{{ route('fee-vouchers.index') }}" id="search-form">
                <div class="field">
                    <label for="student_code">Student ID</label>
                    <input
                        id="student_code"
                        name="student_code"
                        value="{{ old('student_code', $selectedStudentCode) }}"
                        placeholder="Enter Student ID (e.g. PGS-00025)"
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
                Enter a valid student ID (<code>student_code</code>) to fetch fee status and vouchers.
            </div>
        </div>
    @else
        <div class="info-fee-grid">
            <div class="panel">
                <div class="panel-head">Student Info</div>
                <div class="panel-body">
                    <div class="info-block">
                        <div class="info-row">
                            <span class="lbl">Full Name</span>
                            <span class="val">{{ $selectedStudent->full_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Student ID</span>
                            <span class="val muted">{{ $selectedStudent->student_code }}</span>
                        </div>
                        @if ($selectedStudent->class_name ?? null)
                            <div class="info-row">
                                <span class="lbl">Class</span>
                                <span class="val">{{ $selectedStudent->class_name }}</span>
                            </div>
                        @endif
                        @if ($selectedStudent->section ?? null)
                            <div class="info-row">
                                <span class="lbl">Section</span>
                                <span class="val">{{ $selectedStudent->section }}</span>
                            </div>
                        @endif
                        @if ($selectedStudent->father_name ?? null)
                            <div class="info-row">
                                <span class="lbl">Father / Guardian</span>
                                <span class="val">{{ $selectedStudent->father_name }}</span>
                            </div>
                        @endif
                        @if ($selectedStudent->contact_number ?? null)
                            <div class="info-row">
                                <span class="lbl">Contact</span>
                                <span class="val muted">{{ $selectedStudent->contact_number }}</span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="lbl">Monthly Fee</span>
                            <span class="val">Rs {{ number_format((float) $selectedStudent->monthly_fee, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">Fee Status</div>
                <div class="panel-body">
                    <div class="actions" style="margin-top:0; margin-bottom: 12px;">
                        <button type="button" class="btn primary" id="quick-create-challan" title="Create challan for the workspace billing month and open print">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M7 3h8v4H7V3zM5 7h14a1.5 1.5 0 0 1 1.5 1.5V16A1.5 1.5 0 0 1 19 17.5h-2.5V21h-9v-3.5H5A1.5 1.5 0 0 1 3.5 16V8.5A1.5 1.5 0 0 1 5 7z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                <path d="M9 14h6M12 11v6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                            </svg>
                            Create challan
                        </button>
                    </div>

                    @if ($previewVoucher)
                        @php
                            $pvReady = (bool) $previewVoucher->voucher_generated_at;
                            $pvPaid = $previewVoucher->totalPaidAmount();
                            $pvRem = $previewVoucher->remainingAmount();
                        @endphp
                        <div class="voucher-actions-row">
                            <div>
                                <div class="va-label">Selected voucher</div>
                                <div class="va-sub">{{ $previewVoucher->voucher_number }} · {{ optional($previewVoucher->billing_month)->format('F Y') ?? '—' }}</div>
                                <div class="ledger-mini">Received Rs {{ number_format($pvPaid, 2) }} · Remaining Rs {{ number_format($pvRem, 2) }}</div>
                            </div>
                            <div class="actions" style="margin-top:0;">
                                @if ($pvReady)
                                    <a href="{{ route('fee-vouchers.print', $previewVoucher) }}" target="_blank" rel="noopener" class="btn primary icon-only" title="Print fee challan" aria-label="Print fee challan">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M7 3h10v5H7V3zM5 8h14a2 2 0 0 1 2 2v5h-3v4H6v-4H3v-5a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            <path d="M8 17h8v4H8v-4z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        <span class="hide-mobile">Print</span>
                                    </a>
                                    <a href="{{ route('fee-vouchers.download', $previewVoucher) }}" class="btn ghost icon-only" title="Download PDF" aria-label="Download PDF">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M12 4v11m0 0l-4-4m4 4l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 19h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                        <span class="hide-mobile">PDF</span>
                                    </a>
                                    @if ($previewVoucher->status !== 'Paid' && $pvRem > 0.009)
                                        <button
                                            type="button"
                                            class="btn warning"
                                            data-open-payment
                                            data-voucher-id="{{ $previewVoucher->id }}"
                                            data-voucher-label="{{ e($previewVoucher->voucher_number ?? 'Voucher') }}"
                                            data-remaining="{{ number_format((float) $pvRem, 2, '.', '') }}"
                                        >Pay fee</button>
                                    @endif
                                @else
                                    <span class="btn primary disabled icon-only" title="Generate the voucher first" aria-disabled="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M7 3h10v5H7V3zM5 8h14a2 2 0 0 1 2 2v5h-3v4H6v-4H3v-5a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            <path d="M8 17h8v4H8v-4z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </span>
                                    <span class="btn ghost disabled icon-only" aria-disabled="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M12 4v11m0 0l-4-4m4 4l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 19h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="fee-summary-grid">
                        <div class="fee-summary-card">
                            <div class="k">Total fee (open)</div>
                            <div class="v neutral">Rs {{ number_format((float) $feeStatusSummary['total_fee'], 2) }}</div>
                            <div class="s">Unpaid + partial + overdue</div>
                        </div>
                        <div class="fee-summary-card">
                            <div class="k">Received amount</div>
                            <div class="v">Rs {{ number_format((float) $feeStatusSummary['received'], 2) }}</div>
                            <div class="s">Sum of ledger payments</div>
                        </div>
                        <div class="fee-summary-card">
                            <div class="k">Remaining amount</div>
                            <div class="v {{ $feeStatusSummary['remaining'] > 0.009 ? 'danger' : 'neutral' }}">Rs {{ number_format((float) $feeStatusSummary['remaining'], 2) }}</div>
                            <div class="s">Against open vouchers</div>
                        </div>
                        <div class="fee-summary-card">
                            <div class="k">Payment status</div>
                            <div class="s" style="margin-top:8px;">
                                @if ($feeStatusSummary['tone'] === 'success')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M7 13l3 3 7-8" stroke="#0f7a35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="12" r="9" stroke="#0f7a35" stroke-width="1.5" fill="none"/>
                                    </svg>
                                @elseif ($feeStatusSummary['tone'] === 'danger')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 9v5M12 17h.01" stroke="#a93b3b" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M10.3 3.6h3.4L21 16.8a1 1 0 0 1-.9 1.4H3.9a1 1 0 0 1-.9-1.4L10.3 3.6z" stroke="#a93b3b" stroke-width="1.5" fill="none" stroke-linejoin="round"/>
                                    </svg>
                                @elseif ($feeStatusSummary['tone'] === 'warning')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" stroke="#966113" stroke-width="1.5" fill="none"/>
                                        <path d="M12 7v6M12 16h.01" stroke="#966113" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" stroke="#1d4589" stroke-width="1.5" fill="none"/>
                                        <path d="M12 7v5l3 2" stroke="#1d4589" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                                <strong style="color:#1f3f24;">{{ $feeStatusSummary['label'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider">Month-wise Breakdown</div>
                    <div class="breakdown-grid">
                        @forelse ($monthBreakdown as $bucket)
                            <div class="breakdown-chip">
                                <div class="m">{{ $bucket['month_label'] }}</div>
                                <div class="x">Paid: {{ $bucket['paid_count'] }} | Open: {{ $bucket['unpaid_count'] }}</div>
                                <div class="x">Open Amt: Rs {{ number_format((float) $bucket['unpaid_amount'], 0) }}</div>
                            </div>
                        @empty
                            <div class="breakdown-chip">
                                <div class="m">No month-wise records available.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- One-click challan: billing month from workspace (?month=), due +15 days, no modal --}}
        <form id="fee-challan-generate-form" method="POST" action="{{ route('fee-vouchers.store') }}" aria-hidden="true" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">
            @csrf
            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
            <input type="hidden" name="billing_month" value="{{ $billingMonth }}">
            <input type="hidden" name="due_date" value="{{ now()->addDays(15)->toDateString() }}">
            <input type="hidden" name="arrears" value="0">
            <button type="submit" tabindex="-1" aria-hidden="true">Submit</button>
        </form>

        <div class="panel" style="margin-bottom: 12px;">
            <div class="panel-head">Voucher lists &amp; filters</div>
            <div class="panel-body">
                <p style="margin:0 0 10px; color:#47624a; font-weight:600; font-size:14px;">
                    Open the lists to search, filter, and record payments. Pending includes unpaid, partial, and overdue vouchers for this student.
                </p>
                <div class="actions" style="margin-top:0;">
                    <a class="btn primary" href="{{ route('fee-vouchers.list.pending', array_filter(['student_code' => $selectedStudent->student_code])) }}">
                        Pending ({{ (int) $listCounts['pending'] }})
                    </a>
                    <a class="btn ghost" href="{{ route('fee-vouchers.list.paid', array_filter(['student_code' => $selectedStudent->student_code])) }}">
                        Paid ({{ (int) $listCounts['paid'] }})
                    </a>
                </div>
            </div>
        </div>

        @include('fee-vouchers.partials.record-payment-dialog', [
            'paymentUrlTemplate' => url('/fee-vouchers/__ID__/payments'),
        ])
    @endif
@endsection

@push('scripts')
<script>
(function () {
    const idInput = document.getElementById('student_code');
    if (idInput) {
        let timer = null;
        const pattern = /^PGS-\d{5}$/i;
        const triggerSearch = () => {
            if (pattern.test(idInput.value.trim())) {
                idInput.form?.requestSubmit();
            }
        };
        idInput.addEventListener('input', () => {
            if (timer) clearTimeout(timer);
            timer = setTimeout(triggerSearch, 500);
        });
    }

    const quickCreate = document.getElementById('quick-create-challan');
    const genForm = document.getElementById('fee-challan-generate-form');
    if (quickCreate && genForm) {
        quickCreate.addEventListener('click', () => genForm.requestSubmit());
    }
    if (genForm) {
        genForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const quickBtn = document.getElementById('quick-create-challan');
            if (quickBtn) quickBtn.disabled = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(genForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    body: new FormData(genForm),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    let msg = data.message || 'Could not create challan.';
                    if (data.errors && typeof data.errors === 'object') {
                        const first = Object.values(data.errors).flat()[0];
                        if (first) msg = first;
                    }
                    alert(msg);
                    if (quickBtn) quickBtn.disabled = false;
                    return;
                }
                if (data.print_url) {
                    window.open(data.print_url, '_blank', 'noopener,noreferrer');
                }
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } catch {
                alert('Network error. Please try again.');
                if (quickBtn) quickBtn.disabled = false;
            }
        });
    }
})();
</script>
@endpush

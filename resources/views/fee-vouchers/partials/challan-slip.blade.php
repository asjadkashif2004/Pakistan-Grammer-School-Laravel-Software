{{--
  Fee challan — browser print (A4-friendly) and DomPDF ($forPdf).
  Logo: screen/print only when !$forPdf (see print.blade); PDF uses text header.
--}}
@php
    $s = $voucher->student;
    $lines = $lines ?? \App\Support\FeeChallanPresenter::feeLines($voucher);
    $monthLabel = optional($voucher->billing_month)->format('F Y') ?? '—';
    $studentId = $s?->student_code ?? '—';
    $fullName = $s?->full_name ?? '—';
    $father = $s?->father_name ?? '—';
    $classSection = trim(implode(' ', array_filter([$s?->class_name, $s?->section])));
    $classSection = $classSection !== '' ? $classSection : '—';
    $challanDate = \App\Support\FeeChallanPresenter::challanDateLabel($voucher);
    $fontStack = $forPdf ? 'DejaVu Sans, sans-serif' : 'Georgia, "Times New Roman", serif';
    $sans = $forPdf ? 'DejaVu Sans, sans-serif' : 'Figtree, system-ui, sans-serif';
    $logoDiskPath = public_path('images/logo.png');
    $hasLogo = ! $forPdf && is_string($logoDiskPath) && $logoDiskPath !== '' && file_exists($logoDiskPath);
    $logoUrl = $hasLogo ? asset('images/logo.png') : null;
@endphp
<style>
    .challan-doc {
        font-family: {{ $fontStack }};
        color: #111;
        background: #fff;
        box-sizing: border-box;
        margin: 0 auto;
        max-width: {{ $forPdf ? '100%' : 'min(720px, 100%)' }};
        padding: {{ $forPdf ? '8px 10px 12px' : '18px 22px 22px' }};
    }
    .challan-top {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: {{ $forPdf ? '10px' : '16px' }};
        padding-bottom: {{ $forPdf ? '8px' : '14px' }};
        border-bottom: 2px solid #111;
    }
    .challan-top-logo,
    .challan-top-brand {
        display: table-cell;
        vertical-align: middle;
    }
    .challan-top-logo {
        width: 1%;
        white-space: nowrap;
        padding-right: 14px;
    }
    .challan-top-logo img {
        display: block;
        max-height: {{ $forPdf ? '0' : '56px' }};
        max-width: {{ $forPdf ? '0' : '150px' }};
        width: auto;
        height: auto;
        object-fit: contain;
    }
    .challan-top-brand {
        text-align: {{ $forPdf ? 'center' : 'left' }};
    }
    .challan-school {
        font-family: {{ $sans }};
        font-weight: 900;
        font-size: {{ $forPdf ? '15px' : 'clamp(17px, 2.1vw, 22px)' }};
        letter-spacing: 0.04em;
        line-height: 1.2;
        color: #0a1f0d;
        margin: 0 0 4px;
    }
    .challan-place {
        font-family: {{ $sans }};
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.14em;
        color: #333;
        margin: 0;
    }
    .challan-meta-grid {
        display: table;
        width: 100%;
        margin-bottom: {{ $forPdf ? '10px' : '14px' }};
        font-family: {{ $sans }};
        font-size: {{ $forPdf ? '11px' : '12px' }};
    }
    .challan-meta-grid .row {
        display: table-row;
    }
    .challan-meta-grid .cell {
        display: table-cell;
        padding: 5px 8px 5px 0;
        vertical-align: bottom;
    }
    .challan-meta-grid .cell.r { text-align: right; padding-right: 0; padding-left: 12px; white-space: nowrap; }
    .challan-meta-grid .lbl {
        font-style: italic;
        color: #333;
    }
    .challan-meta-grid .u {
        border-bottom: 1.5px solid #111;
        font-weight: 700;
        min-width: 120px;
        padding: 2px 4px 1px;
        display: inline-block;
    }
    .challan-lines {
        margin-top: {{ $forPdf ? '8px' : '12px' }};
    }
    .challan-line {
        display: table;
        width: 100%;
        margin: 0 0 {{ $forPdf ? '5px' : '7px' }};
        font-size: {{ $forPdf ? '11px' : '12px' }};
    }
    .challan-line .lbl {
        display: table-cell;
        font-style: italic;
        font-family: {{ $sans }};
        white-space: nowrap;
        padding-right: 10px;
        vertical-align: bottom;
        width: 130px;
        color: #333;
    }
    .challan-line .val {
        display: table-cell;
        border-bottom: 1.5px solid #111;
        min-height: 20px;
        font-weight: 700;
        font-family: {{ $sans }};
        vertical-align: bottom;
        padding: 2px 6px 3px;
    }
    .challan-fee-grid {
        margin-top: {{ $forPdf ? '12px' : '18px' }};
        border: 2px solid #111;
        border-radius: {{ $forPdf ? '0' : '18px' }};
        overflow: hidden;
        font-family: {{ $sans }};
    }
    .challan-fee-head {
        display: table;
        width: 100%;
        border-bottom: 2px solid #111;
        font-weight: 800;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: #f6faf6;
    }
    .challan-fee-head .c1, .challan-fee-head .c2 {
        display: table-cell;
        padding: 10px 14px;
    }
    .challan-fee-head .c2 {
        width: 120px;
        text-align: right;
        border-left: 2px solid #111;
    }
    .challan-fee-pill {
        display: table;
        width: 100%;
        border-bottom: 2px solid #111;
        font-size: {{ $forPdf ? '11px' : '12px' }};
    }
    .challan-fee-pill:last-child { border-bottom: none; }
    .challan-fee-pill .c1, .challan-fee-pill .c2 {
        display: table-cell;
        padding: 9px 14px;
        vertical-align: middle;
    }
    .challan-fee-pill .c2 {
        width: 120px;
        text-align: right;
        font-weight: 700;
        border-left: 2px solid #111;
    }
    .challan-fee-pill.total .c1, .challan-fee-pill.total .c2 {
        font-weight: 900;
        font-size: {{ $forPdf ? '12px' : '13px' }};
        background: #fafdfb;
    }
    .challan-foot {
        margin-top: {{ $forPdf ? '12px' : '18px' }};
        font-size: 10px;
        font-family: {{ $sans }};
        color: #222;
        border-top: 1px dashed #444;
        padding-top: 10px;
    }
    .challan-foot table { width: 100%; border-collapse: collapse; }
    .challan-foot td { padding: 3px 0; vertical-align: top; }
    .challan-foot .ra { text-align: right; font-weight: 700; }
    @media print {
        .challan-doc {
            max-width: none;
            width: 100%;
            padding: 0;
        }
    }
</style>
<div class="challan-doc">
    <div class="challan-top">
        @if ($hasLogo && $logoUrl)
            <div class="challan-top-logo">
                <img src="{{ $logoUrl }}" width="150" height="56" alt="School logo">
            </div>
        @endif
        <div class="challan-top-brand" style="{{ $hasLogo ? '' : 'text-align:center;width:100%;' }}">
            @if ($forPdf)
                <div class="challan-school" style="text-align:center;">PAKISTAN GRAMMAR SCHOOL&nbsp;®</div>
                <p class="challan-place" style="text-align:center;">QUETTA</p>
            @else
                <h1 class="challan-school">PAKISTAN GRAMMAR SCHOOL&nbsp;®</h1>
                <p class="challan-place">QUETTA</p>
            @endif
        </div>
    </div>

    <div class="challan-meta-grid">
        <div class="row">
            <div class="cell">
                <span class="lbl">Billing month</span><br>
                <span class="u">{{ $monthLabel }}</span>
            </div>
            <div class="cell r">
                <span class="lbl">Student ID</span><br>
                <span class="u">{{ $studentId }}</span>
            </div>
        </div>
    </div>

    <div class="challan-lines">
        <div class="challan-line">
            <span class="lbl">Name</span>
            <span class="val">{{ $fullName }}</span>
        </div>
        <div class="challan-line">
            <span class="lbl">Father / Guardian</span>
            <span class="val">{{ $father }}</span>
        </div>
        <div class="challan-line">
            <span class="lbl">Challan date</span>
            <span class="val">{{ $challanDate }}</span>
        </div>
        <div class="challan-line">
            <span class="lbl">Class &amp; section</span>
            <span class="val">{{ $classSection }}</span>
        </div>
    </div>

    <div class="challan-fee-grid">
        <div class="challan-fee-head">
            <div class="c1">Description</div>
            <div class="c2">Amount (Rs)</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Admission fee</div>
            <div class="c2">{{ number_format($lines['admission'], 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Annual fund</div>
            <div class="c2">{{ number_format($lines['annual'], 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Tuition fee</div>
            <div class="c2">{{ number_format($lines['tuition'], 0) }}</div>
        </div>
        @if ((float) ($lines['other'] ?? 0) > 0.009)
        <div class="challan-fee-pill">
            <div class="c1">Other / fine</div>
            <div class="c2">{{ number_format($lines['other'], 0) }}</div>
        </div>
        @endif
        <div class="challan-fee-pill">
            <div class="c1">Previous / arrears</div>
            <div class="c2">{{ number_format($lines['previous'], 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Arrears</div>
            <div class="c2">{{ number_format((float) ($lines['arrears'] ?? 0), 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Fine (Rs 100 / day after due)</div>
            <div class="c2">{{ number_format((float) ($lines['fine'] ?? 0), 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Discount ({{ number_format((float) ($lines['discount_percentage'] ?? 0), 0) }}%)</div>
            <div class="c2">-{{ number_format((float) ($lines['discount_amount'] ?? 0), 0) }}</div>
        </div>
        <div class="challan-fee-pill">
            <div class="c1">Defaulter status</div>
            <div class="c2">{{ $lines['defaulter_status'] ?? 'No' }}</div>
        </div>
        <div class="challan-fee-pill total">
            <div class="c1">Final payable</div>
            <div class="c2">{{ number_format((float) ($lines['final_payable'] ?? $lines['total'] ?? 0), 0) }}</div>
        </div>
    </div>

    <div class="challan-foot">
        <table>
            <tr>
                <td>Voucher</td>
                <td class="ra">{{ $voucher->voucher_number ?? '—' }}</td>
            </tr>
            <tr>
                <td>Due date</td>
                <td class="ra">{{ optional($voucher->due_date)->format('d-m-Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td class="ra">{{ $voucher->status }}</td>
            </tr>
            <tr>
                <td>Received</td>
                <td class="ra">{{ number_format((float) $totalPaid, 0) }}</td>
            </tr>
            <tr>
                <td>Remaining</td>
                <td class="ra">{{ number_format((float) $remaining, 0) }}</td>
            </tr>
        </table>
        @if ($voucher->payments->isNotEmpty())
            <div style="margin-top:8px;font-weight:800;font-size:10px;">Payment history</div>
            <table style="margin-top:6px;">
                @foreach ($voucher->payments->sortBy('paid_at') as $p)
                    <tr>
                        <td>{{ optional($p->paid_at)->format('d-m-Y') }}</td>
                        <td class="ra">{{ number_format((float) $p->amount, 0) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>

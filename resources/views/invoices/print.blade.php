<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $invoice->invoice_number }}</title>
    <style>
        :root {
            --paper: 72mm;
            --ink: #111;
            --muted: #444;
            --dash: #bbb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 10px;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            background: #e8e8e8;
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .toolbar {
            max-width: var(--paper);
            margin: 0 auto 8px;
            display: flex;
            gap: 6px;
        }
        .toolbar button {
            flex: 1;
            border: 1px solid #333;
            border-radius: 6px;
            background: #fff;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }
        .receipt {
            max-width: var(--paper);
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 0 0 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .receipt-logo {
            text-align: center;
            padding: 10px 8px 6px;
        }
        .receipt-logo img {
            display: block;
            margin: 0 auto;
            max-height: 44px;
            max-width: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .banner {
            background: #111;
            color: #fff;
            text-align: center;
            font-weight: 900;
            font-size: 12px;
            letter-spacing: 0.12em;
            padding: 8px 6px;
            margin: 0;
        }
        .meta {
            padding: 8px 10px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
            margin: 4px 0;
            font-size: 11px;
        }
        .meta-row span:first-child {
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.04em;
        }
        .meta-row strong {
            font-weight: 800;
            text-align: right;
        }
        .hr {
            border: 0;
            border-top: 1px dashed var(--dash);
            margin: 8px 10px;
        }
        .hr-thick {
            border-top: 2px dashed #333;
            margin: 10px 10px;
        }
        .section-label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: var(--muted);
            margin: 0 10px 4px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 10px;
        }
        .items thead th {
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 5px 10px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .items th:nth-child(1), .items td:nth-child(1) { text-align: left; }
        .items th:nth-child(2), .items td:nth-child(2),
        .items th:nth-child(3), .items td:nth-child(3),
        .items th:nth-child(4), .items td:nth-child(4) {
            text-align: right;
            white-space: nowrap;
        }
        .items tbody td {
            padding: 5px 10px;
            vertical-align: top;
            border-bottom: 1px dotted #ddd;
        }
        .items tbody tr:last-child td { border-bottom: none; }
        .totals {
            padding: 6px 10px 0;
        }
        .tot-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 11px;
        }
        .tot-row.grand {
            font-size: 13px;
            font-weight: 900;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #111;
        }
        .footer {
            padding: 8px 10px 0;
            text-align: center;
            font-size: 10px;
            color: var(--muted);
        }
        .footer strong { color: #111; }
        .footer .line { margin: 6px 0; padding-top: 6px; border-top: 1px dotted var(--dash); }
        @media print {
            @page { size: 80mm auto; margin: 4mm; }
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .receipt { border: 0; box-shadow: none; max-width: none; width: 72mm; }
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $hasLogo = is_string($logoPath) && $logoPath !== '' && file_exists($logoPath);
        $at = $invoice->created_at ?? $invoice->updated_at ?? now();
        $paidAmount = $invoice->status === 'Paid' ? (float) $invoice->total_amount : 0.0;
        $balanceDue = max(0, round((float) $invoice->total_amount - $paidAmount, 2));
    @endphp
    @if (! $isDownload)
        <div class="toolbar">
            <button type="button" onclick="window.print()">Print</button>
            <button type="button" onclick="window.close()">Close</button>
        </div>
    @endif

    <article class="receipt">
        @if ($hasLogo)
            <div class="receipt-logo">
                <img src="{{ asset('images/logo.png') }}" width="120" height="44" alt="">
            </div>
        @endif

        <p class="banner">SALES RECEIPT</p>

        <div class="meta">
            <div class="meta-row"><span>Receipt #</span><strong>{{ $invoice->invoice_number }}</strong></div>
            <div class="meta-row"><span>Date</span><strong>{{ optional($invoice->invoice_date)->format('d F Y') ?? '—' }}</strong></div>
            <div class="meta-row"><span>Time</span><strong>{{ $at->format('g:i a') }}</strong></div>
        </div>

        <hr class="hr">

        <p class="section-label">Student</p>
        <div class="meta">
            @if ($invoice->student)
                <div class="meta-row"><span>Student ID</span><strong>{{ $invoice->student->student_code }}</strong></div>
                <div class="meta-row"><span>Name</span><strong>{{ $invoice->student->full_name }}</strong></div>
                @if ($invoice->student->class_name || $invoice->student->section)
                    <div class="meta-row"><span>Class</span><strong>{{ trim(($invoice->student->class_name ?? '') . ' ' . ($invoice->student->section ?? '')) ?: '—' }}</strong></div>
                @endif
            @else
                <div class="meta-row"><span>Student ID</span><strong>—</strong></div>
                <div class="meta-row"><span>Customer</span><strong>{{ $invoice->customer_name ?: '—' }}</strong></div>
            @endif
        </div>

        <hr class="hr">

        <p class="section-label" style="margin-top:4px;">Items</p>
        <table class="items">
            <thead>
                <tr>
                    <th>Desc</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amt</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'Item' }}</td>
                        <td>{{ (int) $item->quantity }}</td>
                        <td>Rs {{ number_format((float) $item->unit_price, 0) }}</td>
                        <td>Rs {{ number_format((float) $item->line_total, 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;padding:8px;">No line items</td></tr>
                @endforelse
            </tbody>
        </table>

        <hr class="hr-thick">

        <div class="totals">
            <div class="tot-row"><span>Subtotal</span><span>Rs {{ number_format((float) $invoice->subtotal, 0) }}</span></div>
            <div class="tot-row"><span>Discount</span><span>- Rs {{ number_format((float) $invoice->discount, 0) }}</span></div>
            <div class="tot-row grand"><span>TOTAL</span><span>Rs {{ number_format((float) $invoice->total_amount, 0) }}</span></div>
            <div class="tot-row"><span>Paid</span><span>Rs {{ number_format($paidAmount, 0) }}</span></div>
            <div class="tot-row"><span>Remaining</span><span>Rs {{ number_format($balanceDue, 0) }}</span></div>
            <div class="tot-row" style="margin-top:8px;padding-top:8px;border-top:1px dotted #bbb;font-size:10px;">
                <span>Payment</span><span><strong>Cash</strong></span>
            </div>
        </div>

        @if ($invoice->notes)
            <hr class="hr">
            <div class="meta" style="font-size:10px;color:#555;"><strong>Note:</strong> {{ $invoice->notes }}</div>
        @endif

        <hr class="hr-thick">

        <div class="footer">
            <div><strong>Thank you for your purchase.</strong></div>
            <div class="line" style="font-size:9px;">No return or exchange without receipt.</div>
            <div style="font-size:9px;">No return or exchange after 3 days.</div>
            <div class="line" style="font-size:10px;">Software developed by: <strong>AddsMint.com</strong></div>
            <div style="font-size:9px;margin-top:6px;">Printed: {{ now()->format('d/m/Y') }}</div>
        </div>
    </article>
</body>
</html>

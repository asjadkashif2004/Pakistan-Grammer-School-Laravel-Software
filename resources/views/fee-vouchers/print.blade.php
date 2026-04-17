<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher {{ $voucher->voucher_number }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f6faf6;
            color: #1e3120;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f7a35;
            border-radius: 12px;
            overflow: hidden;
        }

        .head {
            background: #0f7a35;
            color: #ffffff;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 10px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 6px;
            background: #ffffff;
            padding: 2px;
        }

        .head h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.1;
        }

        .head p {
            margin: 2px 0 0;
            font-size: 13px;
            opacity: 0.95;
        }

        .voucher-no {
            text-align: right;
        }

        .voucher-no small {
            font-size: 12px;
            opacity: 0.9;
        }

        .voucher-no strong {
            display: block;
            font-size: 34px;
            line-height: 1;
        }

        .body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            padding: 14px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #d6e8d6;
            padding: 7px 0;
            font-size: 15px;
        }

        .total {
            border-top: 1px solid #d6e8d6;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total strong {
            color: #0f7a35;
            font-size: 44px;
            line-height: 1;
        }

        .notes {
            padding: 0 14px 14px;
            color: #5d7760;
            font-size: 14px;
        }

        .toolbar {
            max-width: 900px;
            margin: 0 auto 10px;
            display: flex;
            gap: 8px;
        }

        .toolbar button {
            border: 1px solid #d4ead4;
            border-radius: 9px;
            background: #ffffff;
            padding: 8px 12px;
            font-weight: 700;
            cursor: pointer;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .page {
                border-radius: 0;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    @if (! $isDownload)
        <div class="toolbar">
            <button onclick="window.print()">Print Voucher</button>
            <button onclick="window.close()">Close</button>
        </div>
    @endif

    <article class="page">
        <header class="head">
            <div class="brand">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s" alt="PGS logo">
                <div>
                    <h1>Pakistan Grammar School</h1>
                    <p>Zarghoon Road, Quetta, Balochistan</p>
                </div>
            </div>
            <div class="voucher-no">
                <small>Voucher #</small>
                <strong>{{ $voucher->voucher_number }}</strong>
                <small>{{ optional($voucher->billing_month)->format('F Y') }}</small>
            </div>
        </header>

        <section class="body">
            <div>
                <div class="row"><span>Student</span><strong>{{ $voucher->student?->full_name ?? 'N/A' }}</strong></div>
                <div class="row"><span>Roll #</span><strong>{{ $voucher->student?->student_code ?? 'N/A' }}</strong></div>
                <div class="row"><span>Class</span><strong>{{ $voucher->student?->class_name }} - {{ $voucher->student?->section }}</strong></div>
                <div class="row"><span>Father</span><strong>{{ $voucher->student?->father_name ?? 'N/A' }}</strong></div>
            </div>
            <div>
                <div class="row"><span>Monthly Fee</span><strong>Rs {{ number_format((float) ($voucher->student?->monthly_fee ?? 0), 0) }}</strong></div>
                <div class="row"><span>Arrears</span><strong>Rs {{ number_format((float) $voucher->arrears, 0) }}</strong></div>
                <div class="row"><span>Fine</span><strong>Rs {{ number_format((float) $voucher->fine, 0) }}</strong></div>
                <div class="row"><span>Discount</span><strong>- Rs {{ number_format((float) $voucher->discount, 0) }}</strong></div>
            </div>
        </section>

        <footer class="total">
            <div>
                <div style="font-size: 22px; font-weight: 800;">Total Payable</div>
                <div style="font-size: 14px; color: #5d7760;">Due by {{ optional($voucher->due_date)->format('d M Y') }}</div>
                <div style="font-size: 14px; color: #5d7760;">Status: {{ $voucher->status }}</div>
            </div>
            <strong>Rs {{ number_format((float) $voucher->amount, 0) }}</strong>
        </footer>

        @if ($voucher->notes)
            <div class="notes">
                <strong>Notes:</strong> {{ $voucher->notes }}
            </div>
        @endif
    </article>
</body>
</html>

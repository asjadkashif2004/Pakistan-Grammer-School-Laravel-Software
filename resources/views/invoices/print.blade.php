<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        :root {
            --paper-width: 80mm;
            --ink: #111;
            --muted: #555;
            --line: #d0d0d0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 10px;
            font-family: "Courier New", Consolas, monospace;
            background: #f5f5f5;
            color: var(--ink);
        }

        .toolbar {
            width: var(--paper-width);
            margin: 0 auto 8px;
            display: flex;
            gap: 6px;
        }

        .toolbar button {
            flex: 1;
            border: 1px solid #cfd8cf;
            border-radius: 8px;
            background: #fff;
            padding: 7px 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .receipt {
            width: var(--paper-width);
            margin: 0 auto;
            background: #fff;
            border: 1px solid #dcdcdc;
            padding: 10px 9px 12px;
        }

        .center {
            text-align: center;
        }

        .title {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.2px;
        }

        .subtitle {
            font-size: 11px;
            color: var(--muted);
            margin: 2px 0;
        }

        .brand {
            font-size: 12px;
            font-weight: 800;
            margin: 2px 0 0;
        }

        .sep {
            border-top: 1px dashed var(--line);
            margin: 8px 0;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 11px;
            margin: 3px 0;
        }

        .items th,
        .items td {
            font-size: 11px;
            padding: 3px 0;
            text-align: left;
            vertical-align: top;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items th:nth-child(2),
        .items td:nth-child(2),
        .items th:nth-child(3),
        .items td:nth-child(3),
        .items th:nth-child(4),
        .items td:nth-child(4) {
            text-align: right;
            white-space: nowrap;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            margin: 4px 0;
        }

        .grand {
            font-weight: 700;
            font-size: 13px;
        }

        .notes {
            font-size: 11px;
            color: var(--muted);
            white-space: pre-wrap;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                border: 0;
                width: 80mm;
                padding: 8px 6px 10px;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @if (! $isDownload)
        <div class="toolbar">
            <button onclick="window.print()">Print</button>
            <button onclick="window.close()">Close</button>
        </div>
    @endif

    <section class="receipt">
        <div class="center">
            <p class="title">Pakistan Grammar School</p>
            <p class="brand">Addsmint.com</p>
            <p class="subtitle">Sales Receipt</p>
            <p class="subtitle">Invoice # {{ $invoice->invoice_number }}</p>
        </div>

        <div class="sep"></div>

        <div class="meta-row"><span>Customer</span><strong>{{ $invoice->customer_name }}</strong></div>
        <div class="meta-row"><span>Contact</span><strong>{{ $invoice->customer_contact ?: '-' }}</strong></div>
        <div class="meta-row"><span>Date</span><strong>{{ optional($invoice->invoice_date)->format('d-m-Y') }}</strong></div>
        <div class="meta-row"><span>Status</span><strong>{{ $invoice->status }}</strong></div>

        <div class="sep"></div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'N/A' }}</td>
                        <td>{{ number_format((float) $item->quantity, 2) }}</td>
                        <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td>{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="sep"></div>

        <div class="total-row"><span>SubTotal:</span><strong>{{ number_format((float) $invoice->subtotal, 2) }}</strong></div>
        <div class="total-row"><span>Discount:</span><strong>- {{ number_format((float) $invoice->discount, 2) }}</strong></div>
        <div class="total-row grand"><span>Total:</span><strong>{{ number_format((float) $invoice->total_amount, 2) }}</strong></div>

        @if ($invoice->notes)
            <div class="sep"></div>
            <div class="notes"><strong>Notes:</strong> {{ $invoice->notes }}</div>
        @endif
    </section>

    @if (! $isDownload)
        <script>
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 120);
            });
        </script>
    @endif
</body>
</html>


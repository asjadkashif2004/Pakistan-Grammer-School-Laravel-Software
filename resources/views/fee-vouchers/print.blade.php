<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee challan {{ $voucher->voucher_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 16px;
            background: #e4e8e4;
            font-family: Figtree, system-ui, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .toolbar {
            max-width: 210mm;
            margin: 0 auto 14px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .toolbar button {
            border: 1px solid #2a5a32;
            border-radius: 10px;
            background: #fff;
            padding: 10px 18px;
            font-weight: 800;
            cursor: pointer;
            color: #0f3a16;
        }
        .slip-wrap {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            overflow: hidden;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .slip-wrap {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
                width: 100%;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 14mm;
            }
        }
    </style>
</head>
<body>
    @if (! $isDownload)
        <div class="toolbar">
            <button type="button" onclick="window.print()">Print</button>
            <button type="button" onclick="window.close()">Close</button>
        </div>
    @endif
    <div class="slip-wrap">
        @include('fee-vouchers.partials.challan-slip', [
            'voucher' => $voucher,
            'lines' => $lines,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining,
            'forPdf' => false,
        ])
    </div>
    @if (! $isDownload)
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 400);
            });
        </script>
    @endif
</body>
</html>

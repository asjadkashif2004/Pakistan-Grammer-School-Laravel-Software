<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory Print</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f6faf6;
            color: #1e3120;
        }

        .sheet {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d7e9d7;
            border-radius: 12px;
            overflow: hidden;
        }

        .head {
            padding: 14px;
            background: #0f7a35;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .head h1 {
            margin: 0;
            font-size: 26px;
        }

        .head p {
            margin: 3px 0 0;
            font-size: 12px;
            opacity: 0.95;
        }

        .table-wrap {
            padding: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 10px 8px;
            border-top: 1px solid #e6f2e6;
            font-size: 14px;
        }

        th {
            border-top: none;
            color: #557158;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
        }

        .toolbar {
            max-width: 980px;
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
                padding: 0;
                background: #ffffff;
            }

            .toolbar {
                display: none;
            }

            .sheet {
                border-radius: 0;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Inventory</button>
        <button onclick="window.close()">Close</button>
    </div>

    <section class="sheet">
        <header class="head">
            <div>
                <h1>Pakistan Grammar School - Product Inventory</h1>
                <p>Printed on {{ $printedAt->format('d M Y h:i A') }}</p>
            </div>
            <strong>Total Products: {{ $products->count() }}</strong>
        </header>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Sale Price</th>
                        <th>Stock Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>Rs {{ number_format((float) ($product->sale_price ?? $product->unit_price), 0) }}</td>
                            <td>{{ $product->stock_qty }}</td>
                            <td>{{ $product->stock_status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No products available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>

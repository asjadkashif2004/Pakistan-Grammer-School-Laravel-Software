<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Challan {{ $voucher->voucher_number }}</title>
    <style>
        body { margin: 0; padding: 0; background: #fff; }
    </style>
</head>
<body>
    @include('fee-vouchers.partials.challan-slip', [
        'voucher' => $voucher,
        'lines' => $lines,
        'totalPaid' => $totalPaid,
        'remaining' => $remaining,
        'forPdf' => true,
    ])
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admission Form {{ $student->student_code }}</title>
    <link rel="icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">
    <style>
        :root {
            --ink: #1a1a1a;
            --muted: #59675b;
            --line: #cfdccc;
            --brand: #145d26;
            --accent: #ce1b26;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 14px;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background: #fff;
            font-size: 12px;
            line-height: 1.35;
        }
        .tools {
            max-width: 780px;
            margin: 0 auto 10px;
            display: flex;
            gap: 8px;
        }
        .tools button {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            padding: 8px 11px;
            font-weight: 700;
            cursor: pointer;
        }
        .sheet {
            max-width: 780px;
            margin: 0 auto;
            border: 1.5px solid #114f21;
            padding: 12px;
        }
        .head {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: start;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 8px;
        }
        .title {
            margin: 0;
            color: var(--brand);
            font-size: 28px;
            line-height: 1.05;
            font-weight: 900;
        }
        .subtitle {
            margin: 2px 0 0;
            font-size: 18px;
            font-weight: 800;
        }
        .meta {
            font-size: 11px;
            margin-top: 5px;
            color: var(--muted);
            font-weight: 700;
        }
        .head-right {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .logo {
            width: 62px;
            height: 62px;
            object-fit: contain;
        }
        .photo-box {
            width: 96px;
            height: 110px;
            border: 1.5px solid #114f21;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #114f21;
            overflow: hidden;
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .sec { margin-top: 10px; }
        .sec h4 {
            margin: 0 0 6px;
            font-size: 13px;
            color: var(--accent);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .kv {
            width: 100%;
            border-collapse: collapse;
        }
        .kv td {
            border: 1px solid var(--line);
            padding: 5px 7px;
            vertical-align: top;
        }
        .kv td:first-child {
            width: 34%;
            font-weight: 700;
            color: #334c36;
            background: #f7faf6;
        }
        .grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .fee-table th,
        .fee-table td {
            border: 1px solid var(--line);
            padding: 6px 8px;
        }
        .fee-table th {
            background: #f4f8f2;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-size: 10.5px;
            text-align: left;
        }
        .fee-table td:last-child,
        .fee-table th:last-child { text-align: right; }
        .fee-table tr.total td {
            font-weight: 800;
            background: #f9fcf8;
        }
        .checks {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px;
            margin-top: 6px;
        }
        .check {
            border: 1px solid var(--line);
            padding: 7px;
            font-size: 11px;
            font-weight: 700;
        }
        .signatures {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .sign-box {
            border-top: 1px solid #7d8f7f;
            padding-top: 4px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #344e37;
            min-height: 34px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            color: #3b4f3d;
            border-top: 1px solid #bbb;
            padding-top: 6px;
        }
        @media (max-width: 760px) {
            .grid2 { grid-template-columns: 1fr; }
            .checks { grid-template-columns: 1fr 1fr; }
            .signatures { grid-template-columns: 1fr; gap: 10px; }
        }
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { padding: 0; font-size: 11px; }
            .tools { display: none; }
            .sheet { border: 0; max-width: 100%; padding: 0; }
            .sec { break-inside: avoid; }
        }
    </style>
</head>
<body>
    @php
        $tuitionFee = (float) ($student->monthly_fee ?? 0);
        $admissionFee = (float) ($student->admission_fee ?? 0);
        $examFee = (float) ($student->exam_fee ?? 0);
        $transportFee = (float) ($student->transport_fee ?? 0);
        $totalFee = $tuitionFee + $admissionFee + $examFee + $transportFee;
        $discountPercentage = (float) ($student->sibling_discount_percentage ?? 0);
        $discountAmount = (float) ($student->sibling_discount_amount ?? 0);
        $finalPayable = (float) ($student->final_payable ?? 0);
        if ($finalPayable <= 0.009) {
            $finalPayable = max(0, $totalFee - $discountAmount);
        }
    @endphp

    <div class="tools">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <section class="sheet">
        <header class="head">
            <div>
                <p class="title">Pakistan Grammar School</p>
                <p class="subtitle">Admission Form</p>
                <p class="meta">
                    Form #: {{ $student->form_number ?: '-' }} |
                    Student ID: {{ $student->student_code }} |
                    Generated: {{ now()->format('d M Y h:i A') }}
                </p>
            </div>
            <div class="head-right">
                <img class="logo" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s" alt="logo">
                <div class="photo-box">
                    @if ($student->student_photo_path)
                        <img src="{{ asset('storage/'.$student->student_photo_path) }}" alt="student photo">
                    @else
                        Photo
                    @endif
                </div>
            </div>
        </header>

        <section class="sec">
            <h4>Student Information</h4>
            <div class="grid2">
                <table class="kv">
                    <tr><td>Name</td><td>{{ $student->full_name }}</td></tr>
                    <tr><td>Date of Birth</td><td>{{ optional($student->date_of_birth)->format('d M Y') ?: '-' }}</td></tr>
                    <tr><td>Gender</td><td>{{ $student->gender ?: '-' }}</td></tr>
                    <tr><td>Admission Date</td><td>{{ optional($student->admission_date)->format('d M Y') ?: '-' }}</td></tr>
                </table>
                <table class="kv">
                    <tr><td>Class</td><td>{{ $student->class_name ?: '-' }}</td></tr>
                    <tr><td>Section</td><td>{{ $student->section ?: '-' }}</td></tr>
                    <tr><td>Session</td><td>{{ $student->session_label ?: '-' }}</td></tr>
                    <tr><td>Status</td><td>{{ $student->status ?: '-' }}</td></tr>
                </table>
            </div>
        </section>

        <section class="sec">
            <h4>Guardian & Contact</h4>
            <table class="kv">
                <tr><td>Father Name</td><td>{{ $student->father_name ?: '-' }}</td></tr>
                <tr><td>Father/Guardian Occupation</td><td>{{ $student->father_occupation ?: '-' }}</td></tr>
                <tr><td>Guardian Name</td><td>{{ $student->guardian_name ?: '-' }}</td></tr>
                <tr><td>Father/Guardian CNIC</td><td>{{ $student->father_cnic ?: '-' }}</td></tr>
                <tr><td>Contact Number</td><td>{{ $student->contact_number ?: '-' }}</td></tr>
                <tr><td>Emergency Contact</td><td>{{ $student->emergency_contact_number ?: '-' }}</td></tr>
                <tr><td>Address</td><td>{{ $student->address ?: '-' }}</td></tr>
            </table>
        </section>

        <section class="sec">
            <h4>Fee Breakdown</h4>
            <table class="fee-table">
                <thead>
                    <tr><th>Fee Head</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>Tuition Fee</td><td>Rs {{ number_format($tuitionFee, 2) }}</td></tr>
                    <tr><td>Admission Fee</td><td>Rs {{ number_format($admissionFee, 2) }}</td></tr>
                    <tr><td>Exam Fee</td><td>Rs {{ number_format($examFee, 2) }}</td></tr>
                    <tr><td>Transport Fee</td><td>Rs {{ number_format($transportFee, 2) }}</td></tr>
                    <tr><td>Discount ({{ number_format($discountPercentage, 0) }}%)</td><td>- Rs {{ number_format($discountAmount, 2) }}</td></tr>
                    <tr class="total"><td>Total Payable</td><td>Rs {{ number_format($finalPayable, 2) }}</td></tr>
                </tbody>
            </table>
        </section>

        <section class="sec">
            <h4>Academic / Office Use</h4>
            <table class="kv">
                <tr><td>Previous School</td><td>{{ $student->previous_school ?: '-' }}</td></tr>
                <tr><td>Last Attended Class</td><td>{{ $student->last_attended_class ?: '-' }}</td></tr>
            </table>
            <div class="checks">
                <div class="check">Birth certificate / B-Form: {{ $student->office_bform_submitted ? 'Yes' : 'No' }}</div>
                <div class="check">Father CNIC / Local: {{ $student->office_father_cnic_submitted ? 'Yes' : 'No' }}</div>
                <div class="check">Result cards: {{ $student->office_result_cards_submitted ? 'Yes' : 'No' }}</div>
                <div class="check">Consumable fee: {{ $student->office_consumable_fee_paid ? 'Yes' : 'No' }}</div>
                <div class="check">3 Passport size photos: {{ $student->office_photos_submitted ? 'Yes' : 'No' }}</div>
                <div class="check">Admission fee paid: {{ $student->office_admission_fee_paid ? 'Yes' : 'No' }}</div>
            </div>
        </section>

        <section class="signatures">
            <div class="sign-box">Parent / Guardian Signature</div>
            <div class="sign-box">Admission Officer Signature</div>
            <div class="sign-box">Principal Signature</div>
        </section>

        <div class="footer">Developed by: Addsmint.com</div>
    </section>
</body>
</html>

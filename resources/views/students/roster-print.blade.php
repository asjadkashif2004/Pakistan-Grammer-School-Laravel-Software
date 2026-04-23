<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Roster Print</title>
    <style>
        body { margin: 16px; font-family: Arial, sans-serif; color: #15261a; }
        .tools { margin-bottom: 8px; display: flex; gap: 8px; }
        .tools button { border: 1px solid #cad7ca; border-radius: 8px; background: #fff; padding: 8px 10px; font-weight: 700; cursor: pointer; }
        .sheet { border: 1px solid #ccdacc; padding: 12px; }
        .head { display: flex; justify-content: space-between; border-bottom: 2px solid #1b5f2d; padding-bottom: 8px; margin-bottom: 10px; }
        .head h1 { margin: 0; font-size: 22px; color: #155d25; }
        .head p { margin: 3px 0 0; font-size: 12px; color: #5f7661; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d8e5d8; padding: 7px 8px; font-size: 12px; text-align: left; vertical-align: top; }
        th { background: #f2f8f2; text-transform: uppercase; letter-spacing: .6px; font-size: 11px; }
        .footer { margin-top: 12px; text-align: center; font-size: 12px; font-weight: 700; color: #3b4f3d; }
        @media print { @page { size: A4 landscape; margin: 8mm; } body { margin: 0; } .tools { display: none; } .sheet { border: 0; padding: 0; } }
    </style>
</head>
<body>
    <div class="tools">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    <section class="sheet">
        <header class="head">
            <div>
                <h1>Student Roster</h1>
                <p>Pakistan Grammar School - Quetta ERP</p>
            </div>
            <div>
                <p><strong>Date:</strong> {{ now()->format('d M Y h:i A') }}</p>
                <p><strong>Filters:</strong> {{ $search ?: 'All' }} | {{ $classFilter ?: 'All Classes' }} | {{ $sectionFilter ?: 'All Sections' }}</p>
            </div>
        </header>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Class/Section</th>
                    <th>Contact</th>
                    <th>Emergency</th>
                    <th>Father</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->class_name }}-{{ $student->section }}</td>
                        <td>{{ $student->contact_number }}</td>
                        <td>{{ $student->emergency_contact_number ?: '-' }}</td>
                        <td>{{ $student->father_name }}</td>
                        <td>{{ $student->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No students found for selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">Developed by: Addsmint.com</div>
    </section>
</body>
</html>

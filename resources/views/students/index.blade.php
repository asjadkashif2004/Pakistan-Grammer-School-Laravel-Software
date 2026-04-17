@extends('layouts.school')

@section('title', 'Student Registration | Pakistan Grammar School')
@section('page_heading', 'Student Registration')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="#registration-form" class="action-chip primary" title="Register student" aria-label="Register student">➕ <span class="header-action-text">Register</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .student-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
            gap: 12px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 24px;
            color: #1f3f24;
            font-weight: 800;
        }

        .panel-body {
            padding: 14px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field.full {
            grid-column: 1 / -1;
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

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .error {
            color: #bd3434;
            font-size: 12px;
            font-weight: 600;
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

        .error-box ul {
            margin: 6px 0 0;
            padding-left: 16px;
        }

        .field-hint {
            font-size: 11px;
            color: #6f8570;
            margin-top: -2px;
        }

        .submit {
            margin-top: 8px;
            border: 0;
            border-radius: 10px;
            width: 100%;
            padding: 11px;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .form-actions {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .btn-neutral {
            border: 1px solid #d4ead4;
            border-radius: 10px;
            padding: 11px;
            background: #ffffff;
            color: #1f5d2a;
            font-weight: 700;
            cursor: pointer;
        }

        .roster-tools {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .roster-tools input {
            flex: 1;
            min-width: 180px;
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 9px 11px;
        }

        .roster-tools button {
            border: 1px solid #d4ead4;
            background: #ffffff;
            border-radius: 10px;
            padding: 9px 12px;
            font-weight: 700;
            color: #355538;
        }

        .roster-table {
            width: 100%;
            border-collapse: collapse;
        }

        .roster-table th,
        .roster-table td {
            text-align: left;
            padding: 9px 8px;
            border-top: 1px solid #e8f3e8;
            font-size: 14px;
        }

        .actions {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        .btn-link {
            border: 1px solid #d4ead4;
            border-radius: 7px;
            padding: 4px 7px;
            font-size: 12px;
            text-decoration: none;
            color: #1f5d2a;
            font-weight: 700;
            background: #ffffff;
        }

        .btn-link.danger {
            color: #9f3131;
            border-color: #ffd3d3;
            background: #fff7f7;
        }

        .roster-table th {
            color: #56735a;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
            border-top: none;
            padding-top: 0;
        }

        .avatar {
            display: inline-flex;
            width: 30px;
            height: 30px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #0f7a35;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            margin-right: 8px;
        }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .status.active {
            background: #ddf8e4;
            color: #0f7a35;
        }

        .status.inactive {
            background: #fff2da;
            color: #966113;
        }

        .status.suspended {
            background: #ffe3e3;
            color: #a93b3b;
        }

        @media (max-width: 1200px) {
            .student-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                grid-template-columns: 1fr;
            }

            .roster-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="student-grid">
        <section id="registration-form" class="panel">
            <header class="panel-head">Register New Student</header>
            <div class="panel-body">
                <form method="POST" action="{{ route('students.store') }}">
                    @csrf
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
                    <div class="field full">
                        <label>Student ID</label>
                        <input type="text" value="Auto generated on save" readonly>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                            @error('date_of_birth') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                @foreach (['Male', 'Female', 'Other'] as $gender)
                                    <option value="{{ $gender }}" @selected(old('gender', 'Female') === $gender)>{{ $gender }}</option>
                                @endforeach
                            </select>
                            @error('gender') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="class_name">Class / Grade</label>
                            <select id="class_name" name="class_name" required>
                                <option value="">Select Class</option>
                                @foreach ($classOptions as $classOption)
                                    <option value="{{ $classOption }}" @selected(old('class_name') === $classOption)>{{ $classOption }}</option>
                                @endforeach
                            </select>
                            @error('class_name') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="section">Section</label>
                            <input type="text" id="section" name="section" value="{{ old('section', 'A') }}" required>
                            @error('section') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field full">
                            <label for="father_name">Father's Name</label>
                            <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}" required>
                            @error('father_name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="father_cnic">Father's CNIC</label>
                            <input
                                type="text"
                                id="father_cnic"
                                name="father_cnic"
                                value="{{ old('father_cnic') }}"
                                placeholder="12345-1234567-1"
                                maxlength="15"
                                pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}"
                                required>
                            <span class="field-hint">Format: 12345-1234567-1</span>
                            @error('father_cnic') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="contact_number">Contact Number</label>
                            <input
                                type="text"
                                id="contact_number"
                                name="contact_number"
                                value="{{ old('contact_number') }}"
                                placeholder="03XX-XXXXXXX"
                                maxlength="12"
                                pattern="03[0-9]{2}-[0-9]{7}"
                                required>
                            <span class="field-hint">Pak format: 03XX-XXXXXXX</span>
                            @error('contact_number') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field full">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="monthly_fee">Monthly Fee</label>
                            <input
                                type="text"
                                inputmode="decimal"
                                id="monthly_fee"
                                name="monthly_fee"
                                value="{{ old('monthly_fee') }}"
                                placeholder="e.g. 5000"
                                pattern="\d+(\.\d{1,2})?"
                                required>
                            <span class="field-hint">Enter amount manually (numbers only).</span>
                            @error('monthly_fee') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="admission_fee">Admission Fee</label>
                            <input
                                type="text"
                                inputmode="decimal"
                                id="admission_fee"
                                name="admission_fee"
                                value="{{ old('admission_fee') }}"
                                placeholder="e.g. 15000"
                                pattern="\d+(\.\d{1,2})?"
                                required>
                            <span class="field-hint">One-time admission amount.</span>
                            @error('admission_fee') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="admission_date">Admission Date</label>
                            <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date', now()->toDateString()) }}" required>
                            @error('admission_date') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field full">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                @foreach (['Active', 'Inactive', 'Suspended'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', 'Active') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-actions">
                        <button id="printStudentForm" class="btn-neutral" type="button">Print Form</button>
                        <button class="submit" type="submit">Register Student</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">Student Roster</header>
            <div class="panel-body">
                <form method="GET" class="roster-tools">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search students...">
                    <button type="submit">Search</button>
                </form>

                <table class="roster-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_code ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="avatar">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    <div style="font-size: 12px; color: #69826b;">Father: {{ $student->father_name }}</div>
                                </td>
                                <td>{{ $student->class_name }}-{{ $student->section }}</td>
                                <td>Rs {{ number_format((float) $student->monthly_fee, 0) }}</td>
                                <td><span class="status {{ strtolower($student->status) }}">{{ $student->status }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('students.edit', $student) }}" class="btn-link">Edit</a>
                                        <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Delete this student record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-link danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="color: #6f8570;">No students found yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="list-pagination">
                    {{ $students->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const cnicInput = document.getElementById('father_cnic');
            const contactInput = document.getElementById('contact_number');
            const printButton = document.getElementById('printStudentForm');

            if (cnicInput) {
                cnicInput.addEventListener('input', () => {
                    const digits = cnicInput.value.replace(/\D/g, '').slice(0, 13);
                    const p1 = digits.slice(0, 5);
                    const p2 = digits.slice(5, 12);
                    const p3 = digits.slice(12, 13);
                    cnicInput.value = [p1, p2, p3].filter(Boolean).join('-');
                });
            }

            if (contactInput) {
                contactInput.addEventListener('input', () => {
                    const digits = contactInput.value.replace(/\D/g, '').slice(0, 11);
                    const p1 = digits.slice(0, 4);
                    const p2 = digits.slice(4, 11);
                    contactInput.value = [p1, p2].filter(Boolean).join('-');
                });
            }

            if (printButton) {
                printButton.addEventListener('click', () => {
                    const fields = [
                        { label: 'Student ID', value: 'Auto generated on save' },
                        { label: 'First Name', value: document.getElementById('first_name')?.value },
                        { label: 'Last Name', value: document.getElementById('last_name')?.value },
                        { label: 'Date of Birth', value: document.getElementById('date_of_birth')?.value },
                        { label: 'Gender', value: document.getElementById('gender')?.value },
                        { label: 'Class / Grade', value: document.getElementById('class_name')?.value },
                        { label: 'Section', value: document.getElementById('section')?.value },
                        { label: "Father's Name", value: document.getElementById('father_name')?.value },
                        { label: "Father's CNIC", value: document.getElementById('father_cnic')?.value },
                        { label: 'Contact Number', value: document.getElementById('contact_number')?.value },
                        { label: 'Address', value: document.getElementById('address')?.value },
                        { label: 'Monthly Fee', value: document.getElementById('monthly_fee')?.value },
                        { label: 'Admission Fee', value: document.getElementById('admission_fee')?.value },
                        { label: 'Admission Date', value: document.getElementById('admission_date')?.value },
                        { label: 'Status', value: document.getElementById('status')?.value },
                    ];

                    const rows = fields
                        .map(({ label, value }) => `
                            <tr>
                                <th>${label}</th>
                                <td>${(value && String(value).trim()) ? String(value).trim() : '-'}</td>
                            </tr>
                        `)
                        .join('');

                    const popup = window.open('', '_blank', 'width=900,height=700');
                    if (!popup) return;

                    popup.document.write(`
                        <!doctype html>
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <title>Student Registration Form Print</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 24px; color: #1f3f24; }
                                h1 { margin: 0 0 4px; font-size: 22px; }
                                p { margin: 0 0 16px; color: #56735a; font-size: 13px; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid #d4ead4; padding: 9px 10px; text-align: left; font-size: 13px; vertical-align: top; }
                                th { width: 35%; background: #f4faf3; }
                            </style>
                        </head>
                        <body>
                            <h1>Pakistan Grammar School</h1>
                            <p>Student Registration Details (Current Form Information)</p>
                            <table>${rows}</table>
                        </body>
                        </html>
                    `);
                    popup.document.close();
                    popup.focus();
                    popup.print();
                });
            }
        })();
    </script>
@endpush

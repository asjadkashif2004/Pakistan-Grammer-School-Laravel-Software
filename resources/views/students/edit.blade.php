@extends('layouts.school')

@section('title', 'Edit Student | Pakistan Grammar School')
@section('page_heading', 'Edit Student')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('students.index') }}" class="action-chip" title="Back to students" aria-label="Back to students">← <span class="header-action-text">Students</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .edit-card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .edit-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 22px;
            color: #1f3f24;
            font-weight: 800;
        }

        .edit-body {
            padding: 14px;
        }

        .field-grid {
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
            margin-top: 12px;
            border: 0;
            border-radius: 10px;
            width: 100%;
            padding: 11px;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 700px) {
            .field-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <section class="edit-card">
        <header class="edit-head">Edit Student - {{ $student->student_code }}</header>
        <div class="edit-body">
            <form method="POST" action="{{ route('students.update', $student) }}">
                @csrf
                @method('PUT')
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

                <div class="field-grid">
                    <div class="field full">
                        <label>Student ID</label>
                        <input type="text" value="{{ $student->student_code }}" readonly>
                    </div>
                    <div class="field">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                        @error('first_name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                        @error('last_name') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth)->toDateString()) }}" required>
                        @error('date_of_birth') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            @foreach (['Male', 'Female', 'Other'] as $gender)
                                <option value="{{ $gender }}" @selected(old('gender', $student->gender) === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                        @error('gender') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="class_name">Class / Grade</label>
                        <select id="class_name" name="class_name" required>
                            <option value="">Select Class</option>
                            @foreach ($classOptions as $classOption)
                                <option value="{{ $classOption }}" @selected(old('class_name', $student->class_name) === $classOption)>{{ $classOption }}</option>
                            @endforeach
                        </select>
                        @error('class_name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="section">Section</label>
                        <input type="text" id="section" name="section" value="{{ old('section', $student->section) }}" required>
                        @error('section') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field full">
                        <label for="father_name">Father's Name</label>
                        <input type="text" id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" required>
                        @error('father_name') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="father_cnic">Father's CNIC</label>
                        <input
                            type="text"
                            id="father_cnic"
                            name="father_cnic"
                            value="{{ old('father_cnic', $student->father_cnic) }}"
                            maxlength="15"
                            placeholder="00000-0000000-0"
                            pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}"
                            required>
                        <span class="field-hint">Format: 00000-0000000-0</span>
                        @error('father_cnic') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="contact_number">Contact Number</label>
                        <input
                            type="text"
                            id="contact_number"
                            name="contact_number"
                            value="{{ old('contact_number', $student->contact_number) }}"
                            maxlength="12"
                            placeholder="03XX-XXXXXXX"
                            pattern="03[0-9]{2}-[0-9]{7}"
                            required>
                        <span class="field-hint">Pak format: 03XX-XXXXXXX</span>
                        @error('contact_number') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field full">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3" required>{{ old('address', $student->address) }}</textarea>
                        @error('address') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="monthly_fee">Monthly Fee</label>
                        <input
                            type="text"
                            inputmode="decimal"
                            id="monthly_fee"
                            name="monthly_fee"
                            value="{{ old('monthly_fee', (float) $student->monthly_fee) }}"
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
                            value="{{ old('admission_fee', (float) $student->admission_fee) }}"
                            placeholder="e.g. 15000"
                            pattern="\d+(\.\d{1,2})?"
                            required>
                        <span class="field-hint">One-time admission amount.</span>
                        @error('admission_fee') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="admission_date">Admission Date</label>
                        <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date', optional($student->admission_date)->toDateString()) }}" required>
                        @error('admission_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field full">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach (['Active', 'Inactive', 'Suspended'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $student->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="submit">Update Student</button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const cnicInput = document.getElementById('father_cnic');
            const contactInput = document.getElementById('contact_number');

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
        })();
    </script>
@endpush

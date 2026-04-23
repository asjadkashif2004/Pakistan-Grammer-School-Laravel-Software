<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student - {{ $student->student_code }}</title>
    <style>
        :root {
            --line: #d4ead4;
            --soft: #e7f3e7;
            --ink: #1f3f24;
            --muted: #56735a;
            --primary: #0f7a35;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Figtree', system-ui, sans-serif;
            background: #f6fbf6;
            color: #1f2e21;
        }
        .shell {
            max-width: 1080px;
            margin: 0 auto;
            padding: 12px;
        }
        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }
        .head {
            padding: 12px 14px;
            border-bottom: 1px solid var(--soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            background: linear-gradient(180deg, #fbfefb, #f4fbf5);
        }
        .head h2 {
            margin: 0;
            font-size: 18px;
            color: var(--ink);
            font-weight: 800;
        }
        .head .meta {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }
        .body { padding: 12px; }
        .error-box {
            margin-bottom: 10px;
            border: 1px solid #ffd6d6;
            background: #fff6f6;
            color: #8e2d2d;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
        }
        .error-box ul { margin: 6px 0 0; padding-left: 16px; }
        .section-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        .section {
            border: 1px solid #e2efe2;
            border-radius: 12px;
            padding: 10px;
            background: #fcfffc;
        }
        .section h4 {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 800;
            color: #1b5f2d;
            text-transform: uppercase;
            letter-spacing: .7px;
            border-bottom: 2px solid #da1e28;
            padding-bottom: 6px;
        }
        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field.full { grid-column: 1 / -1; }
        .field label {
            font-size: 11px;
            font-weight: 700;
            color: #244f28;
            letter-spacing: .7px;
            text-transform: uppercase;
        }
        .field input, .field select, .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 14px;
            background: #fff;
            width: 100%;
        }
        .field textarea { min-height: 72px; resize: vertical; }
        .field-hint { font-size: 11px; color: #6f8570; }
        .error { color: #bd3434; font-size: 12px; font-weight: 600; }
        .office-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }
        .check-item {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 8px;
            border: 1px solid #e2efe2;
            border-radius: 9px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #305433;
        }
        .check-item input { width: 16px; height: 16px; margin: 0; }
        .actions {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #fff;
            color: #36563a;
        }
        .btn.primary {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }
        @media (max-width: 960px) {
            .section-grid { grid-template-columns: 1fr; }
            .office-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 700px) {
            .field-grid, .office-grid { grid-template-columns: 1fr; }
            .head h2 { font-size: 16px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <section class="card">
        <header class="head">
            <h2>Edit Admission Form</h2>
            <div class="meta">{{ $student->full_name }} ({{ $student->student_code }})</div>
        </header>
        <div class="body">
            <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
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

                <div class="section-grid">
                    <section class="section">
                        <h4>Personal Information</h4>
                        <div class="field-grid">
                            <div class="field"><label for="form_number">Form Number</label><input id="form_number" name="form_number" value="{{ old('form_number', $student->form_number) }}"></div>
                            <div class="field"><label for="class_name">Admission Seeking in Class</label><select id="class_name" name="class_name" required><option value="">Select Class</option>@foreach ($classOptions as $classOption)<option value="{{ $classOption }}" @selected(old('class_name', $student->class_name) === $classOption)>{{ $classOption }}</option>@endforeach</select>@error('class_name')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="first_name">Student First Name</label><input id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>@error('first_name')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="last_name">Student Last Name</label><input id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>@error('last_name')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="date_of_birth">Date of Birth</label><input id="date_of_birth" type="date" name="date_of_birth" min="1990-01-01" max="2026-12-31" value="{{ old('date_of_birth', optional($student->date_of_birth)->toDateString()) }}" required>@error('date_of_birth')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="admission_date">Date of Admission</label><input id="admission_date" type="date" name="admission_date" value="{{ old('admission_date', optional($student->admission_date)->toDateString()) }}" required>@error('admission_date')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="gender">Gender</label><select id="gender" name="gender" required>@foreach (['Male','Female','Other'] as $gender)<option value="{{ $gender }}" @selected(old('gender', $student->gender) === $gender)>{{ $gender }}</option>@endforeach</select>@error('gender')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="section">Section</label><input id="section" name="section" value="{{ old('section', $student->section) }}" required>@error('section')<span class="error">{{ $message }}</span>@enderror</div>
                        </div>
                    </section>

                    <section class="section">
                        <h4>Academic Details</h4>
                        <div class="field-grid">
                            <div class="field"><label for="previous_school">Previous School</label><input id="previous_school" name="previous_school" value="{{ old('previous_school', $student->previous_school) }}" required>@error('previous_school')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="last_attended_class">Attending Class / Last Attended</label><input id="last_attended_class" name="last_attended_class" value="{{ old('last_attended_class', $student->last_attended_class) }}" required>@error('last_attended_class')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="session_label">Session</label><input id="session_label" name="session_label" value="{{ old('session_label', $student->session_label) }}" required>@error('session_label')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="status">Status</label><select id="status" name="status" required>@foreach (['Active','Inactive','Suspended'] as $status)<option value="{{ $status }}" @selected(old('status', $student->status) === $status)>{{ $status }}</option>@endforeach</select>@error('status')<span class="error">{{ $message }}</span>@enderror</div>
                        </div>
                    </section>
                </div>

                <div class="section-grid">
                    <section class="section">
                        <h4>Guardian & Contact</h4>
                        <div class="field-grid">
                            <div class="field full"><label for="father_name">Father's Name</label><input id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" required>@error('father_name')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="father_occupation">Father / Guardian Occupation</label><input id="father_occupation" name="father_occupation" value="{{ old('father_occupation', $student->father_occupation) }}" required>@error('father_occupation')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="guardian_name">Guardian Name (if father not alive)</label><input id="guardian_name" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}">@error('guardian_name')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="father_cnic">CNIC of Father / Guardian</label><input id="father_cnic" name="father_cnic" value="{{ old('father_cnic', $student->father_cnic) }}" maxlength="15" placeholder="12345-1234567-1" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}" required><span class="field-hint">Format: 12345-1234567-1</span>@error('father_cnic')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="contact_number">Contact Number</label><input id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" maxlength="12" placeholder="03XX-XXXXXXX" pattern="03[0-9]{2}-[0-9]{7}" required>@error('contact_number')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="emergency_contact_number">Emergency Contact</label><input id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number', $student->emergency_contact_number) }}" maxlength="12" placeholder="03XX-XXXXXXX" pattern="03[0-9]{2}-[0-9]{7}" required>@error('emergency_contact_number')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field full"><label for="address">Home Address</label><textarea id="address" name="address" required>{{ old('address', $student->address) }}</textarea>@error('address')<span class="error">{{ $message }}</span>@enderror</div>
                        </div>
                    </section>

                    <section class="section">
                        <h4>Fee & Office Use</h4>
                        <div class="field-grid">
                            <div class="field"><label for="monthly_fee">Monthly Fee</label><input id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', (float) $student->monthly_fee) }}" inputmode="decimal" pattern="\d+(\.\d{1,2})?" required>@error('monthly_fee')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="admission_fee">Admission Fee</label><input id="admission_fee" name="admission_fee" value="{{ old('admission_fee', (float) $student->admission_fee) }}" inputmode="decimal" pattern="\d+(\.\d{1,2})?" required>@error('admission_fee')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="exam_fee">Exam Fee</label><input id="exam_fee" name="exam_fee" value="{{ old('exam_fee', (float) ($student->exam_fee ?? 0)) }}" inputmode="decimal" pattern="\d+(\.\d{1,2})?" required>@error('exam_fee')<span class="error">{{ $message }}</span>@enderror</div>
                            <div class="field"><label for="transport_fee">Transport Fee</label><input id="transport_fee" name="transport_fee" value="{{ old('transport_fee', (float) ($student->transport_fee ?? 0)) }}" inputmode="decimal" pattern="\d+(\.\d{1,2})?" required>@error('transport_fee')<span class="error">{{ $message }}</span>@enderror</div>
                        </div>

                        <div class="office-grid">
                            <label class="check-item" style="flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_bform_submitted" id="cb_office_bform_edit" value="1" @checked(old('office_bform_submitted', $student->office_bform_submitted))> Birth Certificate / B-Form</span>
                                <input type="file" name="office_bform_file" class="office-doc-input" data-office-for="cb_office_bform_edit" data-has-file="{{ $student->office_bform_file_path ? '1' : '0' }}" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                @if ($student->office_bform_file_path)<span class="field-hint" style="width:100%;">A file is on record; upload a new file to replace.</span>@endif
                                @error('office_bform_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                            </label>
                            <label class="check-item" style="flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_father_cnic_submitted" id="cb_office_father_cnic_edit" value="1" @checked(old('office_father_cnic_submitted', $student->office_father_cnic_submitted))> Father CNIC / Local</span>
                                <input type="file" name="office_father_cnic_file" class="office-doc-input" data-office-for="cb_office_father_cnic_edit" data-has-file="{{ $student->office_father_cnic_file_path ? '1' : '0' }}" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                @if ($student->office_father_cnic_file_path)<span class="field-hint" style="width:100%;">A file is on record; upload a new file to replace.</span>@endif
                                @error('office_father_cnic_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                            </label>
                            <label class="check-item" style="flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_result_cards_submitted" id="cb_office_result_cards_edit" value="1" @checked(old('office_result_cards_submitted', $student->office_result_cards_submitted))> Result Cards</span>
                                <input type="file" name="office_result_cards_file" class="office-doc-input" data-office-for="cb_office_result_cards_edit" data-has-file="{{ $student->office_result_cards_file_path ? '1' : '0' }}" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                @if ($student->office_result_cards_file_path)<span class="field-hint" style="width:100%;">A file is on record; upload a new file to replace.</span>@endif
                                @error('office_result_cards_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                            </label>
                            <label class="check-item"><input type="checkbox" name="office_consumable_fee_paid" value="1" @checked(old('office_consumable_fee_paid', $student->office_consumable_fee_paid))> Consumable Fee</label>
                            <label class="check-item"><input type="checkbox" name="office_photos_submitted" value="1" @checked(old('office_photos_submitted', $student->office_photos_submitted))> 3 Passport Size Photos</label>
                            <label class="check-item"><input type="checkbox" name="office_admission_fee_paid" value="1" @checked(old('office_admission_fee_paid', $student->office_admission_fee_paid))> Admission Fee Paid</label>
                        </div>
                    </section>
                </div>

                <div class="actions">
                    <button type="submit" class="btn primary">Update Student</button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
(function () {
    const cnicInput = document.getElementById('father_cnic');
    const contactInput = document.getElementById('contact_number');
    const emergencyInput = document.getElementById('emergency_contact_number');

    if (cnicInput) {
        cnicInput.addEventListener('input', () => {
            const digits = cnicInput.value.replace(/\D/g, '').slice(0, 13);
            cnicInput.value = [digits.slice(0, 5), digits.slice(5, 12), digits.slice(12, 13)].filter(Boolean).join('-');
        });
    }
    if (contactInput) {
        contactInput.addEventListener('input', () => {
            const digits = contactInput.value.replace(/\D/g, '').slice(0, 11);
            contactInput.value = [digits.slice(0, 4), digits.slice(4, 11)].filter(Boolean).join('-');
        });
    }
    if (emergencyInput) {
        emergencyInput.addEventListener('input', () => {
            const digits = emergencyInput.value.replace(/\D/g, '').slice(0, 11);
            emergencyInput.value = [digits.slice(0, 4), digits.slice(4, 11)].filter(Boolean).join('-');
        });
    }

    const MAX_OFFICE_DOC = 5 * 1024 * 1024;
    const officeDocOk = (file) => {
        const okTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        const ext = (file.name && file.name.includes('.')) ? file.name.split('.').pop().toLowerCase() : '';
        const extOk = ['pdf', 'jpg', 'jpeg', 'png', 'webp'].includes(ext);
        if (file.size > MAX_OFFICE_DOC) return 'File must be 5MB or less.';
        if (!okTypes.includes(file.type) && !extOk) return 'Only PDF, JPG, PNG, or WEBP files are allowed.';
        return '';
    };

    document.querySelectorAll('.office-doc-input').forEach((fileInput) => {
        const id = fileInput.getAttribute('data-office-for');
        const cb = id ? document.getElementById(id) : null;
        if (fileInput.getAttribute('data-has-file') === '1' && cb) {
            cb.checked = true;
            cb.disabled = true;
        }
        fileInput.addEventListener('change', () => {
            if (!cb) return;
            const file = fileInput.files && fileInput.files[0];
            if (!file) {
                if (fileInput.getAttribute('data-has-file') !== '1') cb.disabled = false;
                return;
            }
            const err = officeDocOk(file);
            if (err) {
                window.alert(err);
                fileInput.value = '';
                if (fileInput.getAttribute('data-has-file') !== '1') cb.disabled = false;
                return;
            }
            cb.checked = true;
            cb.disabled = true;
        });
    });
})();
</script>
</body>
</html>

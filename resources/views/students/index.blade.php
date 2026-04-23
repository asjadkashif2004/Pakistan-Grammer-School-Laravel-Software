@extends('layouts.school')

@section('title', 'Student Registration | Pakistan Grammar School')
@section('page_heading', 'Student Registration')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('students.roster') }}" class="action-chip" title="Student roster">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="header-action-text">Roster</span>
        </a>
    </div>
@endsection

@push('styles')
<style>
/* Container */
.admission-wrap { 
    max-width: 1080px; 
    margin: 0 auto; 
    padding: 10px;
}

.admission-card { 
    background:#fff; 
    border:1px solid #d4ead4; 
    border-radius:14px; 
    overflow:hidden; 
}

/* Header */
.admission-head { 
    padding:14px 16px; 
    border-bottom:1px solid #e7f3e7; 
    display:flex; 
    justify-content:space-between; 
    gap:10px; 
    flex-wrap:wrap; 
    align-items:center; 
}

.admission-head h3 { 
    margin:0; 
    font-size:22px; 
    color:#1f3f24; 
    font-weight:800; 
}

.admission-head p { 
    margin:2px 0 0; 
    font-size:11px; 
    color:#6b816c; 
    font-weight:700; 
}

.auto-form { 
    background:#e6f4ea; 
    color:#1e6b2f; 
    padding:6px 12px; 
    border-radius:20px; 
    font-size:12px; 
    font-weight:700; 
}

.admission-body { padding:14px; }

/* Error */
.error-box { 
    margin-bottom:14px; 
    border:1px solid #ffd6d6; 
    background:#fff6f6; 
    color:#8e2d2d; 
    border-radius:10px; 
    padding:10px; 
    font-size:12px; 
}

/* Sections */
.section-grid { 
    display:grid; 
    grid-template-columns:repeat(2,1fr); 
    gap:12px; 
    margin-bottom:12px; 
}

.section-box { 
    border:1px solid #e2efe2; 
    border-radius:12px; 
    padding:14px; 
    background:#fcfffc; 
}

.section-title { 
    margin:0 0 10px; 
    font-size:12px; 
    font-weight:800; 
    color:#1b5f2d; 
    text-transform:uppercase; 
    border-bottom:2px solid #da1e28; 
    padding-bottom:6px; 
}

/* Fields */
.field-grid { 
    display:grid; 
    grid-template-columns:repeat(2,1fr); 
    gap:10px; 
}

.field { 
    display:flex; 
    flex-direction:column; 
    gap:5px; 
}

.field.full { grid-column:1 / -1; }

.field label { 
    font-size:10px; 
    font-weight:700; 
    color:#244f28; 
}

.field input, 
.field select, 
.field textarea { 
    border:1px solid #dbe8fb; 
    border-radius:8px; 
    padding:9px; 
    font-size:13px; 
    width:100%;
}

.field textarea { min-height:70px; }

.field input[readonly] { 
    background:#f0f7f2; 
    color:#1e6b2f; 
    font-weight:600; 
}

.field-hint { font-size:10px; color:#1e6b2f; }
.error { font-size:11px; color:#bd3434; }

.discount-box {
    margin-top: 12px;
    border: 1px solid #dbeedc;
    border-radius: 10px;
    background: #f8fdf9;
    padding: 10px;
}

.discount-title {
    margin: 0 0 8px;
    font-size: 11px;
    font-weight: 800;
    color: #1d5b2b;
    letter-spacing: .06em;
    text-transform: uppercase;
}

/* Photo */
.photo-area { 
    border:2px dashed #b7d8bb; 
    border-radius:12px; 
    padding:16px; 
    text-align:center; 
}

.photo-preview-container { 
    display:none; 
    flex-direction:column; 
    align-items:center; 
    gap:10px; 
}

.photo-preview-container img { 
    max-width:140px; 
    max-height:140px; 
    border-radius:10px; 
    border:3px solid #1e6b2f; 
}

/* Buttons */
.btn-photo { 
    padding:8px 14px; 
    border-radius:8px; 
    font-weight:700; 
    cursor:pointer; 
    font-size:13px; 
    margin:4px; 
}

.btn-upload { background:#1e6b2f; color:#fff; }
.btn-webcam { background:#0d6efd; color:#fff; }
.btn-remove { background:#bd3434; color:#fff; font-size:12px; }

/* Office */
.office-grid { 
    display:grid; 
    grid-template-columns:repeat(3,1fr); 
    gap:8px; 
}

.check-item { 
    display:flex; 
    gap:6px; 
    align-items:center; 
    padding:8px; 
    border:1px solid #e2efe2; 
    border-radius:8px; 
    font-size:12px; 
}

/* Actions */
.form-actions { 
    margin-top:20px; 
    display:grid; 
    grid-template-columns:1fr 1fr; 
    gap:10px; 
}

.btn-primary, .btn-secondary { 
    border-radius:10px; 
    padding:12px; 
    font-weight:700; 
    font-size:14px; 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    gap:6px; 
}

.btn-primary { background:#0f7a35; color:#fff; }
.btn-secondary { border:1px solid #d4ead4; }

/* Webcam */
.webcam-modal {
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    display:none;
    align-items:center;
    justify-content:center;
    padding:10px;
}

.webcam-modal.open { display:flex; }
.webcam-modal[hidden] { display:none !important; }

.webcam-card { 
    background:#fff; 
    width:100%; 
    max-width:500px; 
    border-radius:12px; 
    border: 1px solid #d4ead4;
    overflow: hidden;
}

.webcam-head {
    padding: 12px 14px;
    border-bottom: 1px solid #e7f3e7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.webcam-title {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: #1f3f24;
}

.webcam-body {
    padding: 12px;
}

.webcam-body video { 
    width:100%; 
    border-radius:8px; 
    border: 1px solid #dbe8fb;
    background: #f4f8f4;
}

.webcam-foot {
    padding: 12px;
    border-top: 1px solid #e7f3e7;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
}

.webcam-status {
    margin-top: 8px;
    font-size: 11px;
    font-weight: 700;
    color: #2d5e35;
    background: #eef8f0;
    border: 1px solid #cde6d1;
    border-radius: 8px;
    padding: 6px 8px;
}

.webcam-btn {
    border: 1px solid #d4ead4;
    border-radius: 9px;
    padding: 9px 12px;
    background: #fff;
    color: #355538;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.webcam-btn.primary {
    background: #0f7a35;
    border-color: #0f7a35;
    color: #fff;
}

.webcam-btn.secondary {
    background: #f7fbf8;
}

.webcam-btn:disabled {
    opacity: .6;
    cursor: wait;
}

/* ===================== */
/* 📱 MOBILE */
/* ===================== */
@media (max-width:768px){

    .section-grid { grid-template-columns:1fr; }

    .field-grid { grid-template-columns:1fr; }

    .office-grid { grid-template-columns:1fr 1fr; }

    .form-actions { grid-template-columns:1fr; }

    .admission-head { flex-direction:column; align-items:flex-start; }

    .btn-photo { width:100%; }
}

/* 📲 SMALL MOBILE */
@media (max-width:480px){

    .office-grid { grid-template-columns:1fr; }

    .admission-head h3 { font-size:18px; }

    .btn-primary, .btn-secondary { font-size:13px; padding:10px; }
}
</style>
@endpush

@section('content')
    <div class="admission-wrap">
        <section id="registration-form" class="admission-card">
            <header class="admission-head">
                <div>
                    <h3>Admission Form</h3>
                    <p>Pakistan Grammar School - Quetta</p>
                </div>
                <div class="auto-form">Form #: Auto Generated</div>
            </header>

            <div class="admission-body">
                <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" id="studentForm">
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

                    <div class="section-grid">
                        <!-- Personal Information -->
                        <section class="section-box">
                            <h4 class="section-title">Personal Information</h4>
                            <div class="field-grid">
                                <div class="field">
                                    <label for="form_number">Form Number</label>
                                    <input id="form_number" name="form_number" value="{{ old('form_number') }}" 
                                           placeholder="Auto Generated (PGS-2026-00001)" readonly>
                                    <span class="field-hint">Form number will be generated automatically</span>
                                </div>

                                <div class="field">
                                    <label for="class_name">Admission Seeking in Class</label>
                                    <select id="class_name" name="class_name" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classOptions as $classOption)
                                            @php($classMeta = $classMetaByName[$classOption] ?? null)
                                            <option
                                                value="{{ $classOption }}"
                                                data-class-id="{{ $classMeta['id'] ?? '' }}"
                                                data-has-fee="{{ ($classMeta['has_fee'] ?? false) ? '1' : '0' }}"
                                                @selected(old('class_name') === $classOption)
                                            >
                                                {{ $classOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="fee_class_id" name="fee_class_id" value="{{ old('fee_class_id') }}">
                                    @error('class_name')<span class="error">{{ $message }}</span>@enderror
                                    @error('fee_class_id')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="first_name">Student First Name</label>
                                    <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="last_name">Student Last Name</label>
                                    <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input id="date_of_birth" type="date" name="date_of_birth" min="1990-01-01" max="2026-12-31" value="{{ old('date_of_birth') }}" required>
                                    <span class="field-hint">Student must be born between 1990 and 2026</span>
                                    @error('date_of_birth')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="admission_date">Date of Admission</label>
                                    <input id="admission_date" type="date" name="admission_date" 
                                           value="{{ old('admission_date', now()->toDateString()) }}" required>
                                    @error('admission_date')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="gender">Gender</label>
                                    <select id="gender" name="gender" required>
                                        @foreach (['Male', 'Female', 'Other'] as $gender)
                                            <option value="{{ $gender }}" @selected(old('gender', 'Female') === $gender)>{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="section">Section</label>
                                    <input id="section" name="section" value="{{ old('section', 'A') }}" required>
                                    @error('section')<span class="error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </section>

                        <!-- Student Photo -->
                        <section class="section-box">
                            <h4 class="section-title">Student Photo (Passport Size)</h4>
                            <div class="photo-area" id="photoArea">
                                <div id="photoButtons">
                                    <button type="button" id="btnUpload" class="btn-photo btn-upload">Upload from Device</button>
                                    <button type="button" id="btnWebcam" class="btn-photo btn-webcam">Capture from Webcam</button>
                                </div>

                                <div class="photo-preview-container" id="previewContainer">
                                    <img id="photoPreview" src="" alt="Student Photo">
                                    <div>
                                        <button type="button" id="btnChange" class="btn-photo btn-upload">Change Photo</button>
                                        <button type="button" id="btnRemove" class="btn-remove">Remove Photo</button>
                                    </div>
                                </div>
                            </div>

                            <input type="file" id="student_photo" name="student_photo" accept="image/jpeg,image/png,image/webp" style="display:none;">
                            <div class="field-hint">Allowed formats: JPG, PNG, WEBP. Max size: 5MB.</div>
                            @error('student_photo')<span class="error">{{ $message }}</span>@enderror
                        </section>
                    </div>

                    <!-- Guardian & Contact + Fee & Office -->
                    <div class="section-grid">
                        <section class="section-box">
                            <h4 class="section-title">Guardian & Contact</h4>
                            <div class="field-grid">
                                <div class="field full"><label for="father_name">Father's Name</label>
                                    <input id="father_name" name="father_name" value="{{ old('father_name') }}" required>
                                    @error('father_name')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="father_occupation">Father / Guardian Occupation</label>
                                    <input id="father_occupation" name="father_occupation" value="{{ old('father_occupation') }}" required>
                                    @error('father_occupation')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="guardian_name">Guardian Name (if father not alive)</label>
                                    <input id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}">
                                    @error('guardian_name')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="father_cnic">CNIC of Father / Guardian</label>
                                    <input id="father_cnic" name="father_cnic" value="{{ old('father_cnic') }}" 
                                           placeholder="12345-1234567-1" maxlength="15" required>
                                    <span class="field-hint">Format: 12345-1234567-1</span>
                                    @error('father_cnic')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="contact_number">Contact Number</label>
                                    <input id="contact_number" name="contact_number" value="{{ old('contact_number') }}" 
                                           placeholder="03XX-XXXXXXX" maxlength="12" required>
                                    @error('contact_number')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="emergency_contact_number">Emergency Contact</label>
                                    <input id="emergency_contact_number" name="emergency_contact_number" 
                                           value="{{ old('emergency_contact_number') }}" 
                                           placeholder="03XX-XXXXXXX" maxlength="12" required>
                                    @error('emergency_contact_number')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field full"><label for="address">Home Address</label>
                                    <textarea id="address" name="address" required>{{ old('address') }}</textarea>
                                    @error('address')<span class="error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </section>

                        <section class="section-box">
                            <h4 class="section-title">Academic, Fee & Office Use</h4>
                            <div class="field-grid">
                                <div class="field full"><label for="previous_school">Previous School Name</label>
                                    <input id="previous_school" name="previous_school" value="{{ old('previous_school') }}" required>
                                    @error('previous_school')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="last_attended_class">Attending Class / Last Attended</label>
                                    <input id="last_attended_class" name="last_attended_class" value="{{ old('last_attended_class') }}" required>
                                    @error('last_attended_class')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="session_label">Session</label>
                                    <input id="session_label" name="session_label" value="{{ old('session_label', $defaultSession ?? '') }}" required>
                                    @error('session_label')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="monthly_fee">Monthly Fee</label>
                                    <input id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee') }}" 
                                           inputmode="decimal" required>
                                    @error('monthly_fee')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="admission_fee">Admission Fee</label>
                                    <input id="admission_fee" name="admission_fee" value="{{ old('admission_fee') }}" 
                                           inputmode="decimal" required>
                                    @error('admission_fee')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="exam_fee">Exam Fee</label>
                                    <input id="exam_fee" name="exam_fee" value="{{ old('exam_fee', '0') }}"
                                           inputmode="decimal" required>
                                    @error('exam_fee')<span class="error">{{ $message }}</span>@enderror
                                </div>

                                <div class="field"><label for="transport_fee">Transport Fee</label>
                                    <input id="transport_fee" name="transport_fee" value="{{ old('transport_fee', '0') }}"
                                           inputmode="decimal" required>
                                    @error('transport_fee')<span class="error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <input type="hidden" name="status" value="{{ old('status', 'Active') }}">

                            <div class="discount-box">
                                <h5 class="discount-title">Sibling Discount (Auto Calculated)</h5>
                                <div class="field-grid">
                                    <div class="field">
                                        <label for="sibling_status_view">Sibling Status</label>
                                        <input id="sibling_status_view" type="text" value="No" readonly>
                                    </div>
                                    <div class="field">
                                        <label for="sibling_discount_percent_view">Discount Percentage</label>
                                        <input id="sibling_discount_percent_view" type="text" value="0%" readonly>
                                    </div>
                                    <div class="field full">
                                        <label for="sibling_discount_amount_view">Discount Amount</label>
                                        <input id="sibling_discount_amount_view" type="text" value="Rs 0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="office-grid" style="margin-top:12px;">
                                <label class="check-item" style="flex-wrap:wrap;">
                                    <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_bform_submitted" id="cb_office_bform" value="1" @checked(old('office_bform_submitted')) disabled> Birth Certificate / B-Form</span>
                                    <input type="file" name="office_bform_file" class="office-doc-input" data-office-for="cb_office_bform" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                    @error('office_bform_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                                </label>
                                <label class="check-item" style="flex-wrap:wrap;">
                                    <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_father_cnic_submitted" id="cb_office_father_cnic" value="1" @checked(old('office_father_cnic_submitted')) disabled> Father CNIC</span>
                                    <input type="file" name="office_father_cnic_file" class="office-doc-input" data-office-for="cb_office_father_cnic" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                    @error('office_father_cnic_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                                </label>
                                <label class="check-item" style="flex-wrap:wrap;">
                                    <span style="display:flex;align-items:center;gap:8px;width:100%;"><input type="checkbox" name="office_result_cards_submitted" id="cb_office_result_cards" value="1" @checked(old('office_result_cards_submitted')) disabled> Result Cards</span>
                                    <input type="file" name="office_result_cards_file" class="office-doc-input" data-office-for="cb_office_result_cards" accept="application/pdf,.pdf,image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" style="margin-top:4px;font-size:11px;width:100%;">
                                    @error('office_result_cards_file')<span class="error" style="width:100%;">{{ $message }}</span>@enderror
                                </label>
                                <label class="check-item"><input type="checkbox" name="office_consumable_fee_paid" value="1" @checked(old('office_consumable_fee_paid'))> Consumable Fee</label>
                                <label class="check-item"><input type="checkbox" name="office_photos_submitted" value="1" @checked(old('office_photos_submitted'))> 3 Passport Size Photos</label>
                                <label class="check-item"><input type="checkbox" name="office_admission_fee_paid" value="1" @checked(old('office_admission_fee_paid'))> Admission Fee Paid</label>
                            </div>
                        </section>
                    </div>

                    <!-- Form Actions - Fixed & Clean -->
                    <div class="form-actions">
                        <button id="printStudentForm" type="button" class="btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Print Blank Form
                        </button>
                        <button type="submit" class="btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Register Student
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

<div id="webcamModal" class="webcam-modal" aria-hidden="true" hidden>
    <div class="webcam-card" role="dialog" aria-modal="true" aria-labelledby="webcamTitle">
        <div class="webcam-head">
            <h5 class="webcam-title" id="webcamTitle">Capture Student Photo</h5>
            <button id="btnWebcamClose" type="button" class="webcam-btn" aria-label="Close">Close</button>
        </div>
        <div class="webcam-body">
            <video id="webcamVideo" autoplay playsinline></video>
            <canvas id="webcamCanvas" style="display:none;"></canvas>
            <div id="webcamStatus" class="webcam-status">Ready to start camera.</div>
        </div>
        <div class="webcam-foot">
            <button type="button" id="btnSwitchCamera" class="webcam-btn secondary">Switch Camera</button>
            <button type="button" id="btnCapture" class="webcam-btn primary">Capture Photo</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024;
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    const classSelect = document.getElementById('class_name');
    const feeClassIdInput = document.getElementById('fee_class_id');
    const cnicInput = document.getElementById('father_cnic');
    const monthlyFeeInput = document.getElementById('monthly_fee');
    const admissionFeeInput = document.getElementById('admission_fee');
    const examFeeInput = document.getElementById('exam_fee');
    const transportFeeInput = document.getElementById('transport_fee');
    const feeApiBaseUrl = @json(url('/get-fee'));
    const siblingApiUrl = @json(route('students.sibling-discount'));
    const siblingStatusView = document.getElementById('sibling_status_view');
    const siblingDiscountPercentView = document.getElementById('sibling_discount_percent_view');
    const siblingDiscountAmountView = document.getElementById('sibling_discount_amount_view');

    const setFeeField = (field, value) => {
        if (!field) return;
        const parsed = Number.parseFloat(value);
        field.value = Number.isFinite(parsed) ? parsed.toFixed(2) : '0.00';
    };

    const syncClassFee = async () => {
        if (!classSelect) return;
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        const classId = selectedOption?.dataset?.classId || '';
        if (feeClassIdInput) feeClassIdInput.value = classId;
        if (!classId) return;

        try {
            const response = await fetch(`${feeApiBaseUrl}/${classId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) return;
            const data = await response.json();
            if (!data || !data.found) return;
            setFeeField(monthlyFeeInput, data.monthly_fee ?? data.tuition_fee);
            setFeeField(admissionFeeInput, data.admission_fee);
            setFeeField(examFeeInput, data.exam_fee);
            setFeeField(transportFeeInput, data.transport_fee);
            await syncSiblingDiscount();
        } catch (_) {}
    };

    const toMoney = (n) => `Rs ${Number(n || 0).toFixed(2)}`;

    const syncSiblingDiscount = async () => {
        const payload = new URLSearchParams({
            father_cnic: (cnicInput?.value || '').trim(),
            monthly_fee: (monthlyFeeInput?.value || '0').trim(),
            admission_fee: (admissionFeeInput?.value || '0').trim(),
            exam_fee: (examFeeInput?.value || '0').trim(),
            transport_fee: (transportFeeInput?.value || '0').trim(),
        });
        try {
            const response = await fetch(`${siblingApiUrl}?${payload.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) return;
            const data = await response.json();
            if (!data) return;
            if (siblingStatusView) siblingStatusView.value = data.sibling_status || 'No';
            if (siblingDiscountPercentView) siblingDiscountPercentView.value = `${Number(data.discount_percentage || 0).toFixed(0)}%`;
            if (siblingDiscountAmountView) siblingDiscountAmountView.value = toMoney(data.discount_amount || 0);
        } catch (_) {}
    };

    classSelect?.addEventListener('change', syncClassFee);
    if ((classSelect?.value || '').trim() !== '') {
        syncClassFee();
    } else {
        syncSiblingDiscount();
    }

    // Photo Logic
    const fileInput = document.getElementById('student_photo');
    const previewContainer = document.getElementById('previewContainer');
    const photoPreview = document.getElementById('photoPreview');
    const btnUpload = document.getElementById('btnUpload');
    const btnChange = document.getElementById('btnChange');
    const btnRemove = document.getElementById('btnRemove');

    const validateImageFile = (file) => {
        if (!file) {
            return { valid: false, message: 'Please select an image file.' };
        }
        if (!ALLOWED_TYPES.includes(file.type)) {
            return { valid: false, message: 'Only JPG, PNG, and WEBP images are allowed.' };
        }
        if (file.size > MAX_IMAGE_SIZE) {
            return { valid: false, message: 'Image size must be 5MB or less.' };
        }
        return { valid: true, message: '' };
    };

    const setPreviewFromFile = (file) => {
        const reader = new FileReader();
        reader.onload = function (ev) {
            photoPreview.src = ev.target.result;
            previewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    };

    const showImageError = (message) => {
        window.alert(message);
    };

    const openFilePicker = () => {
        // Ensure we open device files/gallery picker (not forced camera).
        fileInput.removeAttribute('capture');
        fileInput.click();
    };

    btnUpload.addEventListener('click', openFilePicker);
    btnChange.addEventListener('click', openFilePicker);

    fileInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const validation = validateImageFile(file);
            if (!validation.valid) {
                fileInput.value = '';
                previewContainer.style.display = 'none';
                showImageError(validation.message);
                return;
            }
            setPreviewFromFile(file);
        }
    });

    btnRemove.addEventListener('click', () => {
        fileInput.value = '';
        previewContainer.style.display = 'none';
    });

    // Webcam Logic
    let stream = null;
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('webcamCanvas');
    const webcamModal = document.getElementById('webcamModal');
    const btnWebcam = document.getElementById('btnWebcam');
    const btnWebcamClose = document.getElementById('btnWebcamClose');
    const btnCapture = document.getElementById('btnCapture');
    const btnSwitchCamera = document.getElementById('btnSwitchCamera');
    const webcamStatus = document.getElementById('webcamStatus');
    const isMobileDevice = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);
    let preferredFacingMode = isMobileDevice ? 'environment' : 'user';

    const setWebcamStatus = (message) => {
        if (webcamStatus) webcamStatus.textContent = message;
    };

    const stopStream = () => {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }
    };

    const closeWebcam = () => {
        stopStream();
        webcamModal.classList.remove('open');
        webcamModal.hidden = true;
        webcamModal.setAttribute('aria-hidden', 'true');
        setWebcamStatus('Camera closed.');
    };

    const startWebcam = async () => {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showImageError('Webcam is not supported on this browser/device.');
            return;
        }

        webcamModal.hidden = false;
        webcamModal.classList.add('open');
        webcamModal.setAttribute('aria-hidden', 'false');
        btnCapture.disabled = true;
        btnSwitchCamera.disabled = true;
        setWebcamStatus('Starting camera...');
        stopStream();
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: preferredFacingMode }, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false,
            });
            if (!stream) {
                throw new Error('No stream');
            }
            video.srcObject = stream;
            await video.play();
            btnCapture.disabled = false;
            btnSwitchCamera.disabled = false;
            setWebcamStatus(
                preferredFacingMode === 'environment'
                    ? 'Rear camera active.'
                    : 'Front camera active.'
            );
        } catch (err) {
            try {
                // Fallback if facingMode is not supported.
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                await video.play();
                btnCapture.disabled = false;
                btnSwitchCamera.disabled = false;
                setWebcamStatus('Camera active.');
            } catch (_) {
                closeWebcam();
                showImageError('Unable to access webcam/camera. Please allow camera permissions.');
            }
        }
    };

    btnWebcam?.addEventListener('click', startWebcam);
    btnWebcamClose?.addEventListener('click', closeWebcam);
    btnSwitchCamera?.addEventListener('click', async () => {
        preferredFacingMode = preferredFacingMode === 'user' ? 'environment' : 'user';
        await startWebcam();
    });
    webcamModal?.addEventListener('click', (event) => {
        if (event.target === webcamModal) {
            closeWebcam();
        }
    });

    btnCapture.addEventListener('click', () => {
        if (!video.videoWidth || !video.videoHeight) {
            showImageError('Camera stream is not ready. Please try again.');
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob((blob) => {
            if (!blob) {
                showImageError('Unable to capture image from webcam.');
                return;
            }
            const file = new File([blob], 'webcam-photo.jpg', { type: 'image/jpeg' });
            const validation = validateImageFile(file);
            if (!validation.valid) {
                showImageError(validation.message);
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            setPreviewFromFile(file);
            setWebcamStatus('Photo captured successfully.');
            closeWebcam();
        }, 'image/jpeg', 0.9);
    });

    // CNIC & Phone Masking
    const contactInput = document.getElementById('contact_number');
    const emergencyInput = document.getElementById('emergency_contact_number');

    const maskCnic = (input) => {
        let digits = input.value.replace(/\D/g, '').slice(0, 13);
        input.value = [digits.slice(0,5), digits.slice(5,12), digits.slice(12,13)].filter(Boolean).join('-');
    };
    const maskPhone = (input) => {
        let digits = input.value.replace(/\D/g, '').slice(0, 11);
        input.value = [digits.slice(0,4), digits.slice(4,11)].filter(Boolean).join('-');
    };

    cnicInput?.addEventListener('input', () => {
        maskCnic(cnicInput);
        syncSiblingDiscount();
    });
    contactInput?.addEventListener('input', () => maskPhone(contactInput));
    emergencyInput?.addEventListener('input', () => maskPhone(emergencyInput));
    monthlyFeeInput?.addEventListener('input', syncSiblingDiscount);
    admissionFeeInput?.addEventListener('input', syncSiblingDiscount);
    examFeeInput?.addEventListener('input', syncSiblingDiscount);
    transportFeeInput?.addEventListener('input', syncSiblingDiscount);

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
        fileInput.addEventListener('change', () => {
            const id = fileInput.getAttribute('data-office-for');
            const cb = id ? document.getElementById(id) : null;
            if (!cb) return;
            cb.disabled = true; // always read-only; auto-managed by uploads only
            const f = fileInput.files && fileInput.files[0];
            if (!f) {
                cb.checked = false;
                return;
            }
            const err = officeDocOk(f);
            if (err) {
                window.alert(err);
                fileInput.value = '';
                cb.checked = false;
                return;
            }
            cb.checked = true;
        });
    });

    const printButton = document.getElementById('printStudentForm');
    printButton?.addEventListener('click', () => {
        const value = (id) => (document.getElementById(id)?.value || '').trim() || '________________';
        const amount = (id) => {
            const raw = (document.getElementById(id)?.value || '0').trim();
            const parsed = Number.parseFloat(raw);
            return Number.isFinite(parsed) ? parsed : 0;
        };
        const feeTotal = amount('monthly_fee') + amount('admission_fee') + amount('exam_fee') + amount('transport_fee');
        const discountPercent = Number.parseFloat((document.getElementById('sibling_discount_percent_view')?.value || '0').replace('%', '')) || 0;
        const discountAmount = Number.parseFloat((document.getElementById('sibling_discount_amount_view')?.value || '0').replace(/[^\d.]/g, '')) || 0;
        const finalPayable = Math.max(0, feeTotal - discountAmount);
        const money = (num) => `Rs ${num.toFixed(2)}`;
        const check = (name) => document.querySelector(`[name="${name}"]`)?.checked ? 'Yes' : 'No';
        const win = window.open('', '_blank', 'width=980,height=760');
        if (!win) return;
        win.document.write(`<!doctype html><html><head><meta charset="utf-8"><title>Admission Form Print</title><link rel="icon" href="{{ asset('images/logo.png') }}">
<style>
body{margin:0;padding:14px;font-family:Arial,sans-serif;color:#111}
.sheet{max-width:980px;margin:0 auto;border:2px solid #0f5d23;padding:10px}
.head{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:start;border-bottom:2px solid #cb1e2f;padding-bottom:8px}
.title{margin:0;color:#155d25;font-size:44px;font-weight:900;line-height:1}
.sub{margin:4px 0 0;font-size:38px;font-weight:800}
.meta{margin-top:5px;font-size:13px;font-weight:700}
.photo{width:130px;height:150px;border:2px solid #0f5d23;display:flex;align-items:center;justify-content:center;font-weight:800;color:#0f5d23}
.sec{margin-top:10px}
.sec h4{margin:0 0 6px;color:#cb1e2f;font-size:18px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.line{border-bottom:1px solid #cb1e2f;padding:6px 0;font-size:14px}
.line span{font-weight:700;display:inline-block;min-width:220px}
.fee-table{width:100%;border-collapse:collapse;margin-top:6px}
.fee-table th,.fee-table td{border:1px solid #cfdccc;padding:7px 8px;font-size:13px}
.fee-table th{background:#f4f8f2;text-transform:uppercase;letter-spacing:.6px;font-size:11px}
.fee-table td:last-child,.fee-table th:last-child{text-align:right}
.checks{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:8px}
.chk{border:1px solid #ddd;padding:7px;font-size:12px;font-weight:700}
.footer{margin-top:16px;border-top:1px solid #bbb;padding-top:8px;text-align:center;font-size:12px;font-weight:700}
@media print{@page{size:A4;margin:10mm}.sheet{border:0;max-width:100%}}
</style></head><body>
<div class="sheet">
<div class="head">
<div><p class="title">Pakistan Grammar School</p><p class="sub">Admission form</p><p class="meta">Form #: ${value('form_number')} | Class: ${value('class_name')}</p></div>
<div class="photo">Photo</div>
</div>
<div class="sec"><h4>Student Information</h4>
<div class="grid">
<div class="line"><span>Name</span>${value('first_name')} ${value('last_name')}</div>
<div class="line"><span>Date of Birth</span>${value('date_of_birth')}</div>
<div class="line"><span>Date of Admission</span>${value('admission_date')}</div>
<div class="line"><span>Section</span>${value('section')}</div>
<div class="line"><span>Gender</span>${value('gender')}</div>
<div class="line"><span>Session</span>${value('session_label')}</div>
</div></div>
<div class="sec"><h4>Guardian / Contact</h4>
<div class="line"><span>Father Name</span>${value('father_name')}</div>
<div class="line"><span>Father / Guardian Occupation</span>${value('father_occupation')}</div>
<div class="line"><span>Guardian Name</span>${value('guardian_name')}</div>
<div class="line"><span>Father / Guardian CNIC</span>${value('father_cnic')}</div>
<div class="line"><span>Home Address</span>${value('address')}</div>
<div class="line"><span>Contact No</span>${value('contact_number')}</div>
<div class="line"><span>Emergency Contact No</span>${value('emergency_contact_number')}</div>
</div>
<div class="sec"><h4>Academic & Office Use</h4>
<div class="line"><span>Previous School</span>${value('previous_school')}</div>
<div class="line"><span>Last Attended Class</span>${value('last_attended_class')}</div>
<table class="fee-table">
<thead><tr><th>Fee Head</th><th>Amount</th></tr></thead>
<tbody>
<tr><td>Tuition Fee</td><td>${money(amount('monthly_fee'))}</td></tr>
<tr><td>Admission Fee</td><td>${money(amount('admission_fee'))}</td></tr>
<tr><td>Exam Fee</td><td>${money(amount('exam_fee'))}</td></tr>
<tr><td>Transport Fee</td><td>${money(amount('transport_fee'))}</td></tr>
<tr><td>Discount (${discountPercent.toFixed(0)}%)</td><td>- ${money(discountAmount)}</td></tr>
<tr><td><strong>Total Payable</strong></td><td><strong>${money(finalPayable)}</strong></td></tr>
</tbody>
</table>
<div class="checks">
<div class="chk">Birth certificate / B-Form: ${check('office_bform_submitted')}</div>
<div class="chk">Father CNIC / Local: ${check('office_father_cnic_submitted')}</div>
<div class="chk">Result cards: ${check('office_result_cards_submitted')}</div>
<div class="chk">Consumable fee: ${check('office_consumable_fee_paid')}</div>
<div class="chk">3 Passport size photos: ${check('office_photos_submitted')}</div>
<div class="chk">Admission fee paid: ${check('office_admission_fee_paid')}</div>
</div></div>
<div class="footer">Developed by: Addsmint.com</div>
</div></body></html>`);
        win.document.close();
        win.focus();
        win.print();
    });

})();
</script>
@endpush
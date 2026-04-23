@extends('layouts.school')

@section('title', 'Student Details | Pakistan Grammar School')
@section('page_heading', 'Student Details')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('students.roster') }}" class="action-chip" title="Back to roster">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="header-action-text">Roster</span>
        </a>
        <a href="{{ route('students.print', $student) }}" target="_blank" class="action-chip" title="Print form">Print</a>
        <a href="{{ route('students.edit', $student) }}" class="action-chip primary" title="Edit student">Edit</a>
    </div>
@endsection

@push('styles')
    <style>
        .detail-card { background:#fff; border:1px solid #d4ead4; border-radius:14px; overflow:hidden; max-width:980px; margin:0 auto; }
        .detail-head { padding:14px 16px; border-bottom:1px solid #e7f3e7; font-size:22px; font-weight:800; color:#1f3f24; }
        .detail-body { padding:14px; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
        .group { border:1px solid #e2efe2; border-radius:12px; padding:10px; background:#fcfffc; }
        .group h4 { margin:0 0 8px; font-size:13px; font-weight:800; color:#1b5f2d; text-transform:uppercase; letter-spacing:.7px; }
        .item { display:flex; justify-content:space-between; gap:10px; border-top:1px dashed #dcebdc; padding:7px 0; font-size:13px; }
        .item:first-child { border-top:0; padding-top:0; }
        .item span { color:#6a816d; font-weight:700; }
        .item strong { text-align:right; color:#17361d; }
        @media (max-width: 900px) { .detail-body { grid-template-columns:1fr; } }
    </style>
@endpush

@section('content')
    <section class="detail-card">
        <header class="detail-head">{{ $student->full_name }} ({{ $student->student_code }})</header>
        <div class="detail-body">
            <article class="group">
                <h4>Personal</h4>
                <div class="item"><span>Form Number</span><strong>{{ $student->form_number ?: '-' }}</strong></div>
                <div class="item"><span>Date of Birth</span><strong>{{ optional($student->date_of_birth)->format('d M Y') }}</strong></div>
                <div class="item"><span>Gender</span><strong>{{ $student->gender }}</strong></div>
                <div class="item"><span>Admission Date</span><strong>{{ optional($student->admission_date)->format('d M Y') }}</strong></div>
                <div class="item"><span>Status</span><strong>{{ $student->status }}</strong></div>
            </article>
            <article class="group">
                <h4>Academic</h4>
                <div class="item"><span>Class</span><strong>{{ $student->class_name }}</strong></div>
                <div class="item"><span>Section</span><strong>{{ $student->section }}</strong></div>
                <div class="item"><span>Session</span><strong>{{ $student->session_label ?: '-' }}</strong></div>
                <div class="item"><span>Previous School</span><strong>{{ $student->previous_school ?: '-' }}</strong></div>
                <div class="item"><span>Last Attended</span><strong>{{ $student->last_attended_class ?: '-' }}</strong></div>
            </article>
            <article class="group">
                <h4>Guardian & Contact</h4>
                <div class="item"><span>Father Name</span><strong>{{ $student->father_name }}</strong></div>
                <div class="item"><span>Occupation</span><strong>{{ $student->father_occupation ?: '-' }}</strong></div>
                <div class="item"><span>Guardian Name</span><strong>{{ $student->guardian_name ?: '-' }}</strong></div>
                <div class="item"><span>CNIC</span><strong>{{ $student->father_cnic }}</strong></div>
                <div class="item"><span>Contact</span><strong>{{ $student->contact_number }}</strong></div>
                <div class="item"><span>Emergency</span><strong>{{ $student->emergency_contact_number ?: '-' }}</strong></div>
            </article>
            <article class="group">
                <h4>Profile</h4>
                <div class="item"><span>Student Photo</span><strong>{{ $student->student_photo_path ? 'Available' : 'Not uploaded' }}</strong></div>
            </article>
        </div>
    </section>
@endsection

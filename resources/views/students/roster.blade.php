@extends('layouts.school')

@section('title', 'Student Roster | Pakistan Grammar School')
@section('page_heading', 'Student Roster')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('students.index') }}" class="action-chip" title="New registration">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span class="header-action-text">Register</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .roster-card { background:#fff; border:1px solid #d4ead4; border-radius:14px; overflow:hidden; }
        .roster-head { padding:14px 16px; border-bottom:1px solid #e7f3e7; font-size:22px; font-weight:800; color:#1f3f24; }
        .roster-body { padding:14px; }
        .tools { display:grid; grid-template-columns: 1.7fr 1fr 1fr auto; gap:8px; margin-bottom:12px; }
        .tools input, .tools select { border:1px solid #dbe8fb; border-radius:10px; padding:9px 10px; font-size:14px; background:#fff; }
        .tools button { border:1px solid #d4ead4; border-radius:10px; padding:9px 14px; background:#fff; color:#355538; font-weight:700; cursor:pointer; }
        .tools .reset-link { border:1px solid #d4ead4; border-radius:10px; padding:9px 12px; text-decoration:none; color:#355538; font-weight:700; display:inline-flex; align-items:center; justify-content:center; }
        .table { width:100%; border-collapse:collapse; }
        .table th, .table td { text-align:left; padding:9px 8px; border-top:1px solid #e8f3e8; font-size:13px; vertical-align:top; }
        .table th { border-top:0; color:#56735a; font-size:11px; letter-spacing:1px; text-transform:uppercase; font-weight:800; }
        .status { display:inline-flex; border-radius:999px; padding:2px 8px; font-size:11px; font-weight:700; }
        .status.active  { background:#ddf8e4; color:#0f7a35; }
        .status.inactive { background:#fff2da; color:#966113; }
        .status.suspended { background:#ffe3e3; color:#a93b3b; }
        .actions { display:inline-flex; gap:4px; align-items:center; flex-wrap:wrap; }
        .actions form { display:inline-flex; margin:0; }
        .meta { color:#6f8570; font-size:12px; }
        .avatar { width:42px; height:42px; border-radius:10px; object-fit:cover; border:1px solid #d4ead4; background:#f2f8f2; }
        .avatar-cell { width:54px; }

        /* ── Icon button base ── */
        .ib {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #d4ead4;
            background: #ffffff;
            color: #2a5c30;
            text-decoration: none;
            cursor: pointer;
            padding: 0;
            position: relative;
            transition: background .13s, border-color .13s, color .13s;
        }
        .ib svg { display:block; pointer-events:none; }
        .ib:hover { background:#eef8ef; border-color:#82c48d; }

        /* Colour variants */
        .ib.blue  { color:#1a4d99; border-color:#c5d8f7; background:#f4f8ff; }
        .ib.blue:hover  { background:#deeafb; border-color:#7aaae8; }

        .ib.amber { color:#7a4f00; border-color:#e8d28a; background:#fffcf0; }
        .ib.amber:hover { background:#fff3cc; border-color:#ccaa3a; }

        .ib.red   { color:#922020; border-color:#f7c8c8; background:#fff6f6; }
        .ib.red:hover   { background:#ffe4e4; border-color:#e88080; }

        /* Pure-CSS tooltip via data-tip */
        .ib::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1f3f24;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .12s;
            z-index: 30;
        }
        .ib:hover::after { opacity: 1; }

        dialog#student-details-modal {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 0;
            width: min(920px, 96vw);
            max-height: 92vh;
            overflow: hidden;
        }

        dialog#student-details-modal::backdrop {
            background: rgba(15, 40, 20, 0.35);
        }

        .sdm-head {
            padding: 12px 14px;
            border-bottom: 1px solid #e7f3e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #f8fbf8;
        }

        .sdm-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #1f3f24;
        }

        .sdm-body {
            padding: 14px;
            overflow: auto;
            max-height: calc(92vh - 70px);
        }

        .sdm-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .sdm-avatar {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            border: 1px solid #d4ead4;
            object-fit: cover;
            background: #f2f8f2;
        }

        .sdm-code {
            font-size: 12px;
            color: #56735a;
            font-weight: 700;
        }

        .sdm-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .sdm-group {
            border: 1px solid #e2efe2;
            border-radius: 12px;
            padding: 10px;
            background: #fcfffc;
        }

        .sdm-group h4 {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 800;
            color: #1b5f2d;
            text-transform: uppercase;
            letter-spacing: .7px;
        }

        .sdm-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px dashed #dcebdc;
            padding: 6px 0;
            font-size: 13px;
        }

        .sdm-item:first-child { border-top: 0; padding-top: 0; }
        .sdm-item span { color: #6a816d; font-weight: 700; }
        .sdm-item strong { text-align: right; color: #17361d; }

        .edit-modal {
            position: fixed;
            inset: 0;
            background: rgba(10, 28, 15, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
            z-index: 80;
        }

        .edit-modal.open { display: flex; }

        .edit-shell {
            width: min(1220px, 100%);
            height: min(90vh, 900px);
            background: #fff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 28px 54px -34px rgba(4, 30, 11, 0.65);
            display: grid;
            grid-template-rows: auto 1fr;
        }

        .edit-head {
            padding: 10px 12px;
            border-bottom: 1px solid #e7f3e7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: linear-gradient(180deg, #fcfffc, #f2faf3);
        }

        .edit-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #1f3f24;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .edit-title svg { width: 16px; height: 16px; color: #0f7a35; }

        .edit-actions { display: inline-flex; align-items: center; gap: 6px; }

        .edit-iframe {
            width: 100%;
            height: 100%;
            border: 0;
            background: #f7fbf8;
        }

        .ib.ghost {
            color: #355538;
            border-color: #d4ead4;
            background: #f8fdf8;
        }

        @media (max-width: 900px) {
            .tools { grid-template-columns:1fr; }
            .table { display:block; overflow-x:auto; white-space:nowrap; }
            .sdm-grid { grid-template-columns: 1fr; }
            .edit-shell { width: 100%; height: 94vh; border-radius: 10px; }
        }
    </style>
@endpush

@section('content')
    <section class="roster-card">
        <header class="roster-head">Registered Students</header>
        <div class="roster-body">
            <form method="GET" class="tools">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by ID, name, father, contact">
                <select name="class_name">
                    <option value="">All Classes</option>
                    @foreach ($classFilters as $classOption)
                        <option value="{{ $classOption }}" @selected($classFilter === $classOption)>{{ $classOption }}</option>
                    @endforeach
                </select>
                <select name="section">
                    <option value="">All Sections</option>
                    @foreach ($sectionFilters as $sectionOption)
                        <option value="{{ $sectionOption }}" @selected($sectionFilter === $sectionOption)>{{ $sectionOption }}</option>
                    @endforeach
                </select>
                <button type="submit">Apply</button>
                <a class="reset-link" href="{{ route('students.roster') }}">Reset</a>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Contact</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td class="avatar-cell">
                                @if ($student->student_photo_path)
                                    <img class="avatar" src="{{ asset('storage/'.$student->student_photo_path) }}" alt="Student photo">
                                @else
                                    <img class="avatar" src="https://dummyimage.com/84x84/eaf5ea/5a7a5e&text=+" alt="No photo">
                                @endif
                            </td>
                            <td><strong>{{ $student->student_code }}</strong><div class="meta">Form: {{ $student->form_number ?: '-' }}</div></td>
                            <td><strong>{{ $student->full_name }}</strong><div class="meta">Father: {{ $student->father_name }}</div></td>
                            <td>{{ $student->class_name }} - {{ $student->section }}<div class="meta">Session: {{ $student->session_label ?: '-' }}</div></td>
                            <td>{{ $student->contact_number }}<div class="meta">Emergency: {{ $student->emergency_contact_number ?: '-' }}</div></td>
                            <td>{{ $student->guardian_name ?: '-' }}<div class="meta">Occupation: {{ $student->father_occupation ?: '-' }}</div></td>
                            <td><span class="status {{ strtolower($student->status) }}">{{ $student->status }}</span></td>
                            <td>
                                <div class="actions">

                                    {{-- Details — eye icon --}}
                                    <button
                                        type="button"
                                        class="ib blue"
                                        data-tip="Details"
                                        data-open-student-details
                                        data-full-name="{{ e($student->full_name) }}"
                                        data-student-code="{{ e($student->student_code) }}"
                                        data-form-number="{{ e((string) ($student->form_number ?: '-')) }}"
                                        data-date-of-birth="{{ e((string) (optional($student->date_of_birth)->format('d M Y') ?: '-')) }}"
                                        data-gender="{{ e((string) ($student->gender ?: '-')) }}"
                                        data-admission-date="{{ e((string) (optional($student->admission_date)->format('d M Y') ?: '-')) }}"
                                        data-status="{{ e((string) ($student->status ?: '-')) }}"
                                        data-class-name="{{ e((string) ($student->class_name ?: '-')) }}"
                                        data-section="{{ e((string) ($student->section ?: '-')) }}"
                                        data-session-label="{{ e((string) ($student->session_label ?: '-')) }}"
                                        data-previous-school="{{ e((string) ($student->previous_school ?: '-')) }}"
                                        data-last-attended-class="{{ e((string) ($student->last_attended_class ?: '-')) }}"
                                        data-father-name="{{ e((string) ($student->father_name ?: '-')) }}"
                                        data-father-occupation="{{ e((string) ($student->father_occupation ?: '-')) }}"
                                        data-guardian-name="{{ e((string) ($student->guardian_name ?: '-')) }}"
                                        data-father-cnic="{{ e((string) ($student->father_cnic ?: '-')) }}"
                                        data-contact-number="{{ e((string) ($student->contact_number ?: '-')) }}"
                                        data-emergency-contact="{{ e((string) ($student->emergency_contact_number ?: '-')) }}"
                                        data-photo-url="{{ $student->student_photo_path ? e(asset('storage/'.$student->student_photo_path)) : 'https://dummyimage.com/128x128/eaf5ea/5a7a5e&text=+' }}"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                    {{-- Print — printer icon --}}
                                    <a class="ib" href="{{ route('students.print', $student) }}" target="_blank" data-tip="Print">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 6 2 18 2 18 9"/>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                            <rect x="6" y="14" width="12" height="8"/>
                                        </svg>
                                    </a>

                                    {{-- Edit — pencil icon --}}
                                    <button
                                        type="button"
                                        class="ib amber"
                                        data-tip="Edit"
                                        data-open-edit-modal
                                        data-edit-url="{{ route('students.edit', ['student' => $student, 'embed' => 1]) }}"
                                        data-edit-full-url="{{ route('students.edit', $student) }}"
                                        data-student-name="{{ e($student->full_name) }}"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete — trash icon --}}
                                    <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Delete this student record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ib red" type="submit" data-tip="Delete">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="meta">No students found for applied filters.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="list-pagination">{{ $students->links() }}</div>
        </div>
    </section>

    <dialog id="student-details-modal">
        <div class="sdm-head">
            <h3 class="sdm-title">Student Details</h3>
            <button type="button" class="ib" id="close-student-details" aria-label="Close details">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sdm-body">
            <div class="sdm-top">
                <img id="sdm-photo" class="sdm-avatar" src="" alt="Student photo">
                <div>
                    <div id="sdm-name" style="font-size:18px;font-weight:800;color:#1f3f24;"></div>
                    <div id="sdm-code" class="sdm-code"></div>
                </div>
            </div>

            <div class="sdm-grid">
                <article class="sdm-group">
                    <h4>Personal</h4>
                    <div class="sdm-item"><span>Form Number</span><strong id="sdm-form-number">-</strong></div>
                    <div class="sdm-item"><span>Date of Birth</span><strong id="sdm-dob">-</strong></div>
                    <div class="sdm-item"><span>Gender</span><strong id="sdm-gender">-</strong></div>
                    <div class="sdm-item"><span>Admission Date</span><strong id="sdm-admission-date">-</strong></div>
                    <div class="sdm-item"><span>Status</span><strong id="sdm-status">-</strong></div>
                </article>
                <article class="sdm-group">
                    <h4>Academic</h4>
                    <div class="sdm-item"><span>Class</span><strong id="sdm-class">-</strong></div>
                    <div class="sdm-item"><span>Section</span><strong id="sdm-section">-</strong></div>
                    <div class="sdm-item"><span>Session</span><strong id="sdm-session">-</strong></div>
                    <div class="sdm-item"><span>Previous School</span><strong id="sdm-prev-school">-</strong></div>
                    <div class="sdm-item"><span>Last Attended</span><strong id="sdm-last-attended">-</strong></div>
                </article>
                <article class="sdm-group">
                    <h4>Guardian & Contact</h4>
                    <div class="sdm-item"><span>Father Name</span><strong id="sdm-father-name">-</strong></div>
                    <div class="sdm-item"><span>Occupation</span><strong id="sdm-occupation">-</strong></div>
                    <div class="sdm-item"><span>Guardian Name</span><strong id="sdm-guardian-name">-</strong></div>
                    <div class="sdm-item"><span>CNIC</span><strong id="sdm-cnic">-</strong></div>
                    <div class="sdm-item"><span>Contact</span><strong id="sdm-contact">-</strong></div>
                    <div class="sdm-item"><span>Emergency</span><strong id="sdm-emergency">-</strong></div>
                </article>
            </div>
        </div>
    </dialog>

    <div id="student-edit-modal" class="edit-modal" aria-hidden="true">
        <div class="edit-shell" role="dialog" aria-modal="true" aria-labelledby="student-edit-title">
            <div class="edit-head">
                <h3 class="edit-title" id="student-edit-title">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Edit Student
                </h3>
                <div class="edit-actions">
                    <a id="student-edit-open-tab" class="ib ghost" href="#" target="_blank" rel="noopener" title="Open full page" aria-label="Open full page">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3h7v7M10 14 21 3M19 14v7H3V5h7"/></svg>
                    </a>
                    <button type="button" id="student-edit-close" class="ib" title="Close" aria-label="Close">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <iframe id="student-edit-frame" class="edit-iframe" title="Edit student form"></iframe>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('student-details-modal');
    const closeBtn = document.getElementById('close-student-details');
    if (!modal) return;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '-';
    };

    const openModal = (button) => {
        const photo = document.getElementById('sdm-photo');
        if (photo) photo.src = button.getAttribute('data-photo-url') || '';

        setText('sdm-name', button.getAttribute('data-full-name'));
        setText('sdm-code', button.getAttribute('data-student-code'));
        setText('sdm-form-number', button.getAttribute('data-form-number'));
        setText('sdm-dob', button.getAttribute('data-date-of-birth'));
        setText('sdm-gender', button.getAttribute('data-gender'));
        setText('sdm-admission-date', button.getAttribute('data-admission-date'));
        setText('sdm-status', button.getAttribute('data-status'));
        setText('sdm-class', button.getAttribute('data-class-name'));
        setText('sdm-section', button.getAttribute('data-section'));
        setText('sdm-session', button.getAttribute('data-session-label'));
        setText('sdm-prev-school', button.getAttribute('data-previous-school'));
        setText('sdm-last-attended', button.getAttribute('data-last-attended-class'));
        setText('sdm-father-name', button.getAttribute('data-father-name'));
        setText('sdm-occupation', button.getAttribute('data-father-occupation'));
        setText('sdm-guardian-name', button.getAttribute('data-guardian-name'));
        setText('sdm-cnic', button.getAttribute('data-father-cnic'));
        setText('sdm-contact', button.getAttribute('data-contact-number'));
        setText('sdm-emergency', button.getAttribute('data-emergency-contact'));

        if (typeof modal.showModal === 'function') modal.showModal();
    };

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-open-student-details]');
        if (!button) return;
        openModal(button);
    });

    closeBtn?.addEventListener('click', function () {
        if (typeof modal.close === 'function') modal.close();
    });

    const editModal = document.getElementById('student-edit-modal');
    const editFrame = document.getElementById('student-edit-frame');
    const editClose = document.getElementById('student-edit-close');
    const editOpenTab = document.getElementById('student-edit-open-tab');
    const editTitle = document.getElementById('student-edit-title');

    const openEditModal = (url, studentName) => {
        if (!editModal || !editFrame) return;
        editFrame.src = url;
        if (editTitle) {
            editTitle.lastChild.textContent = ` Edit Student${studentName ? ` - ${studentName}` : ''}`;
        }
        editModal.classList.add('open');
        editModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closeEditModal = () => {
        if (!editModal || !editFrame) return;
        editModal.classList.remove('open');
        editModal.setAttribute('aria-hidden', 'true');
        editFrame.removeAttribute('src');
        document.body.style.overflow = '';
    };

    document.addEventListener('click', function (event) {
        const editBtn = event.target.closest('[data-open-edit-modal]');
        if (editBtn) {
            const url = editBtn.getAttribute('data-edit-url') || '';
            const fullUrl = editBtn.getAttribute('data-edit-full-url') || url;
            const studentName = editBtn.getAttribute('data-student-name') || '';
            if (editOpenTab) editOpenTab.href = fullUrl;
            if (url) openEditModal(url, studentName);
            return;
        }

        if (event.target === editModal) {
            closeEditModal();
        }
    });

    editClose?.addEventListener('click', closeEditModal);
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && editModal?.classList.contains('open')) {
            closeEditModal();
        }
    });
})();
</script>
@endpush
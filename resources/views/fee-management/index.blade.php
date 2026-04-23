@extends('layouts.school')

@section('title', 'Fee Management | Pakistan Grammar School')
@section('page_heading', 'Fee Management')

@push('styles')
<style>
    .fm-wrap { display: grid; gap: 12px; }
    .fm-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .fm-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
        font-weight: 800;
        color: #1f3f24;
    }
    .fm-title svg { width: 18px; height: 18px; color: #0f7a35; }

    .panel {
        background: #fff;
        border: 1px solid #d4ead4;
        border-radius: 14px;
        overflow: hidden;
    }
    .panel-head {
        padding: 12px 14px;
        border-bottom: 1px solid #e7f3e7;
        background: #f8fdf8;
        font-weight: 800;
        color: #1f3f24;
    }
    .panel-body { padding: 12px; }

    .btn {
        border: 1px solid #d4ead4;
        border-radius: 9px;
        padding: 9px 12px;
        background: #fff;
        color: #355538;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }
    .btn.primary { background: #0f7a35; color: #fff; border-color: #0f7a35; }
    .btn.danger { border-color: #ffd3d3; color: #9f3131; background: #fff7f7; }
    .btn svg { width: 14px; height: 14px; }

    .table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2eee3; border-radius: 10px; }
    .table { width: 100%; border-collapse: collapse; min-width: 880px; }
    .table th, .table td { text-align: left; padding: 10px 8px; border-top: 1px solid #e8f3e8; font-size: 13px; }
    .table th {
        border-top: 0;
        color: #56735a;
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 700;
        background: #f8fdf8;
    }

    .money { font-weight: 800; color: #17361d; }
    .muted { color: #6a846e; font-size: 12px; }

    .actions { display: inline-flex; align-items: center; gap: 7px; flex-wrap: wrap; }
    .actions form { margin: 0; }

    dialog#feeModal, dialog#editFeeModal {
        border: 1px solid #d4ead4;
        border-radius: 14px;
        padding: 0;
        width: min(640px, 96vw);
    }
    dialog#feeModal::backdrop, dialog#editFeeModal::backdrop {
        background: rgba(15, 40, 20, 0.35);
    }
    .modal-head {
        padding: 12px 14px;
        border-bottom: 1px solid #e7f3e7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .modal-head h4 { margin: 0; font-size: 18px; color: #1f3f24; }
    .modal-body { padding: 14px; }
    .field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .field-grid .field.full { grid-column: span 2; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 11px; font-weight: 700; color: #1d4589; letter-spacing: 1px; text-transform: uppercase; }
    .field input, .field select {
        border: 1px solid #dbe8fb;
        border-radius: 10px;
        padding: 9px 10px;
        font-size: 14px;
        background: #fcfdff;
        width: 100%;
    }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; flex-wrap: wrap; }
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

    @media (max-width: 760px) {
        .field-grid { grid-template-columns: 1fr; }
        .field-grid .field.full { grid-column: auto; }
    }
</style>
@endpush

@section('content')
    @php
        $configured = $classes->filter(fn ($row) => $row->classFee)->count();
        $missing = $classes->count() - $configured;
    @endphp
    <div class="fm-wrap">
        <section class="fm-head">
            <div class="fm-title">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Class-wise Fee Management
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                Fee Structures
                <span class="muted" style="margin-left:8px;">Configured: {{ $configured }} / {{ $classes->count() }} · Missing: {{ $missing }}</span>
            </header>
            <div class="panel-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Monthly Fee</th>
                                <th>Admission Fee</th>
                                <th>Exam Fee</th>
                                <th>Transport Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $classRow)
                                @php($fee = $classRow->classFee)
                                <tr>
                                    <td>
                                        <strong>{{ $classRow->name }}</strong>
                                        <div class="muted">Class ID: {{ $classRow->id }}</div>
                                    </td>
                                    <td class="money">{{ $fee ? 'Rs '.number_format((float) ($fee->monthly_fee ?? $fee->tuition_fee), 2) : '—' }}</td>
                                    <td class="money">{{ $fee ? 'Rs '.number_format((float) ($fee->admission_fee ?? 0), 2) : '—' }}</td>
                                    <td class="money">{{ $fee ? 'Rs '.number_format((float) $fee->exam_fee, 2) : '—' }}</td>
                                    <td class="money">{{ $fee ? 'Rs '.number_format((float) $fee->transport_fee, 2) : '—' }}</td>
                                    <td>
                                        <div class="actions">
                                            @if ($fee)
                                                <button
                                                    type="button"
                                                    class="btn"
                                                    data-open-edit-fee
                                                    data-fee-id="{{ $fee->id }}"
                                                    data-update-url="{{ route('fee-management.update', $fee) }}"
                                                    data-class-name="{{ e($classRow->name) }}"
                                                    data-monthly="{{ number_format((float) ($fee->monthly_fee ?? $fee->tuition_fee), 2, '.', '') }}"
                                                    data-admission="{{ number_format((float) ($fee->admission_fee ?? 0), 2, '.', '') }}"
                                                    data-exam="{{ number_format((float) $fee->exam_fee, 2, '.', '') }}"
                                                    data-transport="{{ number_format((float) $fee->transport_fee, 2, '.', '') }}"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4zM14 6l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('fee-management.destroy', $fee) }}" onsubmit="return confirm('Delete this class fee structure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn danger" type="submit">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2m-8 0l1 12h8l1-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <button
                                                    type="button"
                                                    class="btn primary"
                                                    data-open-create-for-class
                                                    data-class-id="{{ $classRow->id }}"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                    Set Fee
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <dialog id="feeModal">
        <div class="modal-head">
            <h4>Add Class Fee</h4>
            <button class="btn" type="button" id="closeFeeModal">Close</button>
        </div>
        <div class="modal-body">
            @if ($errors->any() && old('_form_mode', 'add') === 'add')
                <div class="error-box">
                    <strong>Please correct the fields below.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('fee-management.store') }}">
                @csrf
                <input type="hidden" name="_form_mode" value="add">
                <div class="field-grid">
                    <div class="field full">
                        <label for="class_id">Class</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">Select class</option>
                            @foreach ($classes->filter(fn ($row) => ! $row->classFee) as $classRow)
                                <option value="{{ $classRow->id }}" @selected((string) old('class_id') === (string) $classRow->id)>
                                    {{ $classRow->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="monthly_fee">Monthly Fee</label>
                        <input id="monthly_fee" type="number" step="0.01" min="0" name="monthly_fee" value="{{ old('monthly_fee') }}" required>
                    </div>
                    <div class="field">
                        <label for="admission_fee">Admission Fee</label>
                        <input id="admission_fee" type="number" step="0.01" min="0" name="admission_fee" value="{{ old('admission_fee') }}" required>
                    </div>
                    <div class="field">
                        <label for="exam_fee">Exam Fee</label>
                        <input id="exam_fee" type="number" step="0.01" min="0" name="exam_fee" value="{{ old('exam_fee') }}" required>
                    </div>
                    <div class="field full">
                        <label for="transport_fee">Transport Fee</label>
                        <input id="transport_fee" type="number" step="0.01" min="0" name="transport_fee" value="{{ old('transport_fee') }}" required>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn" type="button" id="cancelFeeModal">Cancel</button>
                    <button class="btn primary" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Save Fee
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="editFeeModal">
        <div class="modal-head">
            <h4 id="editFeeHeading">Edit Class Fee</h4>
            <button class="btn" type="button" id="closeEditFeeModal">Close</button>
        </div>
        <div class="modal-body">
            @if ($errors->any() && old('_form_mode') === 'edit')
                <div class="error-box">
                    <strong>Please correct the fields below.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('fee-management.update', old('fee_id', 0)) }}" id="editFeeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form_mode" value="edit">
                <input type="hidden" name="fee_id" id="edit_fee_id" value="{{ old('fee_id') }}">
                <div class="field-grid">
                    <div class="field">
                        <label for="edit_monthly_fee">Monthly Fee</label>
                        <input id="edit_monthly_fee" type="number" step="0.01" min="0" name="monthly_fee" value="{{ old('monthly_fee') }}" required>
                    </div>
                    <div class="field">
                        <label for="edit_admission_fee">Admission Fee</label>
                        <input id="edit_admission_fee" type="number" step="0.01" min="0" name="admission_fee" value="{{ old('admission_fee') }}" required>
                    </div>
                    <div class="field">
                        <label for="edit_exam_fee">Exam Fee</label>
                        <input id="edit_exam_fee" type="number" step="0.01" min="0" name="exam_fee" value="{{ old('exam_fee') }}" required>
                    </div>
                    <div class="field full">
                        <label for="edit_transport_fee">Transport Fee</label>
                        <input id="edit_transport_fee" type="number" step="0.01" min="0" name="transport_fee" value="{{ old('transport_fee') }}" required>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn" type="button" id="cancelEditFeeModal">Cancel</button>
                    <button class="btn primary" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5 10 17l9-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Update Fee
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@push('scripts')
<script>
(function () {
    const addModal = document.getElementById('feeModal');
    const editModal = document.getElementById('editFeeModal');
    const openAddBtn = document.getElementById('openFeeModal');
    const closeAddBtn = document.getElementById('closeFeeModal');
    const cancelAddBtn = document.getElementById('cancelFeeModal');
    const closeEditBtn = document.getElementById('closeEditFeeModal');
    const cancelEditBtn = document.getElementById('cancelEditFeeModal');
    const editForm = document.getElementById('editFeeForm');
    const classSelect = document.getElementById('class_id');
    const editHeading = document.getElementById('editFeeHeading');

    const openDialog = (dialog) => {
        if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
    };
    const closeDialog = (dialog) => {
        if (dialog && typeof dialog.close === 'function') dialog.close();
    };

    openAddBtn?.addEventListener('click', () => openDialog(addModal));
    closeAddBtn?.addEventListener('click', () => closeDialog(addModal));
    cancelAddBtn?.addEventListener('click', () => closeDialog(addModal));
    closeEditBtn?.addEventListener('click', () => closeDialog(editModal));
    cancelEditBtn?.addEventListener('click', () => closeDialog(editModal));

    document.addEventListener('click', (event) => {
        const editBtn = event.target.closest('[data-open-edit-fee]');
        if (editBtn && editForm) {
            const setValue = (id, value) => {
                const field = document.getElementById(id);
                if (field) field.value = value || '';
            };
            editForm.setAttribute('action', editBtn.getAttribute('data-update-url') || editForm.getAttribute('action') || '');
            setValue('edit_fee_id', editBtn.getAttribute('data-fee-id'));
            setValue('edit_monthly_fee', editBtn.getAttribute('data-monthly'));
            setValue('edit_admission_fee', editBtn.getAttribute('data-admission'));
            setValue('edit_exam_fee', editBtn.getAttribute('data-exam'));
            setValue('edit_transport_fee', editBtn.getAttribute('data-transport'));
            if (editHeading) {
                editHeading.textContent = `Edit Class Fee - ${editBtn.getAttribute('data-class-name') || ''}`;
            }
            openDialog(editModal);
            return;
        }

        const addForClassBtn = event.target.closest('[data-open-create-for-class]');
        if (addForClassBtn && classSelect) {
            classSelect.value = addForClassBtn.getAttribute('data-class-id') || '';
            openDialog(addModal);
        }
    });

    @if ($errors->any() && old('_form_mode') === 'edit')
        openDialog(editModal);
    @elseif ($errors->any())
        openDialog(addModal);
    @endif
})();
</script>
@endpush


@extends('layouts.school')

@section('title', 'Edit Employee | Pakistan Grammar School')
@section('page_heading', 'Edit Employee')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('staff-salaries.index') }}" class="action-chip" title="Back to payroll" aria-label="Back to payroll">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="header-action-text">Payroll</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d8ead8;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(14, 41, 21, 0.05);
        }

        .head {
            padding: 13px 15px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 19px;
            color: #1f3f24;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fdf9;
        }

        .body {
            padding: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 2px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .field input,
        .field select {
            border: 1px solid #d8e8da;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 14px;
            background: #fdfffd;
        }

        .btn {
            border: 0;
            border-radius: 9px;
            width: 100%;
            padding: 10px;
            background: #0f7a35;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }

        @media (max-width: 760px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <section class="card">
        <header class="head">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4zM14 6l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Edit Employee - {{ $staffMember->employee_code }}
        </header>
        <div class="body">
            <form method="POST" action="{{ route('staff-salaries.employees.update', $staffMember) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="month" value="{{ request('month', now()->format('Y-m')) }}">
                <div class="grid">
                    <div class="field"><label>Name</label><input type="text" name="name" value="{{ old('name', $staffMember->name) }}" required></div>
                    <div class="field"><label>Phone</label><input type="text" name="phone" value="{{ old('phone', $staffMember->contact_number ?? $staffMember->phone) }}" maxlength="11" pattern="03[0-9]{9}" inputmode="numeric" placeholder="03XXXXXXXXX" required></div>
                    <div class="field"><label>Role</label><input type="text" name="role" value="{{ old('role', $staffMember->role ?: $staffMember->designation) }}" required></div>
                    <div class="field"><label>Status</label>
                        <select name="status" required>
                            <option value="active" @selected(old('status', $staffMember->is_active ? 'active' : 'inactive') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $staffMember->is_active ? 'active' : 'inactive') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="field"><label>Monthly Salary</label><input type="number" step="0.01" min="0" name="monthly_salary" value="{{ old('monthly_salary', (float) $staffMember->monthly_wage) }}" required></div>
                    <div class="field"><label>Overtime Rate</label><input type="number" step="0.01" min="0" name="overtime_rate" value="{{ old('overtime_rate', (float) $staffMember->overtime_rate) }}" required></div>
                </div>
                <button class="btn" type="submit">Update Employee</button>
            </form>
        </div>
    </section>
@endsection


@extends('layouts.school')

@section('title', 'Dashboard | Pakistan Grammar School')
@section('page_heading', 'Dashboard Overview')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('invoices.index') }}" class="action-chip primary" title="New invoice" aria-label="New invoice">🧾 <span class="header-action-text">Invoice</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .stat-card {
            padding: 14px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #d4ead4;
        }

        .stat-card small {
            display: block;
            color: #6b8f70;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-card h3 {
            margin: 0 0 2px;
            font-size: 36px;
            line-height: 1;
            color: #1a311f;
            font-weight: 800;
        }

        .stat-card p {
            margin: 0;
            font-size: 13px;
            color: #5f7a62;
            font-weight: 600;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
            gap: 12px;
        }

        .panel-title {
            margin: 0;
            font-size: 20px;
            color: #1f3f24;
            font-weight: 800;
        }

        .collection-card {
            padding: 14px 14px 12px;
        }

        .chart {
            margin-top: 14px;
            height: 185px;
            display: flex;
            gap: 8px;
            align-items: end;
        }

        .bar-wrap {
            flex: 1;
            text-align: center;
        }

        .bar {
            width: 100%;
            background: #78cf8c;
            border-radius: 8px 8px 0 0;
            min-height: 20px;
            display: block;
            transition: opacity 0.2s ease;
        }

        .bar.current {
            background: #1cca54;
        }

        .bar-label {
            margin-top: 8px;
            font-size: 12px;
            color: #66826b;
            font-weight: 600;
        }

        .quick-actions {
            padding: 14px;
        }

        .quick-actions h3 {
            margin: 0 0 12px;
            font-size: 20px;
            color: #1f3f24;
            font-weight: 800;
        }

        .quick-actions a {
            display: block;
            width: 100%;
            margin-bottom: 8px;
            border-radius: 10px;
            text-align: center;
            padding: 10px;
            border: 1px solid #d4ead4;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .qa-primary {
            background: #0f7a35;
            color: #ffffff;
            border-color: #0f7a35;
        }

        .qa-warning {
            background: #d9bc63;
            color: #2f2b1e;
            border-color: #d9bc63;
        }

        .qa-default {
            background: #ffffff;
            color: #2d4c32;
        }

        .progress-card {
            margin-top: 12px;
            border: 1px solid #d4ead4;
            border-radius: 10px;
            padding: 10px;
            background: #f8fff8;
        }

        .progress-track {
            margin-top: 8px;
            height: 8px;
            border-radius: 999px;
            background: #d8ebdb;
            overflow: hidden;
        }

        .progress-fill {
            width: 68%;
            height: 100%;
            background: #0f9a43;
        }

        .activity-card {
            margin-top: 12px;
            padding: 12px;
        }

        .activity-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 8px;
            padding: 10px 0;
            border-top: 1px solid #e8f3e8;
            align-items: center;
        }

        .activity-row:first-of-type {
            border-top: none;
        }

        .status-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-pill.active {
            background: #ddf8e4;
            color: #0f7a35;
        }

        .status-pill.inactive {
            background: #fff2da;
            color: #966113;
        }

        .status-pill.suspended {
            background: #ffe3e3;
            color: #a93b3b;
        }

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card h3 {
                font-size: 30px;
            }

            .activity-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="stats-grid">
        <article class="stat-card">
            <small>Total Students</small>
            <h3>{{ number_format($totalStudents) }}</h3>
            <p>{{ now()->format('Y') }} session</p>
        </article>
        <article class="stat-card">
            <small>Fee Collected ({{ now()->format('M') }})</small>
            <h3>Rs {{ number_format($feeCollected, 0) }}</h3>
            <p>Current month</p>
        </article>
        <article class="stat-card">
            <small>Fee Pending</small>
            <h3>{{ number_format($pendingFees) }}</h3>
            <p>Invoices pending</p>
        </article>
        <article class="stat-card">
            <small>Staff & Teachers</small>
            <h3>{{ number_format($activeStaff) }}</h3>
            <p>{{ $newHires }} new hires</p>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="card collection-card">
            <h3 class="panel-title">Monthly Fee Collection</h3>
            <div class="chart">
                @foreach ($monthlyCollection as $collection)
                    <div class="bar-wrap" title="{{ $collection['month'] }}: Rs {{ number_format($collection['total'], 0) }}">
                        <span class="bar {{ $collection['is_current'] ? 'current' : '' }}" style="height: {{ $collection['height'] }}%;"></span>
                        <div class="bar-label">{{ $collection['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </article>

        <aside class="card quick-actions">
            <h3>Quick Actions</h3>
            <a href="{{ route('students.index') }}" class="qa-primary">Register Student</a>
            <a href="{{ route('fee-vouchers.index', ['tab' => 'generate']) }}" class="qa-warning">Generate Fee Voucher</a>
            <a href="{{ route('invoices.index') }}" class="qa-default">New Invoice</a>
            <a href="{{ route('staff-salaries.index') }}" class="qa-default">Process Salaries</a>

            <div class="progress-card">
                <small style="font-weight: 700; color: #5a7d61;">TERM COMPLETION</small>
                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>
                <div style="margin-top: 7px; font-size: 12px; color: #5b7c60; font-weight: 600;">68% - Spring Term 2026</div>
            </div>
        </aside>
    </section>

    <section class="card activity-card">
        <h3 class="panel-title">Recent Activity</h3>
        @forelse ($recentAdmissions as $student)
            <div class="activity-row">
                <div style="font-weight: 700;">
                    {{ $student->first_name }} {{ $student->last_name }}
                    <span style="display: block; font-weight: 500; color: #6f8570; font-size: 13px;">
                        {{ $student->class_name }}-{{ $student->section }} | Father: {{ $student->father_name }}
                    </span>
                </div>
                <div style="font-size: 13px; color: #607861; font-weight: 600;">
                    {{ optional($student->admission_date)->format('d M Y') }}
                </div>
                <div>
                    <span class="status-pill {{ strtolower($student->status) }}">
                        {{ $student->status }}
                    </span>
                </div>
            </div>
        @empty
            <div style="padding: 14px 0; color: #6d836f;">No student activity yet. Add your first student from Student Registration.</div>
        @endforelse
    </section>
@endsection

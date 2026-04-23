@extends('layouts.school')

@section('title', 'Fee defaulters | Pakistan Grammar School')
@section('page_heading', 'Fee defaulters')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('fee-vouchers.index') }}" class="action-chip" title="Fee workspace">
            <span class="header-action-text">Fee workspace</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .fv-panel { background: #fff; border: 1px solid #d4ead4; border-radius: 14px; overflow: hidden; margin-bottom: 12px; box-shadow: 0 10px 24px -18px rgba(10, 90, 42, 0.35); }
        .fv-panel-head { padding: 10px 12px; border-bottom: 1px solid #e7f3e7; font-weight: 800; color: #1f3f24; display: flex; align-items: center; gap: 8px; background: linear-gradient(180deg, #fbfefb 0%, #f4fbf5 100%); }
        .fv-panel-body { padding: 10px; }
        .fv-filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 8px; align-items: end; }
        .fv-field label { display: block; font-size: 11px; font-weight: 700; color: #1d4589; text-transform: uppercase; margin-bottom: 4px; }
        .fv-field select { width: 100%; border: 1px solid #dbe8fb; border-radius: 9px; padding: 8px 9px; font-size: 13px; }
        .fv-btn { border-radius: 9px; padding: 8px 11px; font-weight: 700; cursor: pointer; border: 1px solid #d4ead4; background: #0f7a35; color: #fff; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
        .table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2eee3; border-radius: 10px; }
        .fv-table { width: 100%; border-collapse: collapse; min-width: 720px; }
        .fv-table th, .fv-table td { text-align: left; padding: 8px 7px; border-top: 1px solid #e8f3e8; font-size: 13px; vertical-align: top; }
        .fv-table th { border-top: none; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #56735a; font-weight: 800; background: #f8fdf8; }
        .fv-table tbody tr:hover { background: #fbfefb; }
        .fv-money { font-weight: 800; color: #16361c; white-space: nowrap; }
        .fv-sub { margin-top: 2px; color: #6a816d; font-size: 11px; font-weight: 700; }
    </style>
@endpush

@section('content')
    <div class="fv-panel">
        <div class="fv-panel-head">Filters</div>
        <div class="fv-panel-body">
            <form method="GET" action="{{ route('fee-vouchers.list.defaulters') }}" class="fv-filters">
                <div class="fv-field">
                    <label for="f_class">Class</label>
                    <select id="f_class" name="class_name">
                        <option value="">All classes</option>
                        @foreach ($classNames as $cn)
                            <option value="{{ $cn }}" @selected($filters['class_name'] === $cn)>{{ $cn }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fv-field">
                    <label>&nbsp;</label>
                    <button type="submit" class="fv-btn">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fv-panel">
        <div class="fv-panel-head">Defaulters ({{ $rows->total() }})</div>
        <div class="fv-panel-body">
            <div class="table-wrap">
                <table class="fv-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Fee status</th>
                            <th>Arrears (on open)</th>
                            <th>Fine (accrued)</th>
                            <th>Total payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>
                                    <div style="font-weight:800;color:#204426;">{{ $row->student->full_name }}</div>
                                    <div class="fv-sub">{{ $row->student->student_code }} · {{ $row->student->class_name }} {{ $row->student->section }}</div>
                                </td>
                                <td>{{ $row->fee_status }}</td>
                                <td class="fv-money">{{ number_format($row->arrears, 2) }}</td>
                                <td class="fv-money">{{ number_format($row->fine, 2) }}</td>
                                <td class="fv-money">{{ number_format($row->total_payable, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:16px;font-weight:700;color:#56735a;">No defaulters right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;">{{ $rows->links() }}</div>
        </div>
    </div>
@endsection

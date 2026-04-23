@extends('layouts.school')

@section('title', 'Alerts | Pakistan Grammar School')
@section('page_heading', 'Alerts & Notifications')

@section('header_actions')
    <div class="header-actions-slot">
        <form method="POST" action="{{ route('alerts.mark-read') }}">
            @csrf
            <button class="action-chip" type="submit" title="Mark all read" aria-label="Mark all read">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="header-action-text">Read all</span>
            </button>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .item {
            border-top: 1px solid #e8f3e8;
            padding: 12px 14px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: center;
        }

        .item:first-child {
            border-top: none;
        }

        .item.unread {
            background: #f5fff7;
        }

        .pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #ddf8e4;
            color: #0f7a35;
        }
    </style>
@endpush

@section('content')
    <section class="panel">
        @forelse ($logs as $log)
            <article class="item {{ is_null($log->read_at) ? 'unread' : '' }}">
                <div>
                    <strong>{{ $log->action }}</strong>
                    <div style="margin-top: 2px;">{{ $log->description }}</div>
                </div>
                <div style="text-align:right;">
                    @if (is_null($log->read_at))
                        <span class="pill">New</span>
                    @endif
                    <div style="font-size:12px; color:#6f8570; margin-top:2px;">{{ $log->created_at->format('d M Y h:i A') }}</div>
                </div>
            </article>
        @empty
            <article class="item">No notifications yet.</article>
        @endforelse
    </section>
    <div class="list-pagination">{{ $logs->links() }}</div>
@endsection

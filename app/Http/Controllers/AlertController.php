<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::latest()->paginate(25);

        return view('alerts.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'logs' => $logs,
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => ActivityLog::whereNull('read_at')->count(),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $updated = ActivityLog::whereNull('read_at')->update([
            'read_at' => now(),
        ]);

        if (! $request->expectsJson()) {
            return redirect()
                ->route('alerts.index')
                ->with('status', "{$updated} notification(s) marked as read.");
        }

        return response()->json([
            'updated' => $updated,
            'count' => 0,
        ]);
    }
}

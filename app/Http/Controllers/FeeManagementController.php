<?php

namespace App\Http\Controllers;

use App\Models\ClassFee;
use App\Models\SchoolClass;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeManagementController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::query()
            ->with('classFee')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('fee-management.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id', 'unique:class_fees,class_id'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'admission_fee' => ['required', 'numeric', 'min:0'],
            'exam_fee' => ['required', 'numeric', 'min:0'],
            'transport_fee' => ['required', 'numeric', 'min:0'],
        ]);

        // Keep legacy field synchronized for compatibility.
        $validated['tuition_fee'] = $validated['monthly_fee'];

        $classFee = ClassFee::create($validated);
        $className = $classFee->schoolClass?->name ?? 'Class';

        ActivityLogger::log(
            'class_fee.created',
            "Fee structure created for {$className}.",
            'class_fee',
            $classFee->id
        );

        return redirect()
            ->route('fee-management.index')
            ->with('status', 'Class fee structure created successfully.');
    }

    public function update(Request $request, ClassFee $classFee): RedirectResponse
    {
        $validated = $request->validate([
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'admission_fee' => ['required', 'numeric', 'min:0'],
            'exam_fee' => ['required', 'numeric', 'min:0'],
            'transport_fee' => ['required', 'numeric', 'min:0'],
        ]);

        // Keep legacy field synchronized for compatibility.
        $validated['tuition_fee'] = $validated['monthly_fee'];

        $classFee->update($validated);
        $className = $classFee->schoolClass?->name ?? 'Class';

        ActivityLogger::log(
            'class_fee.updated',
            "Fee structure updated for {$className}.",
            'class_fee',
            $classFee->id
        );

        return redirect()
            ->route('fee-management.index')
            ->with('status', 'Class fee structure updated successfully.');
    }

    public function destroy(ClassFee $classFee): RedirectResponse
    {
        $className = $classFee->schoolClass?->name ?? 'Class';
        $classFee->delete();

        ActivityLogger::log(
            'class_fee.deleted',
            "Fee structure deleted for {$className}.",
            'class_fee'
        );

        return redirect()
            ->route('fee-management.index')
            ->with('status', 'Class fee structure deleted successfully.');
    }

    public function getFee(int $classId): JsonResponse
    {
        $schoolClass = SchoolClass::query()->with('classFee')->findOrFail($classId);
        $fee = $schoolClass->classFee;

        if (! $fee) {
            return response()->json([
                'found' => false,
                'class_id' => $schoolClass->id,
                'class_name' => $schoolClass->name,
                'message' => 'No fee structure configured for this class.',
            ]);
        }

        return response()->json([
            'found' => true,
            'class_id' => $schoolClass->id,
            'class_name' => $schoolClass->name,
            'monthly_fee' => (float) ($fee->monthly_fee ?? $fee->tuition_fee),
            'admission_fee' => (float) ($fee->admission_fee ?? 0),
            'tuition_fee' => (float) $fee->tuition_fee,
            'exam_fee' => (float) $fee->exam_fee,
            'transport_fee' => (float) $fee->transport_fee,
        ]);
    }
}


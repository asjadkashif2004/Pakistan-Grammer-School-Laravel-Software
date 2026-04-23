<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\ActivityLogger;
use App\Support\FeeVoucherEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $classOptions = $this->classOptions();
        $classMetaByName = $this->classMetaByName($classOptions);

        return view('students.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'classOptions' => $classOptions,
            'classMetaByName' => $classMetaByName,
            'defaultSession' => now()->format('Y').'-'.now()->addYear()->format('y'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStudent($request);

        $discountContext = $this->buildSiblingDiscountContext(
            (string) ($validated['father_cnic'] ?? ''),
            (float) ($validated['monthly_fee'] ?? 0),
            (float) ($validated['admission_fee'] ?? 0),
            (float) ($validated['exam_fee'] ?? 0),
            (float) ($validated['transport_fee'] ?? 0),
            null
        );
        $validated = array_merge($validated, [
            'has_sibling_discount' => $discountContext['eligible'],
            'sibling_discount_percentage' => $discountContext['percentage'],
            'sibling_discount_amount' => $discountContext['discount_amount'],
            'final_payable' => $discountContext['final_payable'],
        ]);

        if (! empty($validated['class_name']) && empty($validated['fee_class_id'])) {
            $validated['fee_class_id'] = SchoolClass::query()
                ->where('name', $validated['class_name'])
                ->value('id');
        }

        $student = DB::transaction(function () use ($validated, $discountContext) {

            // Auto-generate Form Number
            $year = now()->format('Y');
            $lastForm = Student::where('form_number', 'like', "PGS-{$year}-%")
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastForm && $lastForm->form_number) {
                $parts = explode('-', $lastForm->form_number);
                $nextNumber = (int) end($parts) + 1;
            }

            $formNumber = sprintf('PGS-%s-%05d', $year, $nextNumber);
            $validated['form_number'] = $formNumber;

            // Create the student
            $student = Student::create($validated);

            // Generate student_code
            $student->update([
                'student_code' => sprintf('PGS-%05d', $student->id),
            ]);

            // Create initial fee record (first month includes admission fee on challan)
            $firstMonthAmount = max(0, (float) ($discountContext['final_payable'] ?? 0));
            $firstMonthGrossAmount = max(0, (float) ($discountContext['total_fee'] ?? 0));
            $initialVoucher = FeeCollection::create([
                'voucher_number' => null,
                'student_id' => $student->id,
                'amount' => $firstMonthAmount,
                'gross_amount' => $firstMonthGrossAmount,
                'sibling_discount_percentage' => (float) ($discountContext['percentage'] ?? 0),
                'sibling_discount_amount' => (float) ($discountContext['discount_amount'] ?? 0),
                'arrears' => 0,
                'fine' => 0,
                'status' => 'Unpaid',
                'billing_month' => Carbon::parse($validated['admission_date'])->startOfMonth(),
                'due_date' => Carbon::parse($validated['admission_date'])->addMonth()->startOfMonth(),
                'notes' => 'Auto-created on student registration',
                'paid_at' => null,
                'voucher_generated_at' => now(),
            ]);
            $initialVoucher->update([
                'voucher_number' => sprintf('FV-%s-%05d', now()->format('Y'), $initialVoucher->id),
            ]);
            $initialVoucher = $initialVoucher->fresh(['student', 'payments']);
            FeeVoucherEngine::refreshVoucher($initialVoucher);
            $initialVoucher->syncPaymentStatus();

            return $student;
        });

        ActivityLogger::log(
            'student.created',
            "Student {$student->full_name} ({$student->student_code}) registered with Form # {$student->form_number}.",
            'student',
            $student->id
        );

        return redirect()
            ->route('students.roster')
            ->with('status', "Student registered successfully. Form #: {$student->form_number}");
    }

    public function roster(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $classFilter = trim((string) $request->query('class_name', ''));
        $sectionFilter = trim((string) $request->query('section', ''));

        $students = Student::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('student_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('father_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->when($classFilter !== '', fn ($query) => $query->where('class_name', $classFilter))
            ->when($sectionFilter !== '', fn ($query) => $query->where('section', $sectionFilter))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $classFilters = Student::query()
            ->select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        $sectionFilters = Student::query()
            ->select('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');

        return view('students.roster', [
            'todayLabel' => now()->format('l, d F Y'),
            'students' => $students,
            'search' => $search,
            'classFilter' => $classFilter,
            'sectionFilter' => $sectionFilter,
            'classFilters' => $classFilters,
            'sectionFilters' => $sectionFilters,
        ]);
    }

    public function rosterPrint(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $classFilter = trim((string) $request->query('class_name', ''));
        $sectionFilter = trim((string) $request->query('section', ''));

        $students = Student::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('student_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('father_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->when($classFilter !== '', fn ($query) => $query->where('class_name', $classFilter))
            ->when($sectionFilter !== '', fn ($query) => $query->where('section', $sectionFilter))
            ->orderBy('class_name')
            ->orderBy('section')
            ->orderBy('first_name')
            ->limit(500)
            ->get();

        return view('students.roster-print', [
            'students' => $students,
            'todayLabel' => now()->format('l, d F Y'),
            'search' => $search,
            'classFilter' => $classFilter,
            'sectionFilter' => $sectionFilter,
        ]);
    }

    public function show(Student $student): View
    {
        return view('students.show', [
            'todayLabel' => now()->format('l, d F Y'),
            'student' => $student,
        ]);
    }

    public function print(Student $student): View
    {
        return view('students.print', [
            'student' => $student,
        ]);
    }

    public function edit(Request $request, Student $student): View
    {
        if ($request->boolean('embed')) {
            return view('students.edit-embed', [
                'student' => $student,
                'classOptions' => $this->classOptions(),
            ]);
        }

        return view('students.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'student' => $student,
            'classOptions' => $this->classOptions(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $this->validateStudent($request, $student);

        $discountContext = $this->buildSiblingDiscountContext(
            (string) ($validated['father_cnic'] ?? ''),
            (float) ($validated['monthly_fee'] ?? 0),
            (float) ($validated['admission_fee'] ?? 0),
            (float) ($validated['exam_fee'] ?? 0),
            (float) ($validated['transport_fee'] ?? 0),
            $student
        );
        $validated = array_merge($validated, [
            'has_sibling_discount' => $discountContext['eligible'],
            'sibling_discount_percentage' => $discountContext['percentage'],
            'sibling_discount_amount' => $discountContext['discount_amount'],
            'final_payable' => $discountContext['final_payable'],
        ]);
        $student->update($validated);

        ActivityLogger::log(
            'student.updated',
            "Student {$student->full_name} ({$student->student_code}) updated.",
            'student',
            $student->id
        );

        return redirect()
            ->route('students.roster')
            ->with('status', "Student {$student->student_code} updated successfully.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $studentName = $student->full_name;
        $studentCode = $student->student_code;

        DB::transaction(function () use ($student) {
            if ($student->student_photo_path) {
                Storage::disk('public')->delete($student->student_photo_path);
            }
            foreach ([
                'office_bform_file_path',
                'office_father_cnic_file_path',
                'office_result_cards_file_path',
            ] as $pathKey) {
                if ($student->{$pathKey}) {
                    Storage::disk('public')->delete($student->{$pathKey});
                }
            }
            $student->feeCollections()->delete();
            $student->delete();
        });

        ActivityLogger::log(
            'student.deleted',
            "Student {$studentName} ({$studentCode}) deleted.",
            'student'
        );

        return redirect()
            ->route('students.roster')
            ->with('status', 'Student record deleted successfully.');
    }

    public function siblingDiscountPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'father_cnic' => ['nullable', 'string'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],
            'exam_fee' => ['nullable', 'numeric', 'min:0'],
            'transport_fee' => ['nullable', 'numeric', 'min:0'],
            'student_id' => ['nullable', 'integer'],
        ]);

        $student = null;
        if (! empty($validated['student_id'])) {
            $student = Student::query()->find((int) $validated['student_id']);
        }

        $context = $this->buildSiblingDiscountContext(
            $this->normalizeCnic((string) ($validated['father_cnic'] ?? '')) ?? '',
            (float) ($validated['monthly_fee'] ?? 0),
            (float) ($validated['admission_fee'] ?? 0),
            (float) ($validated['exam_fee'] ?? 0),
            (float) ($validated['transport_fee'] ?? 0),
            $student
        );

        return response()->json([
            'eligible' => $context['eligible'],
            'sibling_status' => $context['eligible'] ? 'Yes' : 'No',
            'sibling_count' => $context['sibling_count'],
            'discount_percentage' => $context['percentage'],
            'discount_amount' => $context['discount_amount'],
            'total_fee' => $context['total_fee'],
            'final_payable' => $context['final_payable'],
        ]);
    }

    private function validateStudent(Request $request, ?Student $student = null): array
    {
        $officeChecklist = [
            'office_bform_submitted',
            'office_father_cnic_submitted',
            'office_result_cards_submitted',
            'office_consumable_fee_paid',
            'office_photos_submitted',
            'office_admission_fee_paid',
        ];

        foreach ($officeChecklist as $key) {
            $request->merge([$key => $request->boolean($key)]);
        }

        if ($student) {
            if ($student->office_bform_file_path && ! $request->hasFile('office_bform_file')) {
                $request->merge(['office_bform_submitted' => true]);
            }
            if ($student->office_father_cnic_file_path && ! $request->hasFile('office_father_cnic_file')) {
                $request->merge(['office_father_cnic_submitted' => true]);
            }
            if ($student->office_result_cards_file_path && ! $request->hasFile('office_result_cards_file')) {
                $request->merge(['office_result_cards_submitted' => true]);
            }
        }

        $request->merge([
            'father_cnic' => $this->normalizeCnic($request->input('father_cnic')),
            'contact_number' => $this->normalizePhone($request->input('contact_number')),
            'emergency_contact_number' => $this->normalizePhone($request->input('emergency_contact_number')),
        ]);

        $validated = $request->validate(
            [
                'form_number' => ['nullable', 'string', 'max:30'],
                'first_name' => ['required', 'string', 'max:120'],
                'last_name' => ['required', 'string', 'max:120'],

                // Fixed Date of Birth Validation (as requested)
                'date_of_birth' => [
                    'required',
                    'date',
                    'after_or_equal:1990-01-01',
                    'before_or_equal:2026-12-31',
                ],

                'gender' => ['required', 'in:Male,Female,Other'],
                'class_name' => ['required', Rule::in($this->classOptions())],
                'fee_class_id' => ['nullable', 'exists:school_classes,id'],
                'section' => ['required', 'string', 'max:16'],
                'father_name' => ['required', 'string', 'max:160'],
                'father_occupation' => ['required', 'string', 'max:160'],
                'guardian_name' => ['nullable', 'string', 'max:160'],
                'father_cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d$/'],
                'previous_school' => ['required', 'string', 'max:255'],
                'last_attended_class' => ['required', 'string', 'max:120'],
                'session_label' => ['required', 'string', 'max:50'],
                'contact_number' => ['required', 'regex:/^03\d{2}-\d{7}$/'],
                'emergency_contact_number' => ['required', 'regex:/^03\d{2}-\d{7}$/'],
                'address' => ['required', 'string', 'max:800'],
                'monthly_fee' => ['required', 'numeric', 'min:0'],
                'admission_fee' => ['required', 'numeric', 'min:0'],
                'exam_fee' => ['required', 'numeric', 'min:0'],
                'transport_fee' => ['required', 'numeric', 'min:0'],
                'admission_date' => ['required', 'date'],
                'status' => ['required', 'in:Active,Inactive,Suspended'],
                'student_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                'office_father_cnic_file' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png,webp', 'max:5120'],
                'office_bform_file' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png,webp', 'max:5120'],
                'office_result_cards_file' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png,webp', 'max:5120'],

                // Office checkboxes
                'office_bform_submitted' => ['nullable', 'boolean'],
                'office_father_cnic_submitted' => ['nullable', 'boolean'],
                'office_result_cards_submitted' => ['nullable', 'boolean'],
                'office_consumable_fee_paid' => ['nullable', 'boolean'],
                'office_photos_submitted' => ['nullable', 'boolean'],
                'office_admission_fee_paid' => ['nullable', 'boolean'],
            ],
            [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'date_of_birth.required' => 'Date of birth is required.',
                'date_of_birth.after_or_equal' => 'Date of birth must be on or after 1st January 1990.',
                'date_of_birth.before_or_equal' => 'Date of birth cannot be after 31st December 2026.',
                'gender.required' => 'Please select a gender.',
                'class_name.required' => 'Please select a class.',
                'class_name.in' => 'Please select a valid class from the list.',
                'section.required' => 'Section is required.',
                'father_name.required' => "Father's name is required.",
                'father_occupation.required' => "Father's occupation is required.",
                'father_cnic.required' => "Father's CNIC is required.",
                'father_cnic.regex' => "Father's CNIC must be in 12345-1234567-1 format.",
                'previous_school.required' => 'Previous school name is required.',
                'last_attended_class.required' => 'Last attended class is required.',
                'session_label.required' => 'Session is required.',
                'contact_number.required' => 'Contact number is required.',
                'contact_number.regex' => 'Contact number must be in Pakistani format (03XX-XXXXXXX).',
                'emergency_contact_number.required' => 'Emergency contact number is required.',
                'emergency_contact_number.regex' => 'Emergency contact must be in Pakistani format (03XX-XXXXXXX).',
                'address.required' => 'Address is required.',
                'monthly_fee.required' => 'Monthly fee is required.',
                'monthly_fee.numeric' => 'Monthly fee must be a valid number.',
                'monthly_fee.min' => 'Monthly fee cannot be negative.',
                'admission_fee.required' => 'Admission fee is required.',
                'admission_fee.numeric' => 'Admission fee must be a valid number.',
                'admission_fee.min' => 'Admission fee cannot be negative.',
                'exam_fee.required' => 'Exam fee is required.',
                'exam_fee.numeric' => 'Exam fee must be a valid number.',
                'exam_fee.min' => 'Exam fee cannot be negative.',
                'transport_fee.required' => 'Transport fee is required.',
                'transport_fee.numeric' => 'Transport fee must be a valid number.',
                'transport_fee.min' => 'Transport fee cannot be negative.',
                'admission_date.required' => 'Admission date is required.',
                'status.required' => 'Please select a status.',
                'student_photo.mimes' => 'Student photo must be JPG, PNG, or WEBP format.',
                'student_photo.max' => 'Student photo must not exceed 5MB.',
                'office_father_cnic_file.max' => 'Father CNIC document must not exceed 5MB.',
                'office_father_cnic_file.mimes' => 'Father CNIC document must be PDF, JPG, PNG, or WEBP.',
                'office_bform_file.max' => 'Birth certificate / B-Form file must not exceed 5MB.',
                'office_bform_file.mimes' => 'Birth certificate / B-Form must be PDF, JPG, PNG, or WEBP.',
                'office_result_cards_file.max' => 'Result cards file must not exceed 5MB.',
                'office_result_cards_file.mimes' => 'Result cards must be PDF, JPG, PNG, or WEBP.',
            ]
        );

        if ($request->hasFile('office_father_cnic_file')) {
            if ($student?->office_father_cnic_file_path) {
                Storage::disk('public')->delete($student->office_father_cnic_file_path);
            }
            $validated['office_father_cnic_file_path'] = $request->file('office_father_cnic_file')->store('students/office/father-cnic', 'public');
            $validated['office_father_cnic_submitted'] = true;
        }
        if ($request->hasFile('office_bform_file')) {
            if ($student?->office_bform_file_path) {
                Storage::disk('public')->delete($student->office_bform_file_path);
            }
            $validated['office_bform_file_path'] = $request->file('office_bform_file')->store('students/office/bform', 'public');
            $validated['office_bform_submitted'] = true;
        }
        if ($request->hasFile('office_result_cards_file')) {
            if ($student?->office_result_cards_file_path) {
                Storage::disk('public')->delete($student->office_result_cards_file_path);
            }
            $validated['office_result_cards_file_path'] = $request->file('office_result_cards_file')->store('students/office/result-cards', 'public');
            $validated['office_result_cards_submitted'] = true;
        }

        // Security/business rule:
        // These document checkboxes must reflect real uploaded documents only,
        // not manual checkbox toggles.
        $validated['office_bform_submitted'] = $request->hasFile('office_bform_file')
            || (bool) ($student?->office_bform_file_path);
        $validated['office_father_cnic_submitted'] = $request->hasFile('office_father_cnic_file')
            || (bool) ($student?->office_father_cnic_file_path);
        $validated['office_result_cards_submitted'] = $request->hasFile('office_result_cards_file')
            || (bool) ($student?->office_result_cards_file_path);

        unset(
            $validated['office_father_cnic_file'],
            $validated['office_bform_file'],
            $validated['office_result_cards_file'],
        );

        // Handle Student Photo
        if ($request->hasFile('student_photo')) {
            if ($student?->student_photo_path) {
                Storage::disk('public')->delete($student->student_photo_path);
            }
            $validated['student_photo_path'] = $request->file('student_photo')->store('students/photos', 'public');
        }

        return $validated;
    }

    private function classOptions(): array
    {
        $fallback = [
            'KG 1', 'KG 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4',
            'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
        ];

        try {
            $fromDb = SchoolClass::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name')
                ->all();

            return ! empty($fromDb) ? $fromDb : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function classMetaByName(array $classOptions): array
    {
        try {
            return SchoolClass::query()
                ->with('classFee')
                ->whereIn('name', $classOptions)
                ->get()
                ->mapWithKeys(function (SchoolClass $schoolClass) {
                    return [
                        $schoolClass->name => [
                            'id' => $schoolClass->id,
                            'has_fee' => (bool) $schoolClass->classFee,
                        ],
                    ];
                })
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function buildSiblingDiscountContext(
        string $fatherCnic,
        float $monthlyFee,
        float $admissionFee,
        float $examFee,
        float $transportFee,
        ?Student $student = null
    ): array {
        $normalizedCnic = $this->normalizeCnic($fatherCnic) ?? '';
        $totalFee = max(0, round($monthlyFee + $admissionFee + $examFee + $transportFee, 2));

        if ($normalizedCnic === '') {
            return [
                'sibling_count' => 1,
                'eligible' => false,
                'percentage' => 0.0,
                'discount_amount' => 0.0,
                'total_fee' => $totalFee,
                'final_payable' => $totalFee,
            ];
        }

        $siblingsQuery = Student::query()->where('father_cnic', $normalizedCnic);
        if ($student?->exists) {
            $siblingsQuery->where('id', '!=', $student->id);
        }
        $existingSiblings = (int) $siblingsQuery->count();
        $siblingCount = $existingSiblings + 1;
        $eligible = $siblingCount > 1;
        $percentage = $eligible ? 10.0 : 0.0;
        $discountAmount = $eligible ? round($totalFee * 0.10, 2) : 0.0;
        $finalPayable = round(max(0, $totalFee - $discountAmount), 2);

        return [
            'sibling_count' => $siblingCount,
            'eligible' => $eligible,
            'percentage' => $percentage,
            'discount_amount' => $discountAmount,
            'total_fee' => $totalFee,
            'final_payable' => $finalPayable,
        ];
    }

    private function normalizeCnic(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = substr(preg_replace('/\D+/', '', $value ?? ''), 0, 13);
        if (strlen($digits) !== 13) {
            return trim($value);
        }

        return sprintf('%s-%s-%s', substr($digits, 0, 5), substr($digits, 5, 7), substr($digits, 12, 1));
    }

    private function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = substr(preg_replace('/\D+/', '', $value ?? ''), 0, 11);
        if (strlen($digits) !== 11 || ! str_starts_with($digits, '03')) {
            return trim($value);
        }

        return sprintf('%s-%s', substr($digits, 0, 4), substr($digits, 4, 7));
    }
}

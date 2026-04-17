<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\Student;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $students = Student::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('student_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'students' => $students,
            'search' => $search,
            'classOptions' => $this->classOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStudent($request);

        $student = DB::transaction(function () use ($validated) {
            $student = Student::create($validated);
            $student->update([
                'student_code' => sprintf('PGS-%05d', $student->id),
            ]);

            FeeCollection::create([
                'voucher_number' => sprintf('FV-%s-%05d', now()->format('Y'), $student->id),
                'student_id' => $student->id,
                'amount' => $validated['monthly_fee'],
                'arrears' => 0,
                'fine' => 0,
                'discount' => 0,
                'status' => 'Pending',
                'billing_month' => Carbon::parse($validated['admission_date'])->startOfMonth(),
                'due_date' => Carbon::parse($validated['admission_date'])->addMonth()->startOfMonth(),
                'notes' => 'Auto-created on student registration',
                'paid_at' => null,
            ]);
            return $student;
        });
        ActivityLogger::log(
            'student.created',
            "Student {$student->full_name} ({$student->student_code}) registered.",
            'student',
            $student->id
        );

        return redirect()
            ->route('students.index')
            ->with('status', 'Student registered successfully.');
    }

    public function edit(Student $student): View
    {
        return view('students.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'student' => $student,
            'classOptions' => $this->classOptions(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $this->validateStudent($request);
        $student->update($validated);
        ActivityLogger::log(
            'student.updated',
            "Student {$student->full_name} ({$student->student_code}) updated.",
            'student',
            $student->id
        );

        return redirect()
            ->route('students.index')
            ->with('status', "Student {$student->student_code} updated successfully.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $studentName = $student->full_name;
        $studentCode = $student->student_code;
        DB::transaction(function () use ($student) {
            $student->feeCollections()->delete();
            $student->delete();
        });
        ActivityLogger::log(
            'student.deleted',
            "Student {$studentName} ({$studentCode}) deleted.",
            'student'
        );

        return redirect()
            ->route('students.index')
            ->with('status', 'Student record deleted successfully.');
    }

    private function validateStudent(Request $request): array
    {
        $request->merge([
            'father_cnic' => $this->normalizeCnic($request->input('father_cnic')),
            'contact_number' => $this->normalizePhone($request->input('contact_number')),
        ]);

        return $request->validate(
            [
                'first_name' => ['required', 'string', 'max:120'],
                'last_name' => ['required', 'string', 'max:120'],
                'date_of_birth' => ['required', 'date'],
                'gender' => ['required', 'in:Male,Female,Other'],
                'class_name' => ['required', Rule::in($this->classOptions())],
                'section' => ['required', 'string', 'max:16'],
                'father_name' => ['required', 'string', 'max:160'],
                'father_cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d$/'],
                'contact_number' => ['required', 'regex:/^03\d{2}-\d{7}$/'],
                'address' => ['required', 'string', 'max:800'],
                'monthly_fee' => ['required', 'numeric', 'min:0'],
                'admission_fee' => ['required', 'numeric', 'min:0'],
                'admission_date' => ['required', 'date'],
                'status' => ['required', 'in:Active,Inactive,Suspended'],
            ],
            [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'date_of_birth.required' => 'Date of birth is required.',
                'gender.required' => 'Please select a gender.',
                'class_name.required' => 'Please select a class.',
                'class_name.in' => 'Please select a valid class from the list.',
                'section.required' => 'Section is required.',
                'father_name.required' => "Father's name is required.",
                'father_cnic.required' => "Father's CNIC is required.",
                'father_cnic.regex' => "Father's CNIC must be in 12345-1234567-1 format.",
                'contact_number.required' => 'Contact number is required.',
                'contact_number.regex' => 'Contact number must be in Pakistani format (03XX-XXXXXXX).',
                'address.required' => 'Address is required.',
                'monthly_fee.required' => 'Monthly fee is required.',
                'monthly_fee.numeric' => 'Monthly fee must be a valid number.',
                'monthly_fee.min' => 'Monthly fee cannot be negative.',
                'admission_fee.required' => 'Admission fee is required.',
                'admission_fee.numeric' => 'Admission fee must be a valid number.',
                'admission_fee.min' => 'Admission fee cannot be negative.',
                'admission_date.required' => 'Admission date is required.',
                'status.required' => 'Please select a status.',
            ]
        );
    }

    private function classOptions(): array
    {
        return [
            'KG 1',
            'KG 2',
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6',
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
        ];
    }

    private function normalizeCnic(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = substr(preg_replace('/\D+/', '', $value) ?? '', 0, 13);
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

        $digits = substr(preg_replace('/\D+/', '', $value) ?? '', 0, 11);
        if (strlen($digits) !== 11 || !str_starts_with($digits, '03')) {
            return trim($value);
        }

        return sprintf('%s-%s', substr($digits, 0, 4), substr($digits, 4, 7));
    }
}

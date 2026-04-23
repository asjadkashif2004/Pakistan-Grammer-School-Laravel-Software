<?php

namespace Database\Seeders;

use App\Models\FeeCollection;
use App\Models\FeeVoucherPayment;
use App\Models\Student;
use App\Support\FeeChallanPresenter;
use App\Support\FeeVoucherEngine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Demo dataset for fee automation (fines, arrears carry, defaulters, siblings).
 * Run: php artisan db:seed --class=DemoFeeAutomationSeeder
 */
class DemoFeeAutomationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->cleanup();

            $mFar = Carbon::now()->subMonths(6)->format('Y-m');
            $mOld = Carbon::now()->subMonths(4)->format('Y-m');
            $mPrev = Carbon::now()->subMonths(3)->format('Y-m');
            $mLate = Carbon::now()->subMonths(2)->format('Y-m');
            $mRecent = Carbon::now()->subMonths(1)->format('Y-m');

            $siblingCnicA = '35202-1111111-1';
            $siblingCnicB = '35202-2222222-1';

            $students = [];

            for ($i = 1; $i <= 20; $i++) {
                $students[$i] = $this->makeStudent($i, $this->pickCnic($i, $siblingCnicA, $siblingCnicB));
            }

            // 1–2: Fully paid (single voucher, paid in full)
            foreach ([1, 2] as $idx) {
                $v = $this->issueVoucher($students[$idx], $mOld, Carbon::parse($mOld.'-10'), 0);
                FeeVoucherPayment::create([
                    'fee_collection_id' => $v->id,
                    'amount' => FeeVoucherEngine::finalPayable($v->fresh(['student'])),
                    'paid_at' => Carbon::parse($mOld.'-12')->toDateString(),
                    'notes' => 'Demo: full payment',
                ]);
                $v->syncPaymentStatus();
            }

            // 3: Arrears-heavy current month (due still in future → mostly arrears line, low fine)
            $this->issueVoucher($students[3], $mRecent, Carbon::now()->addDays(5), 12000);

            // 4–6: Fine + overdue (due long past, unpaid)
            foreach ([4, 5, 6] as $idx) {
                $this->issueVoucher($students[$idx], $mLate, Carbon::parse($mLate.'-05'), 500);
            }

            // 7–11: Active defaulters (billing month ended, still unpaid)
            foreach (range(7, 11) as $idx) {
                $this->issueVoucher($students[$idx], $mPrev, Carbon::parse($mPrev.'-08'), 0);
            }

            // 12–13: Cleared defaulters (were overdue pattern then paid)
            foreach ([12, 13] as $idx) {
                $v = $this->issueVoucher($students[$idx], $mOld, Carbon::parse($mOld.'-06'), 1500);
                FeeVoucherPayment::create([
                    'fee_collection_id' => $v->id,
                    'amount' => FeeVoucherEngine::finalPayable($v->fresh(['student'])),
                    'paid_at' => Carbon::parse($mOld.'-20')->toDateString(),
                    'notes' => 'Demo: cleared',
                ]);
                $v->syncPaymentStatus();
            }

            // 14: Arrears carry: old month unpaid → rolled into newer voucher
            $jan = $this->issueVoucher($students[14], $mFar, Carbon::parse($mFar.'-07'), 0);
            $carry = FeeVoucherEngine::previewRemaining($jan->fresh(['student', 'payments']));
            $feb = $this->issueVoucher($students[14], $mOld, Carbon::parse($mOld.'-10'), max(0, $carry));
            $jan->update(['rolled_into_fee_collection_id' => $feb->id]);
            FeeVoucherEngine::refreshVoucher($feb->fresh(['student', 'payments']));
            $feb->syncPaymentStatus();

            // 15: Partial pay + ongoing fine
            $v15 = $this->issueVoucher($students[15], $mLate, Carbon::parse($mLate.'-04'), 2000);
            FeeVoucherPayment::create([
                'fee_collection_id' => $v15->id,
                'amount' => 3000,
                'paid_at' => Carbon::parse($mLate.'-09')->toDateString(),
                'notes' => 'Demo: partial',
            ]);
            $v15->syncPaymentStatus();

            // 16: Unpaid + sibling discount (same CNIC as 17–18)
            $this->issueVoucher($students[16], $mLate, Carbon::parse($mLate.'-06'), 0);

            // 17–18: Sibling pair (same CNIC), unpaid overdue
            $this->issueVoucher($students[17], $mLate, Carbon::parse($mLate.'-06'), 0);
            $this->issueVoucher($students[18], $mRecent, Carbon::parse($mRecent.'-07'), 0);

            // 19–20: Second sibling cluster, mixed
            $v19 = $this->issueVoucher($students[19], $mPrev, Carbon::parse($mPrev.'-09'), 0);
            FeeVoucherPayment::create([
                'fee_collection_id' => $v19->id,
                'amount' => FeeVoucherEngine::finalPayable($v19->fresh(['student'])) / 2,
                'paid_at' => Carbon::parse($mPrev.'-15')->toDateString(),
                'notes' => 'Demo: half pay',
            ]);
            $v19->syncPaymentStatus();
            $this->issueVoucher($students[20], $mLate, Carbon::parse($mLate.'-05'), 800);
        });

        FeeCollection::syncPastDueStatuses();
    }

    private function cleanup(): void
    {
        $codes = [];
        for ($i = 1; $i <= 20; $i++) {
            $codes[] = 'DEMOFEE'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
        }

        $ids = Student::query()->whereIn('student_code', $codes)->pluck('id');
        if ($ids->isNotEmpty()) {
            FeeCollection::query()->whereIn('student_id', $ids)->delete();
            Student::query()->whereIn('id', $ids)->delete();
        }
    }

    private function pickCnic(int $i, string $a, string $b): string
    {
        if (in_array($i, [16, 17, 18], true)) {
            return $a;
        }
        if (in_array($i, [19, 20], true)) {
            return $b;
        }

        return sprintf('35202-%07d-1', 3000000 + $i);
    }

    private function makeStudent(int $i, string $fatherCnic): Student
    {
        $code = 'DEMOFEE'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
        $admissionMonth = Carbon::now()->subMonths(6)->startOfMonth();

        return Student::query()->create([
            'student_code' => $code,
            'form_number' => 'DEMO-FORM-'.$i,
            'first_name' => 'Demo',
            'last_name' => 'Fee'.$i,
            'date_of_birth' => '2015-06-15',
            'gender' => 'Male',
            'class_name' => 'Grade 1',
            'fee_class_id' => null,
            'section' => 'A',
            'father_name' => 'Demo Father '.$i,
            'father_occupation' => 'Private',
            'guardian_name' => null,
            'father_cnic' => $fatherCnic,
            'previous_school' => 'Demo School',
            'last_attended_class' => 'KG',
            'session_label' => now()->format('Y').'-'.now()->addYear()->format('y'),
            'contact_number' => '0300-'.str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
            'emergency_contact_number' => '0301-'.str_pad((string) (2000000 + $i), 7, '0', STR_PAD_LEFT),
            'student_photo_path' => null,
            'address' => 'Demo address',
            'monthly_fee' => 5000,
            'admission_fee' => 8000,
            'exam_fee' => 0,
            'transport_fee' => 0,
            'sibling_discount_percentage' => 0,
            'sibling_discount_amount' => 0,
            'final_payable' => 0,
            'has_sibling_discount' => false,
            'is_defaulter' => false,
            'office_bform_submitted' => false,
            'office_father_cnic_submitted' => false,
            'office_result_cards_submitted' => false,
            'office_consumable_fee_paid' => false,
            'office_photos_submitted' => false,
            'office_admission_fee_paid' => false,
            'admission_date' => $admissionMonth->toDateString(),
            'status' => 'Active',
        ]);
    }

    private function issueVoucher(Student $student, string $billingYm, Carbon $dueDate, float $arrears): FeeCollection
    {
        $billingStart = Carbon::createFromFormat('Y-m', $billingYm)->startOfMonth();
        $admissionPart = FeeChallanPresenter::isAdmissionBillingMonth($student, $billingStart)
            ? (float) ($student->admission_fee ?? 0)
            : 0.0;
        $grossAmount = max(0, $admissionPart + (float) $student->monthly_fee + $arrears);

        $v = FeeCollection::query()->create([
            'voucher_number' => null,
            'student_id' => $student->id,
            'amount' => 0,
            'gross_amount' => $grossAmount,
            'sibling_discount_percentage' => 0,
            'sibling_discount_amount' => 0,
            'arrears' => $arrears,
            'fine' => 0,
            'status' => 'Unpaid',
            'billing_month' => $billingStart,
            'due_date' => $dueDate,
            'notes' => 'DemoFeeAutomationSeeder',
            'paid_at' => null,
            'voucher_generated_at' => now(),
            'rolled_into_fee_collection_id' => null,
        ]);

        $v->update([
            'voucher_number' => sprintf('FV-DEMO-%05d', $v->id),
        ]);

        $v = $v->fresh(['student', 'payments']);
        FeeVoucherEngine::refreshVoucher($v);
        $v->syncPaymentStatus();

        return $v->fresh(['student', 'payments']);
    }
}

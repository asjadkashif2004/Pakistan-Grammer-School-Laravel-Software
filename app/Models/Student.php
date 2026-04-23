<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'form_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'class_name',
        'fee_class_id',
        'section',
        'father_name',
        'father_occupation',
        'guardian_name',
        'father_cnic',
        'previous_school',
        'last_attended_class',
        'session_label',
        'contact_number',
        'emergency_contact_number',
        'student_photo_path',
        'address',
        'monthly_fee',
        'admission_fee',
        'exam_fee',
        'transport_fee',
        'sibling_discount_percentage',
        'sibling_discount_amount',
        'final_payable',
        'has_sibling_discount',
        'is_defaulter',
        'office_bform_submitted',
        'office_bform_file_path',
        'office_father_cnic_submitted',
        'office_father_cnic_file_path',
        'office_result_cards_submitted',
        'office_result_cards_file_path',
        'office_consumable_fee_paid',
        'office_photos_submitted',
        'office_admission_fee_paid',
        'admission_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'admission_date' => 'date',
            'monthly_fee' => 'decimal:2',
            'admission_fee' => 'decimal:2',
            'exam_fee' => 'decimal:2',
            'transport_fee' => 'decimal:2',
            'sibling_discount_percentage' => 'decimal:2',
            'sibling_discount_amount' => 'decimal:2',
            'final_payable' => 'decimal:2',
            'has_sibling_discount' => 'boolean',
            'is_defaulter' => 'boolean',
            'office_bform_submitted' => 'boolean',
            'office_father_cnic_submitted' => 'boolean',
            'office_result_cards_submitted' => 'boolean',
            'office_consumable_fee_paid' => 'boolean',
            'office_photos_submitted' => 'boolean',
            'office_admission_fee_paid' => 'boolean',
        ];
    }

    public function feeCollections(): HasMany
    {
        return $this->hasMany(FeeCollection::class);
    }

    public function feeClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'fee_class_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}

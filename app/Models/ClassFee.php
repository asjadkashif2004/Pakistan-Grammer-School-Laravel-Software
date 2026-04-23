<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'tuition_fee',
        'monthly_fee',
        'admission_fee',
        'exam_fee',
        'transport_fee',
    ];

    protected function casts(): array
    {
        return [
            'tuition_fee' => 'decimal:2',
            'monthly_fee' => 'decimal:2',
            'admission_fee' => 'decimal:2',
            'exam_fee' => 'decimal:2',
            'transport_fee' => 'decimal:2',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}


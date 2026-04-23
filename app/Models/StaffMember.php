<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'cnic',
        'name',
        'phone',
        'contact_number',
        'role',
        'designation',
        'monthly_wage',
        'overtime_rate',
        'joining_date',
        'payment_method',
        'bank_name',
        'branch_code',
        'iban',
        'account_title',
        'account_number',
        'online_wallet_type',
        'online_wallet_number',
        'hired_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
            'joining_date' => 'date',
            'monthly_wage' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function payrollTransactions(): HasMany
    {
        return $this->hasMany(PayrollTransaction::class);
    }
}

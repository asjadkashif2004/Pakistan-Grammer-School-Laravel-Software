<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'contact_number' => ['required', 'regex:/^03\d{2}-\d{7}$/'],
            'cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d$/', 'unique:staff_members,cnic'],
            'designation' => ['nullable', 'string', 'max:120'],
            'monthly_wage' => ['required', 'numeric', 'min:0'],
            'joining_date' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'payment_method' => ['required', Rule::in(['bank', 'wallet'])],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'branch_code' => ['nullable', 'regex:/^\d{3,10}$/'],
            'iban' => ['nullable', 'regex:/^PK[A-Z0-9]{22}$/'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'online_wallet_type' => ['nullable', Rule::in(['easypaisa', 'jazzcash'])],
            'online_wallet_number' => ['nullable', 'regex:/^03\d{2}-\d{7}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnic' => $this->normalizeCnic($this->input('cnic')),
            'contact_number' => $this->normalizePhone($this->input('contact_number')),
            'online_wallet_number' => $this->normalizePhone($this->input('online_wallet_number')),
            'iban' => $this->normalizeIban($this->input('iban')),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $paymentMethod = $this->input('payment_method');

            if ($paymentMethod === 'bank') {
                if (! $this->filled('bank_name')) {
                    $validator->errors()->add('bank_name', 'Bank name is required for bank transfer.');
                }
                if (! $this->filled('branch_code')) {
                    $validator->errors()->add('branch_code', 'Branch code is required for bank transfer.');
                }
                if (! $this->filled('iban')) {
                    $validator->errors()->add('iban', 'IBAN is required for bank transfer.');
                }
            }

            if ($paymentMethod === 'wallet') {
                if (! $this->filled('online_wallet_type')) {
                    $validator->errors()->add('online_wallet_type', 'Wallet type is required for online wallet.');
                }
                if (! $this->filled('online_wallet_number')) {
                    $validator->errors()->add('online_wallet_number', 'Wallet number is required for online wallet.');
                }
            }
        });
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
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = substr(preg_replace('/\D+/', '', $value) ?? '', 0, 11);
        if (strlen($digits) !== 11 || !str_starts_with($digits, '03')) {
            return trim($value);
        }

        return sprintf('%s-%s', substr($digits, 0, 4), substr($digits, 4, 7));
    }

    private function normalizeIban(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return strtoupper(preg_replace('/\s+/', '', $value) ?? '');
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayPayrollRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(['bank', 'wallet'])],
            'wallet_type' => ['nullable', Rule::in(['easypaisa', 'jazzcash'])],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'branch_code' => ['nullable', 'regex:/^\d{3,10}$/'],
            'iban' => ['nullable', 'regex:/^PK[A-Z0-9]{22}$/'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'payment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $method = $this->input('payment_method');
            if ($method === 'bank') {
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

            if ($method === 'wallet') {
                if (! $this->filled('wallet_type')) {
                    $validator->errors()->add('wallet_type', 'Wallet type is required for online wallet.');
                }
                if (! $this->filled('account_number')) {
                    $validator->errors()->add('account_number', 'Wallet number is required for online wallet.');
                }
            }
        });
    }
}


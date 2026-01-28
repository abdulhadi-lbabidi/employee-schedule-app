<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'sometimes|exists:employees,id',
            'admin_id' => 'sometimes|exists:admins,id',
            'amount' => 'sometimes|numeric',
            'paid_amount' => 'sometimes|numeric',
            'role' => 'sometimes|in:قيد الانتظار , مدفوعة جزئياً, مسددة بالكامل',
            'date' => 'sometimes|date',
        ];
    }
}

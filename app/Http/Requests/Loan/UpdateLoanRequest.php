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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id'=>'required|exists:employees,id',
            'amount'=>'required|numeric',
            'paid_amount'=>'required|numeric',
            'role'=>'required|in:قيد الانتظار , مدفوعة جزئياً, مسددة بالكامل',
            'date'=>'required|date',
        ];
    }
}

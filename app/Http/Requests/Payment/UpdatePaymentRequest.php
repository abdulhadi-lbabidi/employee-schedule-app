<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'week_number' => 'sometimes|integer|min:1|max:53',
            'total_amount' => 'sometimes|numeric|min:0',
            'amount_paid' => 'sometimes|numeric|min:0',
        ];
    }
}
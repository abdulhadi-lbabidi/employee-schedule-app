<?php

namespace App\Http\Requests\WeeklyHistory;

use Illuminate\Foundation\Http\FormRequest;

class CreateWeeklyHistoryRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'workshop_id' => 'required|exists:workshops,id',
            'week_number' => 'required|integer|min:1|max:53',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'amount_paid' => 'required|integer|min:0',
            'is_paid' => 'required|boolean',
        ];
    }
}

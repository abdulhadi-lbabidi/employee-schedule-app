<?php

namespace App\Http\Requests\WeeklyHistory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWeeklyHistoryRequest extends FormRequest
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
      'workshop_id' => 'sometimes|exists:workshops,id',
      'week_number' => 'sometimes|integer|min:1|max:53',
      'month' => 'sometimes|integer|min:1|max:12',
      'year' => 'sometimes|integer',
      'amount_paid' => 'sometimes|integer|min:0',
      'is_paid' => 'sometimes|boolean',
    ];
  }
}

<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
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
      'admin_id' => 'sometimes|exists:admins,id',
      'amount' => 'sometimes|integer|min:1',
      'reason' => 'sometimes|string|max:255',
      'date_issued' => 'sometimes|date',
    ];
  }
}
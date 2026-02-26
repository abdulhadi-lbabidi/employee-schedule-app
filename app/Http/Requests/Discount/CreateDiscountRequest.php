<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
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
      'admin_id' => 'required|exists:admins,id',
      'amount' => 'required|integer|min:1',
      'reason' => 'required|string|max:255',
      'date_issued' => 'required|date',
    ];
  }
}
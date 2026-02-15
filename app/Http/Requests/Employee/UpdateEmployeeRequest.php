<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
      'full_name' => 'sometimes|string|max:255',
      'phone_number' => 'sometimes|unique:users,phone_number,' . $this->user,
      'password' => 'sometimes|min:6',

      'position' => 'sometimes|string',
      'department' => 'sometimes|string',
      'hourly_rate' => 'sometimes|numeric',
      'overtime_rate' => 'sometimes|numeric',
      'is_online' => 'sometimes|boolean',
      'current_location' => 'sometimes|string',
    ];
  }
}

<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
      'full_name' => 'required|string|max:255',
      'phone_number' => 'required|unique:users,phone_number',
      'password' => 'required|min:6',
      'email' => 'nullable|email|unique:users,email',

      'position' => 'required|string',
      'department' => 'required|string',
      'hourly_rate' => 'required|numeric',
      'overtime_rate' => 'required|numeric',
      'is_online' => 'boolean',
      'current_location' => 'required|string',
    ];
  }
}

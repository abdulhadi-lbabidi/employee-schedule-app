<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
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
      'email' => 'sometimes|email|unique:users,email,' . $this->user,
      'password' => 'sometimes|min:6',

      'name' => 'sometimes|string|max:255',
    ];
  }
}

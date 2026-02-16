<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
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
      '*.employee_id' => 'required|exists:employees,id',
      '*.workshop_id' => 'required|exists:workshops,id',
      '*.date' => 'required|date',
      '*.check_in' => 'required|date_format:H:i:s',
      '*.check_out' => 'nullable|date_format:H:i:s',
      '*.regular_hours' => 'required|numeric',
      '*.overtime_hours' => 'required|numeric',
      '*.note' => 'nullable|string',
      '*.status' => 'required|in:مؤرشف,قيد الرفع',
    ];
  }

}

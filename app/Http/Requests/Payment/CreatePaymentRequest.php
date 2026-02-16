<?php

namespace App\Http\Requests\Payment;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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

      'attendance_ids' => 'required|array|min:1',
      'attendance_ids.*' => [
        'exists:attendances,id',
        function ($attribute, $value, $fail) {
          $attendance = Attendance::find($value);
          if ($attendance && $attendance->employee_id != $this->employee_id) {
            $fail('أحد سجلات الحضور المختارة لا ينتمي لهذا الموظف.');
          }
        },
      ],

      'total_amount' => 'required|numeric|min:0',
      'amount_paid' => 'required|numeric|min:0',

      'payment_date' => 'required|date',

    ];
  }
}
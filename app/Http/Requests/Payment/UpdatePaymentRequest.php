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
      'total_amount' => 'sometimes|numeric|min:0',
      'amount_paid' => [
        'sometimes',
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) {
          $totalAmount = $this->input('total_amount') ?? $this->route('payment')->total_amount;

          if (round($value, 2) > round($totalAmount, 2)) {
            $fail('The paid amount cannot be greater than the total amount (' . $totalAmount . ').');
          }
        },
      ],
      'payment_date' => 'sometimes|date',
    ];
  }
}

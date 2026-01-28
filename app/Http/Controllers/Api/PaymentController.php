<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Services\PaymentService;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function index()
    {
        $payments = $this->paymentService->getAll();
        return PaymentResource::collection($payments);
    }

    public function archived()
    {
        $payments = $this->paymentService->getArchived();
        return PaymentResource::collection($payments);
    }
    public function store(CreatePaymentRequest $request)
    {
        $payment = $this->paymentService->create($request->validated());
        return new PaymentResource($payment);
    }

    public function show(Payment $payment)
    {
        return new PaymentResource($payment->load('employee'));
    }


    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment = $this->paymentService->update($payment, $request->validated());
        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment)
    {
        $this->paymentService->delete($payment);
        return response()->json([
            'message' => 'Payment archived successfully'
        ]);
    }

    public function restore($id)
    {
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $this->paymentService->restore($payment);

        return response()->json(['message' => 'Payment restored successfully']);
    }

    public function forceDelete($id)
    {
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $this->paymentService->forceDelete($payment);

        return response()->json(['message' => 'Payment permanently deleted']);
    }
}
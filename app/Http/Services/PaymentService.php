<?php


namespace App\Http\Services;

use App\Models\Payment;

class PaymentService
{
    public function getAll()
    {
        return Payment::whereNull('deleted_at')
            ->get();
    }

    public function getArchived()
    {
        return Payment::onlyTrashed()
            ->get();
    }


    public function create(array $data)
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data)
    {
        $payment->update($data);
        return $payment;
    }

    public function delete(Payment $payment)
    {
        return $payment->delete();
    }

    public function forceDelete(Payment $payment)
    {
        return $payment->forceDelete();
    }

    public function restore(Payment $payment)
    {
        return $payment->restore();
    }
}

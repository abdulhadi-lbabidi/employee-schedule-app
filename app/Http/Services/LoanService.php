<?php


namespace App\Http\Services;

use App\Models\Loan ;

class LoanService
{
    public function getAll()
    {
        return Loan::whereNull('deleted_at')
            ->get();
    }

    public function getArchived()
    {
        return Loan::onlyTrashed()
            ->get();
    }


    public function create(array $data)
    {
        return Loan::create($data);
    }

    public function update(Loan $loan, array $data)
    {
        $loan->update($data);
        return $loan;
    }

    public function delete(Loan $loan)
    {
        return $loan->delete();
    }

    public function forceDelete(Loan $loan)
    {
        return $loan->forceDelete();
    }

    public function restore(Loan $loan)
    {
        return $loan->restore();
    }
}

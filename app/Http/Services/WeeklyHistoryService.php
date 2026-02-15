<?php

namespace App\Http\Services;

use App\Models\WeeklyHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WeeklyHistoryService
{
  public function getAll()
  {
    return QueryBuilder::for(WeeklyHistory::class)
      ->with(['employee.user', 'workshop'])
      ->allowedFilters([
        AllowedFilter::exact('employee_id'),
        AllowedFilter::exact('workshop_id'),
        AllowedFilter::exact('is_paid'),
        AllowedFilter::exact('month'),
        AllowedFilter::exact('year'),
        AllowedFilter::exact('week_number'),
      ])
      ->defaultSort('-year', '-week_number')
      ->paginate(15);
  }

  public function create(array $data)
  {
    return WeeklyHistory::create($data);
  }

  public function togglePaymentStatus(WeeklyHistory $history)
  {
    $history->update(['is_paid' => !$history->is_paid]);
    return $history;
  }
}

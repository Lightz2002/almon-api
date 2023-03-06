<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, HasUuids;

    /**
     * Scope a query to only this month expense.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth($query)
    {
        return $query->where('date', '>=', Carbon::now()->startOfMonth()->toDateString())
            ->where('date', '<=', Carbon::now()->endOfMonth()->toDateString());
    }
}

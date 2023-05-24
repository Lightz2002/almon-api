<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory, HasUuids;

    /**
     * Scope a query to only month expense.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth($query, $date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m');
        return $query->whereRaw("DATE_FORMAT(date,'%Y-%m') = '$date'");
    }
}

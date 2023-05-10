<?php

namespace App\Models;

use App\Models\Scopes\SelfExpenseScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAllocation extends Model
{
    use HasFactory, HasUuids;

    /* Relationships */
    /**
     * Get the phone associated with the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the phone associated with the user.
     */
    public function transaction_category()
    {
        return $this->belongsTo(TransactionCategory::class);
    }


    /* Scopes */
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new SelfExpenseScope);
    }
    /**
     * Scope a query to only this month expense.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

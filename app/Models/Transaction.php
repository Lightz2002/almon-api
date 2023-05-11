<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use App\Models\Scopes\SelfExpenseScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    /* Relationships */
    /**
     * Get the user who has this expense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this expenses
     */
    public function transaction_category()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    protected $casts = [
        'status' => TransactionTypeEnum::class,
        'nullable_enum' => TransactionTypeEnum::class . ':nullable',
        'array_of_enums' => TransactionTypeEnum::class . ':collection',
        'nullable_array_of_enums' => TransactionTypeEnum::class . ':collection,nullable',
    ];



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
    public function scopeMonth(
        $query,
        $start = null,
        $end = null
    ) {
        $start = $start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $end ?? Carbon::now()->endOfMonth()->toDateString();

        return $query->where('date', '>=', $start)
            ->where('date', '<=', $end);
    }

    /**
     * Scope a query for specific type
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(
        $query,
        $type = null
    ) {
        $type = $type === 'income' ? TransactionTypeEnum::income() : TransactionTypeEnum::expense();
        return $query->where('type', '=', $type->value);
    }
}

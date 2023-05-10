<?php

namespace Database\Factories;

use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount' => fake()->randomFloat(),
            'date' => now(),
            'transaction_category_id' => TransactionCategory::inRandomOrder()->first(),
            'user_id' => User::firstWhere('username', 'ryan')->id,
            'note' => Str::random(50),
        ];
    }
}

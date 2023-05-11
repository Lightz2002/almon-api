<?php

namespace App\Services;

use App\Http\Resources\ExpenseAllocationResource;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BalanceService
{
  /**
   * get allocation by user
   *
   * @return \Illuminate\Http\Response
   */
  public function updateCurrentMonthBalance()
  {
    try {
      $balance = Balance::month()->first();
      if (!$balance) {
        $balance = new Balance();
        $balance->date = Carbon::now()->format('Y-m-d');
      }

      $currentMonthExpense = Transaction::type('expense')->month()->get();
      $currentMonthIncome = Transaction::type('income')->month()->get();

      $balance->expense_amount = $currentMonthExpense->sum('amount');
      $balance->income_amount = $currentMonthIncome->sum('amount');
      $balance->save();

      return $balance;
    } catch (\Exception $e) {
      return handleException($e);
    }
  }

  public function getCurrentMonthBalance()
  {
  }
}

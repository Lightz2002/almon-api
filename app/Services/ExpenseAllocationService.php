<?php

namespace App\Services;

use App\Http\Resources\ExpenseAllocationResource;
use App\Models\ExpenseAllocation;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExpenseAllocationService
{

  /**
   * get allocation by user
   *
   * @return \Illuminate\Http\Response
   */
  public function getAllocationByUser(User $user)
  {
    try {
      $allocations = ExpenseAllocation::getByUser($user->id)->get();
      return ExpenseAllocationResource::collection($allocations);
    } catch (\Exception $e) {
      return handleException($e);
    }
  }

  /**
   * Generate allocation for user
   *
   * @return \Illuminate\Http\Response
   */
  public function generateAllocation(User $user)
  {
    /*
        1. get user's monthly salary
        2. find all the allocation for this user
            2.1 loop through each and update allocation
        3. if not exist, create new allocation
        */

    try {
      $allocations = ExpenseAllocation::getByUser($user->id)->get();
      foreach ($allocations as $allocation) {
        switch (strtolower($allocation->expense_category->name)) {
          case 'kebutuhan':
            $allocation->percentage = 30;
            break;
          case 'cicilan':
            $allocation->percentage = 20;
            break;
          case 'tabungan':
            $allocation->percentage = 20;
            break;
          case 'gaya hidup':
            $allocation->percentage = 15;
            break;
          case 'dana darurat':
            $allocation->percentage = 10;
            break;
          case 'sedekah':
            $allocation->percentage = 5;
            break;
        }

        $allocation->save();
        $allocation->amount = $this->calculateAllocationAmount($allocation->percentage, $user->monthly_salary);
        $allocation->save();
      }

      if (count($allocations) === 0) $allocations = $this->create($user);

      return ExpenseAllocationResource::collection($allocations);
    } catch (\Exception $e) {
      return handleException($e);
    }
  }

  public function getAllocationAmountByCategory($expenseAllocations, $expenseCategory)
  {
    $filteredExpenseAllocation = $expenseAllocations->filter(function ($item) use ($expenseCategory) {
      return $item->expense_category->name === $expenseCategory->name;
    });

    return $filteredExpenseAllocation->first()->amount ?? 0;
  }

  protected function create(User $user)
  {
    $expenseCategories = ExpenseCategory::all();

    $allocation1 = new ExpenseAllocation();
    $allocation1->user_id = $user->id;
    $allocation1->expense_category_id = $expenseCategories->firstWhere('name', 'Kebutuhan')->id;
    $allocation1->percentage = 30;
    $allocation1->amount = $this->calculateAllocationAmount($allocation1->percentage, $user->monthly_salary);
    $allocation1->color = "#FFAC00";
    $allocation1->save();

    $allocation2 = new ExpenseAllocation();
    $allocation2->user_id = $user->id;
    $allocation2->expense_category_id = $expenseCategories->firstWhere('name', 'Tabungan')->id;
    $allocation2->percentage = 20;
    $allocation2->amount = $this->calculateAllocationAmount($allocation2->percentage, $user->monthly_salary);
    $allocation2->color = "#FE7E01";
    $allocation2->save();

    $allocation3 = new ExpenseAllocation();
    $allocation3->user_id = $user->id;
    $allocation3->expense_category_id = $expenseCategories->firstWhere('name', 'Gaya Hidup')->id;
    $allocation3->percentage = 15;
    $allocation3->amount = $this->calculateAllocationAmount($allocation3->percentage, $user->monthly_salary);
    $allocation3->color = "#FE3700";
    $allocation3->save();

    $allocation4 = new ExpenseAllocation();
    $allocation4->user_id = $user->id;
    $allocation4->expense_category_id = $expenseCategories->firstWhere('name', 'Dana Darurat')->id;
    $allocation4->percentage = 10;
    $allocation4->amount = $this->calculateAllocationAmount($allocation4->percentage, $user->monthly_salary);
    $allocation4->color = "#D3014C";
    $allocation4->save();

    $allocation5 = new ExpenseAllocation();
    $allocation5->user_id = $user->id;
    $allocation5->expense_category_id = $expenseCategories->firstWhere('name', 'Sedekah')->id;
    $allocation5->percentage = 5;
    $allocation5->amount = $this->calculateAllocationAmount($allocation5->percentage, $user->monthly_salary);
    $allocation5->color = "#A8006D";
    $allocation5->save();

    $allocation6 = new ExpenseAllocation();
    $allocation6->user_id = $user->id;
    $allocation6->expense_category_id = $expenseCategories->firstWhere('name', 'Cicilan')->id;
    $allocation6->percentage = 20;
    $allocation6->amount = $this->calculateAllocationAmount($allocation6->percentage, $user->monthly_salary);
    $allocation6->color = "#650041";
    $allocation6->save();

    return [$allocation1, $allocation2, $allocation3, $allocation4, $allocation5, $allocation6];
  }

  /**
   * Calculate amount of allocation
   *
   * @return int;
   */
  protected function calculateAllocationAmount($percentage, $salary)
  {
    $allocationAmount = $salary * $percentage / 100;
    return $allocationAmount;
  }
}

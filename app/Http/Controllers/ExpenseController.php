<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\ExpenseAllocation;
use App\Models\ExpenseCategory;
use App\Services\ExpenseAllocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    protected $expenseAllocationService;

    private function validateInput(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'expense_category_id' => 'required',
        ]);
    }

    private function fillInput(Expense $expense, Request $request)
    {
        $expense->expense_category_id = $request->expense_category_id;
        $expense->date = $request->date;
        $expense->amount = $request->amount;
        $expense->note = $request->note;
        $expense->save();

        return $expense;
    }

    public function __construct()
    {
        $this->expenseAllocationService = new ExpenseAllocationService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::month()->orderBy('created_at')->get();
        return ExpenseResource::collection($expenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validateInput($request);

            $expense = new Expense();
            $expense->user_id = Auth::User()->id;
            $this->fillInput($expense, $request);

            return new ExpenseResource($expense);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        try {
            $this->validateInput($request);

            $this->fillInput($expense, $request);

            return new ExpenseResource($expense);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }


    private function calculateRemain()
    {
        $user = Auth::user();
        $monthlySalary = $user->monthly_salary;
        $monthlyExpenses = Expense::month()->get()->sum('amount');
        $monthlyRemain = $monthlySalary - $monthlyExpenses;

        return (object) [
            'salary' => $monthlySalary,
            'expenses' => $monthlyExpenses,
            'remain' => $monthlyRemain,
        ];
    }

    /**
     * Get Monthly Expense, Salary, and LeftOvers
     *
     * @return \Illuminate\Http\Response
     */
    public function budgetInfo()
    {
        return response()->json($this->calculateRemain(), Response::HTTP_OK);
    }

    /**
     * Sum expense amount by category
     *
     * @return \Illuminate\Http\Response
     */
    public function sumAmountByCategory($monthlyExpense, $expenseCategory)
    {
        $filteredExpense = $monthlyExpense->filter(function ($item) use ($expenseCategory) {
            return $item->expense_category->name === $expenseCategory->name;
        });

        return $filteredExpense->first()->amount ?? 0;
    }

    /**
     * Get Monthly Expense, Salary, and LeftOvers
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        $remain = $this->calculateRemain();

        $expenseCategories = ExpenseCategory::all();
        $expenseAllocations = ExpenseAllocation::get();
        $monthlyExpense = Expense::month()->get();

        $data = (object) [
            'salary' => $remain->salary,
            'expenses' => $remain->expenses,
            'remain' => $remain->remain,
            'categories' => []
        ];

        foreach ($expenseCategories as $expenseCategory) {
            $expenseCategory->allocation = $this->expenseAllocationService->getAllocationAmountByCategory($expenseAllocations, $expenseCategory);
            $expenseCategory->expense = $this->sumAmountByCategory($monthlyExpense, $expenseCategory);
            $data->categories[] = $expenseCategory;
        }

        return response()->json($data, Response::HTTP_OK);
    }
}

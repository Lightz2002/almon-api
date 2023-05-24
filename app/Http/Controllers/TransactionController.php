<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\ExpenseAllocation;
use App\Models\TransactionCategory;
use App\Services\BalanceService;
use App\Services\ExpenseAllocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $expenseAllocationService;
    protected $balanceService;

    private function validateInput(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'transaction_category_id' => 'required|exists:transaction_categories,id',
        ], getValidationMessage());
    }

    private function fillInput(Transaction $transaction, Request $request)
    {
        $transaction->type = $request->type;
        $transaction->transaction_category_id = $request->transaction_category_id;
        $transaction->date = $request->date;
        $transaction->amount = $request->amount;
        $transaction->note = $request->note;
        $transaction->save();

        return $transaction;
    }

    public function __construct()
    {
        $this->expenseAllocationService = new ExpenseAllocationService();
        $this->balanceService = new BalanceService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Transaction::month()->orderByDesc('date')->get();
        return [
            "data" => TransactionResource::collection($expenses)->groupBy('date')
        ];
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

            $transaction = new Transaction();
            $transaction->user_id = Auth::User()->id;
            $this->fillInput($transaction, $request);

            // sync current month balance
            $this->balanceService->updateCurrentMonthBalance();


            return new TransactionResource($transaction);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        try {
            $this->validateInput($request);

            $this->fillInput($transaction, $request);

            $this->balanceService->updateCurrentMonthBalance();

            return new TransactionResource($transaction);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        $this->balanceService->updateCurrentMonthBalance();
        return response(null, Response::HTTP_NO_CONTENT);
    }


    private function calculateRemain()
    {
        // calculate remain from this month balance expense - this month balance income
        $user = Auth::user();
        $balance = $this->balanceService->updateCurrentMonthBalance();

        $monthlySalary = $user->monthly_salary;
        $monthlyExpenses = $balance->expense_amount;
        $monthlyRemain = $balance->expense_amount - $balance->income_amount;

        return (object) [
            'salary' => $monthlySalary,
            'expenses' => $monthlyExpenses,
            'remain' => $monthlyRemain,
        ];
    }

    /**
     * Get Monthly Transaction, Salary, and LeftOvers
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
    public function sumAmountByCategory($monthlyExpense, $transactionCategory)
    {
        $filteredExpense = $monthlyExpense->filter(function ($item) use ($transactionCategory) {
            return $item->transaction_category->name === $transactionCategory->name;
        });

        return $filteredExpense->first()->amount ?? 0;
    }

    /**
     * Get Monthly Transaction, Salary, and LeftOvers
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        $remain = $this->calculateRemain();

        $transactionCategories = TransactionCategory::all();
        $expenseAllocations = ExpenseAllocation::get();
        $monthlyExpense = Transaction::type('expense')->month()->get();

        $data = (object) [
            'salary' => $remain->salary,
            'expenses' => $remain->expenses,
            'remain' => $remain->remain,
            'categories' => []
        ];

        foreach ($transactionCategories as $transactionCategory) {
            $transactionCategory->allocation = $this->expenseAllocationService->getAllocationAmountByCategory($expenseAllocations, $transactionCategory);
            $transactionCategory->expense = $this->sumAmountByCategory($monthlyExpense, $transactionCategory);
            $data->categories[] = $transactionCategory;
        }

        return response()->json($data, Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\ExpenseAllocation;
use App\Models\TransactionCategory;
use App\Services\ExpenseAllocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $expenseAllocationService;

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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Transaction::month()->orderBy('created_at')->get();
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
        return response(null, Response::HTTP_NO_CONTENT);
    }


    private function calculateRemain()
    {
        $user = Auth::user();
        $monthlySalary = $user->monthly_salary;
        $monthlyExpenses = Transaction::month()->get()->sum('amount');
        $monthlyRemain = $monthlySalary - $monthlyExpenses;

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
    public function sumAmountByCategory($monthlyExpense, $expenseCategory)
    {
        $filteredExpense = $monthlyExpense->filter(function ($item) use ($expenseCategory) {
            return $item->transaction_category->name === $expenseCategory->name;
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
        $monthlyExpense = Transaction::month()->get();

        $data = (object) [
            'salary' => $remain->salary,
            'expenses' => $remain->expenses,
            'remain' => $remain->remain,
            'categories' => []
        ];

        foreach ($transactionCategories as $expenseCategory) {
            $expenseCategory->allocation = $this->expenseAllocationService->getAllocationAmountByCategory($expenseAllocations, $expenseCategory);
            $expenseCategory->expense = $this->sumAmountByCategory($monthlyExpense, $expenseCategory);
            $data->categories[] = $expenseCategory;
        }

        return response()->json($data, Response::HTTP_OK);
    }
}

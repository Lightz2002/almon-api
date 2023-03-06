<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{

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
}

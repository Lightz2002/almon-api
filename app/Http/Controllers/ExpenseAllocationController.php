<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ExpenseAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseAllocationController extends Controller
{

    public function __construct()
    {
        $this->expenseAllocationService = new ExpenseAllocationService();
    }

    public function generateAllocation($userId)
    {
        $user = User::findOrFail($userId);
        return $this->expenseAllocationService->generateAllocation($user);
    }
}

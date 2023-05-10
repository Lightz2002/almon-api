<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseAllocationController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SecurityQuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::post('/users/{user}', 'update');
        Route::post('/update-salary', 'updateSalary');
        Route::get('/profile', 'profile');
    });

    Route::get('expense/budget-info', [TransactionController::class, 'budgetInfo']);
    Route::get('expense/summary', [TransactionController::class, 'summary']);
    Route::resource('transaction', TransactionController::class);

    Route::resource('transaction-category', TransactionCategoryController::class);

    Route::post('expense-allocation/generate/{user}', [ExpenseAllocationController::class, 'generateAllocation']);
    Route::get('expense-allocation/list/{user}', [ExpenseAllocationController::class, 'index']);
});

Route::resource('security-question', SecurityQuestionController::class);
Route::controller(AuthController::class)->group(function () {
    Route::get('/connection', 'connection');
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/validate-forget-password', 'validateForgetPassword');
    Route::post('/send-forget-password-email', 'sendForgetPasswordEmail');
    Route::post('/reset-password', 'resetPassword');
});


// Route::get('/connection', [AuthController::class, 'connection'])->name('connection');
// Route::post('/register', [AuthController::class, 'register'])->name('register');
// Route::post('/login', [AuthController::class, 'login'])->name('login');

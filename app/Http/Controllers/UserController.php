<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ExpenseAllocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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
    $users = User::all();
    return UserResource::collection($users);
  }

  /**
   * Update user monthly salary.
   * @param \Illuminate\Http\Request
   * @return \Illuminate\Http\Response
   */
  public function updateSalary(Request $request)
  {
    try {
      $request->validate([
        'monthly_salary' => 'required|numeric'
      ], getValidationMessage());

      $user = Auth::user();
      $user->monthly_salary = $request->monthly_salary;
      $user->save();

      // generate allocation
      return $this->expenseAllocationService->generateAllocation($user);
    } catch (\Exception $e) {
      return handleException($e);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\User  $User
   * @return \Illuminate\Http\Response
   */
  public function show(User $User)
  {
    //
  }

  /**
   * Return the logged in user.
   *
   * @param  \App\Models\User  $User
   * @return \Illuminate\Http\Response
   */
  public function profile()
  {
    $user = Auth::user();
    return new UserResource($user);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\User  $User
   * @return \Illuminate\Http\Response
   */
  public function edit(User $User)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\User  $User
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, User $User)
  {
    try {
      $request->validate([
        'monthly_salary' => 'required|numeric',
        'email' => [
          Rule::unique('users')->ignore($User->id),
          'required'
        ],
        'username' => [
          Rule::unique('users')->ignore($User->id),
          'required'
        ]
      ], getValidationMessage());

      $User->monthly_salary = $request->monthly_salary;
      $User->username = $request->username;
      $User->email = $request->email;
      $User->save();

      // generate allocation
      return $this->expenseAllocationService->generateAllocation($User);
    } catch (\Exception $e) {
      return handleException($e);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\User  $User
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $User)
  {
    //
  }
}

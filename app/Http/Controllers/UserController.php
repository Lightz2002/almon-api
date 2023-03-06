<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $users = User::all();
    return response()->json([
      'data' => $users,
      'statusCode' => 200
    ], Response::HTTP_OK);
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
    return response()->json($user, Response::HTTP_OK);
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
    //
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

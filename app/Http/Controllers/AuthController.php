<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function connection()
    {
        return '200 ok';
    }

    /**
     * authenticate the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // 1. validate request like email, body
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'device_name' => 'required',
            ]);

            // 2. if the credentials are wrong, throw validation
            $user = User::firstWhere("username", $request->username);

            // 3. create token
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'username' => ['The provided credentials are incorrect.'],
                ]);
            }

            // 4. return a response
            return $user->createToken($request->device_name)->plainTextToken;
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * register the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            // 1. validate the requests
            $request->validate([
                'email' => 'required|email|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required',
                'security_question_id' => 'required',
                'security_question_answer' => 'required',
            ]);

            // 3. create an account
            $user = new User();
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->security_question_id = $request->security_question_id;
            $user->security_question_answer = $request->security_question_answer;
            $user->save();

            // 4. return a response
            return response()->json([
                "message" => "User created successfully",
                "status" => 201,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }
}

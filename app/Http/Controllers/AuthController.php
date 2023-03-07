<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                throw ValidationException::withMessages(
                    ['message' => 'The provided credentials are incorrect']
                );
            }

            // 4. return a response
            return $user->createToken($request->device_name)->plainTextToken;
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * register
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
                'security_question_id' => 'required|uuid',
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

    /**
     * validate forget password
     *
     * @return \Illuminate\Http\Response
     */
    public function validateForgetPassword(Request $request)
    {
        try {
            // 1. validate request like email, body
            $request->validate([
                'username' => 'required',
                'email' => 'required|email',
                'security_question_answer' => 'required',
            ]);

            $authenticated = User::where('username', $request->username)
                ->where('email', $request->email)
                ->where('security_question_answer', $request->security_question_answer)
                ->first();

            if (!$authenticated) $this->sendValidationMessage();


            $passwordResets =  $this->getPasswordReset($request);
            if ($passwordResets) DB::table('password_resets')->where('email', $request->email)->delete();

            //Create Password Reset Token
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Str::random(60),
                'created_at' => Carbon::now()
            ]);

            //Get the token just created above
            $tokenData = $this->getPasswordReset($request);


            return response()->json([
                'status' => 200,
                'message' => 'authenticated',
                'token' => $tokenData->token
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * reset password
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        try {
            // 1. validate request like email, body
            $request->validate([
                'password' => 'required',
                'reset_password_token' => 'required'
            ]);

            $tokenData = DB::table('password_resets')->where('token', $request->reset_password_token)->first();
            if (!$tokenData) $this->sendValidationMessage();

            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'success'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }


    private function getPasswordReset(Request $request)
    {
        return DB::table('password_resets')
            ->where('email', $request->email)->first() ?? null;
    }

    private function sendValidationMessage()
    {
        throw ValidationException::withMessages(
            ['message' => 'The provided credentials are incorrect']
        );
    }
}

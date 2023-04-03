<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPassword;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            ], getValidationMessage());

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
            ], getValidationMessage());

            // 3. create an account
            $user = new User();
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
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
    // public function validateForgetPassword(Request $request)
    // {
    //     try {



    //         // 1. validate request like email, body
    //         $request->validate([
    //             'username' => 'required',
    //             'email' => 'required|email',
    //             'security_question_answer' => 'required',
    //         ], getValidationMessage());

    //         $authenticated = User::where('username', $request->username)
    //             ->where('email', $request->email)
    //             ->where('security_question_answer', $request->security_question_answer)
    //             ->first();

    //         if (!$authenticated) $this->sendValidationMessage();


    //         $passwordResets =  $this->getPasswordReset($request);
    //         if ($passwordResets) DB::table('password_resets')->where('email', $request->email)->delete();

    //         //Create Password Reset Token
    //         DB::table('password_resets')->insert([
    //             'email' => $request->email,
    //             'token' => Str::random(6),
    //             'created_at' => Carbon::now()
    //         ]);

    //         //Get the token just created above
    //         $tokenData = $this->getPasswordReset($request);


    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'authenticated',
    //             'token' => $tokenData->token
    //         ], Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return handleException($e);
    //     }
    // }


    /**
     * send forget password email
     *
     * @return \Illuminate\Http\Response
     */
    public function sendForgetPasswordEmail(Request $request)
    {
        try {
            // 1. get user by email
            $user = $this->getUserByEmail($request);

            // 2. get password reset
            $passwordResets =  $this->getPasswordReset($request);
            if (!$passwordResets) {
                DB::table('password_resets')->insert([
                    'email' => $user->email,
                    'token' => Str::random(6),
                    'created_at' => Carbon::now(),
                    'expired_at' => Carbon::now()->addMinutes(30)
                ]);
            } else {
                DB::table('password_resets')->where('email', $user->email)->update([
                    'token' => Str::random(6),
                    'expired_at' => Carbon::now()->addMinutes(30)
                ]);
            }

            // 3. get the token
            $tokenData = $this->getPasswordReset($request)->token ?? "";

            // 4. send the email with the token
            Mail::to($user->email)->send(new ForgetPassword($user, $tokenData));

            return response()->json([
                'status' => 200,
                'message' => 'authenticated',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    /**
     * validate forget password token
     *
     * @return \Illuminate\Http\Response
     */

    public function validateForgetPassword(Request $request)
    {
        /* 
        2. validate token
            1. validate email
            2. get user with that email, 
                if no user with that email, return error
            3. get the password reset token with that email
            4. compare requeest token and the ps token
                4.1 if expired return already expired with 422
                4.2 if different return wrong token validation with 422
            5. if correct, authenticate 
        */
        try {
            // 1. validate user email
            $request->validate([
                'token' => 'required|size:6',
            ], getValidationMessage());

            $user = $this->getUserByEmail($request);

            // 2. get the password reset token with that email
            $passwordResets =  $this->getPasswordReset($request);

            // 3. compare password reset token
            if (!$passwordResets) {
                return $this->sendValidationMessage('no-token');
            }

            if (Carbon::now()->toDateTimeString() > $passwordResets->expired_at) {
                return $this->sendValidationMessage('expired-token');
            }

            if ($passwordResets->token !== $request->token) {
                return $this->sendValidationMessage('wrong-token');
            }

            return response()->json([
                'status' => 200,
                'message' => 'authenticated',
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
            ], getValidationMessage());

            $tokenData = DB::table('password_resets')->where('token', $request->reset_password_token)->first();
            if (!$tokenData) $this->sendValidationMessage('password-unauthorized');

            $user = User::where('username', $request->username)->where('email', $request->email)->first();
            if (!$user || !$request->has('reset_password_token')) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Kamu tidak memiliki akses untuk ini !'
                ], Response::HTTP_UNAUTHORIZED);
            }
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

    private function sendValidationMessage($type = 'unauthorized')
    {
        $message = '';
        switch (strtolower($type)) {
            case 'unauthorized':
                $message = 'Tidak ada user yang ditemukan dengan data yang diberikan';
                break;
            case 'password-unauthorized':
                $message = 'Kamu tidak memiliki akses ganti password';
                break;
            case 'no-token':
                $message = "Kirim token reset password ke email anda terlebih dahulu";
                break;
            case 'wrong-token':
                $message = "Token yang diberikan salah";
                break;
            case 'expired-token':
                $message = "Token yang diberikan sudah expired";
                break;
        }

        throw ValidationException::withMessages(
            ['message' => $message]
        );
    }

    private function getUserByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], getValidationMessage());

        $authenticated = User::where('email', $request->email)->first();
        if (!$authenticated) $this->sendValidationMessage();

        return $authenticated;
    }
}

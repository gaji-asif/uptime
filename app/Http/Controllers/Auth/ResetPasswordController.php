<?php

namespace App\Http\Controllers\Auth;

use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function resetPassword($token)
    {
       return view('auth.passwords.reset')->with('token', $token);
    }

    public function resetPasswordSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        // var_dump($validator->errors()); die;
        if ($validator->fails()) {
            return back()->withInput()->with('errors', $validator->errors());
        }
       
        $token = $request->input('token');
        $password = $request->input('password');

        $user = Employee::where('forget_token', $token)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->forget_token = null;
            $user->save();

            return back()->withInput()->with('success', "Password reset Successfully.");

        } else {
            return back()->withInput()->with('error', "User does not match!");
        }
    }
}

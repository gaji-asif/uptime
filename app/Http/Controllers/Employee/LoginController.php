<?php

namespace App\Http\Controllers\Employee;

use App\Employee;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

                                              

class LoginController extends Controller
{    
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'employee/dashboard/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /* echo "hello"; die;*/
        //$this->middleware('guest:employee')->except('logout');
    }

    public function showLoginForm()
    {
      //Session::has('employee'); die;
      if (Session::has('employee')) {
          return redirect()->intended('employee/dashboard/home');
      }
      return view('employee.login');        
    
    }


  

   public function login(Request $request)
   {
     $this->validateLogin($request);
     $employee = Employee::where('email', $request->email)->first();
    
     if($employee){

        if (Hash::check($request->password, $employee->password)) {
   
            $email = $request->input('email');
            $password= $request->input('password');
            
            //$userdata = Auth::User();      //fetch all data
            $sessionName = 'employee';        //assume a session name
            Session::put($sessionName, $employee);   //put the data and in session
            //level1
            if($employee->access_level == 0 ){
                return redirect()->intended('employee/dashboard/home');
            }
            //level2
            elseif ($employee->access_level == 1) {
               return redirect()->intended('employee/dashboard/home');
            }
            //level3 
            elseif ($employee->access_level == 2) {
               return redirect()->intended('employee/dashboard/home');
            }
            //level4
            elseif ($employee->access_level == 4) {
               return redirect()->intended('employee/dashboard/home');
            }
            //return redirect()->intended('/home');
            
        }else {
           /*$this->incrementLoginAttempts($request);*/
           return $this->sendFailedLoginResponse($request);
         
       }
     }else{
        return $this->sendFailedLoginResponse($request);
/*        return response()->json([
            'error' => 'Incorrect Email Address or Username '
        ], 402);*/
     }   


       //$this->incrementLoginAttempts($request);
       //return $this->sendFailedLoginResponse($request);
   }


   public function logout(){
    Session::forget('employee');
    return redirect()->route('employee.login');
   }





     protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

  /*
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }*/


   
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function username()
    {
        return 'email';
    }  

 
}

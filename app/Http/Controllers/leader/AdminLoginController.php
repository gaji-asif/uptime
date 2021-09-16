<?php

namespace App\Http\Controllers\leader;

use App\Http\Controllers\Controller\leader;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
//use App\Admin;
use App\Employee;
use Illuminate\Support\Facades\View;

class AdminLoginController extends Controller
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



  public function __construct()
    {
        $this->middleware('guest:admin',['except' => ['leader.logout']]);
    }



  public function showLoginForm()
    {
        return view('leader.auth.login');
    }


  public function login(Request $request)
    {
       
       // Validate the form data
      $errors = $this->validate($request, [
        'email'     => 'required|email',
        'password'  => 'required|min:6'
      ],
      [
        'email.required'    => 'Email is required',
        'password.required' => 'Password is required',
        'password.min'      => 'Password should be minimum 6 characters'
      ]
    );
      $data = $request->all();
      $check = Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']]);

      
      if ($check) {
        
          $employee = Employee::where('email', $request->email)->first();
          $sessionName = 'employee';        //assume a session name
          Session::put($sessionName, $employee);   //put the data and in session
        //  echo Auth::guard('admin')->user()->pic ;
        
        switch (Auth::guard('admin')->user()->access_level) {
            case 0:
                    return redirect()->intended(route('rep.dashboard'));
                break;
            case 1:
                   return redirect()->intended(route('rep.dashboard'));
                break;
            case 2:
                   return redirect()->intended(route('leader.dashboard'));
                break;
            case 3:
                return redirect()->intended(route('leader.dashboard'));
                break;
            case 4:
                return redirect()->intended(route('master.dashboard'));
                break;
                
            default:
                 return redirect()->intended(route('leader.dashboard'));
                break;
        }
        
       // return redirect()->intended(route('leader.dashboard'));
      
    }
  
      // if unsuccessful, then redirect back to the login with the form data
      
     return  redirect()->back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors($errors, 'errors')
                    ->with("error",'Invalid email or password.');


    }

    
 
}

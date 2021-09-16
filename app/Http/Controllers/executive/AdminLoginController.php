<?php

namespace App\Http\Controllers\executive;

use App\Http\Controllers\Controller\executive;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
use App\Http\Controllers\API\ApiController;
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
    $this->middleware('guest:admin',['except' => ['executive.logout']]);
  }


  

  public function showLoginForm()
  {
    return view('executive.auth.login');
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
      // echo Auth::guard('admin')->user()->pic ;
      
      $topsessionName = 'topemployee';        //the session for top leader board employee
      $api = new ApiController;
      $topemp_id = $api->getTopRank($employee->id);
      Session::put($topsessionName, $topemp_id);  
        
      switch (Auth::guard('admin')->user()->access_level) {
          
          case 1:
                 return redirect()->intended(route('rep.dashboard'));                //manager
              break;
          case 2:
                 return redirect()->intended(route('leader.dashboard'));            //laderboard
              break;
          case 3:
              return redirect()->intended(route('executive.dashboard'));           //corportate
              break;
          case 4:
              return redirect()->intended(route('master.dashboard'));              //admin
              break;
              
          default:
               return redirect()->intended(route('executive.dashboard'));
              break;
      }
      
     // return redirect()->intended(route('executive.dashboard'));
    
  }

    // if unsuccessful, then redirect back to the login with the form data
    
   return  redirect()->back()
                  ->withInput($request->only('email', 'remember'))
                  ->withErrors($errors, 'errors')
                  ->with("error",'Invalid email or password.');


  }

    
 
}

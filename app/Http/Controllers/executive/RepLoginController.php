<?php

namespace App\Http\Controllers\executive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;
use App\Rep;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use Redirect;
use Illuminate\Support\Facades\View;

class RepLoginController extends Controller
{
   public function __construct()
    {  
    // Session::set('panel', 'comany');
    $this->middleware('guest:rep',['except' => ['rep.logout']]);
  }

    public function showLoginForm(Request $request)
    {
    // $request->session()->put('panel', 'business');
      session(['panel'=>'rep']);
        return view('rep.auth.login');
    }

    public function login(Request $request)
    {
      // Validate the form data
      $errors = $this->validate($request, 
        [
          'email'     => 'required|email',
          'password'  => 'required|min:6'
        ],
        [
          'email.required'    => 'Email is required',
          'password.required' => 'Password is required',
          'password.min'      => 'Password should be minimum 6 characters'
        ]
      );

      $data  = $request->all();
      $check = Auth::guard('rep')->attempt(['email' => $data['email'], 'password' => $data['password']]);
      
      if ($check) {
        /*$status = Auth::guard('rep')->user()->tbl_status_id ;
        if($status != 1)
        {
          Auth::guard('business')->logout();
          return redirect()->back()->with("error",'Your account is deactivated now, please contact with admin.');
        }
        return redirect()->intended(route('business.dashboard'));
        */
        echo "rep";
      }

      // if unsuccessful, then redirect back to the login with the form data
      return redirect()->back()
          ->withInput($request->only('email', 'remember'))
          ->withErrors($errors, 'errors')
          ->with("error",'Invalid email or password, Kindly contact the system administrator.');
    }


 

}

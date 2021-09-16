<?php

namespace App\Http\Middleware;

use Closure;
use App\Authtoken;
use App\Users;
use App\Employee;
use App\Http\Controllers\API\BaseController as BaseController;

class APIAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->header('Authorization')){
            echo json_encode(array('status'=>false,'msg'=>'Authorization Failed.'));die;
        }else{
            $token = str_replace("Bearer ","",$request->header('Authorization'));
            $tokenVarify = $this->checkAuthCode($token);
            if($tokenVarify != '' || $tokenVarify != false) {
                $request['userData'] = $tokenVarify['data'];
                return $next($request);
            }else{
                echo json_encode(array('status'=>false,'msg'=>'Authorization Failed.'));die;
            }
        }
        return $next($request);
    }

    public function checkAuthCode($token){
        $validateToken = Authtoken::where("token",'=',$token)->first();
        
        if($validateToken != ''){
            if($validateToken->type == 'e'){
                $Employee = Employee::find($validateToken->user_id);
                if($Employee == '' || empty($Employee)){
                    echo json_encode(array('status'=>false,'msg'=>'invalid Employee'));die;
                }
                $userData = array(
                    'id'=>$Employee->id,
                    'full_name'=>$Employee->full_name,
                    'email'=>$Employee->email,
                    'company_id'=>$Employee->company_id,
                    'industry'=>$Employee->industry,
                    'phone_number'=>$Employee->phone_number,
                );
            }else if($validateToken->type == 'c'){
                $user = Users::find($validateToken->user_id);
                $userData = array(
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'address'=>$user->address,
                    'website_url'=>$user->website_url,
                    'phone_number'=>$user->phone_number,
                );
            }
            return array('status'=>true,'data'=>$userData);
        }else{
            return false;
        }
    }
}

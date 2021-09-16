<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
       switch ($guard) {
            case 'admin':
                if (Auth::guard($guard)->check()) {
                    if(Auth::guard($guard)->user()->access_level == 4){
                        return redirect()->route('master.dashboard');
                    }
                }
                break;
            
            case 'rep':
                if (Auth::guard($guard)->check()) {
                   // if(Auth::guard($guard)->user()->access_level == 1){
                    return redirect()->route('rep.dashboard');
               //   }
                }
                break;
                
            
                    
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect('/');
                }
                break;
        }

        return $next($request);
    }
}

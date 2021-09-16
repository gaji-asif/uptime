<?php

namespace App\Http\Controllers\executive;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function employeelogincheck()
    {
    	if(Session::has('employee')){
    		return true;
    	}else{
    		redirect('/');//redirect('employee/login');
    	}
    }
}

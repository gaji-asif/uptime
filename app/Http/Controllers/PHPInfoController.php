<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PHPInfoController extends Controller
{
    public function index(){
    	return view('phpinfo');
    }
}

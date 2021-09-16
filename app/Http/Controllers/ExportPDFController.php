<?php

namespace App\Http\Controllers;

use App\Services\PDFGenerator;
use Illuminate\Http\Request;

class ExportPDFController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->method() == 'GET') {
            $url = $request->input('url');
            if (str_contains($url, 'executive/test/')){
                $url = str_replace('executive/test', 'employeeportfolio', $url);
            }
            else if (str_contains($url, 'master/test/')){
                $url = str_replace('master/test', 'employeeportfolio', $url);
            }
            
            $guard = $request->input('guard');

            $params = [];

            if ($guard) {
                $params = [
                    'user_id' => auth($guard)->id(),
                    'token' => auth($guard)->user() ? bcrypt(auth($guard)->user()->email . auth($guard)->user()->created_at) : "",
                ];
            }

            return PDFGenerator::download($url, $params);
        }

    }
}

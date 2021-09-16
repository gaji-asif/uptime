<?php

namespace App\Http\Controllers\rep;

use DB;
use Auth;
use File;
use Illuminate\Http\Request;
use App\Users;
use App\Employee;
use App\Builds;
use App\Authtoken;
use App\Categories;
use App\Validations;
use App\Challenge;
use Illuminate\Support\Facades\Storage;
//use App\Http\Controller\master\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
            $company = Users::where('role','company')->count();
            $employee = Employee::where('is_deleted','0')->count();
            $challenge = Challenge::all()->count();
            $builds = Builds::all()->count();
            $categories = 0;
            $fiveActiveChallanges = Challenge::where('status','0')->limit(5)->get()->toArray();
      
            $topFiveWinBuilds = $this->getLooseAndWin('1');        
            $topFiveLoseBuilds = $this->getLooseAndWin('0');
            $topFiveCurrentBuilds = $this->getLooseAndWin('-1');

            $data = array('company'=>$company,'employee'=>$employee,'challenge'=>$challenge,'builds'=>$builds,'topFiveWinBuilds'=>$topFiveWinBuilds,'topFiveLoseBuilds'=>$topFiveLoseBuilds,'topFiveCurrentBuilds'=>$topFiveCurrentBuilds,'categories'=>$categories,'fiveActiveChallanges'=>$fiveActiveChallanges);
        return view('master.home')->with('data',$data);
        //return view('home');
    }

    public function Downloadresume($id){
        $tmpFolder = 'images/resumes/';
        // File::isDirectory($tmpFolder) or File::makeDirectory($tmpFolder, 0777, true, true);
        $url = Storage::disk("s3")->url('rep/employee/get-resume/'.$id);
        return redirect($url);

        exec('wkhtmltopdf '.$url.' '.$tmpFolder.$id.'_'.'resume.pdf');
        $file = $tmpFolder.$id.'_'.'resume.pdf';
        return redirect(Storage::disk("s3")->url('rep/images/resumes/'.$id.'_'.'resume.pdf'));
        //return response()->download($file)->deleteFileAfterSend(true);
        
        
        //return response()->download($file);
        // && redirect(url('/images/resumes/'.$id.'_'.'resume.pdf'));
    }

    public function Pullgit(){
        print("<pre>" . $this->execPrint("git pull") . "</pre>");
    }

    function execPrint($command) {
        $result = array();
        exec($command, $result);
        foreach ($result as $line) {
            print($line . "\n");
        }
    }

    public function getLooseAndWin($status){
        if(Auth::user()->role == 'admin'){
            $query = DB::table('builds')
                ->leftJoin('employee', 'employee.id', '=', 'builds.employee_id')
                ->select('builds.*','employee.full_name')
                ->where('builds.status',$status)
                ->limit(5)
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        } else{
            $query = DB::table('builds')
                ->leftJoin('employee', 'employee.id', '=', 'builds.employee_id')
                ->select('builds.*','employee.full_name')
                ->where('builds.status',$status)
                ->where('builds.company_id', Auth::user()->id)
                ->limit(5)
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        }

        if($status != '-1'){
            $newTopFileLooseArray = array();
            if(!empty($query)){
                foreach($query as $k=>$loose){
                    $countLoose = Validations::where('build_id',$loose->id)->where('win','1')->count();
                    $loose->count = $countLoose;
                    $newTopFileLooseArray[] = $loose;
                }
            }
            return $newTopFileLooseArray;
        }
        return $query;
        
    }
}

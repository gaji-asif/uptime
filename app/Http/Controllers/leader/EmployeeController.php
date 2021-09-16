<?php

namespace App\Http\Controllers\leader;

use App\Requests;
use App\Employee;
use App\Users;
use App\Builds;
use App\Validations;
use App\Tenure;
use App\Accesslevel;
use App\Http\Controllers\API\ApiController;
use Auth;
use Session;
use DB;
use File;
use App\Categories;
use App\Industry;
use App\Challenge;
use Illuminate\Http\Request;
use App\Authtoken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;


class EmployeeController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       if(Auth::guard('admin')->user()->access_level == 5){
            $employee = Employee::where('is_deleted','0')->latest()->paginate(5);
        } else{
            $employee = Employee::where('is_deleted','0')->where('company_id',Auth::user()->id)->latest()->paginate(5);
        }
   
        if(!empty($employee)){
            foreach ($employee as $item) {
                $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_id = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_id = $user->name;
                }
                $categories = Categories::select('category_name')->where('id',$item->industry)->first();
                $item->industry = '--';
                if($categories){
                    $item->industry = $categories->category_name;
                }
            }
        }
        $employee->from_where = '0';
        $employee->company_id = 0;
        return view('leader.employee.index',compact('employee'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function employeelist(){
        $employee = Employee::where('is_deleted','0')->latest()->paginate(5);
        $employee->from_where = '0';
        $employee->company_id = 0;
        return view('leader.employee.index',compact('employee'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    


    public function employeedelete(){   

        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                
                $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
               
                if($employee == 0){
                    continue;
                }
                // Employee::find($id)->delete();
                $data = array('is_deleted'=> '1');
                Employee::find($id)->update($data);
                $this->deleteEmployeeToken($id);
            }
            echo json_encode(array('status'=>true));die;
        }     
       
    }
  

    public function employeedatatable()
    {
        
        
            $employee = Employee::where('is_deleted','0')->get();
      
        //print_r($employee); die;
        if(!empty($employee)){
            foreach ($employee as $item) {
                $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_id = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_id = $user->name;
                }
                if($item->industry == ''){
                    $item->industry = '--';
                }
                $categories = Categories::select('category_name')->where('id',$item->industry)->first();
                $item->industry = '--';
                if($categories){
                    $item->industry = $categories->category_name;
                }
            }
        }
        return Datatables::of($employee)->make(true);
    }

    /**
     * Display a listing of the resource based on company.
     *
     * @return \Illuminate\Http\Response
     */
    public function company($id)
    {
        if($id && $id > 0){
            $users = Users::find($id);
            if(!$users){
                return redirect()->route('leader.employee.list');
            }
            $employee = Employee::where('company_id', $id)->where('is_deleted','0')->latest()->paginate(5);
            $employee->from_where = '1';
            $employee->company_id = $id;
            return view('leader.employee.index',compact('employee'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
        } else {
            return redirect()->route('leader.employee.list');
        }
    }

    public function companyData($id)
    {
        $employee = Employee::where('is_deleted','0')->where('company_id',$id)->get();
        if(!empty($employee)){
            foreach ($employee as $item) {
                $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_id = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_id = $user->name;
                }
                if($item->industry == ''){
                    $item->industry = '--';
                }
                $categories = Categories::select('category_name')->where('id',$item->industry)->first();
                $item->industry = '--';
                if($categories){
                    $item->industry = $categories->category_name;
                }
            }
        }
        return Datatables::of($employee)->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        // $user = Auth::user();

        // $employee_data = array('is_admin' => 0, 'categories' => []);
        // if($user->role == 'admin'){
            // $employee_data['is_admin'] = 1;
            $employee_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        // } 
        // else {
            $employee_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
        // }
        $employee_data['access_level']   = Accesslevel::where('id','<=',3)->get()->toArray();

        //Accesslevel::all()->toArray();
 //print_r($employee_data);die;
        
        return view('leader.employee.create')->with('employee_data', $employee_data); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  

        // $user = Auth::user();
        // if($user->role == 'company'){
        //     $request['company'] = $user->id;
        // }
         request()->validate([
            'full_name' => 'required',
            'email' => 'required|unique:employee|max:255',
            'password' => 'required',
            'phone_number' => 'required',
            'company' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        // if($user->role == 'admin'){
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('leader.employee.list')->with('error','Company Not found.');
            }
        // }
        $data = Array (
            'full_name' => $request['full_name'],
            'email' => $request['email'],
            'phone_number' => $request['phone_number'],
            'company_id' => $request['company'],
            'industry' => $request['industry'],
            'access_level' => $request['access_level'], 
        );

        // print_r($data); die("sds");
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $data['image'] = $imageName;
        }
        $data['password'] = Hash::make($request['password']);
        $data['is_deleted'] = '0';
        $employee = Employee::create($data);
        if($employee->id && $request->hasFile('image')){
            // $path = public_path().'/images/employee/';
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->image->move(public_path('images/employee'), $imageName);
            $path = 'images/employee/';
            $file = $request->file('image');
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
        }
        // return  redirect()->route('leader.employee.list')->with('success','Employee created successfully.');
          return  redirect()->route('leader.employee.list')->with('success','Employee created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id && $id > 0){
            // if(Auth::user()->role == 'admin'){
                $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
            // } else{
            //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
            // }
            if($employee){
                // print_r($employee);
                // echo ('http://localhost/uptime/images/employee/'.$employee->image);
                // // die;
                // $exif = function_exists('exif_read_data') ? $this->exif = @exif_read_data($this->filename) : null;
                
                // // $exif = exif_read_data('http://localhost/uptime/images/employee/'.$employee->image, "FILE,COMPUTED,ANY_TAG,IFD0,THUMBNAIL,COMMENT,EXIF", true);
                //     print_r($exif);
                // echo $exif===false ? "No header data found.<br />\n" : "Image contains headers<br />\n";

                // die;


                $user = Users::select('name')->where('id', $employee->company_id)->first();
                $employee->company_name = '--';
                $employee->point = 0;
                if($user && !empty($user) && $user->name != ''){
                    $employee->company_name = $user->name;
                }
                if($employee->industry == ''){
                    $employee->industry = '--'; 
                }
                $api = new ApiController;
                $employee->point = $api->countPoint($employee->id);
                $employee->build = Builds::where('employee_id', $employee->id)->count();
                $employee->validation = Validations::where('employee_id', $employee->id)->count();
                $categories = Categories::select('category_name')->where('id',$employee->industry)->first();
                $employee->industry = '--';
                if($categories){
                    $employee->industry = $categories->category_name;
                }
                //$monthlyLabel = '';
                $monthLyCountArray = $this->countYearlySubmission($id);
                $countdailySubmission = $this->countdailySubmission($id);

                $wincountbuilds = Builds::where('employee_id', $id)->where('status','1')->count();
                $loosecountbuilds = Builds::where('employee_id', $id)->where('status','0')->count();
                $categoriesByEid = $this->getCountsByID($employee->company_id, $id);
                //$this->getCountsByEmpComany($id);

                $win_challenge = array();
                $all_employee = Employee::where('company_id',$employee->company_id)->where('is_deleted','0')->get();
                if($all_employee){
                    foreach($all_employee as $emp){
                        $emp->point = $api->countPoint($emp->id);
                        $win_challenge[] = $emp;
                    }
                }
                usort($win_challenge, function($a, $b) {
                    return $b['point'] - $a['point'] ;
                });
                $win_challenge = array_slice($win_challenge, 0, 10);
               
                return view('leader.employee.show',compact('employee'))->with('monthLyCountArray', $monthLyCountArray)->with('wincountbuilds', $wincountbuilds)->with('loosecountbuilds', $loosecountbuilds)->with('categoriesByEid',$categoriesByEid)->with('win_challenge',$win_challenge)->with('countdailySubmission',$countdailySubmission);
            } else {
                return redirect()->route('leader.employee.list')->with('errors','No employee Found.');
            }
        } else {
            return redirect()->route('leader.employee.list')->with('errors','No employee Found.');
        }
    }

    public function getCountsByID($company_id, $id){
        //getting list according to the category
        // $getEmployee = Employee::find($id);
        // $company_id = $getEmployee->company_id;
        $getCategoriesByCid = Categories::where("company_id",$company_id)->get()->toArray();  
        $array_push = array();
        if(!empty($getCategoriesByCid)){
            foreach($getCategoriesByCid as $categories){
                //$countBuilds = Builds::where('category_id',$categories['id'])->where('challenge_id', '!=',  0 )->count();
                $countBuilds = Builds::where('employee_id',$id)->where('category_id',$categories['id'])->count();
                $array_push[$categories['category_name']] = $countBuilds;
            }
        }
        return $array_push;
    }

    // public function getCountsByEmpComany($id){
    //     //getting list according to the category
    //     $getEmployee = Employee::find($id);
    //     $company_id = $getEmployee->company_id;
    //     $getBuildsByCid = Builds::where("company_id",$company_id)->get()->toArray();        
    //     $dates = array();
    //         if(!empty($getBuildsByCid)){ 
    //             foreach ( $getBuildsByCid as $q ) {
    //             if(array_key_exists($q['employee_id'], $dates)){
    //                 $dates[$q['employee_id']]++;
    //             } else {
    //                 $dates[$q['employee_id']] = 1;
    //             }
    //         }
    //         return $dates;
    //     }
    // }

    public function countYearlySubmission($eid){
        $allmonth = 11;
        for($month = 0;$month <= $allmonth;$month++  ){
            $mm_t = $month - 1 ;                
            //$my_array[] = date("m-Y", strtotime((date("Y-$month-01 00:00:00 -1 months"))));                   
            $my_array[] = date("M-y", strtotime( date( 'Y-m-01' )." -$month months"));  
            $date_s =  date("Y-m-d h:i:s", strtotime( date( 'Y-m-01' )." -$month months"));
            $date_e = date("Y-m-d h:i:s", strtotime( date( "Y-m-01" )." -$mm_t months"));               
            //$date_s = date("Y-m-d h:i:s",strtotime(date("Y-$month-01 00:00:00")));
            // $date_e = date("Y-m-d h:i:s",strtotime(date("Y-$mm_t-01 00:00:00")));                
            $data_d[] = Builds::where('employee_id',$eid)->whereBetween('created_at', [$date_s, $date_e ])->count();
        }
        $data = array("labels"=>array_reverse($my_array),"totalCount"=>array_reverse($data_d));        
        return $data;
    }

    public function countdailySubmission($eid){
        $date_sc = date("Y-m-d 00:00:00",strtotime( date( "Y-m-d")));
        $date_ec = date("Y-m-d 00:00:00", strtotime( date( "Y-m-d" )." -1 months"));
        //echo $date_s.'---'.$date_e ;die;
        $date1 = new \DateTime($date_sc);
        $date2 = new \DateTime($date_ec);
        $diff = $date2->diff($date1)->format("%a");
        $my_array = array();
        for($day = 1;$day <= $diff;$day++  ){
            $mm_t = $day - 1 ;                
            //$my_array[] = date("m-Y", strtotime((date("Y-$month-01 00:00:00 -1 months"))));                   
            $my_array[] = date("Y-m-d", strtotime( date( 'Y-m-d' )." -$day day"));  
            $date_s =  date("Y-m-d 00:00:00", strtotime( date( 'Y-m-d' )." -$day day"));           
            $data_d[] = Tenure::where('employee_id',$eid)->whereDate('created_at',$date_s)->count();
        }
        $data = array("labels"=>array_reverse($my_array),"totalCount"=>array_reverse($data_d));       
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {  
        // if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
        // } else{
        //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
        // }
        if(!$employee){
            return redirect()->route('leader.employee.list')->with('errors','No employee Found.');
        }
        // $user = Auth::user();
        $employee_data = array('is_admin' => 0);
        // if($user->role == 'admin'){
            // $employee_data['is_admin'] = 1;
            $employee_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
            $employee_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($employee->company_id, '0'))->get()->toArray();
        // } else {
        //     $employee_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
        // }
        
        $employee_data['access_level']   = Accesslevel::where('id','<=',3)->get()->toArray();

        //Accesslevel::all()->toArray();

        return view('leader.employee.edit',compact('employee'))->with('employee_data', $employee_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
        // } else{
        //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
        // }
        if(!$employee || $employee == ''){
            return redirect()->route('leader.employee.list')->with('errors','No employee Found.');
        }
        // $user = Auth::user();
        // if($user->role == 'company'){
        //     $request['company'] = $user->id;
        // }
        request()->validate([
            'full_name' => 'required',
            'phone_number' => 'required',
            'company' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = array();
        $data['full_name'] = $request['full_name'];
        $data['industry'] = $request['industry'];
        $data['phone_number'] = $request['phone_number'];
        $data['access_level'] = $request['access_level'];

        // if($user->role == 'admin'){
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('leader.employee.list')->with('error','Company Not found.');
            }
            $data['company_id'] = $request['company'];
        // }

        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
            if($employee->image != ''){
                $filename = 'images/employee/'.$employee->image;
                Storage::disk("s3")->delete($filename);
                // if(File::exists($filename)){                    
                //     File::delete($filename); 
                // }
            }
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $data['image'] = $imageName;
            }
            if(isset($request->delete_image) && $request->delete_image == 1){
                $data['image'] = '';
            }
        }

        Employee::find($id)->update($data);
        
        if($request->hasFile('image')){
            // $path = public_path().'/images/employee/';
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->image->move(public_path('images/employee'), $imageName);
            $path = 'images/employee/';
            $file = $request->file('image');
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
        }

        return redirect()->route('leader.employee.list')->with('success','Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        } else{
            $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->count();
        }
        if($employee == 0){
            return redirect()->route('employee.index')->with('errors','No employee Found.');
        }
        // Employee::find($id)->delete();
        $data = array('is_deleted'=> '1');
        Employee::find($id)->update($data);
        $this->deleteEmployeeToken($id);
        return redirect()->route('employee.index')->with('success','Employee deleted successfully.');
    }

    public function delete($id)
    {
      
        $data = array('is_request'=> '1');
        Employee::find($id)->update($data);
        $emp_data = array(
         'is_request'=>'1'
        );
        
        $request_data = array(
         'request_type'=>'delete',
         'status'=>'0',
         'requested_id'=>$id,
         'from_table'=>'employee',
         'data'=>json_encode($emp_data),
         'employee_id'=>Session::get('employee')->id
        );
        Requests::create($request_data);   
        return response()->json(['status'=>true,'message'=>'Your Request Sent  Successfully.']);
    }

    public function deleteEmployeeToken($id)
    {
        $authTokes = Authtoken::where('user_id',$id)->get();
        if($authTokes != ''){
            $authTokes = $authTokes->toArray();
            foreach($authTokes as $tokes){
                $tokens = Authtoken::find($tokes['id']);
                $tokens->delete();
            }
        }
    }
 
    public function changePassword(Request $request, $id){
        $data = $request->all();
        if($id && $id > 0 && $data && $data['password'] && $data['password'] != ''){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
            if($employee){
                $upd_data['password'] = Hash::make($data['password']);
                Employee::find($id)->update($upd_data);
                return response()->json(['status'=>true,'message'=>'Password change successfully.']);
            } else {
                return response()->json(['status'=>false,'message'=>'Employee not found.']);
            }
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }

    public function getCategoryFromCompany($id){
        if($id && $id > 0){
            // if(Auth::user()->role == 'admin'){
                $categories = Categories::select('id','category_name')->whereIn('company_id',array($id, '0'))->get()->toArray();
            // } else {
            //     $categories = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
            // }
            if($categories){
                $html = '<option value="0">select Category</option>';
                foreach ($categories as $item) {
                    $html .= '<option value="'.$item['id'].'">'.$item['category_name'].'</option>';
                }
                return response()->json(['status'=>true,'html'=>$html]);
            } else {
                return response()->json(['status'=>false,'message'=>'1']);
            }
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }

    public function getResume($id){

        
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
            if(!$employee){
                return redirect()->route('leader.employee.index')->with('errors','No employee Found.');
            } else {
                $employee = $employee->toArray();
            }

            $all_challenge = DB::table('challenge')
                ->join('builds', 'builds.challenge_id', '=', 'challenge.id')
                ->select(DB::raw("sum(challenge.point) as point"))
                ->where('builds.employee_id', $id)
                ->where('challenge.status', '!=', '-1')
                ->count();

            $win_challenge = DB::table('challenge')
                ->join('builds', 'builds.challenge_id', '=', 'challenge.id')
                ->select(DB::raw("sum(challenge.point) as point"))
                ->where('builds.employee_id', $id)
                ->where('challenge.status', '1')
                ->count();

        $company = Users::select('name')->where('id', $employee['company_id'])->first()->toArray();

        if(!$company){
            return redirect()->route('leader.employee.list')->with('errors','employee\'s Company not Found.');
        }

        $employee['company_name'] = $company['name'];
        $employee['all_challenge'] = $all_challenge;
        $employee['win_challenge'] = $win_challenge;
        $employee['win_validation'] = Builds::where('employee_id',$id)->count();

        $cmp_employee_list =  DB::table('validations')
        ->join('employee', 'employee.id', '=', 'validations.employee_id')
        ->select('validations.employee_id')
        ->where('employee.company_id', $employee['company_id'])
        ->where('validations.win', '1')
        ->orderBy('validations.created_at', 'DESC')
        ->get();
        $employee_with_validation = array();
        if($cmp_employee_list){
            foreach($cmp_employee_list as $key){
                if(isset($employee_with_validation[$key->employee_id])){
                    $employee_with_validation[$key->employee_id] = ($employee_with_validation[$key->employee_id]+1);
                } else {
                    $employee_with_validation[$key->employee_id] = 1;
                    $employee_list[] = $key->employee_id;
                }
            }
        }
        if(array_key_exists($id, $employee_with_validation)){
            $key = (array_search($id, $employee_list)) + 1 ;
            $employee['top_validation_per'] = round(($key*100)/count($employee_list));
        } else {
            $employee['top_validation_per'] = 0;
        }

        $build_categore = DB::select('SELECT categories.id, categories.category_name,  COUNT(categories.id) as count FROM categories join builds ON builds.category_id = categories.id where employee_id = '.$id.'  and builds.status = "1" GROUP BY categories.id, categories.category_name');
        if(count($build_categore) == 0){
            $employee['categories_list_right'] = $build_categore;
            $employee['categories_list_left'] = $build_categore;
        } else if(count($build_categore) == 1){
            $employee['categories_list_right'] = $build_categore;
            $employee['categories_list_left'] = [];
        } else {
            list($employee['categories_list_left'], $employee['categories_list_right']) = array_chunk($build_categore, ceil(count($build_categore) / 2));
        }
        $api = new ApiController;
        $employee['validation_score'] = $api->countPoint($employee['id']);
        $employee['created_at'] = 'UT-'.date("mdy-Hi", strtotime($employee['created_at']));
        // set static icon
        $employee['icon_left'] = array('fa-money','fa-bandcamp','fa-eercast','fa-meetup','fa-microchip','fa-podcast','fa-ravelry','fa-snowflake-o','fa-superpowers','fa-wpexplorer','fa-bolt','fa-bullseye','fa-cubes','fa-crosshairs','fa-dot-circle-o','fa-ellipsis-h','fa-fighter-jet','fa-fire','fa-gavel','fa-etsy');
        $employee['icon_right'] = array('fa-leaf','fa-lightbulb-o','fa-location-arrow','fa-qrcode','fa-rocket','fa-share-alt','fa-tag','fa-thumb-tack','fa-ticket','fa-tree','fa-trophy','fa-umbrella','fa-wrench','fa-book','fa-glass','fa-life-ring','fa-lightbulb-o','fa-puzzle-piece','fa-road','fa-spoon');
        
        return view('leader.employee.resume',compact('employee'));
    }
}

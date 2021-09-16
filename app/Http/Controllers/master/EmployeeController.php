<?php

namespace App\Http\Controllers\master;

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
use App\Subcategory;
use App\Industry;
use App\Challenge;
use Illuminate\Http\Request;
use App\Authtoken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use Image;

class EmployeeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $employee = Employee::latest()->paginate(5);

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
        return view('master.employee.index',compact('employee'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function employeelist(){
        $employee = Employee::where('is_deleted','0')->latest()->paginate(5);
        $employee->from_where = '0';
        $employee->company_id = 0;
        return view('employee.index',compact('employee'))
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

        try {
            
        $employee = Employee::query()
                ->where('is_request','0')
                ->latest()
                ->get()
                ->toArray();

        $result = array();
        $resultitem = array();

        if(!empty($employee)){

            foreach ($employee as $item) {

                $user = Users::select('name')->where('id', $item['company_id'])->first();

                if($user && !empty($user) && $user->name != ''){

                if($user->name != $item['full_name']){
                    $resultitem = $item;
                    $resultitem ['company_id'] = $user->name;

                    if($item['industry'] == ''){
                        $resultitem ['industry'] = '--';
                    }
                    else{
                        $industry = Industry::where('id',$item['industry'])->first();
                        if(!empty($industry)){
                            $industry =  $industry->toArray();
                            $resultitem ['industry'] = $industry['industry_name'];
                        }
                        else{
                            $resultitem ['industry'] = '--';
                        }
                    }
                    $access = Accesslevel::where('id',$item['access_level'])->first();
                    $resultitem['access_name'] = $access->access_level_name;

                    $originalDate = $item['created_at'];
                    $newDate = date("d/m/y", strtotime($originalDate));
                    $resultitem['new_date'] = $newDate ;
                    $result[] = $resultitem ;

                }
                }
            }
        }
        return Datatables::of($result)->make(true);
        } catch (\Throwable $th) {
            return json_encode(array('status'=>false, 'message' => $th->getMessage()));
        }
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
                return redirect()->route('master.employee.list');
            }
            $employee = Employee::where('company_id', $id)->where('is_deleted','0')->latest()->paginate(5);
            $employee->from_where = '1';
            $employee->company_id = $id;
            return view('master.employee.index',compact('employee'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
        } else {
            return redirect()->route('master.employee.list');
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

        $employee_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        $employee_data['industry'] = Industry::all();
        $employee_data['access_level']   = Accesslevel::where('id','<=',2)->get()->toArray();
        return view('master.employee.create')->with('employee_data', $employee_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

         request()->validate([
            'full_name' => 'required',
            'email' => 'required|unique:employee|max:255',
            'password' => 'required',
            'phone_number' => 'required',
            'company' => 'required',
            'industry' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
         $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('master.employee.list')->with('error','Company Not found.');
         }

        if(!$request['industry']){
            return redirect()->route('master.employee.create')->with('error','Industry Not found.');
        }
        $access_level = $request['access_level'];
        $data = Array (
            'full_name' => $request['full_name'],
            'email' => $request['email'],
            'phone_number' => $request['phone_number'],
            'company_id' => $request['company'],
            'industry' => $request['industry'],
            'access_level' => $access_level
        );

        // print_r($data); die("sds");
        if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/employee/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
        }
        $data['password'] = Hash::make($request['password']);
        $data['is_deleted'] = '0';
        $employee = Employee::create($data);

          return  redirect()->route('master.employee.list')->with('success','Employee created successfully.');
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

            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();


            if($employee){

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

        //build approved data

                $maincategory_data = $this->getMainCategoryByID($id);
                $subcategory_data = $this->getSubcategoryByID($id);


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


                 //get challenge array
                $challangeiesByEid = $this->getChallengeCountsByID($employee->company_id, $id);

                return view('master.employee.show',compact('employee'))->with('monthLyCountArray', $monthLyCountArray)->with('wincountbuilds', $wincountbuilds)->with('loosecountbuilds', $loosecountbuilds)->with('win_challenge',$win_challenge)->with('countdailySubmission',$countdailySubmission)->with('maincategory_data', $maincategory_data)->with('subcategory_data', $subcategory_data)->with('challangeiesByEid', $challangeiesByEid);

            }
            else {
                return redirect()->route('master.employee.list')->with('errors','No employee Found.');
            }
        } else {
            return redirect()->route('master.employee.list')->with('errors','No employee Found.');
        }

    }

public function getChallengeCountsByID($company_id,$id){


        $getChallengesByCid = Challenge::where("company_id",$company_id)->get()->toArray();
        $array_push = array();
        if(!empty($getChallengesByCid)){
            foreach($getChallengesByCid as $challenge){

                $wincount = Builds::where('employee_id',$id)->where('category_id',$challenge['id'])->count();
                $array_push[$challenge['challenge_text']] = $wincount;
            }
        }
        return $array_push;


    }
    public function getSubcategoryByID($id){

        $Build = Builds::where('employee_id',$id)->where('status','1')->get()->toArray();
        $result = array();

        $totalcount = 0;
        $totalsubcat = "";
        foreach ($Build as $item) {
            if($item['subcategory']){
                $substr = $item['subcategory'];
                $totalsubcat .= $substr.",";
             }
        }

        $totalsubcat =  rtrim($totalsubcat,",");

//seperate individual group
        //convert string array

         $totalsubcat = explode(",",$totalsubcat);

         $Maincategory = Categories::all()->toArray();

         $subresult = array();
         $result = array();

        foreach ($Maincategory as $maincat) {



             $Subcategory = Subcategory::where('category_id',$maincat['id'])->get()->toArray();

            if($Subcategory){

                    $subresult = array();

                    foreach ($Subcategory as $subcat) {

                         $subcatcount= 0;

                         foreach ($totalsubcat as $item) {

                             if($subcat['id'] == $item){

                                    $subcatcount += 1;

                             }
                         }

                         $subresult[$subcat['subcategory_name']] = $subcatcount;

                    }

                 $result[] = $subresult;
             }
        }

       return $result;

    }


    public function getMainCategoryByID($id){

        $Build = Builds::where('employee_id',$id)->where('status','1')->get()->toArray();
        $result = array();

        $totalcount = 0;
        $totalsubcat = "";

        //get subcategory string array

        foreach ($Build as $item) {
          if($item['subcategory']){
              $substr = $item['subcategory'];
              $totalsubcat .= $substr.",";
          }
        }

        $totalsubcat =  rtrim($totalsubcat,",");

//seperate individual group
        //convert string array

         $totalsubcat = explode(",",$totalsubcat);
         $Maincategory = Categories::all()->toArray();
         $subresult = array();
         $result = array();

        foreach ($Maincategory as $maincat) {

             $totalmaincount = 0;

             $Subcategory = Subcategory::where('category_id',$maincat['id'])->get()->toArray();

            if($Subcategory){

                    $subresult = array();

                    foreach ($Subcategory as $subcat) {

                         $subcatcount= 0;

                         foreach ($totalsubcat as $item) {

                             if($subcat['id'] == $item){

                                    $subcatcount += 1;

                             }
                         }

                         $subresult[$subcat['subcategory_name']] = $subcatcount;
                         $totalmaincount += $subcatcount;
                    }

                    $result[$maincat['category_name']] = $totalmaincount;

             }
        }

    return $result;


    }


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

        $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
        if(!$employee){
            return redirect()->route('master.employee.list')->with('errors','No employee Found.');
        }
        $employee_data['industry'] = Industry::where('company_id', $employee->company_id)->get();
        $employee_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        $employee_data['access_level']   = Accesslevel::where('id','<=',2)->get()->toArray();
        return view('master.employee.edit',compact('employee'))->with('employee_data', $employee_data);
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

        $employee = Employee::where('id',$id)->where('is_deleted','0')->first();

        if(!$employee || $employee == ''){
            return redirect()->route('master.employee.list')->with('errors','No employee Found.');
        }

        request()->validate([
            'full_name' => 'required',
            'phone_number' => 'required',
            'company' => 'required',
            'industry' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = array();
        $data['full_name'] = $request['full_name'];
        $data['industry'] = $request['industry'];
        $data['phone_number'] = $request['phone_number'];
        $data['access_level'] = $request['access_level'];

            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('master.employee.list')->with('error','Company Not found.');
            }
            $data['company_id'] = $request['company'];


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
                $path = 'images/employee/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
            }

        }

        Employee::find($id)->update($data);
        return redirect()->route('master.employee.list')->with('success','Employee updated successfully.');
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
        // if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        // } else{
        //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->count();
        // }
        if($employee == 0){
            return response()->json(['status'=>false,'message'=>'No employee Found.']);
        }
        // Employee::find($id)->delete();
        $data = array('is_deleted'=> '1');
        Employee::find($id)->update($data);
        $this->deleteEmployeeToken($id);
        return response()->json(['status'=>true,'message'=>'Employee deleted successfully.']);
    }

    public function restore($id)
    {
        $data = array('is_deleted'=> '0');

        Employee::find($id)->update($data);

        session()->flash('success', 'Employee restored successfully.');

        return redirect()->back();
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

   /* public function getCategoryFromCompany($id){
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
*/
   public function getIndustryFromCompany($id){

        if($id && $id > 0){

            $industry = Industry::select('id','industry_name')->whereIn('company_id',array($id, '0'))->get()->toArray();

            if($industry){
                $html = '<option value="0">Select Store</option>';
                foreach ($industry as $item) {
                    $html .= '<option value="'.$item['id'].'">'.$item['industry_name'].'</option>';
                }
                return response()->json(['status'=>true,'html'=>$html]);
            } else {
                return response()->json(['status'=>false,'message'=>'No Industry']);
            }
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }


    public function getResume($id){
        // if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
            if(!$employee){
                return redirect()->route('master.employee.index')->with('errors','No employee Found.');
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

        // } else{
        //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
            // if(!$employee){
            //     return redirect()->route('employee.index')->with('errors','No employee Found.');
            // } else {
            //     $employee = $employee->toArray();
            // }

        //     $all_challenge = DB::table('challenge')
        //         ->join('builds', 'builds.id', '=', 'challenge.build_id')
        //         ->select(DB::raw("sum(challenge.point) as point"))
        //         ->where('builds.employee_id', $id)
        //         ->where('builds.company_id', Auth::user()->id)
        //         ->where('challenge.status','!=', '-1')
        //         ->first();

        //     $win_challenge = DB::table('challenge')
        //         ->join('builds', 'builds.id', '=', 'challenge.build_id')
        //         ->select(DB::raw("sum(challenge.point) as point"))
        //         ->where('builds.employee_id', $id)
        //         ->where('builds.company_id', Auth::user()->id)
        //         ->where('challenge.status', '1')
        //         ->first();
        // }

        $company = Users::select('name')->where('id', $employee['company_id'])->first()->toArray();

        if(!$company){
            return redirect()->route('master.employee.index')->with('errors','employee\'s Company not Found.');
        }

        //getting company name
        $employee['company_name'] = $company['name'];

        //getting all challenges
        // $employee['all_challenge'] = ($all_challenge->point == '' ? 0 : $all_challenge->point);
        $employee['all_challenge'] = $all_challenge;

        //getting win challenges
        // $employee['win_challenge'] = ($win_challenge->point == '' ? 0 : $win_challenge->point);
        $employee['win_challenge'] = $win_challenge;

        //getting win validation
        // $employee['win_validation'] = Validations::where('employee_id', $id)->where('win', '1')->count();
        $employee['win_validation'] = Builds::where('employee_id',$id)->count();

        //getting percentile from company
        // $cmp_employee_list =  DB::table('validations')
        //     ->join('employee', 'employee.id', '=', 'validations.employee_id')
        //     ->select('validations.employee_id')
        //     ->where('employee.company_id', $employee['company_id'])
        //     ->where('validations.win', '1')
        //     ->count();

        // if($employee['win_validation'] == 0){
        //     $employee['top_validation_per'] = 0;
        // }else{
        //     $employee['top_validation_per'] = round(($employee['win_validation']*100)/$cmp_employee_list);
        // }

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
        //getting list according to the category
        // $build_categore =  DB::table('builds')
        //     ->join('categories', 'categories.id', '=', 'builds.category_id')
        //     ->select('builds.id','builds.category_id','categories.category_name', DB::raw("count(builds.category_id) as count"))
        //     ->where('builds.employee_id', $id)
        //     ->where('builds.status', '1')
        //     ->groupBy('builds.category_id')
        //     ->get();

        // $build_categore =  DB::table('categories')
        //     ->join('builds', 'categories.id', '=', 'builds.category_id')
        //     ->select('categories.category_name', DB::raw("count(categories.id) as count"))
        //     ->where('builds.employee_id', $id)
        //     ->where('builds.status', '1')
        //     ->groupBy('categories.id')
        //     ->get();

        $build_categore = DB::select('SELECT categories.id, categories.category_name,  COUNT(categories.id) as count FROM categories join builds ON builds.category_id = categories.id where employee_id = '.$id.'  and builds.status = "1" GROUP BY categories.id, categories.category_name');

        // $array_ids = array();
        // $build_categore_main = [];
        // foreach($build_categore as $build_cat){
        //     if(!array_key_exists($build_cat->id, $array_ids)){
        //         $build_cat->count = Validations::where('build_id', $build_cat->id)->where('win', '1')->count();
        //         foreach($build_categore as $build_cat_check){
        //             if($build_cat_check->category_id == $build_cat->category_id && $build_cat->id != $build_cat_check->id){
        //                 $array_ids[$build_cat_check->id] = 1;
        //                 $build_cat->count += Validations::where('build_id', $build_cat_check->id)->where('win', '1')->count();
        //             }
        //         }
        //         $build_categore_main[] = $build_cat;
        //     }
        // }

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

        // $build_categore_count =  DB::table('builds')
        //     ->join('categories', 'categories.id', '=', 'builds.category_id')
        //     ->select('builds.*','builds.category_id','categories.category_name')
        //     ->where('builds.employee_id', $id)
        //     ->where('builds.status', '1')
        //     ->count();

        // $left_total = round($build_categore_count/2);
        // $right_total = $build_categore_count-$left_total;

        // $categories_list_left =  DB::table('builds')
        //     ->join('categories', 'categories.id', '=', 'builds.category_id')
        //     ->select('builds.id','builds.category_id','categories.category_name')
        //     ->where('builds.employee_id', $id)
        //     ->where('builds.status', '1')
        //     ->limit($left_total)
        //     ->get();

        // foreach($categories_list_left as $build_cat){
        //     $build_cat->count = Validations::where('build_id', $build_cat->id)->where('win', '1')->count();
        // }
        // $employee['categories_list_left'] = $categories_list_left;

        // $categories_list_right =  DB::table('builds')
        //     ->join('categories', 'categories.id', '=', 'builds.category_id')
        //     ->select('builds.id','builds.category_id','categories.category_name')
        //     ->where('builds.employee_id', $id)
        //     ->where('builds.status', '1')
        //     ->skip($left_total)->limit($right_total)
        //     ->get();

        // foreach($categories_list_right as $build_cat){
        //     $build_cat->count = Validations::where('build_id', $build_cat->id)->where('win', '1')->count();
        // }
        // $employee['categories_list_right'] = $categories_list_right;
        $employee['created_at'] = 'UT-'.date("mdy-Hi", strtotime($employee['created_at']));
        // set static icon
        $employee['icon_left'] = array('fa-money','fa-bandcamp','fa-eercast','fa-meetup','fa-microchip','fa-podcast','fa-ravelry','fa-snowflake-o','fa-superpowers','fa-wpexplorer','fa-bolt','fa-bullseye','fa-cubes','fa-crosshairs','fa-dot-circle-o','fa-ellipsis-h','fa-fighter-jet','fa-fire','fa-gavel','fa-etsy');
        $employee['icon_right'] = array('fa-leaf','fa-lightbulb-o','fa-location-arrow','fa-qrcode','fa-rocket','fa-share-alt','fa-tag','fa-thumb-tack','fa-ticket','fa-tree','fa-trophy','fa-umbrella','fa-wrench','fa-book','fa-glass','fa-life-ring','fa-lightbulb-o','fa-puzzle-piece','fa-road','fa-spoon');

        return view('master.employee.resume',compact('employee'));
    }
}

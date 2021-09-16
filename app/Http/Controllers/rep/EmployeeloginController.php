<?php

namespace App\Http\Controllers\rep;

use App\Employee;
use App\Employee_requests;
use App\Users;
use App\Builds;
use App\Validations;
use App\Tenure;
use App\Accesslevel;
use App\Subcategory;
use App\Http\Controllers\API\ApiController;
use Auth;
use Session;
use DB;
use File;
use App\Categories;
use App\Notification;
use App\Industry;
use App\Challenge;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
//use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;

class EmployeeloginController extends Controller
{
    
    
        public function __construct()
        {               
           /*    echo "helo"; die;
            if (Session::get('employee')) {              
                echo 'in'; die;
            } */
        }

public function employeecreate(){
 
       $employee_data['access_level']   = Accesslevel::where('id','<=',1)->get()->toArray();

//Accesslevel::all()->toArray();
        $employee_data['region'] = Industry::select('industry_name')->where('company_id',Session::get('employee')->company_id)->get();
        return view('rep.employee.employeecreate')->with('employee_data', $employee_data);
        
}

public function employeestore(Request $request){
      
           request()->validate([
            'full_name' => 'required',
            'email' => 'required|unique:employee|max:255',
            'password' => 'required',
            'phone_number' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
   
        $data = Array (
            'full_name' => $request['full_name'],
            'email' => $request['email'],
            'phone_number' => $request['phone_number'],
            'company_id' => Session::get('employee')->company_id,
            'industry' => $request['industry'],
            'access_level' => $request['access_level']
        );

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
         return redirect()->route('rep.employee.list')->with('success','Employee created successfully.');
}

public function employeeupdate(Request $request,$id){

  
      request()->validate([
            'full_name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            
            'access_level'=> 'required'           
        ]);

      
        $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Session::get('employee')->company_id)->first();
     
        if(!$employee || $employee == ''){
            return redirect()->route('rep.employee.list')->with('errors','No employee Found.');
        }
 
        $data = array();
        $data['full_name'] = $request['full_name'];
        $data['industry'] = $request['industry'];
        $data['phone_number'] = $request['phone_number'];
        $data['access_level'] = $request['access_level'];

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
      return redirect()->route('rep.employee.list')->with('success','Employee updated successfully.');
 
}

 public function company($id)
    {
        if($id && $id > 0){
            $users = Users::find($id);
            if(!$users){
                return redirect()->route('rep.employee.list');
            }
            $employee = Employee::where('company_id', $id)->where('is_deleted','0')->latest()->paginate(5);
            $employee->from_where = '1';
            $employee->company_id = $id;
            return view('rep.employee.userlisting',compact('employee'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
        } else {
            return redirect()->route('rep.employee.list');
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

    public function employeelist(){
        //print_r(Session::get('employee')->company_id); die;
      //  $employee = Employee::where(['is_deleted' => '0', 'company_id' => Session::get('employee')->company_id])->get();
        
           //$employee = Employee::where('is_deleted','0')->where('company_id',Auth::user()->id)->latest() 
      //  $employee->from_where = '0';
      //  $employee->company_id = 0;
        return view('rep.employee.userlisting');
    }

    public function editemployee($id){

        $employee = Employee::where('id',$id)->first(); 
        
        if(!$employee){
            return redirect()->route('rep.employee.list')->with('errors','No employee Found.');
        }       

        $employee_data['categories'] = Categories::select('id','category_name')->where('company_id',Session::get('employee')->company_id)->get();     
        $employee_data['access_level']   = Accesslevel::where('id','<=',1)->get()->toArray();

//Accesslevel::all();

        return view('rep.employee.employeeedit',compact('employee'))->with('employee_data', $employee_data);


    }

    public function employeedatatable()
    {

            
         $employee = Employee::where(['is_deleted' => '0', 'company_id' => Session::get('employee')->company_id])->where('id','!=',Session::get('employee')->id)->where('access_level','<=',1)->where('is_request','0')->orderBy('created_at','desc')->get();                        

        if(!empty($employee)){
            foreach ($employee as $item) {
                  $item->company_name = "--";
                  $company_name = Users::select('name')->where('id',$item->company_id)->get()->first();
                  $item->company_name = $company_name->name;
              
                    $item->industry = '--';
                

                $industry_name = Industry::select('industry_name')->where('company_id',Session::get('employee')->company_id)->get()->first();
                $item->industry = $industry_name->industry_name;
                $originalDate = $item->created_at;
 		$newDate = date("d/m/y", strtotime($originalDate));  
 		$item->newdate = $newDate ;
            }
        }
        
        return Datatables::of($employee)->make(true);
    }


    public function showuser($id)
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
                $all_employee = Employee::where('company_id',$employee->company_id)->where('access_level',$employee->access_level)->where('is_deleted','0')->get();
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
 
                return view('rep.employee.showuserdetails',compact('employee'))->with('monthLyCountArray', $monthLyCountArray)->with('wincountbuilds', $wincountbuilds)->with('loosecountbuilds', $loosecountbuilds)->with('win_challenge',$win_challenge)->with('countdailySubmission',$countdailySubmission)->with('maincategory_data', $maincategory_data)->with('subcategory_data', $subcategory_data)->with('challangeiesByEid', $challangeiesByEid);

            } 
            else {
                return redirect()->route('rep.employee.list')->with('errors','No employee Found.');
            }
        } else {
            return redirect()->route('rep.employee.list')->with('errors','No employee Found.');
        }
   



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

     
    public function getChallengeCountsByID($company_id,$id){

        $result = array();
        $resultitem = array();
        $maincategories = Categories::where('company_id',$company_id)->get();
        foreach($maincategories as $maincat){              
               $win_chal_cnt = Challenge::where('company_id',$company_id)
                            ->where('category_id',$maincat->id)->where('employee_id',$id)
                            ->where('status','1')->get()->count();
                $resultitem['maincat_name'] = $maincat->category_name;
                $resultitem['count'] = $win_chal_cnt;
                $result[] = $resultitem;
        }
       
       return $result;

    }

 
 
    public function home(){
    
        $challenge = Challenge::where('company_id', Session::get('employee')->company_id)->get()->count();
   
        $employee = Employee::where(['is_deleted' => '0', 'company_id' => Session::get('employee')->company_id])->where('access_level','<=',1)->count();
      
        $categories = Categories::where('company_id',Session::get('employee')->company_id)->count();
        
        $companyBuilds = Builds::where('company_id', Session::get('employee')->company_id)->select('id')->get()->toArray();
        
        $fiveActiveChallanges = Challenge::where('status','0')->where('company_id', Session::get('employee')->company_id)->limit(5)->get()->toArray();
        
        $builds = Builds::where('employee_id', Session::get('employee')->id)->orWhere('company_id', Session::get('employee')->company_id)->count();
        
        $topFiveWinBuilds = $this->getLooseAndWin('1');        
        $topFiveLoseBuilds = $this->getLooseAndWin('0');
        $topFiveCurrentBuilds = $this->getLooseAndWin('-1');
        
        $data = array('employee'=>$employee,'challenge'=>$challenge,'builds'=>$builds,   'categories'=>$categories, 'topFiveWinBuilds'=>$topFiveWinBuilds,'topFiveLoseBuilds'=>$topFiveLoseBuilds,'topFiveCurrentBuilds'=>$topFiveCurrentBuilds,'fiveActiveChallanges'=>$fiveActiveChallanges);

        return view('rep.employeehome')->with('data',$data);
    }



    public function getemployeechallenge(){
      //  $challenge = Challenge::where('company_id', Session::get('employee')->id)->latest()->paginate(5);
        return view('rep.employee.employeechallenge');
    }

    public function challangerequests(){
        $challenge = Challenge::where('company_id', Session::get('employee')->id)->latest()->paginate(5);
        return view('employee.challangerequests',compact('challenge'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function challengedatatable()
    {
        //where('company_id', Session::get('employee')->id)->
        //$challenge = Challenge::get();
        $challenge = Challenge::where('company_id', Session::get('employee')->id)->get();

        
        if(!empty($challenge)){
            foreach ($challenge as $item) {
                $item->user_level = Session::get('employee')->access_level;
                // $build = Builds::select('build_text')->where('id', $item->build_id)->first();
                // $item->build_name = '--';
                // if($build && !empty($build) && $build->build_text != ''){
                //     $item->build_name = $build->build_text;
                // }
                $item->challenge_text = '---';
                $item->created_at = '';
                $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_name = $user->name;
                }
                if($item->status == '-1'){
                    $item->status = "<label class='badge badge-info'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-warning'>Rejected</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-success'>Approved</label>";
                }
            }
        }       
        return Datatables::of($challenge)->rawColumns(['status'])->make(true);
    }


    public function categories(){
      
        return view('rep.employee.categories');
    }


    public function categoriesdatatable()
    {  

          $categories = Categories::where('company_id', Session::get('employee')->company_id)->get();
          $newcategories = array();
        
        if(!empty($categories)){
             

            foreach ($categories as $item) {

                  $subcategories = Subcategory::where('category_id',$item->id)->where('user_access_level','<',Session::get('employee')->access_level)->get();
                  
                  foreach ($subcategories as $sub) {
                   
                    $sub->id = $sub->id;
                     
                    $userlevel = Accesslevel::where('id',$sub->user_access_level)->first();

                    $sub->user_access_level = $userlevel['access_level_name'];
                    $sub->category_name = $item->category_name;
                    
                    $industry = Industry::select('industry_name')->where('id',$sub->region_id)->get()->first();
                     
                   $sub->industry_name = $industry['industry_name'];
                    $sub->created_date =  date('Y-m-d a h:i:s',strtotime($sub->created_at));

                    if($sub->status == 0){
                        $sub->status = "<label class='badge badge-danger'>Inactive</label>";
                    }
                    else{
                        $sub->status = "<label class='badge badge-info'>Active</label>";
                    }
                    $newcategories[] = $sub;
                }
                  
            } 
        }
       
       return Datatables::of($newcategories)->rawColumns(['status'])->make(true);
   }
    




    public function build(){
         
        return view('rep.employee.employeebuild');
    }

    public function buildrequests(){
       
        return view('rep.employee.buildrequests');
    }

    public function buildsdatatable()
    {
        $builds = Builds::where('employee_id', Session::get('employee')->id)->Where('company_id', Session::get('employee')->company_id)->where('is_request','0')
        	  ->orderBy('created_at','desc')->get(); 
        
        if(!empty($builds)){
            foreach ($builds as $item) {
                $item->user_level = Session::get('employee')->access_level;
		$item->created_date = date('Y-m-d a h:i:s',strtotime($item->created_at));
                $category = Categories::select('category_name')->where('id', $item->category_id)->first();
                $item->category_name = '--';
                if($category && !empty($category) && $category->category_name != ''){
                    $item->category_name = $category->category_name;
                }
                $employee = Employee::select('full_name')->where('id', $item->employee_id)->first();
                $item->employee_name = '--';
                if($employee && !empty($employee) && $employee->full_name != ''){
                    $item->employee_name = $employee->full_name;
                }
                if($item->status == '-1'){
                    $item->status = "<label class='badge badge-warning'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-danger'>Loss</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-info'>Win</label>";
                }
                $item->challenge_check = 'fa fa-close btn-outline-danger';
                if($item->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text')->where('id', $item->challenge_id)->first();
                    $item->challenge_name = '--';
                    
                    $item->challenge_check = 'fa fa-check btn-outline-info';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }
            }
        }
         
        return Datatables::of($builds)->rawColumns(['status'])->make(true);
    }


    public function buildshow($id)
    {
      if ($this->checklogin()) {
         
        if($id && $id > 0){
            
            
            $builds = Builds::find($id);
            
            if($builds){
                $category = Categories::select('category_name')->where('id', $builds->category_id)->first();
                $builds->category_name = '--';
                if($category && !empty($category) && $category->category_name != ''){
                    $builds->category_name = $category->category_name;
                }
                $employee = Employee::select('full_name','is_deleted')->where('id', $builds->employee_id)->first();
                $builds->employee_name = '--';
                if($employee && !empty($employee) && $employee->full_name != ''){
                    if($employee->is_deleted == '1'){
                        $builds->employee_name = 'Emplyee deleted';
                    } else {
                        $builds->employee_name = $employee->full_name;
                    }
                }
                if($builds->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text', 'image')->where('id',$builds->challenge_id)->first();
                    $builds->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->challenge_text != ''){
                        $builds->challenge_name = $challenge->challenge_text;
                        $builds->challenge_image = $challenge->image;
                    }
                }                
                 
                return view('rep.builds.show',compact('builds'));
                
            } else {
                return redirect()->route('rep.builds.list')->with('errors','No builds Found.');
            }
            
        } else {
            return redirect()->route('rep.builds.list')->with('errors','Parameter missing.');
        }
      }else{
        // login first
        return redirect()->route('executive.logout');  
      }
      

    }


    public function builddelete($id)
    {
            $builds = Builds::find($id);
            
            //$builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
            
            if($builds && $builds != ''){
                if($builds->image != ''){
                    $filename = 'images/build/'.$builds->image;
                    Storage::disk("s3")->delete($filename);
                    // if(File::exists($filename)){                    
                    //     File::delete($filename); 
                    // }
                }
                if($builds->challenge_id != 0){
                    $this->changChallangeStatus($builds->challenge_id, '0');
                }
                $builds->delete();
            } else {
                return response()->json(['status'=>false,'message'=>'No builds Found.']);
            }
          
          return response()->json(['status'=>true,'message'=>'Builds deleted successfully.']);   

    }

    public function setrequest($deleteid, $empid, $table, $request_type, $data=''){       
        if ($table != '') {
             $insert = ['from_emp_id' => $empid, 'request_type' => $request_type, 'requested_id' => $deleteid, 'status' => 'requested', 'from_tabel' => $table, 'data' => $data];
            //$tab = ['from_tabel' => 'build'];
            //array_push ($insert, $tab);     
        }        
        Employee_requests::insert($insert);
        return true;
    }


    public function editbuild($id){
       $builds_data = array();
       
       $builds = Builds::where('id', $id)->first();
       
       //print_r($builds); die; ['id' => '213'];
       $builds_data['employee'] = Employee::select('id','full_name')->where('company_id',Session::get('employee')->id)->where('is_deleted','0')->get()->toArray();
        //print_r($builds_data); die;

        if($builds == ''){
            return redirect('employee/build')->with('errors','No builds Found.');
        }
        
        $builds_data['category'] = Categories::select('id','category_name')->whereIn('company_id',array($builds->company_id, '0'))->get()->toArray();
        $challenge = Challenge::select('id','challenge_text','status')->where('company_id',$builds->company_id)->where('category_id',$builds->category_id)->get()->toArray();
        $challenge_data = array();
        // print_r($builds);
        // print_r($challenge);

        if($challenge){
            foreach ($challenge as $item) {
                if(($item['status'] == '0' && $item['id'] == $builds->challenge_id) || $item['status'] == '-1'){
                    $challenge_data[] = $item;
                }
            }
        }
        $builds_data['challenge'] = $challenge_data;
        // print_r($challenge_data);die;
        if($builds->status != '-1'){
            return view('rep.employee.editbuild');
        }
       
        return view('rep.employee.editbuild',compact('builds'))->with('builds_data', $builds_data);
    }

    public function updatebuild(Request $request)
    {

        request()->validate([
            'build_text' => 'required',
            'category' => 'required',
            'status' => 'required',
            'employee' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $data = Array (
            'build_text' => $request['build_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'employee_id' => $request['employee'],
            'challenge_id' => 0,

        );

        $data = json_encode($data);
    
        $id = $request['build_id'];
    
              
              $get_build = Builds::where('id', $request['build_id'])->get()->first();
              
              if($get_build == ''){
                  return redirect()->route('rep.builds.list')->with('errors','No build Found.');
              }
              if($get_build->status != '-1'){
                  return redirect()->route('rep.builds.list')->with('errors','This build is closed.');
              }              
              request()->validate([
                  'build_text' => 'required',
                  'category' => 'required',
                  'status' => 'required',
                  'employee' => 'required',
                  'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
              ]);
              $data = Array (
                  'build_text' => $request['build_text'],
                  'category_id' => $request['category'],
                  'status' => $request['status'],
                  'employee_id' => $request['employee'],
                  'challenge_id' => 0
              );
            
              $employee = Employee::select('company_id')->where('id', $request['employee'])->where('is_deleted','0')->first();
              if($employee){
                  $data['company_id'] = $employee->company_id;
              } else {
                  // Session::put('edit_build_error', 'The given employee is not found.');
                  return redirect()->route('rep.builds.list',['id'=>$id])->with('error','The given employee is not found.');
                  // exit;
              }
              
              if(isset($request['challenge']) && $request['challenge'] != '' && $request['challenge'] != '0'){
                  if($get_build->challenge_id == $request['challenge']){
                      $data['challenge_id'] = $request['challenge'];
                  } else {
                      $check_chall = Builds::where('id','!=', $id)->where('challenge_id',$request['challenge'])->count();
                      if($check_chall == 0){

                          $challenge = Challenge::where('id',$request['challenge'])->where('company_id',$get_build->company_id)->count();

                          if($challenge != 0){
                              $challenge_list = Challenge::where('id',$request['challenge'])->where('company_id',$get_build->company_id)->first();
                              if($challenge_list->status != '1'){
                                  $data['challenge_id'] = $request['challenge'];
                              }
                          } else {
                              return redirect()->route('rep.builds.list',['id'=>$id])->with('error','The challenge is not available.');
                          }
                      } else {
                          return redirect()->route('rep.builds.list',['id'=>$id])->with('error','The selected challenge already in used.');
                      }
                  }
              }

              if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
                  // $get_build = Builds::find($id);
                  if($get_build->image != ''){
                      $filename = 'images/build/'.$get_build->image;
                      Storage::disk("s3")->delete($filename);
                    //   if(File::exists($filename)){                    
                    //       File::delete($filename); 
                    //   }
                  }
                  if($request->hasFile('image')){
                      $imageName = time().'.'.$request->image->getClientOriginalExtension();
                      $data['image'] = $imageName;
                  }
                  if(isset($request->delete_image) && $request->delete_image == 1){
                      $data['image'] = '';
                  }
              }

              $builds = Builds::find($id)->update($data);
              $winData = array('win'=>'1');
              $loseData = array('win'=>'0');
              if($data['status'] == '1'){
                  Validations::where("build_id",$id)->where('status','1')->update($winData);
                  Validations::where("build_id",$id)->where('status','0')->update($loseData);
              } else if($data['status'] == '0'){
                  Validations::where("build_id",$id)->where('status','0')->update($winData);
                  Validations::where("build_id",$id)->where('status','1')->update($loseData);
              }
              if($request->hasFile('image')){
                  $path = 'images/build/';
                  $file = $request->file("image");
                  Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
                //   File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                //   $request->image->move(public_path('images/build'), $imageName);
              }
              if($get_build->challenge_id == $request['challenge']){
                  $this->changChallangeStatus($request['challenge'], $request['status']);
              } else {
                  if($get_build->challenge_id != 0){
                      $this->changChallangeStatus($get_build->challenge_id, '0');
                  }
                  $this->changChallangeStatus($request['challenge'], $request['status']);
              }

//add part 
             $api = new ApiController;   
             $emp_fullname = Session::get('employee')->full_name;
               if($data['status'] == '1'){
                     
                       $message = "Congratulations ".$emp_fullname.". Your Submission has been approved!";                    
                        $api->sendpush($emp_id,'Submission approved',$message,$data,'buildApprove');
               }
    
               elseif($data['status'] == '0')  {
    
                  $emp_id = Builds::select('id')->where('employee_id',$request['employee'])->get()->first();
                  $emp_fullname = Employee::select('full_name')->where('id',$emp_id)->get()->first();
                   $message = "Sorry ".$emp_fullname.". Your Upload has been rejected!";                    
                   $api->sendpush($emp_id,'Submission rejected',$message,$data,'buildRejected');
               }        

              return redirect()->route('rep.builds.list')->with('success','Submission updated successfully.');


      
    }

    public function createbuild(Request $request)
    {

       $builds_data = array();

        $employee = Session::get('employee');
        $categories = Categories::select('id','category_name')->whereIn('company_id',array($employee->company_id, '0'))->get()->toArray();
        return view('rep.employee.createbuild')->with('categories', $categories);
    

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storebuild(Request $request)
    {
        // print_r($request->all());die;
        request()->validate([
            'build_text' => 'required',
            'category' => 'required',
            'status' => 'required',
            'employee' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $data = Array (
            'build_text' => $request['build_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'employee_id' => $request['employee'],
            'image' => $request['image'],
            'challenge_id' => 0,
        );
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $path = 'images/build/';
            $file = $request->file("image");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $data['image'] = $imageName ;
        }
        $employee = Employee::select('full_name','company_id')->where('id', $request->employee)->where('is_deleted','0')->first();
        $challenge = 0;
        if(isset($request['challenge']) && $request['challenge'] != '0'){
                $challenge = Challenge::where('status', '-1')->where('id',$request['challenge'])->count();
            }
            $employee = Employee::select('full_name','company_id')->where('id', $request->employee)->where('is_deleted','0')->first();

      
        if($employee){
            $data['company_id'] = $employee->company_id;
        } else {
            // Session::put('create_build_error', 'The given employee is not found.');
            return redirect()->route('employee.createbuild')->with('error','The given employee is not found.');
            // exit;
        }
        if($challenge != 0){
            $data['challenge_id'] = $request['challenge'];
        }
        $builds = Builds::create($data);
         
        if($challenge != 0){
            $this->changChallangeStatus($request['challenge'], $request['status']);
        }
        $category = Categories::select('category_name')->where('id', $request['category'])->first();
        if($category){
            $path = storage_path() . '/../public/js/notification.json';
            $json = json_decode(file_get_contents($path), true);
            $message = $json['buildWithCategory'];
            if($message != ''){
                $message = str_replace("{{EMPNAME}}",$employee->full_name,$message);
                $message = str_replace("{{CATNAME}}",$category->category_name,$message);
                $message = str_replace("{{BUILDNAME}}",$request['build_text'],$message);
            } else {
                $message = $employee->full_name. " create a new build '".$request['build_text']."' with '".$category->category_name."' category.";
            }
           $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>Session::get('employee')->company_id,
                  'receiver_type'=>2
              );
    
		Notification::create($list_param);
           
            
        }
        return redirect()->route('rep.builds.list')->with('success','Build created successfully.');
    }

   public function challengerequest(){
       
       return view('rep.employee.challangerequests'); 

   }
    public function getChallengeFromEmployeeAndCategory($build_id, $emp_id, $cat_id){
        if($emp_id && $emp_id > 0 && $cat_id && $cat_id > 0){
            $builds = Builds::find($build_id);
            
                $employee = Employee::where('id',$emp_id)->where('is_deleted','0')->first();
            
            if(!$employee){
                return response()->json(['status'=>false,'message'=>'Selected employee not found.']);
            }
            $categories = Categories::select('id','category_name')->whereIn('company_id',array($employee->company_id, '0'))->first();
            if(!$categories){
                return response()->json(['status'=>false,'message'=>'Selected category not found.']);
            }
            if($build_id == 0){
                $challenge = Challenge::select('id','challenge_text')->where('status', '-1')->where('company_id',$employee->company_id)->where('category_id',$cat_id)->get()->toArray();
                $challenge_html = '<option value="0">No challenge available</option>';
                if($challenge){
                    $challenge_html = '<option value="0">select challenge</option>';
                    foreach ($challenge as $item) {
                        $challenge_html .= '<option value="'.$item['id'].'">'.$item['challenge_text'].'</option>';
                    }
                }
            } else {
                $challenge = Challenge::select('id','challenge_text','status')->where('company_id',$employee->company_id)->where('category_id',$cat_id)->get()->toArray();
                $challenge_html = '<option value="0">No challenge available</option>';
                if($challenge){
                    $challenge_html = '<option value="0">select challenge</option>';
                    foreach ($challenge as $item) {
                        if(($item['status'] == '0' && $item['id'] == $builds->challenge_id) || $item['status'] == '-1'){
                            $challenge_html .= '<option value="'.$item['id'].'">'.$item['challenge_text'].'</option>';
                        }
                    }
                }
                if($challenge_html == '<option value="0">select challenge</option>'){
                    $challenge_html = '<option value="0">No challenge available</option>';
                }
            }
            return response()->json(['status'=>true,'challenge_html'=>$challenge_html]);
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }

    public function showrequest($id){
       
       if ($id) {
            $requestdata =  Employee_requests::select('*')->where(['id' => $id, 'request_type' => 'edit'])->get()->first();
            if ($requestdata) {
                if ($requestdata['from_tabel'] = 'build') {
                
                    $saveddata = json_decode($requestdata['data']);                    
                    $viewdata['request_id'] = $requestdata['id'];
                    $viewdata['build_text'] =  $saveddata->build_text;         
                    $employee =  Employee::select('full_name')->where('id', $saveddata->employee_id)->get()->first()->toArray();       
                    $viewdata['employee_name'] =  $employee['full_name'];  
                    $viewdata['category_name'] = Categories::select('category_name')->where('id', $saveddata->category_id)->first();           
                    $viewdata['category_name'] = $saveddata->build_text;
                    $viewdata['image'] = '';
                         
                    return view('rep.employee.showbuild', $viewdata);
                }

                if ($requestdata['from_tabel'] = 'challenge') {

                    return view('rep.employee.showchallengerequest', $viewdata);

                }
            }
            
        }
     
    }    


    public function buildsrequestdatatable()
    {
        $Employee_requests = Employee_requests::where(['status' => 'requested', 'from_tabel' => 'build'])->get();       
        //$builds = Builds::where('company_id',Auth::user()->id)->get();
        
        if(!empty($Employee_requests)){
            foreach ($Employee_requests as $item) {
                $user_info = Employee::select('full_name')->where('id', $item->from_emp_id)->first();
                //print_r($user_info->full_name); die;
                $item->from_user = '--';
                if($user_info && !empty($user_info) && $user_info->full_name != ''){                 $item->from_user = $user_info->full_name;
                }
                $Build = Builds::select('build_text')->where('id', $item->requested_id)->first();
                $item->build_text = '---';
                if($Build && !empty($Build) && $Build->build_text != ''){
                    $item->build_text = $Build->build_text;
                }
                /*if($item->status == '-1'){
                    $item->status = "<label class='badge badge-warning'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-danger'>Loss</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-info'>Win</label>";
                }*/
              /*  if($item->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text')->where('id', $item->challenge_id)->first();
                    $item->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }*/
                //print_r($Employee_requests); die;
            }
        return Datatables::of($Employee_requests)->rawColumns(['status'])->make(true);
        }
    }



    public function challengerequestdatatable()
    { 
        $Employee_requests = Employee_requests::where([['status', 'requested'],['from_tabel','challenge']])->get();       
        //$builds = Builds::where('company_id',Auth::user()->id)->get();
        
        if(!empty($Employee_requests)){
            foreach ($Employee_requests as $item) {
                $user_info = Challenge::select('challenge_text')->where('id', $item->from_emp_id)->first();
                $item->from_user = '--';
                if($user_info && !empty($user_info) && $user_info->challenge_text != ''){                 $item->from_user = $user_info->challenge_text;
                }
                $Challenge = Challenge::select('challenge_text')->where('id', $item->requested_id)->first();
                $item->challenge_text = '-----';
                if($Challenge&& !empty($Challenge) && $Challenge->challenge_text != ''){
                    $item->challenge_text = $Challenge->challenge_text;
                }
                if($item->status == '-1'){
                    $item->status = "<label class='badge badge-warning'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-danger'>Loss</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-info'>Win</label>";
                }
              /*  if($item->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text')->where('id', $item->challenge_id)->first();
                    $item->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }*/
               /* print_r($Employee_requests); die;*/
            }
        }  
        return Datatables::of($Employee_requests)->rawColumns(['status'])->make(true);
    }


 

    public function handlerequest($id, $action){

        if ($id) { 
               $request =  Employee_requests::where('id', $id)->first();
               if ($request)
               {
                  if ($request->from_tabel == 'build' && $action == 'accept')
                  {
                    if ($request->request_type == 'delete') 
                    {
               
                       #delete record from build table and request from request table
                        $delete = Builds::where('id', $request->requested_id)->delete();
                        $request_delete = Employee_requests::where('id', $request->id)->delete();
                    return redirect()->route('rep.builds.list')->with('info','Request Approved Successfully.');
                        //return redirect()->route('employee.employeebuild')->with('errors','No builds Found.');
                    }

                    if ($request->request_type == 'edit')
                    {
                        #get data from request table and update into build  table then delete request from request table
                        if ($request->data) {
                            $getdata = json_decode($request->data);
                           //$insert = []
                            $insertdata = Array (
                                'build_text' => $getdata->build_text,
                                'category_id' => $getdata->category_id,
                                'status' => $getdata->status,
                                'employee_id' => $getdata->employee_id,
                                'challenge_id' => 0
                            );
                           
                            $builds = Builds::where('id', $id)->update($insertdata);               
                            $request_delete = Employee_requests::where('id', $request->id)->delete();
                        }
                        return redirect()->route('rep.builds.list')->with('info','Request Approved Successfully.');

                        
                    }   

                  } elseif ($request->from_tabel == 'build' && $action == 'rejected') {
                    $request_delete = Employee_requests::where('id', $request->id)->delete();
                   
                    return redirect()->route('rep.builds.list')->with('info','Request Rejected Successfully.');
                    
                  }

               } 
            } 
    }


    public function challengeshow($id)
    {
        if($id && $id > 0){
           // if(Auth::user()->role == 'admin'){
                $challenge = Challenge::find($id);
            
            if($challenge){

                $build = Builds::select('*')->where('challenge_id', $challenge->id)->first();
                //print_r($build); die;
                $challenge->build_name = '--';
                if($build && !empty($build) && $build->build_text != ''){
                    $challenge->build_name = $build->build_text;
                }
                $user = Users::select('id','name')->where('id', $challenge->company_id)->first();
                $challenge->company_name = '--';
                if($user && !empty($user) && $user->user_text != ''){
                    $challenge->company_name = $user->user_text;
                }
                $challenge->category_name = '--';
                // $challenge->employee_name = '--';
                // if(!empty($build)){
                $category = Categories::select('category_name')->where('id', $challenge->category_id)->first();
                
                if($category && !empty($category) && $category->category_name != ''){
                    $challenge->category_name = $category->category_name;
                }
              

                $user = Users::select('*' )->where('id', $challenge->company_id)->first();
                $challenge->company_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $challenge->company_name = $user->name;
                }

                $challenge->build = $build->image;
                //print_r($challenge); die;
               
                return view('challenge.show',compact('challenge'));
            } else {
                return redirect()->route('challenge.index')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect()->route('challenge.index')->with('errors','No challenge Found.');
        }
    }

    public function checklogin(){
         
        if (Session::has('employee')) {
            $userdata = Session::get('employee');
            if ($userdata['id']) {
                return $userdata;
            }else{
                return false;
                return redirect()->route('executive.logout');
            }            
        }else{
            return false;
            return redirect()->route('executive.logout');
        }
    }




    public function getLooseAndWin($status){
       
            $query = DB::table('builds')
                ->leftJoin('employee', 'employee.id', '=', 'builds.employee_id')
                ->select('builds.*','employee.full_name')
                ->where('builds.status',$status)
                ->where('builds.company_id', Session::get('employee')->company_id)
                ->limit(5)
                ->orderBy('id','desc')
                ->get()
                ->toArray();
  
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


    public function handlechallengerequest($id, $action){
        if ($id) { 
               $request =  Employee_requests::where('id', $id)->first();
               if ($request)
               {
                  if ($request->from_tabel == 'build' && $action == 'accept')
                  {
                    if ($request->request_type = 'delete') 
                    {
                       #delete record from build table and request from request table
                        $delete = Builds::where('id', $request->requested_id)->delete();
                        $request_delete = Employee_requests::where('id', $request->id)->delete();
                        return redirect()->route('rep.challengerequests.list')->with('info','Request Approved Successfully.');
                        //return redirect()->route('employee.employeebuild')->with('errors','No builds Found.');
                    }

                    if ($request->request_type == 'edit')
                    {
                        #get data from request table and update into build  table then delete request from request table
                    }   

                  } elseif ($request->from_tabel == 'build' && $action == 'rejected') {
                    if ($request->request_type == 'delete') 
                    {
                       //delete request from request table
                    }

                    if ($request->request_type == 'edit')
                    {
                        #delete request from request table
                    }     
                  }elseif ($request->from_tabel == 'challenge' && $action == 'accept')
                  { 
              
                    if ($request->request_type == 'delete') 
                    {
                       #delete record from build table and request from request table
                        $delete = Challenge::where('id', $request->requested_id)->delete();
                        $request_delete = Employee_requests::where('id', $request->id)->delete();

                        return redirect()->route('rep.challengerequests.list')->with('info','Request Approved Successfully.');
                        //return redirect()->route('employee.employeebuild')->with('errors','No builds Found.');
                    }

                    if ($request->request_type == 'edit')
                    {   
                        /*$delete = Challenge::where('id', $request->requested_id)->delete();*/
                        $Employee_requests = Employee_requests::where('id', $request->id)->get(); 
                        $challenge_data = json_decode($Employee_requests[0]->data);
                        $mdata['challenge_text'] = $challenge_data->challenge_text;
                        $mdata['status'] = $challenge_data->status;
                        $mdata['point'] = $challenge_data->point;
                        $challenge = Challenge::find($Employee_requests[0]->requested_id)->update($mdata);

                        $request_delete = Employee_requests::where('id', $request->id)->delete();
                        
                        return redirect()->route('rep.challengerequests.list')->with('info','Request Approved Successfully.');
                    }   

                  } elseif ($request->from_tabel == 'challenge' && $action == 'reject') {
                     $request_delete = Employee_requests::where('id', $request->id)->delete();
                        return redirect()->route('rep.challengerequests.list')->with('info','Request Rejected Successfully.');
                         
                  }else{
                    if ($request->request_type == 'delete') 
                    {
                       //delete request from request table
                    }

                    if ($request->request_type == 'edit')
                    {
                        #delete request from request table
                    }     
                  }

               } 
            }    
    }



    public function challengedit($id)
    {  
        $challenge = Challenge::where('id',$id)->where('company_id', Session::get('employee')->id)->first();
       
        if($challenge == ''){
            return redirect()->route('employee.employeechallenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1'){
            return view('employee.challengeedit');
        }
        $challenge_data = array();
        $user = Session::get('employee');
        $challenge_data = array('is_admin' => 0);
        if($user->role == 'admin'){
            $challenge_data['is_admin'] = 1;
            $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();

            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($challenge->company_id, '0'))->get()->toArray();
            
        } else {
            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->id, '0'))->get()->toArray();
        }
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::select('id','build_text')->get()->toArray();
        // } else{
        //     $builds = Builds::select('id','build_text')->where('company_id',Auth::user()->id)->get()->toArray();
        // }
        // $build_array = array();
        // if(!empty($builds)){
        //     foreach ($builds as $item) {
        //         if($item['id'] == $challenge->build_id){
        //             $build_array[] = $item;
        //         } else {
        //             $chal = Challenge::select('id')->where('build_id', $item['id'])->first();
        //             if($chal && !empty($chal)){
        //             }else {
        //                 $build_array[] = $item;
        //             }
        //         }
        //     }
        // }
        // $challenge_data['builds'] = $build_array;
        // print_r($challenge_data); die;
        return view('employee.challengeedit',compact('challenge'))->with('challenge_data', $challenge_data);
    }

    public function challengeupdate(Request $request, $id){
        $loginuserdata = $this->checklogin(); 
         $sendreq =  $this->setrequest($id, $loginuserdata['id'], 'challenge', 'edit', json_encode($request->all()));
        if ($sendreq) {
                // return redirect()->route('employee.challenge')->with('info','Request Approved Successfully.');
                return redirect('employee/challenge')->with('info','Request Approved Successfully.');
            }
    }


    public function challengedelete($id)
    {
        $loginuserdata = $this->checklogin();       
        if ($loginuserdata) {
          if ($loginuserdata['access_level'] == '2' || $loginuserdata['access_level'] == '3') { 
            /*$builds = Builds::find($id);
            
            //$builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
            
            if($builds && $builds != ''){
                if($builds->image != ''){
                    $filename = public_path().'/images/build/'.$builds->image;
                    if(File::exists($filename)){                    
                        File::delete($filename); 
                    }
                }
                if($builds->challenge_id != 0){
                    $this->changChallangeStatus($builds->challenge_id, '0');
                }
                $builds->delete();
            } else {
                return response()->json(['status'=>false,'message'=>'No builds Found.']);
            }
           
            return response()->json(['status'=>true,'message'=>'Builds deleted successfully.']);*/
          }else{ 
            //send request to level 2 or 3 user
            $sendreq =  $this->setrequest($id, $loginuserdata['id'], 'challenge', 'delete', '');

            if ($sendreq) {
                return response()->json(['status'=>true,'message'=>'Builds Delete request send successfully.']);
            }
          }  
        }else{
        // login first
        return redirect()->route('login');  
      }    
    }


    public function addcategory()
    {
             
            $employee = Employee::find(Session::get('employee')->id);
            $categories_data['is_level_3_user'] = 1;
        
            $categories_data['employee'] = $employee;
            $categories_data['Categories'] = Categories::where('company_id',$employee->company_id)->get()->toArray();
            $categories_data['Region'] = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
            $categories_data['access_level'] = Accesslevel::where('id','<=',1)->get()->toArray();

//Accesslevel::all();
     return view('rep.employee.add_category')->with('categories_data',$categories_data); 
          
         //echo "123";
    }


    public function storecategory(Request $request)
    {
        
        request()->validate([
            'main_category' => 'required',
            'sub_category_name' => 'required',
            'region_select'  => 'required',
            'user_level' => 'required'
        ]);
       
        $data = Array (
            'category_id'           => $request['main_category'],
            'subcategory_name'      => $request['sub_category_name'],
            'user_access_level'     => $request['user_level'],
            'region_id'             => $request['region_select']
 
        );
    
      Subcategory::create($data);
      return redirect()->route('rep.categories.list')->with('success','Categories created successfully.');
    
    }

  



  public function editcategory($id)
    {

        $employee = Employee::find(Session::get('employee')->id);
        $category = Subcategory::where('category_id',$id)->get()->count();
        
           
            $categories_data['employee'] = $employee;
            $categories_data['Categories'] = Categories::where('company_id',$employee->company_id)->get()->toArray();
            $categories_data['sub_category'] = Subcategory::where('category_id',$id)->first();
            $categories_data['access_level'] = Accesslevel::where('id','<',$employee->access_level)->get()->toArray();
//Accesslevel::all();
            $categories_data['is_exist'] = $category;
    $categories_data['region'] = Industry::all();
        $subcategory = Subcategory::where('id',$id)->first();
    $categories_data['region'] = Industry::all();
        //    $categories_data['is_level_3_user'] = 1;
          if($subcategory == ''){
            return redirect('executive.categories.list')->with('error','No subcategory Fount.');
          }
        return view('rep.employee.edit_category',compact('subcategory'))->with('categories_data',$categories_data); 
    }

    public function updatecategory(Request $request, $id)
    {
          request()->validate([
             
            'user_level' => 'required',
            'status' => 'required',
            'region'  =>'required'
        ]);
        $category = Subcategory::where('id',$id)->get()->count();
        
        if(!$category){
                $data = Array (
                    'category_id'           => $id,
                    
                    'user_access_level'     => $request['user_level'],
                    'created_at'            => date('Y-m-d H:i:s'),
                    'region_id'             => $request['region'],
                    'status'                => $request['status']
                );   
       $category = Subcategory::create($data);
        }else{
            $data = Array (
                 
                'user_access_level'     => $request['user_level'],
                 'region_id'             => $request['region'],
                 'status'                => $request['status']
            );
            $category = Subcategory::where('id',$id)->update($data);
        }
        return redirect()->route('rep.categories.list')->with('success','Categories updated successfully.');
    }

    public function deletecategory($id)
    {
         $sub_category = Subcategory::where('id',$id)->get()->count();
        if($sub_category){
            $sub_category_data = Subcategory::where('id',$id)->delete();
            return response()->json(['status'=>true,'message'=>'Sub Category deleted successfully.']);
        }
       
        return response()->json(['status'=>false,'message'=>'No Subcategory Found']);
    }

    public function changChallangeStatus($id, $status){
        $ch_data = Array (
            'status' => '0'
        );
        if($status == '1'){
            $ch_data['status'] = '1';
        } else if($status == '0'){
            $ch_data['status'] = '-1';
        }
        $challenge = Challenge::find($id);
        if($challenge && $challenge != ''){
            Challenge::find($id)->update($ch_data);
        }
    }
    
}

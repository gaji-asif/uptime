<?php

namespace App\Http\Controllers\leader;

use App\Requests;
use App\Builds;
use App\Validations;
use Illuminate\Http\Request;
use App\Categories;
use App\Employee;
USE App\Notification;
use Yajra\Datatables\Datatables;
use App\Challenge;
use App\Http\Controllers\API\ApiController;
use File;
use Auth;
 use Session;

class BuildsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'admin'){
            $builds = Builds::latest()->paginate(5);
        } else{
            $builds = Builds::where('company_id',Auth::user()->id)->latest()->paginate(5);
        }
        $builds->from_where = '0';
        $builds->employee_id = 0;
        $builds->employee_g_status = '';
        return view('leader.builds.index',compact('builds'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        
    }

    public function buildsdatatable()
    {
        
       $builds = Builds::all();
        if(Auth::guard('admin')->user()->access_level == 5 ){
            $builds = Builds::all();
        } else{
            $builds = Builds::where('company_id',Auth::guard('admin')->user()->comp)->get();
        }
        if(!empty($builds)){
            foreach ($builds as $item) {
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
                    $item->challenge_check = 'fa fa-check btn-outline-info';
                    $item->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }
            }
        }

        return Datatables::of($builds)->rawColumns(['status'])->make(true);
    }

    //employee validation list
    public function employeeBuild($id)
    {
        if(Auth::guard('admin')->user()->access_level == 5){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        } else{
            $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::guard('admin')->user()->company_id)->count();
        }
        if($employee == 0){
            return redirect()->route('leader.builds.list')->with('errors','Employee not found.');
        }
        $builds = Builds::where('employee_id', $id)->latest()->paginate(5);
        $builds->from_where = '1';
        $builds->employee_id = $id;
        $builds->employee_g_status = '';
        return view('leader.builds.index',compact('builds'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
 
    //employee validation list - Datatabel call
    public function employeeBuildData($id)
    {
        $employee = Employee::select('full_name')->where('id', $id)->first();
        $builds = Builds::where('employee_id', $id)->get();
        if(!empty($builds)){
            foreach ($builds as $item) {
                $category = Categories::select('category_name')->where('id', $item->category_id)->first();
                $item->category_name = '--';
                if($category && !empty($category) && $category->category_name != ''){
                    $item->category_name = $category->category_name;
                }
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
                if($item->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text')->where('id', $item->challenge_id)->first();
                    $item->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }
            }
        }
        return Datatables::of($builds)->rawColumns(['status'])->make(true);
    }


    //employee validation list - Datatabel call
    public function employeeBuildByWinLoseData($id,$status)
    {
        $employee = Employee::select('full_name')->where('id', $id)->first();
        $builds = Builds::where('employee_id', $id)->where('status',$status)->get();
        if(!empty($builds)){
            foreach ($builds as $item) {
                $category = Categories::select('category_name')->where('id', $item->category_id)->first();
                $item->category_name = '--';
                if($category && !empty($category) && $category->category_name != ''){
                    $item->category_name = $category->category_name;
                }
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
                if($item->challenge_id != 0){
                    $challenge = Challenge::select('id','challenge_text')->where('id', $item->challenge_id)->first();
                    $item->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->full_name != ''){
                        $item->challenge_name = $challenge->challenge_text;
                    }
                }
            }
        }
        return Datatables::of($builds)->rawColumns(['status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $builds_data = array();
        // $builds_data['category'] = Categories::select('id','category_name')->get()->toArray();
        // $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
        // if(Auth::user()->role == 'admin'){
            $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
        // } else {
            // $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->where('company_id',Auth::user()->id)->get()->toArray();
        // }
        // if(Session::has('create_build_error')){
        //     $builds_data['error'] = Session::get('create_build_error');
        //     Session::forget('create_build_error');
        //     Session::save();
        // }
        return view('leader.builds.create')->with('builds_data', $builds_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
            $data['image'] = $imageName;
        }
        $employee = Employee::select('full_name','company_id')->where('id', $request->employee)->where('is_deleted','0')->first();
        $challenge = 0;
        // if(Auth::user()->role == 'admin'){
            if(isset($request['challenge']) && $request['challenge'] != '0'){
                $challenge = Challenge::where('status', '-1')->where('id',$request['challenge'])->count();
            }
        // } else {
        //     $employee = Employee::select('full_name','company_id')->where('id', $request->employee)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
        //     if(isset($request['challenge']) && $request['challenge'] != '0'){
        //         $challenge = Challenge::where('status', '-1')->where('id',$request['challenge'])->where('company_id',Auth::user()->id)->count();
        //     }
        // }
        if($employee){
            $data['company_id'] = $employee->company_id;
        } else {
            // Session::put('create_build_error', 'The given employee is not found.');
            return redirect()->route('builds.create')->with('error','The given employee is not found.');
            // exit;
        }
        if($challenge != 0){
            $data['challenge_id'] = $request['challenge'];
        }
        $builds = Builds::create($data);
        if($builds->id && $request->hasFile('image')){
            $path = public_path().'/images/build/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/build'), $imageName);
        }
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
            /*$list_param = array(
                'send_to' => $employee->company_id,
                'message' => $message
            );
            Notification::create($list_param);*/
        }
        return redirect()->route('leader.builds.list')->with('success','Build created successfully.');
    }


    public function employeeStatusData($id,$status){
        $builds = Builds::latest()->paginate(5);
        $builds->from_where = '2';
        $builds->employee_id = $id;
        $builds->employee_g_status = $status;
        return view('leader.builds.index',compact('builds'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Builds  $builds
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id && $id > 0){
            
            // if(Auth::user()->role == 'admin'){
                $builds = Builds::find($id);
            // } else {
            //     $builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
            // }
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
                    $challenge = Challenge::select('id','challenge_text')->where('id',$builds->challenge_id)->first();
                    $builds->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->challenge_text != ''){
                        $builds->challenge_name = $challenge->challenge_text;
                    }
                }
                return view('leader.builds.show',compact('builds'));
            } else {
                return redirect()->route('leader.builds.list')->with('errors','No builds Found.');
            }
            
        } else {
            return redirect()->route('leader.builds.list')->with('errors','Parameter missing.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Builds  $builds
     * @return \Illuminate\Http\Response
     */
    public function edit(Builds $builds,$id)
    {
        $builds_data = array();
        // if(Auth::user()->role == 'admin'){
            $builds = Builds::find($id);
            $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
        // } else {
        //     $builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
        //     $builds_data['employee'] = Employee::select('id','full_name')->where('company_id',Auth::user()->id)->where('is_deleted','0')->get()->toArray();
        // }
        if($builds == ''){
            return redirect()->route('leader.builds.list')->with('errors','No builds Found.');
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
            return view('leader.builds.edit');
        }
        // if(Session::has('edit_build_error')){
        //     $builds_data['error'] = Session::get('edit_build_error');
        //     Session::forget('edit_build_error');
        //     Session::save();
        // }
        return view('leader.builds.edit',compact('builds'))->with('builds_data', $builds_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Builds  $builds
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if(Auth::user()->role == 'admin'){
            $get_build = Builds::find($id);
        // } else {
        //     $get_build = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
        // }
        if($get_build == ''){
            return redirect()->route('leader.builds.list')->with('errors','No build Found.');
        }
        if($get_build->status != '-1'){
            return redirect()->route('leader.builds.list')->with('errors','This build is closed.');
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

        $employee = Employee::select('company_id','full_name')->where('id', $request->employee)->where('is_deleted','0')->first();
        if($employee){
            $data['company_id'] = $employee->company_id;
        } else {
            // Session::put('edit_build_error', 'The given employee is not found.');
            return redirect()->route('leader.builds.list',['id'=>$id])->with('error','The given employee is not found.');
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
                        return redirect()->route('leader.builds.list',['id'=>$id])->with('error','The challenge is not available.');
                    }
                } else {
                    return redirect()->route('leader.builds.list',['id'=>$id])->with('error','The selected challenge already rejected.');
                }
            }
        }

        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
            // $get_build = Builds::find($id);
            if($get_build->image != ''){
                $filename = public_path().'/images/build/'.$get_build->image;
                if(File::exists($filename)){                    
                    File::delete($filename); 
                }
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
            $path = public_path().'/images/build/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/build'), $imageName);
        }
        if($get_build->challenge_id == $request['challenge']){
            $this->changChallangeStatus($request['challenge'], $request['status']);
        } else {
            if($get_build->challenge_id != 0){
                $this->changChallangeStatus($get_build->challenge_id, '0');
            }
            $this->changChallangeStatus($request['challenge'], $request['status']);
        }

        $api = new ApiController;

        $builds_data = Builds::find($id);

        
        if($builds_data['challenge_id'] == 0)        
        {
          $title_app = "Submission Approved ";
          $title_reject = "Submission Rejected ";
          
            if($request['status'] == 0 ){
             
                   $message = "Sorry ".$employee->full_name.". Your Upload ".$request['build_text']." is rejected.";
   
                   $api->sendpush($builds_data -> employee_id,$title_reject,$message,$data,'buildrejected');
   
            }
            else{
                   $message = "Congratulations ".$employee->full_name.". Your Upload ".$request['build_text']." is approved.";
                  
                      
                    $api->sendpush($builds_data -> employee_id,$title_app,$message,$data,'buildApprove');
                }
   
        }
       
        else {
            
            $challenge_data = Challenge::find($builds_data['challenge_id']);
            $title_app = " Submission Approved with Challenge";
            $title_reject = " Submission Rejected with Challenge";

            if($request['status'] == 0 ){
             
                    $message = "Sorry ".$employee->full_name.". Your Submission ".$request['build_text']." with challenge ". $challenge_data->challenge_text." is rejected.";

                        
                 $api->sendpush($builds_data -> employee_id,$title_reject,$message,$data,'buildrejectedwithChallange');
   
            }

            else{
                   $message = "Congratulations ".$employee->full_name.". Your Submission ".$request['build_text']." is approved.";
                      
                    $api->sendpush($builds_data -> employee_id,$title_app,$message,$data,'buildApproveWithChallenge');

                }


        }
        
       
                
        return redirect()->route('leader.builds.list')->with('success','Build updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Builds  $builds
     * @return \Illuminate\Http\Response
     */
    public function destroy(Builds $builds,$id)
    {
        if(Auth::user()->role == 'admin'){
            $builds = Builds::find($id);
        } else {
            $builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
        }
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
            return redirect()->route('leader.builds.list')->with('error','No builds Found.');
        }

        // $challenge = Challenge::where('build_id', $id)->get();
        // if($challenge && $challenge != ''){
        //     foreach ($challenge as $item) {
        //         if($item->image != ''){
        //             $filename = public_path().'/images/challenge/'.$item->image;
        //             if(File::exists($filename)){                    
        //                 File::delete($filename); 
        //             }
        //         }
        //         $item->delete();
        //     }
        // }
        return redirect()->route('leader.builds.list')->with('success','Builds Deleted successfully.');
    }

    public function builddelete(){
         
         //echo json_encode(array('status'=>true));die;
         
         //echo json_encode(array('status'=>true));die;
        if(!empty($_POST['ids'])){

            foreach($_POST['ids'] as $id){

               $builds = Builds::find($id);
                if($builds && $builds != ''){
		     $data = array('is_request'=>'1');
                     $builds->update($data);
                } 
                else {
                    continue;
                }
            }
          
          $build_data = array('ids'=>$_POST['ids']); 
          $request_data = array(
	         'request_type'=>'multi',
	         'status'=>'0',
	         'requested_id'=>0,
	         'from_table'=>'build',
	         'data'=>json_encode($build_data),
	         'employee_id'=>Session::get('employee')->id
          );
          Requests::create($request_data);   
          echo json_encode(array('status'=>true));die;
        }
          
    }

    public function delete($id)
    {
        // if(Auth::user()->role == 'admin'){
            $builds = Builds::find($id);
        // } else {
        //     $builds = Builds::where('id', $id)->where('company_id',Auth::user()->id)->first();
        // }
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
        // $challenge = Challenge::where('build_id', $id)->get();
        // if($challenge && $challenge != ''){
        //     foreach ($challenge as $item) {
        //         if($item->image != ''){
        //             $filename = public_path().'/images/challenge/'.$item->image;
        //             if(File::exists($filename)){                    
        //                 File::delete($filename); 
        //             }
        //         }
        //         $item->delete();
        //     }
        // }
        return response()->json(['status'=>true,'message'=>'Builds deleted successfully.']);
    }

    //Get categorty list from employee company
    public function getCategoryFromEmployee($id){

        if($id && $id > 0){
            // return response()->json(['status'=>false,'message'=>'Selected employee not found.']);
            // if(Auth::user()->role == 'admin'){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
            // } else{
            //     $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
            // }
            if(!$employee){
                return response()->json(['status'=>false,'message'=>'Selected employee not found.']);
            }

            $categories = Categories::select('id','category_name')->where('company_id',$employee->company_id)->get()->toArray();
            $category_html = '<option value="">No category available</option>';
            if($categories){
                $category_html = '<option value="">select category</option>';
                foreach ($categories as $item) {
                    $category_html .= '<option value="'.$item['id'].'">'.$item['category_name'].'</option>';
                }
            }
            return response()->json(['status'=>true,'category_html'=>$category_html]);
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }

    //Get Challenge list from employee and category
    public function getChallengeFromEmployeeAndCategory($build_id, $emp_id, $cat_id){
        if($emp_id && $emp_id > 0 && $cat_id && $cat_id > 0){
            $builds = Builds::find($build_id);
            // if(Auth::user()->role == 'admin'){
                $employee = Employee::where('id',$emp_id)->where('is_deleted','0')->first();
            // } else{
                // $employee = Employee::where('id',$emp_id)->where('is_deleted','0')->where('company_id',Auth::user()->id)->first();
            // }
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

    //change challenge status
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

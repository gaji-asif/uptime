<?php

namespace App\Http\Controllers\executive;

use App\Users;
use File;
use Auth;
use Session;
use App\Accesslevel;
use App\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Employee;
use App\Notification;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\API\ApiController;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {     
        return view('executive.users.index'); 
    }

    public function userdatatable()
    {
        $user = Users::all();
        $status = '';
     
            foreach ($user as $item) {
                $uid = $item->id;
                $item->status = "<a class='btn action-btn btn-outline-info' href='".url('executive/users/'.$uid)."'><i class='fa fa-eye'></i></a>
                <a class='btn action-btn btn-outline-primary' href='".url('executive/users/'.$uid.'/edit')."'><i class='fa fa-pencil'></i></a>
                <a class='btn action-btn btn-outline-danger trash-button' href='".url('executive/users/delete/'.$uid)."' data-att-name='user'><i class='fa fa-trash'></i></a>";
                if($uid == Auth::guard('admin')->user()->id){
                    $item->status = "<a class='btn action-btn btn-outline-info' href='".url('executive/users/'.$uid)."'><i class='fa fa-eye'></i></a>
                    <a class='btn action-btn btn-outline-primary' href='".url('executive/users/'.$uid.'/edit')."'><i class='fa fa-pencil'></i></a>";
                }
            }
       

        return Datatables::of($user)->rawColumns(['status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        return view('executive.users.create');
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
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users|max:255',
                'password'=> 'required',
                'role' => 'required',
                'address' => 'required',
                'pic' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $request['name'] = $request['first_name'].' '.$request['last_name'];
    
        
        $request['password'] = Hash::make($request['password']);
        $data = $request->all();
        if($request->hasFile('pic')){
            $imageName = time().'.'.$request->pic->getClientOriginalExtension();
            $data['pic'] = $imageName;
        }
        
        $user = Users::create($data);
        if($user->id && $request->hasFile('pic')){
            $path = 'images/user/';
            $file = $request->file("pic");
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->pic->move(public_path('images/user'), $imageName);
        }
        return redirect()->route('executive.users.list')->with('success','User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if(!isset($id) || $id =='' || $id == 0){
            return redirect()->route('executive.users.list');
        } else {
            $users = Users::find($id);
            if(!$users){
                return redirect()->route('executive.users.list')->with('errors','No company Found.');
            }
            if($users->website_url == ''){
                $users->website_url = '--';
            }
            return view('executive.users.show',compact('users'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users,$id)
    {
        
        $users = Users::find($id);
        if($users == ''){
            return redirect()->route('executive.users.list')->with('errors','No company Found');
        }
        return view('executive.users.edit',compact('users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $users = Users::find($id);
        if($users == ''){
            return redirect()->route('executive.users.list')->with('errors','No company Found');
        }
       
             
                request()->validate([
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'role' => 'required',
                    'address' => 'required',
                    'pic' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $request['name'] = $request['first_name'].' '.$request['last_name'];
            
        $data = $request->all();
        unset($data['email']);
        if($request->hasFile('pic') || (isset($request->delete_image) && $request->delete_image == 1)){
            $get_user = Users::find($id);
            if($get_user->pic != ''){
                $filename = 'images/user/'.$get_user->pic;
                Storage::disk("s3")->delete($filename);
                // if(File::exists($filename)){                    
                //     File::delete($filename); 
                // }
                
            }
            if($request->hasFile('pic')){
                $imageName = time().'.'.$request->pic->getClientOriginalExtension();
                $data['pic'] = $imageName;
            }
            if(isset($request->delete_image) && $request->delete_image == 1){
                $data['pic'] = '';
            }
        }
        $user = Users::find($id)->update($data);
        if($request->hasFile('pic')){
            $path = 'images/user/';
            $file = $request->file("pic");
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->pic->move(public_path('images/user'), $imageName);
        }
       // if(Auth::guard('admin')->user()->role == 'company' && Auth::user()->id == $id){
       
            return redirect()->route('executive.users.list')->with('success','User updated successfully.');
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $users,$id)
    {
         
        $user = Users::find($id);
        if($user == ''){
            return redirect()->route('user.index')->with('error','No user Found.');
        }
        if($user->pic != ''){
            $filename = 'images/user/'.$user->pic;
            Storage::disk("s3")->put($filename);
            // if(File::exists($filename)){                    
            //     File::delete($filename); 
            // }
        }
        $user->delete();
        return redirect()->route('users.index')->with('success','User Deleted successfully.');
    }

    public function delete($id)
    {
         
        $user = Users::find($id);
        if($user == ''){
            return response()->json(['status'=>false,'message'=>'No user Found.']);
        }
        if($user->pic != ''){
            $filename = 'images/user/'.$user->pic;
            Storage::disk("s3")->put($filename);
            // if(File::exists($filename)){                    
            //     File::delete($filename); 
            // }
        }
        $user->delete();
        return response()->json(['status'=>true,'message'=>'User Deleted successfully.']);
        // return redirect()->route('users.index')->with('success','User Deleted successfully.');
    }

    public function changePassword(Request $request)
    {
        // new_password retype_password
        $data = $request->all();
        if($data){
            $msg = '';
            if(!isset($data['old_password'])){
                $msg = "<li>The current password mest be requied.</li>";
            } else if($data['old_password'] == ''){
                $msg = "<li>The current password cannot be null.</li>";
            }
            if(!isset($data['new_password'])){
                $msg .= "<li>The new password mest be requied.</li>";
            } else if($data['new_password'] == ''){
                $msg .= "<li>The new password cannot be null.</li>";
            }
            if(!isset($data['retype_password'])){
                $msg .= "<li>The new password mest be requied.</li>";
            } else if($data['retype_password'] == ''){
                $msg .= "<li>The new password cannot be null.</li>";
            } else if(isset($data['new_password']) && $data['new_password'] != '' && $data['new_password'] != $data['retype_password']){
                $msg .= "<li>The new password and retype password must be same.</li>";
            }
            if($msg == ''){
                $user = Users::find(Auth::user()->id);
                if($user && $user != ''){
                    if(!Hash::check($data['old_password'], $user->password)){
                        return response()->json(['status'=>false,'message'=>"The current password is not correct."]);
                    } else {
                        $update_data = array('password' => Hash::make($request['retype_password']));
                        Users::find($user->id)->update($update_data);
                        Auth::user()->password = Hash::make($request['retype_password']);
                        return response()->json(['status'=>true,'message'=>'Password changed.']);
                    }
                } else {
                    return response()->json(['status'=>false,'message'=>"You are not able to change your password."]);
                }
            } else {
                return response()->json(['status'=>false,'message'=>$msg]);
            }
        } else {
            return response()->json(['status'=>false,'message'=>"The given data was invalid."]);
        }
    }

    // Push notification
    public function pushNotification(){
        
       
        return view('executive.notification');
    }

// add part function 

    public function getNotificationEmployeeList($region,$level){

       
        
        if(isset($region) && isset($level))
        {

            $data = Employee::select('id','full_name')
               ->where('industry',$region)
               ->where('company_id',Session::get('employee')->company_id)
               ->where('access_level',$level)->where('is_deleted','0')
               ->orderBy('full_name', 'asc')
               ->get()->toArray();
                   
            if(!empty($data)){

                       $html= "<strong><label for='company'>Employee *</label></strong><dl class='dropdown dropdown-list form-control'>        
                        <dt><a href='#'><span class='multiSel'>Select Employee</span>   
                           </a></dt>     
                        <dd><div class='mutliSelect'><ul>";
                        $html .= '<span><input type="checkbox" id ="all">&nbsp;&nbsp;&nbsp;Select All</span>';
                       foreach ($data as $item) {
                          $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['full_name'].'</li>';
                    } 

                    $html .= "</ul></div><dd></dl>";
                    
                    return response()->json(['status'=>true,'html'=>$html]);

            }
            
             else {
                return response()->json(['status'=>false,'message'=>"No Employee."]);
            }
        }  

        else {
               return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
            }
 
    }

    public function getNotificationRoleList($key){
       

      
        if($key){
            //company
            if($key ==1){
                $data = Users::where('id',Session::get('employee')->company_id)->get()->toArray();
            } 
            //region
            if($key == 2){
                $data = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
            }
            //level
            if($key == 3){
                 $data = Accesslevel::where('id','<=',Session::get('employee')->access_level-1)->get()->toArray();
            }
            //employee 
            if ($key == 4) {
                 $data = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
            }

           if(!empty($data)){
                
               //in company case 
                if($key == 1){
                    $html = '<strong><label for="company">Company *</label></strong><select class="form-control company-level" name="company" id="company" required><option value="">Select Company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                } 
               //in region case
                 if($key == 2){

                    $html= "<strong><label for='region'>Store *</label></strong><dl class='dropdown dropdown-list form-control region'>        
                        <dt><a href='#'><span class='multiSel'>Select Store</span>   
                           </a></dt>     
                        <dd><div class='region-mutliSelect'><ul>";
                        $html .= '<span><input type="checkbox" id ="region-all">&nbsp;&nbsp;&nbsp;Select All</span>';
                       foreach ($data as $item) {
                          $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['industry_name'].'</li>';
                    } 

                    $html .= "</ul></div><dd></dl>";
                }
                //in level case 
                if($key == 3){
                     $html= "<strong><label for='level'>Access Level *</label></strong><dl class='dropdown dropdown-list form-control region'>        
                        <dt><a href='#'><span class='multiSel'>Select Accesslevel</span>   
                           </a></dt>     
                        <dd><div class='level-mutliSelect'><ul>";
                        $html .= '<span><input type="checkbox" id ="level-all">&nbsp;&nbsp;&nbsp;Select All</span>';
                       foreach ($data as $item) {
                          $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['access_level_name'].'</li>';
                    } 

                    $html .= "</ul></div><dd></dl>";
                } 
                //in employee case 
                if($key == 4){
                    $html = '<strong><label for="employee-region">Store *</label></strong>
                    <select class="form-control employee-region" name="employee-region" id="employee-region" required>
                    <option value="">Select Store</option>';

                     foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['industry_name'].'</option>';
                    }
                    $html .= '</select>';
                } 
                
                
                return response()->json(['status'=>true,'html'=>$html]);

            }
             else {
                return response()->json(['status'=>false,'message'=>"No  Result."]);
            }
        } 
        else {
              return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
        }


    }

   public function getaccessleveluser(){

    
                 $access_level = Accesslevel::where('id','<=',2)->get()->toArray();
    
                   $html = '<strong><label for="employee-level">Access Level *</label></strong>
                    <select class="form-control employee-level" name="employee-level" id="employee-level" required>
                    <option value="">select level</option>';

                     foreach ($access_level as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['access_level_name'].'</option>';
                    }
        
                    $html .= '</select>';
                   return response()->json(['status'=>true,'html'=>$html]);
       
    

}

    public function sendnotification(Request $request){

       
        request()->validate([
            'notification-text' => 'required',
            'notification-for'=> 'required',
        ]);
     
     
        
        if($request['notification-for'] == 1){
            request()->validate([
                'companyinfo' => 'required'
            ]); 
        } 
           
        //region validate 
        if($request['notification-for'] == 2){
            request()->validate([
                'regionlist' => 'required'
            ]);
        }
        //access level
        if($request['notification-for'] == 3){
            request()->validate([
                'levellist' => 'required'
            ]);
             
        }
        //employee validate

        if($request['notification-for'] == 4){
            request()->validate([
                'empcount' => 'required'
            ]);
        } 
        
   

        $api = new ApiController;

        //in case 'company' 
        
        if($request['notification-for'] == 1){
           // $hasCompany = Users::where('id', $request['companyinfo'])->where('role','company')->count();
           
            
            //if($hasCompany == 0){
           //     return redirect('executive/notification')->with('error','Company Not found.');
           // }
            
            $getAllEmployess = Employee::select('id')->where('company_id',$request['companyinfo'])->where('is_deleted','0')->get()->toArray();
            
            if(!empty($getAllEmployess)){
                foreach ($getAllEmployess as $emp) {
                  // echo $emp['id'];
                   $api->sendpush($emp['id'],'Announced to company',$request['notification-text'],[],'newNotification');
                    $list_param = array(
                              'content_type'=>3,
                              'message' => $request['notification-text'],
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$emp['id'],
                              'receiver_type'=> 3
                     );
                     Notification::create($list_param);  
                }
             
                return redirect('executive/notification')->with('success',"Notification send.");
            }

             else {
                return redirect('executive/notification')->with('error',"This company doesn't have an employee.");
            }
        }
        


        // in case 'region'

        if($request['notification-for'] == 2){

           
            $str = $request['regionlist'];
            
            if(!empty($str)){
                $region = explode(',',$str);
                for($i=0;$i<sizeof($region);$i++){
                    $employees = Employee::where('industry',$region[$i])->where('is_deleted','0')->get();

                    foreach($employees as $item ){
                        $api->sendpush($item->id,'Direct Message',$request['notification-text'],[],'newNotification'); 
                         $list_param = array(
                              'content_type'=>3,
                              'message' => $request['notification-text'],
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$item->id,
                              'receiver_type'=> 3
                         );
                         Notification::create($list_param);  
                    }
                                   
                }
                return redirect('executive/notification')->with('success',"Notification send.");
            } else {
                return redirect('executive/notification')->with('error',"Employee not found.");
            }
        }

          // in case 'level'

        if($request['notification-for'] == 3){

           
            $str = $request['levellist'];
            
            if(!empty($str)){
                $level = explode(',',$str);
                for($i=0;$i<sizeof($level);$i++){
                    $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('access_level',$level[$i])->where('is_deleted','0')->get();

                    foreach($employees as $item ){
                       
                       $api->sendpush($item->id,'Direct Message',$request['notification-text'],[],'newNotification'); 
                        $list_param = array(
                              'content_type'=>3,
                              'message' => $request['notification-text'],
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$item->id,
                              'receiver_type'=> 3
                         );
                         Notification::create($list_param);  
                    }
                                        
                }
            
                return redirect('executive/notification')->with('success',"Notification send.");
            } else {
                return redirect('executive/notification')->with('error',"Employee not found.");
            }
        }



        // in case 'employee'

        if($request['notification-for'] == 4){

            //$employee = Employee::where('id',$request['employee'])->where('is_deleted','0')->first();
            $str = $request['empcount'];
            if(!empty($str)){
                $employee = explode(',',$str);
                for($i=0;$i<sizeof($employee);$i++){
                
                  $api->sendpush(intval($employee[$i]),'Direct Message',$request['notification-text'],[],'newNotification');   
                   $list_param = array(
                      'content_type'=>3,
                      'message' => $request['notification-text'],
                      'sender'=>Session::get('employee')->id,
                      'receiver'=>$employee[$i],
                      'receiver_type'=> 3
                   );
                 Notification::create($list_param);  
                }
                
                return redirect('executive/notification')->with('success',"Notification send.");
            } else {
                return redirect('executive/notification')->with('error',"Employee not found.");
            }
        }
        die;
    }

}

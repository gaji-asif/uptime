<?php

namespace App\Http\Controllers\master;

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
use Image;
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
        return view('master.users.index'); 
    }

    public function userdatatable()
    {
       $resultitem = array();
       $result = array();
       
       $user = Users::where('role','company')
       ->orderBy('created_at','desc')->get();
    
       if(!empty($user)){
           $user= $user->toArray();
           foreach ($user as $item) {
                   $resultitem = $item;
                   $resultitem['created_at'] = date('Y-m-d a h:i:s',strtotime($item['created_at']));
	           $uid = $item['id'];
	           $resultitem['status'] = "<a class='btn action-btn btn-outline-info' href='".url('master/users/'.$uid)."'><i class='fa fa-eye'></i></a>
	           <a class='btn action-btn btn-outline-primary' href='".url('master/users/'.$uid.'/edit')."'><i class='fa fa-pencil'></i></a>
	           <a class='btn action-btn btn-outline-danger trash-button' href='".url('master/users/delete/'.$uid)."' data-att-name='user'><i class='fa fa-trash'></i></a>";
           $result[] = $resultitem;
          }
          
       }     
  
        return Datatables::of($result)->rawColumns(['status'])->make(true);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_data['industry'] = Industry::all();
        return view('master.users.create')->with('user_data',$user_data);
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
                'company_name' => 'required',
                'email' => 'required|unique:users|max:255',
                'password'=> 'required',
                'address' => 'required',
                'pic' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
              
            ]);
         $useremail = strtolower($request['email']);
         
        $request['password'] = Hash::make($request['password']);
        $employee_data = array();
        $data = $request->all();
        $data['name'] = $request['company_name'];
        $data['role']  = 'company';
        $data['email'] = $useremail;
         if($request['accesscode']){
             $data['access_code'] =$request['accesscode'];
         }
        $imageName = '';
        if($request->hasFile('pic')){
            $imageName = time().'.'.$request->pic->getClientOriginalExtension();
           
            $path = 'images/user/';
            $file = $request->file("pic");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $employee_data['image'] = $imageName;
            $data['pic'] = $imageName;
        }

        $user = Users::create($data);

///////////employee image 
     
        $employee_data['company_id'] = $user->id;
        $employee_data['full_name'] = $request['company_name'];
        $employee_data['email'] = $useremail;
        $employee_data['password'] = $request['password'];
        $employee_data['industry'] = 0;
        $employee_data['is_deleted'] = '0';
        $employee_data['phone_number'] = "123456";
       
        $employee_data['point_note'] = 0 ;
        $employee_data['access_level'] = 3;
        $employee = Employee::create($employee_data);

        if($employee->id && $request->hasFile('pic')){

          
            $path = 'images/employee/';
            $file = $request->file("pic");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
           
        }

        return redirect()->route('master.users.list')->with('success','User created successfully.');

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
            return redirect()->route('master.users.list');
        } else {
            $users = Users::find($id);
            if(!$users){
                return redirect()->route('master.users.list')->with('errors','No company Found.');
            }
            if($users->website_url == ''){
                $users->website_url = '--';
            }
            return view('master.users.show',compact('users'));
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
            return redirect()->route('master.users.list')->with('errors','No company Found');
        }
        return view('master.users.edit',compact('users'));
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
        $employee = Employee::where('full_name',$users->name)->where('company_id',$users->id)->first();
        if($users == ''){
            return redirect()->route('master.users.list')->with('errors','No company Found');
        }
       
        request()->validate([
                    'website_url' => 'required',
                    'access_code' => 'required',
                    'full_name' => 'required',
                    'address' => 'required',
                    'pic' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);
      
        $data = $request->all();
       // $empdata = $request->all();

        $data['name'] = $request->full_name;
        
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
                $path = 'images/user/';
                $file = $request->file("pic");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $employee_data['image'] = $imageName ;
                $data['pic'] = $imageName;
            }
                
        }
        $user = Users::find($id)->update($data);
       
       if($employee){
               
                   $empdata['full_name'] = $request->full_name;
                //   $empdata['password'] = Hash::make($request->password);
                   if($request->hasFile('pic') || (isset($request->delete_image) && $request->delete_image == 1)){

                        $get_user =   Employee::where('full_name',$users->name)->where('company_id',$users->id)->first();

                    if($get_user->image != ''){
                        $filename = 'images/employee/'.$get_user->image;
                        Storage::disk("s3")->delete($filename);
                        // if(File::exists($filename)){                    
                        //     File::delete($filename); 
                        // }
                        
                    }
                    if($request->hasFile('pic')){

                        $path = 'images/employee/';
                        $file = $request->file("pic");
                        $image = Image::make($file);
                        $image->orientate();
                        Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');                 
                        $employee_data['image'] = $imageName ;
                        $empdata['image'] = $imageName;
                    }
                   
            }

                $emp =Employee::where('full_name',$users->name)->where('company_id',$users->id)->update($empdata);
                
        }
            return redirect()->route('master.users.list')->with('success','User updated successfully.');
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
            Storage::disk("s3")->delete($filename);
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
            Storage::disk("s3")->delete($filename);
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
        return view('master.users.notification');
    }

// add part function 

     public function getNotificationEmployeeList($company,$region,$level){

        if(isset($company) && isset($region) && isset($level))
        {     
            $data = Employee::where('company_id',$company)->where('industry',$region)->where('access_level',$level)->where('is_deleted','0')->get()->toArray();                                         
            
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
                return response()->json(['status'=>false,'message'=>"No Employee found."]);
            }
        } 

        else {
               return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
            }

}

    public function getNotificationRole($key){

       
        if($key){
            
            $data = Users::where('role','company')->get()->toArray();
             
           if(!empty($data)){
                
               //in company case 
                if($key == 1){

                    $html= "<strong><label for='company'>Company *</label></strong><dl class='dropdown dropdown-list form-control'>        
                        <dt><a href='#'><span class='multiSel'>Select Company</span>   
                           </a></dt>     
                        <dd><div class='company-mutliSelect'><ul>";
                        $html .= '<span><input type="checkbox" id ="company-all">&nbsp;&nbsp;&nbsp;Select All</span>';
                       foreach ($data as $item) {
                          $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['name'].'</li>';
                    } 

                    $html .= "</ul></div><dd></dl>";
                
                } 
               
                //in region
                if($key == 2){
                    $html = '<strong><label for="company">Company *</label></strong><select class="form-control company" name="company" id="company" required><option value="">Select Company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                } 
                if($key == 3){
                    $html = '<strong><label for="company">Company *</label></strong><select class="form-control level-company" name="level-company" id="level-company" required><option value="">Select Company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                } 
                if($key == 4){
                    $html = '<strong><label for="employee-company">Company *</label></strong><select class="form-control employee-company" name="employee-company" id="employee-company" required><option value="">Select Company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                } 
                return response()->json(['status'=>true,'html'=>$html]);
            } 
        }
        else {
              return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
        }

    }

 public function getlevelbycompany(){
        $data = Accesslevel::where('id','<=',3)->get()->toArray();
        if(!empty($data)){
              
             $html= "<strong><label for='level'>Access Level *</label></strong><dl class='dropdown dropdown-list form-control region'>        
                        <dt><a href='#'><span class='multiSel'>Select Accesslevel</span>   
                           </a></dt>     
                        <dd><div class='level-mutliSelect'><ul>";
              $html .= '<span><input type="checkbox" id ="level-all">&nbsp;&nbsp;&nbsp;Select All</span>';
            foreach ($data as $item) {
                $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['access_level_name'].'</li>';
            } 

            $html .= "</ul></div><dd></dl>";
         return response()->json(['status'=>true,'html'=>$html]);  
      }
      else{
            return response()->json(['status'=>false,'message'=>'No Store']);
      }

}

  public function getregionbycompany($company){

      $data = Industry::where('company_id',$company)->get()->toArray();
      if(!empty($data)){
              
           $html= "<strong><label for='region'>Store *</label></strong><dl class='dropdown dropdown-list form-control region'>        
                        <dt><a href='#'><span class='multiSel'>Select Store</span>   
                           </a></dt>     
                        <dd><div class='region-mutliSelect'><ul>";
           $html .= '<span><input type="checkbox" id ="region-all">&nbsp;&nbsp;&nbsp;Select All</span>';
            foreach ($data as $item) {
                          $html .= '<li><input type="checkbox" id ="'.$item['id'].'">&nbsp;&nbsp;&nbsp;'.$item['industry_name'].'</li>';
            } 

          $html .= "</ul></div><dd></dl>";
         return response()->json(['status'=>true,'html'=>$html]);  
      }
      else{
            return response()->json(['status'=>false,'message'=>'No Store']);
      }
  }


public function getregion($company){
      $data = Industry::where('company_id',$company)->get()->toArray();
      if(!empty($data)){
        $html = '<strong><label for="employee-region">Store *</label></strong>
                    <select class="form-control employee-region" name="employee-region" id="employee-region" required>
                    <option value="">Select Store</option>';

        foreach ($data as $item) {
            $html .= '<option value="'.$item['id'].'">'.$item['industry_name'].'</option>';
        }
        $html .= '</select>';
        return response()->json(['status'=>true,'html'=>$html]);  
      }
      else{
            return response()->json(['status'=>false,'message'=>'No Store']);

      }
}


 public function getaccessleveluser(){
       
        $access_level = Accesslevel::where('id','<=',3)->get()->toArray();
       
                   $html = '<strong><label for="employee-level">Employee Level *</label></strong>
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
        
        //company  validate
        
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

      /*  else {
            return redirect('master/notification')->with('error','Parameter missing.');
        }*/

        $api = new ApiController;

        //in case 'company' 
        
        if($request['notification-for'] == 1){
            $hasCompany = Users::where('id', $request['companyinfo'])->where('role','company')->count();
           
            
            if($hasCompany == 0){
                return redirect('master/notification')->with('error','Company Not found.');
            }
            
            $getAllEmployess = Employee::select('id')->where('company_id',$request['companyinfo'])->where('is_deleted','0')->get()->toArray();
            
            if(!empty($getAllEmployess)){
                foreach ($getAllEmployess as $emp) {
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
                return redirect('master/notification')->with('success',"Notification send.");
            }

             else {
                return redirect('master/notification')->with('error',"This company doesn't have an employee.");
            }
        }
        


        // in case 'region'

        if($request['notification-for'] == 2){

           
            $str = $request['regionlist'];
            
            if(!empty($str)){
                $region = explode(',',$str);
                for($i=0;$i<sizeof($region);$i++){
                    $employees = Employee::where('company_id',$request['companyinfo'])->where('industry',$region[$i])->where('is_deleted','0')->get();

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
                return redirect('master/notification')->with('success',"Notification send.");
            } else {
                return redirect('master/notification')->with('error',"Employee not found.");
            }
        }

          // in case 'level'

        if($request['notification-for'] == 3){

           
            $str = $request['levellist'];
            
            if(!empty($str)){
                $level = explode(',',$str);
                for($i=0;$i<sizeof($level);$i++){
                    $employees = Employee::where('company_id',$request['companyinfo'])->where('access_level',$level[$i])->where('is_deleted','0')->get();

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
                return redirect('master/notification')->with('success',"Notification send.");
            } else {
                return redirect('master/notification')->with('error',"Employee not found.");
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
                return redirect('master/notification')->with('success',"Notification send.");
            } else {
                return redirect('master/notification')->with('error',"Employee not found.");
            }
        }
        die;
    }

 
}

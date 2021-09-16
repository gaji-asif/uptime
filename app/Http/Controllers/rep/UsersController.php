<?php

namespace App\Http\Controllers\rep;

use App\Users;
use File;
use Auth;

use App\Accesslevel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Employee;
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
        return view('rep.users.index'); 
    }

    public function userdatatable()
    {
        $user = Users::all();
        $status = '';
     
            foreach ($user as $item) {
                $uid = $item->id;
                $item->status = "<a class='btn action-btn btn-outline-info' href='".url('rep/users/'.$uid)."'><i class='fa fa-eye'></i></a>
                <a class='btn action-btn btn-outline-primary' href='".url('rep/users/'.$uid.'/edit')."'><i class='fa fa-pencil'></i></a>
                <a class='btn action-btn btn-outline-danger trash-button' href='".url('rep/users/delete/'.$uid)."' data-att-name='user'><i class='fa fa-trash'></i></a>";
                if($uid == Auth::guard('admin')->user()->id){
                    $item->status = "<a class='btn action-btn btn-outline-info' href='".url('rep/users/'.$uid)."'><i class='fa fa-eye'></i></a>
                    <a class='btn action-btn btn-outline-primary' href='".url('rep/users/'.$uid.'/edit')."'><i class='fa fa-pencil'></i></a>";
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
      
        return view('rep.users.create');
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
        return redirect()->route('rep.users.list')->with('success','User created successfully.');
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
            return redirect()->route('rep.users.list');
        } else {
            $users = Users::find($id);
            if(!$users){
                return redirect()->route('rep.users.list')->with('errors','No company Found.');
            }
            if($users->website_url == ''){
                $users->website_url = '--';
            }
            return view('rep.users.show',compact('users'));
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
            return redirect()->route('rep.users.list')->with('errors','No company Found');
        }
        return view('rep.users.edit',compact('users'));
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
            return redirect()->route('rep.users.list')->with('errors','No company Found');
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
                Storage::disk("s3")->delete($path . $imageName, file_get_contents($file), "public");
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
       
            return redirect()->route('rep.users.list')->with('success','User updated successfully.');
      
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
        
       
        return view('rep.users.notification');
    }

// add part function 

    public function getNotificationEmployeeList($key){

       

        if($key && ($key == '1' || $key == '2' ||  $key == '3' ||  $key == '4'))
        {
            
            //$data = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
            if($key == '1'){

               $data = Employee::select('id','full_name')
               ->where('access_level','1')->where('is_deleted','0')->orderBy('full_name', 'asc')
               ->get()->toArray();
                
            } 
            else if($key == '2'){
               $data = Employee::select('id','full_name')
               ->where('access_level','1')->where('is_deleted','0')
               ->orwhere('access_level','2')->where('is_deleted','0')->orderBy('full_name', 'asc')
               ->get()->toArray();
            }
            if($key == '3'){
               $data = Employee::select('id','full_name')
               ->where('access_level','1')->where('is_deleted','0')
               ->orwhere('access_level','2')->where('is_deleted','0')
               ->orwhere('access_level','3')->where('is_deleted','0')->orderBy('full_name', 'asc')
               ->get()->toArray();
            } 
            else if($key == '4'){
               $data = Employee::select('id','full_name')
               ->where('access_level','1')->where('is_deleted','0')
               ->orwhere('access_level','2')->where('is_deleted','0')
               ->orwhere('access_level','3')->where('is_deleted','0')
               ->orwhere('access_level','4')->where('is_deleted','0')->orderBy('full_name', 'asc')
               ->get()->toArray();
            }
            
            if(!empty($data)){
                    $html = '<strong><label for="company">Select Employee *</label></strong><select class="form-control" name="employee" id="employee" required><option value="">select Employee</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['full_name'].'</option>';
                    }
                    $html .= '</select>';
                   
                    return response()->json(['status'=>true,'html'=>$html]);

            }

             else {
                return response()->json(['status'=>false,'message'=>"No " . $key . " found."]);
            }
        } 

        else {
               return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
            }

    }

    public function getNotificationUserList($key){

       
        if($key && ($key == 'company' || $key == 'employee')){
            
            if($key == 'company'){
                $data = Users::where('role', 'company')->get()->toArray();
            } 
            else if($key == 'employee'){
                
                //add part

                $data = Accesslevel::where('id','<=',1)->get()->toArray();

//Accesslevel::all()->toArray();


               // $data = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
            }

           if(!empty($data)){
                
                if($key == 'company'){
                    $html = '<strong><label for="company">Select Company *</label></strong><select class="form-control company-level" name="company" id="company" required><option value="">select Category</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                } 

                else if($key == 'employee'){

                    $html = '<strong><label for="employee">Employee Level *</label></strong>
                    <select class="form-control level-change-notification-role" name="level-notification-for" id="level-role" required>
                    <option value="">select level</option>';

                     foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['access_level_name'].'</option>';
                    }
                }
                $html .= '</select>';


                return response()->json(['status'=>true,'html'=>$html]);

            } else {
                return response()->json(['status'=>false,'message'=>"No " . $key . " found."]);
            }
        } 
        else {
              return response()->json(['status'=>false,'message'=>"Invalid parameter."]);
        }

    }

    public function sendnotification(Request $request){

      
        request()->validate([
            'notification-text' => 'required',
            'notification-for'=> 'required',
        ]);
        
        //company  validate
        
        if($request['notification-for'] == 'company'){
            request()->validate([
                'company' => 'required'
            ]);
        } 
        
        //employee validate

        else if($request['notification-for'] == 'employee'){
            request()->validate([
                'employee' => 'required'
            ]);
        } 

        else {
            return redirect('rep/users/push-notification')->with('error','Parameter missing.');
        }

        $api = new ApiController;

        //in case 'company' 
        
        if($request['notification-for'] == 'company'){
            $hasCompany = Users::where('id', $request['company'])->where('role','company')->count();
            
            if($hasCompany == 0){
                return redirect('rep/push-notification')->with('error','Company Not found.');
            }
            
            $getAllEmployess = Employee::select('id')->where('company_id',$request['company'])->where('is_deleted','0')->get()->toArray();
            
            if(!empty($getAllEmployess)){
                foreach ($getAllEmployess as $emp) {
                    $api->sendpush($emp['id'],'Announced to company',$request['notification-text'],[],'newNotification');
                }
                return redirect('rep/users/push-notification')->with('success',"Notification send.");
            }

             else {
                return redirect('rep/users/push-notification')->with('error',"This company doesn't have an employee.");
            }
        }

        // in case 'employee'

         else if($request['notification-for'] == 'employee'){

            $employee = Employee::where('id',$request['employee'])->where('is_deleted','0')->first();
           
            if(!empty($employee)){

                $api->sendpush($employee->id,'Direct Message',$request['notification-text'],[],'newNotification');                  
                return redirect('rep/users/push-notification')->with('success',"Notification send.");
            } else {
                return redirect('rep/users/push-notification')->with('error',"Employee not found.");
            }
        }
        die;
    }

}

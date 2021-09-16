<?php

namespace App\Http\Controllers\executive;
use App\Employee;
use App\Requests;
use Session;
use App\Industry;
use App\Accesslevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class EmployeeRequest extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    
        return view('executive.employee_request.index');
    }

    public function employeerequestdatatable(){
     
     $employeerequests = Requests::where('from_table','employee')->where('status','0')->get();  
     
     foreach($employeerequests as $item){
         $employee = Employee::find($item->employee_id);
         $item->employee_name = $employee->full_name;
            
         if($item->request_type == 'edit'){
                    $item->request_type = "<label class='badge badge-warning'>Edit</label>";
         } else if($item->request_type == 'create'){
                    $item->request_type = "<label class='badge badge-info'>Create</label>";
        } else if($item->request_type == 'delete'){
                    $item->request_type = "<label class='badge badge-danger'>Delete</label>";
        }
                                          
     }
     
     return Datatables::of($employeerequests)->rawColumns(['request_type'])->make(true);
     
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $requests = Requests::find($id);
        $getdata = json_decode($requests->data);
        if($requests->request_type == 'create'){
            $level = Accesslevel::where('id',$getdata->access_level)->first();      
            $industry = Industry::where('id',$getdata->industry)->first();   
            $getdata->access_name = $level->access_level_name;
            $getdata->industry_name = $industry->industry_name; 
       
            return view('executive.employee_request.createshow')->with('getdata',$getdata);   
            
       }
       
        elseif($requests->request_type == 'edit'){
           $employee = Employee::where('id',$requests->requested_id)->first();
           $level = Accesslevel::where('id',$getdata->access_level)->first();      
           $industry = Industry::where('id',$employee->industry)->first();  
           
           $employee->industry_name = $industry->industry_name;
           $employee->access_name = $level->access_level_name;
           return view('executive.employee_request.editshow')->with('employee',$employee);
        }
        elseif($requests->request_type == 'delete'){
           $employee = Employee::where('id',$requests->requested_id)->first();
           $level = Accesslevel::where('id',$employee->access_level)->first();      
           $industry = Industry::where('id',$employee->industry)->first();  
           
           $employee->industry_name = $industry->industry_name;
           $employee->access_name = $level->access_level_name;
           return view('executive.employee_request.deleteshow')->with('employee',$employee);
           
        }
        
    }
 
 
   public function approve($id)
    {
        
        $request = Requests::find($id);
        $getdata = json_decode($request->data);      
        if($request->request_type == 'create'){
           $data = array(
            'full_name'=>$getdata->full_name,
            'email'=>$getdata->email,
            'phone_number'=>$getdata->phone_number,
            'company_id'=>$getdata->company_id,
            'industry' =>$getdata->industry,
            'access_level'=>$getdata->access_level,
            'password'=>$getdata->password,
            'is_deleted'=>'0',
            'is_request'=>'0',
            'image'=>$getdata->image            
           );
           Employee::create($data);
            $request_data = array(
             'status'=>'1'
           );
           $request->update($request_data);
           return redirect()->route('executive.employee.list');
          
        }
        elseif($request->request_type == 'edit'){
            $data = array(
              'is_request'=>'0',
              'access_level'=>$getdata->access_level
             );
            
            $request_data = array(
              'status'=>'1'
            );
            
            $employee = Employee::find($request->requested_id);
            $employee->update($data);
            $request->update($request_data);
            return redirect()->route('executive.employee.list');
        }
        elseif($request->request_type == 'delete'){
              $data = array(
              'is_request'=>'0',
              'is_deleted'=>'1'
             );
            
            $request_data = array(
              'status'=>'1'
            );
            
            $employee = Employee::find($request->requested_id);
            $employee->update($data);
            $request->update($request_data);
            return redirect()->route('executive.employee.list');
 
        }
                
    }
    
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    
    public function delete($id)
    {
        $request = Requests::find($id);
        
        $data = array(
        'is_request'=>'0'
        );
        $request_data = array(
         'status'=>'1'
        );
        if($request->request_type == 'create'){
             $request->update($request_data);    
             return response()->json(['status'=>true,'message'=>'Employee Request Rejected.']);
        }
        elseif($request->request_type == 'edit'){
            $employee = Employee::find($request->requested_id);
            $employee->update($data);
            $request->update($request_data); 
            return response()->json(['status'=>true,'message'=>'Employee Request Rejected.']);
           
        }
        elseif($request->request_type == 'delete'){
            $employee = Employee::find($request->requested_id);
            $employee->update($data);
            $request->update($request_data); 
            return response()->json(['status'=>true,'message'=>'Employee Request Rejected.']);
        }
                
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

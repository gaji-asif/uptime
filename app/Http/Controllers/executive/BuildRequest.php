<?php

namespace App\Http\Controllers\executive;
use App\Requests;
use App\Builds;
use App\Employee;
use Session;
use File;
use App\Industry;
use App\Accesslevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class BuildRequest extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    
     
         return view('executive.build_request.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function buildrequestdatatable(){
    
     $buildrequest= Requests::where('from_table','build')->where('status','0')->get();       
     foreach($buildrequest as $item){
         
         $employee = Employee::find($item->employee_id);
         $item->employee_name = $employee->full_name;
               
         if($item->request_type == 'edit'){
                    $item->request_type = "<label class='badge badge-warning'>Edit</label>";
         } else if($item->request_type == 'create'){
                    $item->request_type = "<label class='badge badge-info'>Create</label>";
        } else if($item->request_type == 'delete'){
                    $item->request_type = "<label class='badge badge-danger'>Delete</label>";
        }else if($item->request_type == 'multi'){
                    $item->request_type = "<label class='badge badge-success'>MultiDelete</label>";
        }
        $data = json_decode($item->data);
        $item->data = $data->ids;
                                      
     }
 
     
     return Datatables::of($buildrequest)->rawColumns(['request_type'])->make(true);
    }
    
    
    public function approve($id)
    {
       $buildreqeust = Requests::find($id);
       $req_data = json_decode($buildreqeust->data);
       $ids = $req_data->ids;
       
       foreach($ids as $id){

               $builds = Builds::find($id);
                if($builds && $builds != ''){


                    if($builds->image != ''){
                        $filename = public_path().'/images/build/'.$builds->image;
                        if(File::exists($filename)){                    
                            File::delete($filename); 
                        }
                    }
                    
                    $builds->delete();
                } 
                else {
                    continue;
                }
       }

       $data = array('status'=>'1');
       $buildreqeust->update($data);       
       return redirect()->route('executive.builds.list');             
    }
    
    public function reject($id)
    {
           
       $buildrequest = Requests::find($id);
       $getdata = json_decode($buildrequest->data);
      
       $ids = $getdata->ids;
         
       foreach($ids as $id){

               $builds = Builds::find($id);
                if($builds && $builds != ''){
			
                   $build_data = array(
                    'is_request'=>'0'
                   );
                   $builds->update($build_data);
                } 
                else {
                    continue;
                }
       }

       $data = array('status'=>'1');
       $buildrequest->update($data);
       
      return response()->json(['status'=>true,'message'=>'Builds Request Rejected.']);
 
    }
    
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
        //
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

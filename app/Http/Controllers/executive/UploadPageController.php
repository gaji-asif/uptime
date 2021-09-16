<?php

namespace App\Http\Controllers\executive;

use App\Upload;
use App\ReadItem;
use App\Accesslevel;
use App\Industry;
use Auth;
use App\Employee;
use App\Notification;
use Session;
use App\Users;
use DB;
use File;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Image;

class UploadPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
   
      return view('executive.upload.index');
    }
    
  //get the count of employee that have seen photo 
  
 public function get_employeecount_fromregionlevel($employee_ids,$region_ids, $level){
       
       $total_emp = array();
       $empcount = 0 ; 
       foreach($region_ids as $region){           
          $employee = Employee::select('id')->where('company_id',Session::get('employee')->company_id)->where('industry',intval($region))->where('access_level','<=',$level)->where('is_deleted','0')->get();
          foreach($employee_ids as $item){
              foreach($employee as $emp){
                if($item == $emp->id) $empcount++;
              }     
          }
       }                  
       return $empcount;  
   }
   
   
   
    public function uploaddatatable(){
 
    $totalemployee = 0;
    $totalcount  = 0 ;
    
    $result = array();
    $resultitem = array();
    $emp_arr = array();
    
    $uploads = Upload::where('company_id',Session::get('employee')->company_id)->orderBy('created_at','desc')->get();
    if(!empty($uploads)){    
       foreach ($uploads as $item) {
        
	      $region_str = $item['sendto_region'];
	      $level = $item['sendto_level'];	             
	      $region_ids = explode(",", $region_str);		            
	     $totalemployee = Employee::where('company_id',Session::get('employee')->company_id)
	     		     ->whereIn('industry',$region_ids)->where('is_deleted','0')->get()->count();	     
	     
            //get employee list 
            $emp_data = ReadItem::where('readitem_id',$item['id'])->get();
                     
            if($emp_data){                 
                   $emp_data = $emp_data->toArray();                   	            
	            foreach($emp_data as $em){	              
	               $emp_arr[] = $em['employee_id']; 
	            }
	            $emp_arr_unique = array_unique($emp_arr);
	            $template_emp = array();
	            foreach($emp_arr_unique as $key=>$value){
	               $template_emp[]= $value;
	            }	            	                        
	            $region_str = $item['sendto_region'];
	            $level = $item['sendto_level'];	             
	            $region_ids = explode(",", $region_str);	            	            	             
	            $totalcount = $this->get_employeecount_fromregionlevel($template_emp,$region_ids,$level);	 
	                  	            
	        }
	    
            $resultitem = $item;            
            $url_link = $item['url_link'];
            if(strlen($item['url_link']) >= 100)
                  $item['url_link'] = substr($item['url_link'],0,100).'...';
                  
            $search_str = strstr($url_link,"https://");
            if($search_str == '') $url_link = "https://".$url_link;
            $resultitem['url_link'] = "<a href = '".$url_link."'target='_blank' >".$item['url_link']."</a>";                                 
            $resultitem['count'] = $totalcount.'/'.$totalemployee;  
            $resultitem['created_date'] = date('Y-m-d a h:i:s',strtotime($item['created_at']));
            $access = Accesslevel::where('id',$item['sendto_level'])->first();
            $str = $item['sendto_region'];
            $str_Arr = explode(",", $str);
            $region_str = '';
            $i = 0 ;
            if(!empty($str_Arr)){
            
               foreach($str_Arr as $item1){
                
                 $industry = Industry::where('id',$item1)->first();
                 
                 if(!empty($industry)){                
                      $region_str .= $industry->industry_name;
                      $region_str .='  ';                  
                   }
        
                 else{
                    $region_str .= '--';
                    $region_str .='  ';
                 }
                 
                 if($i == 2){
                        $region_str .= '...';
                        break;
                 }
                  $i++;
               }
            }
            $resultitem['access_level'] = $access->access_level_name;
            
            $resultitem['region'] = $region_str;
            $result[] = $resultitem;       
            $emp_arr = array();         
   	    $totalcount  = 0 ;
        }
    }
  
      return Datatables::of($result)->rawColumns(['url_link'])->make(true);

   }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function create()
    {   	
        $upload_data['access_level'] = Accesslevel::where('id','<=',2)->get();
        $upload_data['region'] = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
        return view('executive.upload.create')->with('upload_data',$upload_data);
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
            'description' => 'required',
            'send_region' => 'required',
             'send_level' => 'required', 
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
         
       
        $data = Array (           
            'description' => $request['description'],
            'sendto_level' => $request['send_level'],
            'sendto_region' => $request['send_region'],
            'company_id' =>Session::get('employee')->company_id
        );

	if($request['url_link']){
		$data['url_link'] = $request['url_link'];
	}
        
        if($request->hasFile('image')){

            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $path = 'images/upload/';
            $file = $request->file("image");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $data['image'] = $imageName ;
           
        }
      
        $upload = Upload::create($data);
  
        $receiver = $upload->sendto_region.','.$upload->sendto_level;
        
        $message =  $upload->description.' has been posted';;
	    
	    $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>$receiver,
                  'receiver_type'=>4
        );
        
        Notification::create($list_param);      
        
        $api = new ApiController;     
        $message1 = $upload->description.' has been posted';  
       
        $level = $upload->sendto_level;
        $regions = explode(',',$upload->sendto_region);
        $employees = Employee::where('access_level',$level)->where('company_id',Session::get('employee')->company_id)->whereIn('industry',$regions)->where('is_deleted','0')->get();
        foreach($employees as $emp){
            $api->sendpush($emp->id,'New Announcement',$message1,$data,'createannouncement');
        }
        return redirect('executive/upload')->with('success','Announcement created successfully.');
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    $upload_data['region'] = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
	$upload = Upload::find($id);
	$region_arr = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();
	$region_ids = array();     
	 
	foreach($region_arr as $item){
	    $region_ids[] = $item['id'];    
	}
	
	$photoview_empcount = 0 ;
	$region_empcount = Employee::where('company_id',Session::get('employee')->company_id)->whereIn('industry',$region_ids)->where('is_deleted','0')->get()->count();
        $region_employee_data= Employee::where('company_id',Session::get('employee')->company_id)->whereIn('industry',$region_ids)->where('is_deleted','0')->get();
        $emp_data = ReadItem::where('readitem_id',$id)->get();
        $emp_arr = array();
        if($emp_data){                 
                   $emp_data = $emp_data->toArray();                   	            
	            foreach($emp_data as $em){	              
	               $emp_arr[] = $em['employee_id']; 
	            }
	            $emp_arr_unique = array_unique($emp_arr);
	            $template_emp = array();
	            foreach($emp_arr_unique as $key=>$value){
	               $template_emp[]= $value;
	            }	            	                        
	            
	            $level = 3;	    
	             $photoview_empcount = $this->get_employeecount_fromregionlevel($template_emp,$region_ids,$level);
	            
	 }
	$result = $photoview_empcount.'/'.$region_empcount;
	$upload_data['totalregion_count'] = $result;
	 
        return view('executive.upload.show',compact('upload'))->with('upload_data',$upload_data);
        

    }
  
    
    public function get_VisitCount_Photo($region,$id){
    
       $region_empcount = 0;
       $photoview_empcount = 0 ;
       $phptoview_employee = array();
       $result = '';
       $region_empcount = Employee::where('company_id',Session::get('employee')->company_id)->where('industry',$region)->where('is_deleted','0')->get()->count();
       $region_employee_data= Employee::where('company_id',Session::get('employee')->company_id)->where('industry',$region)->where('is_deleted','0')->get();
       $emp_data = ReadItem::where('readitem_id',$id)->get();
       $emp_arr = array();
       if($emp_data){                 
                   $emp_data = $emp_data->toArray();                   	            
	            foreach($emp_data as $em){	              
	               $emp_arr[] = $em['employee_id']; 
	            }
	            $emp_arr_unique = array_unique($emp_arr);
	            $template_emp = array();
	            foreach($emp_arr_unique as $key=>$value){
	               $template_emp[]= $value;
	            }	            	                        
	            
	            $level = 3;	             
	            $region_ids = array();
	            $region_ids[]= $region;	            	            	             
	            $photoview_empcount = $this->get_employeecount_fromregionlevel($template_emp,$region_ids,$level);	            	            
       }           
       $result = $photoview_empcount.'/'.$region_empcount;    
       $html = '<div class="form-group"><strong><label>PhotoViews Count</label></strong>
                <input type="text" class= "form-control" value="'.$result.'" readonly></div>';
         
        return response()->json(['status'=>true,'html'=>$html]); 
    
    
 }
    
    
    public function deletes(){

        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                
                $upload = Upload::where('id',$id)->count();
               
                if($upload == 0){
                    continue;
                }
                 Upload::find($id)->delete();
               
            }
            echo json_encode(array('status'=>true));die;
        }     

        //Upload::find($id)->delete();
       // return response()->json(['status'=>true,'message'=>'Upload deleted successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $upload_data['access_level'] = Accesslevel::where('id','<=',2)->get();
       $upload_data['region'] = Industry::where('company_id',Session::get('employee')->company_id)->get()->toArray();

        
        $upload = Upload::find($id);
        $str = $upload->sendto_region;
        
        $str_Arr = explode(",", $str);
        $upload_data['region_count'] = sizeof($str_Arr);
        $upload_data['region_arr'] = $str_Arr;

        return view('executive.upload.edit',compact('upload'))->with('upload_data',$upload_data);

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
        $upload = Upload::find($id);
        
        if($upload == ''){
            return redirect()->route('upload.index')->with('errors','No challenge Found.');
        }
       
         request()->validate([
           
            'description' => 'required',
            'send_region' => 'required',
            'send_level' => 'required', 
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        
        
        $data = Array (
           
            'description' => $request['description'],
            'sendto_level' => $request['send_level'],
            'sendto_region' => $request['send_region']
        );

        if($request['url_link']){
               $data['url_link'] = $request['url_link'];
        }
        if($request->hasFile('image')){
            $get_upload = Upload::find($id);

            if($get_upload->image != ''){
                $filename = 'images/upload/'.$get_upload->image;
                Storage::disk("s3")->delete($filename);
                
            }
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/upload/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
            }
            
        }
        $update_data = Upload::find($id)->update($data);
        
        return redirect()->route('upload.index')->with('success','Upload updated successfully.');
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         Upload::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Upload deleted successfully.']);
    }
}

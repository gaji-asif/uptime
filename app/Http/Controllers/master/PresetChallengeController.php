<?php

namespace App\Http\Controllers\master;

use App\PresetChallenge;
use Illuminate\Http\Request;
use App\Builds;
use App\Categories;
use App\Subcategory;
use App\Employee;
use Yajra\Datatables\Datatables;
use App\Notification;
use File;
use Session;
use App\Users;
use App\Accesslevel;
use App\Industry;
use DB;
use Auth;
use App\Http\Controllers\API\ApiController;
use Image;
use Illuminate\Support\Facades\Storage;

class PresetChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
       
        return view('master.preset_challenge.index');
    }

    public function challengedatatable()
    {
       
        $challenge = PresetChallenge::where('preset_type','1')->where('status','-1')->orwhere('status','0')->orwhere('status','1')->orderBy('created_at','desc')->get();
        
        if(!empty($challenge)){
            foreach ($challenge as $item) {
            
              
                 $region = '';
                $level = '';
                if($item->type == "employee"){
                    
                      $emp_region = Industry::where('id',intval($item->sendto_region))->first();
                    if(!empty($emp_region))
                        $region = $emp_region->industry_name;
                    $access = AccessLevel::where('id',intval($item->sendto_level))->first();
                    $level = $access->access_level_name;
                   
                }
                //region
                elseif($item->type == "region"){
                    $emp_region = Industry::where('id',intval($item->sendto_region))->first();
                    if(!empty($emp_region))
                    $region = $emp_region->industry_name;
                    $access = AccessLevel::where('id',intval($item->sendto_level))->first();
                    $level = $access->access_level_name;
                    
                    
                }
                //all
                elseif($item->type == "all"){
                      $regions = explode(',',$item->sendto_region);
                   
                    if(!empty($regions)){
                        foreach($regions as $emp_region){
                            
                            $industry = Industry::find(intval($emp_region));
                            if(!empty($industry)){
                                $region .= $industry->industry_name.',';
                            }
                        }
                        $region =  rtrim($region,",");
                    }
                    
                    //$access = AccessLevel::where('id',2)->first();
                    //$level = $access->access_level_name;
                     $level = "All";
                }
                
                $item->industry = $region;
                $item->access_name = $level;
                 
                 
                 $date = date('Y-m-d a h:i:s',strtotime($item->created_at));
                 $item->created_date = $date;
                if($item->status == '-1'){
                    $item->status = "<label class='badge badge-info'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-warning'>Rejected</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-success'>Approved</label>";
                }
                
                 if($item->is_active == 0){
                        $item->is_active = "<label class='badge badge-danger'>Inactive</label>";
                    }
                else{
                        $item->is_active = "<label class='badge badge-info'>Active</label>";
                }
                
            }
        }
        return Datatables::of($challenge)->rawColumns(['status','is_active'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            
            $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
     
           return view('master.preset_challenge.create')->with('challenge_data', $challenge_data);
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
            'company'    => 'required',
            'sent_to'     => 'required',
            'challenge_text' => 'required',
            'point' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            'subcategory' => 'required'
         ]);
 
        $kind = $request['sent_to'];

        $sent_to = '-1';
        $type = "";
            $sendto_level ='';
         $sendto_region = '';
         
        $region = '';
        
        switch ($kind) {
            case 1:
                $type = "employee";
                  $sent_to = $request['empcount'];
                break;
             case 2:
                $type = "region";
                $sent_to = $region;
                $sendto_level = $request['emp_accesslevel'];
                $sendto_region = $request['emp_industry'];
                break;
             case 3:
                $type = "all";
                $sendto_level = 2;
                $regions = Industry::where('company_id',$request['company'])->get();
                if(!empty($regions)){
                    foreach($regions as $emp_region){
                            $region .= $emp_region->id.',';
                    }
                    $region =  rtrim($region,",");
                    $sendto_region = $region;
                    
                }
                else $sendto_region = '';
                $sent_to = $request['company'];
                break;
            default:
                $type = "";
                $sent_to = '-1';
                break;
        }
        $enddate = date('Y-m-d h:i:s');
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' =>$request['company'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'point' => $request['point'],
            'subcategory_id' => $request['subcategory'],
            'sent_in' => $sent_to,
            'type'  => $type,
            'preset_type' => '1',
             'sendto_level'=>$sendto_level,
            'sendto_region'=>$sendto_region,
            'end_on' => $enddate,
            'employee_id'=>-1
        );

        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $path = 'images/challenge/';
            $file = $request->file("image");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $data['image'] = $imageName ;
        }  
        $challenge = PresetChallenge::create($data);
        
       
        $company = Users::where('id',$challenge->company_id)->first();
        
        $message = $company->name." has created a Preset Challenge,".$challenge->challenge_text;
        if($challenge->type == 'employee'){
          $receiver = $request['empcount'];
          $receiver_type = 3;
        }
        elseif($challenge->type == 'region'){
          $receiver = $sendto_region;
           $receiver_type = 4;
        }
        elseif($challenge->type == 'all'){
          $receiver = $challenge->company_id;
           $receiver_type = 2;
        }
        
         $list_param = array(
                  'content_type'=>4,
                  'message' => $message,
                  'sender'=>$challenge->employee_id,
                  'receiver'=>$receiver,
                  'receiver_type'=> $receiver_type
              );
         Notification::create($list_param);      
          
          
       $api = new ApiController;     
       $message = 'A preset challenge '.$challenge->challenge_text.' has been created';
        
        if($challenge->type == 'employee'){
          $emp_ids = explode(',',$request['empcount']);
          foreach($emp_ids as $id)
           $api->sendpush($id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
        }
        elseif($challenge->type == 'region'){
          $regions = explode(',',$region); 
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->where('industry',$challenge->sendto_region)->where('access_level','>=',$challenge->sendto_level)->get();
          foreach($employees as $emp)
            $api->sendpush($emp->id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
        }
        elseif($challenge->type == 'all'){
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->get();
          foreach($employees as $emp){
            $api->sendpush($emp->id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
          }
        } 
        
        return redirect()->route('master.preset-challenge.list')->with('success',' Preset Challenge created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id && $id > 0){
           
                $challenge = PresetChallenge::find($id);
           
            if($challenge){

                $build = Builds::select('id','build_text')->where('challenge_id', $challenge->id)->first();
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
       
                $category = Categories::select('category_name')->where('id', $challenge->category_id)->first();
                
                if($category && !empty($category) && $category->category_name != ''){
                    $challenge->category_name = $category->category_name;
                }
                
                $user = Users::select('name')->where('id', $challenge->company_id)->first();
                $challenge->company_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $challenge->company_name = $user->name;
                }
 		$challenge->created_date = date('Y-m-d a h:i:s',strtotime($challenge->created_at));
		 $subcategories = $this->getSubCategories($challenge->subcategory_id);
		 $sub_name = '';
		 foreach($subcategories as $item){
		    if($item != '--') $sub_name.= $item->subcategory_name;
		    else $sub_name.= '--';
		    $sub_name.= ",";
		 }
		 
		 $sub_name = rtrim($sub_name,",");
		 
		  $challenge->subcategories = $sub_name;
                return view('master.preset_challenge.show',compact('challenge'));
            } else {
                return redirect()->route('master.preset-challenge.show')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect()->route('master.preset-challenge.list')->with('errors','No challenge Found.');
        }
    }

 public function getSubCategories($str){
        $subcat_str = $str;
        $subcat_arr = explode(",",$subcat_str);
        $sub_data = array();                   
        if($subcat_str!="" && isset($subcat_str)){
             foreach ($subcat_arr as $item1) {
                $subcat_data = Subcategory::where('id',$item1)->get()->first();
                if($subcat_data != '' && !isset($subcat_data))  $sub_data[] = $subcat_data;
                else $sub_data[] = '--';
             }                               
         }                             
        return $sub_data;
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
     
        $challenge = PresetChallenge::find($id);
        if($challenge == ''){
            return redirect()->route('master.preset-challenge.list')->with('errors','No Preset challenge Found.');
        }
      
        $challenge_data = array();
         
        $challenge_data = array();
       
       $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();

       $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($challenge->company_id, '0'))->get()->toArray();
       $challenge_data['subcategory'] = Subcategory::where('category_id',$challenge->category_id)->get()->toArray();
        return view('master.preset_challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $challenge = PresetChallenge::find($id);
      
        if($challenge == ''){
            return redirect()->route('master.preset-challenge.list')->with('errors','No challenge Found.');
        }
        
      
        request()->validate([
            'challenge_text' => 'required',
            'company' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
          
            'subcategory'=>'required'
        ]);

       $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('master.challenge.list')->with('error','Company Not found.');
            }
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
       
        if($hasCategory == 0){
            return redirect()->route('master.preset-challenge.list')->with('error','Category Not found.');
        }
       if($request['sent_to']){
        $kind = $request['sent_to'];
      
        $sent_to = '-1';
        $type = "";
        
        $region = '';
        $sendto_level ='';
        $sendto_region = '';
        switch ($kind) {
            case 1:
                $type = "employee";
                 $sent_to = $request['empcount'];
                 $sendto_level =$request['emp_accesslevel'];
                $sendto_region = $request['emp_industry'];
                break;
             case 2:
                $type = "region";
                $sent_to = $region;
                $sendto_level =$request['emp_accesslevel'];
                $sendto_region = $request['emp_industry'];
                break;
             case 3:
                $type = "all";
                   $sendto_level = 2;
                $regions = Industry::where('company_id',$request['company'])->get();
                if(!empty($regions)){
                    foreach($regions as $emp_region){
                            $region .= $emp_region->id.',';
                    }
                    $region =  rtrim($region,",");
                    $sendto_region = $region;
                    
                }
                else $sendto_region = '';
                   $sent_to = $request['company'];
                break;
            default:
                $type = "";
                $sent_to = $request['company'];
                break;
        }
        
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' => $request['company'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'point' => $request['point'],
            'subcategory_id' =>$request['subcategory'],
            'sent_in' => $sent_to,
            'type' => $type,
            'sendto_level'=>$sendto_level,
            'sendto_region'=>$sendto_region,
            'preset_type' => '1',
            
            'employee_id'=>-1,
            'is_active' =>$request['is_active']
        );
      }
        else{
        
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'company_id'=>$request['company'],
            'point' => $request['point'],
            'subcategory_id' =>$request['subcategory'],
            'access_level' => $request['access_level'],
            'preset_type' => '1',
            'is_active' =>$request['is_active'],
          
        );
   
        }
        if($request->hasFile('image')){
            $get_challenge = Challenge::find($id);
            if($get_challenge->image != ''){
                $filename = 'images/challenge/'.$get_challenge->image;
                Storage::disk("s3")->delete($filename);
                // if(File::exists($filename)){                    
                //     File::delete($filename); 
                // }
            }
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/challenge/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
            }
          
        }

        $api = new ApiController;     
        $currenttop = $api->getTopRank(Session::get('employee')->id);

        PresetChallenge::find($id)->update($data);
        
        $challenge = PresetChallenge::find($id);
        //top leader 
        if($challenge->status != '-1'){
        
            $aftertop = $api->getTopRank(Session::get('employee')->id);
            if($currenttop != $aftertop){
                $api->topleadermsg($aftertop,Session::get('employee')->id);
            } 
        }
        //top leader  end 
         $company = Users::where('id',$challenge->company_id)->first();
        
        $message = Session::get('employee')->full_name." created,".$challenge->challenge_text;
        if($challenge->type == 'employee'){
          $receiver = $challenge->sent_in;
          $receiver_type = 3;
        }
        elseif($challenge->type == 'region'){
           $receiver = $challenge->sendto_region;
           $receiver_type = 4;
        }
        elseif($challenge->type == 'all'){
          $receiver = $challenge->company_id;
           $receiver_type = 2;
        }
        
         $list_param = array(
                  'content_type'=>4,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>$receiver,
                  'receiver_type'=> $receiver_type
              );
         Notification::create($list_param);   
      
       $api = new ApiController;     
          //top leader start
         $currenttop = $api->getTopRank(Session::get('employee')->id);
            
            if($currenttop != Session::get('topemployee')){
                 
                    Session::put('topemployee', $currenttop);  
                    $topemp = Employee::find($currenttop);
                    $message= "Congratulations ".$topemp->full_name.". You are position 1th and now in the top leaderboard for your company";
                    $api->sendpush($currenttop,'Top Leaderboard',$message,[],'Top');
                                
                    $list_param = array(
                          'content_type'=>3,
                          'message' => $message,
                          'sender'=>Session::get('employee')->id,
                          'receiver'=>$currenttop,
                          'receiver_type'=>3
                        );
                    Notification::create($list_param);
                   
            }
        //top leader  end 
        if($challenge->type == 'employee'){
          $emp_ids = explode(',',$challenge->sent_in);
          foreach($emp_ids as $id){
              $emp = Employee::where('id',$id)->where('is_deleted','0')->first();
              if(!empty($emp) && $challenge->status != '0')
              $api->sendpush($id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
          }
           
        }
        elseif($challenge->type == 'region'){
        
          $employees = Employee::where('company_id',$company->id)->where('is_deleted','0')->where('access_level','>=',$challenge->sendto_level)->where('industry',$challenge->sendto_region)->get();
          foreach($employees as $emp)
            if ($challenge->status != '0') {
                $api->sendpush($emp->id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
            }
            
        }
        elseif($challenge->type == 'all'){
          $employees = Employee::where('company_id',$company->id)->where('is_deleted','0')->get();
          foreach($employees as $emp){
              if ($challenge->status != '0') {
                  $api->sendpush($emp->id,'Preset Challenge Created',$message,$data,'presetchallengecreate');
              }
          }
        } 
      
        return redirect()->route('master.preset-challenge.list')->with('success','Preset Challenge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Challenge $challenge,$id)
    {
        if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::find($id);
        } else{
            $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
          
        }
        if($challenge == ''){
            return redirect()->route('preset-challenge.index')->with('error','No challenge Found.');
        }
        if($challenge->image != ''){
            $filename = public_path().'/images/challenge/'.$challenge->image;
            if(File::exists($filename)){                    
                File::delete($filename); 
            }
        }
        PresetChallenge::find($id)->delete();
        return redirect()->route('preset-challenge.index')->with('success','Challenge Deleted successfully.');
    }

    public function challangedelete(){
        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                // if(Auth::user()->role == 'admin'){
                    $challenge = PresetChallenge::find($id);
               
                if($challenge == ''){
                    continue;
                }
                if($challenge->image != ''){
                    $filename = public_path().'/images/challenge/'.$challenge->image;
                    if(File::exists($filename)){                    
                        File::delete($filename); 
                    }
                }
                PresetChallenge::find($id)->delete();
                //return response()->json(['status'=>true,'message'=>'Challenge deleted successfully.']);
            }
            echo json_encode(array('status'=>true));die;
        }  
    }

    public function delete($id)
    {
        // if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::find($id);
        // } else{
        //     $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
        //     // $challenge = DB::table('challenge')
        //     //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
        //     //     ->select('challenge.*')
        //     //     ->where('challenge.id', $id)
        //     //     ->where('builds.company_id', Auth::user()->id)
        //     //     ->first();
        // }
        
        if($challenge == ''){
            return response()->json(['status'=>false,'message'=>'No challenge Found.']);
        }
        if($challenge->image != ''){
            $filename = public_path().'/images/challenge/'.$challenge->image;
            if(File::exists($filename)){                    
                File::delete($filename); 
            }
        }
        PresetChallenge::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Challenge deleted successfully.']);
    }

    public function getCategoryFromCompany($id){
        if($id && $id > 0){
            if(Auth::user()->role == 'admin'){
                $categories = Categories::select('id','category_name')->whereIn('company_id',array($id, '0'))->get()->toArray();
            } else {
                $categories = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
            }
            if($categories){
                $html = '<option value="">select Category</option>';
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

    function getSubcategory(Request $request, $id){

        $subcategory = Subcategory::where('category_id',$id)->get();
        $subcat_string = "";
        if($subcategory->count()){
            foreach($subcategory as $key => $subcat)
            $subcat_string = "<option value=".$subcat->id.">$subcat->subcategory_name</option>";
        }
        return response()->json(['status'=>true,'sub_cat_html'=>$subcat_string]);
    }
}

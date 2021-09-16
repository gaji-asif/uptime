<?php

namespace App\Http\Controllers\executive;

use App\LevelChallenge;
use Illuminate\Http\Request;
use App\Builds;
use App\Categories;
use App\Subcategory;
use App\Employee;
use App\Notification;
use Yajra\Datatables\Datatables;
use File;
use App\Users;
use App\Industry;
use App\Accesslevel;
use DB;
use Session;
use App\Http\Controllers\API\ApiController;
use Image;
use Illuminate\Support\Facades\Storage;
class LevelChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        return view('executive.level_challenge.index');
    }

    public function updatechallengebyexpirydate(){
      $challenge = LevelChallenge::where('preset_type','0')->where('company_id',Session::get('employee')->company_id)->get();

      foreach($challenge as $item){

          $today = date('Y-m-d h:i:s');

          //make inactive
          if($today > $item->end_on){

              $data = array('is_active'=>0);
              LevelChallenge::find($item->id)->update($data);
          }
          else{
              $data = array('is_active'=>1);
              LevelChallenge::find($item->id)->update($data);
          }

      }


    //  $now = date("Y-m-d H:i:s").'';
    //  $expiry_date =  date("Y-m-d H:i:s",strtotime('+12 hours',strtotime($now)));
    //  foreach($challenge as $item){

    //  }

    }

    public function challengedatatable()
    {


         $this->updatechallengebyexpirydate();

         $challenge = LevelChallenge::where('preset_type','0')->where('company_id',Session::get('employee')->company_id)->orderBy('created_at','desc')->get();

        if(!empty($challenge)){
           foreach ($challenge as $item) {

                $item->created_date = date('Y-m-d a h:i:s',strtotime($item->created_at));

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
                    /*
                    $emp_region = Industry::where('id',intval($item->sendto_region))->first();
                     if(!empty($emp_region))
                    $region = $emp_region->industry_name;
                    */
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
                    $level = 'All';
                }

                $item->industry = $region;
                $item->access_level = $level;

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
             return Datatables::of($challenge)->rawColumns(['status','is_active'])->make(true);
        }
        $result = array();
        return Datatables::of($result)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Session::get('employee');
        $challenge_data = array('is_level_3' => 0, 'categories' => []);
        $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($user->company_id, '0'))->get()->toArray();
        $challenge_data['access_level_data'] = Accesslevel::where('id','<=',4)->get()->toArray();
        return view('executive.level_challenge.create')->with('challenge_data', $challenge_data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = Session::get('employee');

        if($user->role == 'company'){
            $request['company'] = $user->id;
        }

        request()->validate([
            'sent_to'     => 'required',
            'challenge_text' => 'required',
            'point' => 'required',
            'category' => 'required',
            'subcategory' => 'required',
            'end_date'=>'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
       if(empty($request['subcategory'])){
           return redirect()->route('executive.level-challenge.create')->with('error','No Subcategory Found.');
       }
        $kind = $request['sent_to'];

        $type = "";
        $region= '';
         $sendto_level ='';
         $sendto_region = '';
        switch ($kind) {
            case 1:
                $type = "employee";
                $sent_to = $request['empcount'];
                $sendto_level = $request['emp_accesslevel'];
                $sendto_region = $request['emp_industry'];
                break;
             case 2:
                $type = "region";
                $sent_to = $region;
                $sendto_level = $request['emp_accesslevel'];
                $sendto_region = $request['emp_industry'];
                break;
             case 3:
                $type = "access_level";
                $sendto_level = $request['emp_accesslevel'];
                break;
             case 4:
               $type = "all";
                $sendto_level = 2;
                $regions = Industry::where('company_id',Session::get('employee')->company_id)->get();
                if(!empty($regions)){
                    foreach($regions as $emp_region){
                            $region .= $emp_region->id.',';
                    }
                    $region =  rtrim($region,",");
                    $sendto_region = $region;

                }
                else $sendto_region = '';

                $sent_to = '-1';
                break;
            default:
                $type = "";
                $sent_to = '-1';
                break;
        }

        $enddate = date('Y-m-d h:i:s',strtotime($request['end_date']));

        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' => Session::get('employee')->company_id,
            'category_id' => $request['category'],
            'status' => '-1',
            'point' => $request['point'],
            'subcategory_id' => $request['subcategory'],
            'sent_in' => $sent_to,
            'type'  => $type,
            'preset_type' => '0',
            'sendto_level'=>$sendto_level,
            'sendto_region'=>$sendto_region,
            'end_on' => $enddate,
            'employee_id' => Session::get('employee')->id
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
        $challenge = LevelChallenge::create($data);


        $company = Users::where('id',$challenge->company_id)->first();

        $message = $company->name." has created a Timed Challenge, ".$challenge->challenge_text;
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
        elseif($challenge->type == 'acccess_level') {
          //$receiver = $challenge->employee_id;
          $receiver_type = 5;
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
       $message = Session::get('employee')->full_name.' created, '.$challenge->challenge_text;

        if($challenge->type == 'employee'){
          $emp_ids = explode(',',$request['empcount']);
          foreach($emp_ids as $id)
           $api->sendpush($id,'Timed Challenge Created',$message,$data,'timedchallengecreate');
        }
        elseif($challenge->type == 'region'){
          $regions = explode(',',$region);
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->where('industry',$challenge->sendto_region)->where('access_level','>=',$challenge->sendto_level)->get();
          foreach($employees as $emp)
            $api->sendpush($emp->id,'Timed Challenge Created',$message,$data,'timedchallengecreate');
        }
        elseif($challenge->type == 'all'){
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->get();
          foreach($employees as $emp){
            $api->sendpush($emp->id,'Timed Challenge Created',$message,$data,'timedchallengecreate');
          }
        }



        return redirect()->route('executive.level-challenge.list')->with('success','Challenge created successfully.');
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

             $challenge = LevelChallenge::find($id);

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

                return view('executive.level_challenge.show',compact('challenge'));
            }
            else {
                return redirect()->route('executive.level-challenge.list')->with('errors','No challenge Found.');
            }

        } else {
            return redirect()->route('executive.level-challenge.list')->with('errors','No challenge Found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

         $challenge = LevelChallenge::find($id);

        if($challenge == ''){
            return redirect('employee/level-challenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1'){
            return view('executive.challenge.edit');
        }
        $challenge_data = array();
        $user = Session::get('employee');
        $challenge_data = array('is_level_3' => 0);
        $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->get()->toArray();
        $challenge_data['subcategory'] = Subcategory::where('category_id',$challenge->category_id)->get()->toArray();
        $challenge_data['access_level_data'] = Accesslevel::where('id','<=',4)->get()->toArray();
        return view('executive.level_challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
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

         $challenge = LevelChallenge::find($id);

        if($challenge == ''){
            return redirect('executive/employee/level-challenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1' && $challenge->status != '2'){
            return redirect('executive/employee/level-challenge')->with('errors','This challenge is closed.');
        } else if($challenge->status == '2'){
            return redirect('executive/employee/level-challenge')->with('errors','This challenge is rejected.');
        }
        $user = Session::get('employee');

        request()->validate([
            'challenge_text' => 'required',
            'subcategory' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if(empty($request['subcategory'])){

             return redirect('executive.employee.level-challenge.edit')->with('error','No SubCategroy Found.');
        }

        if(!empty($request['sent_to'])){

          $kind = $request['sent_to'];

        $type = "";
        $region='';
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
                $regions = Industry::where('company_id',Session::get('employee')->company_id)->get();
                if(!empty($regions)){
                    foreach($regions as $emp_region){
                            $region .= $emp_region->id.',';
                    }
                    $region =  rtrim($region,",");
                    $sendto_region = $region;

                }
                else $sendto_region = '';

                $sent_to = '-1';
                break;
            default:
                $type = "";
                $sent_to = '-1';
                break;
        }
        $enddate = date('Y-m-d h:i:s',strtotime($request['end_date']));
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' => Session::get('employee')->company_id,
            'category_id' => $request['category'],
            'status' => '-1',
            'point' => $request['point'],
            'subcategory_id' => $request['subcategory'],
            'sent_in' => $sent_to,
            'type'  => $type,
            'preset_type' => '0',
            'sendto_level'=>$sendto_level,
            'sendto_region'=>$sendto_region,
            'end_on' => $enddate,
            'employee_id' => Session::get('employee')->id
        );


	}else {
        $enddate = date('Y-m-d h:i:s',strtotime($request['end_date']));
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'point' => $request['point'],
            'subcategory_id' =>$request['subcategory'],
            'end_on' => $enddate,
            'access_level' => $request['access_level']
        );



	}
        if($request->hasFile('image')){
            $get_challenge = LevelChallenge::find($id);
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
            else{
                $data['image'] = '';
            }


        }

        $challengedata = LevelChallenge::find($id)->update($data);

        $challenge = LevelChallenge::find($id);

            $company = Users::where('id',$challenge->company_id)->first();

        $message = $company->name." has created a Timed Challenge, ".$challenge->challenge_text;
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
       $message = Session::get('employee')->full_name.' created, '.$challenge->challenge_text;

        if($challenge->type == 'employee'){
          $emp_ids = explode(',',$challenge->sent_in);
          foreach($emp_ids as $id){
             $emp = Employee::where('id',$id)->where('is_deleted','0')->first();
           if(!empty($emp))
           $api->sendpush($id,'Timed Challenge Created',$message,$data,'timedchallengecreate');

          }
        }
        elseif($challenge->type == 'region'){
          //$regions = explode(',',$challenge->sendto_region);
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->where('industry',$challenge->sendto_region)->where('access_level','>=',$challenge->sendto_level)->get();
          foreach($employees as $emp)
            $api->sendpush($emp->id,'Timed Challenge Created',$message,$data,'timedchallengecreate');
        }
        elseif($challenge->type == 'all'){
          $employees = Employee::where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->get();
          foreach($employees as $emp){
            $api->sendpush($emp->id,'Timed Challenge Created',$message,$data,'timedchallengecreate');
          }
        }
        /*if($request->hasFile('image')){
            $path = public_path().'/images/challenge/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/challenge'), $imageName);
        }*/
        return redirect()->route('executive.level-challenge.list')->with('success','Challenge updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Challenge $challenge,$id)
    {
        if(Session::get('employee')->access_level == 3){
            $challenge = LevelChallenge::find($id);
        } else{
            $challenge = LevelChallenge::where('id',$id)->where('company_id', Session::get('employee')->company_id)->first();
        }
        if($challenge == ''){
            return redirect('employee/level-challenge')->with('error','No challenge Found.');
        }
        if($challenge->image != ''){
            $filename = 'images/challenge/'.$challenge->image;
            Storage::disk("s3")->delete($filename);
            // if(File::exists($filename)){
            //     File::delete($filename);
            // }
        }
        LevelChallenge::find($id)->delete();
        return redirect('employee/level-challenge')->with('success','Challenge Deleted successfully.');
    }

    public function challangedelete(){

        if(!empty($_POST['ids'])){

            foreach($_POST['ids'] as $id){

                    $challenge = LevelChallenge::find($id);

                if($challenge == ''){
                    continue;
                }
                if($challenge->image != ''){
                    $filename = 'images/challenge/'.$challenge->image;
                    Storage::disk("s3")->delete($filename);
                    // if(File::exists($filename)){
                    //     File::delete($filename);
                    // }
                }
                LevelChallenge::find($id)->delete();
            }
            echo json_encode(array('status'=>true));die;
        }
    }

    public function delete($id)
    {

            $challenge = LevelChallenge::find($id);

        if($challenge == ''){
            return response()->json(['status'=>false,'message'=>'No challenge Found.']);
        }
        if($challenge->image != ''){
            $filename = 'images/challenge/'.$challenge->image;
            Storage::disk("s3")->delete($filename);
            // if(File::exists($filename)){
            //     File::delete($filename);
            // }
        }
        LevelChallenge::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Challenge deleted successfully.']);
    }

    public function getCategoryFromCompany($id){
        if($id && $id > 0){
            if(Session::get('employee')->access_level == 3){
                $categories = Categories::select('id','category_name')->whereIn('company_id',array($id, '0'))->get()->toArray();
            } else {
                $categories = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->get()->toArray();
            }
            if($categories){
                $html = '<option value="">select Category</option>';
                foreach ($categories as $item) {
                    $html .= '<option value="'.$item['id'].'">'.$item['category_name'].'</option>';
                }
                return response()->json(['status'=>true,'html'=>$html]);
            } else {
                return response()->json(['status'=>false,'message'=>'No Category']);
            }
        } else {
            return response()->json(['status'=>false,'message'=>'Parameter missing.']);
        }
    }



  public  function getSubcategory(Request $request, $id){

        $subcategory = Subcategory::where('category_id',$id)->get();
        $subcat_string = "";
        if($subcategory){

            foreach($subcategory as $key => $subcat){
            	$subcat_string .= "<option value=".$subcat->id.">$subcat->subcategory_name</option>";
            }

        }
        return response()->json(['status'=>true,'sub_cat_html'=>$subcat_string]);
  }

   public  function getemployee($region, $level){

        $data = Employee::where('industry',$region)->where('access_level','<=',$level)->where('is_deleted','0')
        ->where('company_id',Session::get('employee')->company_id)->get()->toArray();

        if(!empty($data) && $data != ''){

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

       else return response()->json(['status'=>false,'message'=>"No Employee Found."]);


  }

  public function getaccesslevel(){

       $data= AccessLevel::where('id','<=',2)->get();

       if(!empty($data)){
        $data = $data->toArray();
           $html = '<strong><label for="employee">Select AccessLevel*</label></strong><select class="form-control" name="employee_accesslevel" id="employee_accesslevel" required>
           <option value="">select Accesslevel</option>';
                      foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['access_level_name'].'</option>';
                    }
                    $html .= '</select>';
          return response()->json(['status'=>true,'html'=>$html]);
       }

       else return response()->json(['status'=>false,'message'=>"No AccessLevel Found."]);

  }
  public function getregion($id){

        if($id && ($id == '1' || $id == '2' ))
        {

            $data = Industry::select('id','industry_name')->where('company_id',Session::get('employee')->company_id)->get()->toArray();

            if(!empty($data)){

                 if($id == "1") {

		    $html = '<strong><label for="employee">Select Store *</label></strong><select class="form-control" name="employee_region[]" id="employee_region" required multiple>
		    <option value="">select Store</option>';
                      foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['industry_name'].'</option>';
                    }
                    $html .= '</select>';

                 }

                 elseif($id == "2"){
                     $html = '<strong><label for="employee">Select Store *</label></strong><select class="form-control" name="region[]" id="region" required multiple>';
                      foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['industry_name'].'</option>';
                    }
                      $html .= '</select>';
                 }
                return response()->json(['status'=>true,'html'=>$html]);
            }

             else {
                return response()->json(['status'=>false,'message'=>"No Store Found."]);
            }
        }
   }

}

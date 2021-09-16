<?php

namespace App\Http\Controllers\master;

use App\Builds;
use App\Validations;
use Illuminate\Http\Request;
use App\Categories;
use App\Employee;
use App\Notification;
use Yajra\Datatables\Datatables;
use App\Challenge;
use App\Subcategory;
use App\Duels;
use App\Http\Controllers\API\ApiController;
use File;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;

use Image;
class BuildsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $builds = Builds::latest()->paginate(5);
        $builds->from_where = '0';
        $builds->employee_id = 0;
        $builds->employee_g_status = '';
        return view('master.builds.index',compact('builds'));

    }

    public function buildsdatatable()
    {
//        $builds = Builds::orderBy('created_at','desc')->get();
        $builds = Builds::with(['employee'])->orderBy('created_at','desc')->get();

        $result = array();
        $resultitem = array();

        if(!empty($builds)){

            $builds = $builds->toArray();

            foreach ($builds as $item) {
               $resultitem = $item ;
      	      $resultitem['created_at'] = date('Y-m-d a h:i:s',strtotime($item['created_at']));
//               $employee = Employee::where('id', $item['employee_id'])->first();
               $employee = $item['employee'];

                if(!empty($employee)){
                    $resultitem['employee_id'] = $item['employee']['full_name'];
//                    $resultitem['employee_id'] = $employee->full_name;
                }
                else{
                    $resultitem['employee_id'] = '--';
                }
                if($item['status'] == '-1'){
                    $resultitem['status'] = "<label class='badge badge-warning'>In progress</label>";
                } else if($item['status'] == '0'){
                    $resultitem['status'] = "<label class='badge badge-danger'>Loss</label>";
                } else if($item['status'] == '1'){
                    $resultitem['status'] = "<label class='badge badge-info'>Win</label>";
                }

                $result[] = $resultitem;
            }
        }

        return Datatables::of($result)->rawColumns(['status'])->make(true);
    }

    //employee validation list
    public function employeeBuild($id)
    {
        if(Auth::guard('admin')->user()->access_level == 4){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        } else{
            $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::guard('admin')->user()->company_id)->count();
        }
        if($employee == 0){
            return redirect()->route('master.builds.list')->with('errors','Employee not found.');
        }
        $builds = Builds::where('employee_id', $id)->latest()->paginate(5);
        $builds->from_where = '1';
        $builds->employee_id = $id;
        $builds->employee_g_status = '';
        return view('master.builds.index',compact('builds'))
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

        $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->orderBy('full_name','asc')->get()->toArray();
  	    $builds_data['categories'] = Categories::all();
        return view('master.builds.create')->with('builds_data', $builds_data);
    }


     function getSubcategory(Request $request, $id){

        $subcategory = Subcategory::where('category_id',$id)->get();
        $subcat_string = "";
        if($subcategory->count()){
            foreach($subcategory as $key => $subcat)
            $subcat_string .= "<option value=".$subcat->id.">$subcat->subcategory_name</option>";
            return response()->json(['status'=>true,'sub_cat_html'=>$subcat_string]);
        }
        else{
             return response()->json(['status'=>false,'message'=>'No Subcategory Found.']);
        }
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
            'build_text' => 'required',
            'category' => 'required',
            'status' => 'required',
            'employee' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = Array (
            'build_text' => $request['build_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'employee_id' => $request['employee'],
            'image' => $request['image'],
            'challenge_id' => 0
        );
        if($request['subcategory']){
            $data['subcategory'] = $request['subcategory'];
        }
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
                $message = $employee->full_name. " create a new build ".$request['build_text']." with ".$category->category_name." category.";
            }
            $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>$employee->company_id,
                  'receiver_type'=>2
              );

	        Notification::create($list_param);

	        $api = new ApiController;
            $emp = Employee::where('company_id',$employee->company_id)->where('is_deleted','0')->get();
            if(!empty($emp)){
                foreach($emp as $item)
                $api->sendpush($item->id,'Build Created',$message,$data,'buildcreate');
            }

        }
        return redirect()->route('master.builds.list')->with('success','Build created successfully.');
    }


    public function employeeStatusData($id,$status){
        $builds = Builds::latest()->paginate(5);
        $builds->from_where = '2';
        $builds->employee_id = $id;
        $builds->employee_g_status = $status;
        return view('master.builds.index',compact('builds'))
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
                    $challenge = Challenge::select('id','challenge_text')->where('id',$builds->challenge_id)->first();
                    $builds->challenge_name = '--';
                    if($challenge && !empty($challenge) && $challenge->challenge_text != ''){
                        $builds->challenge_name = $challenge->challenge_text;
                    }
                }

                return view('master.builds.show',compact('builds'));
            } else {
                return redirect()->route('master.builds.list')->with('errors','No Builds Found.');
            }

        } else {
            return redirect()->route('master.builds.list')->with('errors','No Builds Found.');
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

        $builds = Builds::find($id);


        if($builds == ''){
            return redirect()->route('master.builds.list')->with('errors','No builds Found.');
        }
         $builds_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
        if($builds->status != '-1'){
            return view('master.builds.edit');
        }

        return view('master.builds.edit',compact('builds'))->with('builds_data', $builds_data);
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

        $get_build = Builds::find($id);

        if($get_build == ''){
            return redirect()->route('master.builds.list')->with('errors','No build Found.');
        }
        if($get_build->status != '-1'){
            return redirect()->route('master.builds.list')->with('errors','This build is closed.');
        }
        request()->validate([
            'build_text' => 'required',

            'status' => 'required',
            'employee' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = Array (
            'build_text' => $request['build_text'],
            'status' => $request['status']
        );

        $employee = Employee::select('id','company_id','full_name')->where('id', $request['employee'])->where('is_deleted','0')->first();

        if($employee != '' && isset($employee)){
            $data['company_id'] = $employee->company_id;
            $data['employee_id'] = $employee->id;

        } else {

            return redirect()->route('master.builds.list',['id'=>$id])->with('error','The given employee is not found.');

        }

        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){

            if($get_build->image != ''){
                $filename = public_path().'/images/build/'.$get_build->image;
                if(File::exists($filename)){
                    File::delete($filename);
                }
            }
            if($request->hasFile('image')){

                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/build/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
            }

        }


        $api = new ApiController;
        $currenttop = $api->getTopRank(Session::get('employee')->id);
        Builds::find($id)->update($data);
        $build = Builds::find($id);

        if($build->status != '-1'){
            $validatedata = array(
                'employee_id'=>Session::get('employee')->id,
                'status'=>$build->status,
                'build_id'=>$build->id,
                'win'=>$build->status
               );
            Validations::create($validatedata);
            $aftertop = $api->getTopRank(Session::get('employee')->id);
            if($currenttop != $aftertop){
                $api->topleadermsg($aftertop,Session::get('employee')->id);
            }
        }


        $emp_id = $build->employee_id;
        $emp = Employee::where('id',$emp_id)->first();
        $title = '';
        $alert = '';


        if($build->status == '1'){
          $title = "Congratulations ";
          $alert = " approved!";
        }
        if($build->status == '0'){
          $title = "Sorry ";
          $alert = " rejected!";
        }

        if($build->duel_id == 0 && $build->status != '-1'){

                $message = $title.$emp->full_name.". Your Upload has been".$alert;
                $api->sendpush($emp->id,'Build approved',$message,$data,'buildApprove');

                 $list_param = array(
                              'content_type'=>5,
                              'message' => $message,
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$emp_id,
                              'receiver_type'=>3
                );
                Notification::create($list_param);
        }
        else{

            $duel = Duels::find($build->duel_id);

            if(!empty($duel)){

                $duelstatus = '';
                $chal = Challenge::find($duel->challenge_id);
                if(!empty($chal)){
                    $sender_emp = Employee::find($duel->sender);
                    $receiver_emp = Employee::find($duel->receiver);
                    //approve
                    if($build->status == '1'){

                        $duelstatus = '2';
                        $message1 = "Congratulations,".$sender_emp->full_name."Your Duel(".$chal->challenge_text.") has been approved";
                        $message2 = "Sorry,".$receiver_emp->full_name."Your Duel(".$chal->challenge_text.") has been rejected";
                        $api->sendpush($duel->receiver,'Duel approved',$message1,$data,'DuelApprove');

                        $list_param = array(
                              'content_type'=>5,
                              'message' => $message1,
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$duel->receiver,
                              'receiver_type'=>3
                        );
                        Notification::create($list_param);

                        $api->sendpush($duel->sender,'Duel rejected',$message2,$data,'DuelReject');
                        $list_param1 = array(
                              'content_type'=>5,
                              'message' => $message2,
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$duel->sender,
                              'receiver_type'=>3
                        );
                        Notification::create($list_param1);

                    }
                    //reject
                    if($build->status == '0'){
                        $duelstatus = '0';

                        $message3 = "Congratulations,".$receiver_emp->full_name."Your Duel(".$chal->challenge_text.") has been approved";
                        $message4 = "Sorry,".$sender_emp->full_name."Your Duel(".$chal->challenge_text.") has been rejected";

                        $api->sendpush($duel->receiver,'Duel Rejected',$message4,$data,'DuelReject');

                        $list_param2 = array(
                              'content_type'=>5,
                              'message' => $message4,
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$duel->receiver,
                              'receiver_type'=>3
                        );

                        Notification::create($list_param2);

                        $api->sendpush($duel->sender,'Duel Approved',$message3,$data,'DuelApprove');
                        $list_param3 = array(
                              'content_type'=>5,
                              'message' => $message3,
                              'sender'=>Session::get('employee')->id,
                              'receiver'=>$duel->sender,
                              'receiver_type'=>3
                        );
                        Notification::create($list_param3);
                    }
                }


            }
        }
        return redirect()->route('master.builds.list')->with('success','Build updated successfully.');
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
            return redirect()->route('master.builds.list')->with('error','No builds Found.');
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
        return redirect()->route('master.builds.list')->with('success','Builds Deleted successfully.');
    }

    public function builddelete(){
         //echo json_encode(array('status'=>true));die;
        if(!empty($_POST['ids'])){

            foreach($_POST['ids'] as $id){

               $builds = Builds::find($id);
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
                }
                else {
                    continue;
                }
            }


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
            $challenge->update($ch_data);
        }
    }
}

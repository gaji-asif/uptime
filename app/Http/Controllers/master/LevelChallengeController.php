<?php

namespace App\Http\Controllers\master;

use App\LevelChallenge;
use Illuminate\Http\Request;
use App\Builds;
use App\Categories;
use App\Subcategory;
use App\Employee;
use Yajra\Datatables\Datatables;
use File;
use App\Users;
use App\Accesslevel;
use DB;
use Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\ApiController;

class LevelChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        return view('admin.level_challenge.index');
    }

    public function challengedatatable()
    {
       
      $challenge = LevelChallenge::where('preset_type','0')->get();      
        if(!empty($challenge)){
           foreach ($challenge as $item) {
                if($item->status == '-1'){
                    $item->status = "<label class='badge badge-info'>In progress</label>";
                } else if($item->status == '0'){
                    $item->status = "<label class='badge badge-warning'>Rejected</label>";
                } else if($item->status == '1'){
                    $item->status = "<label class='badge badge-success'>Approved</label>";
                }
            }
        }
        return Datatables::of($challenge)->rawColumns(['status'])->make(true);
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
        $challenge_data['access_level_data'] = Accesslevel::all();
        return view('admin.level_challenge.create')->with('challenge_data', $challenge_data);

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
           // 'employee'    => 'required',
            'challenge_text' => 'required',
            'point' => 'required',
            'category' => 'required',
            
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($user->access_level == 3){
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                //return redirect('employee/level-challenge/create')->with('error','Company Not found.');
            }
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
        } else {
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->count();
        }
        if($hasCategory == 0){
            //return redirect('employee/level-challenge/create')->with('error','Category Not found.');
        }

        if(isset($request['subcategory'])){
            $sub_category_id = $request['subcategory'];
        }else{
            $sub_category_id = 0;
        }

        $companyId = $request['company'] ? $request['company'] : 0;
        
        $kind = $request['sent_to'];

        $sent_to = $request['employee'];
        $type = "";
        switch ($kind) {
            case 1:
                $type = "employee";
                break;
             case 2:
                $type = "region";
                break;
             case 3:
                $type = "all";
                   $sent_to = -1;
                break;
            default:
                $type = "";
                $sent_to = -1;
                break;
        }
     
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' => Session::get('employee')->company_id,
            'category_id' => $request['category'],
            'status' => '-1',
            'point' => $request['point'],
            'subcategory_id' => $sub_category_id,
           // 'access_level' => $request['access_level'],
            'sent_in' => $sent_to,
            'type'  => $type,
            'preset_type' => '0',
            'end_on' => $request['end_date'],
        );

        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $data['image'] = $imageName;
        }
        $challenge = LevelChallenge::create($data);

        if($challenge->id && $request->hasFile('image')){
            $path = 'images/challenge/';
            $file = $request->file('image');
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->image->move(public_path('images/challenge'), $imageName);
        }

        // $api = new ApiController;
        // $api->newChallenge($challenge);
        return redirect()->route('admin.level-challenge.list')->with('success','Challenge created successfully.');
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

                return view('admin.level_challenge.show',compact('challenge'));
            } 
            else {
                return redirect()->route('admin.level-challenge.list')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect()->route('admin.level-challenge.list')->with('errors','No challenge Found.');
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
            return view('challenge.edit');
        }
        $challenge_data = array();
        $user = Session::get('employee');
        $challenge_data = array('is_level_3' => 0);
        $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->get()->toArray();

        /*if($user->access_level == 3){
            $challenge_data['is_level_3'] = 1;
            $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();

            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($challenge->company_id, '0'))->get()->toArray();
            
        } else {
            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->get()->toArray();
        }*/

        $challenge_data['access_level_data'] = Accesslevel::all();
        return view('admin.level_challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
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
            return redirect('employee/level-challenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1' && $challenge->status != '2'){
            return redirect('employee/level-challenge')->with('errors','This challenge is closed.');
        } else if($challenge->status == '2'){
            return redirect('employee/level-challenge')->with('errors','This challenge is rejected.');
        }
        $user = Session::get('employee');
       
        request()->validate([
            'challenge_text' => 'required',
            //'company' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);



        if(isset($request['subcategory']) && $request['subcategory'] != "" && !empty($request['subcategory'])){
            $sub_category_id = $request['subcategory'];
        }else{
            $sub_category_id = '0';
        }

        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'point' => $request['point'],
            'subcategory_id' => $sub_category_id,
            'access_level' => $request['access_level'],
            'sent_in' => $request['sent_to'],
            'end_on' => $request['end_date'],
        );

        
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                //return redirect()->route('employee/level-challenge/edit',['id'=>$id])->with('error','Company Not found.');
            }
            $data['company_id'] = $request['company'] ? $request['company'] : 0;
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
      
        if($hasCategory == 0){
            //return redirect('employee/level-challenge/edit/'.$id)->with('error','Category Not found.');
        }

        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
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
                $data['image'] = $imageName;
            }
            if(isset($request->delete_image) && $request->delete_image == 1){
                $data['image'] = '';
            }
        }
        $challenge = LevelChallenge::find($id)->update($data);
        if($request->hasFile('image')){
            $path = 'images/challenge/';
            $file = $request->file("image");
            Storage::disk("s3")->put($path . $imageName, file_get_contents($file), "public");
            // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            // $request->image->move(public_path('images/challenge'), $imageName);
        }
        return redirect()->route('admin.level-challenge.list')->with('success','Challenge updated successfully.');
       
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


      function getemployee($id){
     
        if($id && ($id == '1' || $id == '2' ))         //||  $id == '3'))
        {
           
            if($id == '1'){

               $data = Employee::select('id','full_name')
               ->where('company_id', Session::get('employee')->company_id)
               ->get()->toArray();
                
            } 
            else if($id == '2'){
             $data = Employee::select('id','full_name')
               ->where('company_id',Session::get('employee')->company_id)
               ->where('industry',Session::get('employee')->industry)
               ->get()->toArray();
            }
            
            if(!empty($data)){
                
                 if($id == "1") {
                      $html = '<strong><label for="company">Select Employee *</label></strong><select class="form-control" name="employee" id="employee" required><option value="">select Employee</option>';
                 }
                 elseif($id == "2"){
                     $html = '<strong><label for="company">Select Region *</label></strong><select class="form-control" name="employee" id="employee" required><option value="">select Region</option>';
                 }
            
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['full_name'].'</option>';
                    }
                    $html .= '</select>';
                   
                    return response()->json(['status'=>true,'html'=>$html]);

            }

             else {
                return response()->json(['status'=>false,'message'=>"No " . $id . " found."]);
            }
        }
   }

}

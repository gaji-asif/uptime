<?php

namespace App\Http\Controllers\master;

use App\LevelPresetChallenge;
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
use App\Http\Controllers\API\ApiController;
use Illuminate\Support\Facades\Storage;

class LevelPresetChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('admin.level_preset_challenge.index');
        
    }

    public function challengedatatable()
    {
        if(Session::get('employee')->access_level == 3){
            $challenge = LevelPresetChallenge::where('preset_type','1')->get();
        } else{
            $challenge = LevelPresetChallenge::where('company_id', Session::get('employee')->company_id)->get();
        }
        if(!empty($challenge)){
            foreach ($challenge as $item) {
               /* $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_name = '--';
                $item->subcategory_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_name = $user->name;
                }

                $subcategory = Subcategory::where('id',$item->subcategory_id)->first();
                if($subcategory && !empty($subcategory) && $subcategory->subcategory_name != ''){
                    $item->subcategory_name = $subcategory->subcategory_name;
                } 
               */
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
      
       $challenge_data['categories'] = Categories::select('id','category_name')->where('company_id',Session::get('employee')->company_id)->get()->toArray();

       $challenge_data['access_level_data'] = Accesslevel::all();

        return view('admin.level_preset_challenge.create')->with('challenge_data', $challenge_data);
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
           'sent_to'     => 'required',
            'challenge_text' => 'required',
            'point' => 'required',
            'category' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
         

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
                break;
            default:
                $type = "";
                $sent_to = -1;
                break;
        }
     
        $data = Array (
           'challenge_text' => $request['challenge_text'],
            'company_id' => $companyId,
            'category_id' => $request['category'],
            'status' => '-1',
            'point' => $request['point'],
            'subcategory_id' => $sub_category_id,
           // 'access_level' => $request['access_level'],
            'sent_in' => $sent_to,
            'type'  => $type,
            'preset_type' => '1',
            'end_on' => $request['end_date'],
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
        
        $challenge = LevelPresetChallenge::create($data);
        return redirect()->route('admin.level-preset-challenge.list')->with('success','Challenge created successfully.');
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
            
                $challenge = LevelPresetChallenge::find($id);
           
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

                return view('admin.level_preset_challenge.show',compact('challenge'));
            } else {
                return redirect('admin/employee/level-preset-challenge')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect('admin/employee/level-preset-challenge')->with('errors','No challenge Found.');
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
       
        
        $challenge = LevelPresetChallenge::find($id);
        
        if($challenge == ''){
            return redirect('employee/level-preset-challenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1'){
            return view('challenge.edit');
        }
        $challenge_data = array();
        $user = Session::get('employee');
        $challenge_data = array('is_level_3' => 0);
        $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Session::get('employee')->company_id, '0'))->get()->toArray();

      
        $challenge_data['access_level_data'] = Accesslevel::all();
        return view('admin.level_preset_challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
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
        
        $challenge = LevelPresetChallenge::find($id);
        
        if($challenge == ''){
            return redirect('employee/level-preset-challenge')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1' && $challenge->status != '2'){
            return redirect('employee/level-preset-challenge')->with('errors','This challenge is closed.');
        } else if($challenge->status == '2'){
            return redirect('employee/level-preset-challenge')->with('errors','This challenge is rejected.');
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
            'preset_type' => $request['preset_type'],
            'subcategory_id' => $sub_category_id,
            'access_level' => $request['access_level'],
            'sent_in' => $request['sent_to'],
            'end_on' => $request['end_date'],
        );

      
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                //return redirect()->route('employee/level-preset-challenge/edit',['id'=>$id])->with('error','Company Not found.');
            }
            $data['company_id'] = $request['company'] ? $request['company'] : 0;
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
        
        if($hasCategory == 0){
            //return redirect('employee/level-preset-challenge/edit/'.$id)->with('error','Category Not found.');
        }

        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
            $get_challenge = LevelPresetChallenge::find($id);
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
        $challenge = LevelPresetChallenge::find($id)->update($data);
        
        return redirect()->route('admin.level-preset-challenge.list')->with('success','Challenge updated successfully.');
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
            $challenge = LevelPresetChallenge::find($id);
        } else{
            $challenge = LevelPresetChallenge::where('id',$id)->where('company_id', Session::get('employee')->company_id)->first();
        }
        if($challenge == ''){
            return redirect('employee/level-preset-challenge')->with('error','No challenge Found.');
        }
        if($challenge->image != ''){
            $filename = 'images/challenge/'.$challenge->image;
            Storage::disk("s3")->delete($filename);
            // if(File::exists($filename)){                    
            //     File::delete($filename); 
            // }
        }
        LevelPresetChallenge::find($id)->delete();
        return redirect('employee/level-preset-challenge')->with('success','Challenge Deleted successfully.');
    }

    public function challangedelete(){
        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                
                    $challenge = LevelPresetChallenge::find($id);
                   
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
                LevelPresetChallenge::find($id)->delete();
            }
            echo json_encode(array('status'=>true));die;
        }  
    }

    public function delete($id)
    {
      
            $challenge = LevelPresetChallenge::find($id);
       
        
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
        LevelPresetChallenge::find($id)->delete();
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
}

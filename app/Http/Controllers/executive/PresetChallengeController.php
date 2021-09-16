<?php

namespace App\Http\Controllers\executive;

use App\PresetChallenge;
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
use Auth;
use App\Http\Controllers\API\ApiController;

class PresetChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::latest()->paginate(5);
        } else{
            $challenge = PresetChallenge::where('company_id', Auth::user()->id)->latest()->paginate(5);
  
        }
        return view('executive.preset_challenge.index',compact('challenge'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function challengedatatable()
    {
        if(Auth::user()->role == 'admin'){
           $challenge = PresetChallenge::where('preset_type','1')->get();
        } else{
            $challenge = PresetChallenge::where('company_id', Auth::user()->id)->get();
            // $challenge = DB::table('challenge')
            //     ->leftJoin('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->get();
        }
        if(!empty($challenge)){
            foreach ($challenge as $item) {
                // $build = Builds::select('build_text')->where('id', $item->build_id)->first();
                // $item->build_name = '--';
                // if($build && !empty($build) && $build->build_text != ''){
                //     $item->build_name = $build->build_text;
                // }
                /*$user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_name = '--';
                $item->subcategory_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_name = $user->name;
                }

                $subcategory = Subcategory::where('id',$item->subcategory_id)->first();
                if($subcategory && !empty($subcategory) && $subcategory->subcategory_name != ''){
                    $item->subcategory_name = $subcategory->subcategory_name;
                } */

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
        // $challenge_data = array();
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::select('id','build_text')->get()->toArray();
        // } else{
        //     $builds = Builds::select('id','build_text')->where('company_id',Auth::user()->id)->get()->toArray();
        // }
        // $build_array = array();
        // if(!empty($builds)){
        //     foreach ($builds as $item) {
        //         $chal = PresetChallenge::select('id')->where('build_id', $item['id'])->first();
        //         if($chal && !empty($chal)){
        //         }else {
        //             $build_array[] = $item;
        //         }
        //     }
        // }
        // $challenge_data['builds'] = $build_array;
        
        $user = Auth::user();
        $challenge_data = array('is_admin' => 0, 'categories' => []);
        if($user->role == 'admin'){
            $challenge_data['is_admin'] = 1;
            $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        } else {
            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
        }

           // $challenge_data['access_level_data'] =Accesslevel::where('id','<=',3)->get()->toArray();

// Accesslevel::all();
        return view('executive.preset_challenge.create')->with('challenge_data', $challenge_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {
        
       
        $user = Auth::user();
        if($user->role == 'company'){
            $request['company'] = $user->id;
        }
        request()->validate([
            'company'    => 'required',
            'sent_to'     => 'required',
            'challenge_text' => 'required',
            'point' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);
        if($user->role == 'admin'){
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('challenge.create')->with('error','Company Not found.');
            }
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
        } else {
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array(Auth::user()->id, '0'))->count();
        }
        if($hasCategory == 0){
            return redirect()->route('challenge.create')->with('error','Category Not found.');
        }

        if(isset($request['subcategory'])){
            $sub_category_id = $request['subcategory'];
        }else{
            $sub_category_id = 0;
        }

        $companyId = $request['company'] ? $request['company'] : 0;
        
        $kind = $request['sent_to'];

        $sent_to = -1;
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
            'company_id' => $companyId,
            'category_id' => $request['category'],
            'status' => $request['status'],
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
            $data['image'] = $imageName;
        }

     
        $challenge = PresetChallenge::create($data);
        if($challenge->id && $request->hasFile('image')){
            $path = public_path().'/images/challenge/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/challenge'), $imageName);
        }
       // $api = new ApiController;
       // $api->newChallenge($challenge);
        return redirect()->route('preset-challenge.index')->with('success','Challenge created successfully1111.');
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
            if(Auth::user()->role == 'admin'){
                $challenge = PresetChallenge::find($id);
            } else{
                $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
                // $challenge = DB::table('challenge')
                //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
                //     ->select('challenge.*')
                //     ->where('challenge.id', $id)
                //     ->where('builds.company_id', Auth::user()->id)
                //     ->first();
            }
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
                // $challenge->employee_name = '--';
                // if(!empty($build)){
                $category = Categories::select('category_name')->where('id', $challenge->category_id)->first();
                
                if($category && !empty($category) && $category->category_name != ''){
                    $challenge->category_name = $category->category_name;
                }
                //     $employee = Employee::select('full_name', 'is_deleted')->where('id', $build->employee_id)->first();
                    
                //     if($employee && !empty($employee) && $employee->full_name != ''){
                //         if($employee->is_deleted == '0'){
                //             $challenge->employee_name = $employee->full_name;
                //         } else {
                //             $challenge->employee_name = 'Employee deleted';
                //         }
                //     }
                // }

                $user = Users::select('name')->where('id', $challenge->company_id)->first();
                $challenge->company_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $challenge->company_name = $user->name;
                }

                return view('executive.preset_challenge.show',compact('challenge'));
            } else {
                return redirect()->route('preset-challenge.index')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect()->route('preset-challenge.index')->with('errors','No challenge Found.');
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
        if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::find($id);
        } else{
            $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        if($challenge == ''){
            return redirect()->route('preset-challenge.index')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1'){
            return view('executive.challenge.edit');
        }
        $challenge_data = array();
        $user = Auth::user();
        $challenge_data = array('is_admin' => 0);
        if($user->role == 'admin'){
            $challenge_data['is_admin'] = 1;
            $challenge_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();

            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array($challenge->company_id, '0'))->get()->toArray();
            
        } else {
            $challenge_data['categories'] = Categories::select('id','category_name')->whereIn('company_id',array(Auth::user()->id, '0'))->get()->toArray();
        }
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::select('id','build_text')->get()->toArray();
        // } else{
        //     $builds = Builds::select('id','build_text')->where('company_id',Auth::user()->id)->get()->toArray();
        // }
        // $build_array = array();
        // if(!empty($builds)){
        //     foreach ($builds as $item) {
        //         if($item['id'] == $challenge->build_id){
        //             $build_array[] = $item;
        //         } else {
        //             $chal = PresetChallenge::select('id')->where('build_id', $item['id'])->first();
        //             if($chal && !empty($chal)){
        //             }else {
        //                 $build_array[] = $item;
        //             }
        //         }
        //     }
        // }
        // $challenge_data['builds'] = $build_array;
        $challenge_data['access_level_data'] = Accesslevel::where('id','<=',3)->get()->toArray();

// Accesslevel::all();
        return view('executive.preset_challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
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
        if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::find($id);
        } else{
            $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        if($challenge == ''){
            return redirect()->route('preset-challenge.index')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1' && $challenge->status != '2'){
            return redirect()->route('preset-challenge.index')->with('errors','This challenge is closed.');
        } else if($challenge->status == '2'){
            return redirect()->route('preset-challenge.index')->with('errors','This challenge is rejected.');
        }
        $user = Auth::user();
        if($user->role == 'company'){
            $request['company'] = $user->id;
        }
        request()->validate([
            'challenge_text' => 'required',
            'company' => 'required',
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
        if($user->role == 'admin'){
            $hasCompany = Users::where('id', $request['company'])->count();
            if($hasCompany == 0){
                return redirect()->route('employee.edit',['id'=>$id])->with('error','Company Not found.');
            }
            $data['company_id'] = $request['company'];
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array( $request['company'], '0'))->count();
        } else {
            $hasCategory = Categories::where('id',$request['category'])->whereIn('company_id',array(Auth::user()->id, '0'))->count();
        }
        if($hasCategory == 0){
            return redirect()->route('preset-challenge.edit',['id'=>$id])->with('error','Category Not found.');
        }
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::find($request->build);
        // } else{
        //     $builds = Builds::where('id',$request->build)->where('company_id',Auth::user()->id)->first();
        // }
        // if($builds && $builds!= ''){
        //     $chal = PresetChallenge::select('id')->where('build_id', $builds->id)->first();
        //     if($chal && !empty($chal) && $chal->id != $id){
        //         return redirect()->route('preset-challenge.edit',['id'=>$id])->with('error','The given buil already have challenge.');
        //     }
        // }
        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
            $get_challenge = PresetChallenge::find($id);
            if($get_challenge->image != ''){
                $filename = public_path().'/images/challenge/'.$get_challenge->image;
                if(File::exists($filename)){                    
                    File::delete($filename); 
                }
            }
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $data['image'] = $imageName;
            }
            if(isset($request->delete_image) && $request->delete_image == 1){
                $data['image'] = '';
            }
        }
        $challenge = PresetChallenge::find($id)->update($data);
        if($request->hasFile('image')){
            $path = public_path().'/images/challenge/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/challenge'), $imageName);
        }
        return redirect()->route('preset-challenge.index')->with('success','Challenge updated successfully.');
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
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
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
                if(Auth::user()->role == 'admin'){
                    $challenge = PresetChallenge::find($id);
                } else{
                    $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
                }
                
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
        if(Auth::user()->role == 'admin'){
            $challenge = PresetChallenge::find($id);
        } else{
            $challenge = PresetChallenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        
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

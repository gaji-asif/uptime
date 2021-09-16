<?php

namespace App\Http\Controllers\leader;

use App\Challenge;
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

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'admin'){
            $challenge = Challenge::latest()->paginate(5);
        } else{
            $challenge = Challenge::where('company_id', Auth::user()->id)->latest()->paginate(5);
            // $challenge = DB::table('challenge')
            //     ->leftJoin('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->get();
        }
        return view('challenge.index',compact('challenge'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function challengedatatable()
    {
        if(Auth::user()->role == 'admin'){
            $challenge = Challenge::where('preset_type','0')->get();
            
        } else{
            $challenge = Challenge::where('company_id', Auth::user()->id)->get();
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
        // $challenge_data = array();
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::select('id','build_text')->get()->toArray();
        // } else{
        //     $builds = Builds::select('id','build_text')->where('company_id',Auth::user()->id)->get()->toArray();
        // }
        // $build_array = array();
        // if(!empty($builds)){
        //     foreach ($builds as $item) {
        //         $chal = Challenge::select('id')->where('build_id', $item['id'])->first();
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

       
        return view('challenge.create')->with('challenge_data', $challenge_data);
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

        $companyid = $request['company'] ? $request['company'] : 0;

         
        $kind = $request['sent_to'];

        $sent_to = -1;
        $type = "";
        switch ($kind) {
            case 1:
                $type = "employee";
                 $sent_to = -1;
                break;
             case 2:
                $type = "region";
                 $sent_to = -1;
                break;
             case 3:
                $type = "all";
                   $sent_to = $companyid;
                break;
            default:
                $type = "";
                $sent_to = $companyid;
                break;
        }
        
        $data = Array (
            'challenge_text' => $request['challenge_text'],
            'company_id' => $request['company'],
            'category_id' => $request['category'],
            'status' => $request['status'],
            'point' => $request['point'],
            'subcategory_id' => $sub_category_id,
            'sent_in' => $sent_to,
            'type' => $type,
            'preset_type' => '0',
            'end_on'    => $request['end_date']
        );
    
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $data['image'] = $imageName;
        }
        
        $challenge = Challenge::create($data);
        if($challenge->id && $request->hasFile('image')){
            $path = public_path().'/images/challenge/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/challenge'), $imageName);
        }

      //  $api = new ApiController;
      //  $api->newChallenge($challenge);
        return redirect()->route('challenge.index')->with('success','Challenge created successfully.');


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
                $challenge = Challenge::find($id);
            } else{
                $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
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

                return view('challenge.show',compact('challenge'));
            } else {
                return redirect()->route('challenge.index')->with('errors','No challenge Found.');
            }
            
        } else {
            return redirect()->route('challenge.index')->with('errors','No challenge Found.');
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
            $challenge = Challenge::find($id);
        } else{
            $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        if($challenge == ''){
            return redirect()->route('challenge.index')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1'){
            return view('challenge.edit');
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
        //             $chal = Challenge::select('id')->where('build_id', $item['id'])->first();
        //             if($chal && !empty($chal)){
        //             }else {
        //                 $build_array[] = $item;
        //             }
        //         }
        //     }
        // }
        // $challenge_data['builds'] = $build_array;
        $challenge_data['access_level_data'] =Accesslevel::where('id','<=',3)->get()->toArray();

        // Accesslevel::all();
        return view('challenge.edit',compact('challenge'))->with('challenge_data', $challenge_data);
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
            $challenge = Challenge::find($id);
        } else{
            $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        if($challenge == ''){
            return redirect()->route('challenge.index')->with('errors','No challenge Found.');
        }
        if($challenge->status != '-1' && $challenge->status != '2'){
            return redirect()->route('challenge.index')->with('errors','This challenge is closed.');
        } else if($challenge->status == '2'){
            return redirect()->route('challenge.index')->with('errors','This challenge is rejected.');
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
            'end_on'    => $request['end_date']
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
            return redirect()->route('challenge.edit',['id'=>$id])->with('error','Category Not found.');
        }
        // if(Auth::user()->role == 'admin'){
        //     $builds = Builds::find($request->build);
        // } else{
        //     $builds = Builds::where('id',$request->build)->where('company_id',Auth::user()->id)->first();
        // }
        // if($builds && $builds!= ''){
        //     $chal = Challenge::select('id')->where('build_id', $builds->id)->first();
        //     if($chal && !empty($chal) && $chal->id != $id){
        //         return redirect()->route('challenge.edit',['id'=>$id])->with('error','The given buil already have challenge.');
        //     }
        // }
        if($request->hasFile('image') || (isset($request->delete_image) && $request->delete_image == 1)){
            $get_challenge = Challenge::find($id);
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
        $challenge = Challenge::find($id)->update($data);
        if($request->hasFile('image')){
            $path = public_path().'/images/challenge/';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $request->image->move(public_path('images/challenge'), $imageName);
        }
        return redirect()->route('challenge.index')->with('success','Challenge updated successfully.');
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
            $challenge = Challenge::find($id);
        } else{
            $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
            // $challenge = DB::table('challenge')
            //     ->join('builds', 'builds.id', '=', 'challenge.build_id')
            //     ->select('challenge.*')
            //     ->where('challenge.id', $id)
            //     ->where('builds.company_id', Auth::user()->id)
            //     ->first();
        }
        if($challenge == ''){
            return redirect()->route('challenge.index')->with('error','No challenge Found.');
        }
        if($challenge->image != ''){
            $filename = public_path().'/images/challenge/'.$challenge->image;
            if(File::exists($filename)){                    
                File::delete($filename); 
            }
        }
        Challenge::find($id)->delete();
        return redirect()->route('challenge.index')->with('success','Challenge Deleted successfully.');
    }

    public function challangedelete(){
        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                if(Auth::user()->role == 'admin'){
                    $challenge = Challenge::find($id);
                } else{
                    $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
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
                Challenge::find($id)->delete();
                //return response()->json(['status'=>true,'message'=>'Challenge deleted successfully.']);
            }
            echo json_encode(array('status'=>true));die;
        }  
    }

    public function delete($id)
    {
        if(Auth::user()->role == 'admin'){
            $challenge = Challenge::find($id);
        } else{
            $challenge = Challenge::where('id',$id)->where('company_id', Auth::user()->id)->first();
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
        Challenge::find($id)->delete();
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

    function getemployee($id){

        if($id && ($id == '1' || $id == '2' ||  $id == '3'))
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
            if($id == '3'){
               $data = Employee::select('id','full_name')
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
                return response()->json(['status'=>false,'message'=>"No " . $id . " found."]);
            }
        }
    }
}

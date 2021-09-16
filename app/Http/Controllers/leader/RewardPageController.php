<?php

namespace App\Http\Controllers\leader;
use Auth;
use Session;
use DB;
use File;
use App\Reward;
use App\Accesslevel;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Users;
use App\Industry;
use App\Employee;
use App\Purchase;
use Image;
use Illuminate\Support\Facades\Storage;

class RewardPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          return view('leader.reward.index');
    }
    
    public function rewarddatatable(){

      $reward = Reward::where('company_id',Session::get('employee')->company_id)->orderBy('created_at','DESC')->get();
        if(!empty($reward)){

            foreach ($reward as $item) {
               
               if($item->is_active == '0'){
                    $item->is_active = "<label class='badge badge-info'>In Active</label>";
                } 
                else if($item->is_active == '1'){
                    $item->is_active = "<label class='badge badge-success'>Active</label>";
                }
                $level = Accesslevel::where('id',$item->access_level)->first();
                $item->access_level = $level->access_level_name;
                 

            }
              return Datatables::of($reward)->rawColumns(['is_active'])->make(true);
        }
       else{
           $result = array();
            return Datatables::of($result)->make(true);
       }
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reward_data['access_level'] = Accesslevel::where('id','<=',2)->get();
        return view('leader.reward.create')->with('reward_data',$reward_data);
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
            
            'name'=>'required',
            'description'=>'required',
            'point'=>'required',
            'access_level'=>'required',
            'is_active' => 'required', 
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        
        if($request['image']==""){
              return redirect()->route('reward.create')->with('error','Reward Image required.');
        }
        
        $data = Array (
            'name' => $request['name'],
            'description' => $request['description'],
            'point' => $request['point'],
            'access_level' => $request['access_level'],
            'is_active' => $request['is_active'],
              'company_id' =>Session::get('employee')->company_id
        );

        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $path = 'images/reward/';
            $file = $request->file("image");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $data['image'] = $imageName ;
           
        }

        $reward = Reward::create($data);
 
        return redirect()->route('reward.index')->with('success','Reward created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reward = Reward::find($id);        
        $sessionName = 'reward';        //assume a session name
        Session::put($sessionName, $id);   //put the data and in session  
        return view('leader.reward.show',compact('reward'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reward = Reward::find($id);
        $reward_data['access_level'] = Accesslevel::where('id','<=',2)->get();

        return view('leader.reward.edit',compact('reward'))->with('reward_data',$reward_data);
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
        $reward = Reward::find($id);
        
        if($reward == ''){
            return redirect()->route('reward.index')->with('errors','No Reward Found.');
        }
       
        request()->validate([
            'name'=>'required',
            'description'=>'required',
            'point'=>'required',
            'access_level'=>'required',
            'is_active' => 'required', 
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        
        
        $data = Array (
            'name' => $request['name'],
            'description' => $request['description'],
            'point' => $request['point'],
            'access_level' => $request['access_level'],
            'is_active' => $request['is_active']
           
        );


        if($request->hasFile('image')){
            $get_reward = Reward::find($id);

            if($get_reward->image != ''){
                $filename = 'images/reward/'.$get_reward->image;
                Storage::disk("s3")->delete($filename);
                // if(File::exists($filename)){                    
                //     File::delete($filename); 
                // }
            }
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/reward/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;
               
            }
            
        } 

        $update_data = Reward::find($id)->update($data);

        
        return redirect()->route('reward.index')->with('success','Upload updated successfully.');
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Reward::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Reward deleted successfully.']);
    }

    public function deletes(){

        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                
                $upload = Reward::where('id',$id)->count();
               
                if($upload == 0){
                    continue;
                }
                 Reward::find($id)->delete();
               
            }
            echo json_encode(array('status'=>true));die;
        }     

        //Upload::find($id)->delete();
       // return response()->json(['status'=>true,'message'=>'Upload deleted successfully.']);
    }
    
    public function useremployeedatatable(){
    
        $reward_id = Session::get('reward');
        $purchase = Purchase::where('rewarditem_id',$reward_id)->latest()->get();
       
        $resultitem = array();
        $result= array();

       if(!empty($purchase)){

            foreach($purchase as $item){
                $employee = Employee::where('is_deleted','0')->where('id',$item->employee_id)->first();                        
                
                if(!empty($employee)){
                    $resultitem['full_name'] = $employee->full_name;
                    $resultitem['id'] = $employee->id;
                  
                    if($employee->image != '')
                          $imagepath = Storage::disk("s3")->url('/images/employee/').'/'.$employee->image;
                    else $imagepath =  Storage::disk("s3")->url('images/no_image.png');
                    
                    $resultitem['image'] = '<img src="'.$imagepath.'" alt="emloyeeimage" width= 500px height= 500px >';
                    $resultitem['imagepath'] = $imagepath;
                    $resultitem['created_at'] = date("m/d/Y H:i:s", strtotime($item->created_at));
                    $result[] = $resultitem;
                 }
            }
        }
        return Datatables::of($result)->rawColumns(['image'])->make(true);
    
                       
    }

}

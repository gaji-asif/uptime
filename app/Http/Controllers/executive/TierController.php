<?php

namespace App\Http\Controllers\executive;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Tier;
use App\TierList;
use App\Notification;
use App\Accesslevel;
use App\Users;
use App\Employee;
use App\Categories;
use App\Subcategory;
use App\Builds;
use Session;
class TierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {      
   
        return view('executive.tier.index');
    }
    
    public function tierdatatable(){

        $tiers = TierList::orderBy('created_at','desc')->get();
        $tiers_data = array();
        $resultitem = array();
        if(!empty($tiers)){
           foreach ($tiers as $item) {
               $resultitem['id'] = $item->id; 
               $tier = Tier::find($item->tier);
               $resultitem['tier'] = $tier->tier_name;
               $resultitem['validates'] = $tier->validates;
               $access = Accesslevel::find($item->access_level);
               $resultitem['access_level'] = $access->access_level_name;
               $resultitem['uploads'] = $item->uploads;
               $resultitem['challenges'] = $item->challenges;
               $resultitem['validates'] = $item->validates;
               $resultitem['points'] = $item->points; 
               $tiers_data[] = $resultitem;
           }
        }        
       return Datatables::of($tiers_data)->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tiers_count = TierList::where('access_level',array(0,1,2))->get()->count();

        if($tiers_count == 9)  return redirect('executive/tier')->with('errors','Maximum amount of tiers already created. You can edit the current ones instead.');

        $tier_data['access_level']= Accesslevel::where('id','<',Session::get('employee')->access_level)->get()->toArray();
        $tier_data['tier'] = Tier::all();
        $tier_data['point_title'] = 'Points Prize *';
        $tier1_count = TierList::where('access_level',0)->get()->count();
        $tier2_count = TierList::where('access_level',1)->get()->count();
        $tier3_count = TierList::where('access_level',2)->get()->count();
        if($tier1_count == 3 ||$tier2_count == 3 ||$tier3_count == 3)   $tier_data['point_title'] = 'Points Awarded';
        return view('executive.tier.create')->with('tier_data',$tier_data);  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    public function getBuildsCountByRegionLevel($region,$level){
    
         $builds = Builds::where('status','-1')->get();         
         $build_count = 0 ;
         foreach($builds as $item){
             $template_emp = Employee::where('id',$item->employee_id)->where('access_level','<=',$level)->where('industry',$region)->where('is_deleted','0')->get();
             if(!empty($template_emp)){
                 $build_count++;
             }
         }
         return $build_count;
    }
    public function store(Request $request)
    {
    
        request()->validate([
            'tier' => 'required',
            'access_level' => 'required',
            'uploads'  => 'required',
            'challenges' => 'required',
            'points' => 'required'
            
        ]);
       
      
        $region = Session::get('employee')->industry;
        $level = Session::get('employee')->access_level;
        
        $tiers_count = TierList::where('access_level',$request['access_level'])->get()->count();
        $access_level_name = Accesslevel::find($request['access_level'])->access_level_name;
        if($tiers_count == 3)  return redirect('executive/tier')->with('errors','You cannot create for "'.$access_level_name.'". 3 Tiers created already!'); 

        $maincats = Categories::where('company_id',Session::get('employee')->company_id)->get();
        $subcategory = array();
        foreach($maincats as $cat){
            $subcategories = Subcategory::where('category_id',$cat->id)->where('user_access_level',$request['access_level'])->get();
            if(!empty($subcategories)){
                foreach($subcategories as $item){
                    $subcategory[] = $item;
                }
            }
        }
        
       // $subcategories = Subcategory::where('user_access_level',$request['access_level'])->get();
        $subcategory_ids = '';
        $subcategory_value = '';
        
        foreach($subcategory as $item){
            $subcategory_ids .= $item['id'].',';
            $subcategory_value .= $request[$item['id']].',';
        }
        
        $subcategory_ids = rtrim($subcategory_ids,',');
        $subcategory_value = rtrim($subcategory_value,',');
        
         //estimate validate 
        $company = Session::get('employee')->company_id;
        
        $build_count = $this->getBuildsCountByRegionLevel($region,$level);
        $emp_count = Employee::where('company_id',$company)->where('is_deleted','0')->get()->count();
        
        
        $validates = $build_count/$emp_count ;
        if($validates<1) $validates = 1;
        
        $data = Array (
            'tier'        => $request['tier'],
            'access_level'=> $request['access_level'],
            'uploads'     => $request['uploads'],
            'challenges'  => $request['challenges'],
            'points' => $request['points'],
            'subcategory' => $subcategory_ids,
            'subcategory_value' =>$subcategory_value,
            'validates' =>$validates
        );
    	 
        $message =  'Tier has created';
	    
	    $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>Session::get('employee')->company_id,
                  'receiver_type'=>2
        );
        
        Notification::create($list_param);  
    
      
      TierList::create($data);
      return redirect('executive/tier')->with('success','Tier created successfully.');
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$tier = Tier::where('id',$id)->first();
        //return view('executive.tier.show',compact($tier));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tier_data['access_level']= Accesslevel::where('id','<',Session::get('employee')->access_level)->get()->toArray();
        $tier_data['tier'] = Tier::all();
        $tier = TierList::where('id',$id)->first();
        return view('executive.tier.edit',compact('tier'))->with('tier_data',$tier_data);   
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
           
     request()->validate([
            'tier' => 'required',
            'access_level' => 'required',
            'uploads'  => 'required',
            'challenges' => 'required',
            'points' => 'required'
        ]);
        
         $maincats = Categories::where('company_id',Session::get('employee')->company_id)->get();
        $subcategory = array();
        foreach($maincats as $cat){
            $subcategories = Subcategory::where('category_id',$cat->id)->where('user_access_level',$request['access_level'])->get();
            if(!empty($subcategories)){
                foreach($subcategories as $item){
                    $subcategory[] = $item;
                }
            }
        }
        
       // $subcategories = Subcategory::where('user_access_level',$request['access_level'])->get();
        $subcategory_ids = '';
        $subcategory_value = '';
        
        foreach($subcategory as $item){
            $subcategory_ids .= $item['id'].',';
            $subcategory_value .= $request[$item['id']].',';
        }
        
        $subcategory_ids = rtrim($subcategory_ids,',');
        $subcategory_value = rtrim($subcategory_value,',');
        
        $data = Array (
            'tier'        => $request['tier'],
            'access_level'=> $request['access_level'],
            'uploads'     => $request['uploads'],
            'challenges'  => $request['challenges'],
            'points' => $request['points'],
             'subcategory' => $subcategory_ids,
            'subcategory_value' =>$subcategory_value
        );
        
        TierList::find($id)->update($data);
         
        $message =  'Tier has updated';
	$list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>Session::get('employee')->company_id,
                  'receiver_type'=>2
        );
        
        Notification::create($list_param);  
    
 
 
      return redirect('executive/tier')->with('success','Tier updated successfully.');

    }

    public function getsubcategory($level){
         
        $maincats = Categories::where('company_id',Session::get('employee')->company_id)->get();
        $subcategory = array();
        foreach($maincats as $cat){
            $subcategories = Subcategory::where('category_id',$cat->id)->where('user_access_level',$level)->get();
            if(!empty($subcategories)){
                foreach($subcategories as $item){
                    $subcategory[] = $item;
                }
            }
        }
        //$subcategory = Subcategory::where('user_access_level',$level)->get();
        
        
        $header = '<div class="form-group">
                    <h6 for="subcategory">Set Max Amount of Categories a User Can Have to get Points</h6>
                    <p>Setting these numbers are setting limitations and qualifications for a user to achieve points.</p>
                   </div>';
          $sub_name = '';

           $resulthtml = '';
       
          $sub_layer = '<div class="row subcategory-layer">';
          $sub_content = '';

          if($subcategory && !empty($subcategory) && $subcategory != ''){

                $sub_name = '';
                 
                foreach($subcategory as $item){
                   
                    $sub_content .= '<div class="subcat">
                         <strong><label  class="sub-title">'.$item['subcategory_name'].'</label></strong>
                           <div class ="input-box-layer" ><input type="number" min="0" name="'.$item['id'].'" id="'.$item['id'].'" class="input-box" value="0" required>
                           </div>
                       </div>';

                }
                
                
                   $resulthtml = $header.$sub_layer.$sub_content.'</div>';           
                return response()->json(['status'=>true,'html'=>$resulthtml]);   
               
            }   
          return response()->json(['status'=>false,'message'=>'No Subcategory']);          
            
       
    }
    
public function getsubcategoryfromid($id){
         
          
          $tier = TierList::find($id);
          $subcategory_str = $tier->subcategory;
          $subcategory_value_str = $tier->subcategory_value;
         
          $subcat_arr = explode(",",$subcategory_str);
          $subcat_valarr = explode(",",$subcategory_value_str);

          $header = '<div class="form-group">
                    <h6 for="subcategory">Set Max Amount of Categories a User Can Have to get Points</h6>
                    <p>Setting these numbers are setting limitations and qualifications for a user to achieve points.</p>
                   </div>';
          $sub_name = '';

           $resulthtml = '';
       
          $sub_layer = '<div class="row subcategory-layer">';
          $sub_content = '';

          $i = 0 ;

          foreach($subcat_arr as $item){
            $value = $subcat_valarr[$i];
            $sub = Subcategory::where('id',$item)->first();
            $title = $sub->subcategory_name;
            $i++;
            $sub_content .= '<div class="subcat">
                         <strong><label  class="sub-title">'.$title.'</label></strong>
                           <div class ="input-box-layer" ><input type="number" min="0" name="'.$item.'" id="'.$item.'" class="input-box" value="'.$value.'" required>
                           </div>
                       </div>';

           }   

           $resulthtml = $header.$sub_layer.$sub_content.'</div>';           
        return response()->json(['status'=>true,'html'=>$resulthtml]);   
                    
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //echo $$_POST['ids'];die;
       if(!empty($_POST['ids'])){

            foreach($_POST['ids'] as $id){

               $tiers = TierList::find($id);
                if($tiers && $tiers != ''){
                    $tiers->delete();
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
        $tier = TierList::where('id',$id)->get()->count();
        if($tier){
            TierList::find($id)->delete();
            return response()->json(['status'=>true,'message'=>'Tier deleted successfully.']);
        }
       
        return response()->json(['status'=>false,'message'=>'No Tier Found']);
    }
}

<?php


namespace App\Http\Controllers\API;

use App\Utils\ImageProcessor;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Users;
use App\Employee;
use App\Builds;
use App\EmployeeTier;
use App\Authtoken;
use App\Useruuid;
use App\Categories;
use App\Validations;
use App\Challenge;
use App\Position;
use App\Tenure;
use App\Industry;
use App\Subcategory;
use App\Notification;
use App\Accesslevel;
use App\ReadItem;
use App\Upload;
use App\Reward;
use App\Http\Middleware\APIAuth;
use Validator;
use App\Duels;
use App\TierList;
use App\Purchase;
use App\Batch;
use File;
use Image;
use DB;
use Imagick;
use Illuminate\Support\Facades\Storage;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use Twilio\Rest\Client;
use Bitly\BitlyClient;

class ApiController extends BaseController
{

  /* Phone number Formatting */
  function formatPhoneNumber($phoneNumber) {

      $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
      if(strlen($phoneNumber) > 10) {
          $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
          $areaCode = substr($phoneNumber, -10, 3);
          $nextThree = substr($phoneNumber, -7, 3);
          $lastFour = substr($phoneNumber, -4, 4);

          $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
      }
      else if(strlen($phoneNumber) == 10) {
          $areaCode = substr($phoneNumber, 0, 3);
          $nextThree = substr($phoneNumber, 3, 3);
          $lastFour = substr($phoneNumber, 6, 4);

          $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
      }
      else if(strlen($phoneNumber) == 7) {
          $nextThree = substr($phoneNumber, 0, 3);
          $lastFour = substr($phoneNumber, 3, 4);

          $phoneNumber = $nextThree.'-'.$lastFour;
      }

      return $phoneNumber;
  }

 /*This api is for the add two string array*/
   public function addStringArray($arr1,$arr2){
       $arr = '';
       for($i = 0 ;$i < sizeof($arr1);$i++){
           $arr .= intval($arr1[$i])+intval($arr2[$i]).',';
       }
       $arr = rtrim($arr,',');
       return $arr;
   }

   public function getEmployeeTierData($eid){

        $employee = Employee::find($eid);
        $tiers = $this->getTierModel($employee);
        $starttime = date('Y-m-d 00:00:00');
        $endtime = date('Y-m-d 23:59:59');

        if(!empty($tiers)){

          //get the status of the current employee
          $emp_tier_cnt = EmployeeTier::where('employee_id',$eid)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get()->count();

          //loop the tier list
          $emp_tier = array();
          $count = 0 ;
        if($emp_tier_cnt>0){
          for($k=0;$k < $emp_tier_cnt%3; $k++){

             $emp_tier['id']= $tiers[$k]->id;
             $emp_tier['tier']= $tiers[$k]->tier;
             $emp_tier['access_level']= $tiers[$k]->access_level;
             $emp_tier['points']= $tiers[$k]->points;
             $emp_tier['subcategory']= $tiers[$k]->subcategory;

             if($count == 0){
                 $emp_tier['uploads'] = intval($tiers[$k]->uploads);
                 $emp_tier['challenges'] = intval($tiers[$k]->challenges);
                 $emp_tier['validates'] = intval($tiers[$k]->validates);
                 $emp_tier['subcategory_value']= $tiers[$k]->subcategory_value;
                 $count++;
             }
             else{
                 $emp_tier['uploads'] = $emp_tier['uploads']+intval($tiers[$k]->uploads);
                 $emp_tier['challenges'] = $emp_tier['challenges'] +intval($tiers[$k]->challenges);
                 $emp_tier['validates'] = $emp_tier['validates']+intval($tiers[$k]->validates);
                 $emp_tier['subcategory_value']= $this->addStringArray(explode(',',$emp_tier['subcategory_value']),explode(',',$tiers[$k]->subcategory_value));
             }

          }

          //get the count of uploads by employee wiht sub category
          $employee_uploads = $this->getUploadsCount($employee,$emp_tier);
          //get the count of challenges by employee with sub category
          $employee_challenges = $this->getChallengesCount($employee,$emp_tier);
          //get the count of validates by employee with sub category
          $employee_validates = $this->getValidatesCount($employee,$emp_tier);

          if($employee_uploads == 1 && $employee_challenges == 1 && $employee_validates == 1)
          {
              $data = array(
                  'employee_id'=>$eid,
                  'tier_id'=>$emp_tier['id']
              );
              EmployeeTier::create($data);
          }
        }

        else{
            $emp_tier1 = $tiers[0];

             //get the count of uploads by employee wiht sub category
                $employee_uploads1 = $this->getUploadsCount($employee,$emp_tier1);
             //get the count of challenges by employee with sub category
                $employee_challenges1 = $this->getChallengesCount($employee,$emp_tier1);
             //get the count of validates by employee with sub category
                $employee_validates1 = $this->getValidatesCount($employee,$emp_tier1);

              if($employee_uploads1 == 1 && $employee_challenges1 == 1 && $employee_validates1 == 1)
              {
                  $data = array(
                      'employee_id'=>$eid,
                      'tier_id'=>$emp_tier1['id']
                  );
                  EmployeeTier::create($data);
              }
        }

    }

   }

   // get the count of uploads by employee wiht sub category
   public function getUploadsCount($employee,$tier){

       $starttime = date('Y-m-d 00:00:00');
       $endtime = date('Y-m-d 23:59:59');
       $uploadscount = 0;
       $emp_uploads = Builds::where('employee_id',$employee->id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get();
       //$emp_uploads_cnt = Builds::where('employee_id',$employee->id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get()->count();
       $subcat = array();
       $emp_uploads_cnt=0;
       if(!empty($emp_uploads)){
          foreach($emp_uploads as $item){
           $subcats = explode(',',$item->subcategory);
            foreach($subcats as $cat){
              $subcat[] = $cat;
            }

          }
          $emp_uploads_cnt = sizeof($subcat);
        }

       if($emp_uploads_cnt >= $tier['uploads'])
          return $uploadscount = 1 ;
       else return $uploadscount = 0 ;

   }


   //get the count of challenges by employee with sub category
    public function getChallengesCount($employee,$tier){

       $starttime = date('Y-m-d 00:00:00');
       $endtime = date('Y-m-d 23:59:59');
       $challenge_cnt = 0;
       $emp_challenge_cnt = Challenge::where('employee_id',$employee->id)->where('status','1')->where('updated_at','>=',$starttime)->where('updated_at','<=',$endtime)->get()->count();

       if($emp_challenge_cnt >= $tier['challenges'])
         return $challenge_cnt=1;
       else
          return $challenge_cnt;
    }


   //get the count of validates by employee with sub category
    public function getValidatesCount($employee,$tier){
       $starttime = date('Y-m-d 00:00:00');
       $endtime = date('Y-m-d 23:59:59');
       $validate_cnt = 0;
       $emp_validate_cnt = Validations::where('employee_id',$employee->id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get()->count();

      // if($emp_validate_cnt >= $tier['validates'])
      if($emp_validate_cnt >= $tier['tier'])
         return $validate_cnt=1;
       else
          return $validate_cnt;
    }

//this function is for getting the rank from company
   public function get_Employee_Rank($employeeid){
       $cur_emp = Employee::find($employeeid);
       $cur_emp->point = round($this->countPoint($employeeid));
       $com_employees = Employee::where('company_id',$cur_emp->company_id)->where('industry',$cur_emp->industry)->where('access_level','<=',$cur_emp->access_level)->where('is_deleted','0')->get();
       $class = 1;
       $emp_data = array();
       if(!empty($com_employees)){
           foreach($com_employees as $emp){
               $emp->point = round($this->countPoint($emp->id));
               $emp_data[] = $emp;
           }

           usort($emp_data, function($a, $b) {
                return $b['point'] - $a['point'] ;
            });
           foreach($emp_data as $temp)
           {
               if($temp['id'] == $cur_emp->id)
                 break;
               $class+=1;
           }
       }
       return $class;
   }

   public function get_EmployeeInfo_fromID($id){

   $employee = Employee::where('id',$id)->first();
   $Employee_data = array();
   $company_instance = $this->get_CompanyInfo_fromID($employee->company_id);
   $industry_instance = $this->get_IndustryInfo_fromID($employee->industry);

   if($employee!= '' && isset($employee)){
       $Employee_data['id'] = $employee->id;
       $Employee_data['full_name'] = $employee->full_name;
       $Employee_data['phone_number'] = $employee->phone_number;
       $Employee_data['email'] = $employee->email;
       $Employee_data['website'] = $employee->website;
       $Employee_data['access_level'] = $employee->access_level;
//       $Employee_data['image'] = $employee->image != '' ? Storage::disk('s3')->url('/images/employee/').$employee->image : '';
       $Employee_data['image'] = $employee->image;
       $Employee_data['industry'] = $industry_instance  ;
       $Employee_data['company_instance'] = $company_instance;
       $created_at = $employee->created_at->format('Y-m-d H:i:s');
       $Employee_data['created_at'] = $created_at;
       $updated_at = $employee->updated_at->format('Y-m-d H:i:s');
       $Employee_data['updated_at'] = $updated_at;
       $Employee_data['myplan'] = $employee->myplan;
       $Employee_data['past_jobs'] = $employee->past_jobs;
       $Employee_data['references'] = $employee->emp_reference;
       $Employee_data['myobjective'] = $employee->myobjective;
       $Employee_data['point'] = 0;
       $point = round($this->countPoint($employee->id));
       $Employee_data['point'] = $point;
       $Employee_data['rank'] = $this->get_Employee_Rank($employee->id);
       $Employee_data['userType'] = $employee->userType;
       $Employee_data['business_url'] = $employee->business_url;
       $Employee_data['independent_category_id'] = $employee->independent_category_id;
     }
   return $Employee_data;
  }

   public function get_CompanyInfo_fromID($id){
    $company = Users::where('id',$id)->first();
    $company_data = array();
    if($company != '' && isset($company)){
        $company_data['id'] = $company->id;
        $company_data['name'] = $company->name;
        $company_data['email'] = $company->email;
        $company_data['address'] = $company->address;
        $company_data['pic'] = $company->pic != '' ?
                 Storage::disk('s3')->url('/images/user/').$company->pic : '';
        $company_data['website_url'] = $company->website_url;
        $company_data['access_code'] = $company->access_code;

        $created_at = $company->created_at->format('Y-m-d H:i:s');
        $company_data['created_at'] = $created_at;
        $updated_at = $company->updated_at->format('Y-m-d H:i:s');
        $company_data['updated_at'] = $updated_at;

    }
    return $company_data;
}
public function get_IndustryInfo_fromID($id){

    $industry = Industry::where('id',$id)->first();
    $industry_data = array();
    if($industry != '' && isset($industry)) {
         $industry_data['id'] = $industry->id;
         $industry_data['industry_name'] = $industry->industry_name;
         $industry_data['latitude'] = $industry->latitude;
         $industry_data['longitude'] = $industry->longitude;
         $industry_data['location']  = $industry->location;
         $industry_data['company_id'] = $industry->company_id;

         $created_at = $industry->created_at->format('Y-m-d H:i:s');
         $industry_data['created_at'] = $created_at;
         $updated_at = $industry->updated_at->format('Y-m-d H:i:s');
         $industry_data['updated_at'] = $updated_at;
         return $industry_data;
    } else {
         return null;
    }


}

public function getSubCategories($str){
        $subcat_str = $str;
        $subcat_arr = explode(",",$subcat_str);
        $sub_data = array();
        if($subcat_str!="" && isset($subcat_str)&& $subcat_arr!='0'){
             foreach ($subcat_arr as $item1) {
                $subcat_data = Subcategory::select('id', 'category_id', 'subcategory_name', 'user_access_level', 'status', 'created_at', 'updated_at')->where('id',$item1)->get()->first();
                $sub_data[] = $subcat_data;
             }
         }

        return $sub_data;
}

public function getMainSubCategories($str){
        $subcat_str = $str;
        $subcat_arr = explode(",",$subcat_str);
        $sub_data = array();
        if($subcat_str!="" && isset($subcat_str)&& $subcat_arr!='0'){
             foreach ($subcat_arr as $item1) {
                $subcat_data = Categories::where('id',$item1)->get()->first();
                $sub_data[] = $subcat_data;
             }
         }

        return $sub_data;
}

public function get_ChallengeInfo_fromID($id){

    $challenge = Challenge::where('id',$id)->first();
    $challenge_data = array();
    if($challenge!='' && isset($challenge)){
    $company_instance = $this->get_CompanyInfo_fromID($challenge->company_id);
    $sub_categories = $this->getSubCategories($challenge->subcategory_id);
    if($challenge!= '' && isset($challenge)){
        $challenge_data['id'] = $challenge->id;
        $challenge_data['image'] = $challenge->image != '' ?
                 Storage::disk('s3')->url('/images/challenge/').$challenge->image : '';
        $challenge_data['challenge_text'] = $challenge->challenge_text;
        $challenge_data['status'] = $challenge->status;
        $challenge_data['point'] = intval($challenge->point);

        $created_at = $challenge->created_at->format('Y-m-d H:i:s');
        $challenge_data['created_at'] = $created_at;
        $updated_at = $challenge->updated_at->format('Y-m-d H:i:s');
        $challenge_data['updated_at'] = $updated_at;

        $challenge_data['company_id'] = $challenge->company_id;
        $challenge_data['category_id'] = $challenge->category_id;
        $challenge_data['sub_categories'] = $sub_categories;
        $challenge_data['sent_in'] = $challenge->sent_in;
        $challenge_data['sendto_region'] = $challenge->sendto_region;
        $challenge_data['end_on'] = $challenge->end_on;

        $challenge_data['preset_type'] = $challenge->preset_type;
        $challenge_data['type'] = $challenge->type;
        $challenge_data['is_active'] = $challenge->is_active;
        $challenge_data['employee_id'] = $challenge->employee_id;

    }
    }
    return $challenge_data;
}

public function get_DuelInfo_fromID($id){
    $duel = Duels::where('id',$id)->first();
    $duel_data = array();
    if($duel!=''&&isset($duel)){
      $sender = $this->get_EmployeeInfo_fromID($duel->sender);
      $receiver = $this->get_EmployeeInfo_fromID($duel->receiver);
      $challenge = $this->get_ChallengeInfo_fromID($duel->challenge_id);
        if($duel != '' && isset($duel)){
            $duel_data['id'] = $duel->id;
            $duel_data['sender'] = $sender;
            $duel_data['receiver'] = $receiver;
            $duel_data['challenge'] = $challenge;
            $duel_data['status'] = $duel->status;
            $created_at = $duel->created_at->format('Y-m-d H:i:s');
            $duel_data['created_at'] = $created_at;
            $updated_at = $duel->updated_at->format('Y-m-d H:i:s');
            $duel_data['updated_at'] = $updated_at;
            $duel_data['point'] = $duel->point;
            $duel_data['expiry_date'] = $duel->expiry_date;
        }
    }
    return $duel_data;
}

public function get_BuildInfo_fromID($id){

     $build = Builds::where('id',$id)->first();
     $Build_data = array();

     $employee =  $this->get_EmployeeInfo_fromID($build->employee_id);
     $challenge = $this->get_ChallengeInfo_fromID($build->challenge_id);
     $duel_request = $this->get_DuelInfo_fromID($build->duel_id);
     $sub_categories = $this->getSubCategories($build->subcategory);
     if($build != '' && isset($build)){

         $Build_data['id'] = $build->id;
         $Build_data['image'] =
               $build->image != '' ? Storage::disk('s3')->url('/images/build/').$build->image : '';
         $Build_data['build_text'] = $build->build_text;
         $Build_data['status'] = $build->status;
         $Build_data['employee'] = $employee;
         $created_at = $build->created_at->format('Y-m-d');

         $Build_data['created_at'] = $created_at;
         $updated_at = $build->updated_at->format('Y-m-d');
         $Build_data['updated_at'] = $updated_at;

         $Build_data['company_id'] = $build->company_id;
         $Build_data['subcategory'] = $build->subcategory;
         if(!empty($challenge))
            $Build_data['challenge'] = $challenge;
         $Build_data['sub_categories'] = $sub_categories;
         if(!empty($duel_request))
            $Build_data['duel_request'] = $duel_request;
     }
     return $Build_data;
}

    /* This API use For Company And Admin Login */
    public function getWinDuelCount($employee){
     //duel win count
        $WinChallenges = Challenge::where('status','1')->where('employee_id',$employee->id)->get();
        $LoseChallenges = Challenge::where('status','0')->where('employee_id',$employee->id)->get();
        $duelcount = 0 ;
        $win_duels = array();
        if(!empty($WinChallenges)){
           foreach($WinChallenges as $winchal){
                $duels = Duels::where('challenge_id',$winchal->id)->get();

                if(!empty($duels)){
                   foreach($duels as $duel){

                      if($duel->sender == $employee->id){

                            $win_duels[] = $duel;


                       }

                   }
                }
           }

          foreach($LoseChallenges as $losechal){
                $duels = Duels::where('challenge_id',$losechal->id)->get();

                if(!empty($duels)){
                   foreach($duels as $duel){
                      if($duel->receiver == $employee->id){
                            $win_duels[] = $duel;

                       }

                   }
                }
           }

        }
        $win_duels =  array_unique($win_duels);
        $duelcount = sizeof($win_duels);
        return $duelcount;
   }

  public function makePDFForMobile(Request $request) {

    $encrypted_token = $request->encrypted_token;
    $authToken = DB::select(
      'select * from (SELECT *,MD5(token) as encrypted_token FROM `auth_tokens`) a '
      .  'where encrypted_token = "' . $encrypted_token. '"'
    );
    $employee_id = 0;
    if (count($authToken) >= 1)
      $employee_id = $authToken[0]->user_id;

    // return redirect()->action('EmployeePortfolioController@index',['id' => 115]);
    if($employee_id == 0){
      echo json_encode(array('status'=>false,'message'=>'No Employee Found'));die;
    } else {
      $employee = Employee::find($employee_id);

      $requesturl = url('/employeeportfolio').'/'.$employee_id.'/'.\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y').'/'.\Carbon\Carbon::today()->format('m-d-Y');

      if (intval($employee->company_id) == intval(getenv('PUBLIC_COMPANY_ID'))) {
        $requesturl = url('/employeeportfolioIndependent').'/'.$employee_id.'/'.\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y').'/'.\Carbon\Carbon::today()->format('m-d-Y');
      }

      $path = 'images/resumes/';
      $imageName = $employee_id.'_resume.png';

      $accesskey = '0f50be0856cbbdacb9d2126d13b0f590';
      $file = file_get_contents('http://api.screenshotlayer.com/api/capture?access_key='.$accesskey.'&url='.$requesturl.'&viewport=1200x768&fullpage=1&user_agent=HHeadlessChrome');
      if (intval($employee->company_id) == intval(getenv('PUBLIC_COMPANY_ID'))) {
        $file = file_get_contents('http://api.screenshotlayer.com/api/capture?access_key='.$accesskey.'&url='.$requesturl.'&viewport=425x750&fullpage=1&user_agent=HHeadlessChrome');
      }

      Storage::disk("s3")->put($path . $imageName,$file, "public");
      $file = Storage::disk("s3")->url('images/resumes/').$imageName;

      $pdfname = $employee_id.'_resume.pdf';

      $imagedata = new Imagick($file);

      $imagedata->setImageFormat('pdf');

      Storage::disk("s3")->put($path .$pdfname,$imagedata, "public");
      $pdfurl = Storage::disk("s3")->url($path.$pdfname);

      $data = array(
        'pdfurl'=>$pdfurl
      );

      echo json_encode(array('status'=>true,'data'=>$data));die;
    }
  }

  public function getApprovedChallengeCount($employee){

      $company_chal_count = 0 ;
      $appr_chal_build = Builds::where('challenge_id','!=',0)->where('employee_id',$employee->id)->where('status','1')->where('company_id',$employee->company_id)->get();
      $companychal = array();
      if(!empty($appr_chal_build)){
          foreach($appr_chal_build as $build){
              $chal = Challenge::find($build->challenge_id);
              if(!empty($chal)){
                  if($chal->preset_type == '1'){
                      $companychal[] = $chal;
                  }
              }

          }
      }
      //$companychal = array_unique($companychal);
      $company_chal_count = sizeof($companychal);
      return $company_chal_count;
  }

  public function getTierModel($employee){
    $emp_tiers = TierList::where('access_level',$employee->access_level)->orderBy('tier','asc')->get();
    return $emp_tiers;
  }

  public function getMobilePortfolio($id) {
    // $validator = Validator::make($request->all(), [
    //     'id' => 'required',
    // ]);

    // if ($validator->fails()) {
    //     return response()->json(['error' => $validator->errors()], 422);
    // }

    $employee_id = $id;

    $employee = Employee::find($employee_id);

    return redirect()->action(
    'EmployeePortfolioController@dateindex',
    [
        'id' => $employee_id,
        'startdate'=>\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y'),
        'enddate'=>\Carbon\Carbon::today()->format('m-d-Y')
    ]
    );
  }


  public function goMobilePortfolio(Request $request){

    $encrypted_token = $request->encrypted_token;
    $authToken = DB::select(
      'select * from (SELECT *,MD5(token) as encrypted_token FROM `auth_tokens`) a '
      .  'where encrypted_token = "' . $encrypted_token. '"'
    );
    $employee_id = 0;
    if (count($authToken) >= 1) {
      $employee_id = $authToken[0]->user_id;
    }

    // return redirect()->action('EmployeePortfolioController@index',['id' => 115]);

    if($employee_id == 0){
      echo json_encode(array('status'=>false,'message'=>'No Employee Found'));die;
    } else {
      $employee = Employee::find($employee_id);
      return redirect()->action(
        'EmployeePortfolioController@dateindex',
        [
          'id' => $employee_id,
          'startdate'=>\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y'),
          'enddate'=>\Carbon\Carbon::today()->format('m-d-Y')
        ]
      );
    }
  }

  public function goMobilePortfolioIndependent(Request $request){

    $encrypted_token = $request->encrypted_token;
    $authToken = DB::select(
      'select * from (SELECT *,MD5(token) as encrypted_token FROM `auth_tokens`) a '
      .  'where encrypted_token = "' . $encrypted_token. '"'
    );
    $employee_id = 0;
    if (count($authToken) >= 1) {
      $employee_id = $authToken[0]->user_id;
    }

    // return redirect()->action('EmployeePortfolioController@index',['id' => 115]);

    if($employee_id == 0){
      echo json_encode(array('status'=>false,'message'=>'No Employee Found'));die;
    } else {
      $employee = Employee::find($employee_id);
      return redirect()->action(
        'EmployeePortfolioController@dateindexIndependent',
        [
          'id' => $employee_id,
          'startdate'=>\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y'),
          'enddate'=>\Carbon\Carbon::today()->format('m-d-Y')
        ]
      );
    }
  }

  public function share(Request $request){


      $employee_id = $request->id;


    // return redirect()->action('EmployeePortfolioController@index',['id' => 115]);

    if($employee_id == 0){
      echo json_encode(array('status'=>false,'message'=>'No Employee Found'));die;
    } else {
      $employee = Employee::find($employee_id);
      return redirect()->action(
        'EmployeePortfolioController@dateindexIndependent',
        [
          'id' => $employee_id,
          'startdate'=>\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y'),
          'enddate'=>\Carbon\Carbon::today()->format('m-d-Y'),
            'share' => true,
        ]
      );
    }
  }

  public function shareWebViewURLForIndependent(Request $request){

    $encrypted_token = $request->encrypted_token;
    $authToken = DB::select(
      'select * from (SELECT *,MD5(token) as encrypted_token FROM `auth_tokens`) a '
      .  'where encrypted_token = "' . $encrypted_token. '"'
    );
    $employee_id = 0;
    if (count($authToken) >= 1) {
      $employee_id = $authToken[0]->user_id;
    }

    // return redirect()->action('EmployeePortfolioController@index',['id' => 115]);

    if($employee_id == 0){
      echo json_encode(array('status'=>false,'message'=>'No Employee Found'));die;
    } else {
      $employee = Employee::find($employee_id);
      return redirect()->action(
        'EmployeePortfolioController@dateindexIndependent',
        [
          'id' => $employee_id,
          'startdate'=>\Carbon\Carbon::parse($employee->created_at)->format('m-d-Y'),
          'enddate'=>\Carbon\Carbon::today()->format('m-d-Y')
        ]
      );
    }
  }






  public function getApprovedBuildCntwithMainCat($employee){

     //get main category action
        $categories = Categories::where('company_id',$employee->company_id)->get();

        $builds = Builds::where('employee_id',$employee->id)->where('status','1')->get();
        //get sub-category
        foreach($categories as $item){

          $subcategories = Subcategory::where('category_id',$item->id)->get();
          $appr_build_cnt = 0;

            foreach($builds as $builditem){

                $subcat_str = $builditem->subcategory;
                $subcat_arr = explode(',',$subcat_str);

                foreach($subcat_arr as $buildsub){

                    foreach($subcategories as $subitem){

                        if($subitem->id == $buildsub){
                            $appr_build_cnt++;
                        }
                    }
                }
            }
          $item->buildcount = $appr_build_cnt;
        }

        return $categories;
  }

 public function getTierCount($employee){

    $emp_id = $employee->id;
    $tier1_count = 0;
    $tier2_count = 0;
    $tier3_count = 0;

    $employee_tiers = EmployeeTier::where('employee_id',$emp_id)->get();
    if(!empty($employee_tiers)){
        foreach($employee_tiers as $item){
            $tier = TierList::where('id',$item->tier_id)->first();
            switch($tier->tier){
                case 1:
                    $tier1_count++;
                    break;
                case 2:
                    $tier2_count++;
                    break;
                case 3:
                    $tier3_count++;
                    break;
            }

        }
    }
    $data = array(
        'tier1'=>$tier1_count,
        'tier2'=>$tier2_count,
        'tier3'=>$tier3_count
    );
    return $data;
 }

  public function getProfileDetails(Request $request){

       $emp_id = $request['employee_id'];

       $employee = Employee::find($emp_id);

       $winduel_count  = $this->getWinDuelCount($employee);
       $apprchal_count  = $this->getApprovedChallengeCount($employee);

       $emp_tiers = $this->getTierCount($employee);
       $maincat = $this->getApprovedBuildCntwithMainCat($employee);
       $duel_lists =array();// $this->getFourDuels($employee);

       $data = array(
         'winduel_cnt' => $winduel_count,
         'appr_chal_cnt' =>  $apprchal_count,
         'tiers' =>  $emp_tiers,
         'maincats' => $maincat,
         'duel_lists'=>$duel_lists
       );

        echo json_encode(array('status'=>true,'data'=>$data));die;
  }

  public function getRestTierModel($emp_id){

    $starttime = date('Y-m-d 00:00:00');
    $endtime  = date('Y-m-d 23:59:59');

    $employee = Employee::find($emp_id);

    $employee_tiers = EmployeeTier::where('employee_id',$emp_id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get();

    $uploads = Builds::where('employee_id',$emp_id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get();
    $subcat = array();
    foreach($uploads as $build){
        $subcats = explode(',',$build->subcategory);
        foreach($subcats as $cat){
            $subcat[] = $cat;
        }
    }

    $emp_uploads = sizeof($subcat);

    $emp_validates = Validations::where('employee_id',$emp_id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get()->count();
    $emp_challenges = Challenge::where('employee_id',$emp_id)->where('status','1')->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get()->count();

    foreach($employee_tiers as $item){

        $tier = TierList::where('id',$item->tier_id)->get()->first();

        if(!empty($tier)){

            $emp_uploads -= $tier->uploads;
            $emp_validates -= $tier->tier;
            $emp_challenges -= $tier->challenges;
        }
    }
     $TierList = array(
        'categories'=>$emp_uploads,
        'challenges'=>$emp_challenges,
        'validates'=>$emp_validates,
        'points'=>0
    );

    return $TierList;

  }
  //This API is for getting the content of the each employee's total completed tier model//uploads, challenges,validates, points
  public function getEmployeeTierModel($emp_id){

    $employee = Employee::find($emp_id);
    $TierList = array(
        'uploads'=>0,
        'challenges'=>0,
        'validates'=>0,
        'points'=>0
    );

    $Tier1 = $TierList;
    $Tier2 = $TierList;
    $Tier3 = $TierList;

    $employee_tiers = EmployeeTier::where('employee_id',$emp_id)->get();

    if(!empty($employee_tiers)){
        foreach($employee_tiers as $item){
            $tier = TierList::where('id',$item->tier_id)->first();
            switch($tier->tier){
                case 1:
                    $Tier1['uploads'] += $tier->uploads;
                    $Tier1['challenges'] += $tier->challenges;
                    $Tier1['validates'] += $tier->validates;
                    $Tier1['points'] += $tier->points;
                    break;
                case 2:
                    $Tier2['uploads'] += $tier->uploads;
                    $Tier2['challenges'] += $tier->challenges;
                    $Tier2['validates'] += $tier->validates;
                    $Tier2['points'] += $tier->points;
                    break;
                case 3:
                    $Tier3['uploads'] += $tier->uploads;
                    $Tier3['challenges'] += $tier->challenges;
                    $Tier3['validates'] += $tier->validates;
                    $Tier3['points'] += $tier->points;
                    break;
            }

        }
    }

    $data = array(
        'tier1'=>$Tier1,
        'tier2'=>$Tier2,
        'tier3'=>$Tier3
    );

    return $data;
  }

public function getEmployeeTodaySubcategoryCount($emp_id){
    $result = 0 ;
    $starttime = date('Y-m-d 00:00:00');
    $endtime  = date('Y-m-d 23:59:59');
    $builds = Builds::where('employee_id',$emp_id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get();
    foreach($builds as $item){
        $subcat = $item->subcategory;
        $subcat = explode(',',$subcat);
        $result += sizeof($subcat);
    }
    return $result;
}
public function getEmployeeTodayTierModel($emp_id){

    $starttime = date('Y-m-d 00:00:00');
    $endtime  = date('Y-m-d 23:59:59');

    $employee = Employee::find($emp_id);
    $Tier = array(
        'categories'=>0,
        'challenges'=>0,
        'validates'=>0,
        'points'=>0,
        'status'=>0
    );

    $Tier1 = $Tier;
    $TierList = array();
    $employee_tiers = EmployeeTier::where('employee_id',$emp_id)->where('created_at','>=',$starttime)->where('created_at','<=',$endtime)->get();

    $tier_ids = array();
    foreach($employee_tiers as $emp_tier){
        $tier_ids[] = $emp_tier->tier_id;
    }


    $tiers = TierList::where('access_level',$employee->access_level)->get();
    $tiers_count =  TierList::where('access_level',$employee->access_level)->get()->count();
    $k = 0 ;
    if($tiers_count==3 && !empty($tiers)){

            foreach($tiers as $tier){

                    //$Tier['categories']  = $this->getEmployeeTodaySubcategoryCount($emp_id);
                    $Tier['categories']  = intval($tier->uploads);
                    $Tier['challenges']  = intval($tier->challenges);
                    $Tier['validates']  = intval($tier->validates);

                    if(in_array($tier->id,$tier_ids)){
                         $Tier['status'] = 1;
                         $Tier['points']  = intval($tier->points);
                    }
                    else{

                        if($k == 0 ){
                            $Tier['status'] = 0;
                            $k++;
                        }
                        else $Tier['status'] = -1;
                        $Tier['points']  = 0;


                    }
                    $TierList[]= $Tier;
            }

    }
    elseif(empty(!tiers)){
        for($i=0;$i<3;$i++){
           $TierList[] = $Tier1;
        }
    }
    elseif($tiers_count<3){

        foreach($tiers as $tier){
            $count = 0 ;
            for($j=1;$j<3;$j++){
               if($j == $tier->tier) $count++;
            }
            if($count != 0 ){
                    //$Tier1['categories']  =  $this->getEmployeeTodaySubcategoryCount($emp_id);
                    $Tier1['categories']  = intval($tier->uploads);
                    $Tier1['challenges']  = intval($tier->challenges);
                    $Tier1['validates']  = intval($tier->validates);
                    if(in_array($tier->id,$tier_ids)){
                        $Tier1['status'] = 1;
                        $Tier1['points']  = intval($tier->points);
                    }
                    else{
                        if($k == 0 ){
                           $Tier1['status'] = 0;
                           $k++;
                        }
                        else{
                            $Tier1['status'] = -1;
                        }
                        $Tier1['points']  = 0;
                    }
                    $TierList[]= $Tier1;
                    break;
            }
            else{
                    $TierList[] = $Tier1;
            }
        }
    }

    return $TierList;
  }


   //thsi api is for the user login
    public function userLogin(Request $request){

        $validation = Validator::make(
            array(
                'email' => $request->input( 'email' ),
                'password' => $request->input( 'password' ),
            ),
            array(
                'email' => array( 'required' ),
                'password' => array( 'required' ),
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else{
            $credentials = $request->only('email', 'password');
            $user = Users::where('email',$request->email) -> first();

            if (Auth::attempt($credentials)) {
                $data = array(
                    'user_id'=>$user->id,
                    'token'=>uniqid().uniqid().uniqid(),
                    'type'=>'c'
                );
                $userauth = Authtoken::create($data);
                $userData = array(
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'address'=>$user->address,
                    'website_url'=>$user->website_url
                );
                $jsonData = array(
                    'token'=>$userauth->token,
                    'userdata'=>$userData
                );
                echo json_encode(array('status'=>true,'data'=>$jsonData));die;
            } else {
                echo json_encode(array('status'=>false,'msg'=>'Email Not Found or Something Went Wrong'));die;
            }
        }
    }
    /* END */

    /* This API use for Email Verification */

    public function emailVerification(Request $request) {


        $validation = Validator::make(
            array(
                'email' => $request->input( 'email' )
            ),
            array(
                'email' => array( 'required' )
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else
        {


                $verificationCode = substr(number_format(time() * rand(),0,'',''),0,6);
                $toName = '';
                $toEmail = $request->email;
                $bodytext = 'Your email verification code is <span style="background-color:#eff0f1;"><b>'.$verificationCode.'</b></span>.';

                $data = ['name' => 'Uptime Email Verification',
                        'body' => $bodytext];

                Mail::send(['html' => 'email.mail'], $data, function($message) use ($toName, $toEmail)
                {
                    $message->to($toEmail, $toName)
                            ->subject('Email Verification Request.');
                    $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
                });

                if(count(Mail::failures()) > 0)
                {
                    echo json_encode(['status'=>false, 'msg' => 'email send failed']);
                    die();
                }
                else
                {
                    echo json_encode(['status'=>true, 'msg'=> $verificationCode]);
                    die();

                }



        }
    }

    public function checkEmailExistance(Request $request) {
      $validation = Validator::make(
            array(
                'email' => $request->input( 'email' )
            ),
            array(
                'email' => array( 'required' )
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else
        {
            $employee = Employee::where('email', $request->email)->first();
           if(empty( $employee))
           {
              echo json_encode(['status'=>true]);
           }
           else
           {
              echo json_encode(['status'=>false, 'msg' => 'The email is already taken. Please try with another.']);
           }
         }
    }

    /* This API use for Forgot Password */

    public function forgotPassword(Request $request) {


        $validation = Validator::make(
            array(
                'email' => $request->input( 'email' )
            ),
            array(
                'email' => array( 'required' )
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else
        {
            $employee = Employee::where('email', $request->email)->first();
           if(!empty( $employee))
           {
                $forgetToken = $employee->forget_token;
                if(empty($forgetToken))
                {
                    $forgetToken = str_random(20);//substr(number_format(time() * rand(),0,'',''),0,6);
                }
                $employee->forget_token = $forgetToken;
                $employee->save();

                $toName = $employee->full_name;
                $toEmail = $employee->email;
                // $bodytext = 'You have requested forget password kindly use this code <span style="background-color:#eff0f1;"><b>'.$forgetToken.'</b></span> to restore password.';

                $reset_link = route('resetpassword', ['token'=> $forgetToken]);
                $bodytext = 'Hi <b>' . $toName .',</b><br/>We have received your request to reset your password.<br/>Please use this link <b>'.$reset_link.'</b> to set a new password.<br/>If you did not request to reset your password, please ignore this email.';

                $data = ['name' => ucwords($employee->full_name),
                        'body' => $bodytext];

                Mail::send(['html' => 'email.mail'], $data, function($message) use ($toName, $toEmail)
                {
                    $message->to($toEmail, $toName)
                            ->subject('Forget Password Request.');
                    $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
                });

                if(count(Mail::failures()) > 0)
                {
                    echo json_encode(['status'=>false, 'msg' => 'email send failed']);
                    die();
                }
                else
                {
                    echo json_encode(['status'=>true]);
                    die();
                }

            }
            else
            {
                echo json_encode(['status'=>false, 'msg' => 'Email address not found.']);
                die();
            }
        }
    }

    /* This API use For Employee Forgetpassword To save New password */
    public function forgotPasswordSave(Request $request)
    {
        $data = $request->all();
        $rules = array('password' => 'required|confirmed',
                       'password_confirmation' => 'required',
                       'forget_code' => 'required');
        $validation = Validator::make($data,$rules);

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));
            die();
        }
        else
        {

            $forgetToken = $request->input('forget_code');
            $employee = Employee::where('forget_token', $forgetToken)->first();
            if(!empty($employee))
            {
                $password = $request->input('password');
                $password = Hash::make($password);
                $forgetTokenNew = substr(number_format(time() * rand(),0,'',''),0,6);
                $employee = Employee::where('forget_token', $forgetToken)->first();
                $employee->password = $password;
                $employee->forget_token = $forgetTokenNew;
                if($employee->save())
                {
                    $message = 'password has been changed successfully';
                    echo json_encode(array('status'=>true,'msg'=>$message));
                    die();
                }
                else
                {
                    $message = 'password has not been changed successfully';
                    echo json_encode(array('status'=>false,'msg'=>$message));
                    die();
                }
            }
            else
            {
                $message = 'You have entered invalid forget code, Please add correct code.';
                echo json_encode(array('status'=>false,'msg'=>$message));
                die();



            }




        }
        die();

    }

    /* This API use For Employee Login */

    public function employeeLogin(Request $request){
        $validation = Validator::make(
            array(
                'email' => $request->input( 'email' ),
                'password' => $request->input( 'password' )
            ),
            array(
                'email' => array( 'required' ),
                'password' => array( 'required' )
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            return response()->json(array('status'=>false,'msg'=>$errors), 400);
        }else{


            $Employee = Employee::where('email',$request->email) -> first();

            if($Employee != ''){

                if($Employee->is_deleted == '1'){
                    return response()->json(array('status'=>false,'msg'=>'Your Account is disabled or deleted.'), 403);
                }
                $checkPassword = Hash::check($request->password,$Employee->password);
                if(!$checkPassword){
                    return response()->json(array('status'=>false,'msg'=>'Please Enter Correct Password'), 400);
                }
                else{

                    $data = array(
                        'user_id'=>$Employee->id,
                        'token'=>uniqid().uniqid().uniqid(),
                        'type'=>'e'
                    );
                    $userauth = Authtoken::create($data);

                    $userData = $this->get_EmployeeInfo_fromID($Employee->id);
                    $jsonData = array(
                        'token'=>$userauth->token,
                        'encrypted_token'=>md5($userauth->token),
                        'userdata'=>$userData
                    );


                   return response()->json(array('status'=>true,'data'=>$jsonData), 200)
                    ->setEncodingOptions(JSON_FORCE_OBJECT);
                    // echo json_encode(array('status'=>true,'data'=>$jsonData));die;
                }
            }else{
                return response()->json(array('status'=>false,'msg'=>'User Not Found'), 404);
            }
        }
    }
    /* END */

    /* This API use For Employee Register */
    public function employeeRegister(Request $request){
        try {
            $validation = Validator::make(
                array(
                    'full_name' => $request->input( 'full_name' ),
                    'email' => $request->input( 'email' ),
                    'password' => $request->input( 'password' ),
                    'phone_number' => $request->input( 'phone_number' ),
                    'company_id' => $request->input( 'company_id' ),
                    'industry'=> $request->input( 'industry' ),
                    'userType'=> $request->input('userType')
                ),
                array(
                    'full_name' => array( 'required' ),
                    'email' => array( 'required','unique:employee' ),
                    'password' => array( 'required' ),
                    'phone_number' => array( 'nullable' ),
                    'company_id' => array( 'required' ),
                    'industry'=>array( 'required' ),
                    'userType'=> array( 'required' )
                )
            );
    
            $errors = '';
            if ( $validation->fails() ) {
                $errors = $validation->messages();
                $errors->toJson();
                echo json_encode(array('status'=>false,'msg'=>$errors));die;
            }else{
                $this->requestlog($request->all());
                $data = array(
                    'full_name'=>$request->full_name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                    'company_id'=>($request->company_id > 0)?$request->company_id:getenv('PUBLIC_COMPANY_ID'),
                    'industry'=>$request->industry,
                    'phone_number' => $request->phone_number,
                    'access_level'=>0,
                    'is_deleted'=>'0',
                    'is_request'=>'0',
                    'point_note'=>0,
                    'myplan'=> $request->myplan,
                    'past_jobs'=> $request->past_jobs,
                    'emp_reference'=> $request->emp_reference,
                    'myobjective'=> $request->myobjective,
                    'userType' => $request->userType,
                    'business_url' => $request->business_url,
                    'independent_category_id' => $request->independent_category_id,
                );
    
                if(isset($request->image) && $request->image != ''){
    
                    if($request->hasFile('image')){
    
                      $imageName = time().'.'.$request->image->getClientOriginalExtension();
                      $path = 'images/employee/';
                      $file = $request->file("image");
                      $image = Image::make($file);
                      $image->orientate();
                      Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                      $data['image'] = $imageName ;
                    }
                }
    
                $employee = Employee::create($data);
                $result = $this->get_EmployeeInfo_fromID($employee->id);
    
                echo json_encode(array('status'=>true,'data'=>$result));die;
            }
        } catch (\Throwable $th) {
            echo json_encode(array('status'=>false,'message'=>$th->getMessage()));die;
        }
    }
    /* END */


public function userSearch(Request $request){
	 $userDataRequest = $request['userData'];
         $emp_id= $userDataRequest['id'];
         $current_empinfo = Employee::find($emp_id);
         $keyword = $request->keyword;
         $level = 0 ;
         //$current_empinfo->access_level == 4 ? $level=3 : $level = $current_empinfo->access_level;

         $employee = Employee::where('company_id',$current_empinfo->company_id)->where('access_level','<=',$level)->where('is_deleted','0')
         		->where('industry',$current_empinfo->industry)->where('full_name','like','%'.$keyword.'%')
         		->get()->toArray();
         $result = array();
         $resultitem = array();
        if(!empty($employee)) {
           foreach($employee as $item){

             $result[] = $this->get_EmployeeInfo_fromID($item['id']);
           }
        }

        echo json_encode(array('status'=>true,'data'=>$result));die;

}
/*This API use to get the duel count by filtering (month,week)*/

/*Start*/

public function getPresetCount(Request $request){

   $userDataRequest = $request['userData'];
   $emp_id= $userDataRequest['id'];
   $emp_data = Employee::where('id',$emp_id)->first();

   $category_id = $request->category_id;

   $NewChals = array();

   if($category_id == -1){

   	   $Chal_Data = Challenge::where('preset_type','1')
                 ->where('company_id',$emp_data->company_id)
                 ->where('status','-1')
                 ->get();
     if(!empty($Chal_Data)){
            foreach($Chal_Data as $chal){
                $regions = explode(',',$chal->sendto_region);
                if($chal->type == 'employee'){
                    $empids  = explode(',',$chal->sent_in);
                    if(in_array($emp_id,$empids))
                      $NewChals[] = $chal;
                }
                else{
                    if($chal->type == 'all')
                         $NewChals[] = $chal;
                     else{
                         if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                            $NewChals[] = $chal;
                    }
                }


            }
        }
     $count = sizeof($NewChals);
   }
   else{

   	 $Chal_Data= Challenge::where('preset_type','1')
   	   	         ->where('company_id',$emp_data->company_id)
                 ->where('category_id',$category_id)
                 ->where('status','-1')
                 ->get();
       if(!empty($Chal_Data)){
            foreach($Chal_Data as $chal){
                $regions = explode(',',$chal->sendto_region);
                    if($chal->type == 'all')
                        $NewChals[] = $chal;
                else{
                    if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                    $NewChals[] = $chal;
                }
            }
        }
     $count = sizeof($NewChals);

   }

    $result = array(
      'count'=>$count
    );

    echo json_encode(array('status'=>true,'data'=>$result));die;

}
/*END*/

public function findPos($arr,$val){
  $i = 0 ;
  $pos = 0 ;
  foreach($arr as $item){

    if($item['id'] == $val){
       $pos = $i;
       break;
    }
    $i++;
  }
  return $pos;

}


public function getPresetChallenges(Request $request){

   $userDataRequest = $request['userData'];
   $emp_id= $userDataRequest['id'];
   $emp_data = Employee::where('id',$emp_id)->first();

   $results = array();
   $resultitem = array();

   $category_id = $request->category_id;

    $challenges = array();
    if($category_id == -1){
    	$Chals = Challenge::where('preset_type','1')
    		       ->where('company_id',$emp_data->company_id)
                   ->where('status','-1')
                   ->orderBy('created_at','desc')
                   ->get();
        if(!empty($Chals)){
            foreach($Chals as $chal){
                $regions = explode(',',$chal->sendto_region);
                if($chal->type == 'employee'){
                    $empids  = explode(',',$chal->sent_in);
                    if(in_array($emp_id,$empids))
                      $challenges[] = $chal;
                }
                else{
                    if($chal->type == 'all')
                        $challenges[] = $chal;
                    else{
                        if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                        $challenges[] = $chal;
                    }
                }
            }
        }
     }
     else{
       $Chals = Challenge::where('preset_type','1')
    		       ->where('company_id',$emp_data->company_id)
    		       ->where('category_id',$category_id)
                   ->where('status','-1')
                   ->orderBy('created_at','desc')
                   ->get();
      if(!empty($Chals)){
            foreach($Chals as $chal){
                $regions = explode(',',$chal->sendto_region);
                if($chal->type == 'employee'){
                    $empids  = explode(',',$chal->sent_in);
                    if(in_array($emp_id,$empids))
                      $challenges[] = $chal;
                }
                else{
                    if($chal->type == 'all')
                        $challenges[] = $chal;
                    else{
                        if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                        $challenges[] = $chal;
                    }
                }
            }
        }

     }

      if(!empty($challenges)){

	     foreach($challenges as $chal){
            $resultitem = $this->get_ChallengeInfo_fromID($chal->id);
            $results[] = $resultitem;
         }
          echo json_encode(array('status'=>true,'data'=>$results));die;
       }

       // in caset that there is no new results
       else{
          echo json_encode(array('status'=>true,'data'=>$results));die;
       }

}


/*This api  use for the timed challenge*/
 public function getTimedChallenges(Request $request){

   $userDataRequest = $request['userData'];
   $emp_id= $userDataRequest['id'];
   $emp_data = Employee::where('id',$emp_id)->first();

   $resultitem = array();
   $result = array();

   $today =   date('Y-m-d h:i:s');


   $category_id = $request->category_id;

   $challenges= array();

   if($category_id == -1){
    	$Chals= Challenge::where('preset_type','0')
    		   ->where('company_id',$emp_data->company_id)
    		       ->where('status','-1')
                  ->where('end_on','>=',$today)
                  ->where('is_active',1)
                   ->orderBy('created_at','desc')
                   ->get();

        if(!empty($Chals)){
            foreach($Chals as $chal){
                $regions = explode(',',$chal->sendto_region);
                if($chal->type == 'employee'){
                    $empids  = explode(',',$chal->sent_in);
                    if(in_array($emp_id,$empids))
                      $challenges[] = $chal;
                }
                else{
                    if($chal->type=='all')
                        $challenges[] = $chal;
                    else{
                        if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                        $challenges[] = $chal;
                    }
                }


            }
        }
    }
    else{
        $Chals= Challenge::where('preset_type','0')
    		   ->where('company_id',$emp_data->company_id)
                ->where('status','-1')
                ->where('is_active',1)
               ->where('created_at','<=',$end)
               ->where('end_on','>=',$today)
               ->orderBy('created_at','desc')
               ->get();
       if(!empty($Chals)){
            foreach($Chals as $chal){
                $regions = explode(',',$chal->sendto_region);
                if($chal->type == 'employee'){
                    $empids  = explode(',',$chal->sent_in);
                    if(in_array($emp_id,$empids))
                      $challenges[] = $chal;
                }
                else{
                    if($chal->type=='all')
                    $challenges[] = $chal;
                    else{
                        if($emp_data->access_level >= $chal->sendto_level && in_array($emp_data->industry,$regions))
                            $challenges[] = $chal;
                    }
                }
            }
        }
    }
     if(!empty($challenges)){
          foreach($challenges as $item){
            $resultitem = $this->get_ChallengeInfo_fromID($item['id']);
             $result[] = $resultitem;
          }
          echo json_encode(array('status'=>true,'data'=>$result));die;
       }

       // in caset that there is no new chals
       else{
          echo json_encode(array('status'=>true,'data'=>$result));die;
       }

}
/*end*/
public function createNewDuel(Request $request){

      $userDataRequest = $request['userData'];
      $sender = $userDataRequest['id'];


      $validation = Validator::make(
            array(
                'receiver' => $request->input( 'receiver' ),
                'challenge_id' => $request->input( 'challenge_id' ),
                 'point' => $request->input( 'point' )
            ),
            array(
                'receiver' => array( 'required' ),
                'challenge_id' => array( 'required' ),
                'point' => array( 'required' )

            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>'Incorrect Params!'));die;
        }
        else{

	      $receiver = $request->receiver;
	      $challenge_id = $request->challenge_id;
	      $chal = Challenge::find($challenge_id);
	      $point = $request->point;
	      $now = date("Y-m-d H:i:s").'';
	      $expiry_date =  date("Y-m-d H:i:s",strtotime('+12 hours',strtotime($now)));

	     // echo date('Y-m-d H:i:s');
	     // echo $expiry_date; die;

	      $data = array(
	         'sender' => $sender,
	         'receiver' => $receiver,
	         'challenge_id'=>$challenge_id,
	         'point' => $point,
	         'expiry_date'=>$expiry_date
	      );

	      $duel = Duels::create($data);
	      $senderemp = $this->get_EmployeeInfo_fromID($duel->sender);
	      $receiveremp = $this->get_EmployeeInfo_fromID($duel->receiver);
	      $challenge = $this->get_ChallengeInfo_fromID($duel->challenge_id);
	      $chal_text = $chal->challenge_text;
	      $msgdata = array('action'=>1);
          $message = $senderemp['full_name']." has challenged you a duel!";

          $this->sendpush($duel->receiver,'',$message,[],'duel request');

              $list_param = array(
                                'content_type'=>1,
                                'message' => $message,
                                'sender'=>$duel->sender,
                                'receiver'=>$duel->receiver,
                                'receiver_type'=>3
              );
              $result = array(
                 'sender' => $senderemp,
    	         'receiver' => $receiveremp,
    	         'challenge'=>$challenge,
    	         'point' => $point,
    	         'expiry_date'=>$expiry_date
              );
             Notification::create($list_param);

	      echo json_encode(array('status'=>true,'data'=>$result));die;
      }
}

public function acceptDuel(Request $request){

    $id = $request->id;
    $duel = Duels::find($id);

    if($duel){
         $data = array(
            'status' => '1'
         );

        Duels::find($id)->update($data);

        $challege_id = $duel->challenge_id;
        $chal = Challenge::find($challege_id);
        $chal_text = $chal->challenge_text;
        $message = "Your Duel has been Accepted!";
        $msgdata = array('action'=>2);
        $this->sendpush($duel->sender,'Duel Accepted',$message,$msgdata,'duel accept');

        $list_param = array(
                  'content_type'=>1,
                  'message' => $message,
                  'sender'=>$duel->receiver,
                  'receiver'=>$duel->sender,
                  'receiver_type'=>3
              );

         Notification::create($list_param);
        echo json_encode(array('status'=>true,'msg'=>'Duel Accepted'));die;
    }
    else{
        echo json_encode(array('status'=>true,'msg'=>'No Duel Found'));die;
    }
}

public function completeDuel(Request $request){

    $id = $request->id;
    $duel = Duels::find($id);

    if($duel){
         $data = array(
            'status' => '2'
         );

        Duels::find($id)->update($data);
    /*    $challege_id = $duel->challenge_id;
        $chal = Challenge::find($challege_id);

        $chal_text = $chal->challenge_text;
        $message = "Duel(".$chal_text.") has been completed by(".$duel->receiver.")";
        $msgdata = array('action'=>3);
        $this->sendpush($duel->sender,'',$message,$msgdata,'duel complete');
     */
        echo json_encode(array('status'=>true,'msg'=>'Duel Completed'));die;
    }
    else{
        echo json_encode(array('status'=>true,'msg'=>'No Duel Found'));die;
    }
}

public function rejectDuel(Request $request){

    $userDataRequest = $request['userData'];
    $employee_id = $userDataRequest['id'];

    $id = $request->id;
    $duel = Duels::find($id);
    if($duel){
         $data = array(
            'status' => '0'
         );

        Duels::find($id)->update($data);
        $challege_id = $duel->challenge_id;
        $chal = Challenge::find($challege_id);

        $chal_text = $chal->challenge_text;
        $message = "Duel(".$chal_text.") has been rejected by(".$duel->receiver.")";
       $msgdata = array('action'=>3);
        $this->sendpush($duel->sender,'',$message,$msgdata,'duel reject');

	$list_param = array(
                  'content_type'=>1,
                  'message' => $message,
                  'sender'=>$duel->sender,
                  'receiver'=>$duel->receiver,
                  'receiver_type'=>3
              );

	Notification::create($list_param);
        echo json_encode(array('status'=>true,'msg'=>'Duel Rejected'));die;
    }
    else{
        echo json_encode(array('status'=>true,'msg'=>'No Duel Found'));die;
    }
}

public function getAllCompletedDuels(Request $request){

    $userDataRequest = $request['userData'];
    $employee_id = $userDataRequest['id'];

    $duels = array();
    $resultitem = array();
    //this week
    $monday = date("Y-m-d 00:00:00",strtotime("Monday this week"));
    $sunday = date("Y-m-d 23:59:59",strtotime("Sunday this week"));

    //this month
    $firstday = date('Y-m-01 00:00:00');
    $lastday =  date('Y-m-t 23:59:59');


    $filter = $request->filter;
    $index = $request->index;
    $category_id = $request->category_id;

    if(empty($filter)){
      $filter  = 'month';
    }
   $start = date('Y-m-d');
   $end = date('Y-m-d');

   if(empty($filter)){
      $filter  = 'month';
   }

   switch($filter){
     case 'month':
       $start = $firstday;
       $end = $lastday;
       break;
     case 'week':
       $start = $monday;
       $end = $sunday;
       break;
     default:
       $start = $firstday;
       $end = $lastday;

   }

 $DuelRequest = Duels::where('status','2')->where('receiver',$employee_id)
                   ->where('created_at','>=',$start)
                   ->where('created_at','<=',$end)
                   ->orderBy('created_at','desc')
		   ->get()->toArray();

 $DuelRequest1 = Duels::where('status','2')->where('sender',$employee_id)
                   ->where('created_at','>=',$start)
                   ->where('created_at','<=',$end)
                   ->orderBy('created_at','desc')
		   ->get()->toArray();

     $DuelRequest2 = $this->addArray($DuelRequest,$DuelRequest1);

      $DuelRequest = array();
   foreach($DuelRequest2 as $item){
       $chal = Challenge::find($item['challenge_id']);
       if(!empty($chal)){
           $DuelRequest[] = $item;
       }
   }

       if(!empty($DuelRequest)){

         if($index != 0 ){

	         $key = $this->findPos($DeulRequest,$index);
	          $m = 1;
	         for($j = $key+1;$j< sizeof($DuelRequest);$j++){

	            if($m<=20){
	               $template = $DuelRequest[$j];
	               $resultitem =  $this->get_DuelInfo_fromID($template ['id']);
	               if($category_id == -1){

		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;

	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	            }

	         }
	 }

	 if($index == 0){
	         $m = 1;
	         foreach($DuelRequest as $item ){
	            if($m<=20){

	                $resultitem = $this->get_DuelInfo_fromID($item['id']);

	               if($category_id == -1){
		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;
	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	            }

	         }
	 }
          echo json_encode(array('status'=>true,'data'=>$duels));die;

      }

       // in caset that there is no new duels
      else{
          echo json_encode(array('status'=>false,'data'=>$duels));die;
      }
}

public function getBadge(Request $request){


   $userDataRequest = $request['userData'];
   $employee_id = $userDataRequest['id'];
   $firstday = date('Y-m-01 00:00:00');
   $lastday =  date('Y-m-t 23:59:59');

   $duelrequest_count= 0;

   $duelrequests =  Duels::where('status','-1')->where('receiver',$employee_id)->where('created_at','>=',$firstday)->where('created_at','<=',$lastday)->get()->toArray();

   if(!empty($duelrequests)){

       foreach($duelrequests  as $item ){

           if(date('Y-m-d H:i:s') < $item['expiry_date']){

                $chal_data = Challenge::where('id',$item ['challenge_id'])->first();
    	        //if(!isset($chal_data))
    	            $duelrequest_count += 1;
           }
       }
       //$today = date('Y/m/d A H:i:s');
   }

   $data = array(
    'duelrequest_count'=>$duelrequest_count
   );
   echo json_encode(array('status'=>true,'data'=>$data));die;

}

public function getDuelRequests(Request $request){

    $userDataRequest = $request['userData'];
    $employee_id = $userDataRequest['id'];

    $duels = array();
    $resultitem = array();
    //this week
    $monday = date("Y-m-d 00:00:00",strtotime("Monday this week"));
    $sunday = date("Y-m-d 23:59:59",strtotime("Sunday this week"));

    //this month
    $firstday = date('Y-m-01 00:00:00');
    $lastday =  date('Y-m-t 23:59:59');

    $filter = $request->filter;
    $index = $request->index;

    $category_id  = $request->category_id;


    if(empty($filter)){
      $filter  = 'month';
    }
   $start = date('Y-m-d');
   $end = date('Y-m-d');

   if(empty($filter)){
      $filter  = 'month';
   }

   switch($filter){
     case 'month':
       $start = $firstday;
       $end = $lastday;
       break;
     case 'week':
       $start = $monday;
       $end = $sunday;
       break;
     default:
       $start = $firstday;
       $end = $lastday;

   }
    $RequestsDuel = Duels::where('status','-1')
		    ->where('receiver',$employee_id)
		    ->where('created_at','>=',$start)
		    ->where('created_at','<=',$end)
		    ->orderBy('created_at','desc')
		    ->get()->toArray();

   $DuelRequests = array();
   foreach($RequestsDuel as $item){
       $chal = Challenge::find($item['challenge_id']);
       if(!empty($chal)){
           $DuelRequests[] = $item;
       }
   }
       $DuelRequest = array();

       if(!empty($DuelRequests)){
         foreach($DuelRequests as $duelreq){

	            if(date('Y-m-d H:i:s') < $duelreq['expiry_date']){
	                $DuelRequest[] = $duelreq;
	            }
         }
          if($index != 0 ){
	         $key = $this->findPos($DuelRequest,$index);
	          $m = 1;
	         for($j = $key+1;$j< sizeof($DuelRequest);$j++){

	            if($m<=20){

	               $template = $DuelRequest[$j];
	               $resultitem = $this->get_DuelInfo_fromID($template['id']);
	               if($category_id == -1){

		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;

	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	              }
	            }

	  }

	 if($index == 0){

	          $m = 1;
	         foreach($DuelRequest as $item ){
	            if($m<=20){
	               $resultitem = $this->get_DuelInfo_fromID($item ['id']);
	               if($category_id == -1){

		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;

	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	               }
	            }

	 }
	  echo json_encode(array('status'=>true,'data'=>$duels));die;
       }
       // in caset that there is no new duels
      else{
          echo json_encode(array('status'=>true,'data'=>$duels));die;
      }


}


public function getInprogressDuels(Request $request){

    $userDataRequest = $request['userData'];
    $employee_id = $userDataRequest['id'];

    $duelrequest = array();
    $resultitem = array();
    $duels = array();
    //this week
    $monday = date("Y-m-d 00:00:00",strtotime("Monday this week"));
    $sunday = date("Y-m-d 23:59:59",strtotime("Sunday this week"));

    //this month
    $firstday = date('Y-m-01 00:00:00');
    $lastday =  date('Y-m-t 23:59:59');

    $filter = $request->filter;
    $index = $request->index;
    $category_id = $request->category_id;
    if(empty($filter)){
      $filter  = 'month';
    }
   $start = date('Y-m-d');
   $end = date('Y-m-d');

   if(empty($filter)){
      $filter  = 'month';
   }

   switch($filter){
     case 'month':
       $start = $firstday;
       $end = $lastday;
       break;
     case 'week':
       $start = $monday;
       $end = $sunday;
       break;
     default:
       $start = $firstday;
       $end = $lastday;

   }


      $DuelRequest = Duels::where('status','1')->where('receiver',$employee_id)
                   ->where('created_at','>=',$start)
                   ->where('created_at','<=',$end)
                   ->orderBy('created_at','desc')
		   ->get()->toArray();

      $DuelRequest1 = Duels::where('status','1')->where('sender',$employee_id)
                   ->where('created_at','>=',$start)
                   ->where('created_at','<=',$end)
                   ->orderBy('created_at','desc')
		   ->get()->toArray();

     $DuelRequest2 = $this->addArray($DuelRequest,$DuelRequest1);


   $DuelRequest = array();
   foreach($DuelRequest2 as $item){
       $chal = Challenge::find($item['challenge_id']);
       if(!empty($chal)){
           $DuelRequest[] = $item;
       }
   }

  // echo json_encode($DuelRequest);die;

       if(!empty($DuelRequest)){

	 if($index != 0 ){
	         $key = $this->findPos($DeulRequest,$index);
	          $m = 1;
	         for($j = $key+1;$j< sizeof($DuelRequest);$j++){

	            if($m<=20){

	               $template = $DuelRequest[$j];
	               $resultitem = $this->get_DuelInfo_fromID($template['id']);
	               if($category_id == -1){

		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;

	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	            }

	         }
	 }

	 if($index == 0){

	          $m = 1;
	         foreach($DuelRequest as $item ){
	            if($m<=20){
	               $resultitem = $this->get_DuelInfo_fromID($item['id']);
	                 if($category_id == -1){

		            $duels[] = $resultitem;
		            if($m==20) break;
		            $m++;

	               }
	               else{
	                     if($chal_data->category_id == $category_id){
		                    $duels[] = $resultitem;
		                    if($m==20) break;
		                    $m++;
		               }
	               }
	            }

	         }
	 }

          echo json_encode(array('status'=>true,'data'=>$duels));die;

       }
       // in caset that there is no new duels
       else{
          echo json_encode(array('status'=>true,'data'=>$duels));die;
       }

}




/*This API use for get the ReadItem Count */
public function addVisit(Request $request){

      $e_id = $request['employee_id'];
      $r_id = $request['readitem_id'];

      $data = array(
        'employee_id' => $e_id,
        'readitem_id' => $r_id
      );

      ReadItem::create($data);

}

public function getReadItems($current_emp){

    $result = array();
    $emp_arr = array();
    if($current_emp){

        $currentuser_companyid = $current_emp['company_id'];
       // $totalemployee = Employee::where('company_id',$currentuser_companyid )->where('is_deleted','0')->count();
        $uploadData = Upload::where('sendto_level','<=',$current_emp->access_level)->where('company_id',$currentuser_companyid)->orderBy('created_at','desc')->get();

        $uploads = array();
         if(!empty($uploadData)){

            foreach($uploadData as $upload){
                $region_str1 = $upload['sendto_region'];
                $region_str1 = str_replace(' ', '', $region_str1);

                $region_ids1 = explode(",", $region_str1);
                if(in_array($current_emp->industry,$region_ids1)){
                    $uploads[] = $upload;
                }
            }

            if(!empty($uploads)){
             foreach($uploads as $item){
                $totalemployee  = 0 ;
                 $resultitem = $item;

                $region_str = $item['sendto_region'];
                $region_str = str_replace(' ', '', $region_str);


                $region_ids = explode(",", $region_str);

                //the action to get proper readitems (current user level >= sendto_level of upload )
                //$readitems = ReadItem::where('readitem_id',$item['id'])->get();

                $readitems =  ReadItem::where('readitem_id',$item['id'])->get();

                if($readitems){

                    $readitems = $readitems->toArray();
                    foreach($readitems as $item1){

                        $emp_data = Employee::where('id',$item1['employee_id'])->where('is_deleted','0')->first();
                        if(!empty($emp_data)){
                            //if($emp_data->company_id == $current_emp->company_id && $emp_data->industry == $current_emp->industry)
                            // if($emp_data->company_id == $current_emp->company_id)
                            $emp_arr[] = $item1['employee_id'];
                        }
                    }

                    if($emp_arr){
                            $emp_arr_unique = array_unique($emp_arr);
                        $totalcount = sizeof($emp_arr_unique);
                    }
                    else{
                            $totalcount = 0 ;
                    }
                }

                $totalemployee = Employee::where('company_id',$current_emp->company_id)->whereIn('industry',$region_ids)->where('is_deleted','0')->get()->count();
                $resultitem['count'] = $totalcount.'/'.$totalemployee;
                $resultitem['image']  = $item['image'] != '' ? Storage::disk('s3')->url('images/upload').'/'.$item['image'] : '';
                $result[] = $resultitem;
                $totalcount = 0 ;
                $emp_arr = array();
             }

         }
       }
    }
   return $result;
}
/*This API use for the get the all ReadItems(uploads)*/

public function getAllReadItems(Request $request){

    $userData = $request['userData'];
    $id = $userData['id'];
    $current_emp = Employee::where('id',$id)->where('is_deleted','0')->first();
    $totalcount = 0;
    $totalemployee = 0;

    $resultitem = array();
    $result = array();
    $emp_arr = array();
    $readitems= array();
    $result = $this->getReadItems($current_emp);
    echo json_encode(array('status'=>true,'data'=>$result));die;
}


/*This API for get the All Rewards*/
public function getAllRewards(Request $request){

    $userData = $request['userData'];
    $id = $userData['id'];
    $current_emp = Employee::where('id',$id)->where('is_deleted','0')->first();
    $resultitem = array();
    $result = array();
    if($current_emp){
        $current_emp = $current_emp->toArray();
        $reward= Reward::where('is_active','1')->where('company_id',$current_emp['company_id'])->where('access_level','<=',$current_emp['access_level'] )->get()->toArray();
    	if($reward){
    		foreach($reward as $item){
    		     $resultitem = $item;
    		     $resultitem['image']  = $item['image'] != '' ? Storage::disk('s3')->url('images/reward').'/'.$item['image'] : '';
    		     $result[] = $resultitem;
    		 }
    	     echo json_encode(array('status'=>true,'data'=>$result));die;
    	}

    	else{
    	      echo json_encode(array('status'=>false,'msg'=>'No Active'));die;
    	}
    }

    echo json_encode(array('status'=>false,'msg'=>'No Employee'));die;

}


public function createPurchase(Request $request){

    $userData = $request['userData'];
    $emp_id = $userData['id'];
    $data = array(
             'employee_id'=>$emp_id,
             'rewarditem_id'=>$request->rewarditem_id
   );

    Purchase::create($data);
    $reward = Reward::find($request->rewarditem_id);
    $employee = Employee::find($emp_id);
    $leader_employees = Employee::where('is_deleted','0')->where('company_id',$employee->company_id)->where('access_level',2)->get();
    if(!empty($leader_employees)){
      foreach($leader_employees as $leader){
        $message = $employee->full_name.' has purchased '.$reward->name;
        $this->sendpush($leader->id,'Reward Purchase',$message,[],'Purchase');
      }
    }

    echo json_encode(array('status'=>true,'msg'=>'Success'));die;
}

public function getPurchasePoint($id)
{
    $emp_purchase = 0 ;
    $purchases = Purchase::with(['reward'])->where('employee_id',$id)->get();

    if(count($purchases) > 0){
        foreach($purchases as $purchase) {
            if ($purchase->reward) {
                $emp_purchase += $purchase->reward->point;
            }
        }
    }

    return $emp_purchase;
}
/* This API use for get  company info from company id */

public function getCompanyInfo(Request $request){

      $company_id = $request->company_id;
      $company = Users::where('id',$company_id)->first();

    if($company != '' && isset($company)){

        $data = array(
            'id'=> $company->id,
            'name'=> $company->name,
            'email' => $company->email,
            'access_code' => $company->access_code,
            'created_at' => $company->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $company->updated_at->format('Y-m-d H:i:s'),
            'password' => $company->password,
            'pic' => $company->pic,
            'address' =>$company->address,
            'website_url'=>$company->website_url
        );
        echo json_encode(array('status'=>true,'data'=>$data));die;
    }
    echo json_encode(array('status'=>false,'msg'=>'error'));die;
}

/*This API use for get the region info from the Company id*/
public function getRegionfromCompany(Request $request){

      $company_id = $request->company_id;
      $industry_data = Industry::where('company_id',$company_id)->get()->toArray();

      if($industry_data){
          echo json_encode(array('status'=>true,'data'=>$industry_data));die;
      }

      echo json_encode(array('status'=>false,'msg'=>'error'));die;

}

/* This API use For store user uu id */

    public function setUUid(Request $request){
        $token = str_replace("Bearer ","",$request->header('Authorization'));
        $userData = $request['userData'];
        $validation = Validator::make(
            array(
                'uu_id' => $request->input( 'uu_id' ),
                'platform' => $request->input( 'platform' )
            ),
            array(
                'uu_id' => array( 'required'),
                'platform' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
            $authTokes = Authtoken::where('token',$token )->first();
            if($authTokes != ''){
                $haveUUid = Useruuid::where('uu_id',$request->input( 'uu_id' ))->first();
                if($haveUUid != ''){
                    $haveUUid->delete();
                }
                $platform = strtoupper($request->input( 'platform' ));
                if($platform != 'A' && $platform != 'I'){
                    echo json_encode(array('status'=>false,'msg'=>'The plateform must be android or ios'));die;
                }
                $data = array(
                    'employee_id' => $userData['id'],
                    'uu_id' => $request->input( 'uu_id' ),
                    'platform' => $platform,
                    'token_id' => $authTokes->id
                );
                $userauth = Useruuid::create($data);
                echo json_encode(array('status'=>true));die;
            } else {
                echo json_encode(array('status'=>false,'msg'=>'Authorization Failed.'));die;
            }
        }
    }
    /* END */
 public function changePassword(Request $request){
        $userDataRequest = $request['userData'];
        $employee = Employee::where('id',$userDataRequest['id'])->where('is_deleted','0')->first();
         $validation = Validator::make(
            array(
                'old_password' => $request->input('old_password'),
                'new_password' => $request->input( 'new_password' )
            ),
            array(
                'old_password' => array( 'required'),
                'new_password' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else{
                $checkPassword = Hash::check($request->old_password,$employee->password);
                if(!$checkPassword){
                    echo json_encode(array('status'=>false,'msg'=>'Please Enter Correct Password'));die;
                }
                else {
                    $data = array(
                    'password'=>Hash::make($request->new_password)
                    );
                     Employee::find($userDataRequest['id'])->update($data);
                     echo json_encode(array('status'=>true,'msg'=>'Change Password Success'));die;
                }
        }

 }




    /* This API use For UpdateProfile */
   public function updateProfile(Request $request){
        $userDataRequest = $request['userData'];
        $employee = Employee::where('id',$userDataRequest['id'])->where('is_deleted','0')->first();

        if(!$employee || $employee == ''){
            echo json_encode(array('status'=>false,'msg'=>'No employee Found.'));die;
        }

        if(($userDataRequest['email'] != $request->email) && isset($request->email)){
            $validation = Validator::make(
                array(
                    'email' => $request->input( 'email' )
                ),
                array(
                    'email' => array( 'required','unique:employee' )
                )
            );
            if ( $validation->fails() ) {
                $errors = $validation->messages();
                $errors->toJson();
                echo json_encode(array('status'=>false,'msg'=>$errors));die;
            }
        }

        $data = array(

            'email'=>isset($request->email) ? $request->email: $userDataRequest['email'],
            'full_name'=>isset($request->full_name) ? $request->full_name : $userDataRequest['full_name'],

            'phone_number'=>isset($request->phone_number) ? $request->phone_number : $userDataRequest['phone_number'] ,
            'website' => isset($request->website) ? $request->website : (isset($userDataRequest['website']) ? $userDataRequest['website'] : ""),
            'myplan'=>isset($request->myplan) ? $request->myplan :"",
            'past_jobs'=>isset($request->past_jobs) ? $request->past_jobs :"",
            'emp_reference'=>isset($request->references) ? $request->references :"",
            'myobjective'=>isset($request->myobjective) ? $request->myobjective :"",
            'business_url'=>isset($request->business_url) ? $request->business_url : (isset($userDataRequest['business_url']) ? $userDataRequest['business_url'] : ""),
            'independent_category_id'=>isset($request->independent_category_id) ? $request->independent_category_id :"",
        );

        $this->requestlog($request->all());

        if(isset($request->image) && $request->image != ''){

              if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $path = 'images/employee/';
                $file = $request->file("image");
                $image = Image::make($file);
                $image->orientate();
                Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
                $data['image'] = $imageName ;

              }
        }

        Employee::find($userDataRequest['id'])->update($data);
        $update_data = $this->get_EmployeeInfo_fromID($userDataRequest['id']);

        echo json_encode(array('status'=>true,'data'=>$update_data ));die;

    }

    /* END */

    public function requestlog($data){
        //Something to write to txt log
        $path = public_path().'/log/';
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        $log  = print_r($data,true).PHP_EOL.
        "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents($path.'log.txt', $log, FILE_APPEND);

    }

    /*This API use for getting the region to get the distancne between */
    public function getRegion(Request $request){
        $userDataRequest = $request['userData'];
        $employee = Employee::find($userDataRequest['id']);
        $region = $this->get_IndustryInfo_fromID($employee->industry);
        echo json_encode(array('status'=>true,'data'=>$region ));die;


    }


    /* */
     public function getchatusers(Request $request){
        $userDataRequest = $request['userData'];
        $employees = Employee::where('company_id',$userDataRequest['company_id'])->where('is_deleted','0')
        ->where('access_level','<=',2)->orderBy('created_at','desc')->get();
        $emp_data = array();
        $temp_data = array();
        if(!empty($employees)){
            foreach($employees as $emp){
                $temp_data['id'] = $emp->id;
                $temp_data['full_name'] = $emp->full_name;
//                $temp_data['image'] =  $emp->image != '' ? Storage::disk('s3')->url('/images/employee/').$emp->image : '';
                $temp_data['image'] =  $emp->image;
                $emp_data[] = $temp_data;
            }
        }

        echo json_encode(array('status'=>true,'data'=>$emp_data));die;
     }

    /* This API use For buildPost */
    public function buildPost(Request $request){

        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'image' => $request->file( 'image' ),
                'build_text' => $request->input( 'build_text' ),
                'subcategory' => $request->input( 'subcategory' ),
                // 'email' => $request->input( 'email' )
            ),
            array(
                'image' => array( 'required'),
                'build_text' => array( 'required' ),
                'subcategory' => array( 'required' ),
                // 'email' => array( 'required')
            )
        );

        $builds = null;
        $errors = '';

        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            return response()->json(array('status'=>false,'msg'=>$errors), 400);
        } else {
            $this->requestlog($request->all());


 	          $challenge_id = 0 ;


            if(!empty($request->challenge_id)){

                $getChallenge = Challenge::where('id',$request->challenge_id)->first();
                //echo json_encode($getChallenge);

                if(empty($getChallenge)){
                	return response()->json(array('status'=>false,'msg'=>'Invalid empty Challenge'), 400);
                 }
                if($getChallenge=='') {
                	return response()->json(array('status'=>false,'msg'=>'Invalid space Challenge'), 400);
                }

                else{
                    $getChallenge = $getChallenge->toArray();
                }

                if($getChallenge['status'] == '0'){
                    return response()->json(array('status'=>true,'msg'=>'This Challenge is in use'), 400);
                }
                if($getChallenge['status'] == '1'){
                    return response()->json(array('status'=>true,'msg'=>'This Challenge is Already Won'), 400);
                }

                if($getChallenge['company_id'] != $userDataRequest['company_id']){
                    return response()->json(array('status'=>false,'msg'=>'Invalid Challenge'), 400);
                }
    		        $challenge_id = $request->challenge_id;

            }

           $duel_id = 0 ;
           if(!empty($request->duel_id)) $duel_id = $request->duel_id;

            $data = array(
                'build_text'=>  urldecode($request->build_text),
                'subcategory'=> $request->subcategory,
                'status'=> '-1',
                'employee_id'=> $userDataRequest['id'],
                'company_id'=>$userDataRequest['company_id'],
                'email_to'=>$request->email,
                'challenge_id'=>$challenge_id,
                'duel_id'=>$duel_id
            );

            // if(isset($request->image) && $request->image != ''){

            //     if($request->hasFile('image')){
            $imageName = $userDataRequest['id'].'_'.time().'.'.$request->image->getClientOriginalExtension();
            $path = 'images/build/';
            $file = $request->file("image");
            $image = Image::make($file);
            $image->orientate();
            Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');
            $data['image'] = $imageName ;

            //     }
            // }

            $builds = Builds::create($data);


            $buildsData = array(
                'id'=>$builds->id,
                'image'=>$builds->image != '' ? Storage::disk('s3')->url('/images/build/').$builds->image : '',
                'build_text'=>$builds->build_text,
                'subcategory'=>$builds->subcategory,
                'status'=>$builds->status,
                'employee_id'=>$builds->employee_id,
                'email_to'=>$builds->email_to,
                'duel_id'=>$request->duel_id = ''? 0:$request->duel_id,
                'challenge_id'=>$request->challenge_id = ''? 0:$request->challenge_id
            );

            $category = Categories::select('category_name')->where('id', $builds->category_id)->first();
            if($category){
                $path = storage_path() . '/../public/js/notification.json';
                $json = json_decode(file_get_contents($path), true);
                $message = $json['buildWithCategory'];
                if($message != ''){
                    $message = str_replace("{{EMPNAME}}",$userDataRequest['full_name'],$message);
                    $message = str_replace("{{CATNAME}}",$category->category_name,$message);
                    $message = str_replace("{{BUILDNAME}}",$builds->build_text,$message);
                } else {
                    $message = $userDataRequest['full_name']. " created a new build '".$builds->build_text."' with '".$category->category_name."' category.";
                }

               $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>$userDataRequest['id'],
                  'receiver'=>$userDataRequest['company_id'],
                  'receiver_type'=>2
              );

              Notification::create($list_param);
            }
            $this->getEmployeeTierData($userDataRequest['id']);

            if (!empty($request->email) && $builds != null) {
              // $leader = Employee::where('email', $request->email)->firstOrFail();
              $employee = Employee::findOrFail($builds->employee_id);

              if(filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                  Mail::to($request->email)->send( new \App\Mail\SubmissionPosted($builds, $employee) );
              } else {
                  $content = $this->makeSMSContent($builds, $employee);
                  $this->sendSMS($request->email, $content);
              }
            }

            return response()->json(array('status'=>true,'data'=>$buildsData));
        }

    }
    /* END */

    /* This API is for building multiple posts */
    public function buildPosts(Request $request){

      $userDataRequest = $request['userData'];
      $validation = Validator::make($request->all(),
        array(
          'posts.*.image' => 'required',
          'posts.*.description' => 'required',
          'posts.*.subcategory' => 'required',
          'posts.*.challenge_id' => 'numeric|nullable',
          'posts.*.duel_id' => 'numeric|nullable',
          'email' => 'required',
          'firstname' => 'nullable',
          'lastname' => 'nullable'
        )
      );

      if ( $validation->fails() ) {
        $errors = $validation->messages();
        $errors->toJson();
        return response()->json(array('status'=>false,'msg'=>$errors), 400);
      }


      $email = $request->email;
      $firstname = $request->firstname;
      $lastname = $request->lastname;
      $posts = $request->get("posts");
      $images = $request->file("posts");

      $resp = [];
      $builds = [];


      // challenges validate
      foreach ($posts as $index => $post) {
        $challenge_id = 0 ;

        if(!empty($post['challenge_id'])){

          $getChallenge = Challenge::where('id',$post['challenge_id'])->first();
          //echo json_encode($getChallenge);

          if (empty($getChallenge))
            return response()->json(array('status'=>false,'msg'=>'Invalid empty Challenge'), 400);

          if ($getChallenge=='')
            return response()->json(array('status'=>false,'msg'=>'Invalid space Challenge'), 400);

          $getChallenge = $getChallenge->toArray();

          if ($getChallenge['status'] == '0')
            return response()->json(array('status'=>true,'msg'=>'This Challenge is in use'), 400);

          if ($getChallenge['status'] == '1')
            return response()->json(array('status'=>true,'msg'=>'This Challenge is Already Won'), 400);

          if ($getChallenge['company_id'] != $userDataRequest['company_id'])
            return response()->json(array('status'=>false,'msg'=>'Invalid Challenge'), 400);

          $challenge_id = $post['challenge_id'];
        }
        $posts[$index]['challenge_id'] = $challenge_id;
      }

      // create posts
      // $index = 0;
      $batch = null;

      if (!empty($email)) {

          /*
          if (strpos($email, '@') !== true) {
              // In the case of phone number, Format the number
              $email = $this->formatPhoneNumber($employee->phone_number);
          }
          */

          $batch_where = Batch::where('email_to', $email)->where('employee_id', $userDataRequest['id'])->orderBy('created_at', 'DESC')->first();
          if ($batch_where) {
              $firstname = $batch_where->firstname;
              $lastname = $batch_where->lastname;
          }

          $batch = Batch::create([
            'id'          => Uuid::uuid1(),
            'employee_id' => $userDataRequest['id'],
            'email_to'    => $email,
            'firstname'   => $firstname,
            'lastname'    => $lastname,
          ]);
          $batch->save();

      }

      $subcategoriesName = " ";
      foreach ($posts as $index => $post) {
        $duel_id = 0;

        if(!empty($post['duel_id'])) $duel_id = $post['duel_id'];

        $data = array(
            'build_text'=>  urldecode($post['description']),
            'subcategory'=> $post['subcategory'],
            'status'=> '-1',
            'employee_id'=> $userDataRequest['id'],
            'company_id'=>$userDataRequest['company_id'],
            'email_to'=>$request->email,
            'challenge_id'=>$post['challenge_id'],
            'duel_id'=>$duel_id,
            'batch_id'=>$batch?$batch->id:null,
        );

        $employee = Employee::find($batch->employee_id);
        $userType = $employee->userType;
        if ($userType == "1") { // employee
          $subcategory = Subcategory::where('id',$post['subcategory'])->get()->first();
        } else { // independent user
          $subcategory = Subcategory::where('id',$post['subcategory'])->get()->first();
        }


        $subcategoriesName = $subcategoriesName.$subcategory->subcategory_name.", ";

        $imageName = $userDataRequest['id'].'_'.time().$index.'.'.$images[$index]['image']->getClientOriginalExtension();
        $path = 'images/build/';
        $file = $images[$index]["image"];

//        $image = Image::make($file);
//        $image->orientate();
//        Storage::disk("s3")->put($path.$imageName,$image->stream(),'public');

          $imageProcessor = new ImageProcessor($file);
          $imageProcessor->scale(80);
          $imageProcessor->save($path . $imageName);



        $data['image'] = $imageName;

        if ($employee->independent_category_id != null) { // If employee, it is null
            $data['category_id'] = $employee->independent_category_id;
        }


        $build = Builds::create($data);

        $buildData = array(
            'id'=>$build->id,
            'image'=>$build->image != '' ? Storage::disk('s3')->url('/images/build/').$build->image : '',
            'build_text'=>$build->build_text,
            'subcategory'=>$build->subcategory,
            'status'=>$build->status,
            'employee_id'=>$build->employee_id,
            'email_to'=>$build->email_to,
            'duel_id'=>$request->duel_id = ''? 0:$request->duel_id,
            'challenge_id'=>$request->challenge_id = ''? 0:$request->challenge_id
        );

        array_push($resp, $buildData);
        array_push($builds, $build);

        $this->getEmployeeTierData($userDataRequest['id']);
      } /** end foreach $posts **/


      $subcategoriesName = rtrim($subcategoriesName, ', ');

      if ($batch) {
        $employee = Employee::findOrFail($batch->employee_id);

        if(filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($request->email)->send( new \App\Mail\SubmissionPosted($batch, $employee) );
        } else {
            $content = $this->makeSMSContent($batch, $employee);
            $this->sendSMS($request->email, $content);
        }




        if ($batch->email_to == "employeeMultiPosts@gmail.com") {


            $employee = Employee::find($batch->employee_id);
            $access_level = $employee->access_level;

            $message = $employee->full_name." has uploaded ".$subcategoriesName;
            $leader_employees = [];
            if ($access_level == "0") {

                $leader_employees1 = Employee::where('is_deleted','0')->where('company_id',$employee->company_id)->where('access_level', 1)->where('industry', $employee->industry)->get();

                $leader_employees2 = Employee::where('is_deleted','0')->where('company_id',$employee->company_id)->where('access_level', 2)->orWhere('access_level', 3)->get();

                  if(!empty($leader_employees1))
                  {
                    foreach($leader_employees1 as $leader) {
                      $this->sendpush($leader->id,'Uptime',$message,[],'submissions upload by employee 1.0');
                    }

                  }

                  if(!empty($leader_employees2))
                  {
                    foreach($leader_employees2 as $leader) {
                      $this->sendpush($leader->id,'Uptime',$message,[],'submissions upload by employee 1.0');
                    }
                  }

            } else if ($access_level == "1") {
                $leader_employees = Employee::where('is_deleted','0')->where('company_id',$employee->company_id)->where('access_level', 2)->orWhere('access_level', 3)->get();
            } else if ($access_level == "2") {
                $leader_employees = Employee::where('is_deleted','0')->where('company_id',$employee->company_id)->where('access_level', 3)->get();
            }

            if(!empty($leader_employees))
            {
              foreach($leader_employees as $leader) {
                $this->sendpush($leader->id,'Uptime',$message,[],'submissions upload by employee 1.0');
              }

            }


        }



      }

      return response()->json(array('status'=>true,'data'=>$resp));


    }
    /* END */


    public function reminderBuild(Request $request) {

        $batch_id = $request['batch_id'];
        $validation = Validator::make(
            array(
                'batch_id' => $request->input( 'batch_id' ),
            ),
            array(
                'batch_id' => array( 'required' ),
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
          if (!empty($batch_id)) {
              $batch = Batch::where('id', $batch_id)->first();
              $employee = Employee::findOrFail($batch->employee_id);

                if(filter_var($batch->email_to, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($batch->email_to)->send( new \App\Mail\SubmissionPosted($batch, $employee) );
                } else {
                    $content = $this->makeSMSContent($batch, $employee);
                    $this->sendSMS($batch->email_to, $content);
                }

              echo json_encode(array('status'=>true,'msg'=>"The reminder was sent"));die;
                // return response()->json(array('status'=>true,'data'=>$content));
          }
        }
    }

    public function makeSMSContent($batch, $employee) {
        $content = $employee->full_name. " has requested you to review and approve their work with you.\n"
            ."Click link below\n";

        $url = route('verify.submission', ['uuid' => $batch->id]);

        // $bitlyClient = new BitlyClient(getenv('BITLY_TOKEN'));
        // $options = ['longUrl' => $url];
        // $response = $bitlyClient->shorten($options);

        // $content = $content . $response->data->url;

        $content = $content . $url;

        return $content;
    }

    public function getBitly(Request $request) {
        $bitlyClient = new BitlyClient(getenv('BITLY_TOKEN'));
        //952692dc-5c4b-11ea-88d7-064fcdfa57ae
        $url = route('verify.submission', ['uuid' => '952692dc-5c4b-11ea-88d7-064fcdfa57ae']);
        $options = ['longUrl' => $url];
        $response = $bitlyClient->shorten($options);

        return response()->json(array('result' => $response->data->url), 200);
    }

    public function sendSMS($recipients, $message)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");

        $client = new Client($account_sid, $auth_token);

        $client->messages->create($recipients, ['from' => $twilio_number, 'body'=>$message]);
    }

    public function getCategory($employee){
        $result_categories = array();
        $employee = $employee->toArray();
        $Categories = Categories::where('company_id',$employee['company_id'])->get()->toArray();

        if(!empty($Categories)){

            $category_instance = array();
            foreach ($Categories as $item) {

              $Subcategories = Subcategory::where('category_id',$item['id'])->where('user_access_level','<=',2)->where('status',1)->get();




                $category_instance['id'] = $item['id'];
                $category_instance['category_name'] = $item['category_name'];
                $category_instance['company_id'] = $item['company_id'];
                $category_instance['created_at'] = $item['created_at'];
                $category_instance['updated_at'] = $item['updated_at'];
                $category_instance['is_active'] = $item['is_active'];
                if(!empty($Subcategories))
                $category_instance['sub_categories'] = $Subcategories->toArray();
                else  $category_instance['sub_categories'] = array();
                $result_categories[] = $category_instance;

            }
        }
        return $result_categories;
    }

    public function getCategories(Request $request){

         $userData = $request['userData'];
         $id = $userData['id'];
         $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
         $result_categories = array();
        if(!empty($employee))
           $result_categories = $this->getCategory($employee);
        echo json_encode(array('status'=>true,'data'=>$result_categories));die;
    }
    /* END */

    public function getPublicCategories(Request $request)
    {
        $result_categories = array();
        $Categories = Categories::where('company_id','119')->get()->toArray();

        if(!empty($Categories)){

          $category_instance = array();
          foreach ($Categories as $item) {

            $Subcategories = Subcategory::where('category_id',$item['id'])->where('user_access_level','<=',2)->where('status',1)->get();

            $category_instance['id'] = $item['id'];
            $category_instance['category_name'] = $item['category_name'];
            $category_instance['company_id'] = $item['company_id'];
            $category_instance['created_at'] = $item['created_at'];
            $category_instance['updated_at'] = $item['updated_at'];
            $category_instance['is_active'] = $item['is_active'];
            if(!empty($Subcategories))
            $category_instance['sub_categories'] = $Subcategories->toArray();
            else  $category_instance['sub_categories'] = array();
            $result_categories[] = $category_instance;

          }
        }
        return response()->json(array('status'=>true,'data'=>$result_categories));
    }

     public function getMainCategory($employee){
        $result_categories = array();
        $employee = $employee->toArray();
        $Categories = Categories::where('company_id',$employee['company_id'])->get()->toArray();

        if(!empty($Categories)){

            $category_instance = array();
              foreach ($Categories as $item) {
                  $category_instance['id'] = $item['id'];
                  $category_instance['category_name'] = $item['category_name'];
                  $category_instance['company_id'] = $item['company_id'];
                  $category_instance['created_at'] = $item['created_at'];
                  $category_instance['updated_at'] = $item['updated_at'];
                  $category_instance['is_active'] = $item['is_active'];
                  $result_categories[] = $category_instance;
              }
        }
        return $result_categories;
    }

    public function getMainCategories(Request $request) {

         $userData = $request['userData'];
         $id = $userData['id'];
         $employee = Employee::where('id',$id)->where('is_deleted','0')->first();
         $result_categories = array();
         if(empty($employee)) {
            echo json_encode(array('status'=>false,'data'=>"User is not registered!"));die;
         } else {
            $employee = $employee->toArray();
            $Categories = Categories::get()->toArray();

          if(!empty($Categories)){
              $category_instance = array();
              foreach ($Categories as $item) {
                  $category_instance['id'] = $item['id'];
                  $category_instance['category_name'] = $item['category_name'];
                  $category_instance['company_id'] = $item['company_id'];
                  $category_instance['created_at'] = $item['created_at'];
                  $category_instance['updated_at'] = $item['updated_at'];
                  $category_instance['is_active'] = $item['is_active'];
                  $result_categories[] = $category_instance;
              }
          }
          echo json_encode(array('status'=>true,'data'=>$result_categories));die;
        }
    }

    /* This API use For GetBuild */
    public function getBuild(Request $request){
        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'build_id' => $request->input( 'build_id' ),
            ),
            array(
                'build_id' => array( 'required' ),
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
            $getBuild = Builds::find($request->build_id);
            if(!empty($getBuild) || $getBuild != ''){
                if($getBuild->employee_id != $userDataRequest['id']){
                    echo json_encode(array('status'=>false,'msg'=>'Something went wrong.'));die;
                }
                $data = array(
                    'id'=> $getBuild->id,
                    'image'=>$getBuild->image != '' ? Storage::disk('s3')->url('/images/build/').$getBuild->image : '',
                    'build_text'=> $getBuild->build_text,
                    'category_id'=> $getBuild->category_id,
                    'status'=> $getBuild->status,
                    'employee_id'=> $getBuild->employee_id,
                    'created_at'=> $getBuild->created_at,
                    'updated_at'=> $getBuild->updated_at,
                );
                echo json_encode(array('status'=>false,'data'=>$data));die;
            }else{
                echo json_encode(array('status'=>true,'msg'=>'No Build Found'));die;
            }
        }

    }
    /* END */

    /* This API use For GetBuild */
    public function getBuilds(Request $request){
        $userDataRequest = $request['userData'];
        $getBuild = Builds::where('company_id',$userDataRequest['company_id'])->get()->toArray();
        if(!empty($getBuild) || $getBuild != ''){
            foreach($getBuild as $build){

                $data[] = array(
                    'id'=> $build['id'],
                    'image'=>$build['image'] != '' ? Storage::disk('s3')->url('/images/build/').$build['image'] : '',
                    'build_text'=> $build['build_text'],
                    'category_id'=> $build['category_id'],
                    'status'=> $build['status'],
                    'challenge_id' => $build['challenge_id'] === 0? null : $build['challenge_id'],
                    'employee_id'=> $build['employee_id'],
                    'created_at'=> $build['created_at'],
                    'updated_at'=> $build['updated_at'],
                );
            }
            return response()->json(
                array('status'=>true,'data'=>$data)
            );
        }else{
            return response()->json(
                array(array('status'=>true,'msg'=>'No Builds Found'))
            );
        }
    }
    /* END */

public function get_Challenge_WinCount(Request $request){
    $userDataRequest = $request['userData'];
    $emp_id = $userDataRequest['id'];
    $winCount = Challenge::where('employee_id',$emp_id)->where('preset_type','0')->where('status','1')->get()->count();
    $data = array(
    'challenge_wincount'=>$winCount
    );
    echo json_encode(array('status'=>true,'data'=>$data));die;
}

    /* This API use For postChallenge */
    public function postChallenge(Request $request){
        $userDataRequest = $request['userData'];
        //print_r($userDataRequest);die;
        $validation = Validator::make(
            array(
                'challenge_text' => $request->input( 'challenge_text' ),
                //'build_id' => $request->input( 'build_id' ),
                'point' => $request->input( 'point' ),
                'company_id' => $request->input( 'company_id' ),
                'category_id' => $request->input( 'category_id' ),


            ),
            array(
                'challenge_text' => array( 'required' ),
                // 'build_id' => array( 'required' ),
                'point' => array( 'required' ),
                'company_id' => array( 'required' ),
                'category_id' => array( 'required' ),
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{

            $categories_check = Categories::where('id',$request->category_id)->where('company_id',$userDataRequest['company_id'])->first();
            if(empty($categories_check)){
                echo json_encode(array('status'=>false,'msg'=>'Invalid Category'));die;
            }

            // if($categories_check )

            if(isset($request->image) && $request->image != ''){
                if (preg_match('/^data:image\/(\w+);base64,/', $request->image, $type)) {
                    $image_data = substr($request->image, strpos($request->image, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                        echo json_encode(array('status'=>false,'msg'=>'Invalid image.'));die;
                    }

                    $image_data = base64_decode($image_data);

                    if ($image_data === false) {
                        echo json_encode(array('status'=>false,'msg'=>'Somthing Went Wrong.'));die;
                    }
                } else {
                    echo json_encode(array('status'=>false,'msg'=>'Invalid image.'));die;
                }
            }

            $data = array(

                'challenge_text'=> $request->challenge_text,
                //'build_id'=> $request->build_id,
                'status'=> '-1',
                'point'=> $request->point,
                'company_id'=> $request->company_id,
            );


            if(isset($request->image) && $request->image != ''){
                $path = 'images/challenge/';
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                $uploaded_image_name = time().".{$type}";
                $imageName = $path.$uploaded_image_name;
                Storage::disk("s3")->put($imageName, $image_data, "public");
                // file_put_contents($imageName, $image_data);
                $data['image'] = $uploaded_image_name;
                // $newImage = $this->rotetImage($imageName);
                // file_put_contents($imageName, $newImage);
            }

            $challenge = Challenge::create($data);

            // if($challenge->id && $request->hasFile('image')){
            //     $path = public_path().'/images/challenge/';
            //     File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            //     $request->image->move(public_path('images/challenge'), $imageName);
            // }
            $data_s = array(
                'id'=> $challenge->id,
                'image'=>$challenge->image != '' ? Storage::disk('s3')->url('/images/challenge/').$challenge->image : '',
                'challenge_text'=> $challenge->challenge_text,
                'build_id'=> $challenge->build_id,
                'status'=> $challenge->status,
                'point'=> $challenge->point,
                'category_id'=> $challenge->category_id,
                'created_at'=> $challenge->created_at,
                'updated_at'=> $challenge->updated_at,
            );
            $this->newChallenge($challenge);
            echo json_encode(array('status'=>true,'data'=>$data_s));die;
        }
    }
    /* END */

    /* This API use For GetChallenge */
    public function GetChallenge(Request $request){
        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'challenge_id' => $request->input( 'challenge_id' )

            ),
            array(
                'challenge_id' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else{
            $challenge = Challenge::find($request->challenge_id);
            if($challenge == ''){
                echo json_encode(array('status'=>false,'msg'=>'Invalid Challenge'));die;
            }
            // $getBuild = Builds::where("id",$challenge->build_id)->first();
            // if(empty($getBuild)){
            //     echo json_encode(array('status'=>false,'msg'=>'Invalid Build'));die;
            // }
            /*
            if($challenge->company_id != $userDataRequest['company_id']){
                echo json_encode(array('status'=>false,'msg'=>'Invalid Challenge'));die;
            }*/

            $data_s = array(
                'id'=> $challenge->id,
                'image'=>$challenge->image != '' ? Storage::disk('s3')->url('/images/challenge/').$challenge->image : '',
                'challenge_text'=> $challenge->challenge_text,
                'build_id'=> $challenge->build_id,
                'status'=> $challenge->status,
                'point'=> $challenge->point,
                'category_id'=> $challenge->category_id,
                'created_at'=> $challenge->created_at,
                'updated_at'=> $challenge->updated_at,
            );
            echo json_encode(array('status'=>true,'data'=>$data_s));die;

        }

    }
    /* END */

    /* This API use For GetChallenges */
    public function GetChallenges(Request $request){
        $userDataRequest = $request['userData'];
        $builds = Builds::select('challenge_id')->where('employee_id',$userDataRequest['id'])->get()->toArray();

        if(!empty($builds)){
            $builds = $builds;
        }else{
            echo json_encode(array('status'=>true,'data'=>array()));die;
        }
        $challengesArry = array();
        foreach($builds as $build){
            $bids[] = $build['challenge_id'];
        }

        $challenges = Challenge::whereIn('id',$bids)->get();
        $data_s = array();
        if($challenges != ''){
            $challenges = $challenges->toArray();
            if(!empty($challenges)){
                foreach($challenges as $challenge ){
                    $data_s[] = array(
                        'id'=> $challenge['id'],
                        'image'=>$challenge['image'] != '' ? Storage::disk('s3')->url('/images/challenge/').$challenge['image'] : '',
                        'challenge_text'=> $challenge['challenge_text'],
                        //'build_id'=> $challenge['build_id'],
                        'status'=> $challenge['status'],
                        'point'=> $challenge['point'],
                        'category_id'=> $challenge['category_id'],
                        'created_at'=> $challenge['created_at'],
                        'updated_at'=> $challenge['updated_at'],
                    );
                }
            }
            echo json_encode(array('status'=>true,'data'=>$data_s));die;
        }else{
            echo json_encode(array('status'=>true,'data'=>array()));die;
        }

    }
    /* END */
    public function topleadermsg($aftertop,$sender){
        $topemp = Employee::find($aftertop);
        $message= "Congratulations ".$topemp->full_name.". You are the top points leader in your company";
        $this->sendpush($aftertop,'Top Leaderboard',$message,[],'Top');

        $list_param = array(
            'content_type'=>3,
            'message' => $message,
            'sender'=>$sender,
            'receiver'=>$aftertop,
            'receiver_type'=>3
            );
        Notification::create($list_param);
    }

    /* This API used For Post Votes on uploads */
    public function postValidate(Request $request){

        $this->requestlog($request->all());
        $todayDate = date('Y-m-d');
        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'status' => $request->input( 'status' ),
                'build_id' => $request->input( 'build_id' )

            ),
            array(
                'status' => array( 'required' ),
                'build_id' => array( 'required' )
            )
        );

        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }
        else{

            $getBuild = Builds::find($request->build_id);

            if($getBuild != ''){
                $getBuild = $getBuild->toArray();
            }
            else{
                echo json_encode(array('status'=>false,'msg'=>'Build Not Found'));die;
            }

          //  if($getBuild['employee_id'] != $userDataRequest['id']){
                if($getBuild['status'] == '-1'){
                    $getVailidation = Validations::where('employee_id',$userDataRequest['id'])->where('build_id',$request->build_id)->get()->toArray();
                    if(!empty($getVailidation)){
                        echo json_encode(array('status'=>true,'msg'=>'Already Voted'));die;
                    }
                    $data = array(
                        'employee_id'=>$userDataRequest['id'],
                        'status'=>$request->status,
                        'build_id'=>$request->build_id,
                        'win'=>$request->status
                    );
                    $Validations = Validations::create($data);

                    $currenttop = $this->getTopRank($userDataRequest['id']);

                    //update build status start
                    $bData = array('status'=>$request->status);
                    $build_data = Builds::find($request->build_id);
                    $build_data->update($bData);
                    //end build status
                    $aftertop = $this->getTopRank($userDataRequest['id']);
                    if($currenttop != $aftertop){
                      $this->topleadermsg($aftertop,$userDataRequest['id']);
                    }

                    //send message

                        $employee_validates = Validations::where('build_id',$request->build_id)->get();
                        $data = [];
                        $emp = Employee::where("id",$build_data->employee_id)->where("is_deleted",'0')->first();
                        if(!empty($emp)){
                            $message = '';
                            if($request->status == '1'){
                                $message = "Congratulations, ".$emp->full_name.". Your Submission ".$build_data->build_text." has been approved";
                                $this->sendpush($emp->id,'Submission Approved',$message,$data,'buildApprove');

                            }
                            if($request->status == '0'){
                                $message = "Sorry, ".$emp->full_name.". Your Submission ".$build_data->build_text." has been rejected";
                                $this->sendpush($emp->id,'Submission rejected',$message,$data,'buildReject');

                            }
                            $list_param = array(
                                'content_type'=>5,
                                'message' => $message,
                                'sender'=>$build_data->company_id,
                                'receiver'=>$emp->id,
                                'receiver_type'=>3
                                );
                            Notification::create($list_param);
                        }

                    //end of send message
                    //$this->setValidationAndSet($request->build_id,$userDataRequest['company_id'], $userDataRequest['id']);   //check status employee count company's employee count
                    $getTenure = Tenure::where('employee_id',$userDataRequest['id'])->whereDate('created_at',$todayDate)->count();

                    if($getTenure == 0){
                        $getTotalValidation = Validations::where('employee_id',$userDataRequest['id'])->whereDate('created_at',$todayDate)->count();
                        //echo $getTotalValidation;die;
                        if($getTotalValidation == 5){
                            $TenureData = array(
                                'employee_id'=>$userDataRequest['id'],
                                'point'=>1
                            );
                            $Tenures = Tenure::create($TenureData);
                        }
                    }

                    $this->getEmployeeTierData($userDataRequest['id']);

                    echo json_encode(array('status'=>true,'msg'=>'Successfully Voted'));die;

                }else{
                    echo json_encode(array('status'=>true,'msg'=>'Challenge is over'));die;
                }
            // }else{
            //     echo json_encode(array('status'=>false,'msg'=>'You are not allow vote for own challenge'));die;
            // }
        }
    }
    /* END */

    /* This API use For GetValidate */
    public function getValidate(Request $request){
        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'validation_id' => $request->input( 'validation_id' )

            ),
            array(
                'validation_id' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
            $getValidations = Validations::where('id',$request->validation_id)->where('employee_id',$userDataRequest['id'])->first();
            if($getValidations != ''){
                $getValidations->toArray();
            }else{
                echo json_encode(array('status'=>true,'msg'=>'Something went wrong.'));die;
            }
            $data = array(
                'employee_id'=>$getValidations['id'],
                'status'=>$getValidations['status'],
                'build_id'=>$getValidations['build_id'],
                'win'=>$getValidations['win'],
                'created_at'=>$getValidations['created_at'],
            );
            // $Validations = Validations::create($data);
            echo json_encode(array('status'=>true,'data'=>$data));die;
        }

    }
    /* END */

    /* This API use For getValidates */
    public function getValidates(Request $request){
        $userDataRequest = $request['userData'];

        $getValidations = Validations::where('employee_id',$userDataRequest['id'])->get()->toArray();
        if(empty($getValidations)){
            echo json_encode(array('status'=>true,'msg'=>'No data found.'));die;
        }
        echo json_encode(array('status'=>true,'data'=>$getValidations));die;

    }
    /* END */




    /* This API use For getValidates */
    public function getNotification(Request $request){
        $userDataRequest = $request['userData'];
        $id = $userDataRequest['id'];
        $employee  = Employee::find($id);
       // $employee = $sender;
        $notification = Notification::orderBy('created_at','desc')->get();;
        $result = array();
        $resultitem = array();

        foreach($notification as $msg){
          if($employee->id == $msg->sender){
              $flag = 0;
          }
          else{
                $temp_receiver = $msg->receiver;
                $receiver_arr = explode(',',$temp_receiver);
                $flag = 0;

                //if receiver_type is all - 1
                      if($msg->receiver_type == 1){
                          //in case of same company
                          if($msg->receiver == $employee->company_id)
                                $flag = 1 ;
                      }
                //if receiver_type is company - 2
                     if($msg->receiver_type == 2){
                           if($employee->company_id == $msg->receiver){
                               $flag = 1;
                           }
                      }
                //if receiver_type is employee - 3
                      if($msg->receiver_type == 3){
                          foreach($receiver_arr as $item){
                            $receiver = Employee::find(intval($item));
                            if(!empty($receiver)){
                                if($employee->id == $item) {
                                     $flag = 1;
                                     break;
                                }
                            }
                          }
                      }
                //if receiver_type is region - 4
                      if($msg->receiver_type == 4 ){

                         foreach($receiver_arr as $item){
                             $region = Industry::find($item);
                             if(!empty($region)){
                                 if($employee->company_id == $region->company_id){
                                        $flag = 1 ;
                                        break;
                                 }
                             }
                         }
                      }
                //if receiver_type is level - 5
                      if($msg->receiver_type == 5){
                          if($employee->access_level <= $msg->receiver)
                           $flag = 1;
                      }

          }
          if($flag == 1){
             $resultitem['message']=$msg->message;
             $resultitem['content_type'] = $msg->content_type;
             $result[]= $resultitem;
          }

        }

       $tier_model = $this->getEmployeeTodayTierModel($employee->id);
       $rest_model = $this->getRestTierModel($employee->id);

       $point = round($this->countPoint($id));

       $data = array(
        'notifications'=>$result,
        'tier_model'=>$tier_model,
        'tier_restmodel'=>$rest_model,
        'point'=>$point
       );

     echo json_encode(array('status'=>true,'data'=>$data));die;

    }
    /* END */

    public function getTotalvalidationByBid($bid){
        $totalValidation = Validations::where('build_id',$bid)->get();
        return $totalValidation->count();
    }

    public function getValidationbyBidWin($bid){
        $totalValidation = Validations::where('build_id',$bid)->where('status','1')->get();
        return $totalValidation->count();
    }

    public function getValidationbyBidLose($bid){
        $totalValidation = Validations::where('build_id',$bid)->where('status','0')->get();
        return $totalValidation->count();
    }

    public function getTotalEmployeeByCid($cid){
        $totalCompanyEmployee = Employee::where('company_id',$cid)->where("is_deleted",'0')->get();
        $count = $totalCompanyEmployee->count();
        $newCount = $count-1;
        return $newCount;
    }

    public function setValidationAndSet($bid,$cid,$eid){


        $totalValidations = $this->getTotalvalidationByBid($bid);
        $totalCompanyEmplyies = $this->getTotalEmployeeByCid($cid);
        //echo $totalCompanyEmplyies;die;
        if($totalValidations == 0 || $totalCompanyEmplyies == 0){
            $checkVotes = 0;
        }else{
            $checkVotes = $totalValidations/$totalCompanyEmplyies * 100;
        }
       // echo json_encode(array('status'=>true,'msg'=>$checkVotes.','));
        if($checkVotes >= 50){
            $totalWin =$this->getValidationbyBidWin($bid);
            $totalLose =$this->getValidationbyBidLose($bid);
            if($totalWin > $totalLose){
                $majurity = '1';
            }else if($totalWin < $totalLose){
                $majurity = '0';
            }else{
                $majurity = '';
            }
            if($majurity != ''){


                $bData = array('status'=>$majurity);
                $winData = array('win'=>'1');
                $loseData = array('win'=>'0');
                $build = Builds::find($bid);

                Builds::find($bid)->update($bData);

                $this->approveandrejectBuild($bid,$majurity);
                $this->checkBuildWithCategory($bid);

                if($totalWin > $totalLose){
                     Validations::where("build_id",$bid)->where('status','1')->update($winData);
                     Validations::where("build_id",$bid)->where('status','0')->update($winData);
                }else if($totalWin < $totalLose){
                     Validations::where("build_id",$bid)->where('status','0')->update($loseData);
                     Validations::where("build_id",$bid)->where('status','1')->update($loseData);

                }
            }
        }
    }


  public function countPointbyfilter($eid,$filter){

         $BuildWin = 0;

        $employee_details = Employee::find($eid);
        $starttime = date('Y-m-d 00:00:00');
        $endtime  = date('Y-m-d 23:59:59');

        $monday = date("Y-m-d 00:00:00",strtotime("Monday this week"));
        $sunday = date("Y-m-d 23:59:59",strtotime("Sunday this week"));
        $firstday = date('Y-m-01 00:00:00');
        $lastday =  date('Y-m-t 23:59:59');

        $start = date('Y-m-d 00:00:00');
        $end  = date('Y-m-d 23:59:59');


        switch($filter){
	     case 'month':
	       $start = $firstday;
	       $end = $lastday;
	       break;
	     case 'week':
	       $start = $monday;
	       $end = $sunday;
	       break;
	     case 'today':
	       $start = $starttime;
	       $end = $endtime;
	       break;
	     default:
	       $start = $starttime;
	       $end = $endtime;

       }


        $BuildLose = 0 ;
        $BuildWin = 0 ;
        $BuildLose_Data = Builds::where('employee_id',$eid)->where('challenge_id',0)->where('company_id',$employee_details['company_id'])->where('updated_at','>=',$start)
            ->where('updated_at','<=',$end)->where('status','0')->get();

        $BuildWin_Data = Builds::where('employee_id',$eid)->where('challenge_id',0)->where('company_id',$employee_details['company_id'])->where('updated_at','>=',$start)
            ->where('updated_at','<=',$end)->where('status','1')->get();

    if(!empty($BuildLose_Data)){
        foreach($BuildLose_Data as $losebuild){
            if(!empty($losebuild->subcategory)){
                $lose_substr = explode(',',$losebuild->subcategory);
                $BuildLose += sizeof($lose_substr);
            }

        }
    }
    if(!empty($BuildWin_Data)){
        foreach($BuildWin_Data as $winbuild){

            if(!empty($winbuild->subcategory)){
                $win_substr = explode(',',$winbuild->subcategory);
                $BuildWin += sizeof($win_substr);
            }

        }
    }

       $BuildData = Builds::where('employee_id',$eid)->where('status','1')->where('company_id',$employee_details['company_id'])
       ->where('updated_at','>=',$start)->where('updated_at','<=',$end)->get();
        $challengeWinCount = 0;

        if($BuildData && $BuildData !=''){
            $BuildData = $BuildData->toArray();
            foreach($BuildData as $build){
                    $ChallengeWins = Challenge::where('id',$build['challenge_id'])->where('company_id',$employee_details['company_id'])->first();
                    if($ChallengeWins && $ChallengeWins != ''){

                        $challengeWinCount += $ChallengeWins->point;

                    }

                }
            }


         //duel win count


        $WinBuild = Builds::where('status','1')->where('duel_id','!=',0)->where('updated_at','>=',$start)->where('updated_at','<=',$end)->get();
        $LoseBuild = Builds::where('status','0')->where('duel_id','!=',0)->where('updated_at','>=',$start)->where('updated_at','<=',$end)->get();

        $duelwinpoints = 0 ;
        $duellosepoints = 0 ;

        //get win duel point
         foreach($WinBuild as $win_item){
             $win_duel = Duels::find($win_item->duel_id);
             if(!empty($win_duel)){
                if($win_duel->sender == $eid)
                     $duelwinpoints += $win_duel->point;
                if($win_duel->receiver == $eid)
                     $duelwinpoints += $win_duel->point;
             }
         }
         //get lose duel point
         foreach($LoseBuild as $lose_item){

             $lose_duel = Duels::find($lose_item->duel_id);

             if(!empty($lose_duel)){

                 if($lose_duel->sender == $eid)
                    $duellosepoints += $lose_duel->point;
                 if($lose_duel->receiver == $eid)
                    $duellosepoints += $lose_duel->point;

             }

         }
         // ORIGINAL FORMUAL -- TOP SECRET
        //(Build wins) - (Build losses) + (0.5 * validate wins) - (validate losses) + (Challenge Points)
        //0 - 1 + (0.5* 0) - (0) - 0 = -1
        //Tier count
        $emp_tiers = $this->getEmployeeTierModelbyFilter($eid,$start,$end);

        $TierCount = 0;
        foreach($emp_tiers as $tier){
            $TierCount += $tier['points'];
        }
        $finalCount = 0 ;
        $finalCount = $challengeWinCount - ($BuildLose) + $BuildWin + ($duelwinpoints) - $duellosepoints +$TierCount;


        $purchase_point = $this->getPurchasePointbyFilter($eid,$start,$end);

        $finalCount -= $purchase_point ;
        return $finalCount;

    }


    public function getEmployeeTierModelbyFilter($emp_id,$start,$end){

        $employee = Employee::find($emp_id);
        $TierList = array(
            'uploads'=>0,
            'challenges'=>0,
            'validates'=>0,
            'points'=>0
        );

        $Tier1 = $TierList;
        $Tier2 = $TierList;
        $Tier3 = $TierList;

        $employee_tiers = EmployeeTier::where('employee_id',$emp_id)->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        if(!empty($employee_tiers)){
            foreach($employee_tiers as $item){
                $tier = TierList::where('id',$item->tier_id)->first();
                switch($tier->tier){
                    case 1:
                        $Tier1['uploads'] += $tier->uploads;
                        $Tier1['challenges'] += $tier->challenges;
                        $Tier1['validates'] += $tier->validates;
                        $Tier1['points'] += $tier->points;
                        break;
                    case 2:
                        $Tier2['uploads'] += $tier->uploads;
                        $Tier2['challenges'] += $tier->challenges;
                        $Tier2['validates'] += $tier->validates;
                        $Tier2['points'] += $tier->points;
                        break;
                    case 3:
                        $Tier3['uploads'] += $tier->uploads;
                        $Tier3['challenges'] += $tier->challenges;
                        $Tier3['validates'] += $tier->validates;
                        $Tier3['points'] += $tier->points;
                        break;
                }

            }
        }

        $data = array(
            'tier1'=>$Tier1,
            'tier2'=>$Tier2,
            'tier3'=>$Tier3
        );

        return $data;
      }

    public function getPurchasePointbyFilter($id,$start,$end){

        $purchase = Purchase::where('employee_id',$id)->where('created_at','>=',$start)->where('created_at','<=',$end)->get();
        $emp_purchase = 0 ;
        if( $purchase != ''&& isset($purchase)){

              $purchase = $purchase->toArray();
              foreach($purchase as $item){
                     $rewarditem_id = $item['rewarditem_id'];
                     $reward = Reward::find($rewarditem_id);

                     if($reward != '' && isset($reward))
                        $emp_purchase += $reward->point;

              }
        }

        return $emp_purchase;
    }


     public function getTopRank($employeeid){
       $cur_emp = Employee::find($employeeid);
       $cur_emp->point = round($this->countPoint($employeeid));
       $com_employees = Employee::where('company_id',$cur_emp->company_id)->where('is_deleted','0')->get();
       $topemp_id = 0 ;
       $emp_data = array();
       if(!empty($com_employees)){
           foreach($com_employees as $emp){
               $emp->point = round($this->countPoint($emp->id));
               $emp_data[] = $emp;
           }

           usort($emp_data, function($a, $b) {
                return $b['point'] - $a['point'] ;
            });

           foreach($emp_data as $temp)
           {
               $topemp_id = $temp['id'];
                 break;
           }
       }
       return $topemp_id;
   }

    public function countPoint($emp){
        // Use employee object directly to reduce db call
        if (! is_object($emp)) {
            $eid = $emp;
            $employee_details = Employee::with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])->find($emp);
        } else {
            $eid = $emp->id;
            $employee_details = clone $emp;
        }

        $ValidateWin = 0;

//        $ValidateWinData = Validations::where('employee_id',$eid)->where('status','1')->get();

        $BuildLose = 0 ;
        $BuildWin = 0 ;

        if ($employee_details->relationLoaded('lostBuilds')) {
            $BuildLose_Data = $employee_details['lostBuilds'];
        } else {
            $BuildLose_Data = Builds::where('employee_id',$eid)
                ->where('company_id',$employee_details['company_id'])
                ->where('challenge_id',0)
                ->where('status','0')
                ->get();
        }

        if ($employee_details->relationLoaded('wonBuilds')) {
            $BuildWin_Data = $employee_details['wonBuilds'];
        } else {
            $BuildWin_Data = Builds::where('employee_id',$eid)
                ->where('company_id',$employee_details['company_id'])
                ->where('challenge_id',0)
                ->where('status','1')
                ->get();
        }

         if(!empty($BuildLose_Data)){
        foreach($BuildLose_Data as $losebuild){
            $lose_substr = explode(',',$losebuild->subcategory);
            $BuildLose += sizeof($lose_substr);     ///
        }}
        if(!empty($BuildWin_Data)){
        foreach($BuildWin_Data as $winbuild){
            $win_substr = explode(',',$winbuild->subcategory);
            $BuildWin += sizeof($win_substr);
        }
        }

        // Eager load "challenge" relation to prevent n+1 query issue
        if ($employee_details->relationLoaded('allBuilds')) {
            $BuildData = $employee_details['allBuilds'];
        } else {
            $BuildData = Builds::with(['challenge'])
                ->where('employee_id',$eid)
                ->where('status','1')
                ->where('company_id',$employee_details['company_id'])
                ->get();
        }

        $challengeWinCount = 0;

        if(count($BuildData) > 0){
//            $BuildData = $BuildData->toArray();
            foreach($BuildData as $build){
                if($build->employee_id == $eid){
//                    $ChallengeWins = Challenge::where('id',$build['challenge_id'])->where('company_id',$employee_details['company_id'])->first();
                    $ChallengeWins = $build->challenge;
                    if($ChallengeWins){
                        $challengeWinCount += $ChallengeWins->point;
                    }

                }
            }
        }

        //duel win count


        $WinBuild = Builds::with(['duel'])->where('status','1')->where('duel_id','!=',0)->get();
        $LoseBuild = Builds::with(['duel'])->where('status','0')->where('duel_id','!=',0)->get();

        $duelwinpoints = 0 ;
        $duellosepoints = 0 ;

        //get win duel point
         foreach($WinBuild as $win_item){
             $win_duel = $win_item->relationLoaded('duel')
                 ? $win_item->duel
                 : Duels::find($win_item->duel_id);

             if(!empty($win_duel)){
                if($win_duel->sender == $eid)
                     $duelwinpoints += $win_duel->point;
                if($win_duel->receiver == $eid)
                     $duelwinpoints += $win_duel->point;
             }
         }
         //get lose duel point
         foreach($LoseBuild as $lose_item){
             $lose_duel = $lose_item->relationLoaded('duel')
                 ? $lose_item->duel
                 : Duels::find($lose_item->duel_id);

             if(!empty($lose_duel)){

                 if($lose_duel->sender == $eid)
                    $duellosepoints += $lose_duel->point;
                 if($lose_duel->receiver == $eid)
                    $duellosepoints += $lose_duel->point;

             }

         }

        //Tier count
        $emp_tiers = $this->getEmployeeTierModel($eid);
        $TierCount = 0;
        foreach($emp_tiers as $tier){
            $TierCount += $tier['points'];
        }

        $finalCount = 0 ;

        //$finalCount = $BuildWin - $BuildLose  +(0.5 *$ValidateWin )-$ValidateLose+ $challengeWinCount + ($duelwinpoints-1) - $duellosepoints +$TierCount;
        // $finalCount = $BuildWin - $BuildLose  + $challengeWinCount + ($duelwinpoints) - $duellosepoints +$TierCount;
        $finalCount = $challengeWinCount - ($BuildLose) + $BuildWin + ($duelwinpoints) - $duellosepoints +$TierCount;

        $purchase_point = $this->getPurchasePoint($eid);

        $finalCount -= $purchase_point ;


        return $finalCount;
    }

    /* This API use For calculatePoints */
    public function calculatePoints(Request $request){
        $userDataRequest = $request['userData'];
        $count = $this->countPoint($userDataRequest['id']);

        //$count += 100 ;
       // echo json_encode(array('status'=>true,'points'=>number_format($count,2)));die;
       echo json_encode(array('status'=>true,'points'=>strval($count)));die;
    }
    /* END */

    /* This API use For deleteProfile */
    public function deleteProfile(Request $request){
        $userDataRequest = $request['userData'];
        $authTokes = Authtoken::where('user_id',$userDataRequest)->get();
        if($authTokes != ''){
            $authTokes = $authTokes->toArray();
            foreach($authTokes as $tokes){
                $tokens = Authtoken::find($tokes['id']);
                $tokens->delete();
            }
        }
        $Employee = Employee::find($userDataRequest['id']);
        $Employee->is_deleted = '1';
        $Employee->save();
        echo json_encode(array('status'=>true,'msg'=>'Your Account Successfully Deleted.'));die;
    }
    /* END */

    /* This API use For logOutEmployee */
    public function logoutEmployee(Request $request) {
        $token = str_replace("Bearer ", "", $request->header('Authorization'));
        $authTokes = Authtoken::where('token', $token )->first();
        if($authTokes != '') {
            $uuRow = Useruuid::where('token_id',$authTokes->id )->first();
            if($uuRow != '') {
                $uuRow->delete();
            }
            $authTokes->delete();
        }
        echo json_encode(array('status'=>1,'msg'=>'Logout Success'));die;
    }
    /* END */

    /* This API use For get Resume By Employee ID */
    public function getResume(Request $request){
        //$userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'employee_id' => $request->input( 'employee_id' )

            ),
            array(
                'employee_id' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
            $eid = $request->employee_id;
            $Employee = Employee::find($eid);
            if($Employee == ''){
                echo json_encode(array('status'=>false,'msg'=>'Invalid Employee'));die;
            }
            $tmpFolder = 'resumes/';
            // File::isDirectory($tmpFolder) or File::makeDirectory($tmpFolder, 0777, true, true);
            $url = url('employee/get-resume/'.$eid);
            exec('wkhtmltopdf '.$url.' '.$tmpFolder.$eid.'_'.'resume.pdf');
            $url_d = Storage::disk("s3")->url('resumes/'.$eid.'_'.'resume.pdf');
            $file = $tmpFolder.$eid.'_'.'resume.pdf';
            echo json_encode(array('status'=>true,'link'=>$url_d));die;
        }

    }
    /* END */

    /* This API use For get Resume By Employee ID */
    public function getResumeApi(Request $request){
        $uiDate = $request->uidate;
        if($uiDate === ''){
            echo json_encode(array('status'=>false,'msg'=>'Please Enter Value'));die;
        } else if(strpos($uiDate,"UT-") !== 0 || strlen($uiDate) !== 14){
            echo json_encode(array('status'=>false,'msg'=>'Please Enter Valid Value'));die;
        }

       // echo json_encode(array('status'=>false,'msg'=>'invalid user111'));die;

        $uiDate = str_replace("UT-","",$uiDate);
        $employees = Employee::where('is_deleted','0')->get();
        $Employee = array();
        foreach($employees as $item){
           $create_date = $item->created_at;
           $utcode = date("dmy-is", strtotime($create_date));
           if($utcode == $uiDate){
             $Employee = $item->toArray();
             break;
           }
        }

        if(empty($Employee)){
            echo json_encode(array('status'=>false,'msg'=>'invalid user'));die;
        }
        else{

           $tmpFolder = 'resumes/';
           $eid = $Employee['id'];
            // File::isDirectory($tmpFolder) or File::makeDirectory($tmpFolder, 0777, true, true);
            $url = url('executive/employee/resume/'.$Employee['id']);
            $url_d = url('/wix-employeeportfolio/'.$eid);
            echo json_encode(array('status'=>true,'link'=>$url_d));die;

        }
    }
    /* END */

    /* This API use For GetTenure */
    public function getTenure(Request $request){
        $id = 0;
        if(!$request->header('Authorization')){
            if($request->input('id')){
                $get_param = array('id'=>$request->input('id'));
            } else if($request->input('email')){
                $get_param = array('email'=>$request->input('email'));
            } else {
                echo json_encode(array('status'=>false,'msg'=>'Something went wrong.'));die;
            }
            $checkUser = $this->checkUserByIdOrEmail($get_param);
        } else {
            $APIAuth = new APIAuth;
            $token = str_replace("Bearer ","",$request->header('Authorization'));
            $checkUser = $APIAuth->checkAuthCode($token);
        }
        if($checkUser != '' || $checkUser != false) {
            $id = $checkUser['data']['id'];
        }else{
            echo json_encode(array('status'=>false,'msg'=>'Authorization Failed.'));die;
        }
        $getTenure = Tenure::select('id','created_at','employee_id')->where('employee_id',$id)->get()->toArray();
        if($getTenure){
            echo json_encode(array('status'=>true,'data'=>$getTenure));die;
        }else{
            echo json_encode(array('status'=>true,'msg'=>'No Tenure Found'));die;
        }

    }
    /* END */

    /* Get user from id or email */
    public function checkUserByIdOrEmail($param,$all = 0){
        if($all == 1){
            $Employee_array = Employee::all()->toArray();
            $userData = array();
            if(!empty($Employee_array)){
                foreach($Employee_array as $emp){
                    $userData[] = array(
                        'id'=>$emp['id'],
                        'full_name'=>$emp['full_name'],
                        'email'=>$emp['email'],
                        'company_id'=>$emp['company_id'],
                        'industry'=>$emp['industry'],
                        'phone_number'=>$emp['phone_number'],
                        'website' => $emp['website'],
                        'created_at'=>$emp['created_at'],
                        'updated_at'=>$emp['updated_at'],
                    );
                }
            }
            return $userData;
        }
        if(isset($param['id'])){
            $Employee = Employee::where("id",'=',$param['id'])->first();
        } else if(isset($param['email'])){
            $Employee = Employee::where("email",'=',$param['email'])->first();
        } else {
            return false;
        }
        if($Employee){
            $userData = array(
                'id'=>$Employee->id,
                'full_name'=>$Employee->full_name,
                'email'=>$Employee->email,
                'company_id'=>$Employee->company_id,
                'industry'=>$Employee->industry,
                'phone_number'=>$Employee->phone_number,
                'website' => $Employee->website,
            );
            return array('status'=>true,'data'=>$userData);
        } else {
            return false;
        }
    }
    /* END */

    /* This API use For GetEmployeeFromCompany */

    public function getEmployeeFromCompany(Request $request){

         $id = 0;

          $userDataRequest = $request['userData'];
          $emp_id= $userDataRequest['id'];
          $employee = Employee::where('id',$emp_id)->first();

        $employee_company = Employee::where("company_id",$employee->company_id)
        ->where("is_deleted",'0')
        // ->where('industry',$employee->industry)
        ->where('access_level','<=',$employee->access_level)
        ->get()->toArray();

         $filter = $request->input('filter');
        $emp_data = [];
        $data = array();
        if($employee_company) {

            foreach ($employee_company as $item) {

                $emp_data = array();
                $emp_data = $item;

                $user = Users::select('name')->where('id', $item['company_id'])->first();
                $emp_data['company_name'] = '--';
                if($user && !empty($user) && $user->name != ''){
                    $emp_data['company_name'] = $user->name;
                }
                $industry= Industry::where('id',$item['industry'])->first();

//                $emp_data['image'] = $item['image'] != '' ? Storage::disk("s3")->url('images/employee').'/'.$item['image'] : '';
                $emp_data['image'] = $item['image'];
                $emp_data['industry'] = $industry;
                $emp_data['point'] = $this->countPointbyfilter($item['id'],$filter);
                $emp_data['rank'] = 0;
                $data[] = $emp_data;

            }

            usort($data, function($a, $b) {
                return $b['point'] - $a['point'] ;
            });


            echo json_encode(array('status'=>true,'data'=>$data));die;
        }else{
            echo json_encode(array('status'=>true,'data'=>$data));die;
        }
    }
    /* END */

    /* This API use For GetEmployee */
    public function getEmployee(Request $request){
        $id = 0;
        if($request->input('id')){
            $get_param = array('id'=>$request->input('id'));
        } else if($request->input('email')){
            $get_param = array('email'=>$request->input('email'));
        }else{
            $checkUser = $this->checkUserByIdOrEmail('',1);
            echo json_encode(array('status'=>true,'data'=>$checkUser));die;
        }
        $checkUser = $this->checkUserByIdOrEmail($get_param);

        if($checkUser != '' || $checkUser != false) {
            $emp_data = $checkUser['data'];
            $user = Users::select('name')->where('id', $emp_data['company_id'])->first();
            $emp_data['company_id'] = '--';
            if($user && !empty($user) && $user->name != ''){
                $emp_data['company_id'] = $user->name;
            }
            $categories = Categories::select('category_name')->where('id',$emp_data['industry'])->first();
            $emp_data['industry'] = '--';
            if($categories){
                $emp_data['industry'] = $categories->category_name;
            }
            echo json_encode(array('status'=>true,'data'=>$emp_data));die;
        }else{
            echo json_encode(array('status'=>false,'msg'=>'No Employee Found.'));die;
        }
    }
    /* END */

    /*  Get All Challenges */

    public function getAllchallenges(Request $request){


        $challenges = Challenge::all()->toArray();

        $status = $request->status;

        $index = $request->index;

        if(!empty($index)){


                    if($request->input('company_id')){

                        if($request->input('category_id')){
                            $challenges = Challenge::where('company_id',$request->input('company_id'))->where('category_id', $request->input('category_id'))->get()->toArray();
                        }
                        else{
                            $challenges = Challenge::where('company_id',$request->input('company_id'))->get()->toArray();
                        }

                    }

                    else if($request->input('category_id')){
                        $challenges = Challenge::where('category_id', $request->input('category_id'))->get()->toArray();
                    }


                    if(empty($challenges)){
                        echo json_encode(array('status'=>true,'data'=>array()));die;
                    }

                    $challenge_array = $challenge_array2 = array();

                    $i = 0 ;

                    foreach($challenges as $k=>$chal){

                        if($i > $index*20 && $i <= $index*20+20 ){

                                $chal_point = $chal['point'];
                                $chal['point'] = "$chal_point";
                                if(isset($status) && ($status === $chal['status'])){
                                    $chal['image'] =  $chal['image'] != '' ? Storage::disk("s3")->url('/images/challenge').'/'.$chal['image'] : '';
                                    $challenge_array[] = $chal;
                                }
                                $chal['image'] =  $chal['image'] != '' ? Storage::disk("s3")->url('/images/challenge').'/'.$chal['image'] : '';
                                $chal['preset_type'] = $chal['preset_type'];
                                $challenge_array2[] = $chal;
                        }
                        $i++;
                    }


                    if(isset($status)){
                        $challenges_c = $challenge_array;
                        echo json_encode(array('status'=>true,'data'=>$challenge_array));die;
                    }
                    else{
                        $challenges_c = $challenge_array2;
                        echo json_encode(array('status'=>true,'data'=>$challenge_array2));die;
                    }
            }

            else{

                 if($request->input('company_id')){

                        if($request->input('category_id')){
                            $challenges = Challenge::where('company_id',$request->input('company_id'))->where('category_id', $request->input('category_id'))->get()->toArray();
                        }
                        else{
                            $challenges = Challenge::where('company_id',$request->input('company_id'))->get()->toArray();
                        }

                    }

                    else if($request->input('category_id')){
                        $challenges = Challenge::where('category_id', $request->input('category_id'))->get()->toArray();
                    }


                    if(empty($challenges)){
                        echo json_encode(array('status'=>true,'data'=>array()));die;
                    }

                    $challenge_array = $challenge_array2 = array();


                    foreach($challenges as $k=>$chal){

                        $chal_point = $chal['point'];
                        $chal['point'] = "$chal_point";
                        if(isset($status) && ($status === $chal['status'])){
                            $chal['image'] =  $chal['image'] != '' ? Storage::disk("s3")->url('/images/challenge').'/'.$chal['image'] : '';
                            $challenge_array[] = $chal;
                        }
                        $chal['image'] =  $chal['image'] != '' ? Storage::disk("s3")->url('/images/challenge').'/'.$chal['image'] : '';
                        $chal['preset_type'] = $chal['preset_type'];
                        $challenge_array2[] = $chal;
                    }



                    if(isset($status)){
                        $challenges_c = $challenge_array;
                        echo json_encode(array('status'=>true,'data'=>$challenge_array));die;
                    }
                    else{
                        $challenges_c = $challenge_array2;
                        echo json_encode(array('status'=>true,'data'=>$challenge_array2));die;
                    }
            }

    }
    /* END */

public function checkValidate($build,$Validations,$emp_id){

   $is_exist = false;
   foreach ($Validations as $item) {

       if($build['id']==$item['build_id'] && $emp_id == $item['employee_id']){
           $is_exist = true;

           break;
       }
   }

   return $is_exist;

}

public function getArray($arr){

   $result = array();
   foreach($arr as $item){
      $result[] = $item['build_id'];
   }

   return $result;
}

public function addArray($arr1,$arr2){

  $result = array();
  foreach($arr1 as $item1){
   $result[] = $item1;
  }
  foreach($arr2 as $item2){
    $result[] = $item2;
  }
  return $result;
}


public function checkduel($emp_id,$duel_id){
    $flag = false;
    $duel = Duels::find($duel_id);

   if(!empty($duel)&& $duel!= ""){
         if($duel->sender == $emp_id || $duel->receiver == $emp_id) $flag = true;
    }

   return $flag;

}



/*  Get All Builds  validation */
    public function getAllbuilds(Request $request){

       // echo json_encode(array('status'=>true,'data'=>$result));die;

        $userDataRequest = $request['userData'];
        $emp_id = $userDataRequest['id'];


        $current_emp = Employee::find($emp_id);
        $Validations = Validations::where('employee_id',$userDataRequest['id'])->get()->toArray();


        $company  = $this->get_CompanyInfo_fromID($current_emp->company_id);
        $industry = $this->get_IndustryInfo_fromID($current_emp->industry);



        $result_arr = array();
        $Builds = array();


        $BuildData = Builds::where('company_id',$userDataRequest['company_id'])->whereIn('status', ['-1','0'])->limit(20)->orderBy('created_at','desc')->get();


        if(!empty($BuildData)){
            foreach($BuildData as $builditem){
                $build_emp = Employee::find($builditem->employee_id);
                if(!empty($build_emp)){
                    if($build_emp->access_level <= $current_emp->access_level){
                            $Builds[] = $builditem;
                    }
                }
            }
        }


        if(!empty($Builds)){
          foreach($Builds as $item){

             if(!$this->checkValidate($item ,$Validations,$userDataRequest['id'])){

	          if(!$this->checkduel($emp_id,$item['duel_id'])){

                       $Build = array();
		                //build employee
                        $build_emp = Employee::find($item['employee_id']);
                        $build_emp->industry = $industry;
                        $build_emp->company_instance = $company;
//                        $build_emp->image = $build_emp->image != '' ? Storage::disk('s3')->url('/images/employee/').$build_emp->image : '';
                        $Build['employee'] = $build_emp;
                        //build challenge
                        $challenge = '';
                        if($item['challenge_id'] != 0){
                            $challenge = Challenge::find($item['challenge_id']);
                            if(!empty($challenge)){
                                if(Storage::disk("s3")->exists('images/challenge/'.$challenge->image)){
                                    $challenge->image = $challenge->image != '' ? Storage::disk('s3')->url('/images/challenge/').$challenge->image : '';
                                    $challenge->company_instance = $company;
                                    $challenge->sub_categories = $this->getSubCategories($challenge->subcategory);
                                    $Build['challenge'] = $challenge;
                                }

                            }
                        }

                       if ($current_emp['userType'] == "1") {
                            $Build['sub_categories'] = $this->getSubCategories($item['subcategory']);
                            $Build['image'] = $item['image'] != '' ? Storage::disk('s3')->url('/images/build/').$item['image'] : '';
                            $Build['id'] = $item['id'];
                            $Build['build_text'] = $item['build_text'];
                            $Build['status'] = $item['status'];
                            $created_at = $item['created_at']->format('Y-m-d H:i:s');
                            $Build['created_at'] = $created_at;
                            $updated_at = $item['updated_at']->format('Y-m-d H:i:s');
                            $Build['updated_at'] = $updated_at;
                            $Build['company_id'] = $item['company_id'];
                            $result_arr[] = $Build;
                        } else if ($emp_id == $item['employee_id']) {
                            $Build['sub_categories'] = $this->getSubCategories($item['subcategory']);
                            $Build['image'] = $item['image'] != '' ? Storage::disk('s3')->url('/images/build/').$item['image'] : '';
                            $Build['id'] = $item['id'];
                            $Build['build_text'] = $item['build_text'];
                            $Build['status'] = $item['status'];
                            $created_at = $item['created_at']->format('Y-m-d H:i:s');
                            $Build['created_at'] = $created_at;
                            $updated_at = $item['updated_at']->format('Y-m-d H:i:s');
                            $Build['updated_at'] = $updated_at;
                            $Build['company_id'] = $item['company_id'];
                            $Build['batch_id'] = $item['batch_id'];
                            // Upon batch id, will get the emailto email and full name
                            $id = $item['batch_id'];
                            $batch = Batch::where('id', $id)->first();

                            $toFullName = "";
                            $toEmail = "";

                            if ($batch) {
                               $toFullName = $batch->firstname . ' ' . $batch->lastname;
                               $toEmail = $batch->email_to;
                            }


                            $Build['toFullName'] = $toFullName;
                            $Build['toEmail'] = $toEmail;
                            $result_arr[] = $Build;
                        }




                  }
                }
             }
          }

        if ($current_emp['userType'] == "1") { // employee
              $categories = $this->getCategory($current_emp);
              $readitems = $this->getReadItems($current_emp);
              $result = array(
                  'builds'=>$result_arr,
                  'categories'=>$categories,
                  'readitems'=>$readitems
              );
              echo json_encode(array('status'=>true,'data'=>$result));die;
          } else { // independent
              $categories = $this->getMainCategory($current_emp);
              $readitems = $this->getReadItems($current_emp);
              $result = array(
                  'builds'=>$result_arr,
                  'categories'=>$categories,
                  'readitems'=>$readitems
              );
              echo json_encode(array('status'=>true,'data'=>$result));die;
          }
}
    /* END */
    /* GET ALL COMPANIES  */
    public function getAllcompanies(Request $request){

        $Companies = Users::where('role','company')->get()->toArray();


        if(empty($Companies)){
            echo json_encode(array('status'=>true,'data'=>array()));die;
        }
        $Companie = array();
        foreach($Companies as $k=>$chal){
            $Companie[$k] = $chal;
            unset($Companie[$k]['password']);
            unset($Companie[$k]['remember_token']);
            unset($Companie[$k]['last_name']);

            $Companie[$k]['company_name'] =  $chal['first_name'];
            unset($Companie[$k]['first_name']);
            $Companie[$k]['pic'] =  $chal['pic'] != '' ? Storage::disk("s3")->url('images/user').'/'.$chal['pic'] : '';
        }
        $Companies = $Companie;
        echo json_encode(array('status'=>true,'data'=>$Companies));die;
    }
    /* END */

    public function getTenurebymonth(Request $request){
        $userDataRequest = $request['userData'];
        $allmonth = 11;
        $monthss = array();
        for($month = 0;$month <= $allmonth;$month++  ){
            $mm_t = $month - 1 ;
            $my_array[] = date("F", strtotime( date( 'Y-m-01' )." -$month months"));
            $date_s =  date("Y-m-d h:i:s", strtotime( date( 'Y-m-01' )." -$month months"));
            $date_e = date("Y-m-d h:i:s", strtotime( date( "Y-m-01" )." -$mm_t months"));

            $monthss[ date("F", strtotime( date( 'Y-m-01' )." -$month months"))] = array('total'=>date("t", strtotime( date( 'Y-m-d' )." -$month months")));


            //$date_s = date("Y-m-d h:i:s",strtotime(date("Y-$month-01 00:00:00")));
            // $date_e = date("Y-m-d h:i:s",strtotime(date("Y-$mm_t-01 00:00:00")));
            //$data_d[] = Builds::where('employee_id',$eid)->whereBetween('created_at', [$date_s, $date_e ])->count();
        }

        $newArray = array();
        foreach($monthss as $k=>$month){
            $nmonth = date("m", strtotime($k));
            $totalDays = $month['total'];
            $startDate = date("y-$nmonth-01");
            $endDate = date("y-$nmonth-$totalDays");
            $countData = Tenure::where('employee_id',$userDataRequest['id'])->whereBetween('created_at', [$startDate, $endDate ])->count();
            //print_r($countData);die;
            $endCount =  $totalDays - $countData;
            $newArray[$k] = array('total' => (int)$totalDays, 'active'=>$countData,'inactive'=>$endCount);
        }
        echo json_encode(array('status'=>true,'data'=>$newArray));die;
    }

    /* This API use For deleteBuild */
    public function deleteBuild(Request $request){
        $userDataRequest = $request['userData'];
        $validation = Validator::make(
            array(
                'build_id' => $request->input( 'build_id' )
            ),
            array(
                'build_id' => array( 'required' )
            )
        );
        $errors = '';
        if ( $validation->fails() ) {
            $errors = $validation->messages();
            $errors->toJson();
            echo json_encode(array('status'=>false,'msg'=>$errors));die;
        }else{
            $builds = Builds::where('id', $request->input('build_id'))->where('employee_id',$userDataRequest['id'])->first();
            if($builds && $builds != ''){
                if($builds->image != ''){
                    $filename = 'images/build/'.$builds->image;
                    Storage::disk("s3")->delete($filename);
                    // if(File::exists($filename)){
                    //     File::delete($filename);
                    // }
                }
                if($builds->challenge_id != 0){
                    $this->changChallangeStatus($builds->challenge_id, '0');
                }
                $builds->delete();
                echo json_encode(array('status'=>true,'msg'=>'Your Upload Successfully Deleted.'));die;
            } else {
                echo json_encode(array('status'=>false,'msg'=>'No Uploads Found.'));die;
            }
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
            Challenge::find($id)->update($ch_data);
        }
    }
    /*
        Imagerotate replacement. ignore_transparent is work for png images
        Also, have some standard functions for 90, 180 and 270 degrees.
        Rotation is clockwise
    */

    // function rotetImage($imageName) {
    //     $image = new Imagick();
    //     $image_filehandle = fopen($imageName, 'a+');
    //     $image->readImageFile($image_filehandle );
    //     switch ($image->getImageOrientation()) {
    //         case Imagick::ORIENTATION_TOPLEFT:
    //             break;
    //         case Imagick::ORIENTATION_TOPRIGHT:
    //             $image->flopImage();
    //             break;
    //         case Imagick::ORIENTATION_BOTTOMRIGHT:
    //             $image->rotateImage("#000", 180);
    //             break;
    //         case Imagick::ORIENTATION_BOTTOMLEFT:
    //             $image->flopImage();
    //             $image->rotateImage("#000", 180);
    //             break;
    //         case Imagick::ORIENTATION_LEFTTOP:
    //             $image->flopImage();
    //             $image->rotateImage("#000", -90);
    //             break;
    //         case Imagick::ORIENTATION_RIGHTTOP:
    //             $image->rotateImage("#000", 90);
    //             break;
    //         case Imagick::ORIENTATION_RIGHTBOTTOM:
    //             $image->flopImage();
    //             $image->rotateImage("#000", 90);
    //             break;
    //         case Imagick::ORIENTATION_LEFTBOTTOM:
    //             $image->rotateImage("#000", -90);
    //             break;
    //         default: // Invalid orientation
    //             break;
    //     }
    //     $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    //     return $image;
    // }

    /* This function used for call push notification when users build approved */
    public function approveandrejectBuild($bid,$status){

        if($bid && $bid != ''){
            $build_data = Builds::where('id',$bid)->first();


            if(!empty($build_data)&& $build_data != ''){
                $employee_validates = Validations::where('build_id',$bid)->get();
                $data = [];

                $emp = Employee::where("id",$build_data->employee_id)->where("is_deleted",'0')->first();
                //echo json_encode(array('status'=>true,'msg'=>$emp->full_name));
                // foreach($employee_validates as $emp_val){
                    // $emp = Employee::where('id',$emp_val->employee_id)->where('is_deleted','0')->first();
                    if(!empty($emp)){
                        $message = '';
                        if($status == '1'){
                             $message = "Congratulations, ".$emp->full_name.". Your Submission ".$build_data->build_text." has been approved";
                             $this->sendpush($emp->id,'Submission Approved',$message,$data,'buildApprove');
                            // echo json_encode(array('status'=>true,'msg'=>'approve'));
                        }
                        if($status == '0'){
                            $message = "Sorry, ".$emp->full_name.". Your Submission ".$build_data->build_text." has been rejected";
                            $this->sendpush($emp->id,'Submission rejected',$message,$data,'buildReject');
                            //echo json_encode(array('status'=>true,'msg'=>'reject'));
                        }
                        $list_param = array(
                              'content_type'=>5,
                              'message' => $message,
                              'sender'=>$build_data->company_id,
                              'receiver'=>$emp->id,
                              'receiver_type'=>3
                            );
                        Notification::create($list_param);
                    }
                // }
            }
        }
        //return;
    }
    /* END */


    /* This function for nush notification */
    // public function mobilesendpush($uid,$title,$message,$SenderUid,$data,$pic,$type){
        public function mobilesendpush(Request $request){

            $UUIDCount = Useruuid::where('employee_id',$request->uid)->count();

            if($UUIDCount > 0){
                if(!defined('API_ACCESS_KEY')){
                        define( 'API_ACCESS_KEY', 'AAAAcMvfsJM:APA91bEmaR9tV8sFf7su07qAglrtWmpxb-9tyn5Rsf6FG0b5CgXjiVEhD-HuSxklFOIlF7Le6KTih3BldNvDfjIJGtXhaUquxhnC-JmekqZBdYlZLhNmk1HX1NGHLBr8GYgmkP_g8ntu');
                }

                $userUuids = Useruuid::where('employee_id',$request->uid)->get()->toArray();
                $registrationIds = array();
                foreach($userUuids as $uk=>$uv){
                        $registrationIds[] = $uv['uu_id'];
                }

                $result = array();
                $msg = array(
                    'message'   => $request->message,
                    'title'     => $request->title,
                    //'subtitle'    => $title,
                    'body'      => $request->message,
                    // 'badge'     => $userBagdeCount,
                    "sound"     => "default",
                    //'show_in_foreground'=>true,
                    //"vibrate" => 100,
                    'vibrate'   => 1,
                    'vibration' => 300
                );

                $data = [
                        "title"=>$request->title,
                        "message"   =>  $request->message,
                        // "otherUId"=>$SenderUid,
                        "image"     =>  url('/images/48X48.jpg'),
                        'type'      =>  'employee',
                        'action'    => isset($data['action'])?$data['action']:0,
                        // 'imgSrc'    =>  $pic,
                        // 'ctype'     =>  $data['ctype'],
                        // 'adId'       =>  $data['adid'],
                        // 'badge'     => $userBagdeCount,
                         'clearBadge' => true,
                        // "sound"=> "www/sounds/regular.mp3",
                         //"vibration" => 1
                ];
                if(count($registrationIds) > 0){

                    $fields = array
                    (
                        'registration_ids' => $registrationIds,
                        'notification' => $msg,
                        'data'=> $data,
                        'priority'=> 'high',
                        'content_available'=> true,
                        'show_in_foreground'=>true,
                    );

                    $headers = array(
                        'Authorization: key=' . API_ACCESS_KEY,
                        'Content-Type: application/json'
                    );
                    $ch = curl_init();
                    //curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                    // echo 'Sending push to Android <br>'.PHP_EOL;
                    $result[] = curl_exec($ch);
                    curl_close( $ch );
                }
                return $result;
            }else{
                return 'No push sent';
            }
        }
        /* END */




    /* This function for nush notification */
    // public function sendpush($uid,$title,$message,$SenderUid,$data,$pic,$type){
    public function sendpush($uid,$title,$message,$data,$type){

        $UUIDCount = Useruuid::where('employee_id',$uid)->count();

        if($UUIDCount > 0){
            if(!defined('API_ACCESS_KEY')){
                    define( 'API_ACCESS_KEY', 'AAAAcMvfsJM:APA91bEmaR9tV8sFf7su07qAglrtWmpxb-9tyn5Rsf6FG0b5CgXjiVEhD-HuSxklFOIlF7Le6KTih3BldNvDfjIJGtXhaUquxhnC-JmekqZBdYlZLhNmk1HX1NGHLBr8GYgmkP_g8ntu');
            }


            $userUuids = Useruuid::where('employee_id',$uid)->get()->toArray();
            $registrationIds = array();
            foreach($userUuids as $uk=>$uv){
                    $registrationIds[] = $uv['uu_id'];
            }

            $result = array();
            $msg = array(
                'message'   => $message,
                'title'     => $title,
                //'subtitle'    => $title,
                'body'      => $message,
                // 'badge'     => $userBagdeCount,
                "sound"     => "default",
                //'show_in_foreground'=>true,
                //"vibrate" => 100,
                'vibrate'   => 1,
                'vibration' => 300
            );

            $data = [
                    "title"=>$title,
                    "message"   =>  $message,
                    // "otherUId"=>$SenderUid,
                    "image"     =>  url('/images/48X48.jpg'),
                    'type'      =>  $type,
                    'action'    => isset($data['action'])?$data['action']:0,
                    // 'imgSrc'    =>  $pic,
                    // 'ctype'     =>  $data['ctype'],
                    // 'adId'       =>  $data['adid'],
                    // 'badge'     => $userBagdeCount,
                     'clearBadge' => true,
                    // "sound"=> "www/sounds/regular.mp3",
                     //"vibration" => 1
            ];
            if(count($registrationIds) > 0){

                $fields = array
                (
                    'registration_ids' => $registrationIds,
                    'notification' => $msg,
                    'data'=> $data,
                    'priority'=> 'high',
                    'content_available'=> true,
                    'show_in_foreground'=>true,
                );

                $headers = array(
                    'Authorization: key=' . API_ACCESS_KEY,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                //curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                // echo 'Sending push to Android <br>'.PHP_EOL;
                $result[] = curl_exec($ch);
                //  $result['status'] = true;
                //  echo json_encode($result);die;
                // print_r($result);
                curl_close( $ch );
            }
            return $result;
        }else{
            return 'No push sent';
        }
    }
    /* END */

    /* This function used for call push notification when new challenge is created */
    public function newChallenge($challenge){
        if(!empty($challenge)){
            $emp_list = Employee::select('id','full_name')->where("company_id",$challenge->company_id)->where("is_deleted",'0')->get()->toArray();
            if(!empty($emp_list)){
                $path = storage_path() . '/../public/js/notification.json';
                $json = json_decode(file_get_contents($path), true);
                $message = $json['newChallenge'];
                $data = [];
                if($message != ''){

                    $message = str_replace("{{CHALLENGENAME}}",$challenge->challenge_text,$message);

                } else {
                    $message = "There is a new challenge available, ".$challenge->challenge_text.".";
                }


                  $list_param = array(
                  'content_type'=>4,
                  'message' => $message,
                  'sender'=>$challenge->employee_id,
                  'receiver'=>$challenge->company_id,
                  'receiver_type'=>2
              );
               Notification::create($list_param);

            }
        }
        return;
    }
    /* END */

    /* This function used for call push notification when catgory add/update/deleted from company or admin */
    public function categoryMaintain($id,$type){
        if($id && $id != ''){
            $categories = Categories::where('id',$id)->first();
            if(!empty($categories)){
                $path = storage_path() . '/../public/js/notification.json';
                $json = json_decode(file_get_contents($path), true);
                $message = $json['category'];
                $data = [];
                $emp_list = Employee::select('id','full_name')->where("company_id",$categories->company_id)->where("is_deleted",'0')->get()->toArray();
                if(!empty($emp_list))
                {
                    $company = Users::where('id',$categories->company_id)->first();

                    if($message != ''){
                        //$message = str_replace("{{EMPNAME}}",$emp['full_name'],$message);
                        $message = str_replace("{{CATACTION}}",$type,$message);
                        $message = str_replace("{{CATNAME}}",$categories->category_name,$message);
                    } else {
                        $message = $company->name." has ".$type." new category,".$categories->category_name.".";
                    }
                    foreach($emp_list as $emp){

                        //$title = 'New Category';
                        $title = '';
                        if($type == 'updated'){
                            $title = 'Category Update';
                        } else if($type == 'deleted'){
                            $title = 'Category Delete';
                        }
                        $this->sendpush($emp['id'],$title,$message,$data,'category');
                    }
                    if ($type != 'updated') {
                       /* $list_param = array(
                            'type'=>'category',
                            'send_to' => $categories->company_id,
                            'message' => $message
                        );
                        Notification::create($list_param);
                        */
                    }


                  if($type == 'updated'){
                    $message1 = $json['categoryUpdate'];
                    if($message1 != ''){
                        $message1 = str_replace("{{CATACTION}}",$type,$message1);
                        $message1 = str_replace("{{CATNAME}}",$categories->category_name,$message1);
                    } else {
                        $message1 = $company->name." "."updated a category ".$categories->category_name.".";
                    }
                    /*
                    $list_param = array(
                       'type'=>'category',
                       'send_to' => $categories->company_id,
                       'message' => $message1
                    );
                    Notification::create($list_param);
                    */
                }
            }
         }
        }
        return;
    }
    /* END */

    /* This function used for call push notification when user top 5 spot in leaderboard and When a employee hits 50 point increments */

    /* END */

    /* Check the win build with same category count and create antry in notification list */
    public function checkBuildWithCategory($bid){
        if($bid && $bid != ''){
            $build_data = Builds::select('employee_id', 'company_id', 'category_id')->where('id',$bid)->first();
            if(!empty($build_data) && $build_data != ''){
                $win_cont = Builds::where('company_id',$build_data->company_id)->where('category_id',$build_data->category_id)->where('status','1')->count();
                if($win_cont > 0){
                    $div_val = $win_cont/10;
                    if((floor($div_val) == $div_val)){
                        $employee = Employee::select('full_name')->where('id',$build_data->employee_id)->where('is_deleted','0')->first();
                        $category = Categories::select('category_name')->where('id', $build_data->category_id)->first();
                        if(!empty($employee) && $employee != ''){
                            $path = storage_path() . '/../public/js/notification.json';
                            $json = json_decode(file_get_contents($path), true);
                            $message = $json['winBuildWithCategory'];
                            if($message != ''){
                                $message = str_replace("{{EMPNAME}}",$employee->full_name,$message);
                                $message = str_replace("{{CATNAME}}",$category->category_name,$message);
                                $message = str_replace("{{COUNT}}",$div_val,$message);
                            } else {
                                $message = $employee->full_name. " hits ".$div_val." Submission approved with ".$category->category_name." category.";
                            }
                            $list_param = array(
                  			'content_type'=>5,
			                'message' => $message,
                  			'sender'=>000,
                  			'receiver'=>$build_data->company_id,
                  			'receiver_type'=>2
			           );

				Notification::create($list_param);

                        }
                    }
                }
            }
        }
       // return;
    }
    /* END */

    /* This function return number with ordinal */
    public function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
    /* END */

    /* Skip approval */
    public function skipApproval(Request $request) {
        $id = $request['id'];


        // $request_id = $request['id'];

        // $buildreqeust = Requests::find($request_id);

        // $req_data = json_decode($buildreqeust->data);
        // $ids = $req_data->ids;

        // foreach($ids as $id){

            $build = Builds::findOrFail($id);
        if(!$id || !$build) {
            // echo json_encode(array('status'=>false));
            return response()->json(['status' => false], 402);
        }
            $build->status = '2';
            $build->save();
    //    }

    //    $data = array('status'=>'1');
    //    $buildreqeust->update($data);

    //    echo json_encode(array('status'=>true));
        return response()->json(['status' => true], 200);
    }
    /* END */

    public function checkPhpinfo(){
      echo phpinfo();
    }
}

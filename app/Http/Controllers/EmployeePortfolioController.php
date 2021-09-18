<?php

namespace App\Http\Controllers;

use App\Accesslevel;
use App\Batch;
use App\Builds;
use App\Categories;
use App\Challenge;
use App\Employee;
use App\EmployeePortfolioViewer;
use App\Http\Controllers\API\ApiController;
use App\Industry;
use App\Subcategory;
use App\Users;
use App\Validations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use Imagick;
use Mail;

class EmployeePortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getRank($points_Arr, $emp_point)
    {
        $index = 1;
        foreach ($points_Arr as $item) {
            if ($emp_point == $item) {
                break;
            } else {
                $index++;
            }

        }

        return $index;
    }

    public function Downloadresume($id)
    {
        return response()->json(['status' => true, 'message' => $file]);
        $file = Storage::disk("s3")->url('images/resumes/') . $id . '_resume.pdf';
        return response()->download($file);
    }

    public function makepdfbydate(Request $request)
    {

        $basesiteurl = $request->url;
        $basesiteurl = str_replace('employeeportfolio', 'pdfgenerator', $basesiteurl);
        $id = $request->employee_id;
        $path = 'images/resumes/';
        $imageName = $id . '_resume.png';

        $accesskey = '0f50be0856cbbdacb9d2126d13b0f590';
        $file = file_get_contents('http://api.screenshotlayer.com/api/capture?access_key=' . $accesskey . '&url=' . $basesiteurl . '&viewport=1200x768&fullpage=1');

        Storage::disk("s3")->put($path . $imageName, $file, "public");
        $file = Storage::disk("s3")->url('images/resumes/') . $imageName;
        $pdfname = $id . '_resume.pdf';
        $imagedata = new Imagick($file);
        $imagedata->setImageFormat('pdf');
        Storage::disk("s3")->put($path . $pdfname, $imagedata, "public");
        $pdffile = Storage::disk("s3")->get($path . $pdfname);
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="' . $pdfname . '"',
            'filename' => $pdfname,
        ];

        return response($pdffile, 200, $headers);

    }

    public function getCompanyChallengeCountByDate($employee, $startdate, $enddate)
    {

        $company_chal_count = 0;
        $company_chal_count = Builds::has('challenge')->where('employee_id', $employee->id)->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->get()->count();
        return $company_chal_count;
    }

    public function getCompanyChallengeCountByDateSearch($employee, $startdate, $enddate, $search_text)
    {
        $company_chal_count = 0;
        $company_chal_count = Builds::has('challenge')->where('employee_id', $employee->id)->whereIn('status', ['1', '2'])->where('build_text', 'like', '%' . $search_text . '%')->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->get()->count();
        return $company_chal_count;
    }

    public function getCompanyChallengeByDate($employee, $startdate, $enddate)
    {

        $companychal = Builds::has('challenge')->where('employee_id', $employee->id)->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->orderBy('created_at', 'desc')->get()->toArray();
        return $companychal;
    }

    public function getCompanyChallengeByDateSearch($employee, $startdate, $enddate, $search_text)
    {

        $companychal = Builds::has('challenge')->where('employee_id', $employee->id)->whereIn('status', ['1', '2'])->where('build_text', 'like', '%' . $search_text . '%')->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->orderBy('created_at', 'desc')->get()->toArray();

        return $companychal;
    }

    public function getRegionalChallengeCountByDate($employee, $startdate, $enddate)
    {
        $region_chal_count = 0;

        $appr_chal_build = Builds::has('challenge')->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->get();
        $allchallenges = Challenge::all();
        $timedchal = array();
        if (!empty($appr_chal_build)) {
            foreach ($appr_chal_build as $build) {

                foreach ($allchallenges as $chal) {
                    if ($chal->id == $build->challenge_id && $chal->preset_type == '0') {
                        $timedchal[] = $build;
                    }

                }
            }
        }

        $region_chal_count = sizeof($timedchal);
        return $region_chal_count;
    }

    public function getRegionalChallengeCountByDateSearch($employee, $startdate, $enddate, $search_text)
    {

        $region_chal_count = 0;
        $appr_chal_build = Builds::has('challenge')->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('build_text', 'like', '%' . $search_text . '%')->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->get();
        $timedchal = array();
        $allchallenges = Challenge::all();
        if (!empty($appr_chal_build)) {
            foreach ($appr_chal_build as $build) {
                //$chal = Challenge::find($build->challenge_id);
                foreach ($allchallenges as $chal) {
                    if ($chal->id == $build->challenge_id && $chal->preset_type == '0') {
                        $timedchal[] = $build;
                    }

                }
            }
        }

        $region_chal_count = sizeof($timedchal);
        return $region_chal_count;
    }

    public function getRegionalChallengeByDate($employee, $startdate, $enddate)
    {

        $appr_chal_build = Builds::has('challenge')->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();
        $timedchal = array();
        $allchallenges = Challenge::all();

        if (!empty($appr_chal_build)) {
            foreach ($appr_chal_build as $build) {
                foreach ($allchallenges as $chal) {
                    if ($chal->id == $build->challenge_id && $chal->preset_type == '0') {
                        $timedchal[] = $build;
                    }

                }
            }
        }

        $timedchal = array_unique($timedchal);
        return $timedchal;

    }

    public function getRegionalChallengeByDateSearch($employee, $startdate, $enddate, $search_text)
    {

        $region_chal_count = 0;
        $appr_chal_build = Builds::has('challenge')->whereIn('status', ['1', '2'])->where('company_id', $employee->company_id)->where('build_text', 'like', '%' . $search_text . '%')->where('updated_at', '>=', $startdate)->where('updated_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();
        $timedchal = array();
        $allchallenges = Challenge::all();

        if (!empty($appr_chal_build)) {
            foreach ($appr_chal_build as $build) {
                //$chal = Challenge::find($build->challenge_id);
                foreach ($allchallenges as $chal) {
                    if ($chal->id == $build->challenge_id && $chal->preset_type == '0') {
                        $timedchal[] = $build;
                    }

                }
            }
        }

        return $timedchal;

    }

    public function getDuelCountByDate($employee, $startdate, $enddate)
    {
        return 0;

    }

    public function getDuelCountByDateSearch($employee, $startdate, $enddate, $search_text)
    {
        return 0;
    }

    public function getDuelByDate($employee, $startdate, $enddate)
    {

        $winduel = array();
        return $winduel;
    }

    public function getDuelByDateSearch($employee, $startdate, $enddate, $search_text)
    {
        $winduel = array();
        return $winduel;
    }

    public function getUTCode($employee)
    {
        $uiDate = $employee->created_at;
        $uiDate = 'UT-' . date("dmy-is", strtotime($uiDate));
        return $uiDate;
    }

    //by date
    //get the employees with access level 2 from validation table

    public function getTestinomialEmployeeInfo($id, $startdate, $enddate, $search_text)
    {

        $ids = array();
        $employees = array();

        if ($search_text == '') {
            $builds = Builds::where('employee_id', $id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('status', '!=', '-1')->orderBy('created_at', 'desc')->get();
        } else {
            $builds = Builds::where('employee_id', $id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('build_text', 'like', '%' . $search_text . '%')->where('status', '!=', '-1')->orderBy('created_at', 'desc')->get();
        }
        $approved_count = 0;
        $rejected_count = 0;
        if (!empty($builds)) {
            foreach ($builds as $build) {
                $validates = Validations::where('build_id', $build->id)->groupBy('build_id')->get();
                if (!empty($validates)) {
                    foreach ($validates as $validate) {
                        if ($validate->employee_id != $id) {
                            if ($validate->employee_id) {
                                $ids[] = $validate->employee_id;
                            } else {
                                $ids[] = $validate->batch_id;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($ids)) {
            $ids = array_unique($ids);
            foreach ($ids as $id) {
                $approved_count = 0;
                $rejected_count = 0;
                if (is_numeric($id)) {
                    $employee = Employee::find($id);
                    if (!empty($employee)) {
                        if (!empty($builds)) {
                            foreach ($builds as $build) {
                                $approved = Validations::where('build_id', $build->id)->groupBy('build_id')->where('status', '1')->where('employee_id', $employee->id)->get()->count();
                                $rejected = Validations::where('build_id', $build->id)->groupBy('build_id')->where('status', '0')->where('employee_id', $employee->id)->get()->count();
                                $approved_count += $approved;
                                $rejected_count += $rejected;
                            }
                        }
                        $employee->approved_count = $approved_count;
                        $employee->rejected_count = $rejected_count;
                        $employees[] = $employee->toArray();
                    }
                } else {
                    $batch = Batch::where('id', $id)->first();
                    $emp = null;
                    $empImage = '';
                    $empPhone = '';
                    if ($batch) {
                        if (!empty($builds)) {
                            foreach ($builds as $build) {
                                $approved = Validations::where('build_id', $build->id)->groupBy('build_id')->where('status', '1')->where('batch_id', $batch->id)->get()->count();
                                $rejected = Validations::where('build_id', $build->id)->groupBy('build_id')->where('status', '0')->where('batch_id', $batch->id)->get()->count();
                                $approved_count += $approved;
                                $rejected_count += $rejected;
                            }
                        }

                        $emp = Employee::where('email', $batch->email_to)->first();
                        if ($emp) {
                            $empImage = $emp->image;
                            $empPhone = $emp->phone_number;
                        }
                        $batch->full_name = $batch->firstname . ' ' . $batch->lastname;
                        $batch->email = $batch->email_to;
                        $batch->image = $empImage;
                        $batch->phone_number = $empPhone;
                        $batch->approved_count = $approved_count;
                        $batch->rejected_count = $rejected_count;
                        $employees[] = $batch;
                    }
                }
            }
        }


        $unique_employees = array();
        $emails = array();

        foreach ($employees as $emp) {
            if (!in_array($emp['email'], $emails)) {
                $unique_employees[] = $emp;
                $emails[] = $emp['email'];
            }
        }
        //ppppp
        usort($unique_employees, function ($a, $b) {
            return strcmp($a['full_name'], $b['full_name']);
        });

//        return $data = array('employee' => $unique_employees);

        return $data = array('employee' => $employees);
    }

    public function getTestinomialEmployeeInfoByCategory($id, $startdate, $enddate, $search_cat)
    {

        $ids = array();
        $employees = array();
        $employee = Employee::find($id);
        $categories = Categories::where('company_id', $employee->company_id)->where('category_name', $search_cat)->get();

        $builds = Builds::where('employee_id', $employee->id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('status', '!=', '-1')->orderBy('created_at', 'desc')->get();

        $result = array();

        if (!empty($categories)) {

            foreach ($categories as $item) {

                $subcategories = Subcategory::where('category_id', $item->id)->get();

                foreach ($subcategories as $subitem) {

                    foreach ($builds as $builditem) {
                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($subitem->id == $subcat_id) {
                                $result[] = $builditem->id;
                            }
                        }
                    }
                }
            }
        }

        $result = array_unique($result);

        $approved_count = 0;
        $rejected_count = 0;

        if (!empty($result)) {
            foreach ($result as $temp) {
                $validates = Validations::where('build_id', $temp)->groupBy('build_id')->where('employee_id', '!=', $id)->get();
                if (!empty($validates)) {
                    foreach ($validates as $validate) {
                        $ids[] = $validate->employee_id;
                    }
                }
            }
        }

        if (!empty($ids)) {

            $ids = array_unique($ids);

            foreach ($ids as $id) {

                $approved_count = 0;
                $rejected_count = 0;
                $employee = Employee::find($id);

                if (!empty($result)) {

                    foreach ($result as $temp) {
                        $approved = Validations::where('build_id', $temp)->groupBy('build_id')->where('status', '1')->where('employee_id', $employee->id)->get()->count();
                        $rejected = Validations::where('build_id', $temp)->groupBy('build_id')->where('status', '0')->where('employee_id', $employee->id)->get()->count();
                        $approved_count += $approved;
                        $rejected_count += $rejected;
                    }
                }

                $employee->approved_count = $approved_count;
                $employee->rejected_count = $rejected_count;

                $employees[] = $employee;
            }
        }

        $unique_employees = array();
        $emails = array();
        foreach ($employees as $emp) {
            if (!in_array($emp['email'], $emails)) {
                $unique_employees[] = $emp;
                $emails[] = $emp['email'];
            }
        }
        //ppppp
        usort($unique_employees, function ($a, $b) {
            return strcmp($a['full_name'], $b['full_name']);
        });

        return $data = array('employee' => $unique_employees);
    }

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

    public function cat_dateindex($id, $startdate, $enddate, $search_cat)
    {

        $startdate_old = $startdate;
        $enddate_old = $enddate;

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->find($id);
        //get the testinomial
        $testinomials_data = $this->getTestinomialEmployeeInfoByCategory($id, $startdate, $enddate, $search_cat);

        $search_categories = Categories::where('company_id', $employee->company_id)->get();
        $company = Users::where('id', $employee->company_id)->first();
        $industry = Industry::where('id', $employee->industry)->first();
        $level = Accesslevel::where('id', $employee->access_level)->first();
        $today = date('Y-m-d h:i:s') . '';
        $date1 = strtotime($today);
        $date2 = strtotime($employee->created_at . '');
        $diff = $date1 - $date2;
        $days = floor($diff / (60 * 60 * 24)); //seconds/minute*minutes/hour*hours/day)
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $times = $days . " days / " . $hours . " hours";
        $api = new ApiController;
        $emp_points = $api->countPoint($id);
        //get the company rank
        $comp_win_chal = array();
        $all_employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->where('company_id', $employee->company_id)
            ->where('is_deleted', '0')
            ->get();

        $comp_win_challenge = array();

        if ($all_employee) {
            foreach ($all_employee as $emp) {
                $emp->point = $api->countPoint($emp->id);
                $comp_win_challenge[] = $emp->point;
            }
        }
        usort($comp_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $company_rank = $this->getRank($comp_win_challenge, $emp_points);

        //get the region rank
        $region_win_challenge = array();
        $region_employee = $all_employee->where('industry', $employee->industry);
        if ($region_employee) {
            foreach ($region_employee as $region_emp) {
                $region_win_challenge[] = $region_emp->point;
            }
        }
        usort($region_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $region_rank = $this->getRank($region_win_challenge, $emp_points);
        $user_data['company_rank'] = $company_rank;
        $user_data['region_rank'] = $region_rank;

        $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDate($employee, $startdate, $enddate);
        $user_data['companychalcount'] = $this->getCompanyChallengeCountByDate($employee, $startdate, $enddate);
        $user_data['duelcount'] = $this->getDuelCountByDate($employee, $startdate, $enddate);

        //get user detail info

        $user_data['email'] = $employee->email;
        $user_data['name'] = $employee->full_name;
        $user_data['phone_number'] = $this->formatPhoneNumber($employee->phone_number);
        $user_data['website'] = $employee->website;
        $user_data['image'] = $employee->image;
        $user_data['id'] = $id;

        $user_data['startmonth'] = $startdate_old;
        $user_data['endmonth'] = $enddate_old;

        $company_name = '';
        if (!empty($company)) {
            $company_name = $company->name;
        }

        $industry_name = '';
        if (!empty($industry)) {
            $industry_name = $industry->industry_name;
        }

        $user_data['company'] = $company_name . ' ' . $industry_name;

        $user_data['level'] = $level->access_level_name;
        $user_data['times'] = $times;
        $user_data['ut_code'] = $this->getUTCode($employee);

        $user_data['myplan'] = $employee->myplan;
        $user_data['past_jobs'] = $employee->past_jobs;
        $user_data['references'] = $employee->emp_reference;
        $user_data['myobjective'] = $employee->myobjective;
        $user_data['hover_txt'] = '';
        $categorynames = '';
        $count_data = '';
        $maincategory = array();

        //get main category action
        $categories = Categories::with(['subcategories'])->where('company_id', $employee->company_id)->where('category_name', $search_cat)->get();
        $builds = Builds::where('employee_id', $employee->id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->whereIn('status', ['1','2'])->orderBy('created_at', 'desc')->get();
        $i = 0;
        $appr_build_cnt = 0;
        $cat_appr_build_cnt = '';

        $circle_color_list = ['first_color', 'second_color', 'third_color', 'forth_color'];
        $imagename_list = [['tablets.png', 'redux.png', 'power.png'], ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'], ['sales-award.png', 'training-award.png', 'ops-award.png'], ['sales-award.png', 'training-award.png', 'ops-award.png']];
        $j = 0; //variable for subcategory index
        //get sub-category
        $firstcategory = array();
        $maincategory = array();
        if (!empty($categories)) {

            foreach ($categories as $item) {
                //$categorynames .= $item->category_name.',';
                $k = 0; //variable for subcategory image index

//                $subcategories = Subcategory::where('category_id', $item->id)->get();
//                $subcat_count = Subcategory::where('category_id', $item->id)->get()->count();
                $subcategories = $item->subcategories;
                $subcat_count = count($item->subcategories);

                $item['count'] = $subcat_count;
                $appr_build_cnt = 0;
                foreach ($subcategories as $subitem) {

                    if (strlen($subitem->subcategory_name) >= 12) {
                        $subcat_name = substr($subitem->subcategory_name, 0, 12) . '<br />';
                        $subcat_name .= substr($subitem->subcategory_name, 13, strlen($subitem->subcategory_name));
                    }

                    $result = array();
                    foreach ($builds as $builditem) {
                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($subitem->id == $subcat_id) {
                                $result[] = $builditem;
                            }
                        }
                    }
                    $subitem['buildcount'] = sizeof($result);

                    $subitem['image'] = $imagename_list[$j % 4][$k];

                    $k = $k + 1;
                    $k = $k % 3;
                    $appr_build_cnt += sizeof($result);
                }

                $subcategories = $subcategories->toArray();
                //sort by buildcount with subcategory
                usort($subcategories, function ($a, $b) {
                    return $b['buildcount'] - $a['buildcount'];
                });

                foreach ($subcategories as $subcat) {
                    $categorynames .= $subcat['subcategory_name'] . ',';
                    $cat_appr_build_cnt .= $subcat['buildcount'] . ',';
                }
                $user_data['categorynames'] = $categorynames;
                $user_data['count_data'] = $cat_appr_build_cnt;

                $item['appr_build_cnt'] = $appr_build_cnt;
                $item['circle_color'] = $circle_color_list[$j % 4];
                $j++;
                $item['subcateogry'] = $subcategories;
                // $cat_appr_build_cnt .= $appr_build_cnt.',';

            }

            //sort actin by appr_build_count
            $categories = $categories->toArray();
            usort($categories, function ($a, $b) {
                return $b['appr_build_cnt'] - $a['appr_build_cnt'];
            });

            foreach ($categories as $cat) {

                if ($i == 0) {
                    $firstcategory = $cat;
                    $i = 1;
                } else {
                    $maincategory[] = $cat;
                }

            }

        } else {
            $categories = array();
            $user_data['categorynames'] = '';
            $user_data['count_data'] = '0';
        }

        // Percentage meter
        $totalBuilds = Builds::where('employee_id', $id)->whereIn('status',array('1','2'))->count();
        $approvedBuilds = Builds::where('employee_id', $id)->where('status','1')->count();

        $buildSuccessRate = 0;
        if ($totalBuilds != 0) {
            $buildSuccessRate = floor(($approvedBuilds/ $totalBuilds) * 100);
        }


        return view('employeeportfolioIndependent')
            ->with('testinomials_data', $testinomials_data)
            ->with('user_data', $user_data)
            ->with('categories', $categories)
            ->with('firstcategory', $firstcategory)
            ->with('maincategory', $maincategory)
            ->with('search_categories', $search_categories)
            ->with('search_cat', $search_cat)
            ->with('totalBuilds', $totalBuilds)
            ->with('approvedBuilds', $approvedBuilds)
            ->with('buildSuccessRate', $buildSuccessRate);

    }

    public function cat_dateindexIndependent($id, $startdate, $enddate, $search_cat)
    {
     
        $startdate_old = $startdate;
        $enddate_old = $enddate;

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->find($id);
        //get the testinomial
        $employeePortfolioViewers = EmployeePortfolioViewer::where('employee_id', $id)->get();

        $testinomials_data = $this->getTestinomialEmployeeInfoByCategory($id, $startdate, $enddate, $search_cat);

        $search_categories = Categories::where('company_id', $employee->company_id)->get();
        $company = Users::where('id', $employee->company_id)->first();
        $industry = Industry::where('id', $employee->industry)->first();
        $level = Accesslevel::where('id', $employee->access_level)->first();
        $today = date('Y-m-d h:i:s') . '';
        $date1 = strtotime($today);
        $date2 = strtotime($employee->created_at . '');
        $diff = $date1 - $date2;
        $days = floor($diff / (60 * 60 * 24)); //seconds/minute*minutes/hour*hours/day)
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $times = $days . " days / " . $hours . " hours";
        $api = new ApiController;
        $emp_points = $api->countPoint($id);
        //get the company rank
        $comp_win_chal = array();
        $all_employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->where('company_id', $employee->company_id)
            ->where('is_deleted', '0')
            ->get();

        $comp_win_challenge = array();

        if ($all_employee) {
            foreach ($all_employee as $emp) {
                $emp->point = $api->countPoint($emp->id);
                $comp_win_challenge[] = $emp->point;
            }
        }
        usort($comp_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $company_rank = $this->getRank($comp_win_challenge, $emp_points);

        //get the region rank
        $region_win_challenge = array();
        $region_employee = $all_employee->where('industry', $employee->industry);

        if ($region_employee) {
            foreach ($region_employee as $region_emp) {
                $region_win_challenge[] = $region_emp->point;
            }
        }
        usort($region_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $region_rank = $this->getRank($region_win_challenge, $emp_points);
        $user_data['company_rank'] = $company_rank;
        $user_data['region_rank'] = $region_rank;

        $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDate($employee, $startdate, $enddate);
        $user_data['companychalcount'] = $this->getCompanyChallengeCountByDate($employee, $startdate, $enddate);
        $user_data['duelcount'] = $this->getDuelCountByDate($employee, $startdate, $enddate);

        //get user detail info

        $user_data['email'] = $employee->email;
        $user_data['name'] = $employee->full_name;
        $user_data['phone_number'] = $this->formatPhoneNumber($employee->phone_number);
        $user_data['website'] = $employee->website;
        $user_data['image'] = $employee->image;
        $user_data['id'] = $id;

        $user_data['startmonth'] = $startdate_old;
        $user_data['endmonth'] = $enddate_old;

        $company_name = '';
        if (!empty($company)) {
            $company_name = $company->name;
        }

        $industry_name = '';
        if (!empty($industry)) {
            $industry_name = $industry->industry_name;
        }

        $user_data['company'] = $company_name . ' ' . $industry_name;

        $user_data['level'] = $level->access_level_name;
        $user_data['times'] = $times;
        $user_data['ut_code'] = $this->getUTCode($employee);

        $user_data['myplan'] = $employee->myplan;
        $user_data['past_jobs'] = $employee->past_jobs;
        $user_data['references'] = $employee->emp_reference;
        $user_data['myobjective'] = $employee->myobjective;
        $user_data['hover_txt'] = '';
        $categorynames = '';
        $category_ids = '';

        define('INDI_COMPANY_ID', env('PUBLIC_COMPANY_ID'));
        $categories = Categories::with(['subcategories'])->where('company_id', INDI_COMPANY_ID)->get();
            //        $category_count = Categories::where('company_id', INDI_COMPANY_ID)->get()->count();

        $i = 0;
        $appr_build_cnt = 0;
        $cat_appr_build_cnt = '';

        $circle_color_list = ['first_color', 'second_color', 'third_color', 'forth_color'];
        $imagename_list = [['tablets.png', 'redux.png', 'power.png'], ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'], ['sales-award.png', 'training-award.png', 'ops-award.png'], ['sales-award.png', 'training-award.png', 'ops-award.png']];
        $j = 0; //variable for subcategory index
        //get sub-category

        $firstcategory = array();
        $maincategory = array();

        $builds = Builds::where('employee_id', $employee->id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->whereIn('status', ['1','2'])->orderBy('created_at', 'desc')->get();

        if (empty($request->search)) {

            if (!empty($categories)) {

                foreach ($categories as $item) {
                    //$categorynames .= $item->category_name.',';
                    $k = 0; //variable for subcategory image index

                    //  $subcategories = Subcategory::where('category_id',$item->id)->get();
                    //  $subcat_count = Subcategory::where('category_id',$item->id)->get()->count();

                    //   $item['count'] = $subcat_count;

                    $appr_build_cnt = 0;
                    //   foreach($subcategories as $subitem){

                    if (strlen($item->category_name) >= 12) {
                        $cat_name = substr($item->category_name, 0, 12) . '<br />';
                        $cat_name .= substr($item->category_name, 13, strlen($item->category_name));
                    }

                    $result = array();
                    foreach ($builds as $builditem) {

                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($item->id == $subcat_id) {
                                if (!empty($builditem->build_text))
                                // if(strpos(strtolower($builditem->build_text),strtolower($request->search)) !== false){

                                {
                                    $result[] = $builditem;
                                }

                                // }
                            }
                        }
                    }
                    $item['buildcount'] = sizeof($result);

                    $item['image'] = $imagename_list[$j % 4][$k];

                    $k = $k + 1;
                    $k = $k % 3;
                    $appr_build_cnt += sizeof($result);
                    //    }

                    //    $subcategories = $subcategories->toArray();
                    //    //sort by buildcount with subcategory
                    //    usort($subcategories, function($a, $b) {
                    //        return $b['buildcount'] - $a['buildcount'] ;
                    //    });

                    $item['appr_build_cnt'] = $appr_build_cnt;
                    $item['circle_color'] = $circle_color_list[$j % 4];
                    $j++;
                    //   $item['subcateogry'] = $subcategories;
                    // $cat_appr_build_cnt .= $appr_build_cnt.',';

                }

                //sort actin by appr_build_count
                $categories = $categories->toArray();
                usort($categories, function ($a, $b) {
                    return $b['appr_build_cnt'] - $a['appr_build_cnt'];
                });

                foreach ($categories as $cat) {
                    if ($cat['category_name'] == $search_cat) {
                        //   foreach($cat['subcateogry'] as $subcat) {
                        // $user_data['hover_txt'] .= $subcat['subcategory_name'].' '.$subcat['buildcount'].'#';
                        //   }
                        $user_data['hover_txt'] .= $cat['category_name'] . ' ' . $cat['buildcount'] . '#';

                        $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '#');
                        $user_data['hover_txt'] .= '&&&';
                        $categorynames .= $cat['category_name'] . ',';
                        $category_ids .= $cat['id'] . ',';
                        $cat_appr_build_cnt .= $cat['appr_build_cnt'] . ',';
                        //   if($i == 0) {
                        //       $firstcategory = $cat;
                        //       $i = 1;
                        //   }
                        // else
                        $maincategory[] = $cat;
                    }
                }
                $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '&&&');
                $user_data['categorynames'] = $categorynames;
                $user_data['category_ids'] = $category_ids;
                $user_data['count_data'] = $cat_appr_build_cnt;

            } else {
                $categories = array();
                $user_data['categorynames'] = '';
                $user_data['category_ids'] = '';
                $user_data['count_data'] = '0';
            }

        } else {
            if (!empty($categories)) {

                foreach ($categories as $item) {
                    //$categorynames .= $item->category_name.',';
                    $k = 0; //variable for subcategory image index

                //                    $subcategories = Subcategory::where('category_id', $item->id)->get();
                //                    $subcat_count = Subcategory::where('category_id', $item->id)->get()->count();
                    $subcategories = $item->subcategories;
                    $subcat_count = count($item->subcategories);

                    $item['count'] = $subcat_count;
                    $appr_build_cnt = 0;
                    foreach ($subcategories as $subitem) {

                        if (strlen($subitem->subcategory_name) >= 12) {
                            $subcat_name = substr($subitem->subcategory_name, 0, 12) . '<br />';
                            $subcat_name .= substr($subitem->subcategory_name, 13, strlen($subitem->subcategory_name));
                        }

                        $result = array();
                        foreach ($builds as $builditem) {
                            $subcat_str = $builditem->subcategory;
                            $subcat_arr = explode(',', $subcat_str);
                            foreach ($subcat_arr as $subcat_id) {
                                if ($subitem->id == $subcat_id) {
                                    $result[] = $builditem;
                                }
                            }
                        }
                        $subitem['buildcount'] = sizeof($result);

                        $subitem['image'] = $imagename_list[$j % 4][$k];

                        $k = $k + 1;
                        $k = $k % 3;
                        $appr_build_cnt += sizeof($result);
                    }

                    $subcategories = $subcategories->toArray();
                    //sort by buildcount with subcategory
                    usort($subcategories, function ($a, $b) {
                        return $b['buildcount'] - $a['buildcount'];
                    });

                    $item['appr_build_cnt'] = $appr_build_cnt;
                    $item['circle_color'] = $circle_color_list[$j % 4];
                    $j++;
                    $item['subcateogry'] = $subcategories;
                    // $cat_appr_build_cnt .= $appr_build_cnt.',';

                }

                //sort actin by appr_build_count
                $categories = $categories->toArray();
                usort($categories, function ($a, $b) {
                    return $b['appr_build_cnt'] - $a['appr_build_cnt'];
                });

                foreach ($categories as $cat) {

                    foreach ($cat['subcateogry'] as $subcat) {
                        $user_data['hover_txt'] .= $subcat['subcategory_name'] . ' ' . $subcat['buildcount'] . '#';
                    }

                    $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '#');
                    $user_data['hover_txt'] .= '&&&';

                    $categorynames .= $cat['category_name'] . ',';
                    $category_ids .= $cat['id'] . ',';
                    $cat_appr_build_cnt .= $cat['appr_build_cnt'] . ',';

                    if ($i == 0) {
                        $firstcategory = $cat;
                        $i = 1;
                    } else {
                        $maincategory[] = $cat;
                    }

                }
                $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '&&&');
                $user_data['categorynames'] = $categorynames;
                $user_data['category_ids'] = $category_ids;
                $user_data['count_data'] = $cat_appr_build_cnt;

            } else {
                $categories = array();
                $user_data['categorynames'] = '';
                $user_data['category_ids'] = '';
                $user_data['count_data'] = '0';
            }
        }

        // Percentage meter
        $totalBuilds = Builds::where('employee_id', $id)->whereIn('status',array('1','2'))->count();
        $approvedBuilds = Builds::where('employee_id', $id)->where('status','1')->count();

        $buildSuccessRate = 0;
        if ($totalBuilds != 0) {
            $buildSuccessRate = floor(($approvedBuilds/ $totalBuilds) * 100);
        }
        $user_data['hard_work'] = $employeePortfolioViewers->count(); 



        return view('employeeportfolioIndependent')
            ->with('testinomials_data', $testinomials_data)
            ->with('user_data', $user_data)
            ->with('categories', $categories)
            ->with('firstcategory', $firstcategory)
            ->with('maincategory', $maincategory)
            ->with('search_categories', $search_categories)
            ->with('search_cat', $search_cat)
            ->with('totalBuilds', $totalBuilds)
            ->with('approvedBuilds', $approvedBuilds)
            ->with('buildSuccessRate', $buildSuccessRate);
    }



    public function dateindex(Request $request, $id, $startdate, $enddate)
    {

        $startdate_old = $startdate;
        $enddate_old = $enddate;

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);
        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        if ($end_arr[0] == 1) {
            $last_month  = '12' . '-' . '01' .'-' . ($end_arr[2]-1);
        } else {
            $last_month       = $end_arr[0]-1 . '-' . '01' .'-' . $end_arr[2];
        }
        $current_month    = $end_arr[0] . '-' . $end_arr[1] .'-' . $end_arr[2];


        $employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->find($id);
        //get the testinomial
        $search_text = $request->search;
        $testinomials_data = $employee->getPromoters($startdate, $enddate, $search_text);

        $search_categories = Categories::where('company_id', $employee->company_id)->get();

        $company = Users::where('id', $employee->company_id)->first();
        $industry = Industry::where('id', $employee->industry)->first();
        $level = Accesslevel::where('id', $employee->access_level)->first();
        $today = date('Y-m-d h:i:s') . '';
        $date1 = strtotime($today);
        $date2 = strtotime($employee->created_at . '');
        $diff = $date1 - $date2;
        $days = floor($diff / (60 * 60 * 24)); //seconds/minute*minutes/hour*hours/day)
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $times = $days . " days / " . $hours . " hours";
        $api = new ApiController;
        $emp_points = $api->countPoint($employee);
        //get the company rank
        $comp_win_chal = array();

        $all_employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->where('company_id', $employee->company_id)
            ->where('is_deleted', '0')
            ->get();

        $comp_win_challenge = array();

        if ($all_employee) {
            foreach ($all_employee as $key => $emp) {
//                $emp->point = $api->countPoint($emp->id);
                $emp->point = $api->countPoint($emp);
                $comp_win_challenge[] = $emp->point;
            }
        }
        usort($comp_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $company_rank = $this->getRank($comp_win_challenge, $emp_points);

        //get the region rank
        $region_win_challenge = array();

        // Regional employees is a subset of all employees
        // We can get regional employee points since it's loaded previously in all employees
        // Call where() on the existing collection
        $region_employee = $all_employee->where('industry', $employee->industry);

        if ($region_employee) {
            foreach ($region_employee as $region_emp) {
                $region_win_challenge[] = $region_emp->point;
            }
        }
        usort($region_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $region_rank = $this->getRank($region_win_challenge, $emp_points);
        $user_data['company_rank'] = $company_rank;
        $user_data['region_rank'] = $region_rank;
        if (empty($request->search)) {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['duelcount'] = $this->getDuelCountByDate($employee, $startdate, $enddate);
        } else {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['duelcount'] = $this->getDuelCountByDateSearch($employee, $startdate, $enddate, $request->search);
        }

        //get user detail info

        $user_data['email'] = $employee->email;
        $user_data['name'] = $employee->full_name;
        $user_data['phone_number'] = $this->formatPhoneNumber($employee->phone_number);
        $user_data['website'] = $employee->website;
        $user_data['business_url'] = $employee->business_url;
        $user_data['google_reviews_count'] = $employee->google_reviews_count;
        $user_data['fb_share_count'] = $employee->fb_share_count;
        $user_data['image'] = $employee->image;
        $user_data['id'] = $id;

        $user_data['startmonth'] = $startdate_old;
        $user_data['endmonth'] = $enddate_old;

        $user_data['last_month'] = $last_month;
        $user_data['current_month'] = $current_month;

        $company_name = '';
        if (!empty($company)) {
            $company_name = $company->name;
        }

        $industry_name = '';
        if (!empty($industry)) {
            $industry_name = $industry->industry_name;
        }

        $user_data['company'] = $company_name . ' ' . $industry_name;

        $user_data['level'] = $level->access_level_name;
        $user_data['times'] = $times;
        $user_data['ut_code'] = $this->getUTCode($employee);

        $user_data['myplan'] = $employee->myplan;
        $user_data['past_jobs'] = $employee->past_jobs;
        $user_data['references'] = $employee->emp_reference;
        $user_data['myobjective'] = $employee->myobjective;
        $user_data['hover_txt'] = '';
        $categorynames = '';

        $maincategory = array();

        // Eager load "subcategory" to prevent n+1 query issue
        $categories = Categories::with(['subcategories'])->where('company_id', $employee->company_id)->get();

        $i = 0;
        $appr_build_cnt = 0;
        $cat_appr_build_cnt = '';

        $circle_color_list = ['first_color', 'second_color', 'third_color', 'forth_color'];
        $imagename_list = [['tablets.png', 'redux.png', 'power.png'], ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'], ['sales-award.png', 'training-award.png', 'ops-award.png'], ['sales-award.png', 'training-award.png', 'ops-award.png']];
        $j = 0; //variable for subcategory index
        //get sub-category
        $firstcategory = array();
        $maincategory = array();

        if ($startdate_old) {
            $startdate_old = Carbon::createFromFormat('m-d-Y', $startdate_old)->startOfDay()->toDateTimeString();
        }

        if ($enddate_old) {
            $enddate_old = Carbon::createFromFormat('m-d-Y', $enddate_old)->endOfDay()->toDateTimeString();
        }

        $buildTable = (new Builds())->getTable();
        $categoryTable = (new Categories())->getTable();
        $subCategoryTable = (new Subcategory())->getTable();
        $challengeTable = (new Challenge())->getTable();

        $builds = Builds::query()
            ->select([
                "{$buildTable}.*",
                "{$subCategoryTable}.id as subcategory_id",
                "{$subCategoryTable}.subcategory_name",
                "{$categoryTable}.id as category_id",
                "{$categoryTable}.category_name",
            ])
//            ->join($challengeTable, "{$challengeTable}.id", '=', "{$buildTable}.challenge_id")
            ->join($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->join($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->when(!empty($request->search), function ($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                return $query->where(function($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                    $query->whereRaw("LOWER({$buildTable}.build_text) LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$categoryTable}.category_name LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$subCategoryTable}.subcategory_name LIKE '%" . strtolower(request('search')) . "%'");
                });
            })
            ->whereIn("{$buildTable}.status", ['1','2'])
            ->where("{$buildTable}.employee_id", $employee->id)
            ->where("{$buildTable}.company_id", $employee->company_id)
            ->whereBetween("{$buildTable}.created_at", [$startdate_old, $enddate_old])
//            ->where("{$buildTable}.updated_at", '>=', $startdate)
//            ->where("{$buildTable}.updated_at", '<=', $enddate)
            ->orderBy("{$buildTable}.created_at", 'desc')
//            ->groupBy("{$buildTable}.id")
            ->get();


        $categories = $builds->groupBy('category_name')
            ->map(function ($builds) {
                $subcategories = $builds->groupBy('subcategory_id')
                    ->values()
                    ->map(function ($builds) {
                        $subcategory = new \stdClass();
                        $subcategory->id = $builds->first()->subcategory_id;
                        $subcategory->name = $builds->first()->subcategory_name;
                        $subcategory->builds_count = $builds->count();
                        $subcategory->builds = $builds;

                        return $subcategory;
                    })->sortByDesc('builds_count')
                    ->values();

                $category = new \stdClass();
                $category->id = $builds->first()->category_id;
                $category->name = $builds->first()->category_name;
                $category->subcategories = $subcategories;
                $category->builds_count = $builds->count();

                return $category;
            })->sortByDesc('builds_count')
            ->values();

        // Percentage meter
            // Total builds = Approved count + self approved count (without reject count)
        $totalBuilds = Builds::where('employee_id', $id)->whereIn('status',array('1','2'))->count();
        $approvedBuilds = Builds::where('employee_id', $id)->where('status','1')->count();

        $buildSuccessRate = 0;
        if ($totalBuilds != 0) {
            $buildSuccessRate = floor(($approvedBuilds/ $totalBuilds) * 100);
        }
        // moin
        $images = Builds::where('employee_id',$id)->with('subCategory')->latest()->get()->take(5);

        return view('employeeportfolio', [
            'testinomials_data' => $testinomials_data,
            'user_data' => $user_data,
            'categories' => $categories,
//            'firstcategory' => $firstcategory,
//            'maincategory' => $maincategory,
//            'search_categories' => $search_categories,
//            'search_cat' => $search_cat,
            'totalBuilds'       => $totalBuilds,
            'approvedBuilds'    => $approvedBuilds,
            'buildSuccessRate'  => $buildSuccessRate,
            'images'  => $images,
        ]);

    }

    public function dateindexIndependent(Request $request, $id, $startdate, $enddate)
    {
     

        $startdate_old = $startdate;
        $enddate_old = $enddate;

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);
        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));


        if ($end_arr[0] == 1) {
            $last_month  = '12' . '-' . '01' .'-' . ($end_arr[2]-1);
        } else {
            $last_month       = $end_arr[0]-1 . '-' . '01' .'-' . $end_arr[2];
        }
        $current_month    = $end_arr[0] . '-' . $end_arr[1] .'-' . $end_arr[2];




        $employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->find($id);
        //get the testinomial
        $employeePortfolioViewers = EmployeePortfolioViewer::where('employee_id', $id)->get();
        $search_text = $request->search;
        $testinomials_data = $employee->getPromoters($startdate, $enddate, $search_text);

        $search_categories = Categories::where('company_id', $employee->company_id)->get();

        $company = Users::where('id', $employee->company_id)->first();
        $industry = Industry::where('id', $employee->industry)->first();
        $level = Accesslevel::where('id', $employee->access_level)->first();
        $today = date('Y-m-d h:i:s') . '';
        $date1 = strtotime($today);
        $date2 = strtotime($employee->created_at . '');
        $diff = $date1 - $date2;
        $days = floor($diff / (60 * 60 * 24)); //seconds/minute*minutes/hour*hours/day)
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $times = $days . " days / " . $hours . " hours";
        $api = new ApiController;
        $emp_points = $api->countPoint($id);
        //get the company rank
        $comp_win_chal = array();
        $all_employee = Employee::query()
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->where('company_id', $employee->company_id)
            ->where('is_deleted', '0')
            ->get();

        $comp_win_challenge = array();

        if ($all_employee) {
            foreach ($all_employee as $emp) {
                $emp->point = $api->countPoint($emp->id);
                $comp_win_challenge[] = $emp->point;
            }
        }
        usort($comp_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $company_rank = $this->getRank($comp_win_challenge, $emp_points);

        //get the region rank
        $region_win_challenge = array();
        $region_employee = $all_employee->where('industry', $employee->industry);
        if ($region_employee) {
            foreach ($region_employee as $region_emp) {
                $region_win_challenge[] = $region_emp->point;
            }
        }
        usort($region_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $region_rank = $this->getRank($region_win_challenge, $emp_points);
        $user_data['company_rank'] = $company_rank;
        $user_data['region_rank'] = $region_rank;
        if (empty($request->search)) {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['duelcount'] = $this->getDuelCountByDate($employee, $startdate, $enddate);
        } else {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['duelcount'] = $this->getDuelCountByDateSearch($employee, $startdate, $enddate, $request->search);
        }

        //get user detail info

        $user_data['email'] = $employee->email;
        $user_data['name'] = $employee->full_name;
        $user_data['phone_number'] = $this->formatPhoneNumber($employee->phone_number);
        $user_data['website'] = $employee->website;
        $user_data['business_url'] = $employee->business_url;
        $user_data['google_reviews_count'] = $employee->google_reviews_count;
        $user_data['fb_share_count'] = $employee->fb_share_count;
        $user_data['image'] = $employee->image;
        $user_data['id'] = $id;

        $user_data['startmonth'] = $startdate_old;
        $user_data['endmonth'] = $enddate_old;

        $user_data['last_month'] = $last_month;
        $user_data['current_month'] = $current_month;


        $company_name = '';
        if (!empty($company)) {
            $company_name = $company->name;
        }

        $industry_name = '';
        if (!empty($industry)) {
            $industry_name = $industry->industry_name;
        }

        $user_data['company'] = $company_name . ' ' . $industry_name;

        $user_data['level'] = $level->access_level_name;
        $user_data['times'] = $times;
        $user_data['ut_code'] = $this->getUTCode($employee);

        $user_data['myplan'] = $employee->myplan;
        $user_data['past_jobs'] = $employee->past_jobs;
        $user_data['references'] = $employee->emp_reference;
        $user_data['myobjective'] = $employee->myobjective;
        $user_data['hover_txt'] = '';
        $categorynames = '';
        $category_ids = '';

        define('INDI_COMPANY_ID', env('PUBLIC_COMPANY_ID'));
        $categories = Categories::with(['subcategories'])->where('company_id', INDI_COMPANY_ID)->get();

        $i = 0;
        $appr_build_cnt = 0;
        $cat_appr_build_cnt = '';

        $circle_color_list = ['first_color', 'second_color', 'third_color', 'forth_color'];
        $imagename_list = [['tablets.png', 'redux.png', 'power.png'], ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'], ['sales-award.png', 'training-award.png', 'ops-award.png'], ['sales-award.png', 'training-award.png', 'ops-award.png']];
        $j = 0; //variable for subcategory index
        //get sub-category

        $maincategory = array();

        if ($startdate_old) {
            $startdate_old = Carbon::createFromFormat('m-d-Y', $startdate_old)->startOfDay()->toDateTimeString();
        }

        if ($enddate_old) {
            $enddate_old = Carbon::createFromFormat('m-d-Y', $enddate_old)->endOfDay()->toDateTimeString();
        }

        $buildTable = (new Builds())->getTable();
        $categoryTable = (new Categories())->getTable();
        $subCategoryTable = (new Subcategory())->getTable();

        $builds = Builds::query()
            ->select([
                "{$buildTable}.*",
                "{$subCategoryTable}.id as subcategory_id",
                "{$subCategoryTable}.subcategory_name",
                "{$categoryTable}.id as category_id",
                "{$categoryTable}.category_name",
            ])
            ->join($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->join($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->when(!empty($request->search), function ($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                return $query->where(function($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                    $query->whereRaw("LOWER({$buildTable}.build_text) LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$categoryTable}.category_name LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$subCategoryTable}.subcategory_name LIKE '%" . strtolower(request('search')) . "%'");
                });
            })
            ->whereNotNull("{$categoryTable}.id")
            ->whereIn("{$buildTable}.status", ['1','2'])
            ->where("{$buildTable}.employee_id", $employee->id)
            ->where("{$buildTable}.company_id", $employee->company_id)
            ->whereBetween("{$buildTable}.created_at", [$startdate, $enddate])
            ->orderBy("{$buildTable}.created_at", 'desc')
            ->get();

            // Map subcategories to "Skills" in frontend, instead of categories
        $categories = $builds->groupBy('subcategory_id') //->groupBy('category_name')
            ->map(function ($builds) {

                $category = new \stdClass();
                $category->id = $builds->first()->subcategory_id;
                $category->name = $builds->first()->subcategory_name;
                $category->builds_count = $builds->count();
                $category->builds = $builds;

                return $category;
            })->sortByDesc('builds_count')
            ->values();

        $search_cat = '';
        // Percentage meter
        $totalBuilds = Builds::where('employee_id', $id)->whereIn('status',array('1','2'))->count();
        $approvedBuilds = Builds::where('employee_id', $id)->where('status','1')->count();

        $buildSuccessRate = 0;
        if ($totalBuilds != 0) {
            $buildSuccessRate = floor(($approvedBuilds/ $totalBuilds) * 100);
        }

        $shared = 0;
        if (isset(request()->share) && request()->share == 1){
            $shared = 1;
        }
        $user_data['hard_work'] = $employeePortfolioViewers->count(); 
    // moin
        $images = Builds::where('employee_id',$id)->with('subCategory')->latest()->get()->take(5);

        return view('employeeportfolioIndependent')
            ->with('testinomials_data', $testinomials_data)
            ->with('user_data', $user_data)
            ->with('categories', $categories)
            ->with('search_cat', $search_cat)
            ->with('totalBuilds', $totalBuilds)
            ->with('approvedBuilds', $approvedBuilds)
            ->with('buildSuccessRate', $buildSuccessRate)
            ->with('shared', $shared)
            ->with('images', $images);
    }

    public function shareWebViewURLForIndependent(Request $request, $id, $startdate, $enddate)
    {

        $startdate_old = $startdate;
        $enddate_old = $enddate;

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);
        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));
        $employee = Employee::find($id);
        //get the testinomial
        $search_text = $request->search;
        $testinomials_data = $employee->getPromoters($startdate, $enddate, $search_text);

        $search_categories = Categories::where('company_id', $employee->company_id)->get();

        $company = Users::where('id', $employee->company_id)->first();
        $industry = Industry::where('id', $employee->industry)->first();
        $level = Accesslevel::where('id', $employee->access_level)->first();
        $today = date('Y-m-d h:i:s') . '';
        $date1 = strtotime($today);
        $date2 = strtotime($employee->created_at . '');
        $diff = $date1 - $date2;
        $days = floor($diff / (60 * 60 * 24)); //seconds/minute*minutes/hour*hours/day)
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $times = $days . " days / " . $hours . " hours";
        $api = new ApiController;
        $emp_points = $api->countPoint($id);
        //get the company rank
        $comp_win_chal = array();
        $all_employee = Employee::where('company_id', $employee->company_id)
            ->where('is_deleted', '0')->get();

        $comp_win_challenge = array();

        if ($all_employee) {
            foreach ($all_employee as $emp) {
                $emp->point = $api->countPoint($emp->id);
                $comp_win_challenge[] = $emp->point;
            }
        }
        usort($comp_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $company_rank = $this->getRank($comp_win_challenge, $emp_points);

        //get the region rank
        $region_win_challenge = array();
        $region_employee = Employee::where('company_id', $employee->company_id)->where('industry', $employee->industry)->where('is_deleted', '0')->get();
        if ($region_employee) {
            foreach ($region_employee as $region_emp) {

                $region_win_challenge[] = $api->countPoint($region_emp->id);
            }
        }
        usort($region_win_challenge, function ($a, $b) {
            return $b - $a;
        });

        $region_rank = $this->getRank($region_win_challenge, $emp_points);
        $user_data['company_rank'] = $company_rank;
        $user_data['region_rank'] = $region_rank;
        if (empty($request->search)) {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDate($employee, $startdate, $enddate);
            $user_data['duelcount'] = $this->getDuelCountByDate($employee, $startdate, $enddate);
        } else {
            $user_data['regionchalcount'] = $this->getRegionalChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['companychalcount'] = $this->getCompanyChallengeCountByDateSearch($employee, $startdate, $enddate, $request->search);
            $user_data['duelcount'] = $this->getDuelCountByDateSearch($employee, $startdate, $enddate, $request->search);
        }

        //get user detail info

        $user_data['email'] = $employee->email;
        $user_data['name'] = $employee->full_name;
        $user_data['phone_number'] = $this->formatPhoneNumber($employee->phone_number);
        $user_data['website'] = $employee->website;
        $user_data['image'] = $employee->image;
        $user_data['id'] = $id;

        $user_data['startmonth'] = $startdate_old;
        $user_data['endmonth'] = $enddate_old;

        $company_name = '';
        if (!empty($company)) {
            $company_name = $company->name;
        }

        $industry_name = '';
        if (!empty($industry)) {
            $industry_name = $industry->industry_name;
        }

        $user_data['company'] = $company_name . ' ' . $industry_name;

        $user_data['level'] = $level->access_level_name;
        $user_data['times'] = $times;
        $user_data['ut_code'] = $this->getUTCode($employee);

        $user_data['myplan'] = $employee->myplan;
        $user_data['past_jobs'] = $employee->past_jobs;
        $user_data['references'] = $employee->emp_reference;
        $user_data['myobjective'] = $employee->myobjective;
        $user_data['hover_txt'] = '';
        $categorynames = '';
        $category_ids = '';

        define('INDI_COMPANY_ID', env('PUBLIC_COMPANY_ID'));
        $categories = Categories::where('company_id', INDI_COMPANY_ID)->get();
        $category_count = Categories::where('company_id', INDI_COMPANY_ID)->get()->count();

        $i = 0;
        $appr_build_cnt = 0;
        $cat_appr_build_cnt = '';

        $circle_color_list = ['first_color', 'second_color', 'third_color', 'forth_color'];
        $imagename_list = [['tablets.png', 'redux.png', 'power.png'], ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'], ['sales-award.png', 'training-award.png', 'ops-award.png'], ['sales-award.png', 'training-award.png', 'ops-award.png']];
        $j = 0; //variable for subcategory index
        //get sub-category

        //$firstcategory = array();
        $maincategory = array();

        $builds = Builds::where('employee_id', $employee->id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->whereIn('status', ['1','2'])->orderBy('created_at', 'desc')->get();

        if (empty($request->search)) {

            if (!empty($categories)) {

                foreach ($categories as $item) {
                    //$categorynames .= $item->category_name.',';
                    $k = 0; //variable for subcategory image index

                    //  $subcategories = Subcategory::where('category_id',$item->id)->get();
                    //  $subcat_count = Subcategory::where('category_id',$item->id)->get()->count();

                    //   $item['count'] = $subcat_count;

                    $appr_build_cnt = 0;
                    //   foreach($subcategories as $subitem){

                    if (strlen($item->category_name) >= 12) {
                        $cat_name = substr($item->category_name, 0, 12) . '<br />';
                        $cat_name .= substr($item->category_name, 13, strlen($item->category_name));
                    }

                    $result = array();
                    foreach ($builds as $builditem) {

                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($item->id == $subcat_id) {
                                if (!empty($builditem->build_text))
                                // if(strpos(strtolower($builditem->build_text),strtolower($request->search)) !== false){

                                {
                                    $result[] = $builditem;
                                }

                                // }
                            }
                        }
                    }
                    $item['buildcount'] = sizeof($result);

                    $item['image'] = $imagename_list[$j % 4][$k];

                    $k = $k + 1;
                    $k = $k % 3;
                    $appr_build_cnt += sizeof($result);
                    //    }

                    //    $subcategories = $subcategories->toArray();
                    //    //sort by buildcount with subcategory
                    //    usort($subcategories, function($a, $b) {
                    //        return $b['buildcount'] - $a['buildcount'] ;
                    //    });

                    $item['appr_build_cnt'] = $appr_build_cnt;
                    $item['circle_color'] = $circle_color_list[$j % 4];
                    $j++;
                    //   $item['subcateogry'] = $subcategories;
                    // $cat_appr_build_cnt .= $appr_build_cnt.',';

                }

                //sort actin by appr_build_count
                $categories = $categories->toArray();
                usort($categories, function ($a, $b) {
                    return $b['appr_build_cnt'] - $a['appr_build_cnt'];
                });

                foreach ($categories as $cat) {

                    //   foreach($cat['subcateogry'] as $subcat) {
                    // $user_data['hover_txt'] .= $subcat['subcategory_name'].' '.$subcat['buildcount'].'#';
                    //   }
                    $user_data['hover_txt'] .= $cat['category_name'] . ' ' . $cat['buildcount'] . '#';

                    $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '#');
                    $user_data['hover_txt'] .= '&&&';
                    $categorynames .= $cat['category_name'] . ',';
                    $category_ids .= $cat['id'] . ',';
                    $cat_appr_build_cnt .= $cat['appr_build_cnt'] . ',';
                    //   if($i == 0) {
                    //       $firstcategory = $cat;
                    //       $i = 1;
                    //   }
                    // else
                    $maincategory[] = $cat;
                }
                $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '&&&');
                $user_data['categorynames'] = $categorynames;
                $user_data['category_ids'] = $category_ids;
                $user_data['count_data'] = $cat_appr_build_cnt;

            } else {
                $categories = array();
                $user_data['categorynames'] = '';
                $user_data['category_ids'] = '';
                $user_data['count_data'] = '0';
            }

        } else {
            if (!empty($categories)) {

                foreach ($categories as $item) {
                    //$categorynames .= $item->category_name.',';
                    $k = 0; //variable for subcategory image index

                    $subcategories = Subcategory::where('category_id', $item->id)->get();
                    $subcat_count = Subcategory::where('category_id', $item->id)->get()->count();

                    $item['count'] = $subcat_count;
                    $appr_build_cnt = 0;
                    foreach ($subcategories as $subitem) {

                        if (strlen($subitem->subcategory_name) >= 12) {
                            $subcat_name = substr($subitem->subcategory_name, 0, 12) . '<br />';
                            $subcat_name .= substr($subitem->subcategory_name, 13, strlen($subitem->subcategory_name));
                        }

                        $result = array();
                        foreach ($builds as $builditem) {

                            $subcat_str = $builditem->subcategory;
                            $subcat_arr = explode(',', $subcat_str);
                            foreach ($subcat_arr as $subcat_id) {
                                if ($subitem->id == $subcat_id) {
                                    $result[] = $builditem;
                                }
                            }
                        }
                        $subitem['buildcount'] = sizeof($result);

                        $subitem['image'] = $imagename_list[$j % 4][$k];

                        $k = $k + 1;
                        $k = $k % 3;
                        $appr_build_cnt += sizeof($result);
                    }

                    $subcategories = $subcategories->toArray();
                    //sort by buildcount with subcategory
                    usort($subcategories, function ($a, $b) {
                        return $b['buildcount'] - $a['buildcount'];
                    });

                    $item['appr_build_cnt'] = $appr_build_cnt;
                    $item['circle_color'] = $circle_color_list[$j % 4];
                    $j++;
                    $item['subcateogry'] = $subcategories;
                    // $cat_appr_build_cnt .= $appr_build_cnt.',';

                }

                //sort actin by appr_build_count
                $categories = $categories->toArray();
                usort($categories, function ($a, $b) {
                    return $b['appr_build_cnt'] - $a['appr_build_cnt'];
                });

                foreach ($categories as $cat) {

                    foreach ($cat['subcateogry'] as $subcat) {
                        $user_data['hover_txt'] .= $subcat['subcategory_name'] . ' ' . $subcat['buildcount'] . '#';
                    }

                    $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '#');
                    $user_data['hover_txt'] .= '&&&';

                    $categorynames .= $cat['category_name'] . ',';
                    $category_ids .= $cat['id'] . ',';
                    $cat_appr_build_cnt .= $cat['appr_build_cnt'] . ',';
                    if ($i == 0) {
                        $firstcategory = $cat;
                        $i = 1;
                    } else {
                        $maincategory[] = $cat;
                    }

                }
                $user_data['hover_txt'] = rtrim($user_data['hover_txt'], '&&&');
                $user_data['categorynames'] = $categorynames;
                $user_data['category_ids'] = $category_ids;
                $user_data['count_data'] = $cat_appr_build_cnt;

            } else {
                $categories = array();
                $user_data['categorynames'] = '';
                $user_data['category_ids'] = '';
                $user_data['count_data'] = '0';
            }
        }
        $search_cat = '';

        return view('employeeportfolioIndependent')->with('testinomials_data', $testinomials_data)->with('user_data', $user_data)->with('categories', $categories)->with('maincategory', $maincategory)->with('search_categories', $search_categories)->with('search_cat', $search_cat);

    }

    public function getPos($build_id, $subcat_id, $startdate, $enddate)
    {

        $build = Builds::find($build_id);
        $build_ids = array();
        $employee = Employee::find($build->employee_id);

        $emp_id = $employee->id;
        $sub_id = $subcat_id;

        $subcategory = Subcategory::find($sub_id);

        $builds = Builds::where('employee_id', $emp_id)->whereIn('status', ['1','2'])->where('company_id', $employee->company_id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();

        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            if (in_array($sub_id, $subcat_arr)) {
                foreach ($subcat_arr as $imgitem) {
                    if ($sub_id == $imgitem) {
                        $build_ids[] = $item->id;
                    }
                }

            }
        }

        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;

        foreach ($build_ids as $b_id) {
            //get the next pos
            if ($nextflag == 0) {
                if ($currentflag == 1) {
                    $nextpos = $b_id;
                    $nextflag = 1;
                    break;
                }
            }
            //get the current pos
            if ($currentflag == 0) {
                if ($b_id == $build_id) {
                    $currentflag = 1;
                    $currentpos = $b_id;
                }
            }
            //get the pre pos
            if ($preflag == 0) {
                if ($currentflag == 0) {
                    $preflag == 1;
                    $prepos = $b_id;
                }
            }
        }
        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function getPosSearch($build_id, $subcat_id, $startdate, $enddate, $search_text)
    {

        $build = Builds::find($build_id);
        $build_ids = array();
        $employee = Employee::find($build->employee_id);

        $emp_id = $employee->id;
        $sub_id = $subcat_id;

        $subcategory = Subcategory::find($sub_id);

        $builds = Builds::where('employee_id', $emp_id)->whereIn('status', ['1','2'])->where('company_id', $employee->company_id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('build_text', 'like', '%' . $search_text . '%')->orderBy('created_at', 'desc')->get();

        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            if (in_array($sub_id, $subcat_arr)) {
                foreach ($subcat_arr as $imgitem) {
                    if ($sub_id == $imgitem) {
                        $build_ids[] = $item->id;
                    }
                }

            }
        }

        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;

        foreach ($build_ids as $b_id) {
            //get the next pos
            if ($nextflag == 0) {
                if ($currentflag == 1) {
                    $nextpos = $b_id;
                    $nextflag = 1;
                    break;
                }
            }
            //get the current pos
            if ($currentflag == 0) {
                if ($b_id == $build_id) {
                    $currentflag = 1;
                    $currentpos = $b_id;
                }
            }
            //get the pre pos
            if ($preflag == 0) {
                if ($currentflag == 0) {
                    $preflag == 1;
                    $prepos = $b_id;
                }
            }
        }
        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function get_BuildinfofromID(Request $request)
    {

        $html = '';
        $build = Builds::find($request['id']);
        $subcat_id = $request['subcat_id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $pos = $this->getPos($build->id, $subcat_id, $startdate, $enddate);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png');

        $html .= '<img class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; ////0

        $html .= '&&&';
        $type = 0;

        $subcat_arr = explode(',', $build->subcategory);

        $unique_arr = array_count_values($subcat_arr);

        $html1 = '';
        foreach ($unique_arr as $key => $value) {
            if ($key != "") {
                $subcat = Subcategory::where('id', intval($key))->first();
                $subcat_name = $subcat->subcategory_name;
                $html1 .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
            }
        }

        $html .= $html1; //1

        $build_text = $build->build_text;

        $html .= '&&&' . $build_text; //2
        $html .= '&&&' . $build->created_at; ///3
        //get the pre and next position
        $html .= '&&&' . $pos['prepos']; //4
        if ($pos['prepos'] == 0) {
            $html .= '&&&' . $pos['prepos'];
        }
        //5
        else {
            $html .= '&&&' . $pos['prepos'] . '-' . $subcat_id;
        }

        $html .= '&&&' . $pos['nextpos']; //6
        if ($pos['nextpos'] == 0) {
            $html .= '&&&' . $pos['nextpos'];
        }
        //7
        else {
            $html .= '&&&' . $pos['nextpos'] . '-' . $subcat_id;
        }

        //for expand
        $html .= '&&&' . $src; //8

        //get the type
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
            }
        }
        $html .= '&&&' . $type; //9

        //$user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        // {
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);
            if (!empty($chal)) {

                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');

                $html .= '&&&' . '<img style="width:30%;height:100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10

            }
        }
        // }
        // else{
        //   //build with challenge
        //   if($build->challenge_id != 0){
        //        $chal = Challenge::find($build->challenge_id);
        //        if(!empty($chal)){

        //            $chal_src =  $chal->image != '' ? Storage::disk('s3')->url('images/challenge').'/'.$chal->image :  Storage::disk('s3')->url('images/no_image.png');

        //            $html .= '&&&'.'<img style="width:30%;height: auto;object-fit: cover;" src="'.$chal_src.'" alt="challenge_image">';           //10

        //        }
        //   }
        // }

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function get_BuildinfofromIDForIndependent(Request $request)
    {

        $html = '';
        $build = Builds::find($request['id']);
        $subcat_id = $request['subcat_id'];

        $batch = Batch::find($build->batch_id);

        $full_name = "";

        if ($build->status == 2) {
            $full_name = "";
        }
        else if (!empty($batch)) {
            $full_name = $batch->firstname . ' ' . $batch->lastname;
        }

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $pos = $this->getPos($build->id, $subcat_id, $startdate, $enddate);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png');

        $html .= '<img class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; ////0

        $html .= '&&&';
        $type = 0;

        $subcat_arr = explode(',', $build->subcategory);

        $unique_arr = array_count_values($subcat_arr);

        $html1 = '';
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id',intval($key))->first();
//            $subcat = Categories::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
//            $subcat_name = $subcat->category_name;
            $html1 .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }

        $html .= $html1; //1

        $build_text = $build->build_text;

        $html .= '&&&' . $build_text; //2

        $tz = $build->created_at; // "2019-01-16 18:21:31" (UTC Time)
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $tz, 'US/Pacific');
        $date->setTimezone('America/New_York');

        $html .= '&&&' . $date; ///3

        //get the pre and next position
        $html .= '&&&' . $pos['prepos']; //4
        if ($pos['prepos'] == 0) {
            $html .= '&&&' . $pos['prepos'];
        }
        //5
        else {
            $html .= '&&&' . $pos['prepos'] . '-' . $subcat_id;
        }

        $html .= '&&&' . $pos['nextpos']; //6
        if ($pos['nextpos'] == 0) {
            $html .= '&&&' . $pos['nextpos'];
        }
        //7
        else {
            $html .= '&&&' . $pos['nextpos'] . '-' . $subcat_id;
        }

        //for expand
        $html .= '&&&' . $src; //8

        //get the type
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
            }
        }
        $html .= '&&&' . $type; //9

        //$user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        // {
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);
            if (!empty($chal)) {

                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');

                $html .= '&&&' . '<img style="width:30%;height:100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10

            }
        }

        return response()->json(['status' => true, 'html' => $html, 'batch_full_name' => $full_name]);

    }

    public function get_BuildinfofromIDSearch(Request $request)
    {

        $html = '';
        $build = Builds::find($request['id']);
        $subcat_id = $request['subcat_id'];
        $search_text = $request['search_text'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $pos = $this->getPosSearch($build->id, $subcat_id, $startdate, $enddate, $search_text);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png');

        $html .= '<img  class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; ////0

        $html .= '&&&';
        $type = 0;

        $subcat_arr = explode(',', $build->subcategory);

        $unique_arr = array_count_values($subcat_arr);

        $html1 = '';
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
            $html1 .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }

        $html .= $html1; //1

        $build_text = $build->build_text;

        $html .= '&&&' . $build_text; //2
        $html .= '&&&' . $build->created_at; ///3
        //get the pre and next position
        $html .= '&&&' . $pos['prepos']; //4
        if ($pos['prepos'] == 0) {
            $html .= '&&&' . $pos['prepos'];
        }
        //5
        else {
            $html .= '&&&' . $pos['prepos'] . '-' . $subcat_id;
        }

        $html .= '&&&' . $pos['nextpos']; //6
        if ($pos['nextpos'] == 0) {
            $html .= '&&&' . $pos['nextpos'];
        }
        //7
        else {
            $html .= '&&&' . $pos['nextpos'] . '-' . $subcat_id;
        }

        //for expand
        $html .= '&&&' . $src; //8

        //get the type
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
            }
        }
        $html .= '&&&' . $type; //9
        // $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        // {
        //build with challenge
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);
            if (!empty($chal)) {

                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');

                $html .= '&&&' . '<img style=" width: 30%;height: 100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10

            }
        }

        // }
        // else{
        //       //build with challenge
        //       if($build->challenge_id != 0){
        //            $chal = Challenge::find($build->challenge_id);
        //            if(!empty($chal)){

        //                $chal_src =  $chal->image != '' ? Storage::disk('s3')->url('images/challenge').'/'.$chal->image :  Storage::disk('s3')->url('images/no_image.png');

        //                $html .= '&&&'.'<img style=" width: 30%;height: auto;object-fit: cover;" src="'.$chal_src.'" alt="challenge_image">';           //10

        //            }
        //       }

        // }

        return response()->json(['status' => true, 'html' => $html]);

    }

    public function getChallengePos($emp_id, $type, $id, $start, $end)
    {

        $employee = Employee::find($emp_id);
        $enddate = $end;
        $startdate = $start;

        switch ($type) {
            case 1: //in the case regional challenge
                $challenge = $this->getRegionalChallengeByDate($employee, $startdate, $enddate);

                break;
            case 2: //in the case duel challenge
                $challenge = $this->getDuelByDate($employee, $startdate, $enddate);

                break;
            case 3: //in the case company challenge
                $challenge = $this->getCompanyChallengeByDate($employee, $startdate, $enddate);

                break;
            default:
                break;
        }
        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;
        if (!empty($challenge)) {
            foreach ($challenge as $chal) {
                //get the next pos
                if ($nextflag == 0) {
                    if ($currentflag == 1) {
                        $nextpos = $chal['id'];
                        $nextflag = 1;
                        break;
                    }
                }
                //get the current pos
                if ($currentflag == 0) {
                    if ($chal['id'] == $id) {
                        $currentflag = 1;
                        $currentpos = $chal['id'];
                    }
                }
                //get the pre pos
                if ($preflag == 0) {
                    if ($currentflag == 0) {
                        $preflag == 1;
                        $prepos = $chal['id'];
                    }
                }
            }
        }

        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function getChallengePosSearch($emp_id, $type, $id, $start, $end, $search_text)
    {

        $employee = Employee::find($emp_id);
        $enddate = $end;
        $startdate = $start;

        switch ($type) {
            case 1: //in the case regional challenge
                $challenge = $this->getRegionalChallengeByDateSearch($employee, $startdate, $enddate, $search_text);

                break;
            case 2: //in the case duel challenge
                $challenge = $this->getDuelByDateSearch($employee, $startdate, $enddate, $search_text);

                break;
            case 3: //in the case company challenge
                $challenge = $this->getCompanyChallengeByDateSearch($employee, $startdate, $enddate, $search_text);

                break;
            default:
                break;
        }
        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;
        if (!empty($challenge)) {
            foreach ($challenge as $chal) {
                //get the next pos
                if ($nextflag == 0) {
                    if ($currentflag == 1) {
                        $nextpos = $chal['id'];
                        $nextflag = 1;
                        break;
                    }
                }
                //get the current pos
                if ($currentflag == 0) {
                    if ($chal['id'] == $id) {
                        $currentflag = 1;
                        $currentpos = $chal['id'];
                    }
                }
                //get the pre pos
                if ($preflag == 0) {
                    if ($currentflag == 0) {
                        $preflag == 1;
                        $prepos = $chal['id'];
                    }
                }
            }
        }

        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function get_ChallengeinfofromID(Request $request)
    {

        $html = '';
        $build = Builds::find($request['id']);
        $type = $request['type'];
        $employee_id = $request['emp_id'];
        $start = $request['start'];
        $end = $request['end'];
        $start_arr = explode('-', $start);
        $end_arr = explode('-', $end);
        $start = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $end = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $start = date('Y-m-d H:i:s', strtotime($start));
        $end = date('Y-m-d H:i:s', strtotime($end));

        $pos = $this->getChallengePos($employee_id, $type, $request['id'], $start, $end);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png');
        $html .= '<img class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; //0

        $html1 = '';

        $subcat_arr = explode(',', $build->subcategory);
        $unique_arr = array_count_values($subcat_arr);
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
            $html1 .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }

        $html .= '&&&' . $html1; //1
        $html .= '&&&' . $build->build_text; //2
        $html .= '&&&' . $build->created_at; //3

        //get the pre and next position
        $html .= '&&&' . $pos['prepos']; //4
        if ($pos['prepos'] == 0) {
            $html .= '&&&' . $pos['prepos'];
        }
        //5
        else {
            $html .= '&&&' . $pos['prepos'] . '-' . $type;
        }

        $html .= '&&&' . $pos['nextpos']; //6
        if ($pos['nextpos'] == 0) {
            $html .= '&&&' . $pos['nextpos'];
        }
        //7
        else {
            $html .= '&&&' . $pos['nextpos'] . '-' . $type;
        }

        //for expand
        $html .= '&&&' . $src; //8
        $type = 1;
        $html .= '&&&' . $type; //9

        $chal = Challenge::find($build->challenge_id);
        if (!empty($chal)) {
            $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');
            $html .= '&&&' . '<img style="width:30%;height:100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10

        }

        return response()->json(['status' => true, 'html' => $html]);

    }

    public function get_ChallengeinfofromIDSearch(Request $request)
    {

        $html = '';
        $build = Builds::find($request['id']);
        $type = $request['type'];
        $employee_id = $request['emp_id'];
        $start = $request['start'];
        $end = $request['end'];
        $search_text = $request['search_text'];

        $start_arr = explode('-', $start);
        $end_arr = explode('-', $end);
        $start = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $end = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $start = date('Y-m-d H:i:s', strtotime($start));
        $end = date('Y-m-d H:i:s', strtotime($end));

        $pos = $this->getChallengePosSearch($employee_id, $type, $request['id'], $start, $end, $search_text);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png');
        $html .= '<img class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; //0

        $html1 = '';

        $subcat_arr = explode(',', $build->subcategory);
        $unique_arr = array_count_values($subcat_arr);
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
            $html1 .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }

        $html .= '&&&' . $html1; //1
        $html .= '&&&' . $build->build_text; //2
        $html .= '&&&' . $build->created_at; //3

        //get the pre and next position
        $html .= '&&&' . $pos['prepos']; //4
        if ($pos['prepos'] == 0) {
            $html .= '&&&' . $pos['prepos'];
        }
        //5
        else {
            $html .= '&&&' . $pos['prepos'] . '-' . $type;
        }

        $html .= '&&&' . $pos['nextpos']; //6
        if ($pos['nextpos'] == 0) {
            $html .= '&&&' . $pos['nextpos'];
        }
        //7
        else {
            $html .= '&&&' . $pos['nextpos'] . '-' . $type;
        }

        //for expand
        $html .= '&&&' . $src; //8
        $type = 1;
        $html .= '&&&' . $type; //9

        $chal = Challenge::find($build->challenge_id); //10

        if (!empty($chal)) {
            $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');
            $html .= '&&&' . '<img style="width:30%;height:100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10

        }
        return response()->json(['status' => true, 'html' => $html]);

    }

    public function get_BuildsfromsubcatIDDate(Request $request)
    {

        $emp_id = $request['emp_id'];
        $employee = Employee::find($emp_id);
        $sub_id = $request['id'];
        $subcategory = Subcategory::find($sub_id);
        $category_id = $subcategory->category_id;
        $builds = Builds::where('employee_id', $emp_id)->whereIn('status', ['1','2'])->where('company_id', $employee->company_id)->where('updated_at', '>=', $request['startdate'])->where('updated_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();

        $html = '';
        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            if (in_array($sub_id, $subcat_arr)) {
                foreach ($subcat_arr as $imgitem) {
                    if ($sub_id == $imgitem) {
                        $src = $item->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $item->image : Storage::disk('s3')->url('images/no_image.png');
                        $html .= '<div class="slider-item">  <img id="' . $item->id . '" src="' . $src . '" alt="challenge_image" class = "slider-image"> </div>';

                    }
                }

            }
        }
        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Build']);
        } else {
            $html = $category_id . ',' . $html;
            return response()->json(['status' => true, 'html' => $html]);
        }

    }

    public function get_BuildsfromsubcatID(Request $request)
    {

        $emp_id = $request['emp_id'];
        $employee = Employee::find($emp_id);
        $sub_id = $request['id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $subcategory = Subcategory::find($sub_id);
        $category_id = $subcategory->category_id;

        $search = request('search');

        $buildTable = (new Builds())->getTable();
        $categoryTable = (new Categories())->getTable();
        $subCategoryTable = (new Subcategory())->getTable();

        $builds = Builds::query()
            ->select([
                "{$buildTable}.*",
                "{$subCategoryTable}.id as subcategory_id",
                "{$subCategoryTable}.subcategory_name",
                "{$categoryTable}.id as category_id",
                "{$categoryTable}.category_name",
            ])
            ->leftJoin($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->leftJoin($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->when(!empty($request->search), function ($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                return $query->where(function($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                    $query->whereRaw("LOWER({$buildTable}.build_text) LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$categoryTable}.category_name LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$subCategoryTable}.subcategory_name LIKE '%" . strtolower(request('search')) . "%'");
                });
            })
            ->whereRaw("FIND_IN_SET({$sub_id}, {$buildTable}.subcategory) > 0")
            ->whereIn("{$buildTable}.status", ['1','2'])
            ->where("{$buildTable}.employee_id", $employee->id)
            ->where("{$buildTable}.company_id", $employee->company_id)
            ->where("{$buildTable}.created_at", '>=', $startdate)
            ->where("{$buildTable}.created_at", '<=', $enddate)
            ->orderBy("{$buildTable}.created_at", 'desc')
            ->groupBy("{$buildTable}.id")
            ->get();

        $html = '';
        $count = -1;
        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            if (in_array($sub_id, $subcat_arr)) {

                foreach ($subcat_arr as $imgitem) {
                    if ($sub_id == $imgitem) {

                        $src = $item->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $item->image : Storage::disk('s3')->url('images/no_image.png');

                        $count++;
                        if ($count % 16 == 0) {
                            $html .= '<div class="slider-item">';
                        }
                        if ($count % 4 == 0) {
                            $html .= '<div class="row">';
                        }
                        $html .= '<div style="flex-basis: 25%;padding:2px" class="mItem"><span id="' . $item->id . '-' . $sub_id . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';

                        if ($count % 4 == 3) {
                            $html .= '</div>';
                        }
                        if ($count % 16 == 15) {
                            $html .= '</div>';
                        }
                    }
                }
            }
        }
        if ($count % 4 != 3) {
            $html .= '</div>';
        }
        if ($count % 16 != 15) {
            $html .= '</div>';
        }
        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Build']);
        } else {
            $html = $category_id . ',' . $html;
            return response()->json(['status' => true, 'html' => $html]);
        }
    }

    public function get_BuildsfromcatIDForIndependent(Request $request)
    {
        // dd($request->all());
        $emp_id = $request['emp_id'];
        $employee = Employee::find($emp_id);
        $category_id = $request['id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $buildTable = (new Builds())->getTable();
        $categoryTable = (new Categories())->getTable();
        $subCategoryTable = (new Subcategory())->getTable();

        $builds = Builds::query()
            ->select([
                "{$buildTable}.*",
                "{$subCategoryTable}.id as subcategory_id",
                "{$subCategoryTable}.subcategory_name",
                "{$categoryTable}.id as category_id",
                "{$categoryTable}.category_name",
            ])
            ->join($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->join($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->when(!empty($request->search), function ($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                return $query->where(function($query) use ($buildTable, $categoryTable, $subCategoryTable) {
                    $query->whereRaw("LOWER({$buildTable}.build_text) LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$categoryTable}.category_name LIKE '%" . strtolower(request('search')) . "%'")
                        ->orWhereRaw("{$subCategoryTable}.subcategory_name LIKE '%" . strtolower(request('search')) . "%'");
                });
            })
            ->whereIn("{$buildTable}.status", ['1','2'])
            ->where("{$subCategoryTable}.id", $category_id) //->where("{$categoryTable}.id", $category_id)
            ->where("{$buildTable}.employee_id", $employee->id)
            ->where("{$buildTable}.company_id", $employee->company_id)
            ->where("{$buildTable}.created_at", '>=', $startdate)
            ->where("{$buildTable}.created_at", '<=', $enddate)
            ->orderBy("{$buildTable}.created_at", 'desc')
            ->groupBy("{$buildTable}.id")
            ->get();

        $html = '';
        $count = -1;
        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            //            if (in_array($category_id, $subcat_arr)) {

            //                foreach ($subcat_arr as $imgitem) {
            //                    if ($category_id == $imgitem) {

                        $src = $item->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $item->image : Storage::disk('s3')->url('images/no_image.png');

                        $count++;
                        if ($count % 16 == 0) {
                            $html .= '<div class="slider-item">';
                        }
                        if ($count % 4 == 0) {
                            $html .= '<div class="row">';
                        }

                        $html .= '<div style="flex-basis: 25%;padding:2px" class="mItem"><span id="' . $item->id . '-' . $category_id . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';

                        if ($count % 4 == 3) {
                            $html .= '</div>';
                        }
                        if ($count % 16 == 15) {
                            $html .= '</div>';
                        }
            //                    }
            //                }

            //            }
        }

        if ($count % 4 != 3) {
            $html .= '</div>';
        }
        if ($count % 16 != 15) {
            $html .= '</div>';
        }

        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Build']);
        } else {
            $html = $category_id . ',' . $html;
            return response()->json(['status' => true, 'html' => $html]);
        }
    }

    public function getbuildsfromtestinomial(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];

        $status = $request['status'];
        $html = '';

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $result = array();
        $builds = Builds::where('employee_id', $cur_emp_id)->where('status', $status)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();

//        if (is_numeric($testinomial_emp_id)) {
//            $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();
//        } else {
//            $validates = Validations::where('batch_id', $testinomial_emp_id)->where('status', $status)->get();
//        }

        $validates = Validations::reviewedBy($testinomial_emp_id)
            ->status($status)
            ->get();

        foreach ($builds as $build) {
            foreach ($validates as $validate) {
                if ($build->id == $validate->build_id) {
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }
        if (!empty($result)) {
            foreach ($result as $item) {
                $src = $item['image'] != '' ? Storage::disk('s3')->url('images/build') . '/' . $item['image'] : Storage::disk('s3')->url('images/no_image.png');
                if (is_numeric($testinomial_emp_id)) {
                    $html .= '<div class="slider-item"><span id="' . $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $item['validate_id'] . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';
                } else {
                    $html .= '<div class="slider-item"><span id="' . $cur_emp_id . '/' . $testinomial_emp_id . '/' . $status . '/' . $item['validate_id'] . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';
                }

            }
        }
        return response()->json(['status' => true, 'html' => $html]);

    }

    //get testinomial from search
    public function getbuildsfromtestinomialbysearch(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];
        $status = $request['status'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $search_text = $request['search_text'];
        $html = '';
        $result = array();
        $builds = Builds::where('employee_id', $cur_emp_id)->where('status', $status)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('build_text', 'like', '%' . $search_text . '%')->orderBy('created_at', 'desc')->get();

        $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();

        foreach ($builds as $build) {
            foreach ($validates as $validate) {
                if ($build->id == $validate->build_id) {
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }
        if (!empty($result)) {
            foreach ($result as $item) {
                $src = $item['image'] != '' ? Storage::disk('s3')->url('images/build') . '/' . $item['image'] : Storage::disk('s3')->url('images/no_image.png');

                $html .= '<div class="slider-item"><span id="' . $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $item['validate_id'] . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';
            }
        }
        return response()->json(['status' => true, 'html' => $html]);

    }

    //get testinomial from category filter
    public function getbuildsfromtestinomialbycategory(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];
        $status = $request['status'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));
        $search_cat = $request['category'];

        $html = '';
        $result = array();

        $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();

        $employee = Employee::find($cur_emp_id);
        $categories = Categories::where('company_id', $employee->company_id)->where('category_name', $search_cat)->get();

        $builds = Builds::where('employee_id', $employee->id)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->where('status', '!=', '-1')->orderBy('created_at', 'desc')->get();

        $ids = array();

        if (!empty($categories)) {

            foreach ($categories as $item) {

                $subcategories = Subcategory::where('category_id', $item->id)->get();

                foreach ($subcategories as $subitem) {

                    foreach ($builds as $builditem) {
                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($subitem->id == $subcat_id) {
                                $ids[] = $builditem->id;
                            }
                        }
                    }
                }
            }
        }

        $ids = array_unique($ids);
        foreach ($ids as $temp) {
            foreach ($validates as $validate) {
                if ($temp == $validate->build_id) {
                    $build = Builds::find($temp);
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }
        if (!empty($result)) {
            foreach ($result as $item) {
                $src = $item['image'] != '' ? Storage::disk('s3')->url('images/build') . '/' . $item['image'] : Storage::disk('s3')->url('images/no_image.png');

                $html .= '<div class="slider-item"><span id="' . $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $item['validate_id'] . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';
            }
        }
        return response()->json(['status' => true, 'html' => $html]);

    }

    public function getbuildinfofromtestinomialdata(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];
        $status = $request['status'];
        $validate_id = $request['validate_id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $validate = Validations::find($validate_id);
        $build = Builds::find($validate->build_id);

        $pos = $this->getTestinomialPos($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png'); //8

        $build_img = '<img  class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; //0
        $subcats = ''; //1
        $subcat_arr = explode(',', $build->subcategory);
        $unique_arr = array_count_values($subcat_arr);
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat ? $subcat->subcategory_name : '';
            $subcats .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }
        $build_text = $build->build_text; //2
        $created_at = $build->created_at . ''; ///3
        $prepos = $pos['prepos']; //4
        $left_arrow = 0;
        if ($prepos != 0) {
            if (is_numeric($testinomial_emp_id)) {
                $left_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $prepos;
            } else {
                $left_arrow = $cur_emp_id . '/' . $testinomial_emp_id . '/' . $status . '/' . $prepos;
            }

        }
        //5
        $nextpos = $pos['nextpos']; //6
        $right_arrow = 0;
        if ($nextpos != 0) {
            if (is_numeric($testinomial_emp_id)) {
                $right_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $nextpos;
            }
            //7
            else {
                $right_arrow = $cur_emp_id . '/' . $testinomial_emp_id . '/' . $status . '/' . $nextpos;
            }
            //7
        }
        //8 - src
        //get the type
        $type = 0;
        $chal_image = '';

        //  $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        // {
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');
                $chal_image = '<img style=" width: 30%;height: 100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10
            }
        }
        // }
        // else{
        //         if($build->challenge_id != 0){
        //              $chal = Challenge::find($build->challenge_id);

        //              if(!empty($chal)){
        //                 $type = 1;
        //                 $chal_src =  $chal->image != '' ? Storage::disk('s3')->url('images/challenge').'/'.$chal->image :  Storage::disk('s3')->url('images/no_image.png');
        //                 $chal_image = '<img style=" width: 30%;height: auto;object-fit: cover;" src="'.$chal_src.'" alt="challenge_image">';           //10
        //               }
        //         }
        // }

        //9

        return response()->json(['status' => true, 'build_image' => $build_img, 'subcats' => $subcats,
            'build_text' => $build_text, 'created_at' => $created_at, 'prepos' => $prepos, 'left_arrow' => $left_arrow, 'nextpos' => $nextpos, 'right_arrow' => $right_arrow, 'src' => $src, 'type' => $type, 'chal_image' => $chal_image]);

    }

    public function getTestinomialPos($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate)
    {

        $builds = Builds::where('employee_id', $cur_emp_id)->where('status', $status)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();

        if (is_numeric($testinomial_emp_id)) {
            $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();
        } else {
            $validates = Validations::where('batch_id', $testinomial_emp_id)->where('status', $status)->get();
        }

        $result = array();
        foreach ($builds as $build) {
            foreach ($validates as $validate) {
                if ($build->id == $validate->build_id) {
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }

        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;

        foreach ($result as $res) {
            //get the next pos
            if ($nextflag == 0) {
                if ($currentflag == 1) {
                    $nextpos = $res['validate_id'];
                    $nextflag = 1;
                    break;
                }
            }
            //get the current pos
            if ($currentflag == 0) {
                if ($res['validate_id'] == $validate_id) {
                    $currentflag = 1;
                    $currentpos = $res['validate_id'];
                }
            }
            //get the pre pos
            if ($preflag == 0) {
                if ($currentflag == 0) {
                    $preflag == 1;
                    $prepos = $res['validate_id'];
                }
            }
        }
        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function getbuildinfofromtestinomialdatabysearch(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];
        $status = $request['status'];
        $validate_id = $request['validate_id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));
        $search_text = $request['search_text'];

        $validate = Validations::find($validate_id);
        $build = Builds::find($validate->build_id);

        $pos = $this->getTestinomialPosBySearch($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate, $search_text);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png'); //8

        $build_img = '<img  class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; //0
        $subcats = ''; //1
        $subcat_arr = explode(',', $build->subcategory);
        $unique_arr = array_count_values($subcat_arr);
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
            $subcats .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }
        $build_text = $build->build_text; //2
        $created_at = $build->created_at . ''; ///3
        $prepos = $pos['prepos']; //4
        $left_arrow = 0;
        if ($prepos != 0) {
            if (is_numeric($testinomial_emp_id)) {
                $left_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $prepos;
            } else {
                $left_arrow = $cur_emp_id . '/' . $testinomial_emp_id . '/' . $status . '/' . $prepos;
            }

        }
        //5
        $nextpos = $pos['nextpos']; //6
        $right_arrow = 0;
        if ($nextpos != 0) {
            if (is_numeric($testinomial_emp_id)) {
                $right_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $nextpos;
            }
            //7
            else {
                $right_arrow = $cur_emp_id . '/' . $testinomial_emp_id . '/' . $status . '/' . $nextpos;
            }
            //7
        }
        //8 - src
        //get the type
        $type = 0;
        $chal_image = '';
        //$user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        // {

        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');
                $chal_image = '<img style=" width: 30%;height: 100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10
            }
        }
        // }
        // else{

        //       if($build->challenge_id != 0){
        //            $chal = Challenge::find($build->challenge_id);

        //            if(!empty($chal)){
        //               $type = 1;
        //               $chal_src =  $chal->image != '' ? Storage::disk('s3')->url('images/challenge').'/'.$chal->image :  Storage::disk('s3')->url('images/no_image.png');
        //               $chal_image = '<img style=" width: 30%;height: auto;object-fit: cover;" src="'.$chal_src.'" alt="challenge_image">';           //10
        //             }
        //       }
        // }

        //9

        return response()->json(['status' => true, 'build_image' => $build_img, 'subcats' => $subcats,
            'build_text' => $build_text, 'created_at' => $created_at, 'prepos' => $prepos, 'left_arrow' => $left_arrow, 'nextpos' => $nextpos, 'right_arrow' => $right_arrow, 'src' => $src, 'type' => $type, 'chal_image' => $chal_image]);

    }

    public function getTestinomialPosBySearch($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate, $search_text)
    {

        $builds = Builds::where('employee_id', $cur_emp_id)->where('build_text', 'like', '%' . $search_text . '%')->where('status', $status)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();
        $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();

        $result = array();
        foreach ($builds as $build) {
            foreach ($validates as $validate) {
                if ($build->id == $validate->build_id) {
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }

        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;

        foreach ($result as $res) {
            //get the next pos
            if ($nextflag == 0) {
                if ($currentflag == 1) {
                    $nextpos = $res['validate_id'];
                    $nextflag = 1;
                    break;
                }
            }
            //get the current pos
            if ($currentflag == 0) {
                if ($res['validate_id'] == $validate_id) {
                    $currentflag = 1;
                    $currentpos = $res['validate_id'];
                }
            }
            //get the pre pos
            if ($preflag == 0) {
                if ($currentflag == 0) {
                    $preflag == 1;
                    $prepos = $res['validate_id'];
                }
            }
        }
        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function getbuildinfofromtestinomialdatabycategory(Request $request)
    {

        $cur_emp_id = $request['cur_emp_id'];
        $testinomial_emp_id = $request['testinomial_emp_id'];
        $status = $request['status'];
        $validate_id = $request['validate_id'];

        $start_arr = explode('-', $request['start']);
        $end_arr = explode('-', $request['end']);

        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));
        $search_cat = $request['category'];

        $validate = Validations::find($validate_id);
        $build = Builds::find($validate->build_id);

        $pos = $this->getTestinomialPosByCategory($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate, $search_cat);

        $src = $build->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $build->image : Storage::disk('s3')->url('images/no_image.png'); //8

        $build_img = '<img  class="image_fit_css" style="height:100%" src="' . $src . '" alt="build image">'; //0
        $subcats = ''; //1
        $subcat_arr = explode(',', $build->subcategory);
        $unique_arr = array_count_values($subcat_arr);
        foreach ($unique_arr as $key => $value) {
            $subcat = Subcategory::where('id', intval($key))->first();
            $subcat_name = $subcat->subcategory_name;
            $subcats .= '<div class="category-item">' . $subcat_name . '<span class="badge badge-css">' . $value . '</span>' . '</div>';
        }
        $build_text = $build->build_text; //2
        $created_at = $build->created_at . ''; ///3
        $prepos = $pos['prepos']; //4
        $left_arrow = 0;
        if ($prepos != 0) {
            $left_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $prepos;
        }

        //5
        $nextpos = $pos['nextpos']; //6
        $right_arrow = 0;
        if ($nextpos != 0) {
            $right_arrow = $cur_emp_id . '-' . $testinomial_emp_id . '-' . $status . '-' . $nextpos;
        }
        //7
        //8 - src
        //get the type
        $type = 0;
        $chal_image = '';
        //   $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // if (stripos( $user_agent, 'Safari') !== false)
        //  {
        if ($build->challenge_id != 0) {
            $chal = Challenge::find($build->challenge_id);

            if (!empty($chal)) {
                $type = 1;
                $chal_src = $chal->image != '' ? Storage::disk('s3')->url('images/challenge') . '/' . $chal->image : Storage::disk('s3')->url('images/no_image.png');
                $chal_image = '<img style=" width: 30%;height: 100px;object-fit: cover;" src="' . $chal_src . '" alt="challenge_image">'; //10
            }
        }
        // }
        // else{
        //        if($build->challenge_id != 0){
        //             $chal = Challenge::find($build->challenge_id);

        //             if(!empty($chal)){
        //                $type = 1;
        //                $chal_src =  $chal->image != '' ? Storage::disk('s3')->url('images/challenge').'/'.$chal->image :  Storage::disk('s3')->url('images/no_image.png');
        //                $chal_image = '<img style=" width: 30%;height: auto;object-fit: cover;" src="'.$chal_src.'" alt="challenge_image">';           //10
        //              }
        //        }
        // }

        //9

        return response()->json(['status' => true, 'build_image' => $build_img, 'subcats' => $subcats,
            'build_text' => $build_text, 'created_at' => $created_at, 'prepos' => $prepos, 'left_arrow' => $left_arrow, 'nextpos' => $nextpos, 'right_arrow' => $right_arrow, 'src' => $src, 'type' => $type, 'chal_image' => $chal_image]);

    }

    public function getTestinomialPosByCategory($cur_emp_id, $testinomial_emp_id, $status, $validate_id, $startdate, $enddate, $search_cat)
    {

        $builds = Builds::where('employee_id', $cur_emp_id)->where('status', $status)->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->orderBy('created_at', 'desc')->get();
        $validates = Validations::where('employee_id', $testinomial_emp_id)->where('status', $status)->get();
        $employee = Employee::find($cur_emp_id);
        $categories = Categories::where('company_id', $employee->company_id)->where('category_name', $search_cat)->get();

        $ids = array();

        if (!empty($categories)) {

            foreach ($categories as $item) {

                $subcategories = Subcategory::where('category_id', $item->id)->get();

                foreach ($subcategories as $subitem) {

                    foreach ($builds as $builditem) {
                        $subcat_str = $builditem->subcategory;
                        $subcat_arr = explode(',', $subcat_str);
                        foreach ($subcat_arr as $subcat_id) {
                            if ($subitem->id == $subcat_id) {
                                $ids[] = $builditem->id;
                            }
                        }
                    }
                }
            }
        }

        $ids = array_unique($ids);
        $result = array();
        foreach ($ids as $temp) {
            foreach ($validates as $validate) {
                if ($temp == $validate->build_id) {
                    $build = Builds::find($temp);
                    $build->validate_id = $validate->id;
                    $result[] = $build;
                }
            }
        }

        $prepos = 0;
        $nextpos = 0;
        $currentpos = 0;
        $preflag = 0;
        $currentflag = 0;
        $nextflag = 0;

        foreach ($result as $res) {
            //get the next pos
            if ($nextflag == 0) {
                if ($currentflag == 1) {
                    $nextpos = $res['validate_id'];
                    $nextflag = 1;
                    break;
                }
            }
            //get the current pos
            if ($currentflag == 0) {
                if ($res['validate_id'] == $validate_id) {
                    $currentflag = 1;
                    $currentpos = $res['validate_id'];
                }
            }
            //get the pre pos
            if ($preflag == 0) {
                if ($currentflag == 0) {
                    $preflag == 1;
                    $prepos = $res['validate_id'];
                }
            }
        }
        return $pos = array(
            'prepos' => $prepos,
            'currentpos' => $currentpos,
            'nextpos' => $nextpos,
        );

    }

    public function get_BuildsfromsubcatIDSearch(Request $request)
    {

        $emp_id = $request['emp_id'];
        $employee = Employee::find($emp_id);
        $sub_id = $request['id'];
        $search_text = $request['search_text'];
        $subcategory = Subcategory::find($sub_id);
        $category_id = $subcategory->category_id;
        $builds = Builds::where('employee_id', $emp_id)->whereIn('status', ['1','2'])->where('company_id', $employee->company_id)->where('build_text', 'like', '%' . $search_text . '%')->orderBy('created_at', 'desc')->get();
        $html = '';
        $count = -1;
        foreach ($builds as $item) {
            $subcat_str = $item->subcategory;
            $subcat_arr = explode(',', $subcat_str);
            $subcat_arr = array_unique($subcat_arr);
            if (in_array($sub_id, $subcat_arr)) {

                foreach ($subcat_arr as $imgitem) {
                    if ($sub_id == $imgitem) {

                        $src = $item->image != '' ? Storage::disk('s3')->url('images/build') . '/' . $item->image : Storage::disk('s3')->url('images/no_image.png');

                        $count++;
                        if ($count % 16 == 0) {
                            $html .= '<div class="slider-item">';
                        }
                        if ($count % 4 == 0) {
                            $html .= '<div class="row">';
                        }
                        $html .= '<div style="flex-basis: 25%;padding:2px" class="mItem"><span id="' . $item->id . '-' . $sub_id . '" class="slider-image"><a href="" data-srcset="' . $src . '" data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="challenge_image" class = "preview"></a></span></div>';

                        if ($count % 4 == 3) {
                            $html .= '</div>';
                        }
                        if ($count % 16 == 15) {
                            $html .= '</div>';
                        }
                    }
                }

            }
        }
          if ($count % 4 != 3) {
            $html .= '</div>';
        }
        if ($count % 16 != 15) {
            $html .= '</div>';
        }

        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Build']);
        } else {
            $html = $category_id . ',' . $html;
            return response()->json(['status' => true, 'html' => $html]);
        }
    }

    public function getchallengeimageByDate(Request $request)
    {

        $employee = Employee::find($request['emp_id']);
        $enddate = $request['end'];
        $startdate = $request['start'];

        $start_arr = explode('-', $startdate);
        $end_arr = explode('-', $enddate);
        $startdate = $start_arr[2] . '-' . $start_arr[0] . '-' . $start_arr[1] . ' 00:00:00';
        $enddate = $end_arr[2] . '-' . $end_arr[0] . '-' . $end_arr[1] . ' 23:59:59';
        $startdate = date('Y-m-d H:i:s', strtotime($startdate));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate));

        $type = 0;
        switch ($request['id']) {
            case 1: //in the case regional challenge
                $challenge = $this->getRegionalChallengeByDate($employee, $startdate, $enddate);
                $type = 1;
                break;
            case 2: //in the case duel challenge
                $challenge = $this->getDuelByDate($employee, $startdate, $enddate);
                $type = 2;
                break;
            case 3: //in the case company challenge
                $challenge = $this->getCompanyChallengeByDate($employee, $startdate, $enddate);
                $type = 3;
                break;
            default:

                break;
        }

        $html = '';

        foreach ($challenge as $item) {

            $category = Categories::where('id', $item['category_id'])->first();
            if (!empty($category)) {
                $category_name = $category->category_name;
            } else {
                $category_name = '';
            }

            $src = $item['image'] != '' ? Storage::disk('s3')->url('images/build') . '/' . $item['image'] : Storage::disk('s3')->url('images/no_image.png');

            $html .= '<div class="slider-item"><span id="' . $item['id'] . '-' . $type . '" class = "slider-image"><a href=""  data-srcset="' . $src . '"  data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="' . $item['build_text'] . '"  class="preview" name="' . $category_name . '"></a></span> </div>';

        }

        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Submission']);
        }

        return response()->json(['status' => true, 'html' => $html]);

    }

    public function getchallengeimageByDateSearch(Request $request)
    {

        $employee = Employee::find($request['emp_id']);
        $enddate = $request['end'];
        $startdate = $request['start'];
        $search_text = $request['search_text'];
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate = date('Y-m-d', strtotime($enddate));

        $type = 0;
        switch ($request['id']) {
            case 1: //in the case regional challenge
                $challenge = $this->getRegionalChallengeByDateSearch($employee, $startdate, $enddate, $search_text);
                $type = 1;
                break;
            case 2: //in the case duel challenge
                $challenge = $this->getDuelByDateSearch($employee, $startdate, $enddate, $search_text);
                $type = 2;
                break;
            case 3: //in the case company challenge
                $challenge = $this->getCompanyChallengeByDateSearch($employee, $startdate, $enddate, $search_text);
                $type = 3;
                break;
            default:

                break;
        }

        $html = '';

        foreach ($challenge as $item) {
            $category = Categories::where('id', $item['category_id'])->first();
            if (!empty($category)) {
                $category_name = $category->category_name;
            } else {
                $category_name = '';
            }

            $src = $item['image'] != '' ? Storage::disk('s3')->url('images/build') . '/' . $item['image'] : Storage::disk('s3')->url('images/no_image.png');

            $html .= '<div class="slider-item"><span id="' . $item['id'] . '-' . $type . '" class = "slider-image"><a href=""  data-srcset="' . $src . '"  data-sizes="" class="lazy-load replace"><img  src="' . $src . '" alt="' . $item['build_text'] . '"  class="preview" name="' . $category_name . '"></a></span> </div>';

        }

        if (empty($html)) {
            return response()->json(['status' => false, 'message' => 'No Submission']);
        }

        return response()->json(['status' => true, 'html' => $html]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendSMSText(Request $request){
        try {

            $text = $request['smsText'];
            $name = $request['name'];
            $firstName = $request['firstName'];
            $lastName = $request['lastName'];
            $phone = $request['phone'];

            // $smsText = "Hi \n".$name." has scheduled.\n"."Phone number: ".$phone."\n\n".$text;
            $smsText = "Hi \n".$firstName.$lastName."\n".$phone."\n".$text;
            $employee_id = $request['employee_id'];
            $this->sendSMS(Employee::where('id', $employee_id)->first()->phone_number, $text);
            return response()->json(['status' => true]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function sendViewerNameForWatchedEmployeePortfolioIndependent(Request $request){
        try {
            $name = $request['first_name']." ".$request['last_name'];
            $employee_id = $request['employee_id'];
            $employeeProtfolioViewer = new EmployeePortfolioViewer();
            $employeeProtfolioViewer->employee_id = $employee_id;
            $employeeProtfolioViewer->view_name = $name;
            $employeeProtfolioViewer->save();
            $api = new ApiController;
            $api->sendpush($employee_id,"Your Resume has been Viewed ", 'Your resume has been viewed by '.$name , null, 'portfolioViewed');
            return response()->json(['status' => true]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function sendContactText(Request $request){
        try {
            //asif
            $name = $request['first_name']." ".$request['last_name'];
            $text_contact = $request['text_contact'];
            $phone = $request['phone'];
            $contact_information = $request['contactInformation'];
            $employee_id = $request['employee_id'];
            $employee = Employee::where('id', $employee_id)->first();
            $api = new ApiController;
            if ($this->isEmail($contact_information)){
                Mail::to($contact_information)->send( new \App\Mail\Contact($name, $phone, $text_contact, $employee->full_name));
            }
            else if ($this->isPhoneNumber($contact_information)){
                // $text = "Hi ".$employee->full_name."\n".$name.' has requested a reference check on '.$employee->full_name." with the below message: \n\n".$text_contact."\n".'Thank you for using Uptime Profile!';
                //$text = $text_contact."\n"."Contact ".$name." at ".$phone;
                $text = "Hi \n".$request['first_name'].$request['last_name']."\n".$phone."\n".$contact_information;
                $this->sendSMS($contact_information, $text);
            }
            else {
                return response()->json(['status' => false, 'message' => 'contact information is not valid']);
            }
            return response()->json(['status' => true]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }

    }

    protected function isEmail($email) {
        if(preg_match("/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/", $email) || !preg_match("/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) {
             return false;
        } else {
             return true;
        }
    }
    protected function isPhoneNumber($phone){
        if(preg_match_all('/^\+?([0-9-]|\s|\([0-9]+\)){4,20}[0-9]/',$phone)) {
            return true;
        } else {
            return false;
        }
    }
}
<?php

namespace App\Http\Controllers\rep;

use App\Validations;
use Illuminate\Http\Request;
use App\Employee;
use App\Builds;
use App\Tenure;
use DB;
use Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\API\ApiController;

class ValidationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $validations = Validations::latest()->paginate(5);
        if(Auth::guard('admin')->user()->access_level == 5){
            $validations = Validations::latest()->paginate(5);
        } else{
            $validations = DB::table('validations')
                ->leftJoin('builds', 'builds.id', '=', 'validations.build_id')
                ->leftJoin('employee', 'employee.id', '=', 'validations.employee_id')
                ->select('validations.*')
                ->where('builds.company_id', Auth::user()->id)
                ->where('employee.company_id', Auth::user()->id)
                ->get();
        }
        $validations->from_where = '0';
        $validations->employee_id = 0;
        return view('rep.validations.index',compact('validations'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        
    }

    public function validationsdatatable()
    {
        $validations = Validations::all();
        if(Auth::guard('admin')->user()->access_level == 5){
            $validations = Validations::all();
        } else{
            $validations = DB::table('validations')
                ->leftJoin('builds', 'builds.id', '=', 'validations.build_id')
                ->leftJoin('employee', 'employee.id', '=', 'validations.employee_id')
                ->select('validations.*')
                ->where('builds.company_id', Auth::user()->id)
                ->where('employee.company_id', Auth::user()->id)
                ->get();
        }
        if(!empty($validations)){
            foreach ($validations as $item) {
                $item->build_name = '--';
                $item->employee_name = '--';
                $item->status_result = '<p class="text-danger">Reject</p>';
                $item->win_result = '--';
                if($item->status == '1'){
                    $item->status_result = '<p class="text-success">Accept</p>';
                }
                if($item->win == '-1'){
                    $item->win_result = "<label class='badge badge-warning'>In progress</label>";
                } else if($item->win == '0'){
                    $item->win_result = "<label class='badge badge-danger'>Loss</label>";
                } else if($item->win == '1'){
                    $item->win_result = "<label class='badge badge-info'>Win</label>";
                }
                $build = Builds::select('build_text')->where('id', $item->build_id)->first();
                if($build && !empty($build) && $build->build_text != ''){
                    $item->build_name = $build->build_text;
                }
                $employee = Employee::select('full_name','is_deleted')->where('id', $item->employee_id)->first();
                if($employee && !empty($employee) && $employee->full_name != ''){
                    if($employee->is_deleted == '1'){
                        $item->employee_name = 'Employee deleted';
                    } else {
                        $item->employee_name = $employee->full_name;
                    }
                }
            }
        }
        return Datatables::of($validations)->rawColumns(['win_result','status_result'])->make(true);
    }

    //employee validation list
    public function employeeValidations($id)
    {
        if(Auth::guard('admin')->user()->access_level == 5){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        } else{
            $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::guard('admin')->user()->company_id)->count();
        }
        if($employee == 0){
            return redirect()->route('rep.validations.list')->with('errors','Employee not found.');
        }
        $validations = Validations::where('employee_id', $id)->latest()->paginate(5);
        $validations->from_where = '1';
        $validations->employee_id = $id;
        return view('rep.validations.index',compact('validations'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    //employee validation list - Datatabel call
    public function employeeValidationsData($id)
    {
        $employee = Employee::select('full_name')->where('id', $id)->first();
        $validations = Validations::where('employee_id', $id)->get();
        if(!empty($validations)){
            foreach ($validations as $item) {
                $item->build_name = '--';
                $item->employee_name = '--';
                $item->status_result = '<p class="text-danger">Reject</p>';
                $item->win_result = '--';
                if($item->status == '1'){
                    $item->status_result = '<p class="text-success">Accept</p>';
                }
                if($item->win == '-1'){
                    $item->win_result = "<label class='badge badge-warning'>In progress</label>";
                } else if($item->win == '0'){
                    $item->win_result = "<label class='badge badge-danger'>Loss</label>";
                } else if($item->win == '1'){
                    $item->win_result = "<label class='badge badge-info'>Win</label>";
                }
                $build = Builds::select('build_text')->where('id', $item->build_id)->first();
                if($build && !empty($build) && $build->build_text != ''){
                    $item->build_name = $build->build_text;
                }
                if($employee && !empty($employee) && $employee->full_name != ''){
                    $item->employee_name = $employee->full_name;
                }
            }
        }
        return Datatables::of($validations)->rawColumns(['win_result','status_result'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $validations_data = array();
        if(Auth::guard('admin')->user()->access_level == 5){
            $validations_data['builds'] = Builds::select('id','build_text')->where('status','-1')->get()->toArray();
        } else{
            $validations_data['builds'] = Builds::select('id','build_text')->where('company_id',Auth::user()->id)->where('status','-1')->get()->toArray();
        }
        return view('rep.validations.create')->with('validations_data', $validations_data);
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
            'build' => 'required',
            'status' => 'required',
            'employee' => 'required'
        ]);

        if(Auth::guard('admin')->user()->access_level == 5){
            $getBuild = Builds::find($request->build);
            $employee = Employee::select('id', 'company_id')->where('id', $request->employee)->where('is_deleted','0')->first();
        } else{
            $getBuild = Builds::where('id',$request->build)->where('company_id',Auth::guard('admin')->user()->company_id)->first();
            $employee = Employee::select('id', 'company_id')->where('company_id',Auth::guard('admin')->user()->company_id)->where('id', $request->employee)->where('is_deleted','0')->first();
        }
        if($getBuild != ''){
            $getBuild = $getBuild->toArray();
        }else{
            return redirect()->route('rep.validation.list')->with('error','The given build Not Found.');exit;
        }

        if($employee){
            $employee = $employee->toArray();
        } else {
            return redirect()->route('rep.validation.list')->with('error','The given employee is not found.');
        }

        if($getBuild['employee_id'] != $employee['id']){
            if($getBuild['status'] == '-1'){
                $getVailidation = Validations::where('employee_id',$employee['id'])->where('build_id',$request->build)->get()->toArray();
                if(!empty($getVailidation)){
                    return redirect()->route('rep.validation.list')->with('error','This employee have already voted.');
                }
                $data = array(
                    'employee_id'=>$employee['id'],
                    'status'=>$request->status,
                    'build_id'=>$request->build,
                    'win'=>'-1',
                );
                $Validations = Validations::create($data);

                $api = new ApiController;

                $api->setValidationAndSet($request->build,$employee['company_id'],$employee['id']);
                $todayDate = date('Y-m-d');
                $getTenure = Tenure::where('employee_id',$employee['id'])->whereDate('created_at',$todayDate)->count();
                if($getTenure == 0){
                    $getTotalValidation = Validations::where('employee_id',$employee['id'])->whereDate('created_at',$todayDate)->count();
                    if($getTotalValidation == 5){
                        $TenureData = array(
                            'employee_id'=>$employee['id'],                                
                            'point'=>1,
                        );
                        $Tenures = Tenure::create($TenureData);
                    }
                }

                return redirect()->route('rep.validation.list')->with('success','Validations created successfully.');
            }else{
                return redirect()->route('rep.validation.list')->with('error','The Build is over.');
            }
        }else{
            return redirect()->route('rep.validation.list')->with('error','You are not allowed to do vote for employee\'s own build.');
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Validations  $Validations
     * @return \Illuminate\Http\Response
     */
    public function show(Validations $validations)
    {
        return redirect()->route('validations.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Validations  $validations
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route('validations.index');
        // $validations = Validations::find($id);
        // if($validations == ''){
        //     return redirect()->route('validations.index')->with('errors','No validations Found');
        // }
        // if($validations->win != '-1'){
        //     return view('validations.edit');
        // }
        // $validations_data = array();
        // $validations_data['builds'] = $builds = Builds::select('id','build_text','status')->get()->toArray();
        // $emp_data = array();
        // if(!empty($builds)){
        //     foreach ($builds as $item) {
        //         if($item['id'] == $validations->build_id){
        //             $emp_list = $this->getEmployeeFunction($item['id']);
        //             if($emp_list['status']){
        //                 $emp_data[] = $emp_list['data'][0];
        //             }
        //         }
        //     }
        // }
        // $validations_data['employee'] = $emp_data;
        // return view('validations.edit',compact('validations'))->with('validations_data', $validations_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Validations  $validations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('validations.index');
        // request()->validate([
        //     'build_id' => 'required',
        //     'status' => 'required',
        //     'win' => 'required',
        //     'employee_id' => 'required'
        // ]);
        // Validations::find($id)->update($request->all());

        // return redirect()->route('validations.index')->with('success','Validations updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Validations  $validations
     * @return \Illuminate\Http\Response
     */
    public function destroy(Validations $validations)
    {
        $validations = Validations::find($id);
        if($validations == ''){
            return redirect()->route('validations.index')->with('errors','No validations Found.');
        }
        Validations::find($id)->delete();
        return redirect()->route('validations.index')->with('success','Validations deleted successfully.');
    }

    public function validationdelete()
    {
        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                $validations = Validations::find($id);
                if($validations == ''){
                    continue;
                }
                Validations::find($id)->delete();
            }
        }
        echo json_encode(array('status'=>true));die;
       
    }

    public function delete($id)
    {
        $validations = Validations::find($id);
        if($validations == ''){
            return response()->json(['status'=>false,'message'=>'No validations Found.']);
        }
        Validations::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Validations deleted successfully.']);
    }

    //Get Employee from build id
    public function getEmployee($id)
    {
        $data = $this->getEmployeeFunction($id);
        return response()->json($data);
    }

    public function getEmployeeFunction($id)
    {
        if($id && $id != 0){
            if(Auth::guard('admin')->user()->access_level == 5){
                $result = DB::table('builds')
                    ->leftJoin('employee', 'builds.employee_id', '=', 'employee.id')
                    ->select('builds.*')
                    ->where('builds.id', $id)
                    ->where('builds.status', '-1')
                    ->get()
                    ->toArray();
            } else{
                $result = DB::table('builds')
                    ->leftJoin('employee', 'builds.employee_id', '=', 'employee.id')
                    ->select('builds.*')
                    ->where('builds.id', $id)
                    ->where('builds.company_id',Auth::user()->id)
                    ->where('builds.status', '-1')
                    ->get()
                    ->toArray();
            }
            if($result && $result != '' && $result[0]->company_id != ''){

                $employee = Employee::select('id','full_name')->where('company_id', $result[0]->company_id)->where('is_deleted','0')->where('id','!=',$result[0]->employee_id)->get()->toArray();
               
                if($employee && $employee != ''){
                    $employee_data = array();
                    foreach ($employee as $item) {
                        $count = Validations::where('build_id', $id)->where('employee_id', $item['id'])->count();
                        if($count == 0){
                            $employee_data[] = $item;
                        }
                    }
                    return array('status'=>true,'data'=>$employee_data);
                } else {
                    return array('status'=>false,'msg'=>'No data found.');
                }
            } else {
                return array('status'=>false,'msg'=>'No data found.');
            }
        } else {
            return array('status'=>false,'msg'=>'Parameter missing.');
        }
    }
}

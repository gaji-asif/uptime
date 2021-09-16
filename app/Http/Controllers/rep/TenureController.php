<?php

namespace App\Http\Controllers\rep;

use App\Tenure;
use App\Employee;
use Illuminate\Http\Request;
use Auth;
use Yajra\Datatables\Datatables;
use DB;

class TenureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if(Auth::guard('admin')->user()->access_level == 5 ){
            $tenure = Tenure::latest()->paginate(5);
        } else {
            $tenure = DB::table('tenure')
            ->leftJoin('employee', 'employee.id', '=', 'tenure.employee_id')
            ->select('tenure.*','employee.full_name','employee.is_deleted')
            ->where('employee.company_id', Auth::guard('admin')->user()->company_id)
            ->get();
        }
        $tenure->from_where = '0';
        $tenure->employee_id = 0;
        return view('rep.tenure.index',compact('tenure'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
       
    }

    public function tenuredatatable()
    {
        
        if(Auth::guard('admin')->user()->access_level == 5 ){
            $tenure = DB::table('tenure')
            ->leftJoin('employee', 'employee.id', '=', 'tenure.employee_id')
            ->select('tenure.*','employee.full_name','employee.is_deleted')
            ->get();
        } else {
            $tenure = DB::table('tenure')
            ->leftJoin('employee', 'employee.id', '=', 'tenure.employee_id')
            ->select('tenure.*','employee.full_name','employee.is_deleted')
            ->where('employee.company_id', Auth::gurad('admin')->user()->id)
            ->get();
        }
        if(!empty($tenure)){
            foreach ($tenure as $item) {
                if($item->is_deleted == '1'){
                    $item->full_name = '--';
                }
            }
        }
        return Datatables::of($tenure)->make(true);
    }

    //employee validation list
    public function employeeTenure($id)
    {
        if(Auth::guard('admin')->user()->access_level == 5 ){
            $employee = Employee::where('id',$id)->where('is_deleted','0')->count();
        } else{
            $employee = Employee::where('id',$id)->where('is_deleted','0')->where('company_id',Auth::guard('admin')->user()->company_id)->count();
        }
        if($employee == 0){
            return redirect()->route('rep.tenure.list')->with('errors','Employee not found.');
        }
        $tenure = Tenure::where('employee_id', $id)->latest()->paginate(5);
        $tenure->from_where = '1';
        $tenure->employee_id = $id;
        return view('rep.tenure.index',compact('tenure'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    //employee validation list - Datatabel call
    public function employeeTenureData($id)
    {
        if(Auth::guard('admin')->user()->access_level == 5 ){
            $tenure = DB::table('tenure')
            ->leftJoin('employee', 'employee.id', '=', 'tenure.employee_id')
            ->select('tenure.*','employee.full_name','employee.is_deleted')
            ->where('tenure.employee_id', $id)
            ->get();
        } else {
            $tenure = DB::table('tenure')
            ->leftJoin('employee', 'employee.id', '=', 'tenure.employee_id')
            ->select('tenure.*','employee.full_name','employee.is_deleted')
            ->where('employee.company_id', Auth::guard('admin')->user()->company_id)
            ->where('tenure.employee_id', $id)
            ->get();
        }
        if(!empty($tenure)){
            foreach ($tenure as $item) {
                if($item->is_deleted == '1'){
                    $item->full_name = '--';
                }
            }
        }
        return Datatables::of($tenure)->rawColumns(['status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tenure_data = array();        
        $user = Auth::user();
        if(Auth::guard('admin')->user()->access_level == 5 ){
            $tenure_data['employee'] = Employee::select('id','full_name')->where('is_deleted','0')->get()->toArray();
        } else {
            $tenure_data['employee'] = Employee::select('id','full_name')->where('company_id', $user->id)->where('is_deleted','0')->get()->toArray();
        }
        return view('tenure.create')->with('tenure_data', $tenure_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return view('tenure.index');
        // $user = Auth::user();
        
        // request()->validate([
        //     'employee_id' => 'required'
        // ]);
        // $user = Auth::user();
        // if($user->role == 'admin'){
        //     $employee = Employee::select('id','full_name')->where('id',$id)->where('is_deleted','0')->count();
        // } else {
        //     $employee = Employee::select('id','full_name')->where('id',$id)->where('company_id', $user->id)->where('is_deleted','0')->count();
        // }
        // if($employee == 0){
        //     return redirect()->route('tenure.create')->with('error','Employee not found.');
        // }
        // $data = array();
        // $data['employee_id'] = $request->employee_id;
        // $data['point'] = 1;

        // Tenure::create($data);
        // return redirect()->route('tenure.index')->with('success','Tenure created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tenure  $tenure
     * @return \Illuminate\Http\Response
     */
    public function show(Tenure $tenure)
    {
        return view('rep.tenure.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tenure  $tenure
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('rep.tenure.index');
        // if(Auth::user()->role == 'admin'){
        //     $tenure = Tenure::find($id);
        // } else {
        //     $tenure = Tenure::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        // }
        // if(!$tenure){
        //     return redirect()->route('tenure.index')->with('errors','No tenure Found.');
        // }
        // return view('tenure.edit',compact('tenure'))->with('tenure_data', $tenure_data);
        // return view('tenure.edit',compact('tenure'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tenure  $tenure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return view('rep.tenure.index');
        // if(Auth::user()->role == 'admin'){
        //     $tenure = Tenure::find($id);
        // } else {
        //     $request['company_id'] = Auth::user()->id;
        //     $tenure = Tenure::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        // }
        // if(!$tenure){
        //     return redirect()->route('tenure.index')->with('errors','No tenure Found.');
        // }
        // request()->validate([
        //     'category_name' => 'required',
        //     'company_id' => 'required'
        // ]);
        // Tenure::find($id)->update($request->all());

        // return redirect()->route('tenure.index')->with('success','Tenure updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tenure  $tenure
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return view('rep.tenure.index');
        // if(Auth::user()->role == 'admin'){
        //     $tenure = Tenure::find($id);
        // } else {
        //     $tenure = Tenure::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        // }
        // if($tenure == ''){
        //     return redirect()->route('tenure.index')->with('error','No tenure Found.');
        // }
        // tenure::find($id)->delete();
        // return redirect()->route('tenure.index')->with('success','Tenure deleted successfully.');
    }

    public function tenureDelete(){
        if(!empty($_POST['ids'])){
            foreach($_POST['ids'] as $id){
                $Tenure = Tenure::find($id);
                if($Tenure == ''){
                    continue;
                }
                Tenure::find($id)->delete();
            }
            echo json_encode(array('status'=>true));die;
        }
    }
}

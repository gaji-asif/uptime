<?php

namespace App\Http\Controllers\executive;

use App\Employee;
use App\Employee_requests;
use App\Users;
use App\Builds;
use App\Validations;
use App\Tenure;
use App\Accesslevel;
use App\Subcategory;
use App\Http\Controllers\API\ApiController;
use Session;
use DB;
use File;
use App\Categories;
use App\Industry;

use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Challenge;
use Illuminate\Http\Request;
use App\Authtoken;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
//use Illuminate\Auth\Authenticatable;

class EmployeeIndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {   

        return view('executive.empindustry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $data['categories']     = Categories::all()->toArray();
        $data['access_level']   = Accesslevel::where('id','<=',3)->get()->toArray();

//Accesslevel::all()->toArray();
        return view('executive.empindustry.create')->with('data', $data); ; 

    }

    public function store(Request $request)
    {
         request()->validate([
              'industry_name'=>'required',
          ]);


         
          $data = array(
            'industry_name'=>$request->industry_name,
            'location' =>  isset($request->location) ? $request->region : '',
            'latitude'=>isset($request->location) ? $request->latitude:0,
            'longitude'=>isset($request->location) ? $request->longitude:0,
            'company_id'    =>   Session::get('employee')->company_id
         );
          
         Industry::create($data); 

        return redirect()->route('executive.industry.list')->with('success','Region created successfully.');
       
    }

     public function industrydatatable()
    {
  
         $Industry = Industry::where('company_id',Session('employee')->company_id)->orderBy('created_at','desc')->get();
         $emp_count = 0 ;
         
         $resultitem= array();
         $result = array();
         foreach($Industry as $item){
            $resultitem = $item;
            $emp_count = Employee::where('industry',$item->id)->where('company_id',Session::get('employee')->company_id)->where('is_deleted','0')->get()->count();
            $resultitem['employee_count'] = $emp_count;
            $result[]= $resultitem;
         }
         
         return Datatables::of($result)->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $industry = Industry::where('id',$id)->where('id', $id)->first();
       
        return view('executive.empindustry.edit',compact('industry'));
    }

     public function update(Request $request, $id)
    {

        request()->validate([
            'industry_name' => 'required',
           
        ]);
        
        
          $data = array(
            'industry_name'=>$request->industry_name,
            'location' =>  isset($request->location) ? $request->region : '',
            'latitude'=>isset($request->location) ? $request->latitude:0,
            'longitude'=>isset($request->location) ? $request->longitude:0,
            'company_id'    =>   Session::get('employee')->company_id
         );
          

        Industry::find($id)->update($data);

        return redirect()->route('executive.industry.list')->with('success','Industry updated successfully.');
    }

    public function delete($id)
    {
        
        Industry::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Industry deleted successfully.']);
        
        
    }

}

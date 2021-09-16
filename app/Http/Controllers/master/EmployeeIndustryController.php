<?php

namespace App\Http\Controllers\master;

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

        return view('admin.empindustry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $data['categories']     = Categories::all()->toArray();
        $data['access_level']   = Accesslevel::all()->toArray();
        return view('admin.empindustry.create')->with('data', $data); ; 

    }

    public function store(Request $request)
    {
        /*
        request()->validate([
            'industry_name' => 'required',
          //  'category_id' => 'required',
          //  'access_level' => 'required'

        ]);

            $Industry = new Industry;
            $Industry->industry_name = $request->industry_name;
            $Industry->category_id   = 0;
            $Industry->access_level  = 0;
            $Industry->company_id =  $id = User::find(Auth::id());
            $Industry->save();    
        return redirect('employee/industry')->with('success','Industry created successfully.');
        */
          request()->validate([
             'industry_name' => 'required'
          ]);
        //  echo $request->industry_name;
       // echo  Session('employee')->company_id;
         $data = array(
            'industry_name' =>  $request->industry_name,
            'company_id'    =>   Session::get('employee')->company_id
         );

      
         Industry::create($data); 

        return redirect()->route('admin.industry.list')->with('success','Region created successfully.');
    }

     public function industrydatatable()
    {
  
         $Industry = Industry::where('company_id',Session('employee')->company_id);
         return Datatables::of($Industry)->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data['categories']     = Categories::all()->toArray();
        $data['access_levels']   = Accesslevel::all()->toArray();

        $data['industry'] = Industry::where('id',$id)->where('id', $id)->first();
        // echo "<pre>"; print_r($Industry); die;
        return view('admin.empindustry.edit',compact('Industry'))->with($data);
    }

     public function update(Request $request, $id)
    {

        request()->validate([
            'industry_name' => 'required',
            'category_id' => 'required',
            'access_level' => 'required'

        ]);
        
        $data = Array (
            'industry_name' =>  $request->industry_name,
            'category_id' =>    $request->category_id,
            'access_level' =>   $request->access_level
        );

        Industry::find($id)->update($data);
        
        
        return redirect()->route('admin.industry.list')->with('success','Industry updated successfully.');
    }

    public function delete($id)
    {
        
        Industry::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Industry deleted successfully.']);
        
        
    }

}

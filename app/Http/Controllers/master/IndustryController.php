<?php

namespace App\Http\Controllers\master;

use App\Industry;
use App\Categories;
use App\Accesslevel;
use Illuminate\Http\Request;
use App\Users;
use Auth;
use App\Employee;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\API\ApiController;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    
        return view('master.industry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $data['company']  = Users::where('role','company')->get()->toArray();
        return view('master.industry.create')->with('data', $data); ; 

    }

    public function store(Request $request)
    {
            
       request()->validate([
        'industry_name' => 'required',
        'company' => 'required',           
       
      ]);

    
      $data = array(
        'industry_name'=>$request->industry_name,
        'location' =>  isset($request->location) ? $request->region : '',
        'latitude'=>isset($request->location) ? $request->latitude:0,
        'longitude'=>isset($request->location) ? $request->longitude:0,
        'company_id'=> $request->company,
     );
     Industry::create($data);
       return redirect()->route('master.industry.list')->with('success','Industry created successfully.');
    }


    public function getcompany(){

        $data = Users::where('role','company')->get();
        if(!empty($data)){
                    $html = '<strong><label for="company">Select Company *</label></strong><select class="form-control" name="company" id="company" required>
                    <option value="">select company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                   
                    return response()->json(['status'=>true,'html'=>$html]);
            }

   }

     public function industrydatatable()
    {
         $Industry = Industry::orderBy('created_at','desc')->get();              
         $emp_count = 0 ;
         
         $resultitem= array();
         $result = array();
         foreach($Industry as $item){
            $resultitem = $item;
            $emp_count = Employee::where('industry',$item->id)->where('is_deleted','0')->get()->count();
            $resultitem['employee_count'] = $emp_count;
            $result[]= $resultitem;
         }        
         return Datatables::of($result)->make(true);
       //return Datatables::of($Industry)->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {  
        $company   = Users::where('role','company')->get()->toArray();
        $industry = Industry::where('id',$id)->where('id', $id)->first();
        return view('master.industry.edit',compact('industry','company'));
    }

     public function update(Request $request, $id)
    {

        request()->validate([
            'industry_name' => 'required',
            'company_id' => 'required',
        ]);
        
        
        $data = array(
          'industry_name'=>$request->industry_name,
          'location' =>  isset($request->location) ? $request->region : '',
          'latitude'=>isset($request->location) ? $request->latitude:0,
          'longitude'=>isset($request->location) ? $request->longitude:0,
          'company_id'    => $request->company_id
       );
        Industry::find($id)->update($data);

        return redirect()->route('master.industry.list')->with('success','Industry updated successfully.');
    }

    public function delete($id)
    {
        
        Industry::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Industry deleted successfully.']);
        
        
    }

}

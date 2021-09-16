<?php

namespace App\Http\Controllers\rep;

use App\Industry;
use App\Categories;
use App\Accesslevel;
use Illuminate\Http\Request;
use App\Users;
use Auth;
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
            
       $Industry = Industry::latest()->paginate(5);

        return view('industry.index',compact('Industry'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $data['company']     = Users::all()->toArray();
        
        //echo "<pre>"; print_r($categories); die;
        return view('industry.create')->with('data', $data); ; 

    }

    public function store(Request $request)
    {
        request()->validate([
            'industry_name' => 'required',
            'company' => 'required'           

        ]);

            $Industry = new Industry;
            
            $Industry->industry_name = $request->industry_name;
            $Industry->company_id  = $request->company;
            $Industry->save();    
        
            return redirect('industry')->with('success','Industry created successfully.');
    }


public function getcompany(){

        $data = Users::all();
        if(!empty($data)){
                    $html = '<strong><label for="company">Select Company *</label></strong><select class="form-control" name="company" id="company" required><option value="">select company</option>';
                    foreach ($data as $item) {
                        $html .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                    }
                    $html .= '</select>';
                   
                    return response()->json(['status'=>true,'html'=>$html]);
            }

}
     public function industrydatatable()
    {
        $Industry = Industry::all();
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
        $data['access_levels']   = Accesslevel::where('id','<=',1)->get()->toArray();
//Accesslevel::all()->toArray();

        $data['industry'] = Industry::where('id',$id)->where('id', $id)->first();
        // echo "<pre>"; print_r($Industry); die;
        return view('industry.edit',compact('Industry'))->with($data);
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
        
        
        return redirect('industry')->with('success','Industry updated successfully.');
    }

    public function delete($id)
    {
        
        Industry::find($id)->delete();
        return response()->json(['status'=>true,'message'=>'Industry deleted successfully.']);
        
        
    }

}

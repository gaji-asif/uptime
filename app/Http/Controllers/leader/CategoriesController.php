<?php

namespace App\Http\Controllers\leader;

use App\Categories;
use Illuminate\Http\Request;
use App\Users;
use Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\API\ApiController;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::latest()->paginate(5);
        } else {
            $categories = Categories::where('company_id',Auth::user()->id)->orWhere('company_id','0')->latest()->paginate(5);
        }
        if(!empty($categories)){
            foreach ($categories as $item) {
                $user = Users::select('name')->where('id', $item->company_id)->first();
                $item->company_name = '--';
                if($user && !empty($user) && $user->name != ''){
                    $item->company_name = $user->name;
                }
            }
        }
        return view('categories.index',compact('categories'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function categoriesdatatable()
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::all();
        } else {
            $categories = Categories::where('company_id',Auth::user()->id)->orWhere('company_id','0')->get();
        }
        if(!empty($categories)){
            foreach ($categories as $item) {
                $item->company_name = '--';
                if($item->company_id == '0'){
                    $item->company_name = 'All company';
                } else {
                    $user = Users::select('name')->where('id', $item->company_id)->first();
                    if($user && !empty($user) && $user->name != ''){
                        $item->company_name = $user->name;
                    }
                }
            }
        }
        return Datatables::of($categories)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $categories_data = array('is_admin' => 0);
        if($user->role == 'admin'){
            $categories_data['is_admin'] = 1;
            $categories_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        }
        return view('categories.create')->with('categories_data', $categories_data); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if($user->role == 'company'){
            $request['company'] = $user->id;
        }
        request()->validate([
            'category_name' => 'required',
            'company' => 'required'
        ]);
        if($user->role == 'admin' && $request['company'] != 0){
            $check_comapany = Users::where('id', $request['company'])->count();
            if($check_comapany == 0){
                return redirect()->route('categories.create')->with('error','Company Not found.');
            }
        }
        $data = Array (
            'category_name' => $request['category_name'],
            'company_id' => $request['company']
        );
        $category = Categories::create($data);
        $api = new ApiController;
        $api->categoryMaintain($category->id, 'created');
        return redirect()->route('categories.index')->with('success','Categories created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::find($id);
        } else {
            $categories = Categories::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        }
        if(!$categories){
            return redirect()->route('categories.index')->with('errors','No categories Found.');
        }
        $user = Auth::user();
        $categories_data = array('is_admin' => 0);
        if($user->role == 'admin'){
            $categories_data['is_admin'] = 1;
            $categories_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        }
        return view('categories.edit',compact('categories'))->with('categories_data', $categories_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::find($id);
        } else {
            $request['company'] = Auth::user()->id;
            $categories = Categories::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        }
        if(!$categories){
            return redirect()->route('categories.index')->with('errors','No categories Found.');
        }
        request()->validate([
            'category_name' => 'required',
            'company' => 'required'
        ]);
        if(Auth::user()->role == 'admin' && $request['company'] != 0){
            $check_comapany = Users::where('id', $request['company'])->count();
            if($check_comapany == 0){
                return redirect()->route('categories.edit',['id'=>$id])->with('error','Company Not found.');
            }
        }
        $data = Array (
            'category_name' => $request['category_name'],
            'company_id' => $request['company']
        );
        Categories::find($id)->update($data);
        $api = new ApiController;
        $api->categoryMaintain($id, 'updated');
        return redirect()->route('categories.index')->with('success','Categories updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::find($id);
        } else {
            $categories = Categories::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        }
        if($categories == ''){
            return redirect()->route('categories.index')->with('error','No categories Found.');
        }
        Categories::find($id)->delete();
        $api = new ApiController;
        $api->categoryMaintain($id, 'deleted');
        return redirect()->route('categories.index')->with('success','Categories deleted successfully.');
    }

    public function delete($id)
    {
        if(Auth::user()->role == 'admin'){
            $categories = Categories::find($id);
        } else {
            $categories = Categories::where('id',$id)->whereIn('company_id',array(Auth::user()->id, '0'))->first();
        }
        if($categories == ''){
            return response()->json(['status'=>false,'message'=>'No categories Found.']);
        }
        Categories::find($id)->delete();
        $api = new ApiController;
        $api->categoryMaintain($id, 'deleted');
        return response()->json(['status'=>true,'message'=>'Categories deleted successfully.']);
    }
}

<?php

namespace App\Http\Controllers\master;
use Session;
use App\Categories;
use Illuminate\Http\Request;
use App\Users;
use Auth;
use App\Notification;
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

        return view('master.categories.index');
    }

    public function categoriesdatatable()
    {

         $categories = Categories::query()
             ->withTrashed()
             ->latest()
             ->get();

         $result = array();
         $result = array();

        if(!empty($categories))
        {
            $categories = $categories->toArray();

            foreach ($categories as $item) {

              $resultitem = $item;
              $resultitem['created_at'] = date('Y-m-d a h:i:s',strtotime($item['created_at']));
              $resultitem['company_name'] = '--';
                if($item['company_id'] == '0'){
                    $resultitem['company_name'] = 'All company';
                }
                else{
                    $user = Users::select('name')->where('id', $item['company_id'])->first();
                    if($user && !empty($user) && $user->name != ''){

                        $resultitem['company_name'] = $user->name;
                    }
                }

               $result[] = $resultitem;

            }
        }
        return Datatables::of($result)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories_data['users'] = Users::select('id','name')->where('role', 'company')->get();
        return view('master.categories.create')->with('categories_data', $categories_data);
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
            'category_name' => 'required',
            'company' => 'required'
        ]);

       $check_comapany = Users::where('id', $request['company'])->count();
       if($check_comapany == 0){
           return redirect('master/categories')->with('error','Company Not found.');
       }

        $data = Array (
            'category_name' => $request['category_name'],
            'company_id' => $request['company']
        );
        $category = Categories::create($data);
        //$api = new ApiController;
        //$api->categoryMaintain($category->id, 'created');
        $company  = Users::where('id',$category->company_id)->first();
        $message = $company->name.' has created new Category '.$category->category_name;
        $list_param = array(
                  'content_type'=>5,
                  'message' => $message,
                  'sender'=>Session::get('employee')->id,
                  'receiver'=>$request['company'],
                  'receiver_type'=>2
              );

	    Notification::create($list_param);
        return redirect('master/categories')->with('success','Categories created successfully.');
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

        $categories = Categories::find($id);

        if(!$categories){
            return redirect()->route('categories.index')->with('errors','No categories Found.');
        }
       $categories_data['users'] = Users::select('id','name')->where('role', 'company')->get()->toArray();
        return view('master.categories.edit',compact('categories'))->with('categories_data', $categories_data);
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

        $categories = Categories::find($id);

        if(!$categories){
            return redirect()->route('master.categories.list')->with('errors','No categories Found.');
        }
        request()->validate([
            'category_name' => 'required',
            'company' => 'required'
        ]);

        $check_comapany = Users::where('id', $request['company'])->count();
        if($check_comapany == 0){
             return redirect()->route('master.categories.list',['id'=>$id])->with('error','Company Not found.');
        }

        $data = Array (
            'category_name' => $request['category_name'],
            'company_id' => $request['company']
        );
        Categories::find($id)->update($data);
        //$api = new ApiController;
        //$api->categoryMaintain($id, 'updated');
        return redirect()->route('master.categories.list')->with('success','Categories updated successfully.');
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

         $categories = Categories::find($id);

        if($categories == ''){
            return response()->json(['status'=>false,'message'=>'No categories Found.']);
        }
        Categories::find($id)->delete();
        $api = new ApiController;
        $api->categoryMaintain($id, 'deleted');
        return response()->json(['status'=>true,'message'=>'Categories deleted successfully.']);
    }

    public function deletecategories(){
	if(!empty($_POST['ids'])){

            foreach($_POST['ids'] as $id){

                $employee = Categories::where('id',$id)->count();

                if($employee == 0){
                    continue;
                }
                Categories::find($id)->delete();

            }
            echo json_encode(array('status'=>true));die;
        }
    }

    public function restore($id)
    {
        Categories::withTrashed()
            ->find($id)
            ->restore();

        session()->flash('success', 'Category restored successfully.');

        return redirect()->back();
    }

}

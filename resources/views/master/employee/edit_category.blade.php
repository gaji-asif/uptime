@extends('master/layouts.app')

@section('content')
<div class="content-wrapper">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                <li>{{ $message }}</li>
            </ul>
        </div>
    @endif

    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Edit Category</h4>
            <form action="{{ url('admin/employee/categories/update/'.request()->segment(count(request()->segments()))) }}" method="POST" class="forms-sample">
                @csrf
                <div class="form-group">
                    <strong><label for="sub_category_name">Sub Category Name *</label> </strong>
                    <input type="text" name="sub_category_name" id="sub_category_name" class="form-control" value="<?php if(!empty($categories_data['sub_category'])) { echo $categories_data['sub_category']['subcategory_name']; } ?>" placeholder="Sub Category Name" required>
                </div>

                
                <div class="form-group">
                    <strong><label for="user_level">User Level *</label></strong>
                    <select class="form-control" name="user_level" id="user_level" required>
                    <option value="">select level</option>
                    @foreach ($categories_data['access_level'] as $access_level)
                        <option <?php if($categories_data['sub_category']['user_access_level'] == $access_level['id']) { echo "selected";} ?> value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
                    @endforeach
                    </select>
                </div>
               
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>

@endsection
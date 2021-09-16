@extends('leader/layouts.app')

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
            <h4 class="card-title theme-color">Add New Sub Category</h4>
            <form action="{{route('leader.employee.categories.store')}}" method="POST" class="forms-sample">
                @csrf

            
                <div class="form-group">
                    <strong><label for="main_category">Category Name *</label></strong>
                    <select class="form-control" name="main_category" id="main_category" required>
                    <option value="">Select Category</option> 
                    @foreach ($categories_data['Categories'] as $categories)
                        <option value="{{$categories['id']}}">{{$categories['category_name']}}</option>
                    @endforeach
                    </select>
                </div>
            
            

                <div class="form-group">
                    <strong><label for="sub_category_name">Sub Category Name *</label> </strong>
                    <input type="text" name="sub_category_name" id="sub_category_name" maxlength="20"  class="form-control" placeholder="Sub Category Name" required>
                </div>
                
                
                <div class="form-group">
                    <strong><label for="user_level">User Level *</label></strong>
                    <select class="form-control" name="user_level" id="user_level" required>
                    <option value="">select level</option>
                    @foreach ($categories_data['access_level'] as $access_level)
                        <option value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
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
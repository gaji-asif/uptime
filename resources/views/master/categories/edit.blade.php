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
            <form action="{{ url('master/categories/update',$categories->id) }}" method="POST" class="forms-sample">
                @csrf
              <!--   @method('PUT') -->
                <div class="form-group">
                    <strong><label for="category_name">Category Name *</label> </strong>
                    <input type="text" name="category_name" id="category_name" class="form-control" value="{{$categories->category_name}}" placeholder="Category Name" required>
                </div>

            
                <div class="form-group">
                    <strong><label for="company_id">Company Name *</label></strong>
                    <select class="form-control" name="company" id="company_id" required>
                    <option value="0" {{($categories->company_id == '0' ? "selected" : "")}}>For all</option>
                    @foreach ($categories_data['users'] as $user)
                        <option value="{{$user['id']}}" {{($categories->company_id == $user['id'] ? "selected" : "")}}>{{$user['name']}}</option>
                    @endforeach
                    </select>
                </div>
               
                
                <button type="submit" class="btn btn-theme mr-2">Update</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>

@endsection
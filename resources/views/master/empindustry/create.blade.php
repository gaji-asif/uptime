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
            <h4 class="card-title theme-color">Add New Region</h4>
            <form action="{{ route('admin.employee.industrystore') }}" method="POST" class="forms-sample">
                @csrf
                <div class="form-group">
                    <strong><label for="industry_name">Region Name *</label> </strong>
                    <input type="text" name="industry_name" id="industry_name" class="form-control" placeholder="Region Name" required>
                </div>
                
                <!--
                <div class="form-group">
                    <strong><label for="company_id">Category Name *</label></strong>
                    <select class="form-control" name="category_id" id="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($data['categories'] as $category)
                        <option value="{{$category['id']}}">{{$category['category_name']}}</option>
                    @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <strong><label for="company_id">Access Level*</label></strong>
                    <select class="form-control" name="access_level" id="access_level" required>
                    <option value="">Select Access Level</option>
                    @foreach ($data['access_level'] as $access_level)
                        <option value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
                    @endforeach
                    </select>
                </div>
                -->

                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>

@endsection
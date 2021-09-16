@extends('executive/layouts.app')

@section('content')
<div class="content-wrapper">
    
    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Add New Employee</h4>
            <form action="{{ route('executive.employee.employeestore') }}" method="POST" class="forms-sample" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <strong><label for="full_name">Full Name *</label> </strong>
                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <strong><label for="email">Email address *</label></strong>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <strong><label for="password">Password *</label></strong>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <strong><label for="phone_number">Phone Number *</label> </strong>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number" required>
                </div>

                <div class="form-group {{($employee_data['categories'] ? '' : 'hide')}}">
                    <strong><label for="industry">Categories</label></strong>
                    <select class="form-control" name="industry" id="industry">
                        <option value="0">Select Category</option>
                        @foreach ($employee_data['categories'] as $cat)
                            <option value="{{$cat['id']}}">{{$cat['category_name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <strong><label>Employee Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                        <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                        <span class="input-group-append">
                            <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <strong><label>Access Level</label></strong>
                    <select class="form-control" name="access_level" id="access_level">                        
                        <option value="">Select Access Level</option>
                        @foreach ($employee_data['access_level'] as $access_level)
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
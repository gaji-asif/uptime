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
            <h4 class="card-title theme-color">Edit Employee</h4>
            <form action="{{url('leader/employee/'.$employee->id.'/employeeupdate')}}" method="POST" class="forms-sample">
                @csrf
                <div class="form-group">
                    <strong><label for="full_name">Full Name*</label> </strong>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{$employee->full_name}}" placeholder="Full Name" required disabled>
                </div>
                <div class="form-group">
                    <strong><label for="email">Email address *</label></strong>
                    <input type="email" class="form-control" required name="email" id="email" value="{{$employee->email}}" readonly>
                </div>
                <div class="form-group">
                    <strong><label for="phone_number">Phone Number *</label> </strong>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number" value="{{$employee->phone_number}}" required disabled>
                </div>

                  <div class="form-group">
                    <strong><label for="industry">Store</label></strong>
                    <input type="text" name="industry" id="industry" class="form-control" placeholder="Region" value="{{$industry->industry_name}}" required disabled>
                </div>
                <!--
                <div class="form-group">
                    <strong><label>Employee Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>
                @if($employee->image != '')
                <div class="form-group form-group-image">
                <strong><label>Profile Pic</label></strong>
                    <div class="relativeDiv">
                        <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                        <img src="<?php echo \Storage::disk('s3')->url('images/employee/'.$employee->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                    </div>
                </div>
                @endif
                -->
                <div class="form-group">
                    <strong><label>Access Level</label></strong>
                    <select class="form-control" name="access_level" id="access_level">
                        <option value="">Select Access Level</option>
                            @foreach ($employee_data['access_level'] as $access_level)
                            <option value="{{$access_level['id']}}" {{$employee->access_level == $access_level['id'] ? 'selected' : ''}}>{{$access_level['access_level_name']}}</option>
                             @endforeach                        
                    </select>

                </div>
             
                <button type="submit" class="btn btn-theme mr-2" >Update</button>
           
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>
<script type="text/javascript">
    
    $(document).ready(function (){

       $("#update").click(function (){
        alert("update");
       });
    });
</script>
@endsection
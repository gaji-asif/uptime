@extends('executive/layouts.app')

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
            <h4 class="card-title theme-color">Employee Request</h4>
            <form action="" method="POST" class="forms-sample">
                @csrf
                <div class="form-group">
                    <strong><label for="full_name">Full Name*</label> </strong>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{$getdata->full_name}}" placeholder="Full Name" required readonly>
                </div>
                <div class="form-group">
                    <strong><label for="email">Email address *</label></strong>
                    <input type="email" class="form-control" required name="email" id="email" value="{{$getdata->email}}" readonly>
                </div>
                <div class="form-group">
                    <strong><label for="phone_number">Phone Number *</label> </strong>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number" value="{{$getdata->phone_number}}" required readonly>
                </div>

                 <div class="form-group">
                    <strong><label for="industry">Region</label></strong>
                    <input type="text" name="region" id="region" class="form-control" value="{{$getdata->industry_name}}" required readonly>
                </div>
 
                @if($getdata->image != '')
                <div class="form-group form-group-image">
                <strong><label>Profile Pic</label></strong>
                    <div class="relativeDiv">
                        <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                        <img src="<?php echo \Storage::disk('s3')->url('images/employee/'.$getdata->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                    </div>
                </div>
                @endif
                
                <div class="form-group">
                    <strong><label>Access Level</label></strong>
                     <input type="text" name="accesslevel" id="accesslevel" class="form-control" value="{{$getdata->access_name}}" required readonly>
                </div>                        
            </form>
        </div>
    </div>
       
</div>
<script type="text/javascript">
   
</script>
@endsection
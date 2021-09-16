@extends('rep/layouts.app')


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
        <p>{{ $message }}</p>
    </div>
    @endif
    @if ($message = Session::get('success_company'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    
    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">       
            <h4 class="card-title theme-color">Edit Company</h4>
            <form action="{{ url('rep/users/update',$users->id) }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <strong><label for="role">Role *</label></strong>
                    <select class="form-control change-role-company" name="role" id="role" required>
                    <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="company">Company</option>
                    </select>
                </div>
                
                
                <div class="form-group  ">
                    <strong><label for="first_name">First Name *</label> </strong>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="{{$users->first_name}}">
                </div>
                <div class="form-group  ">
                    <strong><label for="last_name">Last Name *</label></strong>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{$users->last_name}}">
                </div>
               

                <div class="form-group">
                    <strong><label for="address">Address *</label></strong>
                    <input type="text" name="address" value="{{$users->address}}"  id="address" class="form-control" placeholder="Address" required>
                </div>

                <div class="form-group">
                    <strong><label for="WebsiteUrl">Website Url</label></strong>
                    <input type="text" name="website_url" value="{{$users->website_url}}" id="WebsiteUrl" class="form-control" placeholder="Website Url">
                </div>
                
                
                <div class="form-group">
                    <strong><label>Profile Pic upload</label></strong>
                    <input type="file" name="pic" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>

                @if($users->pic != '')
                    <div class="form-group form-group-image">
                        <strong><label>Profile Pic</label></strong>
                        <div class="relativeDiv">
                            <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                            <img src="<?php echo \Storage::disk('s3')->url('images/user/'.$users->pic);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        </div>
                    </div>
                @endif
                
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->

</div>

@endsection
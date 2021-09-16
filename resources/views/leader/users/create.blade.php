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
        <p>{{ $message }}</p>
    </div>
    @endif

      <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Add New Company</h4>
            <form action="{{ url('leader/users/store') }}" autocomplete="nope" method="POST" class="forms-sample" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <strong><label for="role">Role *</label></strong>
                    <select class="form-control change-role-company" name="role" id="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="company">Company</option>
                    </select>
                </div>

                <div class="form-group hide">
                    <strong><label for="first_name">First Name *</label> </strong>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name">
                </div>
                <div class="form-group hide">
                    <strong><label for="last_name">Last Name *</label></strong>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
                </div>
                <div class="form-group hide">
                    <strong><label for="company_name">Company Name *</label> </strong>
                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company Name">
                </div>

                <div class="form-group">
                    <strong><label for="email">Email address *</label></strong>
                    <input type="email" name="email" autocomplete="nope" class="form-control" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <strong><label for="password">Password *</label></strong>
                    <input type="password" name="password" autocomplete="new-password"  class="form-control" id="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <strong><label for="address">Address *</label></strong>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Address" required>
                </div>

                <div class="form-group">
                    <strong><label for="WebsiteUrl">Website Url</label></strong>
                    <input type="text" name="website_url" id="WebsiteUrl" class="form-control" placeholder="Website Url">
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
                </div>
                
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div> -->
    <!-- </div> -->

</div>

@endsection
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
            <h4 class="card-title theme-color">Add New Build</h4>
            <form action="{{ url('executive/builds/store') }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <strong><label for="build_text">Build Name *</label> </strong>
                    <input type="text" name="build_text" id="build_text" class="form-control" placeholder="First Name" required>
                </div>

                <div class="form-group">
                    @if($builds_data['employee'])
                    <strong><label for="employee_id">Employee Name *</label></strong>
                    <select class="form-control empoyee-new-build" name="employee" id="employee_id" required>
                    <option value="">Select employee</option>
                    @foreach ($builds_data['employee'] as $builds)
                        <option value="{{$builds['id']}}">{{$builds['full_name']}}</option>
                    @endforeach
                    </select>
                    @else
                    <strong><label>No Employee available.</label></strong>
                    @endif
                </div>

                <div class="form-group">
                    <strong><label for="category_id">Category Name *</label></strong>
                    <select class="form-control build-category-add" name="category" id="category_id" required disabled data-id="0">
                        <option value="">selecte emplyee for category list</option>
                    </select>
                </div>

                <div class="form-group">
                    <strong><label for="challenge_id">Use challenge</label></strong>
                    <select class="form-control" name="challenge" id="challenge_id" disabled>
                        <option value="0">selecte emplyee for challenge list</option>
                    </select>
                </div>                

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Build Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="-1" checked="" required>
                            -1
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status2" value="0" required>
                            0
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status3" value="1" required>
                            1
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <strong><label>Build Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
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
        <!-- </div>
    </div> -->
</div>

@endsection
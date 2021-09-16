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
        <h4 class="card-title theme-color">Edit Challenge</h4>
        @if(isset($challenge))
            <form action="{{ url('employee/challengeupdate/'.$challenge->id) }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf

                @if($challenge_data['is_admin'] == 1)
                <div class="form-group">
                    @if($challenge_data['users'])
                    <strong><label for="company_id">Company Name *</label></strong>
                    <select class="form-control chall-company-select" name="company" id="company_id" required>
                    <option value="">select Company</option>
                    @foreach ($challenge_data['users'] as $user)
                        <option value="{{$user['id']}}" {{($challenge->company_id == $user['id'] ? "selected" : "")}}>{{$user['name']}}</option>
                    @endforeach
                    </select>
                    @else
                    <strong><label>No Company user available</label></strong>
                    @endif
                </div>
                @endif

                <div class="form-group">
                    <strong><label for="challenge_text">Challenge Name *</label> </strong>
                    <input type="text" name="challenge_text" id="challenge_text" class="form-control" value="{{$challenge->challenge_text}}" placeholder="Challenge Title" required>
                </div>

                <div class="form-group {{($challenge_data['categories'] ? '' : 'hide')}}">
                    <strong><label for="category">Category *</label></strong>
                    <select class="form-control" name="category" id="category" >
                        <option value="">select Category</option>
                        @foreach ($challenge_data['categories'] as $cat)
                            <option value="{{$cat['id']}}" {{($challenge->category_id == $cat['id'] ? "selected" : "")}}>{{$cat['category_name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Status *</label> </strong>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status-1" value="-1" {{$challenge->status == '-1' ? "checked" : ""}} required>
                            -1 &nbsp;(Waiting)
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                    <!-- <div class="col-sm-3">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status0" value="0" {{$challenge->status == '0' ? "checked" : ""}} required>
                            0 &nbsp;(Reject)
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="1" {{$challenge->status == '1' ? "checked" : ""}} required>
                            1 &nbsp;(Accept)
                        <i class="input-helper"></i></label>
                        </div>
                    </div> -->
                </div>

                <div class="form-group">
                    <strong><label for="point">Point</label></strong>
                    <select class="form-control" name="point" id="point">
                        <option value="1" {{$challenge->point == "1" ? "selected" : ""}}>1</option>
                        <option value="2" {{$challenge->point == "2" ? "selected" : ""}}>2</option>
                        <option value="3"{{$challenge->point == "3" ? "selected" : ""}}>3</option>
                        <option value="4"{{$challenge->point == "4" ? "selected" : ""}}>4</option>
                        <option value="5"{{$challenge->point == "5" ? "selected" : ""}}>5</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <strong><label>Challenge Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>
                @if($challenge->image != '')
                    <div class="form-group form-group-image">
                    <strong><label>Profile Pic</label></strong>
                        <div class="relativeDiv">
                            <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                            <img src="<?php echo \Storage::disk('s3')->url('images/challenge/'.$challenge->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        </div>
                    </div>
                @endif
                
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        @else
            <div class="text-center"><h3><i class="fa fa-warning size-40"></i></br> This challenge is in use or its over.</h3></div>
        @endif
        </div>
    </div>
        <!-- </div>
    </div> -->

</div>

@endsection
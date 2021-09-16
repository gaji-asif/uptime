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
        <h4 class="card-title theme-color">Add New Reward</h4>

            <form action="{{ url('executive/reward/store') }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                 
                   
                     <div class="form-group">
                        <strong><label for="url_link">Name *</label> </strong>
                        <input type="text" name="name" id="name" class="form-control" maxlength ="25" placeholder="Name"  required>
                    </div>
                    
                    <div class="form-group">
                        <strong><label for="point">Point</label></strong>
                        <input type="number" class="form-control" name="point" id="point" min="0"  required>
                    </div>

                    <div class="form-group">
                        <strong><label for="description">Description *</label> </strong>
                        <input type="text" name="description" id="description" class="form-control" placeholder="Description" required>
                    </div>


                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Reward Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="is_active1" value="0" checked required>
                            0
                        <i class="input-helper"></i></label>
                        </div>
                        <label>Inactive</label>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="is_active2" value="1"required>
                            1 
                        <i class="input-helper"></i></label>
                        </div>
                         <label>Active</label>
                    </div>
                   
                </div>



                    <div class="form-group">
                      
                            <strong><label>Access Level *</label></strong>
                            <select class="form-control" name="access_level" id="access_level">                      
                                <option value="">Select Access Level</option>
                                @foreach ($reward_data['access_level'] as $access_level)
                                    <option value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
                                @endforeach                       
                            </select>
                    </div>  

             

                <div class="form-group">
                     <strong><label>Upload Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Reward Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                </div>
                
               
                <button type="submit" class="btn btn-theme mr-2">Submit</button>      

 
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>

@endsection
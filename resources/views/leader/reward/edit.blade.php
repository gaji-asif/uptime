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
        <h4 class="card-title theme-color">Edit Reward</h4>
           <form action="{{url('leader/reward/'.$reward->id.'/update')}}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                 
                   
                     <div class="form-group">
                        <strong><label for="url_link">Name *</label> </strong>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Name" value = "{{$reward->name}}" required>
                    </div>
                    
                    <div class="form-group">
                        <strong><label for="point">Point</label></strong>
                        <input type="number" class="form-control" name="point" id="point" max="100" min="0" value="{{$reward->point}}" required>
                    </div>

                    <div class="form-group">
                        <strong><label for="description">Description *</label> </strong>
                        <input type="text" name="description" id="description" class="form-control" placeholder="Description" value="{{$reward->description}}" required>
                    </div>


                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Reward Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="is_active1" value="0" {{$reward->is_active == '0' ? "checked" : ""}} required>
                            0
                        <i class="input-helper"></i></label>
                        </div>
                         <label>Inactive</label>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="is_active2" value="1" {{$reward->is_active == '1' ? "checked" : ""}} required>
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
                                    <option value="{{$access_level['id']}}"  {{$reward->access_level == $access_level['id'] ? "selected='selected'" : ""}}>{{$access_level['access_level_name']}}</option>
                                @endforeach                       
                            </select>
                    </div>  

               <div class="form-group">

                     <h5 class="my-4">Reward Image</h5>

                    <div class="new-accounts reward-detail-image">
                          @if($reward->image != '')
                          <img src="<?php echo \Storage::disk('s3')->url('images/reward/'.$reward->image);?>" alt="Reward image" width="100%" height = "auto" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                          @else
                          <div class="btn btn-outline-danger file-icon">
                            <i class="mdi mdi-image-broken"></i>
                          </div>
                          @endif

                         </div>
                    </div>

                <div class="form-group">
                   
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
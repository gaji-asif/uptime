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
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                <li>{{ $message }}</li>
            </ul>
        </div>
    @endif
    
     
    <div class="card col-md-8">
        <div class="card-body">
        <h4 class="card-title theme-color">Edit Submission Request</h4>        
                  
                @csrf
                
                <input type="hidden" name="build_id" value="">
                <div class="form-group">
                    <strong><label for="build_text">Submission Name *</label> </strong>
                    <input type="text" name="build_text" id="build_text" class="form-control" value="{{$employee_name}}" placeholder="First Name" required>
                </div>

                <input type="hidden" name="employee" value="213">
                <div class="form-group">
                    <strong><label for="category_id">Category Name *</label></strong>
                    <select class="form-control buil-category-add" name="category" id="category_id" required data-id="">                    
                    <option value="">{{$category_name}}</option>
                    </select>
                </div>

            
                <div class="form-group">
                    <strong><label>Submission Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                  
                    </div>
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>
                @if($image != '')
                    <div class="form-group form-group-image">
                    <strong><label>Profile Pic</label></strong>
                        <div class="relativeDiv">
                            <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                            <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$builds->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        </div>
                    </div>
                 @endif
                
                <a class='btn action-btn btn-outline-info' href="<?php echo url('employee/approve-request/'.$request_id.'/accept') ?>"><i class='fa fa-eye'></i> Approve</a> <a class='btn action-btn btn-outline-danger trash-button' href="<?php echo url('employee/approve-request/'.$request_id.'/reject');?>" data-att-name='employee'><i class='fa fa-trash'></i> Reject</a>
              
            </form>
       
        </div>
    </div>
     
</div>

@endsection
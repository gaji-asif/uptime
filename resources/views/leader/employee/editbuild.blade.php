@extends('leader/layouts.app')

@section('content')
<?php //print_r($builds_data); die; ?>
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
        <h4 class="card-title theme-color">Edit Build</h4>
        @if(isset($builds))

            <form action="{{ url('leader/employee/build/updatebuild') }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
            
                @csrf
                
                <input type="hidden" name="build_id" value={{$builds->id}}>
                <div class="form-group">
                    <strong><label for="build_text">Build Name *</label> </strong>
                    <input type="text" name="build_text" id="build_text" class="form-control" value="{{$builds->build_text}}" placeholder="First Name" required>
                </div>

              
                <input type="hidden" name="employee" class="empoyee-new-build" id="employee_id" value="{{ Session::get('employee')->id }}">
              
               

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Build Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="-1" {{$builds->status == '-1' ? "checked" : ""}} required>
                            -1
                        <i class="input-helper"></i></label>
                        <br>
                        <label>No Status</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status2" value="0" {{$builds->status == '0' ? "checked" : ""}} required>
                            0
                        <i class="input-helper"></i></label>
                         <br>
                         <label>Rejected</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status3" value="1" {{$builds->status == '1' ? "checked" : ""}} required>
                            1
                        <i class="input-helper"></i></label>
                        <br>
                         <label>Approved</label>
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
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>
                @if($builds->image != '')
                    <div class="form-group form-group-image">
                    <strong><label>Profile Pic</label></strong>
                        <div class="relativeDiv">
                            <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                            <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$builds->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        </div>
                    </div>
                    @endif
                
               
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
              
            </form>
        @else
            <div class="text-center"><h3><i class="fa fa-warning size-40"></i></br> This bulid is over </h3></div>
        @endif
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>
<script type="text/javascript">
     $( "#category" ).change(function() {
       
        var _token = $("input[name='_token']").val();
        var cat_id = $('#category').val();

        $.ajax({
                url: "{{ url('leader/employee/level-preset-challenge/getsubcategory') }}/"+cat_id,
                type: "post",
                data: {_token:_token},
                success: function(d) {
                    if(d.sub_cat_html){
                        $(".sub_cat").removeClass("hide");
                        $('#subcategory').html(d.sub_cat_html);
                    }else{
                        $('.sub_cat').addClass("hide");
                        swal("Oops!","No SubCategory", "error");
                    }
                }
            });

    });  
</script>
@endsection
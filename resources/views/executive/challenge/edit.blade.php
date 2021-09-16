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
        <h4 class="card-title theme-color">Edit Challenge</h4>
        @if(isset($challenge))
            <form action="{{ route('challenge.update',$challenge->id) }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                    <select class="form-control" name="category" id="category" required>
                        <option value="">select Category</option>
                        @foreach ($challenge_data['categories'] as $cat)
                            <option value="{{$cat['id']}}" {{($challenge->category_id == $cat['id'] ? "selected" : "")}}>{{$cat['category_name']}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group sub_cat hide">
                    <strong><label for="category">SubCategory</label></strong>
                    <select class="form-control" name="subcategory" id="subcategory">
                        <option value="">select Subcategory</option>   
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
                    <input type="number" class="form-control" name="point" id="point" max="100" min="0" value="{{ $challenge->point }}">
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
                
                <div class="form-group">
                    <strong><label for="access_level">Access Level</label></strong>
                    <select class="form-control" name="access_level" id="access_level">
                        @foreach($challenge_data['access_level_data'] as $key => $access_level)
                        <option <?php if($challenge->access_level == $access_level['id']) {echo "selected";} ?> value="{{ $access_level['id'] }}">{{ $access_level['access_level_name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <strong><label for="sent_to">Who is sent</label></strong>
                    <select class="form-control" name="sent_to" id="sent_to">
                        <option value="1" <?php if($challenge->sent_in == 1) {echo "selected";} ?>>To a region</option>
                        <option value="2" <?php if($challenge->sent_in == 2) {echo "selected";} ?>>Each Access Level</option>
                        <option value="1" <?php if($challenge->sent_in == 3) {echo "selected";} ?>>select group of people </option>
                    </select>
                </div>

                 <div class="form-group">
                    <strong><label for="end_date">End date</label></strong>
                    <input type="text" value="{{ $challenge->end_on }}" class="form-control" name="end_date" id="datetimepicker">
                </div>

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

<style type="text/css">
    input#datetimepicker{
        border: 1px solid #f3f3f3;
        font-family: "Open Sans", sans-serif;
        font-size: 0.75rem;
        padding: 0.56rem 0.75rem;
        line-height: 14px;
        width: 100%;
    }
</style>

<script type="text/javascript">

    $('#datetimepicker_mask').datetimepicker({
        mask:'9999/19/39 29:59',
    });
    $('#datetimepicker').datetimepicker();
    $('#datetimepicker1').datetimepicker({
        datepicker:false,
        format:'H:i',
        step:5
    });

    $( document ).ready(function() {
        $("#company_id").change(function(){
            $('.sub_cat').addClass("hide");
        })

        $( "#category" ).change(function() {
            var _token = $("input[name='_token']").val();
            var cat_id = $('#category').val();

            $.ajax({
                url: "{{ url('/challenge/getsubcategory') }}/"+cat_id,
                type: "post",
                data: {_token:_token},
                success: function(d) {
                    if(d.sub_cat_html){
                        $(".sub_cat").removeClass("hide");
                        $('#subcategory').html(d.sub_cat_html);
                    }else{
                        $('.sub_cat').addClass("hide");
                    }
                }
            });
        });

        var category_id = $('#category').val(); 
        var _token = $("input[name='_token']").val();
            var cat_id = $('#category').val();

            $.ajax({
                url: "{{ url('/challenge/getsubcategory') }}/"+cat_id,
                type: "post",
                data: {_token:_token},
                success: function(d) {
                    if(d.sub_cat_html){
                        $(".sub_cat").removeClass("hide");
                        $('#subcategory').html(d.sub_cat_html);
                    }else{
                        $('.sub_cat').addClass("hide");
                    }
                }
            });

    });
</script>
@endsection
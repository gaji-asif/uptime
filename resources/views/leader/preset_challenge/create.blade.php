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
            <h4 class="card-title theme-color">Add New Challenge</h4>
            <form action="{{ url('preset-challenge/store') }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                @if($challenge_data['is_admin'] == 1)
                <div class="form-group">
                    @if($challenge_data['users'])
                    <strong><label for="company_id">Company Name *</label></strong>
                    <select class="form-control chall-company-select" name="company" id="company_id" required>
                    <option value="">select Company</option>
                    @foreach ($challenge_data['users'] as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                    @endforeach
                    </select>
                    @else
                    <strong><label>No Company user available</label></strong>
                    @endif
                </div>
                @endif

                <div class="form-group">
                    <strong><label for="challenge_text">Challenge Name *</label> </strong>
                    <input type="text" name="challenge_text" id="challenge_text" class="form-control" placeholder="Challenge Title" required>
                </div>

                <div class="form-group {{($challenge_data['categories'] ? '' : 'hide')}}">
                    <strong><label for="category">Category *</label></strong>
                    <select class="form-control" name="category" id="category" required>
                        <option value="">select Category</option>
                        @foreach ($challenge_data['categories'] as $cat)
                            <option value="{{$cat['id']}}">{{$cat['category_name']}}</option>
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
                            <input type="radio" class="form-check-input" name="status" id="status-1" value="-1" checked="" required>
                            -1 &nbsp;(Waiting)
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                     
                </div>

                <div class="form-group">
                    <strong><label for="point">Point</label></strong>
                    <input type="number" class="form-control" name="point" id="point" max="100" min="0">
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
                </div>

               
               <div class="form-group">
                    <strong><label for="sent_to">Sent to</label></strong>
                    <select class="form-control select_target" name="sent_to" id="sent_to">
                         <option value="">select Person</option>  
                        <option value="1">Employee</option>
                        <option value="2">Region</option>
                        <option value="3">All </option>
                    </select>
                </div>
    
              <div id="employee-list" class="form-group hide">
                    
                </div>

                
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
                
            </form>
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

    $('#datetimepicker').datetimepicker();
    $('#datetimepicker1').datetimepicker({
        datepicker:false,
        format:'H:i',
        step:5
    });

    $("#company_id").change(function(){
        $('.sub_cat').addClass("hide");
    });

  $('.select_target').change(function(e) {

                 e.preventDefault();
                 var form = $(this).closest('form');
                 var key = $(this).val();
                       
                form.find('#employee-list').removeClass('hide').addClass('hide');
                 form.find('#employee-list').html('');

                  if(key != ''){
                    $.ajax({
                      headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                       url: "{{ url('/challenge/getemployee') }}/"+key,
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                        if(result.status){

                          form.find('#employee-list').removeClass('hide');
                          form.find('#employee-list').html(result.html);
                        }
                        else{
                           swal("Oops!", result.message, "error");
                        }
                      }
                    });
                }
      });
  
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
</script>



@endsection
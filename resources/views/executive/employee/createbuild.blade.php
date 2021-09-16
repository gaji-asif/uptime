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
            <h4 class="card-title theme-color">Add New Submission</h4>
            <form action="{{route('executive.employee.builds.storebuild')}}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <strong><label for="build_text">Submission Name *</label> </strong>
                    <input type="text" name="build_text" id="build_text" class="form-control" placeholder="First Name" required>
                </div>
		 
		 <div class="form-group">
                
                    <strong><label for="employee_id">Employee Name *</label></strong>
                    <select class="form-control " name="employee" id="employee_id" required>
                    <option value="">Select employee</option>
                    @foreach ($employees as $builds)
                        <option value="{{$builds['id']}}">{{$builds['full_name']}}</option>
                    @endforeach
                    </select>
                    
                </div>


                <div class="form-group">
                    <strong><label for="category_id">Category Name *</label></strong>
                    <select class="form-control buil-category-add_emp" name="category" id="category" required  data-id="0">
                        <option value="">select category</option>
                        <?php 
                        $category_html = "";
                        foreach ($categories as $item) {
                            $category_html .= '<option value="'.$item['id'].'">'.$item['category_name'].'</option>';
                        }
                        echo $category_html;

                        ?>
                    </select>
                </div>



               <div class="form-group sub_cat hide">
                    <strong><label for="category">SubCategory</label></strong>
                    <select class="form-control" name="subcategory" id="subcategory">
                        <option value="">select Subcategory *</option>   
                    </select>
                </div>



                <div class="form-group">
                    <strong><label for="challenge_id">Use challenge</label></strong>
                    <select class="form-control" name="challenge" id="challenge_id" disabled>
                        <option value="0">select challenge</option>
                    </select>
                </div>                

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Submission Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="-1" checked="" required>
                            -1
                        <i class="input-helper"></i></label>
                        <p>No Status<p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status2" value="0" required>
                            0
                        <i class="input-helper"></i></label>
                         <p>Rejected<p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status3" value="1" required>
                            1
                        <i class="input-helper"></i></label>
                         <p>Approved<p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <strong><label>Submission Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                </div>
                
                @if(Session::get('employee')->id > 0)
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
                @endif
            </form>
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
                url: "{{ url('executive/employee/level-preset-challenge/getsubcategory') }}/"+cat_id,
                type: "post",
                data: {_token:_token},
                success: function(d) {
                    if(d.sub_cat_html){
                        $(".sub_cat").removeClass("hide");
                        $('#subcategory').html(d.sub_cat_html);
                    }else{
                        $('.sub_cat').addClass("hide");
                          swal("Oops!","No Subcategory", "error");
                    }
                }
            });

    });  
</script>
@endsection
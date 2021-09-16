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
            <h4 class="card-title theme-color">Edit Category</h4>
            <form id="category-form" action="{{ url('executive/employee/categories/update/'.request()->segment(count(request()->segments()))) }}" method="POST" class="forms-sample">
                @csrf

                <div class="form-group">
                    <strong><label for="main_category">Sub Category Name *</label></strong>
                    <input type="text" name="subcategory_name" value= "{{$categories_data['subcategory_name']}}"id="subcategory_name" class="form-control" placeholder="Subcategory name" required>
                </div>

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">SubCategory Status *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="0" {{$subcategory->status == '0' ? "checked" : ""}} required>
                            0
                        <i class="input-helper"></i></label>
                        <br>
                        <label>In Active</label>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status2" value="1" {{$subcategory->status == '1' ? "checked" : ""}} required>
                            1
                        <i class="input-helper"></i></label>
                         <br>
                         <label>Active</label>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <strong><label for="user_level">User Level *</label></strong>
                    <select class="form-control" name="user_level" id="user_level" required>
                    <option value="">select level</option>
                    @foreach ($categories_data['access_level'] as $access_level)
                        <option <?php if($subcategory->user_access_level == $access_level['id']) { echo "selected";} ?> value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
                    @endforeach
                    </select>
                </div>


                 <div class="form-group">
                    <strong><label for="region">Store *</label></strong>
                   <input type="hidden" name="region" id="region-input">
                    <select class="form-control" name="stores[]" id="region_select" required multiple>
{{--                    <option value="">Select Store</option>--}}
                    @foreach ($categories_data['region'] as $item)
                      <option <?php if(in_array($item['id'], explode(',',  $subcategory->region_id ))) { echo "selected";} ?> value="{{$item['id']}}">{{$item['industry_name']}}</option>
                    @endforeach
                    </select>
                </div>



                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>

<script>
  $(document).ready(function () {
    $('#region_select').multiselect();

    $('#category-form').submit(function (e) {

      const regions = $('#region_select').val();
      console.log(regions.join(','));
      $('#region-input').val(regions.join(','));
    });
  });
</script>

@endsection
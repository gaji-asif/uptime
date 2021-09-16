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
            <h4 class="card-title theme-color">Add New Sub Category</h4>
            <form id="category-form" action="{{route('executive.employee.categories.store')}}" method="POST" class="forms-sample">
                @csrf


                <div class="form-group">
                    <strong><label for="main_category">Category Name *</label></strong>
                    <select class="form-control" name="main_category" id="main_category" required>
                    <option value="">Select Category</option>
                    @foreach ($categories_data['Categories'] as $categories)
                        <option value="{{$categories['id']}}">{{$categories['category_name']}}</option>
                    @endforeach
                    </select>
                </div>

                <div class = "form-group">
                     <strong><label for="sub_category_name">Store Select *</label></strong>
                  <input type="hidden" name="region_select" id="region-input">
                     <select class="form-control" name="stores[]" id="region_select" required multiple>
{{--                       <option id ="">Select Store</option>--}}

                      @foreach ($categories_data['Region'] as $region)

                        @php
                        @endphp
                        <option value="{{$region['id']}}">{{$region['industry_name']}}</option>
                       @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <strong><label for="sub_category_name">Sub Category Name *</label> </strong>
                    <input type="text" name="sub_category_name" maxlength="20" id="sub_category_name" class="form-control" placeholder="Sub Category Name" required>
                </div>


                <div class="form-group">
                    <strong><label for="user_level">User Level *</label></strong>
                    <select class="form-control" name="user_level" id="user_level" required>
                    <option value="">select level</option>
                    @foreach ($categories_data['access_level'] as $access_level)
                        <option value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
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
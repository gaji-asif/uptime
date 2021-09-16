@extends('master/layouts.app')

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
            <h4 class="card-title theme-color">Edit Store </h4>
            <form action="{{ url('master/industry/'.$industry->id.'/update/') }}" method="POST" class="forms-sample">
                @csrf
                
                <div class="form-group">
                    <strong><label for="industry_name">Store Name *</label> </strong>
                    <input type="text" name="industry_name" id="industry_name" class="form-control" value="{{$industry->industry_name}}" placeholder="Category Name" required>
                </div>

                <div class="form-group">
                    <strong><label for="company_id">Company Name *</label></strong>
                    <select class="form-control" name="company_id" id="company_id" required>
                    <option value="">Select Company</option>
                    @foreach ($company as $item)
                        <option value="{{$item['id']}}" {{($industry->company_id == $item['id'] ? "selected" : "")}}>{{$item['name']}}</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <strong><label for="location">Location *</label></strong>                    
                    <input tye="text" class="form-control" placeholder="Enter the Location" name="location" value="{{$industry->location}}" id="textautoComplete">
                    <input type="text"  value="{{$industry->longitude}}"  name="longitude" id="longitude" class="form-control" hidden>
                    <input type="text" value="{{$industry->latitude}}"  name="latitude" id="latitude" class="form-control" hidden>
                    <input type="region" value="{{$industry->location}}"  name="region" id="region" class="form-control" hidden>
                </div>     
             

                
                <button type="submit" class="btn btn-theme mr-2">Update</button>
            </form>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAaIy8XLRWOin0KB752kWt41zldWWkF-xQ&libraries=places"></script>
    <script>
    
     google.maps.event.addDomListener(window,'load',initialize);
     geocoder = new google.maps.Geocoder();
     function initialize(){
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('textautoComplete'));
        google.maps.event.addListener(autocomplete,'place_changed',function(){         
            var place = autocomplete.getPlace();

            var location = place.formatted_address;
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            $('#region').val(location);
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
        });
     }
    </script>
</div>

@endsection
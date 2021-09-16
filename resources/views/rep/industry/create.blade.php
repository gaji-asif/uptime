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
    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Add New Region</h4>
            <form action="{{ url('industry/store') }}" method="POST" class="forms-sample">
                @csrf
                <div class="form-group">
                    <strong><label for="industry_name">Region Name *</label> </strong>
                    <input type="text" name="industry_name" id="industry_name" class="form-control" placeholder="Region Name" required>
                </div>


                 <div class="form-group">
                    @if($data['company'])
                    <strong><label for="company_id">Company Name *</label></strong>
                    <select class="form-control chall-company-select" name="company" id="company_id" required>
                    <option value="">select Company</option>
                    @foreach ($data['company'] as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                    @endforeach
                    </select>
                    @else
                    <strong><label>No Company user available</label></strong>
                    @endif
                </div>
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        </div>
    </div>
 
</div>

@endsection
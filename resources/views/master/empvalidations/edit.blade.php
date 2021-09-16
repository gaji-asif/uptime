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
    
    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
        <h4 class="card-title theme-color">Edit Validation</h4>
        @if(isset($validations))
            <form action="{{ route('validations.update',$validations->id) }}" method="POST" class="forms-sample">
                @csrf
                @method('PUT')

                @if($validations_data['builds'])
                <div class="form-group">
                    <strong><label for="build_id">Build Name *</label></strong>
                    <select class="form-control get-employee-from-build" name="build" id="build_id" required>
                    <option value="">Select Build</option>
                    @foreach ($validations_data['builds'] as $bld)
                        <option value="{{$bld['id']}}" {{($validations->build_id == $bld['id'] ? "selected" : "")}}>{{$bld['build_text']}}</option>
                    @endforeach
                    </select>
                </div>
                @endif

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Status *</label> </strong>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="0" {{($validations->status == '0' ? "checked" : "")}} required>
                            0 &nbsp;(Reject)
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status3" value="1" {{($validations->status == '1' ? "checked" : "")}} required>
                            1 &nbsp;(Accept)
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Result (Win) *</label> </strong>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="win" id="status1" value="-1" {{($validations->win == '-1' ? "checked" : "")}} required>
                            Pending
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="win" id="win1" value="0" {{($validations->win == '0' ? "checked" : "")}} required>
                            0 &nbsp;(Loss)
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="win" id="win3" value="1" {{($validations->win == '1' ? "checked" : "")}} required>
                            1 &nbsp;(Win)
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                </div>
                
                <div class="form-group employee-list {{($validations_data['employee'] ? '' : 'hide')}}">
                    <strong><label for="employee_id">Employee Name *</label></strong>
                    <select class="form-control" name="employee" id="employee_id" required>
                    <option value="">Select Employee</option>
                    @if($validations_data['employee'])
                    @foreach ($validations_data['employee'] as $emp)
                    <option value="{{$emp['id']}}" {{($validations->employee_id == $emp['id'] ? "selected" : "")}}>{{$emp['full_name']}}</option>
                    @endforeach
                    @endif
                    </select>
                </div>
                
                
                <button type="submit" class="btn btn-theme mr-2">Update</button>
            </form>
        @else
        <div class="text-center"><h3><i class="fa fa-warning size-40"></i></br>  This validation bulid is over </h3></div>
        @endif
        </div>
    </div>
        <!-- </div>
    </div> -->

</div>

@endsection
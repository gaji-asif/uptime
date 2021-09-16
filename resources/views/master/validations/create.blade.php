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
            <h4 class="card-title theme-color">Add New Validation</h4>
            <form action="{{ url('master/validations/store') }}" method="POST" class="forms-sample">
                @csrf

                @if($validations_data['builds'])
                <div class="form-group">
                    <strong><label for="build_id">Submission Name *</label></strong>
                    <select class="form-control get-employee-from-build" name="build" id="build_id" required>
                    <option value="">Select Submission</option>
                    @foreach ($validations_data['builds'] as $bld)
                        <option value="{{$bld['id']}}">{{$bld['build_text']}}</option>
                    @endforeach
                    </select>
                </div>
                @endif

                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Status *</label> </strong>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status1" value="0" checked="" required>
                            0 &nbsp;(Reject)
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <strong><label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status3" value="1" required>
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
                            <input type="radio" class="form-check-input" name="win" id="status1" value="-1" checked="" required>
                            Pending
                        <i class="input-helper"></i></label></strong>
                        </div>
                    </div>
                </div>

                <div class="form-group employee-list hide">
                    <strong><label for="employee_id">Employee Name *</label></strong>
                    <select class="form-control" name="employee" id="employee_id" required>
                    <option value="">Select Employee</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-theme mr-2 hide">Submit</button>
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->

</div>

@endsection
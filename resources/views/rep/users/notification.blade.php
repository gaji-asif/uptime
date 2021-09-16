@extends('rep/layouts.app')


@section('content')
<div class="content-wrapper">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
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
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Push Notification</h4>



            <form action="{{ url('rep/users/sendnotification') }}" autocomplete="nope" method="POST" class="forms-sample" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <strong><label for="notification">Notification Text *</label> </strong>
                    <textarea rows="4" cols="50" name="notification-text" id="notification" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <strong><label for="role">Notification For *</label></strong>
                    <select class="form-control change-notification-role" name="notification-for" id="role" required>
                        <option value="">Select</option>
                        <option value="company">Company</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                 <div id="notification-list" class="form-group hide">
                </div>

                 <div id="employee-notification-list" class="form-group hide">
                    
                </div>
                
                <button type="submit" class="btn btn-theme mr-2">Send</button>
            </form>
        </div>
    </div>
</div>

@endsection
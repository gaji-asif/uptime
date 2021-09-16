@extends('rep/layouts.loginlayout')

@section('content')
<div class="login-page-class"></div>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
        <div class="row w-100">
          <div class="col-lg-4 mx-auto">
                <div class="login-logo">
                    <h1 class="card-title mb-0">Uptime</h1>
                </div>

                <div class="auto-form-wrapper change-class">
                    <form method="POST" action="{{ route('executive.login') }}">
                    @csrf
                    @if ($errors->has('email'))
                        <span class="invalid-feedback display-login-error">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    @if ($errors->has('password'))
                        <span class="invalid-feedback display-login-error">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <div class="form-group">
                        <label class="label">Email</label>
                        <div class="input-group">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-check-circle-outline"></i></span>
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="label">Password</label>
                        <div class="input-group">
                        <input id="password" type="password" placeholder="*********" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-check-circle-outline"></i></span>
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-theme submit-btn btn-block">Login</button>
                    </div>    
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
</div>
    <!-- page-body-wrapper ends -->
</div>
@endsection


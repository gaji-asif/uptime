@extends('executive/layouts.loginlayout')

@section('content')
<div class="login-page-class"></div>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
        <div class="row w-100" style="margin: auto;">
          <div class="col-lg-4 mx-auto">
                <div class="login-logo">
                    <h1 class="card-title mb-0">Uptime</h1>
                </div>

                <div class="auto-form-wrapper change-class">
                    @if (session('success'))
                        <p class="card-title mb-0" style="text-align: center; margin-bottom: 30px !important;">
                            {{ session('success')}}
                        </p>
                    @elseif (session('error'))
                        <p class="card-title mb-0" style="text-align: center; margin-bottom: 30px !important;">
                            {{ session('error')}}
                        </p>
                    @else
                    <form method="POST" action={{route('resetpassword_submit')}}>
                        @csrf
                        @if ($errors->has('password'))
                            <span class="invalid-feedback display-login-error">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif

                        @if ($errors->has('password_confirmation'))
                            <span class="invalid-feedback display-login-error">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label for="password" class="label">{{ __('New Password') }}</label>
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="label">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                                <button type="submit" class="btn btn-theme submit-btn btn-block">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                 </div>

            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
</div>
    <!-- page-body-wrapper ends -->
</div>
@endsection


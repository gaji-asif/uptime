@extends('rep/layouts.app')

@section('content')
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="profile-body">
              <ul class="nav tab-switch" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info" role="tab" aria-controls="user-profile-info" aria-selected="true">Profile</a>
                </li>
              </ul>
              <div class="row">
                <div class="col-md-9">
                  <div class="tab-content tab-body" id="profile-log-switch">
                    <div class="tab-pane fade show active pr-3" id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">
                      <table class="table table-borderless w-100 mt-4">
                        <tr>
                          <td>First Name :<strong> {{ $users->first_name }}</strong></td>
                          <td>Last Name :<strong> {{ $users->last_name }}</strong></td>
                        </tr>
                        <tr>
                          <td>Address :<strong> {{ $users->address }}</strong></td>
                          <td>Email :<strong> {{ $users->email }}</strong></td>
                        </tr>
                        <tr>
                          <td>Website :<strong> {{ $users->website_url }}</strong></td>
                          <td>Role : <strong>{{ $users->role }}</strong></td>
                        </tr>
                      </table>
                      @if($users->role != 'admin')
                      <div class="extra-link">
                        <a class="btn btn-light btn-fw" href="{{ url('rep/employee/company',$users->id)}}">View company's Employee &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                      @endif
                   
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <h5 class="my-4">Profile photo</h5>
                  <div class="new-accounts user-detail-image">
                    @if($users->pic != '')
                    <img src="<?php echo \Storage::disk('s3')->url('images/user/'.$users->pic);?>" alt="profile image" width="200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                    @else
                    <div class="btn btn-outline-danger file-icon">
                      <i class="mdi mdi-account-alert"></i>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@extends('executive/layouts.app')

@section('content')
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="profile-body">
              <ul class="nav tab-switch" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info" role="tab" aria-controls="user-profile-info" aria-selected="true">Challenge Detail</a>
                </li>
              </ul>
              <div class="row">
                <div class="col-md-9">
                  <div class="tab-content tab-body" id="profile-log-switch">
                    <div class="tab-pane fade show active pr-3" id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">
                      <table class="table table-borderless w-100 mt-4">
                        <tr>
                          <td>Challenge Name :<strong> {{ $challenge->challenge_text }}</strong></td>
                          <td>Status :<strong>
                            @if($challenge->status == '-1')
                            <label class='badge badge-info'>Waiting</label>
                            @elseif($challenge->status == '1')
                            <label class='badge badge-success'>Win</label>
                            @elseif($challenge->status == '0')
                            <label class='badge badge-warning'>In progress</label>
                            @endif
                            </strong>
                          </td>
                        </tr>
                        <tr>
                          <td>Company Name :<strong> {{ (isset($challenge->company_name) ? $challenge->company_name : '--') }}</strong></td>
                          <td>Category Name :<strong> {{ (isset($challenge->category_name) ? $challenge->category_name : '--') }}</strong></td>
                        </tr>
                        <tr>
                          <td>Build Name :<strong> {{ (isset($challenge->build_name) ? $challenge->build_name : '--') }}</strong></td>
                        </tr>
                        <tr>
                          <td>created At :<strong> {{ $challenge->created_at }}</strong></td>
                        </tr>
                      </table>
                      @if($challenge->status != '-1')
                      <div class="extra-link">
                        <a class="btn btn-light btn-fw" href="{{ url('builds',$challenge->build_id)}}">View Build Detail &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <h5 class="my-4">Build Image</h5>
                  <div class="new-accounts user-detail-image">
                    @if($challenge->image != '')
                    <img src="<?php echo \Storage::disk('s3')->url('images/challenge/'.$challenge->image);?>" alt="Build image" width="200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                    @else
                    <div class="btn btn-outline-danger file-icon">
                      <i class="mdi mdi-image-broken"></i>
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
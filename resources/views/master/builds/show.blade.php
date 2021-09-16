@extends('master/layouts.app')

@section('content')
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            
            <div class="profile-body">

              <ul class="nav tab-switch" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active theme-color" id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info" role="tab" aria-controls="user-profile-info" aria-selected="true">Submission Detail</a>
                </li>
              </ul>

              <div class="row">
                

                <div class="col-md-4">

                       <div class="tab-content tab-body" id="profile-log-switch">
                    <div class="tab-pane fade show active pr-3" id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">
                      <table class="table table-borderless w-100 mt-4">
                        <tr>
                          <td>Submission Name :<strong> {{ $builds->build_text }}</strong></td>
                        </tr>
                        <tr>
                          <td>Employee Name :<strong> {{ (isset($builds->employee_name) ? $builds->employee_name : '--') }}</strong></td>
                         
                        </tr>
                        <tr>
                          <td>Status :<strong>
                          @if($builds->status == '-1')
                          <label class='badge badge-warning'>In progress</label>
                          @elseif($builds->status == '1')
                          <label class='badge badge-info'>Win</label>
                          @elseif($builds->status == '0')
                          <label class='badge badge-danger'>Loss</label>
                          @endif
                          </strong></td>
                          @if(isset($builds->challenge_name) && $builds->challenge_name != '--')
                          
                          @endif
                        </tr>
                      </table>
                      <div class="extra-link">
                        <a class="btn btn-light btn-fw" href="{{ url('master/employee',$builds->employee_id)}}">View employee Detail &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                  </div>

                </div>           
              


                <div class="col-md-8">
                   <br>
                    <p>Category Name :<strong> {{ (isset($builds->category_name) ? $builds->category_name : '--') }}</strong></p>
                                    
                  <div class="row">
                    
                   @if($builds->challenge_image != '')
                    <div class="col-md-6 " >
                        <h5 class="my-4">Challenge Image</h5>
                      <div class="new-accounts user-detail-image">
                     
                      <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$builds->challenge_image);?>" alt="challenge_image" width="200px" height = "200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                      
                      
                      </div>
                      <br>
                    <p>Description :<strong> {{ $builds->challenge_name }}</strong></p>
                      <p>created At :<strong> {{ $builds->created_at }}</strong></p>
                    </div> 
                      @endif 
                      <div class="col-md-6">
                        <h5 class="my-4">Submission Image</h5>
                        <div class="new-accounts user-detail-image">
                          @if($builds->image != '')
                          <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$builds->image);?>" alt="Submission image" width="200px" height = "200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                          @else
                          <div class="btn btn-outline-danger file-icon">
                            <i class="mdi mdi-image-broken"></i>
                          </div>
                          @endif

                        </div>
                        <br>
                           <p>Description :<strong> {{ $builds->challenge_name }}</strong></p>
                            <p>created At :<strong> {{ $builds->created_at }}</strong></p>
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
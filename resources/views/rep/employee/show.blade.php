@extends('rep/layouts.app')


@section('content')
<?PHP function ordinal($number) {
  $ends = array('th','st','nd','rd','th','th','th','th','th','th');
  if ((($number % 100) >= 11) && (($number%100) <= 13))
    return $number. 'th';
  else
    return $number. $ends[$number % 10];
} ?>
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="profile-body">
              <ul class="nav tab-switch" role="tablist">               
                <li class="nav-item">
                  <a class="nav-link  active theme-color" id="profile-tab" data-toggle="tab" href="#eProfile" role="tab" aria-controls="ourgoal" aria-selected="false">Employee Profile</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="home-tab" data-toggle="tab" href="#eInfo" role="tab" aria-controls="whoweare" aria-selected="true">Employee Info</a>
                </li>
              </ul>
              <div class="tab-content tab-content-basic">
                <div class="tab-pane fade" id="eInfo" role="tabpanel" aria-labelledby="home-tab">
                  <div class="row">
                    <div class="col-md-9">
                      <div class="tab-content tab-body" id="profile-log-switch">
                        <div class="tab-pane fade show active pr-3" id="user-profile-info" role="tabpanel" aria-labelledby="user-profile-info-tab">
                          
                          <table class="table table-borderless w-100 mt-4">
                            <tr>
                              <td>Full Name :<strong> {{ $employee->full_name }}</strong></td>
                              <td>Email :<strong> {{ $employee->email }}</strong></td>
                            </tr>
                            <tr>
                              <td>Company Name :<strong> {{ $employee->company_name }}</strong></td>
                              <td>Industry :<strong> {{ $employee->industry }}</strong></td>
                            </tr>
                            <tr>
                              <td>Join Date :<strong> {{ date("d/m/y",strtotime($employee->created_at)) }}</strong></td>
                              <!-- <td>Points :<strong> <label class="badge badge-success">{{ $employee->point }}</label></strong></td> -->
                            </tr>
                            <tr>
                              
                              <td>Cell phone  :<strong> {{ $employee->phone_number }}</strong></td>
                              <td>Access Level  :<strong> {{ $employee->access_level }}</strong></td>
                            </tr>
                            <!-- <tr>
                              <td>Total Build :<strong> <label class="badge badge-success">{{ $employee->build }}</label></strong></td>
                              <td>Total Validation :<strong> <label class="badge badge-success">{{ $employee->point }}</label></strong></td>
                            </tr> -->
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <h5 class="my-4">Profile Image</h5>
                      <div class="new-accounts user-detail-image">
                        @if($employee->image != '')
                        <img src="<?php echo \Storage::disk('s3')->url('images/employee/'.$employee->image);?>" alt="Profile Image" width="200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        @else
                        <div class="btn btn-outline-danger file-icon">
                          <i class="mdi mdi-image-broken"></i>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="extra-link col-md-3">
                      <a class="btn btn-light btn-fw" href="{{ route('users.show',$employee->company_id) }}">View company detail &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    @if($employee->build != 0)
                    <div class="extra-link col-md-3">
                      <a class="btn btn-light btn-fw" href="<?PHP echo url('builds/employee/'.$employee->id) ;?>">View Build list &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    @endif
                    @if($employee->validation != 0)
                    <div class="extra-link col-md-3">
                      <a class="btn btn-light btn-fw" href="<?PHP echo url('validations/employee/'.$employee->id) ;?>">View Validation list &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    @endif
                    <div class="extra-link col-md-3">
                      <a class="btn btn-light btn-fw" href="<?PHP echo url('tenure/employee/'.$employee->id) ;?>">View Tenure &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade active show" id="eProfile" role="tabpanel" aria-labelledby="profile-tab">
                  <div class="col-md-12 emp-detail-point">
                    <span>Points {{ $employee->point }} </span>
                    <hr>
                  </div>

                  <div class="col-md-12">
                      <div class="col-md-3 text-center fltleft">
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="round">
                              <div class="roundBorder round-blue"> <label>{{array_sum($monthLyCountArray['totalCount'])}}<br>Submissions</label></div>
                          </div>
                          <div class="details">
                              <button class="btn btn-info" data-toggle="modal" data-target="#submitionModal">More Details</button>
                          </div>
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="round">
                              <div class="roundBorder round-green"> <label>{{$wincountbuilds}}<br>Approved</label></div>
                          </div>
                          <div class="details">
                              <a href="{{url('employee/status/'.$employee->id.'/1')}}"  target="_blank"><button class="btn btn-success">More Details</button></a>
                          </div>
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="round">
                              <div class="roundBorder round-red"> <label>{{$loosecountbuilds}}<br>Rejected</label></div>
                          </div>
                          <div class="details">
                              <a href="{{url('employee/status/'.$employee->id.'/0')}}" target="_blank"><button class="btn btn-danger">More Details</button></a>
                          </div>
                      </div>
                  </div>

                  <div class="col-md-12 fltleft">
                      <div class="col-md-2 text-center fltleft">
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="squre">
                              <div class="squreBackground squre-blue"> <label><i class="fa fa-cogs"></i></label></div>
                          </div>
                          <div class="details">
                          <button class="btn btn-info" data-toggle="modal" data-target="#allcategories">Categories</button>
                            
                          </div>
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="squre">
                              <div class="squreBackground squre-green"> <label><i class="fa fa-users"></i></label></div>
                          </div>
                          <div class="details">
                            <!-- <a href="{{url('employee/status/'.$employee->id.'/1')}}"  target="_blank">
                              <button class="btn btn-success">Leaderboard</button>
                            </a> -->
                            <button class="btn btn-success" data-toggle="modal" data-target="#leaderboard">Leaderboard</button>
                          </div>
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="squre">
                              <div class="squreBackground squre-orange"> <label><i class="fa fa-pencil-square-o"></i></label></div>                                
                          </div>
                          <div class="details">
                              <a href="{{url('employee/downloadresume/'.$employee->id)}}" target="_blank"><button class="btn btn-warning">Profile PDF</button></a>
                          </div>
                      </div>
                      <div class="col-md-2 text-center fltleft">
                          <div class="squre">
                              <div class="squreBackground squre-red"> <label><i class="fa fa fa-upload"></i></label></div>  
                          </div>
                          <div class="details">
                              <button class="btn btn-danger" data-toggle="modal" data-target="#dailyModal">Tenure</button>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            
              <!-- <ul class="nav tab-switch" role="tablist">
                <li class="nav-item col-md-3 pad-right-90">
                  <a class="nav-link active" id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info" role="tab" aria-controls="user-profile-info" aria-selected="true">Employee Info</a>
                </li>
                <div class="extra-link col-md-9 text-right">
                    <a class="btn btn-theme btn-fw" href="<?PHP echo url('employee/downloadresume/'.$employee->id) ;?>">Get Resume</a>
                  </div>
              </ul> -->
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Modal -->
<div id="submitionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Submissions for last 12 months</h4>
      </div>
      <div class="modal-body">
        <canvas id="barChart" style="height:100px"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Daily Modal -->
<div id="dailyModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Daily Voting</h4>
      </div>
      <div class="modal-body">
        <canvas id="dailyChat" style="height:100px"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="allcategories" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Builds Approved</h4>
      </div>
      <div class="modal-body">
        <?php
          if(!empty($categoriesByEid)){
            echo '<table class="table"><thead><tr><th>Category Name</th><th>Win Count</th></tr></thead>';
            foreach($categoriesByEid as $key=>$cat){
              echo '<tbody><tr><td>'.$key.'</td><td>'.$cat.'</td></tr></tbody>';
            }
            echo '</table>';
          }else{
            echo '<h2>No Categories Found</h2>';
          }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Leaderboard Modal -->
<div id="leaderboard" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Top Challenges and points</h4>
      </div>
      <div class="modal-body">
        <?php
          if(!empty($win_challenge)){
            echo '<table class="table"><thead><tr><th>Name</th><th>Rank</th><th>Score</th></tr></thead>';
            foreach($win_challenge as $key => $chl){
              echo '<tbody><tr><td>'.$chl->full_name.'</td><td>'.ordinal($key+1).'</td><td>'.$chl->point.'</td></tr></tbody>';
            }
            echo '</table>';
          }else{
            echo '<h2>No Challenges Found</h2>';
          }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
  .modal-body{
    height: 400px;overflow-y: auto;
  }

</style>
<script>
$(function() {
  /* ChartJS
   * -------
   * Data and config for chartjs
   */
  var submitionModaldata = {
    labels:<?php echo json_encode($monthLyCountArray['labels']);?>,
    datasets: [{
      //label: '# of Votes',
      data: <?php echo json_encode($monthLyCountArray['totalCount']);?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
      ],
      borderWidth: 1
    }]
  };
  var submitionModaloptions = {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true
        }
      }]
    },
    legend: {
      display: false
    },
    elements: {
      point: {
        radius: 0
      }
    }
  };
  if ($("#barChart").length) {
    var barChartCanvas = $("#barChart").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: submitionModaldata,
      options: submitionModaloptions
    });
  }

  var dailyChatdata = {
    labels:<?php echo json_encode($countdailySubmission['labels']);?>,
    datasets: [{
      //label: '# of Votes',
      data: <?php echo json_encode($countdailySubmission['totalCount']);?>,
      // backgroundColor: [
      //   'rgba(255, 99, 132, 0.2)',
      //   'rgba(54, 162, 235, 0.2)',
      //   'rgba(255, 206, 86, 0.2)',
      //   'rgba(75, 192, 192, 0.2)',
      //   'rgba(153, 102, 255, 0.2)',
      //   'rgba(255, 159, 64, 0.2)',
      //   'rgba(255, 99, 132, 0.2)',
      //   'rgba(54, 162, 235, 0.2)',
      //   'rgba(255, 206, 86, 0.2)',
      //   'rgba(75, 192, 192, 0.2)',
      //   'rgba(153, 102, 255, 0.2)',
      //   'rgba(255, 159, 64, 0.2)',
      // ],
      // borderColor: [
      //   'rgba(255,99,132,1)',
      //   'rgba(54, 162, 235, 1)',
      //   'rgba(255, 206, 86, 1)',
      //   'rgba(75, 192, 192, 1)',
      //   'rgba(153, 102, 255, 1)',
      //   'rgba(255, 159, 64, 1)',
      //   'rgba(255,99,132,1)',
      //   'rgba(54, 162, 235, 1)',
      //   'rgba(255, 206, 86, 1)',
      //   'rgba(75, 192, 192, 1)',
      //   'rgba(153, 102, 255, 1)',
      //   'rgba(255, 159, 64, 1)',
      // ],
      // borderWidth: 1
    }]
  };
  var dailyChatoptions = {
    // scales: {
    //   yAxes: [{
    //     ticks: {
    //       //beginAtZero: true
    //     }
    //   }]
    // },
    legend: {
      display: false
    },
    // elements: {
    //   point: {
    //     radius: 0
    //   }
    // }
  };

 if ($("#dailyChat").length) {
    var dailyChatCanvas = $("#dailyChat").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var dailyChat = new Chart(dailyChatCanvas, {
      type: 'bar',
      data: dailyChatdata,
      options: dailyChatoptions
    });
  }
});

</script>
@endsection        
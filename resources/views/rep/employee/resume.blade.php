<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Meta Data -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee resume - Up-Time</title>

    <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/resume.css') }}" rel="stylesheet">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans:300,400,500,700" rel="stylesheet">
  </head>
 
  <body>
    <div class="table-billy-main">
      <div class="right-side">
        <div class="yr-div">
          <h1 class="yr-time"><?PHP echo date('Y');?></h1>
          <span class="time-span">Up time Portfolio</span>
          <span class="report-span">Report</span>
        </div>
        <div class="count-num">
          <span class="total-span">Total Numbers of validated Submission</span>
          <h1 class="num-Conunt">{{$employee['win_validation']}}</h1>
        </div>
        <div class="count-point">
          @if($employee['categories_list_left'])
          <div class="point-width">
            <ul>
            @for ($i = 0; $i < count($employee['categories_list_left']); $i++)
              <li class="{{(($i+2)%2 == 0) ? '' : 'light-blue'}}">
                <div class="point-conutDiv">
                  <div class="li-icon"><i class="fa {{$employee['icon_left'][$i]}}" aria-hidden="true"></i></div>
                  <div class="point-name">
                      <span class="point-title">{{$employee['categories_list_left'][$i]->category_name}}</span>
                      <span class="point-count">{{$employee['categories_list_left'][$i]->count}}</span>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </li>
            @endfor
            </ul>
          </div>
          @endif
          @if($employee['categories_list_right'])
          <div class="point-width">
            <ul>
            @for ($i = 0; $i < count($employee['categories_list_right']); $i++)
              <li class="{{(($i+2)%2 == 0) ? '' : 'light-blue'}}">
                <div class="point-conutDiv">
                  <div class="li-icon"><i class="fa {{$employee['icon_right'][$i]}}" aria-hidden="true"></i></div>
                  <div class="point-name">
                      <span class="point-title">{{$employee['categories_list_right'][$i]->category_name}}</span>
                      <span class="point-count">{{$employee['categories_list_right'][$i]->count}}</span>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </li>
            @endfor
            </ul>
          </div>
          @endif
          <div class="clearfix"></div>
        </div>
        <!-- <div class="total-vote">
          <h5 class="totalVote">total votes user received</h5>
          <h2 class="count-vote"><?PHP echo number_format("4261");?></h2>
        </div> -->
      </div>
      <div class="left-side">
        <div class="img-div-left">
          <div class="popup-div">
            <img src="<?php echo url('images/popup.png'); ?>" alt="popup">
            <div class="id-popup">
              <h2 class="popup-title">{{$employee['full_name']}}</h2>
              <span class="popup-id">{{$employee['email']}}</span>
              <span class="popup-num">{{$employee['phone_number']}}</span>
            </div>
          </div>
        </div>
        <div class="box-three">
          <div class="wer-div">
            <h2>Where your future and now meet</h2>
          </div>
          <div class="flag-img">
            <img src="<?php echo url('images/bg-lab.png'); ?>" alt="popup">

            <div class="id-flag">
              <span class="flag-id">validation score</span>
              <h2 class="flag-title">{{$employee['validation_score']}}</h2>
            </div>
          </div>
          <div class="flag-score">
            <div class="score-change">
              <h2 class="scoore-h2">{{$employee['win_challenge']}}</h2>
              <span class="change-span">challenges have been achived</span>
            </div>
            <div class="score-change">
              <img src="<?php echo url('images/flag.png'); ?>" alt="popup">
              <h2 class="out-flag"><span>out of </span>{{$employee['all_challenge']}}</h2>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>

        <div class="top-div">
          <h2 class="valid-text">The validation score <span>reflect</span></h2>
          <h2 class="top-text"><span class="employ">the</span> Top <span class="per-top">{{$employee['top_validation_per']}}% </span><span class="employ">of employee at</span></h2>
          <h2 class="ford-text">{{$employee['company_name']}}</h2>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="bottom-text btm-c">
        <p class="bottom-text">
          This document has been reviewed and approver by the CEO of Up Time Nayt Duval for the purposes of providing an accurate and trusted report on the individuals abilities and behavior.
        </p>
        <span>{{$employee['created_at']}}</span>
      </div>
    </div>
  </body>
</html>
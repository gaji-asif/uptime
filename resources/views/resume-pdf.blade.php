@php

//dd($categories);
  if (!empty($categories)) {
      // Group builds by category names
      $buildsByCategory = $categories
          ->mapWithKeys(function ($category) {
              return [
                  $category->name => isset($category->subcategories) ? $category->subcategories->mapWithKeys(function ($subcategory) {
                    return [$subcategory->name => $subcategory->builds];
                  }) : $category->builds
              ];
          })
          ->take(4)
          ->toArray();

          // dd($buildsByCategory2, $buildsByCategory, $categories);
  }
@endphp

@extends('layouts.app')

@section('title', 'Uptime Resume')

@push('stylesheets')

{{--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">--}}
{{--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">--}}
  <link href="{{ asset('css/vendor.bundle.addons.css') }}" rel="stylesheet">
  <link href="{{ asset('css/view/employeeview.css') }}" rel="stylesheet">
  <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
  <link href="{{ asset('css/owl.theme.css') }}" rel="stylesheet">
  <link href="{{ asset('css/view/style.css') }}" rel="stylesheet">
  <link href="{{ asset('css/portfolio.css') }}" rel="stylesheet">
  <link href="{{ asset('css/circle.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/lazy-load-images.min.css') }}">

  <style>
    #pdf-wrapper * {
      font-family: "Times New Roman", Times, serif !important;
    }
    .chart-wrapper .card-body {
      height: 592px;
      min-width: 100%;
    }
    .chart-wrapper .card {
      overflow-x: auto;
      overflow-y:hidden;
    }
    .chart-wrapper .custom-chart-legend {
      overflow-y: auto;
      padding-right: 15px;
    }
    .card-title .mdi {
      padding: 4px 8px;
      border: 2px solid #2a82a0;
      border-radius: 50%;
      color: #2a82a0;
      font-size: 24px;
    }

    /* Overwrite the default to keep the scrollbar always visible */
    .content-wrapper ::-webkit-scrollbar {
      -webkit-appearance: none;
      width: 7px;
    }
    .content-wrapper ::-webkit-scrollbar-thumb {
      border-radius: 4px;
      background-color: rgba(0,0,0,.5);
      -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
    }
  </style>

  <style type="text/css">
    .skill_set {
      margin: 25px 25px 25px 25px;
    }
    .skill_set > div {
      max-height: 700px;
      overflow-y: auto;
      padding-right: 10px;
    }
    .skill_set h3 {
      font-size: 22px;
      color: #2ebcae;
      border-bottom: 2px solid;
      padding-bottom: 3px;
    }

    .badge-light {
      color: white;
      background-color: #2ebcae;
      border-radius: 10px;
      float: right;
    }

    .margin_top_25 {
      margin-top: 25px;
    }
    .personal_objective {
      margin-bottom: 25px;
      padding: 10px 15px 10px 30px;
      background-color: #2ebcae;
      color: #fff;
      border-radius: 5px;
      box-shadow: 2px 2px 2px rgba(0,0,0,0.1);
    }
    .personal_objective .person_name {
      font-size: 25px;
      color: #fff;
      margin-bottom: 10px;
      font-weight: 600;
    }
    .personal_objective .objective_title {
      font-size: 18px;
      color: #fff;
      margin-bottom: 10px;
      width: 150px;
      border-bottom: 2px solid;
      font-weight: 400;
      padding-bottom: 5px;
    }
    .personal_objective .objective_content {
      color: #fff;
    }
    .personal_info {
      margin-left: 25px;
      margin-top: 35px;
      margin-bottom: 20px;
    }
    .personal_info .detail {
      margin-bottom: 5px;
      font-size: 16px;
      font-weight: 600;
    }
    .personal_info .detail span {
      padding-right: 15px;
      color: #2ebcae;
    }

    .profile-pic-layer {
      text-align: center;
    }


    #testinomila-carousel .item img{
      display: block;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      object-fit: cover;
    }

    .main-panel {
      margin:auto;
    }

    /* width */
    ::-webkit-scrollbar {
      width: 5px;
      margin-left: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      box-shadow: inset 0 0 5px grey;
      border-radius: 10px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #91dacc;
      border-radius: 10px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #91dacc;
    }

    .fade.in {
      opacity: 1 !important;
    }

    @media only screen and (max-width: 576px) {
      .skills {
        display: none;
      }
      .skills_mobile {
        margin: 20px 5px 20px 5px;
        display: block;
      }
    }

    @media only screen and (min-width: 576px) {
      .skills {
        display: block;
      }
      .skills_mobile {
        display: none;
      }
    }

    /* Style the Image Used to Trigger the Modal */
    #profileImage {
      cursor: pointer;
      transition: 0.3s;
      object-fit: cover;
    }

    #profileImage:hover {opacity: 0.7;}

    .reference-modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 10000; /* Sit on top */
      /* padding: 20px 20px 0px 20px; Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgba(231,233,238,1); /* Black w/ opacity */

    }
    .list-container {
      padding: 100px 10px 10px 10px;
    }
    .reference-image-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .reference-list-item-container {
      height: calc(100vh - 180px);
      overflow-y: auto;
    }
    .reference-list-dialog {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .reference-list-item {
      display: flex;
      margin-bottom: 20px;
      min-width: 400px;
    }
    .reference-list-item > div{
      text-align: left;
    }
    /* .reference-list-item-content {
      display: flex;
      justify-content: space-between
    } */
    .reference-list-item-middle {
      display: flex;
      flex-grow: 1;
      overflow: hidden;
      flex-direction: column;
      justify-content: center;
      margin-left: 24px;
      max-width: 300px;
    }
    .reference-list-item-right {
      width: 100px;
      display: flex;
      flex-direction: column;
      padding-left:8px
    }
    .reference-action {
      padding-top:4px;
      padding-bottom:4px;
      font-size: 12px;
    }

    /* The Modal (background) */
    .p-modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 10000; /* Sit on top */
      padding: 100px 20px 0px 20px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (Image) */
    .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
    }

    /* Caption of Modal Image (Image Text) - Same Width as the Image */
    #caption {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
      text-align: center;
      color: #ccc;
      padding: 10px 0;
      height: 150px;
    }

    /* Add Animation - Zoom in the Modal */
    .modal-content, #caption {
      animation-name: zoom;
      animation-duration: 0.6s;
    }

    @keyframes zoom {
      from {transform:scale(0)}
      to {transform:scale(1)}
    }

    /* The Close Button */
    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #f1f1f1;
      font-size: 50px;
      font-weight: bold;
      transition: 0.3s;
    }

    .close:hover,
    .close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    .search-input ::placeholder{
      color: #4a4a4a;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }

      .personal_info {
        text-align: center;
      }
    }

    .align-items-center{
      display: flex;
      justify-content: center;
      align-items: center;
    }

     h3, h4  {
       font-size: 2.5rem;
     }
     h5 {
      font-size: 2rem!important;
    }

    h2 {
      font-size: 3rem;
    }

    .skills * {
      font-size: 1.8rem;
    }

    .text-layer {
      padding: 30px;
      padding-top: 40px;
    }
    .text-layer p{
      font-size: 2rem;
    }
    .c100.big {
      box-shadow: unset;
    }
  </style>
@endpush

@section('header')
@endsection

@section('content')
  <div id="pdf-wrapper" class="content-wrapper bg-white">
    <div class="d-flex align-items-center mb-4">
      <div class="flex-grow-1">
        <hr>
      </div>
      <div class="px-3">
        <h5 class="text-muted">Uptime Resume</h5>
      </div>
      <div class="flex-grow-1">
        <hr>
        <small class="text-muted text-bold position-absolute" style="top: 20px; right: 30px;">
          {{ date('jS F, Y', strtotime(str_replace('-', '/', request()->route()->parameter('startmonth')))) }} - {{ date('jS F, Y', strtotime(str_replace('-', '/', request()->route()->parameter('endmonth')))) }}
        </small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
        <div class="row row-cols-1">
          <div class="col-md-12 mb-4">
            <div class="personal_info mt-0">
              <h2 class="mb-3">{{ $user_data['name'] }}</h2>
              <h4 class="text-muted mb-3">Personal Info</h4>
              <h5 class="mb-2"><span class="font-weight-semibold">{{ $user_data['phone_number'] }}</span></h5>
              <h5 class="mb-2"><span class="font-weight-semibold">{{ $user_data['email'] }}</span></h5>
              @if(isset($user_data['website']) && $user_data['website'] != '')
              @php $httpstring = (strlen($user_data['website']) > 33) ? substr($user_data['website'],0,33).'...' : $user_data['website'];
                  $websitearray = explode("://", $httpstring);

                  $string = $httpstring;
                  if (count($websitearray) > 1) {
                    $string = $websitearray[1];
                  }
              @endphp
                <a href="{{ $user_data['website'] }}">
                  <h5 class="mb-2"><span class="font-weight-semibold">{{$string}}</span></h5>
                </a>
              @endif
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <div class="skill_set skills">
              <h3>SKILLS</h3>
              <div>
                @foreach($categories as $index => $category)
                  @if ($index > 5) @continue @endif
                  <button style="background: #2ebcae; border: none; margin-bottom:10px;" class="btn btn-primary">{{ $category->name }}</button>
                  <span class="badge badge-light">{{ $category->builds_count }}</span>
                  <br>
                @endforeach
              </div>
            </div>
          </div>
          @if (isset($buildSuccessRate))
            <div class="col-md-12 mb-4 text-center">
              @php
                $color = 'blue';

                if ($buildSuccessRate < 50) {
                  $color = 'orange';
                }

                if ($buildSuccessRate >= 50 && $buildSuccessRate < 80) {
                  $color = 'green';
                }
              @endphp

              <div class="c100 big {{ "p{$buildSuccessRate}" }} {{ $color }}" style="margin: auto; margin-top: 100px;">
                <div class="meta">
                  <span class="text-dark font-weight-bold">{{ $buildSuccessRate }}%</span>
                  <span class="text-gray" style="font-size: 20px;">Verified</span>
                  <p></p>
                  <span class="text-dark" style="font-size: 30px;">{{ $approvedBuilds }}/{{ $totalBuilds }}</span>
                </div>
                <div class="slice">
                  <div class="bar"></div>
                  <div class="fill"></div>
                </div>
              </div>
            </div>
          @endif
          <style type="text/css">
            .common_padding{
                padding: 20px;

              }
          </style>
          <div class="row" style="margin-top: 20px;">
            <div class="col-4 text-center">
              <p><button class="btn btn-success">{{ $user_data['google_reviews_count']}}</button></p>
              <p> <img src="{{ asset('images/google_icon.png') }}" alt="" width="50"></p>
              <p>Reviews</p>
            </div>
            <div class="col-4 text-center">
              <p><button class="btn btn-success">{{ $user_data['hard_work']}}</button></p>
              <p> <img src="{{ asset('images/recent_views.png') }}" alt="" width="50"></p>
              <p>Views</p>
            </div>
            <div class="col-4 text-center">
              <p><button class="btn btn-success">{{ $user_data['fb_share_count']}}</button></p>
              <p> <img src="{{ asset('images/fb_icon.png') }}" alt="" width="50"></p>
              <p>Shares</p>
            </div>
          </div>
           <!--  <div class="row common_padding">
                <div class="badges col-lg-4">
                  <i style="font-size:30px; color: #2EBCAF; background-color: #E1EFF3; padding: 20px; border-radius:40px;" class="fas fa-medal"></i>
                </div>
               <div class="hardWorker col-lg-8" style="font-size: 28px; font-weight: bold;">
                <h6 class="font-weight-bold">Hard-Worker</h6>
                <p>{{ $user_data['hard_work']}} Recent Views</p>
              </div>
            </div> -->
          <div class="col-md-12">
            <div class="reference-layer" style="height: 720px; margin-top: 50px;">
              <div style="position: relative">
                <h4 class="text-center" style="font-size: 24px; color: blue;">
                <img src="{{ asset('images/verified.png') }}" alt="" width="50">
                Verified References <font style="color: #000000;">{{count($testinomials_data['employee'])}}</font></h4>
{{--                @if(count($testinomials_data['employee']) > 3)--}}


{{--                @endif--}}
              <!-- references list modal -->
                <div id="reference-list" class="reference-modal">
                  <div class="reference-list-dialog">
                    <div class="list-container">
                      <h1 class="reference-list-item" style="display: block">References</h1>
                      <span class="close" id="reference-close" style="color: black">&times;</span>
                      <div class="reference-list-item-container">
                        @foreach($testinomials_data['employee'] as $testinomial)
                          <div class="reference-list-item">
                            <div class="reference-image-container">
                              @if($testinomial['image'] != '')
                                
                              @else
                               
                              @endif
                            </div>
                            <div class="reference-list-item-middle">
                              <div>{{$testinomial['full_name']}}</div>
                              <a href='mailto:{{$testinomial['email']}}'>{{$testinomial['email']}}</a>
                              <a href='tel:{{$testinomial['phone_number']}}'>{{$testinomial['phone_number']}}</a>
                            </div>
                            <div class="reference-list-item-right">
                              {{-- <div class="rejected reference-action" id="{{$testinomial['id']}}-{{$user_data['id']}}-0">
                                <div><b>Rejected: </b><span>{{$testinomial['rejected_count']}}</span></div>
                              </div> --}}
                              <div class="approved reference-action" id="{{$testinomial['id']}}-{{$user_data['id']}}-1">
                                <div><b>Verified: </b><span>{{$testinomial['approved_count']}}</span></div>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>

              </div>

              <p>{{$user_data['references']}}</p>

              @if(!empty($testinomials_data['employee']))
                @php
                  $testimonials = collect($testinomials_data['employee'])->sortByDesc('approved_count');
                @endphp
                <div class="row testinomial-layer">

                  <div class="col-md-12">
                    <div id="testinomila-carousel" class="d-flex flex-wrap">
                      @foreach($testimonials as $testinomial)
                        @if ($loop->index > 16)
                          @break
                        @endif
                        <div class="item text-center" style="width: 100px; padding: 10px;">
                          <a href="javascript:void(0);" role="button" data-toggle="popover" data-placement="top" data-html="true" data-trigger="focus" title="{{$testinomial['full_name']}}" data-content="<div></div><a href='mailto:{{$testinomial['email']}}'>{{$testinomial['email']}}</a></div><div><a href='tel:{{$testinomial['phone_number']}}'>{{$testinomial['phone_number']}}</a></div>" data-container="body">
                            @if($testinomial['image'] != '')
                              
                            @else
                            
                            @endif
                          </a>
                          <div>{{$testinomial['full_name']}}</div>
                          <div class="row">
                            {{-- <div class="col-md-6 rejected" id="{{$testinomial['id']}}/{{$user_data['id']}}/0">
                             <div><b>Rejected</b></div>
                             <div>{{$testinomial['rejected_count']}}</div>
                           </div> --}}
                            <div class="col-md-12 approved" id="{{$testinomial['id']}}/{{$user_data['id']}}/1">
                              <div><b>Jobs</b></div>
                              <div>{{$testinomial['approved_count']}}</div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>

                </div>
              @endif
              <div class="row">

                <div class="col-md-12">
                  <div class="owl-carousel testinomial_slider hide" id= "testinomial_buildslider">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="row">
          @if (!empty($buildsByCategory))
            <div class="col-md-12 mb-4 chart-wrapper">
              <div class="card">
                <div class="card-body p-3">
                  <canvas id="challenges-categories"></canvas>
                </div>
              </div>
            </div>
          @endif
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="myplan-layer text-layer">
                <div class="text-layer-rect">
                  <h4>My Objective</h4>
                  <p>{{$user_data['myobjective']}}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="myplan-layer text-layer">
                <div class="text-layer-rect">
                  <h4>Summary of Qualifications</h4>
                  <p>{{$user_data['myplan']}}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <div class="card">
              <div class="pastjob-layer text-layer">
                <h4>Work Experience</h4>
                <p>{{$user_data['past_jobs']}}</p>
              </div>
            </div>
          </div>

          <div class="col-md-12 mb-4 mt-5 pt-5">
            <div class="d-flex flex-column justify-content-end align-items-center mt-5">
              <img width="200" src="{{ route('qrcode') }}?src=<?php $array = explode("?", request()->fullUrl()); echo str_replace(' ', '', $array[0]).'?share=1'; ?>" alt="Live Resume Link">
              <h3>Scan to View Live Resume</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js"></script>
  <script src="{{ asset('js/carousel js/owl.carousel.js') }}"></script>
  <script src="{{ asset('js/carousel js/owl.support.js') }}"></script>
  <script src="{{ asset('js/carousel js/owl.navigation.js') }}"></script>
  <script src="{{ asset('js/carousel js/owl.autoplay.js') }}"></script>

  <script>
    var colorsPallete = [
      '#3F838C', '#7F7E7D', '#D99B5A', '#C56654', '#00B3E6',
      '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
      '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
      '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
      '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
      '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
      '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
      '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
      '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
      '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'
    ];

    var themeColor = '#2a82a0';

    var chartOptions = {
      maintainAspectRatio: false,
      animation: {
        duration: 3000,
      },
      legend: {
        display: false,
      },
      scales: {
        xAxes: [{
          gridLines: {
            display: false,
          },
          ticks: {
            fontSize: 12,
            fontWeight: 'bold',
            beginAtZero: true,
            autoSkip: false,
            maxRotation: document.body.clientWidth < 767 ? 90 : 0,
            minRotation: document.body.clientWidth < 767 ? 45 : 0,
          }
        }],
        yAxes: [{
          gridLines: {
            display: true,
          },
          ticks: {
            fontSize: 12,
            fontWeight: 'bold',
            beginAtZero: true,
            suggestedMin: 0,
            precision: 0, // Available in Chart.js 2.7.3 and above
            callback: function(value, index, values) {
              if (Math.floor(value) === value) {
                return value;
              }
            },
            // suggestedMax: 200,
            // stepSize: 5,
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function(tooltipItem, data) {
            const { label, value } = tooltipItem;
            const { datasets: [dataset] } = data;

            let counts = [];

            if (dataset.challengesBySubcategory) {
              let subcategories = Object.keys(dataset.challengesBySubcategory[label]);

              for (let i = 0; i < subcategories.length; i++) {
                if (i >= 8) {
                  counts.push(`+${subcategories.length - i + 1} More Subcategories...`);
                  break;
                }

                let subcategory = subcategories[i];

                let count = dataset.challengesBySubcategory[label][subcategory].length;

                if (typeof subcategory === 'string' && typeof count !== 'undefined') {
                  counts.push(subcategory + ": " + count);
                }
              }
            }

            let i, j, lines = [], chunk = 3;
            for (i = 0, j = counts.length; i < j; i += chunk) {
              lines.push(counts.slice(i,i+chunk).join(', '));
            }

            lines.unshift("Total: " + value)

            return lines;
          }
        }
      }
    };

    function initChart({ canvas, type, dataset, options = null, showSubCategory = false }) {
      if (!canvas) {
        return;
      }

      let labels = Object.keys(dataset);

      let dataPoints = labels.map(function (item, index) {
        if (showSubCategory) {
          return _.flatten(Object.values(dataset[item])).length;
        }
        return dataset[item].length;
      });

      let colors = labels.map(function (item, index) {
        return colorsPallete[index];
      });

      if (type == 'bar') {
        // Set the chart width
        // setBarChartWidth(canvas, labels.length);
      }

      let data = {
        labels: labels,
        datasets: [
          {
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 1.5,
            hoverBackgroundColor: themeColor,
            hoverBorderColor: themeColor,
            data: dataPoints,
            showSubCategory: showSubCategory, // Flag to display sub-categories on hover
            challengesBySubcategory: dataset, // Set reference to original data (to be accessed on hover)
          }
        ]
      };

      return new Chart(canvas, {
        type: type,
        data: data,
        options: options || chartOptions,
      });
    }

    function setBarChartWidth(element, bars) {
      let currentWidth = $(element).parent('.card-body').css('width');
      currentWidth = parseInt(currentWidth.substring(0, currentWidth.length - 2));

      let calculatedWith = bars * 120;

      console.log(currentWidth, calculatedWith);

      if (currentWidth < calculatedWith) {
        console.log('setting width', calculatedWith, element);
        $(element).parent('.card-body').css('width', calculatedWith + 'px');
      }
    }

    function initChallengesByCategoryChart() {
      let canvas = document.getElementById('challenges-categories');
      let challengesByCategory = @json($buildsByCategory ?? [], JSON_PRETTY_PRINT);

      initChart({
        canvas: canvas,
        type: 'bar',
        dataset: challengesByCategory,
        showSubCategory: true,
      });
    }

    $(document).ready(function () {
      try {
        initChallengesByCategoryChart();
      } catch (e) {
        console.log(e);

        // Print the error on browser if print/export is requested.
        @if (request('print'))
          $('#error').append((e));
        @endif
      }
    });
  </script>
@endpush
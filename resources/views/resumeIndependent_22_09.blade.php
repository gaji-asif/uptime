<head>

    <link href="{{ asset('css/view/employeeview.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/view/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/portfolio.css') }}" rel="stylesheet">
    <link href="{{ asset('css/circle.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lazy-load-images.min.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style type="text/css">



    .skill_set {
      margin: 25px 25px 25px 25px;
    }
    .skill_set > div {
      min-height: 200px;
      max-height: 300px;
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
        margin-bottom: 20px;
    }
    .personal_info .detail {
        margin-bottom: 5px;
        font-size: 16px;
        font-weight: 600;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
    .personal_info .detail span {
        padding-right: 15px;
        color: #2ebcae;
    }

    .profile-pic-layer {
        text-align: center;
    }
    .search-input ::placeholder{
        color: #4a4a4a;
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
      .common_padding{
        margin: 0 auto;
        text-align: center;
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
      z-index: 10; /* Sit on top */
      /* padding: 20px 20px 0px 20px; Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgba(231,233,238,1); /* Black w/ opacity */
    }
    .list-container {
      padding: 100px 10px 80px 10px;
    }
    .reference-image-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .reference-list-item-container {
      min-height: calc(100vh - 180px);
      /*overflow-y: auto;*/
    }
    .reference-list-item {
      display: flex;
      margin-bottom: 12px;
      justify-content: center;
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
      margin-left: 8px;
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
      z-index: 10; /* Sit on top */
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
      right: 25px;
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

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }
    }

    .align-items-center{
      display: flex;
      justify-content: center;
      align-items: center;
    }


    /* .meta:hover #status, */
    .meta #details, .meta.show-details #status {
      display: none;
    }

    /*.meta:hover #details,*/
    .meta.show-details #details{
      display: inline-block;
    }

    #bookModal, #shareInputNameModal{
      padding: 0px
    }
    #bookModal .my-modal-content, #shareInputNameModal .my-modal-content{
      position: relative;
      max-width: 500px;
      max-height: 435px;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
    #chooseOption{
      margin-top: 12%;
    }
    @media only screen and (max-width: 1000px){
      #chooseOption{
        margin-top: 13%;
      }
    }
    @media only screen and (max-width: 600px){
      #chooseOption{
        margin-top: 18%;
      }
    }
    @media only screen and (max-width: 500px){
      #chooseOption{
        margin-top: 19%;
      }
    }
    @media (max-width: 1000px){
      .col-xs-12{
        max-width: 100%;
        flex: 0 0 100%;
      }
    }
    .common_padding{
      padding: 20px;

    }

     /* moin */
  

  /* body {font-family: Verdana, sans-serif;} */
  /* .mySlides {display: none;} */
  /* img {vertical-align: middle;} */

  /* Slideshow container */
  .slideshow-container {
    max-width: 1000px;
    position: relative;
    margin: auto;
  }

 .text {
    color: #ffffff;
    font-size: 19px;
    padding: 8px 12px;
    position: absolute;
    bottom: 8px;
   /* text-align: center;
    margin: 0 auto;
    background-color: #000;
    opacity: .70;*/
}

  /* Number text (1/3 etc) */
  .numbertext {
    color: #f2f2f2;
    font-size: 12px;
    padding: 8px 12px;
    position: absolute;
    top: 0;
  }

  /* The dots/bullets/indicators */



  /* Fading animation */
  .fades {
    -webkit-animation-name: fade;
    -webkit-animation-duration: 1.5s;
    animation-name: fade;
    animation-duration: 1.5s;
  }

  @-webkit-keyframes fades {
    from {opacity: .4} 
    to {opacity: 1}
  }

  @keyframes fades {
    from {opacity: .4} 
    to {opacity: 1}
  }

  /* On smaller screens, decrease text size */
  @media only screen and (max-width: 300px) {
    .text {font-size: 11px}
  }
  .div-img {
    height: 350px;
   /* width: 100%;*/
   /* object-fit: cover;
    object-position: 50% 0%;*/
    border-radius: 20px;
  }

  .slider-image{
    display: inline-block;
  }
   

    </style>
  </head>
    <div class="mycontainer">
    <div class="">
    <div class = "overlay">
        <button class="purchase_btn"><h3>Buy Now</h3></button>
    </div>
    <!-- Circle progress bar -->

      <!-- top banner  -->

        <input type="text" id="emp_id" value="{{$user_data['id']}}" hidden>
{{--        <input type = "text" id = "categorynames" value="{{$user_data['categorynames']}}" hidden>--}}
{{--        <input type = "text" id = "count_data" value = "{{$user_data['count_data']}}" hidden>--}}
        <input type = "text" class = "startmonthinput" value = "{{$user_data['startmonth']}}" hidden>
        <input type = "text" class = "endmonthinput" value = "{{$user_data['endmonth']}}" hidden>

      <!-- end of top banner -->
    <!-- report text and line  -->
        <div class="row report-layer">
          <div class="row">
            <div class="col-md-3 col-xs-3 my-col-3">
              <hr class="line">
            </div>
            <div class="col-md-6 col-xs-6 my-col-6 align-items-center">
              <div class="report-title">Uptime Verified</div>
            </div>
            <div class="col-md-3 col-xs-3 my-col-3">
              <hr class="line">

            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
                <div class="row">
                  <div class="h-col-2">
                     <button type="submit" class="btn form-control print-button" id="calendar-btn" style="background-color:#89dceb;">
                      <i class="fas fa-calendar-alt" id="calendar-i" style="margin: auto;"></i>
                    </button>
                  </div>
                  <div class="h-col-x6">
                     <input type="text" class="form-control" name="daterange" readonly="readonly" value="{{$user_data['startmonth']}} - {{$user_data['endmonth']}}" />

                      <input type="hidden" value="{{$user_data['startmonth']}}" class="form-control" id="date_timepicker_start">
                      <input type="hidden" value="{{$user_data['endmonth']}}" class="form-control" id="date_timepicker_end">
                      <input type="hidden" value="{{$user_data['hover_txt']}}" id="hover_txt">
                      <input type="hidden" value="{{$user_data['startmonth']}}" class="form-control" id="last_month">
                      <input type="hidden" value="{{$user_data['current_month']}}" class="form-control" id="current_month">
                  </div>

                  <div class="h-col-2">
                    {{-- <form method="get" id="form_id" action=""> --}}
                    <form method="get" id="form_id" action="{{route('export')}}">
                    {{-- <form method="get" action="{{route('master.makepdfbydateIndependent')}}"> --}}
                      @csrf
                      @if (auth('admin')->check())
                        <input type="hidden" name="guard" value="admin">
                      @endif
                       <input type="hidden" name="url" id="current_url" value="{{ request()->fullUrl() }}">
                       <input type="hidden" value="{{$user_data['id']}}" name="employee_id" />
                       <button type="submit" id="print-button-id" class="btn form-control print-button-print" style="background-color:#89dceb;margin:auto;"><i class="fas fa-print"></i></button>
                    </form>
                  </div>
                </div>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
              <div class="row">
                  <div class="h-col-6">

                          <form method="get" action="{{route('resume.employeeportfolioIndependent.employeeportfolioIndependent.dateindexIndependent',['id'=>$user_data['id'],'startdate'=>$user_data['startmonth'],'enddate'=>$user_data['endmonth']])}}">
                            <div class="row">
                              <div class="h-col-9 search-input" style="padding-right: 0;padding-left: 0;">
                                @if(!empty(app('request')->input('search')))
                                <input type="text" placeholder="Search for specific job" class="form-control" value="{{app('request')->input('search')}}" id="search_text" name="search" >
                                @else
                                <input type="text" placeholder="Search for specific job" class="form-control" value="" id="search_text" name="search" >
                                @endif
                              </div>

                              <div class="h-col-3" style="padding-right: 0;padding-left: 0;">
                                <button class="form-control" style="border:0px;background-color: #dddddd" type="submit">
                                  <span><i class="fas fa-search" style="margin:auto;"></i></span>
                                </button>
                              </div>
                            </div>
                          </form>
                    </div>
                    {{-- <div class="h-col-6">
                      <div class="row">
                         <div class="col-md-12" style="padding: 0px;">
                            <select class="form-control search_category" style="height:calc(2rem + 2px);">
                              <option {{$search_cat == "" ? 'selected' :'' }} value="">Show All</option>
                              @foreach($search_categories as $cat)
                                  <option {{$search_cat == $cat->category_name ? 'selected':''}} value="{{$cat->category_name}}">{{$cat->category_name}}</option>
                              @endforeach
                            </select>
                          </div>
                      </div>
                  </div>     --}}
              </div>
            </div>
          </div>
        </div>

    <!-- end of reort text and line  -->
    <!-- main-introduce layer  -->
    <div class="row margin_top_25">
        <!-- The Modal -->
        <div id="pModal" class="p-modal">
          <!-- The Close Button -->
          <span class="close">&times;</span>
          <!-- Modal Content (The Image) -->
          <img class="modal-content" id="img01">
          <!-- Modal Caption (Image Text) -->
          <div id="caption"></div>
        </div>

        <div class="col-sm-3 col-xs-12">
            <div class="profile-pic-layer">
                @if($user_data['image'] != '')
                    <img id="profileImage" class="img-circle" src="{{$user_data['image']}}" alt="Profile Image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                @else
                    <img class="img-circle" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                @endif
            </div>
            <div class="detail text-padding mt-3" style="text-align:center">
              <button class="btn btn-primary" style="background: #2ebcae; border: none; margin-bottom:10px;" onclick="bookNow('<?php echo $user_data['phone_number'];?>')">
                  <i class="fas fa-phone"></i>
                Schedule Now
              </button>
            </div>
            <div class="personal_info">
              <div class="detail text-padding">
              <span><i class="fas fa-envelope"></i></span>
              <a href="mailto:<?php echo $user_data['email'];?>">{{$user_data['email']}}</a>
            </div>
            @if(isset($user_data['website']) && $user_data['website'] != '')
              @php $httpstring = (strlen($user_data['website']) > 33) ? substr($user_data['website'],0,33).'...' : $user_data['website'];

                  $websitearray = explode("://", $httpstring);

                  $string = $httpstring;
                  if (count($websitearray) > 1) {
                    $string = $websitearray[1];

                    $stringarray = explode("/", $string);
                    if (count($stringarray) > 0) {
                      $string = $stringarray[0];
                    }
                  }
              @endphp
              <div class="detail text-padding"><span><img src="{{ asset('images/website_link.png') }}" alt="" width="20" style="margin-left: -3px;"></span><a href="{{$user_data['website']}}">{{$string}}</a></div>
            @endif
            @if (!empty($user_data['business_url']))
              <div class="detail text-padding"><span><img src="{{ asset('images/google_business.png') }}" alt="" width="25" style="margin-left: -5px;"></span><a href="{{$user_data['business_url']}}">{{strtolower(\Illuminate\Support\Str::limit($user_data['business_url'], 20))}}</a></div>
            @endif
          </div>

            <div class="skill_set skills">
              <h3>SKILLS</h3>
              <div>
                @foreach($categories as $index => $category)
                  <button style="background: #2ebcae; border: none; margin-bottom:10px;" class="btn btn-primary skillCategories" id="SkillBtn{{ $index }}" category={{ $category->id }}>{{ $category->name }}</button>
                  <span class="badge badge-light" style="margin-top: 10px">{{ $category->builds_count }}</span>
                  <br>
                @endforeach

{{--                 @php $count_data_array = explode(',', $user_data['count_data']); @endphp--}}

{{--                  @php $categorynames = explode(',', $user_data['categorynames']); @endphp--}}

{{--                  @php $category_ids = explode(',', $user_data['category_ids']); @endphp--}}

{{--                  @for ($i = 0; $i < count($categorynames); $i++)--}}
{{--                       @if ($categorynames[$i] !=='' && $count_data_array[$i] !== '0')--}}

{{--                          <br>--}}
{{--                          <button style="background: #2ebcae; border: none; margin-bottom:10px;" class="btn btn-primary skillCategories" id = "SkillBtn{{$i}}" category={{$category_ids[$i]}}>{{$categorynames[$i]}}--}}

{{--                          </button>--}}

{{--                          <span style="margin-top: 10px" class="badge badge-light">{{$count_data_array[$i]}}</span>--}}

{{--                        @endif--}}
{{--                  @endfor--}}



              </div>
            </div>
          @if (isset($buildSuccessRate))
            <div class="row">
              <div class="col-md-12 text-center">
                @php
                    $emp = \App\Employee::find($user_data['id']);
                    $profileStatus = $emp->getProfileStatus();

                    $color = 'blue';

                    if ($buildSuccessRate < 50) {
                      $color = 'orange';
                    }

                    if ($buildSuccessRate >= 50 && $buildSuccessRate < 80) {
                      $color = 'green';
                    }
                @endphp

                <div class="c100 {{ "p{$buildSuccessRate}" }} {{ $color }}" style="margin: auto">
                  <div class="meta">
                    <span id="status" class="text-dark font-weight-bold">{{ $buildSuccessRate }}%</span>
                    <small id="details" class="text-dark font-weight-semibold">{{ $profileStatus->approved . '/' . $profileStatus->total }}</small>
                    <span class="text-gray" style="font-size: 20px;">Verified</span>
                  </div>
                  <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
        <div class="col-sm-9 col-xs-12">
            <div class="personal_objective">
                <div class="person_name">{{$user_data['name']}}</div>
                <div class="objective_title">My Objective</div>
                <div class="objective_content">{{$user_data['myobjective']}}</div>
            </div>
          <div class="row">
            <div class="col-lg-1"></div>
            
            <div class="col-lg-10">
              
              <div class="d-flex justify-content-around align-items-center my-3 py-2">

                <div class="row">
                  <div class="col-3">
                    <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $emp->google_reviews_count }}</span>
                    <img src="{{ asset('images/google_icon.png') }}" alt="" width="50">
                    <strong class="text-dark text-bold" style="vertical-align: middle;">Reviews</strong>
                  </div>
                  <div class="col-6" style="text-align: center; margin:0 auto;">
                     <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $user_data['hard_work']}}</span>
                    <img style="width: 16%;" src="{{ asset('images/recent_views.png') }}" alt="" width="50">
                    <strong class="text-dark text-bold" style="vertical-align: middle;"> Recent Reviews</strong>
                  </div>
                  <div class="col-3">
                     <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $emp->fb_share_count }}</span>
                  <img src="{{ asset('images/fb_icon.png') }}" alt="" width="40">
                  <strong class="text-dark text-bold" style="vertical-align: middle;">Shares</strong>
                  </div>
                </div>
                
                <!--   <h5>
                    <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $emp->google_reviews_count }}</span>
                    <img src="{{ asset('images/google_icon.png') }}" alt="" width="50">
                    <strong class="text-dark text-bold" style="vertical-align: middle;">Reviews</strong>
                  </h5>


                   <h5>
                    <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $user_data['hard_work']}}</span>
                    <img src="{{ asset('images/google_icon.png') }}" alt="" width="50">
                    <strong class="text-dark text-bold" style="vertical-align: middle;"> Recent Reviews</strong>
                  </h5>
                
                <h5>
                  <span class="badge badge-light text-white" style="float: unset; background: #2ebcae; font-size: 1rem;">{{ $emp->fb_share_count }}</span>
                  <img src="{{ asset('images/fb_icon.png') }}" alt="" width="40">
                  <strong class="text-dark text-bold" style="vertical-align: middle;">Shares</strong>
                </h5> -->
              </div>
            </div>
          </div>

            <div class="skill_set skills_mobile">
              <h3>SKILLS</h3>
              <div>
                @foreach($categories as $index => $category)
                  <button style="background: #2ebcae; border: none; margin-bottom:10px;" class="btn btn-primary skillCategories" id="SkillBtn{{ $index }}" category={{ $category->id }}>{{ $category->name }}</button>
                  <span class="badge badge-light" style="margin-top: 10px">{{ $category->builds_count }}</span>
                  <br>
                @endforeach
{{--                  @php $count_data_array = explode(',', $user_data['count_data']); @endphp--}}

{{--                  @php $categorynames = explode(',', $user_data['categorynames']); @endphp--}}

{{--                  @php $category_ids = explode(',', $user_data['category_ids']); @endphp--}}

{{--                  @for ($i = 0; $i < count($categorynames); $i++)--}}
{{--                    @if ($categorynames[$i] !=='' && $count_data_array[$i] !== '0')--}}

{{--                      <br>--}}
{{--                      <button style="background: #2ebcae; border: none; margin-bottom:10px;" class="btn btn-primary skillCategories" id = "SkillBtn{{$i}}" category={{$category_ids[$i]}}>{{$categorynames[$i]}}--}}

{{--                      </button>--}}

{{--                      <span style="margin-top: 10px" class="badge badge-light">{{$count_data_array[$i]}}</span>--}}

{{--                    @endif--}}
{{--                  @endfor--}}
              </div>
            </div>

            <div class="profile_detail_content">

                <div class="row second-section">
                    <div class="col-md-6 second-layer" style="height: auto; display: none;">

                        <div class="row training-slider-layer">

                         <div class="col-md-12">
                           <div id="image-position" style="height: 1px;"></div>
                          <h4 id="image-title">Select a skill to see photos</h4>
                            <div class="row" style="display: none">
                              <div id="training-slider" class="owl-carousel circleslider">
                                @foreach($categories as $index => $category)
                                  <div class="slider-item">
                                    <div class="circle {{ Uptime::getCategoryColor($index) }}" id="{{ $category->id }}">
                                      <img style ="width:30%" src="{{ \Storage::disk('s3')->url('images/employeeview/'. Uptime::getCategoryImage(0, $index)) }}" alt="Coaching image" class="sales-icon">
                                      <div class="sales-number sales-padding">{{ $category->builds_count }}</div>
                                      <div class="circle-text ">{{ $category->name }}</div>
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                             </div>

                           </div>

                          </div>

                          <div class="row">

                               <div class="col-md-12">
                                     <div class="owl-carousel buildslider" id="Categoriesbuildslider">
                                     </div>
                               </div>

                          </div>

                    </div>

                    <!-- moin -->
                     <div class="col-md-6 display-layer" style="height: auto; ">
                    <div class="slideshow-container buildsliders">

                      @if(isset($images))
                        @foreach($images as $image)
                          <div class="mySlides fades mItem">
                            <span id="{{$image->id}}-{{$image->subcategory}}" class="slider-image">
                              <a href="" data-srcset="https://uptime-prod.s3-ap-southeast-1.amazonaws.com/images/build/{{$image->image}}" data-sizes=""  style="cursor: default;">
                                <img class="div-img" srcset="https://uptime-prod.s3-ap-southeast-1.amazonaws.com/images/build/{{$image->image}}" sizes="" src="{{ request()->fullUrl() }}" class="" alt=" challenge_image" style="height: 350px">
                                <div class="text" style="text-align: left; margin: 0 auto; margin-left: 7px; display: table-cell;">
                                  <font style="text-transform: uppercase; font-size: 30px; font-weight: bold; font-family: inherit; -webkit-text-stroke:1px #000000;" 
                                  @if(isset($image->subCategory))
                                 <!--  {{date('F',strtotime($image->created_at)).' '.date('d',strtotime($image->created_at)).'th '.date('Y',strtotime($image->created_at))}} -->

                                  {{date('M d', strtotime($image->created_at)) .', '.date('Y', strtotime($image->created_at))}}


                                  @endif
                                </font>
                                  <br>
                                  <font style="font-weight: bold; -webkit-text-stroke: 1px #00000;">@if(isset($image->subCategory)){{$image->subCategory->subcategory_name}}@endif </font>                                
                                </div>
                              </a>
                            </span>
                          </div>
                        @endforeach
                      @endif
                      {{-- <img srcset="https://uptime-prod.s3-ap-southeast-1.amazonaws.com/images/build/575_16177561902.png" sizes="" src="{{ request()->fullUrl() }}" class="" alt=" challenge_image"> --}}
                      {{-- <div style="flex-basis: 25%;padding:2px" class="mItem">
                        <span id="18098-361" class="slider-image">
                          <a href="" data-srcset="https://uptime-prod.s3-ap-southeast-1.amazonaws.com/images/build/575_16177568892.png" data-sizes="" class="lazy-load" style="cursor: default;">
                            <img srcset="https://uptime-prod.s3-ap-southeast-1.amazonaws.com/images/build/575_16177568892.png" sizes="" src="http://localhost/uptime/employeeportfolioIndependent/575/10-28-2019/04-07-2021" class="" alt="challenge_image">
                          </a>
                        </span>
                      </div> --}}
                      
                      
                      </div>
                     
                  </div>
                    <div class="col-md-6">

                        <!-- References  -->
                          <div class="reference-layer">
                            <div style="position: relative">
{{--                               <h4>{{count($testinomials_data['employee'])}} Promoters</h4>--}}
                               <h4 class="text-danger pr-4"><img src="{{ asset('images/verified_clients.png') }}" alt="" width="30"> Verified References ({{ count($testinomials_data['employee'])}})</h4>

                               @if(count($testinomials_data['employee']) > 3)
                               <div class="reference-more" id="reference-more">View all</div>
                               @endif
                               <!-- references list modal -->
                               <div id="reference-list" class="reference-modal">
                                 <div class="list-container">
                                   <h2 class="reference-list-item" style="display: block; margin-bottom: 20px">Promoters</h2>
                                   <span class="close" id="reference-close" style="color: black">&times;</span>
                                   <div class="reference-list-item-container">
                                   @foreach($testinomials_data['employee'] as $testinomial)
                                     <div class="reference-list-item">
                                       <div class="reference-image-container">
{{--                                         @if($testinomial['image'] != '')--}}
                                             
{{--                                         @else--}}

{{--                                         @endif--}}
                                       </div>
                                       <div class="reference-list-item-middle">
                                         <div style="color: ">{{$testinomial['full_name']}}</div>
                                         <a style="width: fit-content" href='#' onclick="contact('{{ $testinomial['email'] }}')" type="button">Contact</a>
                                       </div>
                                       <div class="reference-list-item-right">
                                         {{-- <div class="rejected reference-action" id="{{$testinomial['id']}}/{{$user_data['id']}}/0">
                                           <div><b>Rejected: </b><span>{{$testinomial['rejected_count']}}</span></div>
                                         </div> --}}
                                         <div class="approved reference-action" id="{{$testinomial['id']}}/{{$user_data['id']}}/1">
                                           <div><br><b>Work Verified: </b><span>{{$testinomial['approved_count']}}</span></div>
                                         </div>
                                       </div>
                                     </div>
                                   @endforeach
                                 </div>
                                 </div>
                               </div>


                             </div>

                             <p>{{$user_data['references']}}</p>

                             @if(!empty($testinomials_data['employee']))
                             <div class="row testinomial-layer">

                               <div class="col-md-12">
                                 <div id="" class="">
                                    @foreach($testinomials_data['employee'] as $testinomial)
                                     @if ($loop->index > 5)
                                       @break
                                     @endif
                                    
                                      <div class="item" style="text-align: center; width: 33%; float: left; margin-bottom: 15px;">
                                        <a href="javascript:void(0);" role="button" data-toggle="popover" data-placement="top" data-html="true" data-trigger="focus" title="{{$testinomial['full_name']}}" data-content="<div></div><a href='mailto:{{$testinomial['email']}}'>{{$testinomial['email']}}</a></div><div>(xxx) xxx-x{{substr($user_data['phone_number'], -3)}}</div>" data-container="body">
{{--                                        @if($testinomial['image'] != '')--}}
                                            <img style="height: 50px;width: 50px;margin: auto; display: none;" class="duels-image" src="{{ $testinomial['image'] }}" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
{{--                                        @else--}}
{{--                                           <img style="height: 50px;width: 50px;margin: auto; display: none;" class="duels-image" src="{{ asset('images/avatar.png') }}" alt="Profile image">--}}
{{--                                        @endif--}}
                                         </a>
                                        <div><h6 style="font-size: 13px;">{{$testinomial['full_name']}}</h6></div>
                                        <div class="row">
                                         {{-- <div class="col-md-6 rejected" id="{{$testinomial['id']}}/{{$user_data['id']}}/0">
                                           <div><b>Rejected</b></div>
                                           <div>{{$testinomial['rejected_count']}}</div>
                                         </div> --}}
                                         <div class="col-md-12 approved" id="{{$testinomial['id']}}/{{$user_data['id']}}/1">
                                           <div><b>Work Verified</b></div>
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

                    <!-- Summary of Qualifications -->
                      <div class="my-plan">
                         <div class="myplan-layer text-layer">
                            <div class="text-layer-rect">
                              <h4>Summary of Qualifications</h4>
                               <p>{{$user_data['myplan']}}</p>
                           </div>
                         </div>
                    </div>
                   <!-- Work Experience  -->
                    <div class="past-jobs">
                      <div class="pastjob-layer text-layer">
                      <h4>Work Experience</h4>
                        <p>{{$user_data['past_jobs']}}</p>
                      </div>
                    </div>

                 </div>
                </div>


            </div>


        </div>
    </div>

    <!-- Trigger the Modal for QR Code selection -->
    <div id="qrModal" class="modal" style="padding-left: 0%;padding-top: 30%;z-index: 10000;">
      <div id="qrclose" style="margin-top: 10px;">
        <span class="close">&times;</span>
     </div>
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
             <img src="{{ url('/api/qrcode') . '?src=' . urlencode(url()->current()) }}" style="width: 100%;height: auto;margin: auto;" >

          </div>

          <div class="col-md-2">

          </div>

        </div>

    </div>

    <!-- Trigger the Modal -->
    <div id="expandModal" class="modal" style="padding-left: 0%;padding-top: 0%;z-index: 10000;">
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
             <img src="" style="width: 100%;height: auto;margin: auto;" id="expand_image">
             <div class="close-expand-button">
                <span class="close">&times;</span>
             </div>
          </div>

          <div class="col-md-2">

          </div>

        </div>

    </div>

    <!-- The Modal for selecting the builder  -->
     <div id="myModal" class="modal">
      <span class="close" id="close-myModal-button">&times;</span>
       <div class="my-modal-content row">
           <div class="col-md-7" style="padding:0">
             <div class="row" style="position: relative;text-align: center;">
                <div class="col-md-12" style="padding:0">
                   <div class="right-content">
                        <input type="hidden" id="left-arrow-id" value="0">
                        <input type="hidden" id="right-arrow-id" value="0">
                        <input type="hidden" id="expand-id" value="0">
                        <div class="right_build_image expand-arrow">

                        </div>
                        <div class="arrow-layer left-arrow">
                            <i  class="fas fa-chevron-left"></i>
                         </div>
                        <div class="arrow-layer right-arrow">
                            <i class="fas fa-chevron-right"></i>
                         </div>

                        {{-- <div class="mobile-close-button">
                            <span class="close">&times;</span>
                        </div> --}}
                   </div>
                </div>
             </div>
           </div>
           <div class="col-md-5" style="padding:0">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="description-header">
                           @if($user_data['image'] != '')
                                <img class="modal-desc-img" src="<?php echo $user_data['image'];?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                           @else
                          <img class="modal-desc-img" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                           @endif
                           <div class="desc-intro">
                                  <div class="desc-emp-name">{{$user_data['name']}}</div>
                                 <div class="challenge_check">challenge<img style="width: 8%;height: auto;" src="<?php echo \Storage::disk('s3')->url('images/gray_challenge.png');?>"></div>
                           </div>
                            {{-- <div class="close-button">
                              <span class="close">&times;</span>
                            </div> --}}

                       </div>
                    </div>

                    <div class="row">
                      <div class="col-md-1"></div>
                      <div class="col-md-10">
                        <div style="float: right;">
                          <div class="desc-emp-date desc-text"></div>
                        </div>

                        <div class="row challenge_image_layer" style="margin-top: 1%">

                            </div>
                        <div class="description-content">
                           <div class="row">
                             <div class="col-md-10">
                               <div class="description"></div>
                             </div>
                             <div class="col-md-2">
                              <div class="info-circle">
                                 <i class="fa fa-info-circle"></i>
                              </div>
                             </div>
                           </div>
                       </div>
                       <div class="subcategory-layer">
                            <div class="category row">
                            </div>

                        </div>


                        <h4 id = "batch_full_name" style="font-size: 15px; margin-top: 15px">

                        </h4>

                      <div class="col-md-1"></div>
                    </div>
                  </div>
             </div>
           </div>
    </div>
    </div>
  
    <!-- end of mymodal  -->

    <!-- The Modal for selecting the challenge -->
     <div id="challengeModal" class="modal">

       <div class="my-modal-content row">
           <div class="col-md-7" style="padding:0">
             <div class="row" style="position: relative;text-align: center;">
                <div class="col-md-12" style="padding:0">
                   <div class="right-content">

                        <div class="right_build_image chal-expand-arrow">

                        </div>
                        <div class="arrow-layer chal-left-arrow">
                            <i  class="fas fa-chevron-left"></i>
                         </div>
                        <div class="arrow-layer chal-right-arrow">
                            <i class="fas fa-chevron-right"></i>
                         </div>

                        <div class="challenge-mobile-close-button">
                            <span class="close">&times;</span>
                        </div>
                   </div>
                </div>
             </div>
           </div>
           <div class="col-md-5" style="padding:0">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="description-header">
                           @if($user_data['image'] != '')
                                <img class="modal-desc-img" src="<?php echo $user_data['image'];?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                           @else
                          <img class="modal-desc-img" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                           @endif
                           <div class="desc-intro">
                                  <div class="desc-emp-name">{{$user_data['name']}}</div>
                                 <div class="challenge_check">challenge<img style="width: 8%;height: auto;" src="<?php echo \Storage::disk('s3')->url('images/gray_challenge.png');?>"></div>
                           </div>
                            <div class="challenge-close-button">
                              <span class="close">&times;</span>
                            </div>

                       </div>
                    </div>

                    <div class="row">
                      <div class="col-md-1"></div>
                      <div class="col-md-10">
                        <div style="float: right;">
                          <div class="desc-emp-date desc-text"></div>
                        </div>

                        <div class="row challenge_image_layer" style="margin-top: 1%">

                            </div>
                        <div class="description-content">
                           <div class="row">
                             <div class="col-md-10">
                               <div class="description"></div>
                             </div>
                             <div class="col-md-2">
                              <div class="info-circle">
                                 <i class="fa fa-info-circle"></i>
                              </div>
                             </div>
                           </div>
                       </div>
                       <div class="subcategory-layer">
                            <div class="category row">
                            </div>

                        </div>

                      <div class="col-md-1"></div>
                    </div>
                  </div>
             </div>
           </div>
         </div>
    </div>

     <!-- start of testinomila modal dialog  -->

     <!-- The Modal for selecting the builder and challenge -->
     <div id="testinomialModal" class="modal">
       <span class="close close-testinmial-modal">&times;</span>
       <div class="my-modal-content row">
           <div class="col-md-7" style="padding:0">
             <div class="row" style="position: relative;text-align: center;">
                <div class="col-md-12" style="padding:0">
                   <div class="right-content">

                        <div class="right_build_image testinomial-expand-arrow">

                        </div>
                        <div class="arrow-layer testinomial-left-arrow">
                            <i  class="fas fa-chevron-left"></i>
                         </div>
                        <div class="arrow-layer testinomial-right-arrow">
                            <i class="fas fa-chevron-right"></i>
                         </div>

                        <div class="testinomialmodal-mobile-close-button">
                            {{-- <span class="close">&times;</span>   --}}
                        </div>
                   </div>
                </div>
             </div>
           </div>
           <div class="col-md-5" style="padding:0">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="description-header">
                           @if($user_data['image'] != '')
                              <img class="modal-desc-img" src="<?php echo $user_data['image'];?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                           @else
                              <img class="modal-desc-img" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                           @endif
                           <div class="desc-intro">
                                  <div class="desc-emp-name">{{$user_data['name']}}</div>
                                 <div class="challenge_check">challenge<img style="width: 8%;height: auto;" src="<?php echo \Storage::disk('s3')->url('images/gray_challenge.png');?>"></div>
                           </div>
                            <div class="testinomial-close-button">
                              {{-- <span class="close">&times;</span>   --}}
                            </div>

                       </div>
                    </div>

                    <div class="row">
                      <div class="col-md-1"></div>
                      <div class="col-md-10">
                        <div style="float: right;">
                          <div class="desc-emp-date desc-text"></div>
                        </div>

                        <div class="row challenge_image_layer" style="margin-top: 1%">

                            </div>


                        <div class="description-content">
                           <div class="row">
                             <div class="col-md-10">
                               <div class="description"></div>
                             </div>
                             <div class="col-md-2">
                              <div class="info-circle">
                                 <i class="fa fa-info-circle"></i>
                              </div>
                             </div>
                           </div>
                       </div>
                       <div class="subcategory-layer">
                            <div class="category row">
                            </div>
                        </div>

                      <div class="col-md-1"></div>
                    </div>
                  </div>
             </div>
           </div>
         </div>
    </div>
    <div id="bookModal" class="modal">
      <div class="my-modal-content">
        <div class="col-md-12">
          <div class="modal-header">
            <h4 style="margin:auto">Schedule Now</h4>
            <div id="bookModal-close-button">
              <span class="close" style="color:black;padding:0;">&times;</span>
            </div>
          </div>
          <div class="modal-body" style="text-align: center; height: calc(100% - 55px);">
            <div id="chooseOption">
              <a href="#" onclick="showBookInputTextBox()">
                <h4><i class="fas fa-envelope mr-2"></i> Text {{$user_data['name']}}</h4>
              </a>
              <a href="tel:<?php echo $user_data['phone_number'];?>" style="margin-top: 20px;display:block">
                <h4><i class="fas fa-phone mr-2"></i> Call {{$user_data['name']}}</h4>
              </a>
            </div>
            <div id="smsTextInput">
              <input type="text" id="first_name_book" class="form-control" placeholder="First Name">
              <input class="mt-3 form-control" type="text" id="last_name_book" placeholder="Last Name">
              <input class="mt-3 form-control" type="text" id="phone_book" placeholder="Phone number">
              <textarea name="" id="bookSmsText" rows="10" placeholder="Input message here" class="form-control mt-3" style="border-color:#333">
              </textarea>
              <button class="btn btn-primary" style="float: right; margin:10px 0px;background: #2ebcae; border: none;" onclick="sendSMS()">Send</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="shareInputNameModal" class="modal">
      <div class="my-modal-content" style="height: 220px">
        <div class="col-md-12">
          <div class="modal-header">
            <h4 style="margin:auto">To view Input name</h4>
          </div>
          <div class="modal-body" style="text-align: center;">
            <input type="text" id="first_name" class="form-control" placeholder="First Name">
            <input class="mt-3 form-control" type="text" id="last_name" placeholder="Last Name">
            <button class="btn btn-primary" style="float: right; margin:10px 0px;background: #2ebcae; border: none;" onclick="sendViewerName()">OK</button>
          </div>
        </div>
      </div>
    </div>

    <div id="contactModal" class="modal">
      <div class="my-modal-content" style="height: 360px">
        <div class="col-md-12">
          <div class="modal-header">
            <h4 style="margin:auto">Contact</h4>
            <div id="contactModal-close-button">
              <span class="close" style="color:black;padding:0;">&times;</span>
            </div>
          </div>
          <div class="modal-body" style="text-align: center;">
            <input type="text" id="first_name_contact" class="form-control" placeholder="First Name">
            <input class="mt-3 form-control" type="text" id="last_name_contact" placeholder="Last Name">
            <input class="mt-3 form-control" type="text" id="phone_contact" placeholder="Phone number">
            <textarea id="text_contact" rows="5" placeholder="Input message here" class="form-control mt-3" style="border-color:#333"></textarea>
            <button class="btn btn-primary" style="float: right; margin:10px 0px;background: #2ebcae; border: none;" onclick="sendContactText()">Send</button>
          </div>
        </div>
      </div>
    </div>

     <!-- end of testinomiail modal dialog -->
    </div>
    <!-- end of main  -->
    </div>
    <!-- end of container  -->


     <!-- <script src="{{ asset('js/mdb.min.js') }}"></script>-->
     <script src="{{ asset('js/bootstrap.js') }}"></script>
     <script src="{{ asset('js/employeeview.js') }}"></script>
     <!-- <script src="{{ asset('js/portfolio.js') }}"></script>  -->
     <script type="text/javascript">

        var datecheck_flag = 1;
        $('.info-circle').click(function(){
          datecheck_flag = datecheck_flag == 1 ? 2: 1;

           if(datecheck_flag == 2){
             $('.desc-emp-date').hide();
           }
          else $('.desc-emp-date').show();
        });

        //get the rejected or approved build
        // circle progress bar



     </script>

    <script>
      var contactInformation = ''
      // reference list view
      var referenceModal = document.getElementById("reference-list");
      var referenceModalCloseBtn = document.getElementById("reference-close")


      $('#reference-more').click(function() {
        referenceModal.style.display = "block";
        $('html').css('overflow-y', 'hidden');
      });
      $('#reference-close').click(function() {
        referenceModal.style.display = "none";
        $('html').css('overflow-y', 'auto');
      })

      // Get the modal for profile image view with fullscreen
      var modal = document.getElementById("pModal");

      // Get the image and insert it inside the modal - use its "alt" text as a caption
      var img = document.getElementById("profileImage");
      var modalImg = document.getElementById("img01");
      var captionText = document.getElementById("caption");
      img.onclick = function(){
        modal.style.display = "block";
        modalImg.src = this.src;
        captionText.innerHTML = "{{ $user_data['name'] }}";
      }

      // Get the <span> element that closes the modal
      var span = document.getElementsByClassName("close")[0];

      // When the user clicks on <span> (x), close the modal
      span.onclick = function() {
        modal.style.display = "none";
      }

      $(function() {
          $('#calendar-btn').daterangepicker({
            opens: 'center',
            startDate: $('#last_month').val(),
            endDate: $('#current_month').val()

          }, function(start, end, label) {




            $('#date_timepicker_start').val(start.format('MM-DD-YYYY'));
            $('#date_timepicker_end').val(end.format('MM-DD-YYYY'));

            $('#last_month').val(start.format('MM-DD-YYYY'));
            $('#current_month').val(end.format('MM-DD-YYYY'));



            var id = $('input[name=employee_id]').val();
            location.href= "{{ url('/employeeportfolioIndependent') }}/" +id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val();
            //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));

            $('#calendar-i').addClass('fa-spinner fa-spin');

          });

          $('input[name="daterange"]').daterangepicker({
            opens: 'center',
            startDate: $('#last_month').val(),
            endDate: $('#current_month').val()

          }, function(start, end, label) {



            $('#date_timepicker_start').val(start.format('MM-DD-YYYY'));
            $('#date_timepicker_end').val(end.format('MM-DD-YYYY'));

            $('#last_month').val(start.format('MM-DD-YYYY'));
            $('#current_month').val(end.format('MM-DD-YYYY'));



            var id = $('input[name=employee_id]').val();
            location.href= "{{ url('/employeeportfolioIndependent') }}/" +id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val();
            //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            $('#calendar-i').addClass('fa-spinner fa-spin');

          });
       });

    $(document).ready(function() {
      if (<?php echo $shared ?>){
        var shareInputNameModal = document.getElementById('shareInputNameModal');
        shareInputNameModal.style.display = 'block';
      }
      $('.meta').click(function () {
        $(this).toggleClass('show-details');
      });

      $('[data-toggle="popover"]').popover();

     $('.purchase_btn').click(function(){
          $('.overlay').removeClass('showblock');
          $('.overlay').addClass('hideblock');

     });


     $('.print-button-print').click(function(){

        var url = window.location.href;
        $('#current_url').val(url);

        var messageToPost = {'ButtonId': 'print-button-print'};
        window.webkit.messageHandlers.callbackHandler.postMessage(messageToPost);
     

     });


     $('.skillCategories').on("click", function(e){
       e.stopPropagation();
       e.stopImmediatePropagation();
          //  moin
      $('.second-layer').css("display", "block");
      $('.display-layer').css("display", "none");
      var category_id = $(this).attr('category');
      $('#'+category_id).click();
      $('html, body').animate({
                        scrollTop: $('#image-position').offset().top - 100
                    }, 1000);

      var imageTitle = document.getElementById('image-title');

      imageTitle.innerHTML = this.innerText;

     });


     $('.search_category').change(function(){

          //var id = $('.employee_id').val();
          var id = "<?php echo $user_data['id']; ?>";
          var category = $('.search_category').val();
         location.href = "{{ url('/employeeportfolioIndependent') }}/" +id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+'/'+category;
     });

      var modal = document.getElementById('myModal');
      var challengemodal = document.getElementById('challengeModal');
      var modalImg = document.getElementById("modal-box");
      var testinomialmodal = document.getElementById('testinomialModal');
      var descriptionText = document.getElementById("description");
      var categoryblockText = document.getElementById("categoryblock");
      var qrmodal = document.getElementById('qrModal');

     $("#testinomila-carousel").owlCarousel({

          items:3,
          nav : false,
          dots:false,
          margin:10,
          loop:false,
          autoplay:true,
          slideTransition: 'linear',
          autoplayTimeout: 3000,
          autoplaySpeed: 3000,
          autoplayHoverPause: false

      });


      $("#owl-demo").owlCarousel({
         items : 1,
         dots:true

      });

      var demo_owl = $("#owl-demo");

      $('.pre-img').click(function(){
          demo_owl.trigger('prev.owl.carousel');

      });

      $('.next-img').click(function(){
           demo_owl.trigger('next.owl.carousel');

      });


     $(".buildslider").on("click", ".mItem", function(e){
        e.preventDefault();
        var sub_b_id = $(this).find('.slider-image').attr('id');
        ids =  sub_b_id.split('-');
        sub_id = ids[1];
        id = ids[0];
        search_text = $('#search_text').val();
        console.log()
        if(true){

             $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                             var res =  result.html.split("&&&");
                             $('.right_build_image').html(res[0]);
                             $('.category').html(res[1]);
                             $('.description').text(res[2]);
                             $('.desc-emp-date').text(res[3]);
                             if(res[4] == 0)
                               $('.left-arrow').hide();
                             else  $('.left-arrow').show();
                              $('#left-arrow-id').val(res[5]);
                             if(res[6] == 0)
                               $('.right-arrow').hide();
                             else $('.right-arrow').show();
                              $('#right-arrow-id').val(res[7]);
                              $('#expand-id').val(res[8]);

                              if(!result.batch_full_name) {
                                $('#batch_full_name').text("Not verified");
                              }
                              else {
                                $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);
                              }

                              //check challenge
                              if(res[9] != 0){

                                 $('.challenge_image_layer').html(res[10]);
                                 $('.challenge_check').show();
                              }
                              else{
                                $('.challenge_image_layer').html('');
                                 $('.challenge_check').hide();
                              }
                             modal.style.display = "block";
                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
            }
          else{

            $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+'/'+search_text,
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                         var res =  result.html.split("&&&");
                         $('.right_build_image').html(res[0]);
                         $('.category').html(res[1]);
                         $('.description').text(res[2]);
                         $('.desc-emp-date').text(res[3]);
                         if(res[4] == 0)
                           $('.left-arrow').hide();
                         else  $('.left-arrow').show();
                          $('#left-arrow-id').val(res[5]);
                         if(res[6] == 0)
                           $('.right-arrow').hide();
                         else $('.right-arrow').show();
                          $('#right-arrow-id').val(res[7]);
                          $('#expand-id').val(res[8]);

                          $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);

                          //check challenge
                          if(res[9] != 0){

                             $('.challenge_image_layer').html(res[10]);
                             $('.challenge_check').show();
                          }
                          else{
                            $('.challenge_image_layer').html('');
                             $('.challenge_check').hide();
                          }
                         modal.style.display = "block";
                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });

          }

     });
     $(".buildsliders").on("click", ".mItem", function(e){
        e.preventDefault();
        var sub_b_id = $(this).find('.slider-image').attr('id');
        ids =  sub_b_id.split('-');
        sub_id = ids[1];
        id = ids[0];
        search_text = $('#search_text').val();
        console.log()
        if(true){

             $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                             var res =  result.html.split("&&&");
                             $('.right_build_image').html(res[0]);
                             $('.category').html(res[1]);
                             $('.description').text(res[2]);
                             $('.desc-emp-date').text(res[3]);
                             if(res[4] == 0)
                               $('.left-arrow').hide();
                             else  $('.left-arrow').show();
                              $('#left-arrow-id').val(res[5]);
                             if(res[6] == 0)
                               $('.right-arrow').hide();
                             else $('.right-arrow').show();
                              $('#right-arrow-id').val(res[7]);
                              $('#expand-id').val(res[8]);

                              if(!result.batch_full_name) {
                                $('#batch_full_name').text("Not verified");
                              }
                              else {
                                $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);
                              }

                              //check challenge
                              if(res[9] != 0){

                                 $('.challenge_image_layer').html(res[10]);
                                 $('.challenge_check').show();
                              }
                              else{
                                $('.challenge_image_layer').html('');
                                 $('.challenge_check').hide();
                              }
                             modal.style.display = "block";
                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
            }
          else{

            $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+'/'+search_text,
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                         var res =  result.html.split("&&&");
                         $('.right_build_image').html(res[0]);
                         $('.category').html(res[1]);
                         $('.description').text(res[2]);
                         $('.desc-emp-date').text(res[3]);
                         if(res[4] == 0)
                           $('.left-arrow').hide();
                         else  $('.left-arrow').show();
                          $('#left-arrow-id').val(res[5]);
                         if(res[6] == 0)
                           $('.right-arrow').hide();
                         else $('.right-arrow').show();
                          $('#right-arrow-id').val(res[7]);
                          $('#expand-id').val(res[8]);

                          $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);

                          //check challenge
                          if(res[9] != 0){

                             $('.challenge_image_layer').html(res[10]);
                             $('.challenge_check').show();
                          }
                          else{
                            $('.challenge_image_layer').html('');
                             $('.challenge_check').hide();
                          }
                         modal.style.display = "block";
                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });

          }

     });
     $('.right-arrow').click(function(){
          id_str = $('#right-arrow-id').val();
          ids =  id_str.split('-');
          id = ids[0];
          sub_id = ids[1];

         $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                         var res =  result.html.split("&&&");
                         $('.right_build_image').html(res[0]);
                         $('.category').html(res[1]);
                         $('.description').text(res[2]);
                         $('.desc-emp-date').text(res[3]);

                         $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);

                         if(res[4] == 0)
                           $('.left-arrow').hide();
                         else  $('.left-arrow').show();
                         $('#left-arrow-id').val(res[5]);

                         if(res[6] == 0)
                           $('.right-arrow').hide();
                         else $('.right-arrow').show();
                         $('#right-arrow-id').val(res[7]);
                         $('#expand-id').val(res[8]);
                          //check challenge
                          if(res[9] != 0){
                             $('.challenge_image_layer').html(res[10]);
                             $('.challenge_check').show();
                          }
                          else{
                            $('.challenge_image_layer').html('');
                             $('.challenge_check').hide();
                          }
                        // modal.style.display = "block";
                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });


     });
      $('.left-arrow').click(function(){
          id_str = $('#left-arrow-id').val();

          ids =  id_str.split('-');
          id = ids[0];
          sub_id = ids[1];
         $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildinfofromidforindependent') }}/"+id+"/"+sub_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                         var res =  result.html.split("&&&");
                         $('.right_build_image').html(res[0]);
                         $('.category').html(res[1]);
                         $('.description').text(res[2]);
                         $('.desc-emp-date').text(res[3]);
                         $('#batch_full_name').html("<img src='{{ asset('images/verified_clients.png') }}' width='30'><span class='text-danger pr-3'>Verified</span>" + result.batch_full_name);

                         if(res[4] == 0)
                           $('.left-arrow').hide();
                         else  $('.left-arrow').show();
                          $('#left-arrow-id').val(res[5]);
                         if(res[6] == 0)
                           $('.right-arrow').hide();
                         else $('.right-arrow').show();
                          $('#right-arrow-id').val(res[7]);
                          $('#expand-id').val(res[8]);
                          //check challenge
                          if(res[9] != 0){
                             $('.challenge_image_layer').html(res[10]);
                             $('.challenge_check').show();
                          }
                          else{
                            $('.challenge_image_layer').html('');
                             $('.challenge_check').hide();
                          }
                        // modal.style.display = "block";
                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });
     });

     $('.expand-arrow').click(function(){
        src = $('#expand-id').val();
        $('#expand_image').attr("src",src);
        modal.style.display = "none";
        expandModal.style.display = "block";

      });

      $('.chal-expand-arrow').click(function(){
        src = $('#expand-id').val();
        $('#expand_image').attr("src",src);
        challengemodal.style.display = "none";
        expandModal.style.display = "block";

      });

       $('.testinomial-expand-arrow').click(function(){
        src = $('#expand-id').val();
        $('#expand_image').attr("src",src);
        testinomialmodal.style.display = "none";
        expandModal.style.display = "block";

      });


      $('#qr_img').click(function(){

        qrModal.style.display = "block";

      });


      $(".rankchallengeslider").on("click", ".owl-item", function(e){
          e.preventDefault();
          var str_id = $(this).find('.slider-item .slider-image').attr('id');
          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          id = ids[0];
          type = ids[1];
          search_text = $('#search_text').val();

          if(search_text == ''){

              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getchallengeinfofromid') }}/"+id+'/'+type+'/'+emp_id+'/'+$('#date_timepicker_start').val()+'/'+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                 var res =  result.html.split("&&&");
                                 $('.right_build_image').html(res[0]);
                                 $('.category').html(res[1]);
                                 $('.description').text(res[2]);
                                 $('.desc-emp-date').text(res[3]);
                                 if(res[4] == 0)
                                   $('.chal-left-arrow').hide();
                                 else  $('.chal-left-arrow').show();
                                  $('#left-arrow-id').val(res[5]);
                                 if(res[6] == 0)
                                   $('.chal-right-arrow').hide();
                                 else $('.chal-right-arrow').show();
                                  $('#right-arrow-id').val(res[7]);
                                  $('#expand-id').val(res[8]);
                                  $('.challenge_image_layer').html(res[10]);
                                  $('.challenge_check').show();
                                 challengemodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
               }
              else{

                 $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getchallengeinfofromid') }}/"+id+'/'+type+'/'+emp_id+'/'+$('#date_timepicker_start').val()+'/'+$('#date_timepicker_end').val()+'/'+search_text,
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                             var res =  result.html.split("&&&");
                             $('.right_build_image').html(res[0]);
                             $('.category').html(res[1]);
                             $('.description').text(res[2]);
                             $('.desc-emp-date').text(res[3]);
                             if(res[4] == 0)
                               $('.chal-left-arrow').hide();
                             else  $('.chal-left-arrow').show();
                              $('#left-arrow-id').val(res[5]);
                             if(res[6] == 0)
                               $('.chal-right-arrow').hide();
                             else $('.chal-right-arrow').show();
                              $('#right-arrow-id').val(res[7]);
                              $('#expand-id').val(res[8]);
                              $('.challenge_image_layer').html(res[10]);
                              $('.challenge_check').show();
                             challengemodal.style.display = "block";

                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });
              }

      });

      $('.chal-left-arrow').click(function(){

          id_str = $('#left-arrow-id').val();
          ids = id_str.split('-');
          id = ids[0];
          type = ids[1];

          $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getchallengeinfofromid') }}/"+id+'/'+type+'/'+emp_id+'/'+$('#date_timepicker_start').val()+'/'+$('#date_timepicker_end').val(),
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                             var res =  result.html.split("&&&");
                             $('.right_build_image').html(res[0]);
                             $('.category').html(res[1]);
                             $('.description').text(res[2]);
                             $('.desc-emp-date').text(res[3]);
                             if(res[4] == 0)
                               $('.chal-left-arrow').hide();
                             else  $('.chal-left-arrow').show();
                              $('#left-arrow-id').val(res[5]);
                             if(res[6] == 0)
                               $('.chal-right-arrow').hide();
                             else $('.chal-right-arrow').show();
                              $('#right-arrow-id').val(res[7]);
                              $('#expand-id').val(res[8]);
                              $('.challenge_image_layer').html(res[10]);
                              $('.challenge_check').show();
                             //challengemodal.style.display = "block";

                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });

      });


      $('.chal-right-arrow').click(function(){

          id_str = $('#right-arrow-id').val();
          ids = id_str.split('-');
          id = ids[0];
          type = ids[1];

          $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getchallengeinfofromid') }}/"+id+'/'+type+'/'+emp_id+'/'+$('#date_timepicker_start').val()+'/'+$('#date_timepicker_end').val(),
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                             var res =  result.html.split("&&&");
                             $('.right_build_image').html(res[0]);
                             $('.category').html(res[1]);
                             $('.description').text(res[2]);
                             $('.desc-emp-date').text(res[3]);
                             if(res[4] == 0)
                               $('.chal-left-arrow').hide();
                             else  $('.chal-left-arrow').show();
                              $('#left-arrow-id').val(res[5]);
                             if(res[6] == 0)
                               $('.chal-right-arrow').hide();
                             else $('.chal-right-arrow').show();
                              $('#right-arrow-id').val(res[7]);
                              $('#expand-id').val(res[8]);
                              $('.challenge_image_layer').html(res[10]);
                              $('.challenge_check').show();
                             //challengemodal.style.display = "block";

                      }
                      else{
                          //  swal("Oops!", result.message, "error");
                      }
                  }
                });

      });

     $('.close-expand-button').click(function(){
          expandModal.style.display = "none";
     });

     $('#qrclose').click(function(){
          qrModal.style.display = "none";
     });

      $('.close-button').click(function() {
         modal.style.display = "none";
      });

      $('.mobile-close-button').click(function() {
         modal.style.display = "none";
      });

      $('#close-myModal-button').click(function() {
         modal.style.display = "none";
      });


      $('.challenge-mobile-close-button').click(function() {
         challengemodal.style.display = "none";
      });

      $('.challenge-close-button').click(function() {
         challengemodal.style.display = "none";
      });


      $('.testinomialmodal-mobile-close-button').click(function() {
         testinomialmodal.style.display = "none";
      });


    $('.testinomial-close-button').click(function() {
         testinomialmodal.style.display = "none";
      });

      $('.close-testinmial-modal').click(function() {
         testinomialmodal.style.display = "none";
      });


      $(".testinomial_slider").on("click", ".owl-item", function(e){
          e.preventDefault();
          var str_id = $(this).find('.slider-item .slider-image').attr('id');
          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          cur_emp_id = ids[0];
          testinomial_emp_id = ids[1];
          status = ids[2];
          validate_id = ids[3];
          search_text = $('#search_text').val();
          search_cat = $('.search_category').val() || "";

          if(search_text == '' && search_cat == ''){
              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdata') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                  testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
          }
          if(search_text != ''){
              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabysearch') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_text,
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                  testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
          }
          if(search_cat != ''){
              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabycategory') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_cat,
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                  testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
          }
      });

       $(".testinomial-right-arrow").click(function(e){
          //e.preventDefault();
          var str_id = $('#right-arrow-id').val();

          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          cur_emp_id = ids[0];
          testinomial_emp_id = ids[1];
          status = ids[2];
          validate_id = ids[3];
          search_text = $('#search_text').val();
          search_cat = $('.search_category').val() || "";
          if(search_text == '' && search_cat == ''){
              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdata') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }
                if(search_text != ''){
                      $.ajax({
                              headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              },
                              url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabysearch') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_text,
                              type:'POST',
                              dataType:'json',
                              success: function(result){
                                  if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }
                if(search_cat != ''){
                      $.ajax({
                              headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              },
                              url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabycategory') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_cat,
                              type:'POST',
                              dataType:'json',
                              success: function(result){
                                  if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }
      });

        $(".testinomial-left-arrow").click(function(e){

          var str_id = $('#left-arrow-id').val();

          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          cur_emp_id = ids[0];
          testinomial_emp_id = ids[1];
          status = ids[2];
          validate_id = ids[3];
          search_text = $('#search_text').val();
          search_cat = $('.search_category').val() || "";
          if(search_text == '' && search_cat == ''){
              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdata') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }
                 if(search_text != ''){
                      $.ajax({
                              headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              },
                              url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabysearch') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_text,
                              type:'POST',
                              dataType:'json',
                              success: function(result){
                                  if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }

              if(search_cat != ''){
                      $.ajax({
                              headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                              },
                              url: "{{ url('employeeportfolio/getbuildinfofromtestinomialdatabycategory') }}/"+cur_emp_id+'/'+testinomial_emp_id+'/'+status+'/'+validate_id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_cat,
                              type:'POST',
                              dataType:'json',
                              success: function(result){
                                  if(result.status){

                                  $('.right_build_image').html(result.build_image);
                                  $('.category').html(result.subcats);
                                  $('.description').text(result.build_text);
                                  $('.desc-emp-date').text(result.created_at);
                                  if(result.prepos == 0)
                                     $('.testinomial-left-arrow').hide();
                                  else  $('.testinomial-left-arrow').show();
                                  $('#left-arrow-id').val(result.left_arrow);
                                  if(result.nextpos == 0)
                                    $('.testinomial-right-arrow').hide();
                                  else $('.testinomial-right-arrow').show();
                                  $('#right-arrow-id').val(result.right_arrow);
                                  if(result.type != 0){

                                     $('.challenge_image_layer').html(result.chal_image);
                                     $('.challenge_check').show();
                                  }
                                  else{
                                     $('.challenge_image_layer').html('');
                                     $('.challenge_check').hide();
                                  }
                                  $('#expand-id').val(result.src);
                                 // testinomialmodal.style.display = "block";

                          }
                          else{
                              //  swal("Oops!", result.message, "error");
                          }
                      }
                    });
                }
      });

     var emp_id = $('#emp_id').val();


     $(".circleslider").owlCarousel({
      nav : false,
      dots:false,
      items : 3

     });

     $(".buildslider").owlCarousel({
      nav : true,
      dots:false,
      items : 1

     });

     $(".rankchallengeslider").owlCarousel({
      nav : false,
      dots:false,
      items : 4

     });

     $("#testinomial_buildslider").owlCarousel({
          items:4,
          nav : false,
          dots:false,
          margin:10,
          loop:false,
          autoplay:true,
          slideTransition: 'linear',
          autoplayTimeout: 3000,
          autoplaySpeed: 3000,
          autoplayHoverPause: false

     });

     $('.challenge-button').click(function(e){


        e.preventDefault();
        var id = this.id;

        //in the case this element has already clicked
        if($(this).hasClass("clicked")){

             $(this).removeClass("clicked");
             $(this).removeClass("color-button");
             $('.rank-text-layer').removeClass('hide');
             $('.rankchallengeslider').addClass('hide');


        }
        //in the case this element clicked newly
        else{

          $(this).parent().parent().children().children().removeClass("clicked");
          $(this).parent().parent().children().children().removeClass("color-button");
          $(this).addClass("clicked");
          $(this).addClass("color-button");
          search_text = $('#search_text').val();

          if(search_text == ''){

              $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{url('employeeportfolio/getchallengeimageByDate')}}/"+emp_id+"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){

                            $('.rank-text-layer').addClass('hide');
                            $('.rankchallengeslider').removeClass('hide');
                            $('.rankchallengeslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');
                          }
                          else{
                               // swal("Oops!", result.message, "error");
                                $('.rank-text-layer').removeClass('hide');
                                $('.rankchallengeslider').addClass('hide');
                          }
                      }
               });
          }
          else{
            $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{url('employeeportfolio/getchallengeimageByDate')}}/"+emp_id+"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_text,
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                        $('.rank-text-layer').addClass('hide');
                        $('.rankchallengeslider').removeClass('hide');
                        $('.rankchallengeslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');
                      }
                      else{
                           // swal("Oops!", result.message, "error");
                            $('.rank-text-layer').removeClass('hide');
                            $('.rankchallengeslider').addClass('hide');
                      }
                  }
           });
          }
        }

     });


    $('.circle').click(function(e){

          e.preventDefault();
          var id  =  this.id;
          var search_text = $('#search_text').val();
              if(search_text == ''){
              //in the case this element already clicked
              if($(this).hasClass("clicked")){
                  $(this).removeClass("clicked");
                  $(this).removeClass("circle-border");

                      $.ajax({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{ url('employeeportfolio/getbuildsfromcatidforindependent')}}/"+emp_id +"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                          if(result.status){
                                  var res =  result.html.split(",");
                                  //var buildslider_id = '#'+res[0]+'buildslider';
                                  var buildslider_id = '#Categoriesbuildslider';
                            
                                  //$(buildslider_id).addClass('hide');
                                  //$('#image-title').addClass('hide');
                          }
                          else{
                                swal("Oops!", result.message, "error");
                          }
                      }
                    });
              }
              //
              else{
                //alert("New Click");
                $(this).parent().parent().parent().children().children().children().removeClass("clicked");
                $(this).parent().parent().parent().children().children().children().removeClass("circle-border");

                $(this).addClass("clicked");
                $(this).addClass("circle-border");

                  $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildsfromcatidforindependent') }}/"+emp_id +"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){

                              var res =  result.html.split(",");
                                  //var buildslider_id = '#'+res[0]+'buildslider';
                                  var buildslider_id = '#Categoriesbuildslider';
                              $(buildslider_id).removeClass('hide');
                              $('#image-title').removeClass('hide');
                              $(buildslider_id).trigger('replace.owl.carousel', res[1]).trigger('refresh.owl.carousel');

                      }
                      else{
                            swal("Oops!", result.message, "error");
                      }
                  }
                });
              }
        }
        else{
          //in the case this element already clicked
          if($(this).hasClass("clicked")){
              $(this).removeClass("clicked");
              $(this).removeClass("circle-border");

                  $.ajax({
                  headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ url('employeeportfolio/getbuildsfromcatidforindependent')}}/"+emp_id +"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),//+"/"+search_text,
                  type:'POST',
                  dataType:'json',
                  success: function(result){
                      if(result.status){
                              var res =  result.html.split(",");
                                  //var buildslider_id = '#'+res[0]+'buildslider';
                                  var buildslider_id = '#Categoriesbuildslider';
                              $(buildslider_id).addClass('hide');
                              $('#image-title').addClass('hide');
                      }
                      else{
                            swal("Oops!", result.message, "error");
                      }
                  }
                });
          }
          //
          else{
            //alert("New Click");
            $(this).parent().parent().parent().children().children().children().removeClass("clicked");
            $(this).parent().parent().parent().children().children().children().removeClass("circle-border");

            $(this).addClass("clicked");
            $(this).addClass("circle-border");

              $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ url('employeeportfolio/getbuildsfromcatidforindependent') }}/"+emp_id +"/"+id+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val() + '?search=' + search_text,//+'/'+search_text,
              type:'POST',
              dataType:'json',
              success: function(result){
                  if(result.status){

                          var res =  result.html.split(",");
                                  //var buildslider_id = '#'+res[0]+'buildslider';
                                  var buildslider_id = '#Categoriesbuildslider';
                          $(buildslider_id).removeClass('hide');
                          $('#image-title').removeClass('hide');
                          $(buildslider_id).trigger('replace.owl.carousel', res[1]).trigger('refresh.owl.carousel');

                  }
                  else{
                        swal("Oops!", result.message, "error");
                  }
              }
            });
          }
        }
     });

       $('.rejected').click(function(){

          str_id = this.id;
          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          testinomial_emp_id = ids[0];
          cur_emp_id = ids[1];
          status = ids[2];
          search_text = $('#search_text').val();
          search_cat = $('.search_category').val() || "";
          //in the case this element already clicked
        if($(this).hasClass("clicked")){

            $(this).removeClass("clicked");
            $('#testinomial_buildslider').addClass('hide');

        }
        //
        else{

            $(this).addClass("clicked");
            if(search_text != ''){

                $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('employeeportfolio/getbuildsfromtestinomial') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+"/"+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),//+"/"+search_text,
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }
            if(search_cat != ''){
              $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('employeeportfolio/getbuildsfromtestinomialbycategory') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+'/'+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_cat,
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }
            if(search_text == '' && search_cat == ''){
                $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('employeeportfolio/getbuildsfromtestinomial') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+'/'+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }

        }
        referenceModal.style.display = "none";
    });

    $('.approved').click(function(){
          str_id = this.id;
          ids = [];
          if (str_id.indexOf('/') !== -1)
            ids = str_id.split('/');
          else
            ids = str_id.split('-');
          testinomial_emp_id = ids[0];
          cur_emp_id = ids[1];
          status = ids[2];
          search_text = $('#search_text').val();
          search_cat = $('.search_category').val() || "";
          console.log("123123123", search_cat)
          //in the case this element already clicked

        if($(this).hasClass("clicked")){

            $(this).removeClass("clicked");
            $('#testinomial_buildslider').addClass('hide');

        }
        //
        else{
            $(this).addClass("clicked");
            if(search_text != ''){

                $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // url: "{{ url('employeeportfolio/getbuildsfromtestinomialbysearch') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+"/"+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_text,
                url: "{{ url('employeeportfolio/getbuildsfromtestinomial') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+"/"+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),//+"/"+search_text,
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }
            if(search_cat != ''){
              $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('employeeportfolio/getbuildsfromtestinomialbycategory') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+'/'+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val()+"/"+search_cat,
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }
            if(search_text == '' && search_cat == ''){
                $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('employeeportfolio/getbuildsfromtestinomial') }}" +"/"+cur_emp_id+"/"+testinomial_emp_id+'/'+status+"/"+$('#date_timepicker_start').val()+"/"+$('#date_timepicker_end').val(),
                type:'POST',
                dataType:'json',
                success: function(result){
                    if(result.status){
                           referenceModal.style.display = "none";
                           $('#testinomial_buildslider').removeClass('hide');
                           $('#testinomial_buildslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');

                    }
                    else{
                          swal("Oops!", result.message, "error");
                    }
                }
              });
            }

        }

    });


    $('form').submit(function (event) {
      $(this).find('button i').addClass('fa-spinner fa-spin');
    });
    });

    var bookModal = document.getElementById('bookModal');
    
    function bookNow(phone){
      bookModal.style.display = 'block'
      document.getElementById('chooseOption').style.display = 'block'
      document.getElementById('smsTextInput').style.display = 'none'
    }
    $('#bookModal-close-button').click(() => {
      bookModal.style.display = 'none'
    })
    function showBookInputTextBox(){
      $('#bookSmsText').val('');
      document.getElementById('chooseOption').style.display = 'none'
      document.getElementById('smsTextInput').style.display = 'block'
    }
    function sendSMS(){
      let smsText = $('#bookSmsText').val()
      let firstName = $('#first_name_book').val()
      let lastName = $('#last_name_book').val()
      let name = firstName + ' ' + lastName
      let phone = $('#phone_book').val()

      if(phone === '' || !phone ){
        alert("Please Fill up the phone number.");
        return;
      }
      else{

      bookModal.style.display = 'none'
      $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('sendBookSMS') }}",
        type:'POST',
       // dataType:'json',
        data: {
          smsText,
          firstName,
          lastName,
          name,
          phone,
          employee_id : <?php echo $user_data['id'] ?>
        },
        success: function(result){
         // alert(result);
          if (result.status){
            alert('Sent Text successfully!');
          }
          else{
            alert(result.message);
          }
        }
      });
      }

      // $('#bookSmsText').val('')
      // $('#first_name_book').val('')
      // $('#last_name_book').val('')
      // $('#phone_book').val('')

      
    }

    function sendViewerName(){
      if ($('#first_name').val().trim() !== "" && $('#last_name').val().trim() !== ""){
        let first_name = $('#first_name').val().trim();
        let last_name = $('#last_name').val().trim();
        shareInputNameModal.style.display = 'none';
        $('#first_name').val('')
        $('#last_name').val('')
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ url('sendViewerNameForWatchedEmployeePortfolioIndependent') }}",
          type:'POST',
          dataType:'json',
          data: {
            first_name,
            last_name,
            employee_id : <?php echo $user_data['id'] ?>
          },
          success: function(result){
            if (result.status){
            }
            else{
              alert(result.message);
            }
          }
        });
      }
    }

    function contact(email){
      contactInformation = email
      $('#contactModal').css('display', 'block')
    }
    $('#contactModal-close-button').on('click', () => {
      $('#contactModal').css('display', 'none')
    });
    function sendContactText(){
      if ($('#first_name_contact').val().trim() !== "" && $('#last_name_contact').val().trim() !== "" && $('#text_contact').val().trim() !== ""){
        let first_name = $('#first_name_contact').val().trim()
        let last_name = $('#last_name_contact').val().trim()
        let phone = $('#phone_contact').val().trim()
        let text_contact = $('#text_contact').val().trim()
        $('#contactModal').css('display', 'none')
        $('#first_name_contact').val('')
        $('#last_name_contact').val('')
        $('#text_contact').val('')
        $('#phone_contact').val('')
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ url('sendContactText') }}",
          type:'POST',
          dataType:'json',
          data: {
            first_name,
            last_name,
            text_contact,
            contactInformation,
            phone,
            employee_id : <?php echo $user_data['id'] ?>
          },
          success: function(result){
            if (result.status){
              alert('Sent Text successfully!');
            }
            else{
              alert(result.message);
            }
          }
        });
      }
    }
    </script>

     <!-- {{-- moin --}} -->
    <script>
    var slideIndex = 0;
      showSlides();

      function showSlides() {
        
        var i;
        var slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";  
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}    
        slides[slideIndex-1].style.display = "block";  
        setTimeout(showSlides, 3000); // Change image every 2 seconds
      }
      </script>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
     $current_name = Route::currentRouteName();
     //$te =Route::getAction();$current_name
     $newtext = str_replace("."," ",$current_name);
     $newtext = str_replace("index","",$newtext);
     $ac = $newtext == ''? 'Dashboard' :$newtext;
     $ac = $ac == 'users' ? 'Company' : $ac;
     $ac = ucfirst($ac);
    ?>
    <title>{{ $ac }} - {{config('app.name', 'Uptime') }}</title>
   

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('css/puse-icons-feather/feather.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor.bundle.base.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor.bundle.addons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/groovy.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>

     <style type="text/css">
      .ui-autocomplete {
        z-index: 99999 !important;
      }

      .ui-widget {
        font-family: unset !important;
      }
    </style>

</head>
<body class="<?PHP echo (isset($_COOKIE['sidebar_open']) && $_COOKIE['sidebar_open'] == 1 ? 'sidebar-icon-only' : '')?>">
<?php
function getActiveClass($name){
   //$current_name = Route::currentRouteName();
   $action = explode(".",Route::currentRouteName());
   if($action[0] == $name){
  
    return 'active';
   }else{
   
    return '';
   }
    
  

}

function getActiveClassExpend(){
  $array = array('builds','validations','tenure');
  $current_name = Route::currentRouteName();
  $action = explode(".",Route::currentRouteName());  
  if(in_array($action[0],$array)){
    return 'true';
  }else{
    return 'false';
  }
}

function getActiveClassChallenge(){
  $array = array('challenge');
  $current_name = Route::currentRouteName();
  $action = explode(".",Route::currentRouteName());  
  if(in_array($action[0],$array)){
    return 'true';
  }else{
    return 'false';
  }
}

function getActiveClassRequests(){
  $array = array('build-requests', 'challange-requests');
  $current_name = Route::currentRouteName();
  $action = explode(".",Route::currentRouteName());  
  if(in_array($action[0],$array)){
    return 'true';
  }else{
    return 'false';
  }
}
?>
<div class="container-scroller">


 <!-- partial:partials/_navbar.html start -->


    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo" href="{{url('/')}}">Uptime</a>
        <a class="navbar-brand brand-logo-mini" href="{{url('/')}}">U</a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu theme-color"></span>
        </button>
        
 

<!--   employee logout menu start  -->

                <ul class="navbar-nav navbar-nav-right">
                <input type="text" placeholder="Search employee" class="form-control" style="width:unset;" value="" id="employee_search" name="search_employee" data-employee_id="{{Auth::guard('admin')->user()->id}}">
                  @if(!Route::current()->getName())
                    <li class="nav-item dropdown d-none d-xl-inline-block active">
                  @else
                    <li class="nav-item dropdown d-none d-xl-inline-block">
                  @endif
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                      <span class="mr-3">Hello, {{Auth::guard('admin')->user()->full_name }} !</span>
                      @if(Auth::guard('admin')->user()->image != '')
                      <img class="img-xs rounded-circle" src="<?php echo \Storage::disk('s3')->url('images/employee/'.Auth::guard('admin')->user()->image);?>" alt="profile image" onerror="this.src='<?php echo \Storage::disk('s3')->url('images/avatar.png'); ?>'">
                      @else
                      <img class="img-xs rounded-circle" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                      @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">                         
                      
                      <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                      </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                        @csrf
                    </form>
                    </div>
                  </li>
                </ul>
             

<!--   employee logout menu End  -->


        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
 <!-- partial:partials/_navbar.html  end -->


<!-- partial  -->


    <div class="container-fluid page-body-wrapper">
        

<!-- partial -->

 <!-- partial:partials/_sidebar.html start  -->


 <!-- Nav bar for Employee Start-->

        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">

              <div class="nav-link">
                <div class="profile-image">

                  @if( Auth::guard('admin')->user()->image != '')
                  <img height="88px" src="<?php echo \Storage::disk('s3')->url('images/employee/'.Auth::guard('admin')->user()->image);?>" alt="profile image" onerror="this.src='<?php echo \Storage::disk('s3')->url('images/avatar.png'); ?>'">
                  @else
                  <img height="88px" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                  @endif
                  <span class="online-status online"></span>
                </div>

                <div class="profile-name">
                  <p class="name"> {{ Auth::guard('admin')->user()->full_name}}</p>
                  <p class="designation"></p>
                </div>
               
              </div>

            </li>



            <li class="nav-item"> <a class="nav-link dashboard" href="{{ route('leader.dashboard') }}"> <img class="menu-icon" src="{{ asset('images/theme_menu_icons/02.png') }}" alt="menu icon"> <span class="menu-title">Dashboard</span></a> </li>
            
            <li class="nav-item {{getActiveClass('employee')}}"><a class="nav-link white-color" href="{{ route('leader.employee.list') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon"><span class="menu-title">Employee</span></a></li> 

   <li class="nav-item {{getActiveClass('UploadPage')}}"><a class="nav-link white-color" href="{{url('leader/upload')}}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon"><span class="menu-title">Announcements</span></a></li> 
   
            <li class="nav-item {{getActiveClass('categories')}}"><a class="nav-link white-color" href="{{ route('leader.categories.list') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/09.png') }}" alt="menu icon">Categories</a></li>


            <li class="nav-item {{getActiveClass('builds')}}"><a class="nav-link white-color" href="{{ route('leader.build.list') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/20.png') }}" alt="menu icon">Submissions</a></li>
            
            <li class="nav-item {{getActiveClass('reward')}}"><a class="nav-link white-color" href="{{url('leader/reward')}}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/20.png') }}" alt="menu icon">Reward Page</a></li>
            
            <li class="nav-item">
              <a class="nav-link"  data-toggle="collapse" href="#page-layouts-levelchallenge" aria-expanded="{{getActiveClassChallenge()}}" aria-controls="page-layouts-challenge"> <img class="menu-icon" src="{{ asset('images/theme_menu_icons/05.png') }}" alt="menu icon"> <span class="menu-title">Challenges</span><i class="menu-arrow"></i></a>

              <div class="collapse non-padding {{getActiveClassChallenge() == 'true' ? 'show' : ''}}" id="page-layouts-levelchallenge" style="">
                <ul class="nav flex-column sub-menu non-padding border-bottom-radius">
                  <li class="nav-item {{getActiveClass('level-challenge')}}"><a class="nav-link white-color" href="{{ route('leader.level-challenge.list') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon">Timed Challenge</a></li>
                  
                  <li class="nav-item {{getActiveClass('level-preset-challenge')}}"><a class="nav-link white-color" href="{{route('leader.level-preset-challenge.list') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon">Preset Challenge</a></li>
                </ul>
              </div>       
           </li> 
            <li class="nav-item {{getActiveClass('tier')}}"><a class="nav-link white-color" href="{{ url('leader/tier') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/13.png') }}" alt="menu icon"><span class="menu-title">Daily Achievement</span></a></li>
             
            <li class="nav-item {{getActiveClass('notifiaction')}}"><a class="nav-link white-color" href="{{ url('leader/notification') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/13.png') }}" alt="menu icon"><span class="menu-title">Push Notification</span></a></li>
          </ul>
        </nav>

<!-- Nav bar for Employee End-->

<!-- partial -->
    <div class="main-panel">

@yield('content')
<!-- content-wrapper ends -->

<!-- partial:partials/_footer.html -->
<footer class="footer">

    <div class="container-fluid clearfix">
           <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?php echo date("Y");?> Uptime. All rights reserved.</span>
    </div>

</footer>

<!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="{{ asset('js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('js/vendor.bundle.addons.js') }}"></script>   
<!-- endinject -->
<!-- inject:js -->
<script>
  var base_url = '<?php echo url("/");?>';
</script>

<!-- <script src="{{ asset('js/off-canvas.js') }}"></script> -->
<script type="text/javascript">
  (function($) {
  'use strict';
  $(function() {
    $('[data-toggle="offcanvas"]').on("click", function() {
      $('.sidebar-offcanvas').toggleClass('active')
    });
  });
})(jQuery);
</script>

<script src="{{ asset('js/jquery.datatable.min.js') }}"></script><!-- <script src="{{ asset('js/hoverable-collapse.js') }}"></script> -->
<script type="text/javascript">
  (function($) {
  'use strict';
  //Open submenu on hover in compact sidebar mode and horizontal menu mode
  $(document).on('mouseenter mouseleave', '.sidebar .nav-item', function(ev) {
    var body = $('body');
    var sidebarIconOnly = body.hasClass("sidebar-icon-only");
    var horizontalMenu = body.hasClass("horizontal-menu");
    var sidebarFixed = body.hasClass("sidebar-fixed");
    if (!('ontouchstart' in document.documentElement)) {
      if (sidebarIconOnly || horizontalMenu) {
        if (sidebarFixed) {
          if (ev.type === 'mouseenter') {
            body.removeClass('sidebar-icon-only');
          }
        } else {
          var $menuItem = $(this);
          if (ev.type === 'mouseenter') {
            $menuItem.addClass('hover-open')
          } else {
            $menuItem.removeClass('hover-open')
          }
        }
      }
    }
  });
  // Horizontal menu toggle fuction for mobile
  $(".navbar.horizontal-layout .navbar-menu-wrapper .navbar-toggler").on("click", function() {
    $(".navbar.horizontal-layout").toggleClass("header-toggled");
  });
})(jQuery);
</script>
<script src="{{ asset('js/misc.js') }}"></script>
<script src="{{ asset('js/settings.js') }}"></script>
<script src="{{ asset('js/todolist.js') }}"></script>
<!-- <script src="{{ asset('js/file-upload.js') }}"></script> -->
<script>
  (function($) {
  'use strict';
  $(function() {
    $('.file-upload-browse').on('click', function() {
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $('.file-upload-default').on('change', function() {
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
  });
})(jQuery);  
</script>

<!-- <script src="{{ asset('js/tooltips.js') }}"></script> -->
<script type="text/javascript">
  (function($) {
  'use strict';

  $(function() {
    /* Code for attribute data-custom-class for adding custom class to tooltip */
    if (typeof $.fn.tooltip.Constructor === 'undefined') {
      throw new Error('Bootstrap Tooltip must be included first!');
    }

    var Tooltip = $.fn.tooltip.Constructor;

    // add customClass option to Bootstrap Tooltip
    $.extend(Tooltip.Default, {
      customClass: ''
    });

    var _show = Tooltip.prototype.show;

    Tooltip.prototype.show = function() {

      // invoke parent method
      _show.apply(this, Array.prototype.slice.apply(arguments));

      if (this.config.customClass) {
        var tip = this.getTipElement();
        $(tip).addClass(this.config.customClass);
      }

    };
    $('[data-toggle="tooltip"]').tooltip();

  });
})(jQuery);
</script>
<script src="{{ asset('js/groovy1.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<!-- <script src="{{ asset('js/alerts.js') }}"></script> -->
<!-- <script src="{{ asset('js/modal-demo.js') }}"></script> -->
<script type="text/javascript">
  (function($) {
  'use strict';
  $('#exampleModal-4').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    modal.find('.modal-title').text('New message to ' + recipient)
    modal.find('.modal-body input').val(recipient)
  })
})(jQuery);
</script>
<script src="{{ asset('js/avgrund.js') }}"></script>
<!-- <script src="{{ asset('js/imagesize.js') }}"></script> -->
<script type="text/javascript">
    $('.file-upload-default').bind('change', function() {
                if(Math.round(this.files[0].size/1000) >= 2000){
                     alert('The image may not be greater than 2048 kilobytes');
                     $('.file-upload-default').val("");
                }
                    
  });
</script>

<!-- jQuery UI -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


 <script type="text/javascript">

    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var employee_id = $("#employee_search").data('employee_id');
    $(document).ready(function(){
      $( "#employee_search" ).autocomplete({
        source: function( request, response ) {
          
          // Fetch data
          $.ajax({
            url:"{{route('employees.getEmployees')}}",
            type: 'post',
            dataType: "json",
            data: {
               _token: CSRF_TOKEN,
               search: request.term,
               employee_id: employee_id
            },
            success: function( data ) {
               response( data );
            }
          });
        },
        select: function (event, ui) {
           // Set selection
           $('#employee_search').val(ui.item.label); // display the selected text
          //  $('#employeeid').val(ui.item.value); // save selected id to input

          window.location.href = "{{ url('/leader/test/') }}/" + ui.item.id +"/" + ui.item.startDate + "/" + ui.item.endDate;

           return false;
        }
      });

    });
  </script>
</body>
</html>
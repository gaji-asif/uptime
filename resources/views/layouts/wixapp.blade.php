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

     $shareTitle = "{$user_data['name']} has shared their resume with you through Uptimeprofile.com";
    ?>
  @if(request('share'))
    <title>{{ $shareTitle }}</title>
  @else
    <title>{{ $ac }} - {{ config('app.name', 'Uptime') }}</title>
  @endif

  <link rel="shortcut icon" type="image/png" href="https://www.uptimeprofile.com/images/logo.png"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet">
    <script href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="{{ asset('css/puse-icons-feather/feather.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor.bundle.base.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link href="{{ asset('css/vendor.bundle.addons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/groovy.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
</head>
<body class="<?PHP echo (isset($_COOKIE['sidebar_open']) && $_COOKIE['sidebar_open'] == 1 ? 'sidebar-icon-only' : '')?>">
<?php
function getActiveClass($name){
  $current_name = Route::currentRouteName();
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

<div class="visible-xs" style="margin-bottom: 72px"></div>

<!-- container-scroller -->
<!-- plugins:js -->
{{-- <script src="{{ asset('js/vendor.bundle.base.js') }}"></script> --}}
{{-- <script src="{{ asset('js/vendor.bundle.addons.js') }}"></script> --}}
<script src="{{ asset('js/carousel js/owl.carousel.js') }}"></script>
<script src="{{ asset('js/carousel js/owl.support.js') }}"></script>
<script src="{{ asset('js/carousel js/owl.navigation.js') }}"></script>
<script src="{{ asset('js/carousel js/owl.autoplay.js') }}"></script>
<script src="{{ asset('js/hurkanSwitch.js') }}"></script>

<script type="text/javascript">
    $('.file-upload-default').bind('change', function() {
                if(Math.round(this.files[0].size/1000) >= 2000){
                     alert('The image may not be greater than 2048 kilobytes');
                     $('.file-upload-default').val("");
                }

  });
</script>
<script src="{{ asset('js/lazy-load-images.min.js') }}"></script>

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

</body>
</html>
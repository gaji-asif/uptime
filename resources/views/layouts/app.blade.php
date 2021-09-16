<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Inject page title here -->
  <title>@yield('title') - {{config('app.name', 'Uptime') }}</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

  <!-- Global Styles -->
  <link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('css/groovy.css') }}" rel="stylesheet">

  <style type="text/css">
    .ui-autocomplete {
      z-index: 99999 !important;
    }
    .ui-widget {
      font-family: unset !important;
    }
    .print .sidebar, .print .navbar {
      display: none !important;
    }
    .print .page-body-wrapper {
      padding-top: 0px !important;
    }
    .print .main-panel {
      width: 100%;
    }
  </style>

  <!-- Inject page specific styles here -->
  <!-- Child will insert custom stylesheets/css as required -->
  @stack('stylesheets')

  @php
      $bodyClass = [];
      $bodyClass[] = !empty($_COOKIE['sidebar_open']) ? 'sidebar-icon-only' : '';
      $bodyClass[] = request('print') ? 'print' : '';
  @endphp
</head>
<body class="{{ trim(implode(' ', $bodyClass)) }}">
<div class="container-scroller">
  <!-- Header -->
  @include('layouts.partials.header')

  <div class="page-body-wrapper">
    <!-- Sidebar Navigation -->
    @include('layouts.partials.sidebar')

    <div class="main-panel">
      <!-- Inject the main child view which holds the content for the page -->
      @yield('content')
      asdasd asdasda ads

      <!-- Footer -->
      @include('layouts.partials.footer')
    </div>
  </div>
</div>

<!-- Global Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<!-- Handles the sidebar menu items -->
<script src="{{ asset('js/misc.js') }}"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

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

    $('[data-toggle="offcanvas"]').on("click", function() {
      $('.sidebar-offcanvas').toggleClass('active')
    });
  })(jQuery);
</script>

<!-- Handle employee search on header -->
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

        window.location.href = "{{ url('/executive/test/') }}/" + ui.item.id +"/" + ui.item.startDate + "/" + ui.item.endDate;

        return false;
      }
    });

  });
</script>

<!-- Inject page specific scripts here -->
<!-- Child will insert custom js as required -->
@stack('scripts')
</body>
</html>
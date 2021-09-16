<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <div class="nav-link">
        <div class="profile-image">
          @if( optional(auth('admin')->user())->image != '')
            
          @else
            <img height="88px" src="{{ asset('images/avatar.png') }}" alt="Profile image" />
          @endif
          <span class="online-status online"></span>
        </div>
        <div class="profile-name">
          <p class="name">{{ optional(auth('admin')->user())->full_name }}</p>
          <p class="designation"></p>
        </div>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link dashboard" href="{{ route('executive.dashboard') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/02.png') }}" alt="menu icon" />
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('employee') }}">
      <a class="nav-link white-color" href="{{ route('executive.employee.list') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon" />
        <span class="menu-title">Employee</span>
      </a>
    </li>
  <!--       <li class="nav-item {{ request()->routeIs('test') }}"><a class="nav-link white-color" href="{{ url('executive/test') }}"><img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon"><span class="menu-title">Employee View</span></a></li> -->
    <li class="nav-item {{ request()->routeIs('UploadPage') }}">
      <a class="nav-link white-color" href="{{url('executive/upload') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon" />
        <span class="menu-title">Announcements</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('industry') }}">
      <a class="nav-link white-color" href="{{ route('executive.industry.list') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/14.png') }}" alt="menu icon" />
        <span class="menu-title">Store</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('categories') }}">
      <a class="nav-link white-color" href="{{ route('executive.categories.list') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/09.png') }}" alt="menu icon" />
        <span class="menu-title">Categories</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('builds') }}">
      <a class="nav-link white-color" href="{{ route('executive.builds.list') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/20.png') }}" alt="menu icon" />
        <span class="menu-title">Submissions</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('reward') }}">
      <a class="nav-link white-color" href="{{url('executive/reward') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/20.png') }}" alt="menu icon" />
        <span class="menu-title">Reward Page</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#page-layouts-levelchallenge" aria-expanded="{{ request()->routeIs('*.challenge') }}" aria-controls="page-layouts-challenge">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/05.png') }}" alt="menu icon" />
        <span class="menu-title">Challenges</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse non-padding {{ request()->routeIs('*.challenge') ? 'show' : '' }}" id="page-layouts-levelchallenge" style="">
        <ul class="nav flex-column sub-menu non-padding border-bottom-radius">
          <li class="nav-item {{ request()->routeIs('level-challenge') }}">
            <a class="nav-link white-color" href="{{ route('executive.level-challenge.list') }}">
              <img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon" />
              Timed Challenge
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('level-preset-challenge') }}">
            <a class="nav-link white-color" href="{{route('executive.level-preset-challenge.list') }}">
              <img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon" />
              Preset Challenge
            </a>
          </li>
        </ul>
      </div>
    </li>
{{--    <li class="nav-item">--}}
{{--      <a class="nav-link" data-toggle="collapse" href="#page-layouts-requests" aria-expanded="{{ request()->routeIs(['*.build-requests', '*.challange-requests']) }}" aria-controls="page-layouts-requests">--}}
{{--        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/05.png') }}" alt="menu icon" />--}}
{{--        <span class="menu-title">Requests</span>--}}
{{--        <i class="menu-arrow"></i>--}}
{{--      </a>--}}
{{--      <div class="collapse non-padding {{ request()->routeIs(['*.build-requests', '*.challange-requests']) == 'true' ? 'show' : '' }}" id="page-layouts-requests" style="">--}}
{{--        <ul class="nav flex-column sub-menu non-padding border-bottom-radius">--}}
{{--          <li class="nav-item {{ request()->routeIs('build-requests') }}">--}}
{{--            <a class="nav-link white-color" href="{{ url('executive/build_request') }}">--}}
{{--              <img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon" />--}}
{{--              Build Requests--}}
{{--            </a>--}}
{{--          </li>--}}
{{--          <li class="nav-item {{ request()->routeIs('challange-requests') }}">--}}
{{--            <a class="nav-link white-color" href="{{ url('executive/employee_request') }}">--}}
{{--              <img class="menu-icon" src="{{ asset('images/theme_menu_icons/17.png') }}" alt="menu icon" />--}}
{{--              Employee Requests--}}
{{--            </a>--}}
{{--          </li>--}}
{{--        </ul>--}}
{{--      </div>--}}
{{--    </li>--}}
{{--    <li class="nav-item {{ request()->routeIs('tier') }}">--}}
{{--      <a class="nav-link white-color" href="{{ url('executive/tier') }}">--}}
{{--        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/13.png') }}" alt="menu icon" />--}}
{{--        <span class="menu-title">Daily Achievement</span>--}}
{{--      </a>--}}
{{--    </li>--}}
    <li class="nav-item {{ request()->routeIs('notifiaction') }}">
      <a class="nav-link white-color" href="{{ url('executive/notification') }}">
        <img class="menu-icon" src="{{ asset('images/theme_menu_icons/13.png') }}" alt="menu icon" />
        <span class="menu-title">Push Notification</span>
      </a>
    </li>
  </ul>
</nav>

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
    <a class="navbar-brand brand-logo" href="{{ url('/') }}">Uptime</a>
    <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">U</a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-menu theme-color"></span>
    </button>

    <!-- Inject page specific header elements -->
    @yield('header')

    <ul class="navbar-nav navbar-nav-right">
      <input type="text" placeholder="Search employee" class="form-control" style="width:unset;" value="" id="employee_search" name="search_employee" data-employee_id="{{auth('admin')->id()}}">

      @if(!Route::current()->getName())
        <li class="nav-item dropdown d-none d-xl-inline-block active">
      @else
        <li class="nav-item dropdown d-none d-xl-inline-block">
          @endif
          <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <span class="mr-3">Hello, {{ optional(auth('admin')->user())->full_name }} !</span>
            @if(optional(auth('admin')->user())->image != '')
              
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

    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>

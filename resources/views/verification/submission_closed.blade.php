
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>UptimeProfile - Verify Submission</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="{{asset('css/verification.css')}}" rel="stylesheet">
  </head>
  <body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
  <header class="masthead mb-auto">

  </header>

  <main role="main" class="inner cover">
    <div>
      <img src="{{asset('images/uptime-logo.png')}}" alt="">
      <br>
      <br>
      <br>
      <br>
    </div>
    <h1 class="cover-heading">Thank you</h1>
    <p class="lead"><h4>Start your Uptime profile below</h4></p>
    <p class="lead">
      <br>
      Use our mobile apps to get verification for your works
      <br>
      <a href="https://apps.apple.com/us/app/uptime/id1453545209?at=11lMLE&ct=msv5tx" target="_blank">
      	<img src="{{asset('images/appstore-icon.png')}}" alt=""></a>
      <a href="https://play.google.com/store/apps/details?id=com.uptime.john.uptime" target="_blank">
      	<img src="{{asset('images/playstore-icon.png')}}" alt=""></a>
    </p>

    <p class="lead"><a href="{{ url('employeeportfolioIndependent/'.$user_data['id'].'/'.\Carbon\Carbon::parse($user_data['created_at'])->format('m-d-Y').'/'.\Carbon\Carbon::today()->format('m-d-Y')) }}" target="_blank"><u>View {{ $user_data['full_name'] }}'s Resume</u></a></p>

  </main>

  <footer class="mastfoot mt-auto">
    <div class="inner">
      <p>&copy; {{ date("Y") }} by <a href="https://uptimeprofile.com/">UptimeProfile.com</a>.</p>
    </div>
  </footer>
</div>
</body>
</html>

<html>
<head>
  <title>Uptime Profile</title>
  <meta property="og:url"           content="{{ route('verify.submission.share', ['uuid' => $uuid ]) }}" />
  <meta property="og:type"          content="website" />
  <meta property="og:title"         content="Book Now" />
  <meta property="og:description"   content="View all {{$full_name}}'s work, verified through uptimeprofile.com" />
  <meta property="og:image"         content="{{ \Storage::disk('s3')->url('images/build/'.$builds[0]['image']) }}" />
</head>
<body>

<h2>Test FB Share</h2>

</body>
</html>
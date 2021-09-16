@php
  $layout = request('print') ? 'layouts.app' :  'layouts.wixapp';
  $resume = request('print') ? 'resume-pdf' :  'resumeIndependentNew';
@endphp

@extends($layout)

@section('content')
  @include($resume)
@endsection

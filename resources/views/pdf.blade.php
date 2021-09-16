@php
  $layout = request('print') ? 'layouts.app' :  'layouts.wixapp';
  $resume = request('print') ? 'resume-pdf' :  'resume';
@endphp

@extends($layout)

@section('content')
  @include($resume)
@endsection
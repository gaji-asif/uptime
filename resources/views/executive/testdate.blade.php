@php
  $layout = request('print') ? 'layouts.app' :  'executive/layouts.app';
  $resume = request('print') ? 'resume-pdf' :  'resume';
@endphp

@extends($layout)

@section('content')
  @include($resume)
@endsection
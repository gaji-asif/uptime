@component('mail::layout')
{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ config('app.name') }}
    @endcomponent
@endslot

{{-- Body --}}

Hi {{$employee_name}},

<p>{{ $sender_name }} has requested a reference check on {{ $employee_name }} with the below message:</p>

<p>Phone number : {{ $phone }}</p>

<p>{{ $text }}</p>

<center>Thank you for using <a href="https://Uptimeprofile.com/">uptimeprofile.com</a><center>

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    @endcomponent
@endslot
@endcomponent
@component('mail::layout')
{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ config('app.name') }}
    @endcomponent
@endslot

{{-- Body --}}

Hello {{$batch->lastname}},

<strong>{{ $employee->full_name }}</strong> has requested you to verify their work. By following the provided link below, you will review and provide an e-signature on their attached photos that are accurate and true. Once verified, the images will be saved to their digital resume where you will be listed as a verified client. 


@component('mail::button', ['url' => $url])
Click to Verify
@endcomponent


Verified Through Uptime <img style="max-width: 40px;vertical-align: middle;" src="{{ asset('images/logo-email.png') }}">


<center><a href="https://Uptimeprofile.com/">uptimeprofile.com</a><center>


{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
    @endcomponent
@endslot
@endcomponent



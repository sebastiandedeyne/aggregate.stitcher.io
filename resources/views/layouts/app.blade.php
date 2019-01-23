<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($title)
        <title>{{ $title }} — {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endisset

    {{--@include('admin.layouts.partials.favicons')--}}

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    {{--<link rel="stylesheet" href="{{ mix('css/vendor.css') }}" media="none" onload="this.media='all'">--}}
    <link href="https://fonts.googleapis.com/css?family=Hind:400,700|Volkhov:700" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    {{-- <script defer src="{{ mix('vendor.js') }}"></script> --}}
    <script defer src="{{ mix('js/app.js') }}"></script>
</head>
<body class="bg-black p-3">
<div class="bg-white">
    <div class="max-w-xl mx-auto flex pt-8">
        <nav class="w-1/4 pr-8">
            <header class="h-12 pt-2 flex items-center mb-8" style="padding-bottom: 0.375rem">
                <strong class="font-title text-2xl text-primary font-bold">aggregate</strong>
                <span class="bg-black text-white rounded text-xs ml-2" style="padding: 0.25rem 0.25rem 0.1rem; margin-top: 0.15rem">beta</span>
            </header>
            <ul class="text-sm">
                @if(current_user())
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\UserSourcesController::class, 'index']) }}">
                            {{ __('My content') }}
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\UserMutesController::class, 'index']) }}">
                            {{ __('Mutes') }}
                        </a>
                    </li>
                    @if(current_user()->isAdmin())
                        <li class="mb-3">
                            <a href="{{ action([\App\Http\Controllers\AdminSourcesController::class, 'index']) }}">
                                {{ __('Admin') }}
                            </a>
                        </li>
                    @endif
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\Auth\LogoutController::class, 'logout']) }}">
                            {{ __('Log out') }}
                        </a>
                    </li>
                @else
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}">
                            <i class="fas fa-user w-6 opacity-75"></i> {{ __('Log in') }}
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\Auth\RegisterController::class, 'register']) }}">
                            <i class="fas fa-user-plus w-6 opacity-75"></i> {{ __('Register') }}
                        </a>
                    </li>
                @endif

                @if(! current_user())
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm']) }}">
                            <i class="fas fa-pencil-alt w-6 opacity-75"></i> {{ __('Submit your blog') }}
                        </a>
                    </li>
                @elseif(! current_user()->getPrimarySource())
                    <li class="mb-3">
                        <a href="{{ action([\App\Http\Controllers\UserSourcesController::class, 'index']) }}">
                            <i class="fas fa-pencil-alt w-6 opacity-75"></i> {{ __('Submit your blog') }}
                        </a>
                    </li>
                @endif

                <li class="mb-3">
                    <a href="https://github.com/brendt/aggregate.stitcher.io/issues" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-bug w-6 opacity-75"></i> {{ __('Report an issue') }}
                    </a>
                </li>
            </ul>
        </nav>

        {{-- @include('flash::message') --}}

        <div class="flex-1">
            {{ $slot ?? null }}
        </div>
    </div>

    <footer class="mt-6 p-8 bg-black text-grey-light">
        <div class="max-w-lg mx-auto px-8">
            <span>&copy; {{ now()->format('Y') }} <a href="https://stitcher.io" target="_blank" rel="noopener noreferrer">stitcher.io</a></span>
            <span class="ml-4"><a href="{{ action(\App\Http\Controllers\PrivacyController::class) }}">privacy &amp; disclaimer</a></span>
        </div>
    </footer>
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.analytics_id') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', '{{ config('app.analytics_id') }}');
</script>
</body>
</html>

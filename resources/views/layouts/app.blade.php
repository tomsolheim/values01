<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        @endif
        @livewireStyles
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-white border-bottom">
            <div class="container">
                <a class="navbar-brand fw-semibold" href="{{ url('/') }}">
                    {{ config('app.name', 'Values01') }}
                </a>
            </div>
        </nav>

        <main class="container py-5">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @livewireScripts

        @unless (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        @endunless
    </body>
</html>

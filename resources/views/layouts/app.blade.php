<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title.' · '.config('app.name') : config('app.name', 'Nakasero Market') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-gray-50 text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900">{{ $header }}</h1>
                        @isset($subheader)
                            <p class="mt-1 text-sm text-gray-600">{{ $subheader }}</p>
                        @endisset
                    </div>
                </header>
            @endisset

            <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @if (session('status') && ! is_array(session('status')))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>

            <x-footer />
        </div>
        @stack('scripts')
    </body>
</html>

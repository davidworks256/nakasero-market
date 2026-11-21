<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title.' · '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-gray-50 text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <a href="/" class="flex flex-col items-center group">
                <div class="w-12 h-12 rounded-2xl bg-black text-white flex items-center justify-center shadow-md group-hover:scale-105 transition-transform duration-200 mb-2">
                    <x-application-logo class="w-7 h-7 text-white" />
                </div>
                <div class="text-center leading-tight">
                    <span class="block text-2xl font-bold text-gray-900 tracking-tight">Nakasero</span>
                    <span class="block mt-0.5 text-[11px] font-semibold uppercase tracking-widest text-gray-500">Seller–Buyer Digital Marketplace</span>
                </div>
            </a>

            <div class="w-full sm:max-w-lg mt-6 px-6 py-6 sm:py-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                @isset($title)
                    <h1 class="text-lg font-semibold text-gray-900">{{ $title }}</h1>
                @endisset
                @isset($subtitle)
                    <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
                @endisset
                <div class="mt-5">
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-6 text-xs text-gray-500">Connecting Nakasero sellers directly with buyers.</p>
        </div>
    </body>
</html>

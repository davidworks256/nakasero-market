@props(['title' => 'Admin'])

@php
    $nav = [
        ['label' => 'Overview', 'route' => 'admin.overview', 'key' => 'overview', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
        ['label' => 'Users', 'route' => 'admin.users', 'key' => 'users', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-1.123C19.42 16.377 16.494 15 13.5 15h-2c-2.994 0-5.92 1.377-8.246 3.377A9.337 9.337 0 007.375 19.5c.94 0 1.837-.14 2.625-.372M16.5 6.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM20.25 9.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM3.75 9.75a3.75 3.75 0 107.5 0 3.75 3.75 0 00-7.5 0z'],
        ['label' => 'Listings', 'route' => 'admin.listings', 'key' => 'listings', 'icon' => 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'key' => 'categories', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · {{ config('app.name', 'Nakasero Market') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-gray-50 text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col">
            {{-- Admin top bar --}}
            <div class="bg-black text-white sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.overview') }}" class="leading-tight shrink-0">
                        <span class="block font-bold text-white">Nakasero <span class="text-gray-400 font-medium">/ Admin</span></span>
                        <span class="block text-[10px] font-semibold uppercase tracking-widest text-gray-400">Farmer–Buyer Digital Marketplace</span>
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="hidden sm:inline text-sm text-gray-300 hover:text-white">View market</a>
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 focus:outline-none">
                                    <span class="max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log out</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <div class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col lg:flex-row gap-6">
                {{-- Sidebar --}}
                <aside class="lg:w-60 shrink-0">
                    <nav class="bg-white border border-gray-200 rounded-lg p-2 lg:sticky lg:top-20">
                        <ul class="flex lg:flex-col gap-1 overflow-x-auto no-scrollbar">
                            @foreach ($nav as $item)
                                <li class="shrink-0 grow lg:grow-0">
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap transition
                                              {{ request()->routeIs($item['route']) ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                        </svg>
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="hidden lg:block mt-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                </svg>
                                Public market
                            </a>
                        </div>
                    </nav>
                </aside>

                {{-- Content --}}
                <main class="flex-1 min-w-0">
                    @isset($header)
                        <div class="mb-5">
                            <h1 class="text-lg sm:text-xl font-bold text-gray-900">{{ $header }}</h1>
                            @isset($subheader)
                                <p class="mt-1 text-sm text-gray-600">{{ $subheader }}</p>
                            @endisset
                        </div>
                    @endisset

                    @if (session('status') && ! is_array(session('status')))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>

            <x-footer />
        </div>
        @stack('scripts')
    </body>
</html>

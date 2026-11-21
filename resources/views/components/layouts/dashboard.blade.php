@props(['title' => 'Dashboard'])

@php
    $user = auth()->user();
    $unreadMessages = \App\Models\Message::whereNull('read_at')
        ->where('sender_id', '!=', $user->id)
        ->whereIn('conversation_id', \App\Models\Conversation::forUser($user)->select('id'))
        ->count();

    if ($user->isFarmer()) {
        $pendingOrders = $user->farmerOrders()->where('status', 'requested')->count();
        $navItems = [
            [
                'label' => 'Seller Dashboard',
                'route' => 'farmer.dashboard',
                'active' => request()->routeIs('farmer.dashboard'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
            ],
            [
                'label' => 'My Listings',
                'route' => 'farmer.listings.index',
                'active' => request()->routeIs('farmer.listings.*') && !request()->routeIs('farmer.listings.create'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            ],
            [
                'label' => '+ New Listing',
                'route' => 'farmer.listings.create',
                'active' => request()->routeIs('farmer.listings.create'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            ],
            [
                'label' => 'Sales & Orders',
                'route' => 'orders.index',
                'active' => request()->routeIs('orders.*'),
                'badge' => $pendingOrders > 0 ? $pendingOrders : null,
                'badgeColor' => 'bg-yellow-500 text-white',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
            ],
            [
                'label' => 'Messages',
                'route' => 'inbox.index',
                'active' => request()->routeIs('inbox.*'),
                'badge' => $unreadMessages > 0 ? ($unreadMessages > 99 ? '99+' : $unreadMessages) : null,
                'badgeColor' => 'bg-red-500 text-white',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.9-3.8A7.92 7.92 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            ],
            [
                'label' => 'Stall Profile',
                'route' => 'profile.edit',
                'active' => request()->routeIs('profile.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
            ],
            [
                'label' => 'Browse Market',
                'route' => 'listings.index',
                'active' => false,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            ],
        ];
    } elseif ($user->isBuyer()) {
        $myOrdersCount = $user->buyerOrders()->count();
        $navItems = [
            [
                'label' => 'Buyer Dashboard',
                'route' => 'buyer.dashboard',
                'active' => request()->routeIs('buyer.dashboard'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
            ],
            [
                'label' => 'Browse Produce',
                'route' => 'listings.index',
                'active' => request()->routeIs('listings.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            ],
            [
                'label' => 'Compare Prices',
                'route' => 'compare',
                'active' => request()->routeIs('compare'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>',
            ],
            [
                'label' => 'My Orders',
                'route' => 'orders.index',
                'active' => request()->routeIs('orders.*'),
                'badge' => $myOrdersCount > 0 ? $myOrdersCount : null,
                'badgeColor' => 'bg-gray-200 text-gray-800',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            ],
            [
                'label' => 'Messages',
                'route' => 'inbox.index',
                'active' => request()->routeIs('inbox.*'),
                'badge' => $unreadMessages > 0 ? ($unreadMessages > 99 ? '99+' : $unreadMessages) : null,
                'badgeColor' => 'bg-red-500 text-white',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.9-3.8A7.92 7.92 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            ],
            [
                'label' => 'Account Profile',
                'route' => 'profile.edit',
                'active' => request()->routeIs('profile.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
            ],
        ];
    } else {
        // Admin
        $navItems = [
            [
                'label' => 'Admin Overview',
                'route' => 'admin.overview',
                'active' => request()->routeIs('admin.overview'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
            ],
            [
                'label' => 'Users & Sellers',
                'route' => 'admin.users',
                'active' => request()->routeIs('admin.users'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
            ],
            [
                'label' => 'Produce Listings',
                'route' => 'admin.listings',
                'active' => request()->routeIs('admin.listings'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
            ],
            [
                'label' => 'Categories',
                'route' => 'admin.categories.index',
                'active' => request()->routeIs('admin.categories.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
            ],
        ];
    }
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
            {{-- Navigation Bar --}}
            @include('layouts.navigation')

            {{-- Optional Page Header --}}
            @isset($header)
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-xl font-bold text-gray-900">{{ $header }}</h1>
                        @isset($subheader)
                            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">{{ $subheader }}</p>
                        @endisset
                    </div>
                </header>
            @endisset

            {{-- Main Dashboard Shell with Sidebar --}}
            <div class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col lg:flex-row gap-6">
                {{-- Responsive Sidebar --}}
                <aside class="lg:w-64 shrink-0">
                    {{-- User Profile Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-black text-white font-bold flex items-center justify-center shrink-0 text-base shadow">
                                {{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                <div class="mt-0.5">
                                    @if ($user->isFarmer())
                                        @if ($user->isVerifiedFarmer())
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded-full border border-green-200">
                                                ✓ Verified Seller
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-yellow-800 bg-yellow-50 px-2 py-0.5 rounded-full border border-yellow-200">
                                                ⏳ Pending Seller
                                            </span>
                                        @endif
                                    @elseif ($user->isAdmin())
                                        <span class="inline-flex items-center text-[11px] font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">
                                            👑 Market Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                            🛒 Produce Buyer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($user->isFarmer() && $user->farmerProfile)
                            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                                <span class="font-medium text-gray-500">Stall:</span>
                                <span class="font-semibold text-gray-900 truncate ml-2">{{ $user->farmerProfile->stall }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Navigation Links --}}
                    <nav class="bg-white border border-gray-200 rounded-xl p-2.5 shadow-sm lg:sticky lg:top-20">
                        <p class="px-3 pt-1 pb-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Navigation</p>
                        <ul class="flex lg:flex-col gap-1.5 overflow-x-auto no-scrollbar">
                            @foreach ($navItems as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 whitespace-nowrap {{ $item['active'] ? 'bg-black text-white font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-black font-medium' }}">
                                        <span class="flex items-center gap-2.5">
                                            <svg class="w-5 h-5 shrink-0 {{ $item['active'] ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $item['icon'] !!}
                                            </svg>
                                            <span>{{ $item['label'] }}</span>
                                        </span>
                                        @if (!empty($item['badge']))
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $item['badgeColor'] ?? 'bg-black text-white' }}">
                                                {{ $item['badge'] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </aside>

                {{-- Main Dashboard Content Area --}}
                <main class="flex-1 min-w-0">
                    @if (session('status') && ! is_array(session('status')))
                        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>

            {{-- Footer --}}
            <x-footer />
        </div>
        @stack('scripts')
    </body>
</html>

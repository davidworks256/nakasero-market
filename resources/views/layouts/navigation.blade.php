<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white border-b border-gray-200">
    {{-- Top bar --}}
    <div class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-9 text-xs sm:text-sm">
                <div class="flex items-center gap-4 sm:gap-6 overflow-x-auto no-scrollbar">
                    <a href="{{ route('listings.index') }}" class="hover:text-gray-300 whitespace-nowrap">Browse produce</a>
                    <a href="{{ route('compare') }}" class="hover:text-gray-300 whitespace-nowrap">Compare prices</a>
                    @auth
                        @if (auth()->user()->isFarmer())
                            <a href="{{ route('farmer.listings.index') }}" class="hover:text-gray-300 whitespace-nowrap">My listings</a>
                            <a href="{{ route('orders.index') }}" class="hover:text-gray-300 whitespace-nowrap">Sales</a>
                        @elseif (auth()->user()->isBuyer())
                            <a href="{{ route('orders.index') }}" class="hover:text-gray-300 whitespace-nowrap">My orders</a>
                        @else
                            <a href="{{ route('admin.overview') }}" class="hover:text-gray-300 whitespace-nowrap">Admin panel</a>
                        @endif
                    @endauth
                </div>
                <div class="hidden md:flex items-center gap-4 shrink-0 ml-4">
                    <a href="{{ route('privacy') }}" class="hover:text-gray-300">Privacy</a>
                    @guest
                        <a href="{{ route('register') }}" class="hover:text-gray-300">Sell/Buy</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 sm:h-16 gap-3 sm:gap-4">
            <a href="/" class="shrink-0 flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                    <x-application-logo class="w-6 h-6 text-white" />
                </div>
                <div class="leading-tight">
                    <span class="block text-lg sm:text-xl font-bold text-gray-900 tracking-tight">Nakasero</span>
                    <span class="hidden sm:block text-[10px] font-semibold uppercase tracking-widest text-gray-500">Seller–Buyer Digital Marketplace</span>
                    <span class="sm:hidden block text-[10px] font-medium text-gray-500">Marketplace</span>
                </div>
            </a>

            <form action="{{ route('listings.index') }}" method="GET" class="hidden md:block flex-1 max-w-2xl">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search matooke, tomatoes, maize..."
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black text-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>

            <div class="flex items-center gap-2 shrink-0">
                @auth
                    @if (auth()->user()->isBuyer() || auth()->user()->isFarmer())
                        <a href="{{ route('inbox.index') }}" class="relative p-2 text-gray-600 hover:text-gray-900" title="Messages">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.9-3.8A7.92 7.92 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            @php($unread = \App\Models\Message::whereNull('read_at')
                                ->where('sender_id', '!=', auth()->id())
                                ->whereIn('conversation_id', \App\Models\Conversation::forUser(auth()->user())->select('id'))
                                ->count())
                            @if ($unread > 0)
                                <span class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold">{{ $unread > 99 ? '99+' : $unread }}</span>
                            @endif
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-black">
                                <span class="max-w-[10rem] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                            </div>
                            <x-dropdown-link :href="route(auth()->user()->dashboardRoute())">Dashboard</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-black rounded-lg hover:bg-gray-800">Sign up</a>
                @endauth

                <button class="md:hidden p-2 text-gray-600" @click="open = ! open" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="! open">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="open" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile panel --}}
    <div x-show="open" x-cloak class="md:hidden border-t border-gray-200 bg-white px-4 pt-3 pb-4 space-y-3">
        <form action="{{ route('listings.index') }}" method="GET">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search produce..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black text-sm">
        </form>
        <a href="{{ route('listings.index') }}" class="block py-2 text-sm font-medium text-gray-700">Browse produce</a>
        <a href="{{ route('compare') }}" class="block py-2 text-sm font-medium text-gray-700">Compare prices</a>
        @auth
            <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="block py-2 text-sm font-medium text-gray-700">Dashboard</a>
            @if (auth()->user()->isBuyer() || auth()->user()->isFarmer())
                <a href="{{ route('inbox.index') }}" class="block py-2 text-sm font-medium text-gray-700">Messages</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="block py-2 text-sm font-medium text-gray-700">Log in</a>
            <a href="{{ route('register') }}" class="block py-2 text-sm font-semibold text-black underline">Sign up</a>
        @endauth
    </div>
</nav>

<footer class="bg-black text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4 text-sm">
            {{-- Brand Column --}}
            <div>
                <a href="/" class="flex items-center gap-2.5 group inline-block">
                    <div class="w-9 h-9 rounded-xl bg-white text-black flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <x-application-logo class="w-5 h-5 text-black" />
                    </div>
                    <div>
                        <p class="text-lg font-bold leading-tight text-white">Nakasero</p>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Seller–Buyer Marketplace</p>
                    </div>
                </a>
                <p class="mt-4 text-xs sm:text-sm text-gray-400 leading-relaxed">Fresh produce, straight from the seller. Connecting the traders of Nakasero Market with buyers — no middlemen, honest prices in UGX.</p>
            </div>

            {{-- Marketplace Links --}}
            <div>
                <p class="font-semibold text-white tracking-wide">Marketplace</p>
                <ul class="mt-3 space-y-2 text-sm text-gray-300">
                    <li><a href="{{ route('listings.index') }}" class="hover:text-white transition-colors">Browse produce</a></li>
                    <li><a href="{{ route('compare') }}" class="hover:text-white transition-colors">Compare prices</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Become a Seller</a></li>
                    <li><a href="{{ route('register', ['role' => 'buyer']) }}" class="hover:text-white transition-colors">Buy in bulk</a></li>
                </ul>
            </div>

            {{-- Your Account Links --}}
            <div>
                <p class="font-semibold text-white tracking-wide">Your Account</p>
                <ul class="mt-3 space-y-2 text-sm text-gray-300">
                    @auth
                        <li><a href="{{ route(auth()->user()->dashboardRoute()) }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        @if (auth()->user()->isBuyer() || auth()->user()->isFarmer())
                            <li><a href="{{ route('inbox.index') }}" class="hover:text-white transition-colors">Messages</a></li>
                            <li><a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">Orders & Sales</a></li>
                        @endif
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition-colors">Profile &amp; Settings</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Log in</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Create an account</a></li>
                    @endauth
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy notice</a></li>
                </ul>
            </div>

            {{-- Market Office Column --}}
            <div>
                <p class="font-semibold text-white tracking-wide">Market Office</p>
                <ul class="mt-3 space-y-2 text-xs sm:text-sm text-gray-300">
                    <li class="flex items-start gap-2">
                        <span class="text-gray-400">📍</span>
                        <span>Nakasero Market, Kabyoke–Kisenyi Road, Kampala, Uganda</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-gray-400">📞</span>
                        <a href="tel:+256414234567" class="hover:text-white underline">+256 414 234 567</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-gray-400">✉️</span>
                        <a href="mailto:office@nakasero.test" class="hover:text-white underline">office@nakasero.test</a>
                    </li>
                    <li class="flex items-center gap-2 text-xs text-gray-400 pt-1">
                        <span>🕒</span>
                        <span>Mon – Sat: 6:00 AM – 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-10 border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400">
            <p>© {{ date('Y') }} Nakasero Seller–Buyer Digital Marketplace. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="{{ route('listings.index') }}" class="hover:text-white transition-colors">Browse Produce</a>
                <a href="{{ route('compare') }}" class="hover:text-white transition-colors">Compare Prices</a>
                <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Notice</a>
            </div>
        </div>
    </div>
</footer>

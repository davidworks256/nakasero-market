<x-app-layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-black text-white rounded-xl">
        <img src="{{ asset('images/hero.jpg') }}" alt="Nakasero Market produce stalls" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative z-10 px-6 py-12 sm:py-16 text-center">
            <h1 class="text-2xl sm:text-4xl font-bold max-w-2xl mx-auto">Fresh from Nakasero Market, straight to your phone</h1>
        <p class="mt-3 text-gray-300 max-w-xl mx-auto text-sm sm:text-base">
            Sellers list their produce with honest prices. Buyers search, compare and contact them directly — no middlemen.
        </p>
        <form action="{{ route('listings.index') }}" method="GET" class="mt-6 max-w-xl mx-auto flex gap-2">
            <input type="search" name="search" placeholder="Search matooke, tomatoes, maize..."
                   class="flex-1 rounded-lg px-4 py-3 text-sm text-gray-900 bg-white border-0 focus:outline-none focus:ring-2 focus:ring-white">
            <button class="px-5 py-3 bg-black text-white border border-white font-semibold text-sm rounded-lg hover:bg-gray-800">Search</button>
        </form>
        <div class="mt-6 flex flex-wrap justify-center gap-3 text-sm">
            <a href="{{ route('register') }}?role=farmer" class="px-5 py-2.5 bg-black text-white border border-white rounded-lg font-semibold hover:bg-gray-800 transition">I am a seller — list produce</a>
            <a href="{{ route('listings.index') }}" class="px-5 py-2.5 bg-white text-black rounded-lg font-semibold hover:bg-gray-200 transition">Browse as a buyer</a>
        </div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="mt-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Explore Categories</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Fresh produce sorted by harvest category</p>
                </div>
                <a href="{{ route('listings.index') }}" class="text-xs sm:text-sm font-semibold text-gray-700 hover:text-black">View all →</a>
            </div>
            <div class="mt-6 grid grid-cols-3 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-4 sm:gap-6 text-center">
                @foreach ($categories as $category)
                    @php($slug = \Illuminate\Support\Str::slug($category->name))
                    @php($hasImage = file_exists(public_path('images/categories/'.$slug.'.jpg')))
                    <a href="{{ route('listings.index', ['category' => $category->id]) }}" class="group flex flex-col items-center">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full border-2 border-gray-200 bg-white group-hover:border-black group-hover:shadow-lg transition-all duration-300 p-1 flex items-center justify-center overflow-hidden">
                            @if ($hasImage)
                                <img src="{{ asset('images/categories/'.$slug.'.jpg') }}" alt="{{ $category->name }}" loading="lazy"
                                     class="w-full h-full rounded-full object-cover group-hover:scale-110 transition duration-300">
                            @else
                                <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center text-gray-700 group-hover:bg-black group-hover:text-white transition-colors duration-200">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if (str_contains($slug, 'vegetable') || str_contains($slug, 'herb'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        @elseif (str_contains($slug, 'fruit'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9 9 0 100-18 9 9 0 000 18zM12 3v3m0 0a3 3 0 013 3" />
                                        @elseif (str_contains($slug, 'grain') || str_contains($slug, 'legume') || str_contains($slug, 'pulse'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m-6-12l6-6 6 6m-12 6l6-6 6 6" />
                                        @elseif (str_contains($slug, 'fish') || str_contains($slug, 'seafood'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12c4 4 10 4 14 0-4-4-10-4-14 0zm14 0l4-3v6l-4-3z" />
                                        @elseif (str_contains($slug, 'poultry') || str_contains($slug, 'egg'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3c-4.5 0-7 5.5-7 10 0 4.5 3.134 8 7 8s7-3.5 7-8c0-4.5-2.5-10-7-10z" />
                                        @elseif (str_contains($slug, 'tuber') || str_contains($slug, 'root'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v4m0 0c-4 0-7 3-7 7 0 5 7 7 7 7s7-2 7-7c0-4-3-7-7-7z" />
                                        @elseif (str_contains($slug, 'dairy') || str_contains($slug, 'honey'))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        @endif
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <p class="mt-2 text-xs sm:text-sm font-semibold text-gray-900 group-hover:text-black line-clamp-1 max-w-[100px] sm:max-w-[120px]">{{ $category->name }}</p>
                        <span class="text-[11px] text-gray-500">{{ $category->listings_count }} {{ Str::plural('item', $category->listings_count) }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Fresh listings --}}
    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Fresh on the market</h2>
            <a href="{{ route('listings.index') }}" class="text-sm font-medium text-gray-600 hover:text-black">More produce →</a>
        </div>
        @if ($fresh->isEmpty())
            <p class="mt-4 text-sm text-gray-500">No listings yet. Verified sellers' produce will appear here.</p>
        @else
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($fresh as $listing)
                    <x-listing-card :listing="$listing" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Why choose us --}}
    <section class="mt-14">
        <div class="text-center max-w-xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900">Why choose us</h2>
            <p class="mt-1 text-sm text-gray-600">The premier digital trading bridge for Kampala's central market</p>
        </div>
        <div class="mt-6 grid sm:grid-cols-3 gap-5 text-center">
            {{-- Direct from sellers --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 hover:shadow-md hover:border-gray-300 transition group flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-base">Direct from sellers</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Contact verified Nakasero sellers directly. Agree on honest farm prices face-to-face with zero brokers or middlemen markups.</p>
            </div>

            {{-- Compare before you buy --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 hover:shadow-md hover:border-gray-300 transition group flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-base">Compare before you buy</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Put up to three listings side-by-side to instantly evaluate wholesale rates, harvest freshness, and stall locations.</p>
            </div>

            {{-- Track your orders --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 hover:shadow-md hover:border-gray-300 transition group flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-base">Track your orders</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">Every produce request, acceptance, and completion is safely timestamped on an immutable ledger for both parties.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mt-12 bg-gray-100 border border-gray-200 rounded-xl px-6 py-10 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Sell on Nakasero Market</h2>
        <p class="mt-2 text-sm text-gray-600 max-w-lg mx-auto">List your produce in under a minute. Buyers see your stall, prices and photos as soon as the association verifies you.</p>
        <a href="{{ route('register', ['role' => 'farmer']) }}" class="inline-block mt-5 px-6 py-3 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800">Create a seller account</a>
    </section>
</x-app-layout>

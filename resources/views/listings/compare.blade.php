<x-app-layout title="Compare Produce Prices">
    @if ($listings->isNotEmpty())
        {{-- Side-by-Side Comparison Table --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Side-by-Side Comparison</h1>
                <p class="text-sm text-gray-600">Comparing {{ $listings->count() }} produce items side by side.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('compare') }}" class="px-4 py-2 border border-black text-black text-sm font-semibold rounded-lg hover:bg-gray-100 transition">
                    Pick Different Items
                </a>
                <a href="{{ route('listings.index') }}" class="px-4 py-2 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition">
                    Browse All Produce
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
            <table class="w-full min-w-[640px] text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="p-4 text-left font-semibold text-gray-600 w-44">Attribute</th>
                        @foreach ($listings as $listing)
                            <th class="p-4 text-left min-w-[200px]">
                                <a href="{{ route('listings.show', $listing) }}" class="block group">
                                    <div class="aspect-[4/3] bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                        @if ($listing->images->isNotEmpty())
                                            <img src="{{ $listing->images->first()->url() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                                        @endif
                                    </div>
                                    <span class="mt-2.5 block font-bold text-gray-900 group-hover:underline text-base line-clamp-2">{{ $listing->title }}</span>
                                </a>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php($cheapest = $listings->min('price'))
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Price</td>
                        @foreach ($listings as $listing)
                            <td class="p-4">
                                <span class="text-lg font-extrabold {{ $listing->price === $cheapest ? 'text-green-700' : 'text-gray-900' }}">
                                    {{ $listing->formattedPrice() }}
                                </span>
                                <span class="text-xs text-gray-500"> / {{ $listing->unit }}</span>
                                @if ($listing->price === $cheapest)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-800 border border-green-200 text-xs font-semibold rounded-full">
                                        Lowest price
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Quantity Available</td>
                        @foreach ($listings as $listing)
                            <td class="p-4 text-gray-900 font-medium">
                                {{ number_format($listing->quantity_available) }} {{ $listing->unit }}
                            </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Category</td>
                        @foreach ($listings as $listing)
                            <td class="p-4 text-gray-900">
                                <span class="px-2.5 py-1 bg-gray-100 rounded-md text-xs font-medium text-gray-700">
                                    {{ $listing->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Location</td>
                        @foreach ($listings as $listing)
                            <td class="p-4 text-gray-800">
                                📍 {{ $listing->location }}
                            </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Seller & Stall</td>
                        @foreach ($listings as $listing)
                            <td class="p-4">
                                <p class="font-bold text-gray-900">{{ $listing->farmer->name }}</p>
                                @if ($listing->farmer->isVerifiedFarmer())
                                    <span class="inline-block mt-0.5 text-xs text-green-700 font-semibold">✓ Verified Seller</span>
                                @endif
                                <p class="text-xs text-gray-500 mt-0.5">{{ $listing->farmer->farmerProfile?->stall }}</p>
                            </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-gray-700 bg-gray-50/30">Last Updated</td>
                        @foreach ($listings as $listing)
                            <td class="p-4 text-xs text-gray-500">
                                {{ $listing->updated_at->diffForHumans() }}
                            </td>
                        @endforeach
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-4 font-semibold text-gray-700">Action</td>
                        @foreach ($listings as $listing)
                            <td class="p-4">
                                <a href="{{ route('listings.show', $listing) }}" class="block text-center px-4 py-2.5 bg-black text-white text-xs font-semibold rounded-lg hover:bg-gray-800 transition">
                                    View & Contact
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        {{-- Produce Picker for Comparison --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Compare Produce Prices</h1>
                    <p class="mt-1 text-sm text-gray-600">Select 2 or 3 produce items to evaluate rates, available quantities, and seller stalls side by side.</p>
                </div>
                <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                    Browse All Listings →
                </a>
            </div>

            {{-- Category Filter Pills --}}
            <div class="mt-6 flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                <a href="{{ route('compare') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap transition {{ !request('category') ? 'bg-black text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                    All Categories
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('compare', ['category' => $cat->id]) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap transition {{ request('category') == $cat->id ? 'bg-black text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($availableListings->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                <p class="font-semibold text-gray-900">No active produce found in this category</p>
                <p class="mt-1 text-sm text-gray-500">Try selecting another category or check back later.</p>
                <a href="{{ route('compare') }}" class="inline-block mt-4 px-4 py-2 bg-black text-white text-sm font-semibold rounded-lg">View All Produce</a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($availableListings as $listing)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col hover:shadow-md hover:border-gray-300 transition relative">
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                            <a href="{{ route('listings.show', $listing) }}" class="absolute inset-0 group">
                                @if ($listing->images->isNotEmpty())
                                    <img src="{{ $listing->images->first()->url() }}" alt="{{ $listing->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <label class="absolute top-2 right-2 z-10 flex items-center gap-1.5 px-2.5 py-1.5 bg-white/95 border border-gray-300 rounded-lg text-xs font-bold text-gray-800 cursor-pointer shadow-sm hover:border-black hover:bg-white transition">
                                <input type="checkbox" name="ids[]" value="{{ $listing->id }}" class="compare-check accent-black w-4 h-4 rounded">
                                Compare
                            </label>
                        </div>
                        <div class="p-3.5 flex flex-col flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ $listing->category?->name }}</p>
                            <a href="{{ route('listings.show', $listing) }}" class="mt-0.5 text-sm font-semibold text-gray-900 line-clamp-2 hover:underline">{{ $listing->title }}</a>
                            <p class="mt-1.5 text-base font-extrabold text-gray-900">{{ $listing->formattedPrice() }}<span class="text-xs font-normal text-gray-500"> / {{ $listing->unit }}</span></p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ number_format($listing->quantity_available) }} {{ $listing->unit }} available</p>
                            <div class="mt-auto pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
                                <span class="font-medium truncate">{{ $listing->farmer->name }}</span>
                                <span class="shrink-0 ml-2 text-gray-400">📍 {{ $listing->location }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $availableListings->links() }}</div>
        @endif

        {{-- Sticky Floating Compare Bar --}}
        <div id="compare-bar" class="hidden fixed bottom-0 inset-x-0 z-40 bg-black text-white shadow-2xl border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-white text-black font-bold text-xs flex items-center justify-center" id="compare-count">0</span>
                    <p class="text-sm font-medium">selected for comparison <span class="text-xs text-gray-400 hidden sm:inline">(select 2 or 3 items)</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <button id="compare-go" disabled class="px-5 py-2 bg-white text-black text-sm font-bold rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 transition shadow">
                        Compare Now →
                    </button>
                    <button onclick="clearCompare()" class="px-4 py-2 border border-gray-600 text-sm font-medium rounded-lg hover:border-white hover:text-white transition">
                        Clear
                    </button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        function updateCompareBar() {
            const checks = [...document.querySelectorAll('.compare-check:checked')];
            if (checks.length > 3) {
                checks.at(-1).checked = false;
                alert('You can select up to 3 produce items to compare.');
                return;
            }
            const bar = document.getElementById('compare-bar');
            if (bar) bar.classList.toggle('hidden', checks.length === 0);
            const countEl = document.getElementById('compare-count');
            if (countEl) countEl.textContent = checks.length;
            const goBtn = document.getElementById('compare-go');
            if (goBtn) goBtn.disabled = checks.length < 2;
        }

        function clearCompare() {
            document.querySelectorAll('.compare-check:checked').forEach(c => c.checked = false);
            updateCompareBar();
        }

        const goBtn = document.getElementById('compare-go');
        if (goBtn) {
            goBtn.addEventListener('click', () => {
                const ids = [...document.querySelectorAll('.compare-check:checked')].map(c => c.value);
                window.location = '{{ route('compare') }}?ids[]=' + ids.join('&ids[]=');
            });
        }

        document.addEventListener('change', (e) => {
            if (e.target && e.target.classList.contains('compare-check')) {
                updateCompareBar();
            }
        });
        document.addEventListener('DOMContentLoaded', updateCompareBar);
    </script>
    @endpush
</x-app-layout>

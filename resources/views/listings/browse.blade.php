<x-app-layout title="Browse produce">
    <div x-data="{ filtersOpen: false, selected: [] }" class="lg:grid lg:grid-cols-4 lg:gap-6">
        {{-- Filters --}}
        <aside class="lg:col-span-1">
            <button @click="filtersOpen = !filtersOpen" class="lg:hidden w-full flex items-center justify-between px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium">
                Filters
                <svg class="w-4 h-4" :class="filtersOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <form method="GET" action="{{ route('listings.index') }}" class="mt-2 lg:mt-0 space-y-4 bg-white border border-gray-200 rounded-lg p-4" :class="!filtersOpen && 'hidden lg:block'">
                <div>
                    <label class="text-sm font-medium text-gray-700" for="search">Produce</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="e.g. tomatoes"
                           class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700" for="category">Category</label>
                    <select id="category" name="category" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700" for="location">Location</label>
                    <select id="location" name="location" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                        <option value="">Anywhere around the market</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}" @selected(request('location') === $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="min_price">Min UGX</label>
                        <input id="min_price" type="number" min="0" step="100" name="min_price" value="{{ request('min_price') }}"
                               class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="max_price">Max UGX</label>
                        <input id="max_price" type="number" min="0" step="100" name="max_price" value="{{ request('max_price') }}"
                               class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700" for="sort">Sort by</label>
                    <select id="sort" name="sort" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                        <option value="newest" @selected(request('sort') === 'newest')>Newest updates</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: low to high</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: high to low</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 px-4 py-2.5 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800">Apply</button>
                    <a href="{{ route('listings.index') }}" class="px-4 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50">Clear</a>
                </div>
            </form>
        </aside>

        {{-- Results --}}
        <div class="lg:col-span-3 mt-6 lg:mt-0">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">{{ $listings->total() }} listing{{ $listings->total() === 1 ? '' : 's' }} from verified sellers</p>
                <p class="text-xs text-gray-500 hidden sm:block">Tick <span class="font-semibold">Compare</span> on up to 3 listings</p>
            </div>

            @if ($listings->isEmpty())
                <div class="mt-6 bg-white border border-gray-200 rounded-lg p-10 text-center">
                    <p class="font-semibold text-gray-900">No produce matches your filters</p>
                    <p class="mt-1 text-sm text-gray-500">Try a different search term or widen the price range.</p>
                </div>
            @else
                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($listings as $listing)
                        <x-listing-card :listing="$listing" compare />
                    @endforeach
                </div>
                <div class="mt-6">{{ $listings->links() }}</div>
            @endif
        </div>

        {{-- Compare bar --}}
        <div id="compare-bar" class="hidden fixed bottom-0 inset-x-0 z-40 bg-black text-white">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
                <p class="text-sm"><span id="compare-count">0</span> selected — compare prices side by side</p>
                <div class="flex gap-2">
                    <button id="compare-go" disabled class="px-4 py-2 bg-white text-black text-sm font-semibold rounded-lg disabled:opacity-40">Compare</button>
                    <button onclick="clearCompare()" class="px-4 py-2 border border-gray-600 text-sm rounded-lg hover:border-white">Clear</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateCompareBar() {
            const checks = [...document.querySelectorAll('.compare-check:checked')];
            if (checks.length > 3) {
                checks.at(-1).checked = false;
                return;
            }
            const bar = document.getElementById('compare-bar');
            bar.classList.toggle('hidden', checks.length === 0);
            document.getElementById('compare-count').textContent = checks.length;
            document.getElementById('compare-go').disabled = checks.length < 2;
        }

        function clearCompare() {
            document.querySelectorAll('.compare-check:checked').forEach(c => c.checked = false);
            updateCompareBar();
        }

        document.getElementById('compare-go').addEventListener('click', () => {
            const ids = [...document.querySelectorAll('.compare-check:checked')].map(c => c.value);
            window.location = '{{ route('compare') }}?ids[]=' + ids.join('&ids[]=');
        });

        document.addEventListener('change', (e) => {
            if (e.target && e.target.classList.contains('compare-check')) {
                updateCompareBar();
            }
        });
        document.addEventListener('DOMContentLoaded', updateCompareBar);
    </script>
    @endpush
</x-app-layout>

<x-app-layout :title="$listing->title">
    @php($isOwner = auth()->check() && auth()->id() === $listing->farmer_id)

    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('listings.index') }}" class="hover:text-black">Browse</a>
        <span class="mx-1">/</span>
        <a href="{{ route('listings.index', ['category' => $listing->category_id]) }}" class="hover:text-black">{{ $listing->category?->name }}</a>
        <span class="mx-1">/</span>
        <span class="text-gray-900">{{ $listing->title }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- Gallery --}}
        <div x-data="{ active: {{ $listing->images->first()?->id ?? 'null' }} }">
            <div class="aspect-[4/3] max-h-96 w-full bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
                @if ($listing->images->isNotEmpty())
                    @foreach ($listing->images as $image)
                        <img x-show="active === {{ $image->id }}" x-cloak src="{{ $image->url() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                    @endforeach
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>
            @if ($listing->images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-x-auto no-scrollbar">
                    @foreach ($listing->images as $image)
                        <button @click="active = {{ $image->id }}"
                                class="w-16 h-16 shrink-0 rounded-lg overflow-hidden border-2"
                                :class="active === {{ $image->id }} ? 'border-black' : 'border-transparent'">
                            <img src="{{ $image->url() }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ $listing->category?->name }} · {{ $listing->location }}</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $listing->title }}</h1>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $listing->isSoldOut() ? 'bg-gray-100 text-gray-600 border-gray-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                    {{ $listing->isSoldOut() ? 'Sold out' : 'Available' }}
                </span>
            </div>

            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-3xl font-bold text-gray-900">{{ $listing->formattedPrice() }}<span class="text-base font-normal text-gray-500"> per {{ $listing->unit }}</span></p>
                <p class="mt-1 text-sm text-gray-600">{{ number_format($listing->quantity_available) }} {{ $listing->unit }} available now · updated {{ $listing->updated_at->diffForHumans() }}</p>
            </div>

            @if ($listing->description)
                <p class="mt-4 text-sm text-gray-700 whitespace-pre-line">{{ $listing->description }}</p>
            @endif

            {{-- Seller --}}
            <div class="mt-6 p-4 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $listing->farmer->name }}</p>
                        <p class="text-sm text-gray-600">{{ $listing->farmer->farmerProfile?->stall }} · {{ $listing->farmer->farmerProfile?->location }}</p>
                    </div>
                    @if ($listing->farmer->isVerifiedFarmer())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Verified Seller
                        </span>
                    @endif
                </div>

                @auth
                    <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-2">
                        @if ($listing->farmer->phone)
                            <p class="flex items-center gap-2">
                                <span class="text-base">📞</span>
                                <a href="tel:{{ $listing->farmer->phone }}" class="font-semibold underline hover:text-black">{{ $listing->farmer->phone }}</a>
                            </p>
                        @endif
                        <p class="flex items-center gap-2">
                            <span class="text-base">✉️</span>
                            <a href="mailto:{{ $listing->farmer->email }}" class="font-medium underline hover:text-black">{{ $listing->farmer->email }}</a>
                        </p>
                    </div>
                @else
                    <p class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600">
                        <a href="{{ route('login') }}" class="font-semibold text-black underline hover:text-gray-700">Log in</a> or <a href="{{ route('register') }}" class="font-semibold text-black underline hover:text-gray-700">sign up</a> to view the seller's direct phone number and email.
                    </p>
                @endauth
            </div>

            {{-- Actions --}}
            <div class="mt-6 space-y-4">
                @if ($isOwner)
                    <a href="{{ route('farmer.listings.edit', $listing) }}" class="block w-full text-center px-5 py-3 bg-black text-white font-semibold text-sm rounded-lg hover:bg-gray-800">Edit this listing</a>
                @elseif (auth()->check() && auth()->user()->isBuyer())
                    @unless ($listing->isSoldOut())
                        <form method="POST" action="{{ route('buyer.order.request', $listing) }}"
                              x-data="{ qty: {{ (int) old('quantity', 1) }}, price: {{ $listing->price }} }"
                              class="p-4 bg-white border border-gray-200 rounded-lg space-y-3">
                            @csrf
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">Request an order</p>
                                <span class="text-xs text-gray-500">Rate: UGX {{ number_format($listing->price) }} / {{ $listing->unit }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-600" for="quantity">Quantity ({{ $listing->unit }})</label>
                                    <input id="quantity" type="number" name="quantity" min="1" max="{{ $listing->quantity_available }}" x-model.number="qty" required
                                           class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600">Total</label>
                                    <p class="mt-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold">UGX <span x-text="((qty || 0) * price).toLocaleString()">{{ number_format((int) old('quantity', 1) * $listing->price) }}</span></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600" for="note">Note for the seller (optional)</label>
                                <textarea id="note" name="note" rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" placeholder="e.g. I will pick up at your stall on Saturday morning">{{ old('note') }}</textarea>
                            </div>
                            <button class="w-full px-5 py-3 bg-black text-white font-semibold text-sm rounded-lg hover:bg-gray-800">Send order request</button>
                            <p class="text-xs text-gray-500">You agree the final price and payment directly with the seller. No money moves through this app.</p>
                        </form>
                    @endunless

                    <form method="POST" action="{{ route('buyer.contact', $listing) }}" class="p-4 bg-white border border-gray-200 rounded-lg space-y-3">
                        @csrf
                        <label class="text-sm font-semibold text-gray-900 block" for="body">Message the seller</label>
                        <textarea id="body" name="body" rows="3" required class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" placeholder="Is this available? Can you deliver to ...?">{{ old('body') }}</textarea>
                        <button class="w-full px-5 py-3 border border-black text-black font-semibold text-sm rounded-lg hover:bg-gray-100">Send enquiry</button>
                    </form>
                @elseif (! auth()->check())
                    <div class="flex gap-3">
                        <a href="{{ route('login') }}" class="flex-1 text-center px-5 py-3 border border-black text-black font-semibold text-sm rounded-lg hover:bg-gray-100">Log in</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-5 py-3 bg-black text-white font-semibold text-sm rounded-lg hover:bg-gray-800">Sign up to contact or order</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-lg font-bold text-gray-900">More {{ \Illuminate\Support\Str::lower($listing->category?->name ?? 'produce') }} on the market</h2>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($related as $item)
                    <x-listing-card :listing="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>

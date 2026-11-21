<x-layouts.dashboard title="My Listings">
    <x-slot name="header">My listings</x-slot>
    <x-slot name="subheader">Update prices and quantities every morning so buyers see what you really have.</x-slot>

    <div class="flex justify-between items-center">
        @if (! auth()->user()->isVerifiedFarmer())
            <p class="text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">⏳ Your account is waiting for verification — buyers cannot see your listings yet.</p>
        @endif
        <a href="{{ route('farmer.listings.create') }}" class="ml-auto px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800">+ New listing</a>
    </div>

    @if ($listings->isEmpty())
        <div class="mt-6 bg-white border border-gray-200 rounded-lg p-10 text-center">
            <p class="font-semibold text-gray-900">You have no listings yet</p>
            <p class="mt-1 text-sm text-gray-500">List your first produce — it takes under a minute.</p>
            <a href="{{ route('farmer.listings.create') }}" class="inline-block mt-4 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg">Create listing</a>
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($listings as $listing)
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row gap-4">
                    <div class="flex gap-4 flex-1 min-w-0">
                        <a href="{{ route('listings.show', $listing) }}" class="w-20 h-20 shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                            @if ($listing->images->isNotEmpty())
                                <img src="{{ $listing->images->first()->url() }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </a>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $listing->title }}</p>
                            <p class="text-sm text-gray-600">{{ $listing->category?->name }} · {{ $listing->location }}</p>
                            <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                @if ($listing->status === \App\Models\Listing::STATUS_ACTIVE)
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full font-semibold">Active</span>
                                @elseif ($listing->status === \App\Models\Listing::STATUS_SOLD_OUT)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-200 rounded-full font-semibold">Sold out</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full font-semibold">Hidden by admin</span>
                                @endif
                                <span class="px-2 py-0.5 text-gray-500">Updated {{ $listing->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('farmer.listings.stock', $listing) }}" class="flex items-end gap-2">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500">Price (UGX)</label>
                            <input type="number" name="price" value="{{ $listing->price }}" min="1" class="w-28 rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500">Qty ({{ $listing->unit }})</label>
                            <input type="number" name="quantity_available" value="{{ $listing->quantity_available }}" min="0" class="w-24 rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                        </div>
                        <button class="px-4 py-2 border border-black text-black text-sm font-semibold rounded-lg hover:bg-gray-100">Update</button>
                    </form>

                    <div class="flex sm:flex-col gap-2 justify-end">
                        <a href="{{ route('farmer.listings.edit', $listing) }}" class="px-4 py-2 text-center text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Edit</a>
                        <form method="POST" action="{{ route('farmer.listings.destroy', $listing) }}" onsubmit="return confirm('Remove this listing?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full px-4 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $listings->links() }}</div>
    @endif
</x-layouts.dashboard>

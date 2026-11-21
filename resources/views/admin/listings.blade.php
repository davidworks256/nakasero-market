<x-layouts.admin title="Listings">
    <x-slot name="header">Listings</x-slot>

    <form method="GET" class="mt-4">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
            <option value="">All statuses</option>
            @foreach (['active', 'sold_out', 'hidden'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-4 bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($listings as $listing)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('listings.show', $listing) }}" class="text-sm font-semibold text-gray-900 hover:underline">{{ $listing->title }}</a>
                    <span class="ml-2 text-sm text-gray-600">UGX {{ number_format($listing->price) }} / {{ $listing->unit }}</span>
                    <p class="text-xs text-gray-500">{{ $listing->farmer->name }} · {{ $listing->category?->name }} · {{ $listing->location }} · updated {{ $listing->updated_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full border
                    {{ $listing->status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : ($listing->status === 'hidden' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200') }}">
                    {{ str_replace('_', ' ', $listing->status) }}
                </span>
                <div class="flex flex-wrap gap-2 shrink-0">
                    @if ($listing->status !== 'hidden')
                        <form method="POST" action="{{ route('admin.listings.hide', $listing) }}">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-semibold border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Hide</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.listings.relist', $listing) }}">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-semibold border border-green-300 text-green-700 rounded-lg hover:bg-green-50">Make visible</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.listings.remove', $listing) }}" onsubmit="return confirm('Permanently delete this listing?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1.5 text-xs font-semibold border border-red-300 text-red-600 rounded-lg hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-6 text-sm text-gray-500">No listings match this filter.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $listings->links() }}</div>
</x-layouts.admin>

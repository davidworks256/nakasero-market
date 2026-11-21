@props(['listing', 'compare' => false])

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col hover:shadow-md hover:border-gray-300 transition">
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
        @if ($listing->isSoldOut())
            <span class="absolute top-2 left-2 px-2 py-1 bg-black text-white text-xs font-semibold rounded pointer-events-none">Sold out</span>
        @endif
        @if ($compare)
            <label class="absolute top-2 right-2 z-10 flex items-center gap-1.5 px-2 py-1 bg-white/95 border border-gray-200 rounded text-xs font-medium text-gray-700 cursor-pointer hover:border-black">
                <input type="checkbox" name="ids[]" value="{{ $listing->id }}" class="compare-check accent-black">
                Compare
            </label>
        @endif
    </div>
    <div class="p-3 flex flex-col flex-1">
        <p class="text-[11px] uppercase tracking-wide text-gray-500">{{ $listing->category?->name }}</p>
        <a href="{{ route('listings.show', $listing) }}" class="mt-0.5 text-sm font-semibold text-gray-900 line-clamp-2 hover:underline">{{ $listing->title }}</a>
        <p class="mt-1 text-base font-bold text-gray-900">{{ $listing->formattedPrice() }}<span class="text-xs font-normal text-gray-500"> / {{ $listing->unit }}</span></p>
        <p class="mt-1 text-xs text-gray-500">{{ $listing->quantity_available }} {{ $listing->unit }} available</p>
        <div class="mt-auto pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
            <a href="{{ route('listings.show', $listing) }}" class="truncate hover:underline">{{ $listing->farmer->name }}</a>
            <span class="shrink-0 ml-2 text-gray-400">{{ $listing->location }}</span>
        </div>
    </div>
</div>

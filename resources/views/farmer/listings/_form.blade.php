@csrf

<div class="space-y-4">
    <div>
        <x-input-label for="title" value="What are you selling?" />
        <x-text-input id="title" name="title" :value="old('title', $listing->title ?? '')" required maxlength="255" placeholder="e.g. Ripe tomatoes" />
        <x-input-error :messages="$errors->get('title')" class="mt-1" />
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="category_id" value="Category" />
            <select id="category_id" name="category_id" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $listing->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="unit" value="Sold per" />
            <select id="unit" name="unit" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                @foreach (['kg', 'bunch', 'crate', 'sack', 'dozen', 'piece', 'head'] as $unit)
                    <option value="{{ $unit }}" @selected(old('unit', $listing->unit ?? 'kg') === $unit)>{{ $unit }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('unit')" class="mt-1" />
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="price" value="Price (UGX)" />
            <x-text-input id="price" name="price" type="number" min="1" step="1" :value="old('price', $listing->price ?? '')" required placeholder="2500" />
            <x-input-error :messages="$errors->get('price')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="quantity_available" value="Quantity available" />
            <x-text-input id="quantity_available" name="quantity_available" type="number" min="0" :value="old('quantity_available', $listing->quantity_available ?? '')" required placeholder="100" />
            <x-input-error :messages="$errors->get('quantity_available')" class="mt-1" />
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="location" value="Location" />
            <x-text-input id="location" name="location" :value="old('location', $listing->location ?? auth()->user()->farmerProfile?->location ?? 'Nakasero Market')" required />
            <x-input-error :messages="$errors->get('location')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                <option value="active" @selected(old('status', $listing->status ?? 'active') === 'active')>Available for sale</option>
                <option value="sold_out" @selected(old('status', $listing->status ?? '') === 'sold_out')>Sold out</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
        </div>
    </div>

    <div>
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="3" maxlength="2000" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" placeholder="Grade, farm area, when it was harvested...">{{ old('description', $listing->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div>
        <x-input-label value="Photos (up to 5, max 2 MB each)" />
        <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-sm file:font-semibold hover:file:bg-gray-800">
        <x-input-error :messages="$errors->get('photos')" class="mt-1" />
        <x-input-error :messages="$errors->get('photos.0')" class="mt-1" />

        @isset($listing)
            @if ($listing->images->isNotEmpty())
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Current Photos (check to remove on save):</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                        @foreach ($listing->images as $image)
                            <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50 flex flex-col justify-between">
                                <div class="relative aspect-square w-full overflow-hidden bg-gray-100">
                                    <img src="{{ $image->url() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                                </div>
                                <label class="p-2 flex items-center gap-2 cursor-pointer hover:bg-red-50 text-xs text-gray-700 select-none">
                                    <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600 font-medium">Remove</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endisset
    </div>

    <div class="flex items-center gap-3 pt-2">
        <x-primary-button>{{ $submitLabel ?? 'Save listing' }}</x-primary-button>
        <a href="{{ route('farmer.listings.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Cancel</a>
    </div>
</div>

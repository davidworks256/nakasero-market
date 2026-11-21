<x-layouts.dashboard title="Edit Listing">
    <div class="max-w-2xl">
        <a href="{{ route('farmer.listings.index') }}" class="text-sm text-gray-600 hover:text-black">← My listings</a>
        <div class="mt-3 bg-white border border-gray-200 rounded-lg p-5 sm:p-6">
            <h2 class="text-base font-semibold text-gray-900">Edit "{{ $listing->title }}"</h2>
            <form method="POST" action="{{ route('farmer.listings.update', $listing) }}" enctype="multipart/form-data" class="mt-5">
                @method('PUT')
                @include('farmer.listings._form', ['submitLabel' => 'Save changes'])
            </form>
        </div>
    </div>
</x-layouts.dashboard>

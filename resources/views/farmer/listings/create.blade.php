<x-layouts.dashboard title="New Produce Listing">
    <div class="max-w-2xl">
        <a href="{{ route('farmer.listings.index') }}" class="text-sm text-gray-600 hover:text-black">← My listings</a>
        <div class="mt-3 bg-white border border-gray-200 rounded-lg p-5 sm:p-6">
            <h2 class="text-base font-semibold text-gray-900">List produce for sale</h2>
            <p class="mt-1 text-sm text-gray-500">Buyers only see your listings after the association verifies your account.</p>
            <form method="POST" action="{{ route('farmer.listings.store') }}" enctype="multipart/form-data" class="mt-5">
                @include('farmer.listings._form', ['submitLabel' => 'Publish listing'])
            </form>
        </div>
    </div>
</x-layouts.dashboard>

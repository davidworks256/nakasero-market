<x-layouts.dashboard title="Seller Dashboard">
    <x-slot name="header">Welcome, {{ auth()->user()->name }}</x-slot>
    <x-slot name="subheader">Manage your produce, replies and order requests.</x-slot>

    @unless (auth()->user()->isVerifiedFarmer())
        <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            ⏳ Your account is waiting for verification by the market association. You can prepare listings now — buyers will see them once you are verified.
        </div>
    @endunless

    <div class="grid sm:grid-cols-3 gap-4">
        @php($stats = [
            'Active listings' => auth()->user()->listings()->active()->count(),
            'Order requests' => auth()->user()->farmerOrders()->count(),
            'Pending requests' => auth()->user()->farmerOrders()->where('status', 'requested')->count(),
        ])
        @foreach ($stats as $label => $value)
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('farmer.listings.create') }}" class="bg-black text-white rounded-lg p-5 hover:bg-gray-800">
            <p class="font-semibold">+ New listing</p>
            <p class="mt-1 text-xs text-gray-300">List today's produce with photo and price.</p>
        </a>
        <a href="{{ route('farmer.listings.index') }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-black">
            <p class="font-semibold text-gray-900">My listings</p>
            <p class="mt-1 text-xs text-gray-500">Update prices and quantities quickly.</p>
        </a>
        <a href="{{ route('inbox.index') }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-black">
            <p class="font-semibold text-gray-900">Messages</p>
            <p class="mt-1 text-xs text-gray-500">Reply to buyer enquiries.</p>
        </a>
        <a href="{{ route('orders.index') }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-black">
            <p class="font-semibold text-gray-900">Sales & requests</p>
            <p class="mt-1 text-xs text-gray-500">Accept or decline order requests.</p>
        </a>
    </div>
</x-layouts.dashboard>

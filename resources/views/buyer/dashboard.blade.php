<x-layouts.dashboard title="Buyer Dashboard">
    <x-slot name="header">Welcome, {{ auth()->user()->name }}</x-slot>
    <x-slot name="subheader">Browse fresh produce from verified Nakasero sellers, compare prices and order directly.</x-slot>

    <div class="grid sm:grid-cols-3 gap-4">
        @php($stats = [
            'My order requests' => auth()->user()->buyerOrders()->count(),
            'Awaiting seller reply' => auth()->user()->buyerOrders()->where('status', 'requested')->count(),
            'Completed' => auth()->user()->buyerOrders()->where('status', 'completed')->count(),
        ])
        @foreach ($stats as $label => $value)
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid sm:grid-cols-3 gap-4">
        <a href="{{ route('listings.index') }}" class="bg-black text-white rounded-lg p-5 hover:bg-gray-800">
            <p class="font-semibold">Browse produce</p>
            <p class="mt-1 text-xs text-gray-300">Search, filter and compare listings.</p>
        </a>
        <a href="{{ route('orders.index') }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-black">
            <p class="font-semibold text-gray-900">My orders</p>
            <p class="mt-1 text-xs text-gray-500">Track requests and their status.</p>
        </a>
        <a href="{{ route('inbox.index') }}" class="bg-white border border-gray-200 rounded-lg p-5 hover:border-black">
            <p class="font-semibold text-gray-900">Messages</p>
            <p class="mt-1 text-xs text-gray-500">Chat with the sellers you contacted.</p>
        </a>
    </div>
</x-layouts.dashboard>

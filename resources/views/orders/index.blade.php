<x-layouts.dashboard :title="$title">
    <x-slot name="header">{{ $title }}</x-slot>

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">Requests are agreed and paid directly between buyer and seller.</p>
        <a href="{{ route('orders.export') }}" class="px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Export CSV</a>
    </div>

    @if ($orders->isEmpty())
        <div class="mt-6 bg-white border border-gray-200 rounded-lg p-10 text-center">
            <p class="font-semibold text-gray-900">No orders yet</p>
            <p class="mt-1 text-sm text-gray-500">Order requests and their status history will appear here.</p>
            @if (auth()->user()->isBuyer())
                <a href="{{ route('listings.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg">Browse produce</a>
            @endif
        </div>
    @else
        <div class="mt-4 bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            Order #{{ $order->id }} · {{ $order->listing?->title ?? 'Listing removed' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ number_format($order->quantity) }} {{ $order->listing?->unit ?? '' }} × UGX {{ number_format($order->unit_price) }}
                            = <span class="font-semibold text-gray-700">UGX {{ number_format($order->totalPrice()) }}</span>
                            · {{ $order->created_at->format('M j, Y') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ auth()->user()->isBuyer() ? 'Seller: '.($order->farmer?->name ?? '—') : 'Buyer: '.($order->buyer?->name ?? '—') }}
                        </p>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts.dashboard>

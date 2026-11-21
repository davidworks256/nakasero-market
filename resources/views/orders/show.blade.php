<x-layouts.dashboard :title="'Order #'.$order->id">
    @php($isBuyer = auth()->id() === $order->buyer_id)

    <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-black">← {{ $isBuyer ? 'My orders' : 'Sales' }}</a>

    <div class="mt-3 grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Order #{{ $order->id }}</h2>
                        <p class="text-sm text-gray-600">Placed {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
                </div>

                @if ($order->listing)
                    <div class="mt-4 flex gap-4">
                        <a href="{{ route('listings.show', $order->listing) }}" class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                            @if ($order->listing->images->isNotEmpty())
                                <img src="{{ $order->listing->images->first()->url() }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </a>
                        <div class="text-sm">
                            <a href="{{ route('listings.show', $order->listing) }}" class="font-semibold text-gray-900 hover:underline">{{ $order->listing->title }}</a>
                            <p class="text-gray-600">{{ number_format($order->quantity) }} {{ $order->listing->unit }} × UGX {{ number_format($order->unit_price) }}</p>
                            <p class="mt-1 font-bold text-gray-900">Total: UGX {{ number_format($order->totalPrice()) }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-500">This listing has been removed by the seller.</p>
                @endif

                @if ($order->note)
                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Note</p>
                        <p class="mt-1 whitespace-pre-line">{{ $order->note }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-gray-900">Actions</h3>
                @php
                    $actions = [];
                    if ($order->status === \App\Enums\OrderStatus::Requested) {
                        if (! $isBuyer) {
                            $actions[] = ['accept', 'Accept request', 'bg-black text-white'];
                            $actions[] = ['decline', 'Decline', 'border border-red-300 text-red-600 hover:bg-red-50'];
                        }
                        $actions[] = ['cancel', 'Cancel request', 'border border-gray-300 text-gray-700 hover:bg-gray-50'];
                    } elseif ($order->status === \App\Enums\OrderStatus::Accepted) {
                        if (! $isBuyer) {
                            $actions[] = ['complete', 'Mark as completed', 'bg-black text-white'];
                        }
                        $actions[] = ['cancel', 'Cancel order', 'border border-gray-300 text-gray-700 hover:bg-gray-50'];
                    }
                @endphp
                @forelse ($actions as [$action, $label, $classes])
                    <form method="POST" action="{{ route('orders.update', [$order, $action]) }}" class="inline">
                        @csrf
                        <button class="mt-2 mr-2 px-4 py-2 text-sm font-semibold rounded-lg {{ $classes }}">{{ $label }}</button>
                    </form>
                @empty
                    <p class="mt-2 text-sm text-gray-500">No further actions for this order.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-gray-900">People</h3>
                @foreach ([['Buyer', $order->buyer], ['Seller', $order->farmer]] as [$label, $person])
                    <div class="mt-3">
                        <p class="text-xs uppercase text-gray-500">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-900">{{ $person?->name ?? 'Deleted account' }}</p>
                        @if ($person?->phone)
                            <a href="tel:{{ $person->phone }}" class="text-sm text-gray-600 underline hover:text-black">{{ $person->phone }}</a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h3 class="text-sm font-semibold text-gray-900">Payment reminder</h3>
                <p class="mt-2 text-sm text-gray-600">Agree the final price, delivery and payment directly with each other. This app only records the request and its status.</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>

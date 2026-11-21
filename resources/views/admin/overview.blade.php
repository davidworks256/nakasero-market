<x-layouts.admin title="Admin">
    <x-slot name="header">Market overview</x-slot>

    <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['Farmers', $counts['farmers'], 'farmer'],
            ['Awaiting verification', $counts['unverified'], 'unverified'],
            ['Buyers', $counts['buyers'], 'buyer'],
            ['Active listings', $counts['active_listings'].' / '.$counts['listings'], 'listings'],
            ['Order requests', $counts['orders'], 'orders'],
            ['Completed orders', $counts['completed_orders'], 'completed'],
            ['Conversations', $counts['messages'], 'conversations'],
            ['Suspended accounts', \App\Models\User::where('status', 'suspended')->count(), 'suspended'],
        ] as [$label, $value])
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-base font-semibold text-gray-900">Sellers waiting for verification</h2>
        @if ($pendingFarmers->isEmpty())
            <p class="mt-2 text-sm text-gray-500">Everyone is verified. 🎉</p>
        @else
            <div class="mt-3 bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
                @foreach ($pendingFarmers as $farmer)
                    <div class="flex items-center justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $farmer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $farmer->farmerProfile?->stall }} · {{ $farmer->farmerProfile?->location }} · joined {{ $farmer->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a href="tel:{{ $farmer->phone }}" class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">{{ $farmer->phone ?: 'No phone' }}</a>
                            <form method="POST" action="{{ route('admin.users.verify', $farmer) }}">
                                @csrf
                                <button class="px-3 py-1.5 text-xs font-semibold bg-black text-white rounded-lg hover:bg-gray-800">Verify</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>

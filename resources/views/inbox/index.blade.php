<x-layouts.dashboard title="Messages">
    <x-slot name="header">Messages</x-slot>
    <x-slot name="subheader">Your enquiries and replies from sellers and buyers.</x-slot>

    @if ($conversations->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-10 text-center">
            <p class="font-semibold text-gray-900">No conversations yet</p>
            <p class="mt-1 text-sm text-gray-500">When you contact a seller (or a buyer contacts you), the conversation appears here.</p>
            <a href="{{ route('listings.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg">Browse produce</a>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
            @foreach ($conversations as $conversation)
                @php($other = $conversation->otherParticipant(auth()->user()))
                @php($latest = $conversation->latestMessage())
                @php($unread = $conversation->unreadCountFor(auth()->user()))
                <a href="{{ route('inbox.show', $conversation) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-black text-white font-bold">{{ \Illuminate\Support\Str::substr($other->name, 0, 1) }}</span>
                    <span class="flex-1 min-w-0">
                        <span class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-gray-900 truncate">{{ $other->name }}</span>
                            @if ($latest)
                                <span class="text-xs text-gray-400 shrink-0">{{ $latest->created_at->diffForHumans(short: true) }}</span>
                            @endif
                        </span>
                        <span class="block text-xs text-gray-500 truncate">Re: {{ $conversation->listing?->title ?? 'deleted listing' }}</span>
                        <span class="block text-sm text-gray-600 truncate">{{ $latest?->body }}</span>
                    </span>
                    @if ($unread > 0)
                        <span class="shrink-0 min-w-[22px] h-[22px] px-1.5 flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $conversations->links() }}</div>
    @endif
</x-layouts.dashboard>

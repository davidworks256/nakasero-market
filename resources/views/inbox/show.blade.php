<x-layouts.dashboard title="Conversation">
    @php($other = $conversation->otherParticipant(auth()->user()))

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="flex items-center justify-between gap-3 p-4 border-b border-gray-200">
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ $other->name }} <span class="text-xs font-normal text-gray-500 capitalize">({{ $other->role }})</span></p>
                    @if ($conversation->listing)
                        <a href="{{ route('listings.show', $conversation->listing) }}" class="text-xs text-gray-600 underline hover:text-black">Re: {{ $conversation->listing->title }} — {{ $conversation->listing->formattedPrice() }}/{{ $conversation->listing->unit }}</a>
                    @else
                        <p class="text-xs text-gray-400">Listing removed</p>
                    @endif
                </div>
                @if ($other->phone)
                    <a href="tel:{{ $other->phone }}" class="shrink-0 px-3 py-2 text-xs font-semibold border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">📞 Call</a>
                @endif
            </div>

            <div id="thread" class="p-4 space-y-3 min-h-[300px] max-h-[60vh] overflow-y-auto bg-gray-50">
                @forelse ($conversation->messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-lg px-4 py-2 text-sm {{ $message->sender_id === auth()->id() ? 'bg-black text-white' : 'bg-white border border-gray-200 text-gray-900' }}">
                            <p class="whitespace-pre-line">{{ $message->body }}</p>
                            <p class="mt-1 text-[10px] {{ $message->sender_id === auth()->id() ? 'text-gray-400' : 'text-gray-400' }}">{{ $message->created_at->format('M j, g:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500 py-8">Say hello — ask about freshness, quantity or delivery.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('inbox.reply', $conversation) }}" class="p-4 border-t border-gray-200 flex gap-2 items-end">
                @csrf
                <textarea name="body" rows="2" required maxlength="2000" placeholder="Type a message..."
                          class="flex-1 rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">{{ old('body') }}</textarea>
                <button class="px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800">Send</button>
            </form>
        </div>

        @if (auth()->user()->isBuyer() && $conversation->listing && $conversation->listing->status === \App\Models\Listing::STATUS_ACTIVE)
            <a href="{{ route('listings.show', $conversation->listing) }}#order" class="block mt-4 text-center px-5 py-3 border border-black text-black font-semibold text-sm rounded-lg hover:bg-gray-100">Want to order? Send an order request from the listing page</a>
        @endif
    </div>

    @push('scripts')
    <script>
        const thread = document.getElementById('thread');
        thread.scrollTop = thread.scrollHeight;

        let lastId = {{ $conversation->messages->max('id') ?? 0 }};
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        setInterval(async () => {
            if (!document.hasFocus()) return;
            try {
                const res = await fetch('{{ route('inbox.poll', $conversation) }}?after=' + lastId, { headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                if (!res.ok) return;
                const messages = await res.json();
                for (const m of messages) {
                    if (m.id <= lastId) continue;
                    lastId = m.id;
                    const row = document.createElement('div');
                    row.className = 'flex ' + (m.mine ? 'justify-end' : 'justify-start');
                    const mineClass = m.mine ? 'bg-black text-white' : 'bg-white border border-gray-200 text-gray-900';
                    const body = document.createElement('div');
                    body.className = 'max-w-[80%] rounded-lg px-4 py-2 text-sm ' + mineClass;
                    const p = document.createElement('p');
                    p.style.whiteSpace = 'pre-line';
                    p.textContent = m.body;
                    const time = document.createElement('p');
                    time.className = 'mt-1 text-[10px] text-gray-400';
                    time.textContent = m.time;
                    body.append(p, time);
                    row.append(body);
                    thread.append(row);
                    thread.scrollTop = thread.scrollHeight;
                }
            } catch (e) { /* offline for a moment */ }
        }, 5000);
    </script>
    @endpush
</x-layouts.dashboard>

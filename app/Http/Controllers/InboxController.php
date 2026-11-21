<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Notifications\MessageReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = Conversation::forUser($request->user())
            ->with(['listing', 'buyer', 'farmer'])
            ->latest('updated_at')
            ->paginate(20);

        return view('inbox.index', compact('conversations'));
    }

    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $buyer = $request->user();
        abort_unless($buyer->isBuyer(), 403);
        abort_unless($listing->status === Listing::STATUS_ACTIVE, 404);

        $conversation = Conversation::firstOrCreate(
            ['listing_id' => $listing->id, 'buyer_id' => $buyer->id],
            ['farmer_id' => $listing->farmer_id],
        );

        if ($request->filled('body')) {
            $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);
            $message = $conversation->messages()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $buyer->id,
                'body' => $validated['body'],
            ]);
            $conversation->touch();
            $conversation->farmer->notify(new MessageReceived($message));
        }

        return redirect()->route('inbox.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->load(['listing.category', 'buyer', 'farmer', 'messages.sender']);

        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('inbox.show', compact('conversation'));
    }

    public function poll(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->where('id', '>', (int) $request->query('after', 0))
            ->get()
            ->map(fn (Message $m) => [
                'id' => $m->id,
                'body' => $m->body,
                'mine' => $m->sender_id === $request->user()->id,
                'time' => $m->created_at->format('M j, g:i A'),
            ]);

        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $messages;
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $message = $conversation->messages()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);
        $conversation->touch();

        $conversation->otherParticipant($request->user())->notify(new MessageReceived($message));

        return back();
    }
}

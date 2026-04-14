<?php
namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends \App\Http\Controllers\Controller
{
    // Tampilkan semua chat user
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with(['messages' => function($query) {
                $query->latest()->first();
            }, 'buyer', 'seller'])
            ->get()
            ->map(function($conversation) use ($userId) {
                $otherUser = $conversation->buyer_id === $userId
                    ? $conversation->seller
                    : $conversation->buyer;

                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'last_message' => $conversation->messages->first(),
                    'unread_count' => 0, // TODO: implement unread count
                ];
            });

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations
        ]);
    }

    // Buka chat dengan seller
    public function start($sellerId)
    {
        $buyerId = Auth::id();

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
        ]);

        return redirect()->route('chat.show', $conversation->id);
    }

    // Tampilkan chat
    public function show($id)
    {
        $conversation = Conversation::with('messages.sender')
            ->findOrFail($id);

        return Inertia::render('Chat/Show', [
            'conversation' => $conversation
        ]);
    }

    // Kirim pesan
    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'required'
        ]);

        Message::create([
            'conversation_id' => $id,
            'sender_id' => Auth::id(),
            'message' => $request->message
        ]);

        return back();
    }
}
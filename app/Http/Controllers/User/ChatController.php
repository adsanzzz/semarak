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
<?php
namespace App\Http\Controllers\User;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ChatController extends \App\Http\Controllers\Controller
{
    private function buildConversationList(int $userId)
    {
        return Conversation::where(function ($query) use ($userId) {
                $query->where('buyer_id', $userId)
                    ->orWhere('seller_id', $userId);
            })
            ->with(['lastMessage', 'buyer', 'seller'])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->buyer_id === $userId
                    ? $conversation->seller
                    : $conversation->buyer;

                $lastReadAt = (int) $conversation->buyer_id === (int) $userId
                    ? $conversation->buyer_last_read_at
                    : $conversation->seller_last_read_at;

                $unreadCount = Message::query()
                    ->where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $userId)
                    ->when($lastReadAt, function ($query) use ($lastReadAt) {
                        $query->where('created_at', '>', $lastReadAt);
                    })
                    ->count();

                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'order_id' => $conversation->order_id,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();
    }

    // Tampilkan semua chat user
    public function index()
    {
        $userId = Auth::id();

        $conversations = $this->buildConversationList($userId);

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'selectedConversation' => null,
        ]);
    }

    // Buka chat buyer <-> seller
    public function start(Request $request, $userId)
    {
        $request->validate([
            'order_id' => ['nullable', 'integer', Rule::exists('orders', 'id')],
            'new_room' => ['nullable', 'boolean'],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
        ]);

        $authUser = Auth::user();
        $authId = $authUser->id;
        $orderId = $request->integer('order_id');
        $forceNewRoom = $request->boolean('new_room');
        $productId = $request->integer('product_id');

        $product = null;
        if ($productId) {
            $product = Product::select('id', 'nama', 'user_id')->findOrFail($productId);
        }

        if ((int) $userId === (int) $authId) {
            return redirect()->route('chat.index');
        }

        if ($orderId) {
            $order = Order::with('product:id,user_id')->findOrFail($orderId);

            $buyerId = (int) $order->buyer_id;
            $sellerId = (int) ($order->user_id ?: optional($order->product)->user_id);

            if (!$buyerId || !$sellerId) {
                abort(422, 'Pesanan tidak valid untuk memulai chat.');
            }

            if ((int) $authId !== $buyerId && (int) $authId !== $sellerId) {
                abort(403);
            }

            // Allow opening per-order chat as long as the target user is one of the order participants.
            if ((int) $userId !== $buyerId && (int) $userId !== $sellerId) {
                abort(403);
            }

            if ($product && (int) $product->user_id !== (int) $sellerId) {
                abort(403);
            }

            if ($forceNewRoom) {
                $conversation = Conversation::create([
                    'buyer_id' => $buyerId,
                    'seller_id' => $sellerId,
                    'order_id' => $order->id,
                ]);
            } else {
                // Use one canonical room per buyer-seller pair (per store).
                $conversation = Conversation::firstOrCreate([
                    'buyer_id' => $buyerId,
                    'seller_id' => $sellerId,
                    'order_id' => null,
                ]);
            }

            $prefill = $product
                ? 'Halo kak, saya mau tanya produk: ' . $product->nama
                : null;

            return redirect()->route('chat.show', [
                'id' => $conversation->id,
                'prefill' => $prefill,
            ]);
        }

        if ((int) $authUser->role === 2) {
            // User toko memulai chat ke buyer.
            $buyerId = (int) $userId;
            $sellerId = $authId;
        } else {
            // Buyer memulai chat ke user toko.
            $buyerId = $authId;
            $sellerId = (int) $userId;
        }

        if ($product && (int) $product->user_id !== (int) $sellerId) {
            abort(403);
        }

        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'order_id' => null,
        ]);

        $prefill = $product
            ? 'Halo kak, saya mau tanya produk: ' . $product->nama
            : null;

        return redirect()->route('chat.show', [
            'id' => $conversation->id,
            'prefill' => $prefill,
        ]);
    }

    // Tampilkan chat
    public function show(Request $request, $id)
    {
        $userId = Auth::id();

        $conversation = Conversation::with([
                'buyer',
                'seller',
                'messages' => function ($query) {
                    $query->with(['sender', 'repliedMessage.sender'])->oldest();
                },
            ])
            ->findOrFail($id);

        if ((int) $conversation->buyer_id !== (int) $userId && (int) $conversation->seller_id !== (int) $userId) {
            abort(403);
        }

        $updatedReadAt = false;

        if ((int) $conversation->buyer_id === (int) $userId) {
            $conversation->buyer_last_read_at = now();
            $updatedReadAt = true;
        }

        if ((int) $conversation->seller_id === (int) $userId) {
            $conversation->seller_last_read_at = now();
            $updatedReadAt = true;
        }

        // Mark as read without changing updated_at so list order stays stable when only opening chats.
        if ($updatedReadAt) {
            $conversation->timestamps = false;
            $conversation->save();
            $conversation->timestamps = true;
        }

        $otherUser = (int) $conversation->buyer_id === (int) $userId
            ? $conversation->seller
            : $conversation->buyer;

        $selectedConversation = [
            'id' => $conversation->id,
            'other_user' => $otherUser,
            'order_id' => $conversation->order_id,
            'messages' => $conversation->messages,
        ];

        $conversations = $this->buildConversationList($userId);

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'composerPrefill' => $request->query('prefill'),
        ]);
    }

    // Kirim pesan
    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'reply_to_message_id' => [
                'nullable',
                'integer',
                Rule::exists('messages', 'id')->where(function ($query) use ($id) {
                    $query->where('conversation_id', $id);
                }),
            ],
        ]);

        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);

        if ((int) $conversation->buyer_id !== (int) $userId && (int) $conversation->seller_id !== (int) $userId) {
            abort(403);
        }

        Message::create([
            'conversation_id' => $id,
            'sender_id' => $userId,
            'message' => trim($request->message),
            'reply_to_message_id' => $request->reply_to_message_id,
        ]);

        if ((int) $conversation->buyer_id === (int) $userId) {
            $conversation->buyer_last_read_at = now();
        }

        if ((int) $conversation->seller_id === (int) $userId) {
            $conversation->seller_last_read_at = now();
        }

        $conversation->touch();
        $conversation->save();

        return back();
    }

    public function deleteAllMessages($id)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);

        if ((int) $conversation->buyer_id !== (int) $userId && (int) $conversation->seller_id !== (int) $userId) {
            abort(403);
        }

        Message::where('conversation_id', $id)->delete();

        return back();
    }

    public function deleteSelectedMessages(Request $request, $id)
    {
        $validated = $request->validate([
            'message_ids' => ['required', 'array', 'min:1'],
            'message_ids.*' => [
                'integer',
                Rule::exists('messages', 'id')->where(function ($query) use ($id) {
                    $query->where('conversation_id', $id);
                }),
            ],
        ]);

        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);

        if ((int) $conversation->buyer_id !== (int) $userId && (int) $conversation->seller_id !== (int) $userId) {
            abort(403);
        }

        Message::where('conversation_id', $id)
            ->whereIn('id', $validated['message_ids'])
            ->delete();

        return back();
    }
}
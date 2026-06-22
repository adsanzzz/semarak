<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'chat_unread_count' => fn () => $this->getUnreadChatCount($request),
        ];
    }

    private function getUnreadChatCount(Request $request): int
    {
        $user = $request->user();

        if (!$user) {
            return 0;
        }

        $isSeller = (int) $user->role === 2;
        $isBuyer = (int) $user->role === 3;

        if (!$isSeller && !$isBuyer) {
            return 0;
        }

        $ownerColumn = $isSeller ? 'seller_id' : 'buyer_id';
        $lastReadColumn = $isSeller ? 'seller_last_read_at' : 'buyer_last_read_at';

        return (int) DB::table('messages')
            ->join('conversations', 'conversations.id', '=', 'messages.conversation_id')
            ->where('messages.sender_id', '!=', $user->id)
            ->where("conversations.{$ownerColumn}", $user->id)
            ->whereRaw("messages.created_at > COALESCE(conversations.{$lastReadColumn}, '1970-01-01 00:00:00')")
            ->count();
    }
}

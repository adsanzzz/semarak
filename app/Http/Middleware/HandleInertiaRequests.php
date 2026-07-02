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
            'pending_appeals_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)(\App\Models\ProductAppeal::where('status', 'pending')->where('is_read', false)->count() +
                       \App\Models\Complaint::where('complaint_type', 'appeal_account')->whereNull('admin_reply')->where('is_read', false)->count())
                : 0,
            'new_complaints_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)(\App\Models\Complaint::where(function($q) {
                           $q->whereIn('complaint_type', ['buyer', 'report_product'])
                             ->orWhere(function($qb) {
                                 $qb->whereNull('complaint_type')
                                    ->whereHas('user', function($u) { $u->whereIn('role', [1, 3]); });
                             });
                       })->whereNotIn('id', function($sub) {
                           $sub->select('forwarded_from_id')->from('complaints')->whereNotNull('forwarded_from_id');
                       })->where('is_read', false)->count() +
                       \App\Models\Complaint::where(function($q) {
                           $q->where('complaint_type', 'seller')
                             ->orWhere(function($qb) {
                                 $qb->whereNull('complaint_type')
                                    ->whereHas('user', function($u) { $u->where('role', 2); });
                             });
                       })->whereNull('forwarded_from_id')->whereNull('admin_reply')->where('is_read', false)->count())
                : 0,
            'reported_accounts_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)\App\Models\Complaint::where(function($q) {
                           $q->where('complaint_type', 'report_account')
                             ->orWhereNotNull('reported_user_id');
                       })->where('is_read', false)->count()
                : 0,
            'pending_product_appeals_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)\App\Models\ProductAppeal::where('status', 'pending')->where('is_read', false)->count()
                : 0,
            'pending_account_appeals_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)\App\Models\Complaint::where('complaint_type', 'appeal_account')->whereNull('admin_reply')->where('is_read', false)->count()
                : 0,
            'new_buyer_complaints_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)\App\Models\Complaint::where(function($q) {
                           $q->whereIn('complaint_type', ['buyer', 'report_product'])
                             ->orWhere(function($qb) {
                                 $qb->whereNull('complaint_type')
                                    ->whereHas('user', function($u) { $u->whereIn('role', [1, 3]); });
                             });
                       })->whereNotIn('id', function($sub) {
                           $sub->select('forwarded_from_id')->from('complaints')->whereNotNull('forwarded_from_id');
                       })->where('is_read', false)->count()
                : 0,
            'new_seller_complaints_count' => fn () => $request->user() && (int)$request->user()->role === 1
                ? (int)\App\Models\Complaint::where(function($q) {
                           $q->where('complaint_type', 'seller')
                             ->orWhere(function($qb) {
                                 $qb->whereNull('complaint_type')
                                    ->whereHas('user', function($u) { $u->where('role', 2); });
                             });
                       })->whereNull('forwarded_from_id')->whereNull('admin_reply')->where('is_read', false)->count()
                : 0,
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

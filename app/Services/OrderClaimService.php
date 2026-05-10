<?php

namespace App\Services;

use App\Models\Objednavka;
use App\Models\User;
use Illuminate\Http\Request;

class OrderClaimService
{
    private const SESSION_KEY = 'claimable_order_ids';

    public function rememberOrder(Request $request, Objednavka $order): void
    {
        $orderIds = $this->sessionOrderIds($request);
        $orderIds[] = (int) $order->id;

        $request->session()->put(self::SESSION_KEY, array_values(array_unique($orderIds)));
    }

    public function claimSessionOrders(Request $request, User $user): void
    {
        if ($user->isAdmin()) {
            $request->session()->forget(self::SESSION_KEY);
            return;
        }

        $orderIds = $this->sessionOrderIds($request);

        if (empty($orderIds)) {
            return;
        }

        Objednavka::query()
            ->whereIn('id', $orderIds)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        $request->session()->forget(self::SESSION_KEY);
    }

    public function isClaimableInSession(Request $request, int $orderId): bool
    {
        return in_array($orderId, $this->sessionOrderIds($request), true);
    }

    private function sessionOrderIds(Request $request): array
    {
        return collect($request->session()->get(self::SESSION_KEY, []))
            ->map(fn ($orderId) => (int) $orderId)
            ->filter(fn ($orderId) => $orderId > 0)
            ->values()
            ->all();
    }
}

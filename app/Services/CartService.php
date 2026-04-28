<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class CartService
{
    public const CART_EXPIRY_DAYS = 30;

    public function getCart(Request $request): array
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $cart = $this->getUserCart($user);
            $request->session()->put('cart', $cart);

            return $cart;
        }

        return $this->getSessionCart($request);
    }

    public function storeCart(Request $request, array $cart): void
    {
        $normalizedCart = $this->normalizeCart($cart);

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->storeUserCart($user, $normalizedCart);
        }

        $request->session()->put('cart', $normalizedCart);
    }

    public function mergeSessionIntoUserCart(Request $request, User $user): void
    {
        $sessionCart = $this->getSessionCart($request);
        $userCart = $this->getUserCart($user);

        if (empty($sessionCart)) {
            $request->session()->put('cart', $userCart);
            return;
        }

        foreach ($sessionCart as $variantId => $quantity) {
            $currentQty = (int) ($userCart[$variantId] ?? 0);
            $mergedQty = min(99, $currentQty + (int) $quantity);
            $userCart[(int) $variantId] = $mergedQty;
        }

        $this->storeUserCart($user, $userCart);
        $request->session()->put('cart', $userCart);
    }

    private function getSessionCart(Request $request): array
    {
        return $this->normalizeCart($request->session()->get('cart', []));
    }

    private function getUserCart(User $user): array
    {
        $this->pruneExpired($user);

        return $user->cartItems()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Date::now());
            })
            ->get()
            ->pluck('quantity', 'variant_id')
            ->map(fn ($qty) => (int) $qty)
            ->toArray();
    }

    private function storeUserCart(User $user, array $cart): void
    {
        $variantIds = array_keys($cart);

        if (empty($variantIds)) {
            CartItem::where('user_id', $user->id)->delete();
            return;
        }

        $expiresAt = Date::now()->addDays(self::CART_EXPIRY_DAYS);

        foreach ($cart as $variantId => $quantity) {
            CartItem::updateOrCreate(
                ['user_id' => $user->id, 'variant_id' => (int) $variantId],
                ['quantity' => (int) $quantity, 'expires_at' => $expiresAt]
            );
        }

        CartItem::where('user_id', $user->id)
            ->whereNotIn('variant_id', $variantIds)
            ->delete();
    }

    private function pruneExpired(User $user): void
    {
        CartItem::where('user_id', $user->id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', Date::now())
            ->delete();
    }

    private function normalizeCart(array $cart): array
    {
        $normalized = [];

        foreach ($cart as $variantId => $quantity) {
            $variantId = (int) $variantId;
            $quantity = (int) $quantity;

            if ($variantId < 1 || $quantity < 1) {
                continue;
            }

            $normalized[$variantId] = $quantity;
        }

        return $normalized;
    }
}

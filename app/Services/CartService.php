<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create current session cart.
     */
    public static function getCart(): Cart
    {
        $sessionId = Session::getId();
        if (!$sessionId) {
            Session::start();
            $sessionId = Session::getId();
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add menu item to cart.
     */
    public static function addItem(int $menuId, int $quantity = 1, ?string $note = null): CartItem
    {
        $cart = self::getCart();

        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('menu_id', $menuId)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            if ($note) {
                $item->note = $note;
            }
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->cart_id,
                'menu_id' => $menuId,
                'quantity' => $quantity,
                'note' => $note,
            ]);
        }

        return $item;
    }

    /**
     * Clear the current cart.
     */
    public static function clearCart(): void
    {
        $cart = self::getCart();
        $cart->items()->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PosItem;
use App\Models\StoreShoppingCart;
use App\Models\StoreCartItem;
use App\Models\Item;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Agregar un item al carrito
     */
    public function addItem(Request $request) {
        $request->validate([
            'person_id' => 'required|exists:ospos_people,person_id',
            'item_id' => 'required|exists:ospos_items,item_id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        // Obtener o crear el carrito del usuario
        $cart = StoreShoppingCart::firstOrCreate(['person_id' => $request->person_id]);

        // Obtener el producto
        $item = PosItem::findOrFail($request->item_id);

        // Agregar el item al carrito o actualizar la cantidad si ya existe
        $cartItem = StoreCartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'item_id' => $item->item_id],
            [
                'quantity' => \DB::raw("quantity + " . ($request->quantity ?? 1)),
                'subtotal' => \DB::raw("subtotal + " . ($item->unit_price * ($request->quantity ?? 1)))
            ]
        );

        return response()->json(['message' => 'Item added to cart', 'cartItem' => $cartItem], 201);
    }


    public function createCart(Request $request) {
        $request->validate([
            'person_id' => 'required|exists:ospos_people,person_id'
        ]);

        // Verificar si el usuario ya tiene un carrito
        $cart = StoreShoppingCart::firstOrCreate(['person_id' => $request->person_id]);

        return response()->json(['message' => 'Cart created successfully', 'cart' => $cart], 201);
    }

    /**
     * Mostrar el carrito de un usuario
     */
    public function showCart($person_id) {
        $cart = StoreShoppingCart::where('person_id', $person_id)
            ->with(['items.product'])
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json($cart);
    }

    /**
     * Eliminar un item del carrito
     */
    public function removeItem($cart_item_id) {
        $cartItem = StoreCartItem::findOrFail($cart_item_id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed successfully']);
    }
}

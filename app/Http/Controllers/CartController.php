<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
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
    public function addItem(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:ospos_people,person_id',
            'item_id' => 'required|exists:ospos_items,item_id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1;

        // Obtener o crear el carrito del usuario
        $cart = StoreShoppingCart::firstOrCreate(['person_id' => $request->person_id]);

        // Obtener el producto
        $item = PosItem::findOrFail($request->item_id);

        // Verificar si hay unidades disponibles
        if ($item->quantity < $quantity) {
            return response()->json([
                'message' => 'No hay suficientes unidades disponibles para agregar al carrito.'
            ], 400); // Error 400 Bad Request
        }

        // Verificar si el item ya está en el carrito
        $cartItem = StoreCartItem::where('cart_id', $cart->id)
            ->where('item_id', $item->item_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $cartItem->increment('subtotal', $item->unit_price * $quantity);
        } else {
            $cartItem = StoreCartItem::create([
                'cart_id' => $cart->id,
                'item_id' => $item->item_id,
                'quantity' => $quantity,
                'subtotal' => $item->unit_price * $quantity,
            ]);
        }

        // Restar del inventario
        $item->decrement('quantity', $quantity);

        return response()->json([
            'message' => 'Producto agregado correctamente al carrito.',
            'cartItem' => $cartItem
        ], 201);
    }

    public function verificarCarrito($person_id)
    {
        $existe = Carrito::where('person_id', $person_id)->exists();

        return response()->json([
            'existe' => $existe
        ]);
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
    public function removeItem($cart_item_id)
    {
        $cartItem = StoreCartItem::findOrFail($cart_item_id);

        // Restaurar la cantidad al inventario
        $item = PosItem::find($cartItem->item_id);
        if ($item) {
            $item->increment('quantity', $cartItem->quantity);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Producto eliminado del carrito y stock restaurado.']);
    }

    public function updateItemQuantity(Request $request, $cart_item_id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $cartItem = StoreCartItem::findOrFail($cart_item_id);
    $item = PosItem::findOrFail($cartItem->item_id);

    $nuevaCantidad = $request->quantity;
    $cantidadActual = $cartItem->quantity;
    $diferencia = $nuevaCantidad - $cantidadActual;

    // Si está aumentando la cantidad
    if ($diferencia > 0) {
        if ($item->quantity < $diferencia) {
            return response()->json([
                'message' => 'No hay suficiente stock disponible para aumentar la cantidad.'
            ], 400);
        }

        // Restamos del inventario
        $item->decrement('quantity', $diferencia);
    }
    // Si está reduciendo la cantidad
    elseif ($diferencia < 0) {
        // Devolvemos al inventario
        $item->increment('quantity', abs($diferencia));
    }

    // Actualizamos la cantidad y subtotal
    $cartItem->quantity = $nuevaCantidad;
    $cartItem->subtotal = $item->unit_price * $nuevaCantidad;
    $cartItem->save();

    return response()->json([
        'message' => 'Cantidad actualizada correctamente.',
        'cartItem' => $cartItem
    ]);
}



    public function cartItems($person_id) {
        $cart = StoreShoppingCart::where('person_id', $person_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $items = StoreCartItem::where('cart_id', $cart->id)->with('product')->get();

        return response()->json($items);
    }

    public function clearCart($person_id) {
        $cart = StoreShoppingCart::where('person_id', $person_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        StoreCartItem::where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    public function allCarts() {
        $carts = StoreShoppingCart::with('items')->get();
        return response()->json($carts);
    }

    public function obtenerCarrito($usuarioId)
{
    $carrito = StoreShoppingCart::where('person_id', $usuarioId)->first();

    if (!$carrito) {
        return response()->json(['error' => 'Carrito no encontrado'], 404);
    }

    return response()->json(['data' => $carrito], 200);
}

public function validarCarrito($person_id)
{
    $carrito = StoreShoppingCart::where('person_id', $person_id)->first();

    if (!$carrito) {
        return response()->json([
            'existe' => false,
            'mensaje' => 'Carrito no encontrado'
        ], 404);
    }

    return response()->json([
        'existe' => true,
        'mensaje' => 'Carrito encontrado',
        'carrito' => $carrito
    ], 200);
}


}

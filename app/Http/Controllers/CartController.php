<?php

namespace App\Http\Controllers;

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\PosSalePayment;
use Illuminate\Support\Facades\Log;
use App\Models\ItemQuantity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Models\Carrito;
use App\Models\PosItem;
use App\Models\StoreShoppingCart;
use App\Models\StoreCartItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CartController extends Controller
{


    protected function authenticateUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                Log::warning('Autenticación fallida: token válido pero sin usuario asociado.');
                return response()->json(['message' => 'No se pudo autenticar el usuario.'], 401);
            }

            return $user;

        } catch (TokenExpiredException $e) {
            Log::info('Token expirado: ' . $e->getMessage());
            return response()->json(['message' => 'El token ha expirado.'], 401);
        } catch (TokenInvalidException $e) {
            Log::info('Token inválido: ' . $e->getMessage());
            return response()->json(['message' => 'Token inválido.'], 401);
        } catch (\Exception $e) {
            Log::error('Error general de autenticación: ' . $e->getMessage());
            return response()->json(['message' => 'No se pudo autenticar el token.'], 401);
        }
    }

    public function addItem(Request $request)
    {
        $authResult = $this->authenticateUser();
        if ($authResult instanceof \Illuminate\Http\JsonResponse) {
            return $authResult;
        }

        $user = $authResult;
        $person_id = $user->person_id;

        try {
            $request->validate([
                'item_id' => 'required|exists:ospos_items,item_id',
                'quantity' => 'nullable|integer|min:1'
            ]);

            $quantity = $request->quantity ?? 1;

            $cart = StoreShoppingCart::firstOrCreate(['person_id' => $person_id]);

            $item = PosItem::findOrFail($request->item_id);

            $locationId = 1;
            $stock = ItemQuantity::where('item_id', $item->item_id)
                ->where('location_id', $locationId)
                ->first();

            if (!$stock || $stock->quantity < $quantity) {
                Log::info("Stock insuficiente para el item ID {$item->item_id} - solicitado: $quantity, disponible: " . (isset($stock->quantity) ? $stock->quantity : 0));
                return response()->json([
                    'message' => 'No hay suficientes unidades disponibles en esta ubicación.'
                ], 400);
            }

            $cartItem = StoreCartItem::where('cart_id', $cart->id)
                ->where('item_id', $item->item_id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
                $cartItem->increment('subtotal', $item->unit_price * $quantity);
                Log::info("Item {$item->item_id} incrementado en carrito ID {$cart->id} para usuario {$person_id}");
            } else {
                $cartItem = StoreCartItem::create([
                    'cart_id' => $cart->id,
                    'item_id' => $item->item_id,
                    'quantity' => $quantity,
                    'subtotal' => $item->unit_price * $quantity,
                ]);
                Log::info("Item {$item->item_id} agregado al carrito ID {$cart->id} para usuario {$person_id}");
            }

            ItemQuantity::where('item_id', $item->item_id)
                ->where('location_id', $locationId)
                ->update(['quantity' => DB::raw("quantity - $quantity")]);

            return response()->json([
                'message' => 'Producto agregado correctamente al carrito.',
                'cartItem' => $cartItem
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error al agregar producto al carrito: " . $e->getMessage());
            return response()->json([
                'message' => 'Error interno al agregar el producto al carrito.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Checkout o finalizar compra
     */
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'person_id' => 'required|exists:ospos_people,person_id',
                'payment_amount' => 'required|numeric|min:0',
                'cash_refund' => 'nullable|numeric',
                'cash_adjustment' => 'nullable|numeric',
            ]);

            $carrito = StoreShoppingCart::where('person_id', $request->person_id)
                ->with('items')
                ->first();

            if (!$carrito) {
                return response()->json(['message' => 'Carrito no encontrado'], 404);
            }

            $items_save = [];
            $indice = 0;
            foreach ($carrito->items as $item) {
                $items_save[$indice]['item_id'] = $item->item_id;
                $items_save[$indice]['quantity_purchased'] = $item->quantity;
                $items_save[$indice]['discount'] = 0;
                $items_save[$indice]['discount_type'] = 0;
                $items_save[$indice]['item_cost_price'] = 0.0;
                $items_save[$indice]['item_unit_price'] = $item->product->unit_price ?? 0.0;
                $items_save[$indice]['item_location'] = 1; // O según corresponda
                $indice++;
            }

            // Crear la venta en POS
            $sale_id = $this->makePosSales(
                $request->person_id,
                'Compra ONLINE',
                $items_save,
                'Efectivo', // Podrías parametrizar
                $request->payment_amount,
                $request->cash_refund ?? 0,
                $request->cash_adjustment ?? 0
            );

            // Limpiar carrito después de comprar
            $carrito->items()->delete();

            return response()->json([
                'message' => 'Compra realizada exitosamente',
                'sale_id' => $sale_id
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error en checkout: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al finalizar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function makePosSales($person_id, $comment, $items, $payment_type, $payment_amount, $cash_refund = 0, $cash_adjustment = 0)
    {
        // Creamos la venta
        $sale = PosSale::create([
            'sale_time' => Carbon::now(),
            'customer_id' => $person_id,
            'employee_id' => auth()->id() ?? 1, // O asigna 1 si no hay usuario logueado
            'comment' => $comment,
            'sale_status' => 'Completed', // o el estado que manejes
            'sale_type' => 'sale', // según tus necesidades
        ]);

        // Guardamos los items
        foreach ($items as $itemData) {
            PosSaleItem::create([
                'sale_id' => $sale->sale_id,
                'item_id' => $itemData['item_id'],
                'quantity_purchased' => $itemData['quantity_purchased'],
                'item_unit_price' => $itemData['item_unit_price'],
                'discount' => $itemData['discount'] ?? 0,
                'item_location' => $itemData['item_location'] ?? 1,
                'description' => $itemData['description'] ?? null,
                'serialnumber' => $itemData['serialnumber'] ?? null,
                'line' => $itemData['line'] ?? 0,
            ]);
        }

        // Guardamos el pago
        PosSalePayment::create([
            'sale_id' => $sale->sale_id,
            'payment_type' => $payment_type,
            'payment_amount' => $payment_amount,
            'cash_refund' => $cash_refund,
            'cash_adjustment' => $cash_adjustment,
            'employee_id' => auth()->id() ?? 1,
            'payment_time' => Carbon::now(),
        ]);

        return $sale->sale_id;
    }
    public function verificarCarrito($person_id)
    {
        $existe = Carrito::where('person_id', $person_id)->exists();

        return response()->json([
            'existe' => $existe
        ]);
    }


    public function createCart(Request $request)
    {
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
    public function showCart($person_id)
    {
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
    public function removeItem($person_id, $item_id)
    {
        Log::info("Intentando eliminar item con ID {$item_id} del carrito del usuario con ID {$person_id}");

        try {
            $cart = StoreShoppingCart::where('person_id', $person_id)->first();

            if (!$cart) {
                Log::warning("Carrito para el usuario con ID {$person_id} no encontrado.");
                return response()->json(['message' => 'Carrito no encontrado.'], 404);
            }

            // Verificar si el carrito tiene la relación 'items'
            if (!method_exists($cart, 'items')) {
                Log::error("La relación 'items' no está definida en el modelo StoreShoppingCart.");
                return response()->json(['message' => 'Error interno en la relación del modelo.'], 500);
            }

            // Buscar el ítem dentro del carrito
            $item = $cart->items()->where('id', $item_id)->first();

            if (!$item) {
                Log::warning("Item con ID {$item_id} no encontrado en el carrito del usuario con ID {$person_id}.");
                return response()->json(['message' => 'Item no encontrado en este carrito.'], 404);
            }

            $item->delete();
            Log::info("Item con ID {$item_id} eliminado correctamente del carrito del usuario con ID {$person_id}.");

            return response()->json(['message' => 'Item eliminado correctamente.']);

        } catch (\Exception $e) {
            Log::error("Error al eliminar el item: " . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al eliminar el item.'], 500);
        }
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



    public function cartItems($person_id)
    {
        $cart = StoreShoppingCart::where('person_id', $person_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $items = StoreCartItem::where('cart_id', $cart->id)->with('product')->get();

        return response()->json($items);
    }

    public function clearCart($person_id)
    {
        $cart = StoreShoppingCart::where('person_id', $person_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        StoreCartItem::where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    public function allCarts()
    {
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

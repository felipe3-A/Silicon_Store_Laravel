<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PosItemController,
    ModuloController,
    ModuloXPerfilController,
    PerfilController,
    CartController,
    AdImageController,
    Login,
    OsposPersonController
};
use App\Http\Controllers\OsposCustomerController;
use App\Http\Controllers\Api\DataInfoController;

Route::apiResource('data_info', DataInfoController::class);



// Rutas para imágenes
Route::apiResource('adimages', AdImageController::class);
Route::get('adimages/simple', [AdImageController::class, 'indexSimple']);

// Rutas para items
Route::apiResource('items', PosItemController::class);
Route::get('/attributes', [PosItemController::class, 'attributes']);
Route::get('/attributes/{id}', [PosItemController::class, 'attributesValues']);

// Rutas para módulos
Route::post('/modulos', [ModuloController::class, 'store']);
Route::get('/modulos', [ModuloController::class, 'index']);

// Rutas para perfiles
Route::post('/perfil', [PerfilController::class, 'store']);
Route::get('/perfil', [PerfilController::class, 'index']);

// Rutas para módulos por perfil
Route::get('/modulosxperfil', [ModuloXPerfilController::class, 'index']);
Route::post('/modulosxperfil', [ModuloXPerfilController::class, 'store']);

// Rutas para el carrito
Route::prefix('cart')->group(function () {
    Route::post('/create', [CartController::class, 'createCart']);
    Route::get('/{person_id}', [CartController::class, 'showCart']);
    Route::get('/{person_id}/items', [CartController::class, 'cartItems']);
    Route::delete('/remove/{cart_item_id}', [CartController::class, 'removeItem']);
    Route::delete('/{person_id}/clear', [CartController::class, 'clearCart']);
    Route::get('/carts', [CartController::class, 'allCarts']);
    Route::post('/add', [CartController::class, 'addItem']);
    Route::get('/validar/{person_id}', [CartController::class, 'validarCarrito']);
});

// Autenticación
Route::post('/register', [Login::class, 'register']);
Route::post('/login', [Login::class, 'login']);
Route::post('/logout', [Login::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/users', [Login::class, 'listUsers']);



Route::prefix('customers')->group(function() {
    Route::get('/', [OsposCustomerController::class, 'index']); // Mostrar todos los clientes
    Route::get('{id}', [OsposCustomerController::class, 'show']); // Mostrar un cliente específico

    Route::post('/', [OsposCustomerController::class, 'store']); // Crear un nuevo cliente

    Route::put('{id}', [OsposCustomerController::class, 'update']); // Actualizar un cliente
    Route::delete('{id}', [OsposCustomerController::class, 'destroy']); // Eliminar un cliente
});

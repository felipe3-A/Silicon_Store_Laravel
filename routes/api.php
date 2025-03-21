<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosItemController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\ModuloXPerfilController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\CartController;
/*
Route::get('/items', [PosItemController::class, 'index']); // Obtener todos los items
Route::get('/items/{id}', [PosItemController::class, 'show']); // Obtener un item por ID
Route::post('/items', [PosItemController::class, 'store']); // Crear un item
Route::put('/items/{id}', [PosItemController::class, 'update']); // Actualizar un item
Route::delete('/items/{id}', [PosItemController::class, 'destroy']); // Eliminar un item
*/
Route::apiResource('items', PosItemController::class);

Route::get('/attributes', [PosItemController::class, 'attributes']);
Route::get('/attributes/{id}', [PosItemController::class, 'attributesValues']);

//Rutas para crear Modulos
Route::post('/modulos', [ModuloController::class, 'store']);
Route::get('/modulos', [ModuloController::class, 'index']);

//Rutas para crear perfiles

Route::post('/perfil', [PerfilController::class, 'store']);  // Crear un perfil
Route::get('/perfil', [PerfilController::class, 'index']);   // Obtener todos los perfiles

//Crear modulos por perfil
Route::get('/modulosxperfil', [ModuloXPerfilController::class, 'index']); // Obtener todos los módulos por perfil
Route::post('/modulosxperfil', [ModuloXPerfilController::class, 'store']); // Asignar un módulo a un perfil


//Rutas para el carrito
// Ruta para crear un carrito vacío
Route::post('/cart/create', [CartController::class, 'createCart']);

Route::prefix('cart')->group(callback: function () {
    Route::post('/add', [CartController::class, 'addItem']); // Agregar item al carrito
    Route::get('/{person_id}', [CartController::class, 'showCart']); // Mostrar carrito de un usuario
    Route::delete('/remove/{cart_item_id}', [CartController::class, 'removeItem']); // Eliminar item del carrito
});

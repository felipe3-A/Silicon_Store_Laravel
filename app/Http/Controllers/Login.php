<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StoreShoppingCart;
use Tymon\JWTAuth\Facades\JWTAuth;

class Login extends Controller
{
    /**
     * Método para registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        try {
            \Log::info('Intentando registrar nuevo usuario', $request->all());

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            \Log::info('Usuario creado correctamente', ['user_id' => $user->id]);

            // ✅ Crear carrito automáticamente al registrarse
            StoreShoppingCart::create([
                'person_id' => $user->id
            ]);

            \Log::info('Carrito creado para el usuario', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Usuario registrado correctamente y carrito creado',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error al registrar usuario', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al registrar usuario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Método para iniciar sesión, se le hizo una modificacion para enviar correctamente el JWT
     */

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                \Log::info('Usuario autenticado con éxito', ['user_id' => $user->id]);

                // Generación del token con JWTAuth
                $token = JWTAuth::fromUser($user);

                // Validación de token
                if (count(explode('.', $token)) !== 3) {
                    \Log::error('El token no es un JWT válido', ['token' => $token]);
                    return response()->json(['message' => 'Token inválido'], 500);
                }

                $existingCart = StoreShoppingCart::where('person_id', $user->id)->first();
                $cartMessage = $existingCart ? '¡Bienvenido! Te espera tu carrito.' : '¡Bienvenido! Se te ha creado un carrito.';

                return response()->json([
                    'message' => 'Inicio de sesión exitoso',
                    'cart_message' => $cartMessage,
                    'user' => $user,
                    'token' => JWTAuth::customClaims([
                        'sub' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ])->fromUser($user)
                ], 200);
            }

            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        } catch (\Exception $e) {
            \Log::error('Error durante el inicio de sesión', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error en el inicio de sesión', 'error' => $e->getMessage()], 500);
        }
    }




    public function listUsers()
    {
        try {
            $users = User::all();
            \Log::info('Lista de usuarios obtenida');

            return response()->json([
                'message' => 'Lista de usuarios obtenida correctamente',
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error al obtener lista de usuarios', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al obtener usuarios', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Método para cerrar sesión.
     */
    public function logout(Request $request)
{
    try {
        // Invalida el token actual desde el header Authorization
        JWTAuth::parseToken()->invalidate();

        \Log::info('Token JWT invalidado correctamente');

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        \Log::error('Token inválido al cerrar sesión', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'El token ya es inválido o expiró',
            'error' => $e->getMessage()
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        \Log::error('Token no proporcionado o inválido', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'No se pudo cerrar sesión. Token faltante o inválido.',
            'error' => $e->getMessage()
        ], 400);
    } catch (\Exception $e) {
        \Log::error('Error al cerrar sesión', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Error al cerrar sesión',
            'error' => $e->getMessage()
        ], 500);
    }
}


}

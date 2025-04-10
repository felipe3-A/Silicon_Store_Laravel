<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StoreShoppingCart;

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
     * Método para iniciar sesión.
     */
    public function login(Request $request)
    {
        try {
            \Log::info('Intentando iniciar sesión', $request->all());

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                \Log::info('Usuario autenticado con éxito', ['user_id' => $user->id]);

                $token = $user->createToken('authToken')->plainTextToken;

                // Verificamos si ya tiene carrito
                $existingCart = StoreShoppingCart::where('person_id', $user->id)->first();

                if (!$existingCart) {
                    // Si no tiene carrito, se le crea uno
                    $cart = StoreShoppingCart::create(['person_id' => $user->id]);
                    $cartMessage = '¡Bienvenido! Se te ha creado un carrito.';
                    \Log::info($cartMessage, ['user_id' => $user->id]);
                } else {
                    $cartMessage = '¡Bienvenido! Te espera tu carrito.';
                    \Log::info($cartMessage, ['user_id' => $user->id]);
                }

                return response()->json([
                    'message' => 'Inicio de sesión exitoso',
                    'cart_message' => $cartMessage,
                    'user' => $user,
                    'token' => $token
                ], 200);
            }

            \Log::warning('Intento de inicio de sesión fallido: credenciales incorrectas');
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
        $user = $request->user();

        if (!$user) {
            \Log::warning('Intento de cerrar sesión sin usuario autenticado', [
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'message' => 'No se encontró un usuario autenticado.'
            ], 401);
        }

        // Borra todos los tokens del usuario (revoca sesión)
        $user->tokens()->delete();

        \Log::info('Usuario cerró sesión exitosamente', ['user_id' => $user->id]);

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    } catch (\Throwable $e) {
        \Log::error('Error al cerrar sesión', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all()
        ]);

        return response()->json([
            'message' => 'Error al cerrar sesión',
            'error' => $e->getMessage()
        ], 500);
    }
}

}

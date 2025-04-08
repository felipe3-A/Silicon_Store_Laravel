<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modulo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ModuloController extends Controller
{
    // Obtener todos los módulos
    public function index()
    {
        $modulos = Modulo::all();
        return response()->json($modulos, 200);
    }

    // Crear un nuevo módulo
    public function store(Request $request)
    {
        Log::info('Datos recibidos:', $request->all());

        $validator = Validator::make($request->all(), [
            'id_modulo_padre' => 'nullable|exists:modulos,id',
            'modulo' => 'required|string|max:255',
            'url_modulo' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0|max:255'
        ]);

        if ($validator->fails()) {
            Log::error('Errores de validación:', $validator->errors()->toArray());
            return response()->json(['error' => $validator->errors()], 422);
        }

        $modulo = Modulo::create($request->all());
        Log::info('Módulo creado:', $modulo->toArray());

        return response()->json([
            'message' => 'Módulo creado exitosamente',
            'modulo' => $modulo
        ], 201);
    }

    // Obtener un módulo por ID
    public function show($id)
    {
        $modulo = Modulo::find($id);

        if (!$modulo) {
            return response()->json(['error' => 'Módulo no encontrado'], 404);
        }

        return response()->json($modulo, 200);
    }

    // Actualizar un módulo
    public function update(Request $request, $id)
    {
        $modulo = Modulo::find($id);

        if (!$modulo) {
            return response()->json(['error' => 'Módulo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_modulo_padre' => 'nullable|exists:modulos,id',
            'modulo' => 'required|string|max:255',
            'url_modulo' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $modulo->update($request->all());

        return response()->json([
            'message' => 'Módulo actualizado exitosamente',
            'modulo' => $modulo
        ], 200);
    }

    // Eliminar un módulo
    public function destroy($id)
    {
        $modulo = Modulo::find($id);

        if (!$modulo) {
            return response()->json(['error' => 'Módulo no encontrado'], 404);
        }

        $modulo->delete();

        return response()->json(['message' => 'Módulo eliminado exitosamente'], 200);
    }
}

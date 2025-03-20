<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModuloXPerfil;
use Illuminate\Support\Facades\Validator;

class ModuloXPerfilController extends Controller {
    // Obtener todos los registros de módulos asignados a perfil
    public function index() {
        $modulosXPerfil = ModuloXPerfil::with(['modulo', 'perfil'])->get();
        return response()->json($modulosXPerfil, 200);
    }

    // Asignar un módulo a un perfil
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'idmodulo' => 'required|exists:modulos,id',
            'idperfil' => 'required|exists:perfil,idperfil',
            'permiso' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $moduloXPerfil = ModuloXPerfil::create($request->all());

        return response()->json([
            'message' => 'Módulo asignado correctamente al perfil',
            'data' => $moduloXPerfil
        ], 201);
    }
}


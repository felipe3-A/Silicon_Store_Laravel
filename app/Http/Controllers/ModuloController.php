<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modulo;
use Illuminate\Support\Facades\Validator;

class ModuloController extends Controller {
    public function store(Request $request) {
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

        $modulo = Modulo::create($request->all());

        return response()->json([
            'message' => 'Módulo creado exitosamente',
            'modulo' => $modulo
        ], 201);
    }

    public function index() {
        return response()->json(Modulo::all(), 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perfil;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller {
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'perfil' => 'required|string|max:255|unique:perfil,perfil',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $perfil = Perfil::create($request->all());

        return response()->json([
            'message' => 'Perfil creado correctamente',
            'data' => $perfil
        ], 201);
    }

    public function index() {
        return response()->json(Perfil::all(), 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OsposPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class OsposPersonController extends Controller
{
    public function index()
    {
        try {
            $people = OsposPerson::all();
            return response()->json(['data' => $people], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener personas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener personas'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:ospos_people',
                'phone_number' => 'required|string|max:255',
                'address_1' => 'required|string|max:255',
                'address_2' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'comments' => 'nullable|string',
            ]);

            $person = OsposPerson::create($validated);

            return response()->json(['message' => 'Persona creada', 'data' => $person], 201);
        } catch (Exception $e) {
            Log::error('Error al crear persona: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo crear la persona', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $person = OsposPerson::find($id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            return response()->json(['data' => $person], 200);
        } catch (Exception $e) {
            Log::error("Error al obtener persona con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener la persona'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $person = OsposPerson::find($id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $request->validate([
                'email' => "nullable|email|unique:ospos_people,email,$id,person_id"
            ]);

            $person->update($request->all());

            return response()->json(['message' => 'Persona actualizada', 'data' => $person], 200);
        } catch (Exception $e) {
            Log::error("Error al actualizar persona con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'No se pudo actualizar la persona', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $person = OsposPerson::find($id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $person->delete();

            return response()->json(['message' => 'Persona eliminada correctamente'], 200);
        } catch (Exception $e) {
            Log::error("Error al eliminar persona con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'No se pudo eliminar la persona', 'details' => $e->getMessage()], 500);
        }
    }
}

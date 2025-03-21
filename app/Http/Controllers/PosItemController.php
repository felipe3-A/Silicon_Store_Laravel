<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PosItem;
use Illuminate\Support\Facades\DB;

class PosItemController extends Controller
{
    // Obtener todos los items
    public function index()
    {
        return response()->json(
            PosItem::with('stockQuantities.location') // Cargar relaciones
                ->where('deleted', 0) // Aqui se filtran los items filtrados
                ->paginate(20), // Solo se mostraran 10 elementos
            200
        );
    }





    // Obtener un solo item por ID
    public function show($id)
    {
        $item = PosItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        return response()->json($item, 200);
    }
    public function attributes()
    {

        return response()->json(DB::table('ospos_attribute_definitions')
            ->where('deleted', 0)
            ->get(), 200);
    }
    public function attributesValues($id)
    {

        $results = DB::table('ospos_attribute_links AS il')
            ->join('ospos_attribute_definitions AS d', 'd.definition_id', '=', 'il.definition_id')
            ->join('ospos_attribute_values AS v', 'v.attribute_id', '=', 'il.attribute_id')
            ->select(
                'il.definition_id',
                'd.definition_name',
                'il.definition_id',
                'v.attribute_value'
            )
            ->whereNull('il.item_id')
            ->whereNull('il.receiving_id')
            ->whereNull('il.sale_id')
            ->where('d.deleted', 0)
            ->where('il.definition_id', $id)
            ->get();
        return response()->json($results, 200);
    }



    //     // Crear un nuevo item
//     public function store(Request $request)
// {
//     try {
//         // Validación de datos
//         $validatedData = $request->validate([
//             'name' => 'required|string|max:255',
//             'category' => 'required|string|max:255',
//             'cost_price' => 'required|numeric',
//             'unit_price' => 'required|numeric',
//         ]);

    //         // Crear el nuevo item
//         $item = PosItem::create($validatedData);

    //         return response()->json([
//             'message' => 'Artículo creado exitosamente',
//             'data' => $item
//         ], 201);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'message' => 'Error de validación',
//             'errors' => $e->errors()
//         ], 422);

    //     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'Ocurrió un error al crear el artículo',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


    // Actualizar un item
    public function update(Request $request, $id)
    {
        $item = PosItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $item->update($request->all());

        return response()->json($item, 200);
    }

    // Eliminar un item
    public function destroy($id)
    {
        $item = PosItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item eliminado correctamente'], 200);
    }
}

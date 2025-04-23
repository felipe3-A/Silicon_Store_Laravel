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
        $items = PosItem::with([
            'stockQuantities' => function ($query) {
                $query->where('quantity', '>', 0)
                    ->whereHas('location', function ($q) {
                        $q->where('deleted', 0);
                    });
            },
            'stockQuantities.location'
        ])
            ->where('deleted', 0)
            ->get()
            ->map(function ($item) {
                $totalQuantity = $item->stockQuantities->sum('quantity');
                $item->total_quantity = $totalQuantity; // Añade el campo total_quantity al item
                return $item;
            });

        return response()->json($items, 200);
    }

    //LISTAR UN ITEM SOLAMNETE

    public function show($item_id)
    {
        $item = PosItem::with([
            'stockQuantities' => function ($query) {
                $query->where('quantity', '>', 0)
                    ->whereHas('location', function ($q) {
                        $q->where('deleted', 0);
                    });
            },
            'stockQuantities.location'
        ])
            ->where('deleted', 0)
            ->find($item_id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $totalQuantity = $item->stockQuantities->sum('quantity');
        $item->total_quantity = $totalQuantity;

        if ($totalQuantity <= 0) {
            return response()->json([
                'message' => 'Este producto no tiene unidades disponibles',
                'item' => $item
            ], 200);
        }

        return response()->json($item, 200);
    }
    // Obtener un solo item por ID

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

    public function categoriesWithItems()
    {
        // Agrupar productos por categoría, considerando solo productos no eliminados y con stock
        $categories = PosItem::with(['stockQuantities' => function ($query) {
                $query->where('quantity', '>', 0)
                      ->whereHas('location', function ($q) {
                          $q->where('deleted', 0);
                      });
            }])
            ->where('deleted', 0)
            ->get()
            ->filter(function ($item) {
                return $item->stockQuantities->sum('quantity') > 0;
            })
            ->groupBy('category')
            ->map(function ($items, $category) {
                return [
                    'category' => $category,
                    'items' => $items->map(function ($item) {
                        $item->total_quantity = $item->stockQuantities->sum('quantity');
                        return $item;
                    })->values()
                ];
            })
            ->values(); // Re-indexa el array de categorías

        return response()->json($categories, 200);
    }
    public function getItemsByCategory($category)
    {
        $items = PosItem::with(['stockQuantities' => function ($query) {
                $query->where('quantity', '>', 0)
                      ->whereHas('location', function ($q) {
                          $q->where('deleted', 0);
                      });
            }])
            ->where('category', $category)
            ->where('deleted', 0)
            ->get()
            ->filter(function ($item) {
                return $item->stockQuantities->sum('quantity') > 0;
            })
            ->map(function ($item) {
                $item->total_quantity = $item->stockQuantities->sum('quantity');
                return $item;
            })
            ->values();

        return response()->json($items, 200);
    }


}

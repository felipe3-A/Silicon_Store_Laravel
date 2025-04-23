<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosItem;
use Illuminate\Http\Request;
use App\Models\StoreItemGallery;
use Illuminate\Support\Facades\Storage;

class StoreItemGalleryController extends Controller
{
    public function index()
    {
        \Log::info('Obteniendo todas las galerías de productos.');
        return StoreItemGallery::with('item')->get();
    }

  // En StoreItemGalleryController.php

public function store(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:ospos_items,item_id',
        'images' => 'required|array',
        'images.*' => 'string', // Base64 como string
    ]);

    $itemId = $request->item_id;
    $base64Images = $request->images;

    $newPaths = [];

    foreach ($base64Images as $index => $base64Image) {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]);

            $base64Image = str_replace(' ', '+', $base64Image);
            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                return response()->json(['error' => 'Base64 inválido.'], 400);
            }

            $filename = uniqid() . '.' . $type;
            $path = 'gallery/' . $filename;
            Storage::disk('public')->put($path, $imageData);

            $newPaths[] = $path;
        } else {
            return response()->json(['error' => 'Formato de imagen inválido.'], 422);
        }
    }

    $existingGallery = StoreItemGallery::where('item_id', $itemId)->first();
    $existingImages = $existingGallery ? $existingGallery->images : [];

    $allImages = array_merge($existingImages, $newPaths);

    if (count($allImages) > 10) {
        return response()->json(['error' => 'Máximo 10 imágenes permitidas.'], 422);
    }

    $gallery = StoreItemGallery::updateOrCreate(
        ['item_id' => $itemId],
        ['images' => $allImages]
    );

    return response()->json($gallery, 201);
}


    public function show($item_id)
    {
        \Log::info("Mostrando galería con ID: $item_id");

        return StoreItemGallery::with('item')->findOrFail($item_id);
    }

    public function update(Request $request, $item_id)
    {
        \Log::info("Editando galería ID: $item_id", $request->all());

        $gallery = StoreItemGallery::findOrFail($item_id);

        $request->valitem_idate([
            'image_gallery.*' => 'file|mimetypes:image/*|max:5120',
            'replace' => 'array',
        ]);

        $images = $gallery->images ?? [];

        // Reemplazar imágenes por posición
        if ($request->has('replace') && is_array($request->replace)) {
            foreach ($request->replace as $position => $file) {
                if ($request->file("image_gallery.$position")) {
                    $newPath = $request->file("image_gallery.$position")->store('products/gallery', 'public');

                    if (isset($images[$position])) {
                        Storage::disk('public')->delete($images[$position]); // Elimina la vieja
                        $images[$position] = $newPath; // Reemplaza
                    } else {
                        $images[] = $newPath; // Si no existe esa posición, la agrega
                    }
                }
            }
        }

        // Agregar nuevas imágenes si no hay "replace"
        if ($request->hasFile('image_gallery')) {
            foreach ($request->file('image_gallery') as $index => $file) {
                if (!isset($request->replace[$index])) {
                    $path = $file->store('products/gallery', 'public');
                    $images[] = $path;
                }
            }
        }

        if (count($images) > 10) {
            return response()->json(['error' => 'Máximo 10 imágenes permititem_idas.'], 422);
        }

        $gallery->images = array_values($images);
        $gallery->save();

        \Log::info('Galería actualizada:', $gallery->toArray());

        return response()->json($gallery);
    }

    public function eliminarImagenPorPosicion($item_id, $posicion)
    {
        \Log::info("🔍 Solicitud para eliminar imagen en posición {$posicion} de la galería del item ID: {$item_id}");

        // Verificar si el item existe
        $item = PosItem::find($item_id);
        if (!$item) {
            \Log::warning("❌ Item con ID {$item_id} no existe.");
            return response()->json(['error' => 'El item especificado no existe.'], 404);
        }
        \Log::info("✅ Item encontrado: {$item->name} (ID: {$item_id})");

        // Buscar la galería asociada al item
        $galeria = StoreItemGallery::where('item_id', $item_id)->first();

        if (!$galeria) {
            \Log::warning("⚠️ No se encontró una galería asociada al item ID: {$item_id}");
            return response()->json(['error' => 'Galería no encontrada para el item.'], 404);
        }

        if (!$galeria->item || $galeria->item->item_id != $item_id) {
            \Log::error("❌ La galería no está correctamente asociada al item ID: {$item_id}");
            return response()->json(['error' => 'La galería no está asociada correctamente al item.'], 404                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                );
        }


        \Log::info("✅ Galería encontrada y correctamente asociada al item.");

        $imagenes = $galeria->images;

        if (!is_array($imagenes)) {
            \Log::error("❌ El campo 'images' no contiene un arreglo válido.");
            return response()->json(['error' => 'Formato de galería inválido.'], 500);
        }

        if (!isset($imagenes[$posicion])) {
            \Log::warning("❌ Posición {$posicion} no válida. Total de imágenes: " . count($imagenes));
            return response()->json(['error' => 'La posición de imagen no es válida.'], 422);
        }

        $imagen_a_eliminar = $imagenes[$posicion];
        \Log::info("🖼️ Imagen a eliminar: {$imagen_a_eliminar}");

        // Eliminar la imagen del disco
        if (Storage::disk('public')->exists($imagen_a_eliminar)) {
            Storage::disk('public')->delete($imagen_a_eliminar);
            \Log::info("✅ Imagen eliminada del disco: {$imagen_a_eliminar}");
        } else {
            \Log::warning("⚠️ Imagen no encontrada en el disco: {$imagen_a_eliminar}");
        }

        // Eliminar del array y guardar
        unset($imagenes[$posicion]);
        $galeria->images = array_values($imagenes); // Reindexar
        $galeria->save();

        \Log::info("🗑️ Imagen eliminada de la galería y cambios guardados exitosamente.");

        return response()->json(['mensaje' => 'Imagen eliminada correctamente.']);
    }




    public function obtenerPorItemId($item_id)
{
    \Log::info("Obteniendo galería por item_id: $item_id");

    $galeria = StoreItemGallery::where('item_id', $item_id)->first();

    if (!$galeria) {
        return response()->json(['mensaje' => 'No se encontró galería para este producto.'], 200);
    }

    return response()->json([
        'item_id' => $galeria->item_id,
        'imagenes_adicionales' => $galeria->images
    ]);
}

}

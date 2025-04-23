<?php
namespace App\Http\Controllers;

use App\Models\AdImage;
use App\Models\AdImageItem;
use App\Models\AdImageAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdImageController extends Controller
{
    // Listar todas las imágenes con sus relaciones
    public function index()
    {
        try {
            $images = AdImage::with(['items', 'attributes'])->get();
            return response()->json($images);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las imágenes', 'message' => $e->getMessage()], 500);
        }
    }

    // Listado simplificado
    public function indexSimple()
    {
        return response()->json(AdImage::all(['id', 'image_url', 'description', 'created_at', 'updated_at']));
    }

    // Subir una nueva imagen
    public function store(Request $request)
    {
        \Log::info('Método store alcanzado');

        // Log del contenido del request
        \Log::debug('Contenido del request', $request->all());

        // Validación
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            \Log::warning('Fallo la validación', ['errors' => $validator->errors()]);
            return response()->json(['error' => 'Error de validación', 'details' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            \Log::info('Imagen recibida');

            $imageFile = $request->file('image');
            \Log::debug('Nombre original de la imagen:', ['original_name' => $imageFile->getClientOriginalName()]);

            $path = $imageFile->store('images', 'public');
            \Log::info('Ruta almacenada', ['path' => $path]);

            try {
                $adImage = AdImage::create([
                    'image_url' => $path,
                    'description' => $request->input('description')
                ]);

                \Log::info('Imagen creada en base de datos', ['id' => $adImage->id]);

                return response()->json([
                    'message' => 'Imagen subida con éxito',
                    'image' => $adImage
                ]);
            } catch (\Exception $e) {
                \Log::error('Error al guardar la imagen en la base de datos', ['exception' => $e->getMessage()]);
                return response()->json(['error' => 'Error al guardar en la base de datos'], 500);
            }
        }

        \Log::warning('No se recibió ninguna imagen');
        return response()->json(['error' => 'No se pudo subir la imagen'], 400);
    }



    // Mostrar una imagen específica
    public function show($id)
    {
        $image = AdImage::with(['items', 'attributes'])->find($id);
        if (!$image) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }
        return response()->json($image);
    }

    // Actualizar una imagen y sus relaciones
    public function update(Request $request, $id)
    {
        $image = AdImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }

        $image->update($request->only(['image_url', 'description']));

        if ($request->has('items')) {
            AdImageItem::where('id', $id)->delete();
            foreach ($request->items as $item_id) {
                AdImageItem::create(['id' => $id, 'item_id' => $item_id]);
            }
        }

        if ($request->has('attributes')) {
            AdImageAttribute::where('id', $id)->delete();
            foreach ($request->attributes as $definition_id) {
                AdImageAttribute::create(['id' => $id, 'definition_id' => $definition_id]);
            }
        }

        return response()->json($image->load(['items', 'attributes']));
    }

    // Eliminar una imagen
    public function destroy($id)
    {
        $image = AdImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }

        // Eliminar imagen del almacenamiento
        Storage::disk('public')->delete($image->image_url);
        $image->delete();

        return response()->json(['message' => 'Imagen eliminada correctamente']);
    }
}

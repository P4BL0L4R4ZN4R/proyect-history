<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasificacionController extends Controller
{
    // Métodos existentes que mantienes igual
// En tu controlador
public function index()
{
    $categorias = Categoria::where('id', '!=', 0)->get();
    return view('clasificacion.index', compact('categorias'));
}

public function categoriasAjax()
{
    try {
        $categorias = Categoria::where('id', '!=', 0) // Excluye el registro con ID 0
            ->orderBy('nombre')
            ->get();

        return response()->json($categorias);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al cargar categorías: ' . $e->getMessage()
        ], 500);
    }
}

public function subcategoriasAjax()
{
    try {
        $subcategorias = Subcategoria::where('id', '!=', 0) // Excluye el registro con ID 0
            ->orderBy('nombre')
            ->get();

        return response()->json($subcategorias);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al cargar subcategorías: ' . $e->getMessage()
        ], 500);
    }
}

    public function updateCategoria(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|unique:categorias,nombre,'.$id
            ]);

            $categoria = Categoria::findOrFail($id);
            $categoria->update($request->only('nombre'));

            return response()->json([
                'success' => true,
                'data' => $categoria
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar categoría: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateSubcategoria(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|unique:subcategorias,nombre,'.$id,

            ]);

            $subcategoria = Subcategoria::findOrFail($id);
            $subcategoria->update($request->only(['nombre']));

            return response()->json([
                'success' => true,
                'data' => $subcategoria
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyCategoria($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Reasignar productos
                Producto::where('categoria_id', $id)
                      ->update(['categoria_id' => 0]);

                // Reasignar subcategorías
                Subcategoria::where('categoria_id', $id)
                          ->update(['categoria_id' => 0]);

                // Eliminar categoría
                Categoria::where('id', $id)->delete();
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una subcategoría (con reasignación a ID 0)
     */
    public function destroySubcategoria($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Reasignar productos
                Producto::where('subcategoria_id', $id)
                      ->update(['subcategoria_id' => 0]);

                // Eliminar subcategoría
                Subcategoria::where('id', $id)->delete();
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }


     public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            $categoria = new Categoria();
            $categoria->nombre = $request->nombre;
            $categoria->save();

            return response()->json([
                'success' => true,
                'data' => $categoria
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage()
            ]);
        }
    }


     public function storeSubcategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            $subcategoria = new Subcategoria();
            $subcategoria->nombre = $request->nombre;
            $subcategoria->save();

            return response()->json([
                'success' => true,
                'data' => $subcategoria
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la subcategoría: ' . $e->getMessage()
            ]);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CursosController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cursos = Curso::with('user')->ordenados()->get();
        return view('cursos.index', compact('cursos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cursos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoria' => 'required|string|max:100',
            'nivel' => 'required|string|max:100',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'url_externa' => 'nullable|url',
            'contenido_detallado' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['activo'] = $request->has('activo');

        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('cursos', 's3');
            $data['imagen'] = $imagenPath;
        }

        Curso::create($data);

        return redirect()->route('cursos.index')->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Curso $curso)
    {
        return view('cursos.show', compact('curso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoria' => 'required|string|max:100',
            'nivel' => 'required|string|max:100',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'url_externa' => 'nullable|url',
            'contenido_detallado' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo');

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($curso->imagen) {
                Storage::disk('s3')->delete($curso->imagen);
            }
            $imagenPath = $request->file('imagen')->store('cursos', 's3');
            $data['imagen'] = $imagenPath;
        }

        $curso->update($data);

        return redirect()->route('cursos.index')->with('success', 'Curso actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curso $curso)
    {
        if ($curso->imagen) {
            Storage::disk('s3')->delete($curso->imagen);
        }
        
        $curso->delete();

        return redirect()->route('cursos.index')->with('success', 'Curso eliminado exitosamente.');
    }

    /**
     * Cambiar estado activo/inactivo del curso
     */
    public function toggleStatus(Curso $curso)
    {
        $curso->update(['activo' => !$curso->activo]);
        
        $status = $curso->activo ? 'activado' : 'desactivado';
        return redirect()->route('cursos.index')->with('success', "Curso {$status} exitosamente.");
    }

    /**
     * Obtener cursos para el carrusel (API)
     */
    public function getCursosActivos()
    {
        $cursos = Curso::activos()->ordenados()->take(5)->get();
        return response()->json($cursos);
    }
}

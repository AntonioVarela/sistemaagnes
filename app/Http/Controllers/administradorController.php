<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\materia;
use App\Models\grupo;
use App\Models\User;
use App\Models\tarea;

class administradorController extends Controller
{
    public function index()
    {
        $grupos = grupo::all();
        $materias = materia::all();
        $tareas = tarea::all();
        return view('tareas',compact('grupos','materias','tareas')); // Cambiado a 'tareas'
    }

    public function store(REQUEST $request)
    {
        $tarea = new tarea();
        $tarea->titulo = request('titulo');
        $tarea->descripcion = request('descripcion');
        $tarea->archivo = request('archivo');
        $tarea->fecha_entrega = request('fecha_entrega');
        $tarea->grupo = request('grupo');
        $tarea->materia = request('materia');

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName(); // Generar un nombre único
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public'); // Guardar en el sistema de archivos
            $tarea->archivo = $rutaArchivo; // Guardar la ruta en la base de datos
        }
        $tarea->save();
        return redirect()->route('tareas.index')->with('success', 'Tarea creada exitosamente.');
        // Logic to store a new task
    }

    public function showAlumnos()
    {
        $grupos = grupo::all();
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::all();
        return view("tareasAlumno", compact(['grupos','materias','usuarios', 'tareas'])); // Cambiado a 'tareasAlumno'
 
    }

    public function showGrupos()
    {
        $materias = materia::all();
        $grupos = grupo::all();
        $usuarios = User::all();
        return view("grupos", compact(['materias','grupos','usuarios'])); // Corregido
    }

    public function storeGrupo(Request $request)
    {
        $grupo =  new grupo();
        $grupo->nombre = $request->nombre;
        $grupo->seccion = $request->seccion;
        $grupo->titular = $request->titular_id;
        $grupo->save();
        return redirect()->route('grupos.index')->with('success', 'Grupo creado exitosamente.');
    }
    public function destroyGrupo($id)
    {
        $grupo = grupo::findOrFail($id);
        $grupo->delete();

        return redirect()->route('grupos.index')->with('success', 'Grupo eliminado exitosamente.');
    }
    public function showMaterias()
    {
        $materias = materia::all();
        $grupos = grupo::all();
        $usuarios = User::all();
        return view("materias", compact(['materias','grupos','usuarios'])); // Corregido
    }
    public function storeMateria(Request $request)
    {
        $materia = new materia();
        $materia->nombre = $request->nombre;
        $materia->clave = $request->clave;
        $materia->maestro = $request->maestro_id;
        $materia->save();
        return redirect()->route('materias.index')->with('success', 'Materia creada exitosamente.');
    }
    public function destroyMateria($id)
    {
        $materia = materia::findOrFail($id);
        $materia->delete();

        return redirect()->route('materias.index')->with('success', 'Materia eliminada exitosamente.');
    }
}

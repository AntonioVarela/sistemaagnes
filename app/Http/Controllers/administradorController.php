<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\materia;
use App\Models\grupo;
use App\Models\User;
use App\Models\tarea;
use App\Models\horario;
use Illuminate\Support\Facades\Auth;

class administradorController extends Controller
{
    public function indexDashboard()
    {
        $materias = Auth::user()->materias;
        $grupos = grupo::all();
        $usuarios = User::all();
        return view('dashboard', compact(['materias','grupos','usuarios'])); // Cambiado a 'dashboard'
    }

    public function index()
    {
        $materiasUsuario = Auth::user()->materias->pluck('id');
        $horarios = horario::with(['materia', 'grupo'])->whereIn('materia', $materiasUsuario)->get();
        $grupos = grupo::all();
        $materias = Auth::user()->materias;
        $tareas = tarea::all();

        return view('tareas',compact('grupos','materias','tareas','horarios')); // Cambiado a 'tareas'
    }

    public function store(REQUEST $request)
    {
        $materia = Auth::user()->materia;
        $tarea = new tarea();
        $materia = materia::find($request->materia);
        $tarea->titulo = "Tarea de " . $materia->nombre;
        $tarea->descripcion = request('descripcion');
        $tarea->archivo = request('archivo');
        $tarea->fecha_entrega = request('fecha_entrega');
        $tarea->hora_entrega = request('hora_entrega');
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

    public function showAlumnos($id)
    {
        $grupos = grupo::all();
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::where('grupo', $id)->get(); // Cambiado a 'tareas'
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

    public function showUsuarios()
    {
        $usuarios = User::all();
        return view("usuarios", compact('usuarios')); // Cambiado a 'usuarios'
    }
    public function storeUsuario(Request $request)
    {
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->rol = $request->rol;
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function showHorarios()
    {
        $horarios = horario::all();
        $grupos = grupo::all();
        $materias = materia::all();
        $usuarios = User::all();
        return view("horarios", compact(['horarios','grupos','materias','usuarios'])); // Cambiado a 'horarios'
    }

    public function storeHorario(Request $request)
    {
        $horario = new horario();
        $horario->grupo = $request->grupo_id;
        $horario->materia = $request->materia_id;
        $horario->dias = implode(',', $request->dias); // Convertir el array de días en una cadena separada por comas
        $horario->hora_inicio = $request->hora_inicio;
        $horario->hora_fin = $request->hora_fin;
        $horario->save();
        return redirect()->route('horarios.index')->with('success', 'Horario creado exitosamente.');
    }
}

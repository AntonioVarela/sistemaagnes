<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\materia;
use App\Models\grupo;
use App\Models\User;
use App\Models\tarea;
use App\Models\horario;
use App\Models\anuncio;
use Illuminate\Support\Facades\Auth;

class administradorController extends Controller
{
    public function indexDashboard()
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $grupos = grupo::all();
        $usuarios = User::all();
        return view('dashboard', compact(['grupos','usuarios','horario'])); // Cambiado a 'dashboard'
    }

    //Tareas   
    public function index()
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $seccion = grupo::select('seccion')->whereIn('id', $horario->pluck('grupo_id'))->get();
        $grupos = grupo::all();
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->get();
        $tareas = tarea::whereIn('materia', $horario->pluck('materia_id'))->whereIn('grupo', $horario->pluck('grupo_id'))->get();

        return view('tareas',compact('grupos','materias','tareas','horario','seccion')); // Cambiado a 'tareas'
    }

    public function update(Request $request, $id)
    {
        $tarea = tarea::findOrFail($id);
        $tarea->descripcion = $request->descripcion;
        $tarea->fecha_entrega = $request->fecha_entrega;
        $tarea->hora_entrega = $request->hora_entrega;
        
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $tarea->archivo = $rutaArchivo;
        }

        if ($request->has('grupo')) {
            $tarea->grupo = $request->grupo;
        }
        if ($request->has('materia')) {
            $materia = materia::find($request->materia);
            $tarea->titulo = "Tarea de " . $materia->nombre;
            $tarea->materia = $request->materia;
        }

        $tarea->save();
        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }
    public function destroy($id)
    {
        $tarea = tarea::findOrFail($id);
        $tarea->delete();
        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada exitosamente.');
    }

    public function store(REQUEST $request)
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $tarea = new tarea();
        $tarea->descripcion = request('descripcion');
        $tarea->archivo = request('archivo');
        $tarea->fecha_entrega = request('fecha_entrega');
        $tarea->hora_entrega = request('hora_entrega');
        if(count($horario) == 1){
            $materia = materia::find($horario[0]->materia_id);
            $tarea->titulo = "Tarea de " . $materia->nombre;
            $tarea->grupo = $horario[0]->grupo_id;
            $tarea->materia = $horario[0]->materia_id;
        }
        else{
            $materia = materia::find($request->materia);
            $tarea->titulo = "Tarea de " . $materia->nombre;
            $tarea->grupo = request('grupo');
            $tarea->materia = request('materia');
        }
        

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

    //Alumnos
    public function showAlumnos($id)
    {
        $grupo = grupo::find($id);
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::where('grupo', $id)->get(); // Cambiado a 'tareas'
        return view("tareasAlumno", compact(['grupo','materias','usuarios', 'tareas'])); // Cambiado a 'tareasAlumno'
 
    }

    //Grupos
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
    public function updateGrupo(Request $request, $id)
    {
        $grupo = grupo::findOrFail($id);
        $grupo->nombre = $request->nombre;
        $grupo->seccion = $request->seccion;
        $grupo->titular = $request->titular_id;
        $grupo->save();
        return redirect()->route('grupos.index')->with('success', 'Grupo actualizado exitosamente.');
    }
    //Materias
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
        $materia->save();
        return redirect()->route('materias.index')->with('success', 'Materia creada exitosamente.');
    }
    public function destroyMateria($id)
    {
        $materia = materia::findOrFail($id);
        $materia->delete();
        return redirect()->route('materias.index')->with('success', 'Materia eliminada exitosamente.');
    }
    public function updateMateria(Request $request, $id)
    {
        $materia = materia::findOrFail($id);
        $materia->nombre = $request->nombre;
        $materia->save();
        return redirect()->route('materias.index')->with('success', 'Materia actualizada exitosamente.');
    }

    //Usuarios
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
    public function destroyUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
    public function updateUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->rol = $request->rol;
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }
    //Horarios
    public function showHorarios()
    {
        $horarios = horario::all();
        $grupos = grupo::all();
        $materias = materia::all();
        $usuarios = User::where('rol', 'Maestro')->get();
        return view("horarios", compact(['horarios','grupos','materias','usuarios'])); // Cambiado a 'horarios'
    }

    public function storeHorario(Request $request)
    {
        $horario = new horario();
        $horario->grupo_id = $request->grupo_id;
        $horario->materia_id = $request->materia_id;
        $horario->maestro_id = $request->maestro_id;
        $horario->dias = implode(',', $request->dias); // Convertir el array de días en una cadena separada por comas
        $horario->hora_inicio = $request->hora_inicio;
        $horario->hora_fin = $request->hora_fin;
        $horario->save();
        return redirect()->route('horarios.index')->with('success', 'Horario creado exitosamente.');
    }
    public function destroyHorario($id)
    {
        $horario = horario::findOrFail($id);
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
    public function updateHorario(Request $request, $id)
    {
        $horario = horario::findOrFail($id);
        $horario->grupo_id = $request->grupo_id;
        $horario->materia_id = $request->materia_id;
        $horario->maestro_id = $request->maestro_id;
        $horario->dias = implode(',', $request->dias);
        $horario->hora_inicio = $request->hora_inicio;
        $horario->hora_fin = $request->hora_fin;
        $horario->save();
        return redirect()->route('horarios.index')->with('success', 'Horario actualizado exitosamente.');
    }   

    //Anuncios
    public function showAnuncios()
    {
        $anuncios = anuncio::all();
        return view("anuncios", compact('anuncios')); // Cambiado a 'anuncios'
    }
    public function storeAnuncio(Request $request)
    {
        $anuncio = new anuncio();
        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $anuncio->archivo = $rutaArchivo;
        }
        $anuncio->save();
        return redirect()->route('anuncios.index')->with('success', 'Anuncio creado exitosamente.');
    }
    
    public function destroyAnuncio($id)
    {
        $anuncio = anuncio::findOrFail($id);
        $anuncio->delete();
        return redirect()->route('anuncios.index')->with('success', 'Anuncio eliminado exitosamente.');
    }
    public function updateAnuncio(Request $request, $id)
    {
        $anuncio = anuncio::findOrFail($id);
        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $anuncio->archivo = $rutaArchivo;
        }
        $anuncio->save();
        return redirect()->route('anuncios.index')->with('success', 'Anuncio actualizado exitosamente.');
    }
    
}

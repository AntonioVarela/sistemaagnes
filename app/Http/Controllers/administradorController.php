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
use Illuminate\Support\Facades\Storage;

class administradorController extends Controller
{
    public function indexDashboard()
    {
        if(Auth::user()->rol == 'Coordinador Primaria'){
            // Primero verificamos los grupos de primaria
            $gruposPrimaria = grupo::where('seccion', 'Primaria')->get();
            
            // Luego obtenemos los horarios para estos grupos
            $horarios = horario::with(['grupo', 'materia'])
                ->whereIn('grupo_id', $gruposPrimaria->pluck('id'))
                ->orderBy('grupo_id')
                ->get();
            
            $grupos = $gruposPrimaria->whereIn('id', $horarios->pluck('grupo_id')->unique())
                ->sortBy('nombre');

        } else if(Auth::user()->rol == 'Coordinador Secundaria'){
            // Primero verificamos los grupos de secundaria
            $gruposSecundaria = grupo::where('seccion', 'Secundaria')->get();
            
            // Luego obtenemos los horarios para estos grupos
            $horarios = horario::with(['grupo', 'materia'])
                ->whereIn('grupo_id', $gruposSecundaria->pluck('id'))
                ->orderBy('grupo_id')
                ->get();
            
            $grupos = $gruposSecundaria->whereIn('id', $horarios->pluck('grupo_id')->unique())
                ->sortBy('nombre');
        }
        if(Auth::user()->rol == 'administrador'){
            $horarios = horario::with(['grupo', 'materia'])
                ->orderBy('grupo_id')
                ->get();
            $grupos = grupo::all();
        }
        if(Auth::user()->rol == 'Maestro'){
            $horarios = horario::with(['grupo', 'materia'])
                ->where('maestro_id', Auth::user()->id)
                ->orderBy('grupo_id')
                ->get();
            $grupos = grupo::whereIn('id', $horarios->pluck('grupo_id'))->get();
        }


        return view('dashboard', compact('horarios', 'grupos'));
    }

    //Tareas   
    public function index()
    {
        if(Auth::user()->rol == 'administrador'){
            $horario = horario::all();
            $grupos = grupo::all(); 
        } else{
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->get();
        }
        $seccion = grupo::select('seccion')->whereIn('id', $horario->pluck('grupo_id'))->groupBy('seccion')->get();
        
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->get();
        $tareas = tarea::whereIn('materia', $horario->pluck('materia_id'))->whereIn('grupo', $horario->pluck('grupo_id'))->get();

        return view('tareas',compact('grupos','materias','tareas','horario','seccion')); // Cambiado a 'tareas'
    }

    public function updateTareas(Request $request, $id)
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea actualizada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }

    public function store(Request $request)
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea creada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
        // Logic to store a new task
    }

    //Alumnos
    public function showAlumnos($id)
    {
        $grupo = grupo::find($id);
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::where('grupo', $id)->get(); // Cambiado a 'tareas'
        $anuncios = anuncio::where('grupo_id', $id)->get();
        return view("tareasAlumno", compact(['grupo','materias','usuarios', 'tareas', 'anuncios'])); // Cambiado a 'tareasAlumno'
 
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Grupo creado exitosamente!'
        ]);
        return redirect()->route('grupos.index');
    }
    public function destroyGrupo($id)
    {
        $grupo = grupo::findOrFail($id);
        $grupo->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Grupo eliminado exitosamente!'
        ]);
        return redirect()->route('grupos.index');
    }
    public function updateGrupo(Request $request, $id)
    {
        $grupo = grupo::findOrFail($id);
        $grupo->nombre = $request->nombre;
        $grupo->seccion = $request->seccion;
        $grupo->titular = $request->titular_id;
        $grupo->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Grupo actualizado exitosamente!'
        ]);
        return redirect()->route('grupos.index');
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
        $materia->color = $request->color;
        $materia->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Materia creada exitosamente!'
        ]);
        return redirect()->route('materias.index');
    }
    public function destroyMateria($id)
    {
        $materia = materia::findOrFail($id);
        $materia->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Materia eliminada exitosamente!'
        ]);
        return redirect()->route('materias.index');
    }
    public function updateMateria(Request $request, $id)
    {
        $materia = materia::findOrFail($id);
        $materia->nombre = $request->nombre;
        $materia->color = $request->color;
        $materia->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Materia actualizada exitosamente!'
        ]);
        return redirect()->route('materias.index');
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Usuario creado exitosamente!'
        ]);
        return redirect()->route('usuarios.index');
    }
    public function destroyUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Usuario eliminado exitosamente!'
        ]);
        return redirect()->route('usuarios.index');
    }
    public function updateUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->rol = $request->rol;
        $usuario->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Usuario actualizado exitosamente!'
        ]);
        return redirect()->route('usuarios.index');
    }
    //Horarios
    public function showHorarios()
    {
        $query = horario::query();

        // Aplicar filtros si existen
        if (request('search')) {
            $query->whereHas('materia', function($q) {
                $q->where('nombre', 'like', '%' . request('search') . '%');
            })->orWhereHas('grupo', function($q) {
                $q->where('nombre', 'like', '%' . request('search') . '%')
                  ->orWhere('seccion', 'like', '%' . request('search') . '%');
            });
        }

        if (request('grupo_filter')) {
            $query->where('grupo_id', request('grupo_filter'));
        }

        if (request('materia_filter')) {
            $query->where('materia_id', request('materia_filter'));
        }

        $horarios = $query->get();
        $grupos = grupo::all();
        $materias = materia::all();
        $usuarios = User::where('rol', 'Maestro')->get();
        return view("horarios", compact(['horarios','grupos','materias','usuarios']));
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Horario creado exitosamente!'
        ]);
        return redirect()->route('horarios.index');
    }
    public function destroyHorario($id)
    {
        try {
            $horario = horario::findOrFail($id);
            
            // Verificar si el usuario tiene permiso para eliminar el horario
            if (Auth::user()->rol !== 'administrador') {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No tienes permiso para eliminar horarios'
                ]);
                return redirect()->route('horarios.index');
            }

            // Eliminar el horario
            $horario->delete();

            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Horario eliminado exitosamente!'
            ]);
        } catch (\Exception $e) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al eliminar el horario: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('horarios.index');
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Horario actualizado exitosamente!'
        ]);
        return redirect()->route('horarios.index');
    }   

    //Anuncios
    public function showAnuncios()
    {
        if(Auth::user()->rol == 'administrador'){
            $horario = horario::all();
            $grupos = grupo::all(); 
            $anuncios = anuncio::all();
        }
        if(Auth::user()->rol == 'Maestro'){
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->get();
            $anuncios = anuncio::with('user')->where('usuario_id', Auth::user()->id)->get();
        }
        if(Auth::user()->rol == 'Coordinador Primaria'){
            $grupos = grupo::where('seccion', 'Primaria')->get();
            $anuncios = anuncio::with('user')->whereIn('grupo_id', $grupos->pluck('id'))->get();
        }
        if(Auth::user()->rol == 'Coordinador Secundaria'){
            $grupos = grupo::where('seccion', 'Secundaria')->get();
            $anuncios = anuncio::with('user')->whereIn('grupo_id', $grupos->pluck('id'))->get();
        }
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->get();
        return view("anuncios", compact(['anuncios','horario','grupos','materias']));
    }
    public function storeAnuncio(Request $request)
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $anuncio = new anuncio();
        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        $anuncio->usuario_id = Auth::user()->id;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $anuncio->archivo = $rutaArchivo;
        }
        if(count($horario) == 1){
            $materia = materia::find($horario[0]->materia_id);
            $grupo = grupo::find($horario[0]->grupo_id);
            $anuncio->grupo_id = $horario[0]->grupo_id;
            $anuncio->materia_id = $horario[0]->materia_id;
            $anuncio->seccion = $grupo->seccion;
        }
        else{
            $materia = materia::find($request->materia_id);
            $grupo = grupo::find($request->grupo_id);
            $anuncio->grupo_id = $request->grupo_id;
            $anuncio->materia_id = $request->materia_id;
            $anuncio->seccion = $grupo->seccion;
        }
        $anuncio->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio creado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    
    public function destroyAnuncio($id)
    {
        $anuncio = anuncio::findOrFail($id);
        // Eliminar el archivo si existe
        if ($anuncio->archivo) {
            Storage::disk('public')->delete($anuncio->archivo);
        }
        $anuncio->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio eliminado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    public function updateAnuncio(Request $request, $id)
    {
        $anuncio = anuncio::findOrFail($id);
        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            if ($anuncio->archivo) {
                Storage::disk('public')->delete($anuncio->archivo);
            }
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $anuncio->archivo = $rutaArchivo;
        }
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        if(count($horario) > 1){
            $materia = materia::find($request->materia_id);
            $grupo = grupo::find($request->grupo_id);
            $anuncio->grupo_id = $request->grupo_id;
            $anuncio->materia_id = $request->materia_id;
            $anuncio->seccion = $grupo->seccion;
        }
        $anuncio->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio actualizado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    
    public function storeTarea(Request $request)
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $tarea = new tarea();
        $tarea->descripcion = request('descripcion');
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $tarea->archivo = $rutaArchivo;
        }
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
        $tarea->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea creada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }

    public function updateTarea(Request $request, $id)
    {
        $tarea = tarea::findOrFail($id);
        $tarea->descripcion = $request->descripcion;
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            if ($tarea->archivo) {
                Storage::disk('public')->delete($tarea->archivo);
            }
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 'public');
            $tarea->archivo = $rutaArchivo;
        }
        $tarea->fecha_entrega = $request->fecha_entrega;
        $tarea->hora_entrega = $request->hora_entrega;
        if(count($horario) > 1){
            $materia = materia::find($request->materia);
            $tarea->titulo = "Tarea de " . $materia->nombre;
            $tarea->grupo = $request->grupo;
            $tarea->materia = $request->materia;
        }
        $tarea->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea actualizada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }

    public function destroyTarea($id)
    {
        $tarea = tarea::findOrFail($id);
        // Eliminar el archivo si existe
        if ($tarea->archivo) {
            Storage::disk('public')->delete($tarea->archivo);
        }
        $tarea->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea eliminada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\materia;
use App\Models\grupo;
use App\Models\User;
use App\Models\tarea;
use App\Models\horario;
use App\Models\anuncio;
use App\Http\Requests\AnuncioRequest;
use App\Http\Requests\TareaRequest;
use App\Http\Requests\CircularRequest;
use App\Models\Circular;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        
        // Aplicar filtros
        $query = tarea::whereIn('materia', $horario->pluck('materia_id'))
                      ->whereIn('grupo', $horario->pluck('grupo_id'));
        
        // Filtro por grupo
        if (request('grupo_filter')) {
            $query->where('grupo', request('grupo_filter'));
        }
        
        // Filtro por materia
        if (request('materia_filter')) {
            $query->where('materia', request('materia_filter'));
        }
        
        $tareas = $query->get();

        return view('tareas',compact('grupos','materias','tareas','horario','seccion')); // Cambiado a 'tareas'
    }

    public function updateTareas(TareaRequest $request, $id)
    {
        $tarea = tarea::findOrFail($id);
        $tarea->descripcion = $request->descripcion;
        $tarea->fecha_entrega = $request->fecha_entrega;
        $tarea->hora_entrega = $request->hora_entrega;
        
        if ($request->hasFile('archivo')) {
            try {
                // Eliminar el archivo anterior si existe
                if ($tarea->archivo) {
                    Storage::disk('s3')->delete($tarea->archivo);
                }
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 's3');
                $tarea->archivo = $rutaArchivo;
            } catch (\Exception $e) {
                Log::error('Error al actualizar archivo de tarea: ' . $e->getMessage());
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ]);
                return redirect()->route('tareas.index');
            }
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
    public function store(TareaRequest $request)
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
            try {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 's3');
                $tarea->archivo = $rutaArchivo;
            } catch (\Exception $e) {
                Log::error('Error al subir archivo de tarea: ' . $e->getMessage());
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ]);
                return redirect()->route('tareas.index');
            }
        }
        $tarea->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea creada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }

    public function destroyTarea($id)
    {
        $tarea = tarea::findOrFail($id);
        // Eliminar el archivo si existe
        if ($tarea->archivo) {
            Storage::disk('s3')->delete($tarea->archivo);
        }
        $tarea->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Tarea eliminada exitosamente!'
        ]);
        return redirect()->route('tareas.index');
    }

    //Alumnos
    public function showAlumnos($id)
    {
        $grupo = grupo::find($id);
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::where('grupo', $id)->get(); // Cambiado a 'tareas'
        $anuncios = anuncio::porGrupo($id)->activos()->get();
        $circulares = Circular::where('grupo_id', $id)->activas()->orderBy('created_at', 'desc')->take(3)->get();
        return view("tareasAlumno", compact(['grupo','materias','usuarios', 'tareas', 'anuncios', 'circulares'])); // Cambiado a 'tareasAlumno'
 
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
            $anuncios = anuncio::activos()->get();
        }
        if(Auth::user()->rol == 'Maestro'){
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->get();
            $anuncios = anuncio::with('user')->where('usuario_id', Auth::user()->id)->activos()->get();
        }
        if(Auth::user()->rol == 'Coordinador Primaria'){
            $grupos = grupo::where('seccion', 'Primaria')->get();
            $anuncios = anuncio::with('user')->whereIn('grupo_id', $grupos->pluck('id'))->activos()->get();
        }
        if(Auth::user()->rol == 'Coordinador Secundaria'){
            $grupos = grupo::where('seccion', 'Secundaria')->get();
            $anuncios = anuncio::with('user')->whereIn('grupo_id', $grupos->pluck('id'))->activos()->get();
        }
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->get();
        return view("anuncios", compact(['anuncios','horario','grupos','materias']));
    }
    public function storeAnuncio(AnuncioRequest $request)
    {
        $horario = horario::where('maestro_id', Auth::user()->id)->get();
        $anuncio = new anuncio();
        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        $anuncio->usuario_id = Auth::user()->id;
        $anuncio->fecha_expiracion = $request->fecha_expiracion ? $request->fecha_expiracion : null;
        if ($request->hasFile('archivo')) {
            try {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 's3');
                $anuncio->archivo = $rutaArchivo;
            } catch (\Exception $e) {
                Log::error('Error al subir archivo de anuncio: ' . $e->getMessage());
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ]);
                return redirect()->route('anuncios.index');
            }
        }
        // Si es un anuncio global, no se asigna grupo ni materia específica
        if ($request->es_global) {
            $anuncio->es_global = true;
            $anuncio->grupo_id = null;
            $anuncio->materia_id = null;
            $anuncio->seccion = null;
        } else {
            $anuncio->es_global = false;
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
        
        // Verificar si el usuario es el creador o administrador
        if (Auth::user()->id !== $anuncio->user_id && Auth::user()->rol !== 'administrador') {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar este anuncio'
            ]);
            return redirect()->route('anuncios.index');
        }

        // Eliminar el archivo si existe
        if ($anuncio->archivo) {
            Storage::disk('s3')->delete($anuncio->archivo);
        }
        $anuncio->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio eliminado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    public function updateAnuncio(AnuncioRequest $request, $id)
    {
        $anuncio = anuncio::findOrFail($id);
        
        // Verificar si el usuario es el creador o administrador
        if (Auth::user()->id !== $anuncio->user_id && Auth::user()->rol !== 'administrador') {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'No tienes permiso para editar este anuncio'
            ]);
            return redirect()->route('anuncios.index');
        }

        $anuncio->titulo = $request->titulo;
        $anuncio->contenido = $request->contenido;
        $anuncio->fecha_expiracion = $request->fecha_expiracion ? $request->fecha_expiracion : null;
        if ($request->hasFile('archivo')) {
            try {
                // Eliminar el archivo anterior si existe
                if ($anuncio->archivo) {
                    Storage::disk('s3')->delete($anuncio->archivo);
                }
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs('archivos', $nombreArchivo, 's3');
                $anuncio->archivo = $rutaArchivo;
            } catch (\Exception $e) {
                Log::error('Error al actualizar archivo de anuncio: ' . $e->getMessage());
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ]);
                return redirect()->route('anuncios.index');
            }
        }
        // Si es un anuncio global, no se asigna grupo ni materia específica
        if ($request->es_global) {
            $anuncio->es_global = true;
            $anuncio->grupo_id = null;
            $anuncio->materia_id = null;
            $anuncio->seccion = null;
        } else {
            $anuncio->es_global = false;
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            if(count($horario) > 1){
                $materia = materia::find($request->materia_id);
                $grupo = grupo::find($request->grupo_id);
                $anuncio->grupo_id = $request->grupo_id;
                $anuncio->materia_id = $request->materia_id;
                $anuncio->seccion = $grupo->seccion;
            }
        }
        $anuncio->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio actualizado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    
    // ==================== MÉTODOS PARA CIRCULARES ====================
    
    public function indexCirculares()
    {
        if(Auth::user()->rol == 'administrador'){
            $circulares = Circular::with(['user', 'grupo'])->orderBy('created_at', 'desc')->get();
            $grupos = grupo::all();
        } else if(Auth::user()->rol == 'Coordinador Primaria'){
            $circulares = Circular::with(['user', 'grupo'])
                ->where('seccion', 'Primaria')
                ->orderBy('created_at', 'desc')
                ->get();
            $grupos = grupo::where('seccion', 'Primaria')->get();
        } else if(Auth::user()->rol == 'Coordinador Secundaria'){
            $circulares = Circular::with(['user', 'grupo'])
                ->where('seccion', 'Secundaria')
                ->orderBy('created_at', 'desc')
                ->get();
            $grupos = grupo::where('seccion', 'Secundaria')->get();
        } else {
            // Maestros solo ven circulares de sus grupos
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->get();
            $circulares = Circular::with(['user', 'grupo'])
                ->whereIn('grupo_id', $horario->pluck('grupo_id'))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('circulares', compact('circulares', 'grupos'));
    }

    public function storeCircular(CircularRequest $request)
    {
        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
            $rutaArchivo = $archivo->storeAs('circulares', $nombreArchivo, 's3');

            $circular = new Circular();
            $circular->titulo = $request->titulo;
            $circular->descripcion = $request->descripcion;
            $circular->archivo = $rutaArchivo;
            $circular->nombre_archivo_original = $archivo->getClientOriginalName();
            $circular->tipo_archivo = $archivo->getClientMimeType();
            $circular->usuario_id = Auth::user()->id;
            $circular->grupo_id = $request->grupo_id;
            $circular->seccion = $request->seccion;
            $circular->fecha_expiracion = $request->fecha_expiracion ? $request->fecha_expiracion : null;
            $circular->save();

            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Circular subida exitosamente!'
            ]);
            return redirect()->route('circulares.index');
        } catch (\Exception $e) {
            Log::error('Error al subir circular: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al subir la circular. Por favor, inténtalo de nuevo.'
            ]);
            return redirect()->route('circulares.index');
        }
    }

    public function updateCircular(CircularRequest $request, $id)
    {
        $circular = Circular::findOrFail($id);
        
        // Verificar si el usuario es el creador o administrador
        if (Auth::user()->id !== $circular->usuario_id && Auth::user()->rol !== 'administrador') {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'No tienes permiso para editar esta circular'
            ]);
            return redirect()->route('circulares.index');
        }

        $circular->titulo = $request->titulo;
        $circular->descripcion = $request->descripcion;
        $circular->grupo_id = $request->grupo_id;
        $circular->seccion = $request->seccion;
        $circular->fecha_expiracion = $request->fecha_expiracion ? $request->fecha_expiracion : null;

        if ($request->hasFile('archivo')) {
            try {
                // Eliminar el archivo anterior si existe
                if ($circular->archivo) {
                    Storage::disk('s3')->delete($circular->archivo);
                }
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs('circulares', $nombreArchivo, 's3');
                $circular->archivo = $rutaArchivo;
                $circular->nombre_archivo_original = $archivo->getClientOriginalName();
                $circular->tipo_archivo = $archivo->getClientMimeType();
            } catch (\Exception $e) {
                Log::error('Error al actualizar archivo de circular: ' . $e->getMessage());
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ]);
                return redirect()->route('circulares.index');
            }
        }

        $circular->save();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Circular actualizada exitosamente!'
        ]);
        return redirect()->route('circulares.index');
    }

    public function destroyCircular($id)
    {
        $circular = Circular::findOrFail($id);
        
        // Verificar si el usuario es el creador o administrador
        if (Auth::user()->id !== $circular->usuario_id && Auth::user()->rol !== 'administrador') {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar esta circular'
            ]);
            return redirect()->route('circulares.index');
        }

        // Eliminar el archivo si existe
        if ($circular->archivo) {
            Storage::disk('s3')->delete($circular->archivo);
        }
        $circular->delete();
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Circular eliminada exitosamente!'
        ]);
        return redirect()->route('circulares.index');
    }

    public function downloadCircular($id)
    {
        $circular = Circular::findOrFail($id);
        
        try {
            $url = Storage::disk('s3')->url($circular->archivo);
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Error al descargar circular: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al descargar la circular.'
            ]);
            return redirect()->back();
        }
    }
}

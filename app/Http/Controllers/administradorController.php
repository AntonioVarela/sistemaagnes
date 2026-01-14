<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\materia;
use App\Models\grupo;
use App\Models\User;
use App\Models\tarea;
use App\Models\horario;
use App\Models\anuncio;
use App\Models\Curso;
use App\Http\Requests\AnuncioRequest;
use App\Http\Requests\TareaRequest;
use App\Http\Requests\CircularRequest;
use App\Http\Requests\UsuarioRequest;
use App\Models\Circular;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Imports\HorariosCsvImport;

class administradorController extends Controller
{
    /**
     * Helper method para manejar archivos
     */
    private function handleFileUpload($request, $object, $directory = 'archivos')
    {
        if ($request->hasFile('archivo')) {
            try {
                // Eliminar el archivo anterior si existe
                if ($object->archivo) {
                    Storage::disk('s3')->delete($object->archivo);
                }
                
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $rutaArchivo = $archivo->storeAs($directory, $nombreArchivo, 's3');
                
                return [
                    'success' => true,
                    'ruta' => $rutaArchivo,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'tipo' => $archivo->getClientMimeType()
                ];
            } catch (\Exception $e) {
                Log::error("Error al subir archivo: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
                ];
            }
        }
        return ['success' => true];
    }

    /**
     * Helper method para mostrar toast messages
     */
    private function flashToast($type, $message)
    {
        session()->flash('toast', [
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Helper method para verificar permisos de eliminación
     */
    private function checkDeletePermission($object, $redirectRoute)
    {
        if (Auth::user()->id !== $object->usuario_id && Auth::user()->rol !== 'administrador') {
            $this->flashToast('error', 'No tienes permiso para eliminar este elemento');
            return redirect()->route($redirectRoute);
        }
        return null;
    }

    /**
     * Helper method para eliminar archivo y objeto
     */
    private function deleteWithFile($object, $successMessage, $redirectRoute)
    {
        // Eliminar el archivo si existe
        if ($object->archivo) {
            Storage::disk('s3')->delete($object->archivo);
        }
        
        $object->delete();
        $this->flashToast('success', $successMessage);
        return redirect()->route($redirectRoute);
    }
    public function indexDashboard()
    {
        // Verificar autenticación primero
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        if(Auth::user()->rol == 'Coordinador Primaria'){
            // Buscar todos los grupos de sección Primaria
            // Ordenar por número (numéricamente) y luego por letra
            $grupos = grupo::where('seccion', 'Primaria')
                ->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')
                ->get();
            
            // Obtener todos los horarios de estos grupos
            $horarios = horario::with(['grupo', 'materia'])
                ->whereIn('grupo_id', $grupos->pluck('id'))
                ->orderBy('grupo_id')
                ->get();

        } else if(Auth::user()->rol == 'Coordinador Secundaria'){
            // Buscar todos los grupos de sección Secundaria
            // Ordenar por número (numéricamente) y luego por letra
            $grupos = grupo::where('seccion', 'Secundaria')
                ->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')
                ->get();
            
            // Obtener todos los horarios de estos grupos
            $horarios = horario::with(['grupo', 'materia'])
                ->whereIn('grupo_id', $grupos->pluck('id'))
                ->orderBy('grupo_id')
                ->get();
        }
        if(Auth::user()->rol == 'administrador'){
            $horarios = horario::with(['grupo', 'materia'])
                ->orderBy('grupo_id')
                ->get();
            $grupos = grupo::orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
        }
        if(Auth::user()->rol == 'Maestro'){
            $horarios = horario::with(['grupo', 'materia'])
                ->where('maestro_id', Auth::user()->id)
                ->orderBy('grupo_id')
                ->get();
            $grupos = grupo::whereIn('id', $horarios->pluck('grupo_id'))->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
        }

        // Asegurar que las variables estén definidas
        $horarios = $horarios ?? collect();
        $grupos = $grupos ?? collect();

        return view('dashboard', compact('horarios', 'grupos'));
    }

    //Tareas   
    public function index()
    {
        // Verificar autenticación primero
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        if(Auth::user()->rol == 'administrador'){
            $horario = horario::all();
            $grupos = grupo::orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get(); 
        } else if(Auth::user()->rol == 'Coordinador Primaria'){
            // Coordinador Primaria ve todos los grupos de su sección
            $grupos = grupo::where('seccion', 'Primaria')->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
            $horario = horario::whereIn('grupo_id', $grupos->pluck('id'))->get();
        } else if(Auth::user()->rol == 'Coordinador Secundaria'){
            // Coordinador Secundaria ve todos los grupos de su sección
            $grupos = grupo::where('seccion', 'Secundaria')->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
            $horario = horario::whereIn('grupo_id', $grupos->pluck('id'))->get();
        } else{
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
        }
        $seccion = grupo::select('seccion')->whereIn('id', $grupos->pluck('id'))->groupBy('seccion')->get();
        
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->orderBy('nombre', 'asc')->get();
        
        // Aplicar filtros
        $query = tarea::query();
        
        // Si no es administrador ni coordinador, filtrar por horarios del usuario
        if (Auth::user()->rol !== 'administrador' && Auth::user()->rol !== 'Coordinador Primaria' && Auth::user()->rol !== 'Coordinador Secundaria') {
            $materiasPermitidas = $horario->pluck('materia_id')->unique()->toArray();
            $gruposPermitidos = $horario->pluck('grupo_id')->unique()->toArray();
            
            // Filtrar por materias y grupos del horario del usuario
            $query->whereIn('materia', $materiasPermitidas)
                  ->whereIn('grupo', $gruposPermitidos);
        } else if (Auth::user()->rol == 'Coordinador Primaria') {
            // Coordinador Primaria ve todas las tareas de su sección
            $query->whereIn('grupo', $grupos->pluck('id'));
        } else if (Auth::user()->rol == 'Coordinador Secundaria') {
            // Coordinador Secundaria ve todas las tareas de su sección
            $query->whereIn('grupo', $grupos->pluck('id'));
        }
        
        // Filtro por grupo (si se especifica)
        // NOTA: Si se filtra por grupo específico, se muestran todas las tareas de ese grupo
        // independientemente de la materia (útil para administradores)
        $filtroPorGrupoEspecifico = request('grupo_filter');
        if ($filtroPorGrupoEspecifico) {
            $query->where('grupo', $filtroPorGrupoEspecifico);
        }
        
        // Filtro por materia (si se especifica)
        if (request('materia_filter')) {
            $query->where('materia', request('materia_filter'));
        }
        
        // Filtrar solo tareas de las últimas 2 semanas (basado en fecha de creación)
        // NOTA: Si se filtra por grupo específico, NO aplicamos el filtro de fecha
        // para que se puedan ver todas las tareas de ese grupo
        if (!$filtroPorGrupoEspecifico) {
            $fechaDosSemanasAtras = now()->subWeeks(2)->startOfDay();
            $query->where('created_at', '>=', $fechaDosSemanasAtras);
        }
        
        // Obtener solo las 10 tareas más recientes, ordenadas por fecha de creación descendente
        // NOTA: Si hay más de 10 tareas que cumplen los filtros, solo se mostrarán las 10 más recientes
        $tareas = $query->orderBy('created_at', 'desc')->take(10)->get();

        return view('tareas',compact('grupos','materias','tareas','horario','seccion')); // Cambiado a 'tareas'
    }

    public function updateTareas(TareaRequest $request, $id)
    {
        $tarea = tarea::findOrFail($id);
        
        // Verificar permisos: solo el creador o administrador pueden editar
        // Nota: Si la tarea no tiene user_id, solo administradores pueden editarla
        if ($tarea->user_id && Auth::user()->id !== $tarea->user_id && Auth::user()->rol !== 'administrador') {
            $this->flashToast('error', 'No tienes permiso para editar esta tarea');
            return redirect()->route('tareas.index');
        }
        
        // Verificar que el usuario tenga acceso al grupo/materia si no es administrador
        if (Auth::user()->rol !== 'administrador') {
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $gruposPermitidos = $horario->pluck('grupo_id')->toArray();
            $materiasPermitidas = $horario->pluck('materia_id')->toArray();
            
            $grupoAEditar = $request->has('grupo') ? $request->grupo : $tarea->grupo;
            $materiaAEditar = $request->has('materia') ? $request->materia : $tarea->materia;
            
            if (!in_array($grupoAEditar, $gruposPermitidos) || !in_array($materiaAEditar, $materiasPermitidas)) {
                $this->flashToast('error', 'No tienes permiso para editar tareas de este grupo o materia');
                return redirect()->route('tareas.index');
            }
        }
        
        $tarea->descripcion = $request->descripcion;
        $tarea->fecha_entrega = $request->fecha_entrega;
        $tarea->hora_entrega = $request->hora_entrega;
        
        // Manejar archivo si se sube uno nuevo
        $fileResult = $this->handleFileUpload($request, $tarea, 'archivos');
        if (!$fileResult['success']) {
            $this->flashToast('error', $fileResult['error']);
            return redirect()->route('tareas.index');
        }
        
        if (isset($fileResult['ruta']) && $fileResult['ruta']) {
            $tarea->archivo = $fileResult['ruta'];
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
        $this->flashToast('success', '¡Tarea actualizada exitosamente!');
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
        
        // Asignar el usuario que crea la tarea (si el campo existe en la BD)
        // Nota: Esto requiere agregar user_id a la migración de tareas
        if (Schema::hasColumn('tareas', 'user_id')) {
            $tarea->user_id = Auth::user()->id;
        }
        
        // Validar que el usuario tenga acceso al grupo/materia seleccionados
        if (Auth::user()->rol !== 'administrador') {
            $grupoSeleccionado = count($horario) == 1 ? $horario[0]->grupo_id : request('grupo');
            $materiaSeleccionada = count($horario) == 1 ? $horario[0]->materia_id : request('materia');
            
            $tieneAcceso = $horario->where('grupo_id', $grupoSeleccionado)
                                  ->where('materia_id', $materiaSeleccionada)
                                  ->isNotEmpty();
            
            if (!$tieneAcceso) {
                $this->flashToast('error', 'No tienes permiso para crear tareas en este grupo o materia');
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
        
        // Verificar permisos: solo el creador o administrador pueden eliminar
        // Nota: Si la tarea no tiene user_id, solo administradores pueden eliminarla
        if ($tarea->user_id && Auth::user()->id !== $tarea->user_id && Auth::user()->rol !== 'administrador') {
            $this->flashToast('error', 'No tienes permiso para eliminar esta tarea');
            return redirect()->route('tareas.index');
        }
        
        // Verificar que el usuario tenga acceso al grupo/materia si no es administrador
        if (Auth::user()->rol !== 'administrador') {
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $gruposPermitidos = $horario->pluck('grupo_id')->toArray();
            $materiasPermitidas = $horario->pluck('materia_id')->toArray();
            
            if (!in_array($tarea->grupo, $gruposPermitidos) || !in_array($tarea->materia, $materiasPermitidas)) {
                $this->flashToast('error', 'No tienes permiso para eliminar tareas de este grupo o materia');
                return redirect()->route('tareas.index');
            }
        }
        
        // Eliminar el archivo si existe
        if ($tarea->archivo) {
            Storage::disk('s3')->delete($tarea->archivo);
        }
        $tarea->delete();
        $this->flashToast('success', '¡Tarea eliminada exitosamente!');
        return redirect()->route('tareas.index');
    }

    //Alumnos
    public function showAlumnos($id)
    {
        $grupo = grupo::findOrFail($id);
        
        // Validar permisos de acceso según el rol (solo si el usuario está autenticado y tiene rol)
        if (Auth::check() && !empty(Auth::user()->rol)) {
            $rol = Auth::user()->rol;
            
            if ($rol == 'Coordinador Primaria') {
                // Coordinador Primaria solo puede acceder a grupos de su sección
                if ($grupo->seccion !== 'Primaria') {
                    abort(403, 'No tienes permisos para acceder a este grupo.');
                }
            } else if ($rol == 'Coordinador Secundaria') {
                // Coordinador Secundaria solo puede acceder a grupos de su sección
                if ($grupo->seccion !== 'Secundaria') {
                    abort(403, 'No tienes permisos para acceder a este grupo.');
                }
            } else if ($rol == 'Maestro') {
                // Maestros solo pueden acceder a grupos de sus horarios
                $horario = horario::where('maestro_id', Auth::user()->id)->get();
                $gruposPermitidos = $horario->pluck('grupo_id')->toArray();
                if (!in_array($id, $gruposPermitidos)) {
                    abort(403, 'No tienes permisos para acceder a este grupo.');
                }
            }
            // Los administradores tienen acceso a todos los grupos
        }
        // Los usuarios sin rol o no autenticados pueden acceder a cualquier grupo
        
        $materias = materia::all();
        $usuarios = User::all();
        $tareas = tarea::where('grupo', $id)->select('*')->get(); // Cambiado a 'tareas'
        
        // Filtrar anuncios de las últimas 2 semanas (basado en fecha de creación)
        $fechaDosSemanasAtras = now()->subWeeks(2)->startOfDay();
        $anuncios = anuncio::porGrupo($id)
            ->activos()
            ->where('created_at', '>=', $fechaDosSemanasAtras)
            ->get();
        
        $circulares = Circular::porGrupo($id)->activas()->orderBy('created_at', 'desc')->take(3)->get();
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
    public function storeUsuario(UsuarioRequest $request)
    {
        try {
            // Log de los datos recibidos para debugging
            Log::info('Creando usuario con datos:', $request->all());
            
            // Crear el usuario usando create() para mejor manejo de errores
            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'rol' => $request->rol,
            ]);
            
            Log::info('Usuario creado exitosamente con ID: ' . $usuario->id);
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Usuario creado exitosamente!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación específicamente
            Log::error('Error de validación al crear usuario: ' . $e->getMessage());
            Log::error('Errores de validación: ' . json_encode($e->errors()));
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error de validación: ' . implode(', ', array_flatten($e->errors()))
            ]);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error al crear usuario: ' . $e->getMessage());
            Log::error('Datos del request: ' . json_encode($request->all()));
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->route('usuarios.index');
    }
    public function destroyUsuario($id)
    {
        try {
            // Verificar que no se esté eliminando a sí mismo
            if (Auth::id() == $id) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No puedes eliminar tu propia cuenta'
                ]);
                return redirect()->route('usuarios.index');
            }

            $usuario = User::findOrFail($id);
            
            // Verificar si el usuario tiene horarios asignados
            $horariosAsignados = horario::where('maestro_id', $id)->count();
            if ($horariosAsignados > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el usuario porque tiene horarios asignados'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario es titular de algún grupo
            $gruposTitular = grupo::where('titular', $id)->count();
            if ($gruposTitular > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el usuario porque es titular de un grupo'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario tiene circulares creadas
            $circularesUsuario = Circular::where('usuario_id', $id)->count();
            if ($circularesUsuario > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el usuario porque tiene circulares publicadas'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario tiene cursos creados
            $cursosUsuario = Curso::where('user_id', $id)->count();
            if ($cursosUsuario > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el usuario porque tiene cursos creados'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Nota: Las tareas no tienen un campo user_id, por lo que no se puede verificar
            // si el usuario creó tareas específicas

            // Log de la eliminación
            \Log::info("Eliminando usuario: {$usuario->name} (ID: {$id})");

            // Usar delete() para soft delete (recomendado para mantener integridad de datos)
            // Si necesitas eliminación permanente, usar forceDelete()
            $usuario->delete();
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Usuario eliminado exitosamente!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->route('usuarios.index');
    }

    /**
     * Restaurar un usuario eliminado (soft delete)
     */
    public function restoreUsuario($id)
    {
        try {
            $usuario = User::withTrashed()->findOrFail($id);
            
            if (!$usuario->trashed()) {
                session()->flash('toast', [
                    'type' => 'warning',
                    'message' => 'El usuario no está eliminado'
                ]);
                return redirect()->route('usuarios.index');
            }

            $usuario->restore();
            
            \Log::info("Usuario restaurado: {$usuario->name} (ID: {$id})");
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Usuario restaurado exitosamente!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al restaurar usuario: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al restaurar el usuario: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->route('usuarios.index');
    }

    /**
     * Eliminar permanentemente un usuario (force delete)
     */
    public function forceDeleteUsuario($id)
    {
        try {
            // Verificar que no se esté eliminando a sí mismo
            if (Auth::id() == $id) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No puedes eliminar tu propia cuenta'
                ]);
                return redirect()->route('usuarios.index');
            }

            $usuario = User::withTrashed()->findOrFail($id);
            
            // Verificar si el usuario tiene horarios asignados
            $horariosAsignados = horario::where('maestro_id', $id)->count();
            if ($horariosAsignados > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar permanentemente el usuario porque tiene horarios asignados'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario es titular de algún grupo
            $gruposTitular = grupo::where('titular', $id)->count();
            if ($gruposTitular > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar permanentemente el usuario porque es titular de un grupo'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario tiene circulares creadas
            $circularesUsuario = Circular::where('usuario_id', $id)->count();
            if ($circularesUsuario > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar permanentemente el usuario porque tiene circulares publicadas'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Verificar si el usuario tiene cursos creados
            $cursosUsuario = Curso::where('user_id', $id)->count();
            if ($cursosUsuario > 0) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar permanentemente el usuario porque tiene cursos creados'
                ]);
                return redirect()->route('usuarios.index');
            }

            // Log de la eliminación permanente
            \Log::info("Eliminando permanentemente usuario: {$usuario->name} (ID: {$id})");

            // Eliminación permanente
            $usuario->forceDelete();
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Usuario eliminado permanentemente!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar permanentemente usuario: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al eliminar permanentemente el usuario: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->route('usuarios.index');
    }

    public function updateUsuario(UsuarioRequest $request, $id)
    {
        // Debug temporal
        \Log::info('Datos recibidos en updateUsuario:', $request->all());
        
        try {
            $usuario = User::findOrFail($id);
            
            // Preparar datos para actualización
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'rol' => $request->rol,
            ];
            
            // Solo actualizar la contraseña si se proporciona una nueva
            if (!empty($request->password)) {
                $updateData['password'] = bcrypt($request->password);
            }
            
            // Usar update() para mejor manejo de errores
            $usuario->update($updateData);
            
            \Log::info('Usuario actualizado con ID: ' . $usuario->id);
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => '¡Usuario actualizado exitosamente!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación específicamente
            Log::error('Error de validación al actualizar usuario: ' . $e->getMessage());
            Log::error('Errores de validación: ' . json_encode($e->errors()));
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error de validación: ' . implode(', ', array_flatten($e->errors()))
            ]);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            Log::error('Datos del request: ' . json_encode($request->all()));
            Log::error('ID del usuario: ' . $id);
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->route('usuarios.index');
    }
    //Horarios
    public function showHorarios()
    {
        // Verificar autenticación primero
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $query = horario::query();

        // Aplicar filtros si existen
        if (request('search')) {
            $searchTerm = '%' . request('search') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('materia', function($subQ) use ($searchTerm) {
                    $subQ->where('nombre', 'like', $searchTerm);
                })->orWhereHas('grupo', function($subQ) use ($searchTerm) {
                    $subQ->where('nombre', 'like', $searchTerm)
                          ->orWhere('seccion', 'like', $searchTerm);
                })->orWhereHas('maestro', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', $searchTerm);
                });
            });
        }

        if (request('grupo_filter')) {
            $query->where('grupo_id', request('grupo_filter'));
        }

        if (request('materia_filter')) {
            $query->where('materia_id', request('materia_filter'));
        }


        $horarios = $query->with(['grupo', 'materia', 'maestro'])->get();
        $grupos = grupo::orderByRaw('CAST(SPLIT_PART(nombre, \' \', 1) AS INTEGER) ASC, SPLIT_PART(nombre, \' \', 2) ASC')->get();
        $materias = materia::orderBy('nombre', 'asc')->get();
        $usuarios = User::where('rol', 'Maestro')->orderBy('name', 'asc')->get();
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
            // Verificar autenticación primero
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para eliminar horarios.');
            }
            
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

    public function importHorarios(Request $request)
    {
        // Verificar autenticación al inicio y guardar el ID de usuario
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para importar horarios.');
        }
        
        // Guardar información del usuario antes de procesar
        $userId = Auth::id();
        $userRol = Auth::user()->rol;
        
        // Aumentar tiempo de ejecución para archivos grandes
        set_time_limit(300); // 5 minutos
        
        try {
            // Validar archivo CSV
            $request->validate([
                'archivo_csv' => 'required|mimes:csv,txt|max:10240', // 10MB máximo
            ], [
                'archivo_csv.required' => 'Debes seleccionar un archivo CSV.',
                'archivo_csv.mimes' => 'El archivo debe ser de tipo CSV (.csv o .txt).',
                'archivo_csv.max' => 'El archivo no puede ser mayor a 10MB.',
            ]);

            // Verificar que el archivo existe
            if (!$request->hasFile('archivo_csv')) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'No se recibió ningún archivo.'
                ]);
                return redirect()->route('horarios.index');
            }

            $archivo = $request->file('archivo_csv');
            
            // Verificar que el archivo es válido
            if (!$archivo->isValid()) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => 'El archivo no es válido o está corrupto.'
                ]);
                return redirect()->route('horarios.index');
            }

            // Guardar archivo temporalmente
            $filePath = $archivo->getRealPath();
            
            // Guardar sesión antes de procesar (sin regenerar)
            session()->save();
            
            $import = new HorariosCsvImport();
            $import->import($filePath);
            
            // Verificar que el usuario sigue autenticado después del procesamiento
            // Si no, re-autenticar usando el ID guardado
            if (!Auth::check()) {
                Auth::loginUsingId($userId);
            }
            
            // Guardar sesión después de procesar
            session()->save();

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();
            $skippedRows = $import->getSkippedRows();
            $debugInfo = $import->getDebugInfo();

            // Si no se importó nada y no hay errores, puede ser que el archivo esté vacío o los encabezados no coincidan
            if ($successCount == 0 && empty($errors)) {
                $message = 'El archivo se procesó pero no se encontraron datos válidos para importar. ';
                
                // Agregar información de debug si está disponible (sin saltos de línea para evitar problemas en JavaScript)
                if (!empty($debugInfo)) {
                    if (isset($debugInfo['headers'])) {
                        $message .= 'Encabezados encontrados: ' . implode(', ', $debugInfo['headers']) . '. ';
                    }
                    if (isset($debugInfo['normalized_headers'])) {
                        $message .= 'Encabezados normalizados: ' . implode(', ', $debugInfo['normalized_headers']) . '. ';
                    }
                    if (isset($debugInfo['missing_columns_row_1'])) {
                        $message .= 'Columnas faltantes: ' . implode(', ', $debugInfo['missing_columns_row_1']) . '. ';
                    }
                    if (isset($debugInfo['found_columns_row_1'])) {
                        $foundCols = [];
                        foreach ($debugInfo['found_columns_row_1'] as $req => $found) {
                            $foundCols[] = "$req (como: $found)";
                        }
                        if (!empty($foundCols)) {
                            $message .= 'Columnas encontradas: ' . implode(', ', $foundCols) . '. ';
                        }
                    }
                    if (isset($debugInfo['available_columns_row_1'])) {
                        $message .= 'Columnas disponibles: ' . implode(', ', $debugInfo['available_columns_row_1']) . '. ';
                    }
                }
                
                $message .= 'Verifica que el archivo CSV tenga los encabezados correctos: Grupo, Seccion, Materia, Maestro, Dias, Hora Inicio, Hora Fin';
                
                if ($skippedRows > 0) {
                    $message .= " (Se saltaron {$skippedRows} fila(s) vacías)";
                }
                
                session()->flash('toast', [
                    'type' => 'warning',
                    'message' => $message
                ]);
                
                // Guardar información de debug en sesión para mostrar en el modal
                session()->flash('import_debug', $debugInfo);
                
                return redirect()->route('horarios.index');
            }

            $message = "Se importaron {$successCount} horario(s) exitosamente.";
            if (!empty($errors)) {
                $message .= " Errores encontrados: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " y " . (count($errors) - 5) . " más.";
                }
            }

            session()->flash('toast', [
                'type' => $successCount > 0 ? 'success' : 'warning',
                'message' => $message
            ]);

            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Errores de validación
            // Re-autenticar si es necesario
            if (!Auth::check() && isset($userId)) {
                Auth::loginUsingId($userId);
            }
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error de validación: ' . implode(', ', $e->validator->errors()->all())
            ]);
        } catch (\Exception $e) {
            Log::error('Error al importar horarios: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            
            // Re-autenticar si es necesario
            if (!Auth::check() && isset($userId)) {
                Auth::loginUsingId($userId);
            }
            
            $errorMessage = 'Error al importar el archivo: ' . $e->getMessage();
            
            // Si hay errores específicos de la importación, mostrarlos
            if (isset($import)) {
                $importErrors = $import->getErrors();
                if (!empty($importErrors)) {
                    $errorMessage .= '. Errores: ' . implode('; ', array_slice($importErrors, 0, 3));
                }
            }
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => $errorMessage
            ]);
            
            if (isset($import)) {
                session()->flash('import_debug', $import->getDebugInfo());
                session()->flash('import_errors', $import->getErrors());
            }
        }

        // Re-autenticar si es necesario antes de redirigir
        if (!Auth::check() && isset($userId)) {
            Auth::loginUsingId($userId);
        }
        
        // Guardar sesión antes de redirigir
        session()->save();

        return redirect()->route('horarios.index');
    }

    public function downloadPlantillaHorarios()
    {
        try {
            $filename = 'plantilla_importacion_horarios_' . date('Y-m-d') . '.csv';
            
            // Crear contenido CSV sin encabezados - las columnas se manejan por posición fija
            // Orden de columnas: Grupo, Seccion, Materia, Maestro, Dias, Hora Inicio, Hora Fin
            $ejemplos = [
                ['1 A', 'Primaria', 'Matemáticas', 'Juan Pérez', 'Lunes,Martes,Miércoles', '08:00', '09:00'],
                ['1 B', 'Primaria', 'Español', 'María García', 'Jueves,Viernes', '10:00', '11:00'],
                ['2 A', 'Secundaria', 'Inglés', 'Carlos López', 'Lunes,Martes', '14:00', '15:00'],
            ];
            
            // Crear contenido CSV
            $output = fopen('php://temp', 'r+');
            
            // Escribir BOM para UTF-8 (ayuda con Excel)
            fwrite($output, "\xEF\xBB\xBF");
            
            // Opcional: escribir encabezados como comentario o primera línea (será detectado y saltado)
            // Pero mejor sin encabezados para simplificar
            // fputcsv($output, ['Grupo', 'Seccion', 'Materia', 'Maestro', 'Dias', 'Hora Inicio', 'Hora Fin']);
            
            // Escribir ejemplos directamente (sin encabezados)
            foreach ($ejemplos as $ejemplo) {
                fputcsv($output, $ejemplo);
            }
            
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
        } catch (\Exception $e) {
            Log::error('Error al generar plantilla CSV: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Error al generar la plantilla: ' . $e->getMessage()
            ]);
            return redirect()->route('horarios.index');
        }
    }   

    //Anuncios
    public function showAnuncios()
    {
        if(Auth::user()->rol == 'administrador'){
            $horario = horario::all();
            $grupos = grupo::all(); 
            $anuncios = anuncio::with(['user', 'grupo', 'materia'])->activos()->orderBy('created_at', 'desc')->get();
        }
        if(Auth::user()->rol == 'Maestro'){
            $horario = horario::where('maestro_id', Auth::user()->id)->get();
            $grupos = grupo::whereIn('id', $horario->pluck('grupo_id'))->get();
            $anuncios = anuncio::with(['user', 'grupo', 'materia'])
                ->where(function($query) use ($horario) {
                    $query->whereIn('grupo_id', $horario->pluck('grupo_id'))
                          ->orWhere('es_global', true);
                })
                ->activos()
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if(Auth::user()->rol == 'Coordinador Primaria'){
            $grupos = grupo::where('seccion', 'Primaria')->get();
            $anuncios = anuncio::with(['user', 'grupo', 'materia'])
                ->where(function($query) use ($grupos) {
                    $query->whereIn('grupo_id', $grupos->pluck('id'))
                          ->orWhere('es_global', true);
                })
                ->activos()
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if(Auth::user()->rol == 'Coordinador Secundaria'){
            $grupos = grupo::where('seccion', 'Secundaria')->get();
            $anuncios = anuncio::with(['user', 'grupo', 'materia'])
                ->where(function($query) use ($grupos) {
                    $query->whereIn('grupo_id', $grupos->pluck('id'))
                          ->orWhere('es_global', true);
                })
                ->activos()
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $materias = materia::whereIn('id', $horario->pluck('materia_id'))->get();
        return view("anuncios", compact(['anuncios','horario','grupos','materias']));
    }
    public function storeAnuncio(AnuncioRequest $request)
    {
        // Debug temporal
        \Log::info('Datos recibidos en storeAnuncio:', $request->all());
        
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
        // Si es un anuncio global, asignar grupo 1A pero marcar como global
        if ($request->es_global) {
            $anuncio->es_global = true;
            // Asignar grupo 1A (ID 1) para anuncios globales
            $anuncio->grupo_id = 1; // Grupo 1A
            // Asignar la primera materia disponible para mantener consistencia en BD
            $primeraMateria = materia::first();
            $anuncio->materia_id = $primeraMateria ? $primeraMateria->id : 1;
            $anuncio->seccion = 'Primaria'; // Sección del grupo 1A
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
        
        // Debug temporal
        \Log::info('Anuncio guardado con ID:', $anuncio->id);
        
        session()->flash('toast', [
            'type' => 'success',
            'message' => '¡Anuncio creado exitosamente!'
        ]);
        return redirect()->route('anuncios.index');
    }
    
    public function destroyAnuncio($id)
    {
        $anuncio = anuncio::findOrFail($id);
        
        // Verificar permisos
        $permissionCheck = $this->checkDeletePermission($anuncio, 'anuncios.index');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        return $this->deleteWithFile($anuncio, '¡Anuncio eliminado exitosamente!', 'anuncios.index');
    }
    public function updateAnuncio(AnuncioRequest $request, $id)
    {
        $anuncio = anuncio::findOrFail($id);
        
        // Verificar si el usuario es el creador o administrador
        if (Auth::user()->id !== $anuncio->usuario_id && Auth::user()->rol !== 'administrador') {
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
        // Si es un anuncio global, asignar grupo 1A pero marcar como global
        if ($request->es_global) {
            $anuncio->es_global = true;
            // Asignar grupo 1A (ID 1) para anuncios globales
            $anuncio->grupo_id = 1; // Grupo 1A
            // Asignar la primera materia disponible para mantener consistencia en BD
            $primeraMateria = materia::first();
            $anuncio->materia_id = $primeraMateria ? $primeraMateria->id : 1;
            $anuncio->seccion = 'Primaria'; // Sección del grupo 1A
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
            $circular->archivo = $rutaArchivo;
            $circular->nombre_archivo_original = $archivo->getClientOriginalName();
            $circular->tipo_archivo = $archivo->getClientMimeType();
            $circular->usuario_id = Auth::user()->id;
            
            // Si es global, asignar grupo 1A pero marcar como global
            if ($request->es_global) {
                $circular->es_global = true;
                // Asignar grupo 1A (ID 1) para circulares globales
                $circular->grupo_id = 1; // Grupo 1A
                $circular->seccion = 'Primaria'; // Sección del grupo 1A
            } else {
                $circular->es_global = false;
                $circular->grupo_id = $request->grupo_id;
                $circular->seccion = $request->seccion;
            }
            
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
        
        // Si es global, asignar grupo 1A pero marcar como global
        if ($request->es_global) {
            $circular->es_global = true;
            // Asignar grupo 1A (ID 1) para circulares globales
            $circular->grupo_id = 1; // Grupo 1A
            $circular->seccion = 'Primaria'; // Sección del grupo 1A
        } else {
            $circular->es_global = false;
            $circular->grupo_id = $request->grupo_id;
            $circular->seccion = $request->seccion;
        }
        
        // Debug: Log the fecha_expiracion value
        \Log::info('Fecha expiración recibida: ' . $request->fecha_expiracion);
        
        // Manejar la fecha de expiración correctamente
        if ($request->filled('fecha_expiracion')) {
            $circular->fecha_expiracion = $request->fecha_expiracion;
        } else {
            $circular->fecha_expiracion = null;
        }
        
        \Log::info('Fecha expiración asignada: ' . $circular->fecha_expiracion);

        // Manejar archivo si se sube uno nuevo
        $fileResult = $this->handleFileUpload($request, $circular, 'circulares');
        if (!$fileResult['success']) {
            $this->flashToast('error', $fileResult['error']);
            return redirect()->route('circulares.index');
        }
        
        if (isset($fileResult['ruta']) && $fileResult['ruta']) {
            $circular->archivo = $fileResult['ruta'];
            $circular->nombre_archivo_original = $fileResult['nombre_original'];
            $circular->tipo_archivo = $fileResult['tipo'];
        }

        $circular->save();
        $this->flashToast('success', '¡Circular actualizada exitosamente!');
        return redirect()->route('circulares.index');
    }

    public function destroyCircular($id)
    {
        $circular = Circular::findOrFail($id);
        
        // Verificar permisos
        $permissionCheck = $this->checkDeletePermission($circular, 'circulares.index');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        return $this->deleteWithFile($circular, '¡Circular eliminada exitosamente!', 'circulares.index');
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

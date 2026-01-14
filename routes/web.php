<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\administradorController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\TareasPdfController;


Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('home');



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/tareas', [administradorController::class,'index'])->name('tareas.index');
Route::post('/tareasguardar', [administradorController::class,'store'])->name('tareas.store');

Route::get("/grupos",[administradorController::class,'showGrupos'])->name('grupos.index')->middleware('admin');
Route::post('/gruposguardar', [administradorController::class,'storeGrupo'])->name('grupos.store')->middleware('admin');
Route::delete('/grupos/{id}', [administradorController::class,'destroyGrupo'])->name('grupos.destroy')->middleware('admin');
Route::post('/grupos/{id}/update', [administradorController::class,'updateGrupo'])->name('grupos.update')->middleware('admin');

Route::get('/materias',[administradorController::class,'showMaterias'])->name('materias.index')->middleware('admin');
Route::post('/materiasguardar', [administradorController::class,'storeMateria'])->name('materias.store')->middleware('admin');
Route::delete('/materias/{id}', [administradorController::class,'destroyMateria'])->name('materias.destroy')->middleware('admin');
Route::post('/materias/{id}/update', [administradorController::class,'updateMateria'])->name('materias.update')->middleware('admin');

Route::post('/tareas/{id}/update', [administradorController::class,'updateTareas'])->name('tareas.update');
Route::post('/tareas/{id}/destroy', [administradorController::class,'destroyTarea'])->name('tareas.destroy');
Route::get('/dashboard', [administradorController::class,'indexDashboard'])->name('dashboard')->middleware(['auth', 'verified']);

    Route::get('/usuarios',[administradorController::class,'showUsuarios'])->name('usuarios.index')->middleware('admin');
Route::post('/usuarios', [administradorController::class,'storeUsuario'])->name('usuarios.store')->middleware('admin');
Route::delete('/usuarios/{id}', [administradorController::class,'destroyUsuario'])->name('usuarios.destroy')->middleware('admin');
Route::put('/usuarios/{id}', [administradorController::class,'updateUsuario'])->name('usuarios.update')->middleware('admin');

Route::get('/horarios',[administradorController::class,'showHorarios'])->name('horarios.index');
Route::post('/horariosguardar', [administradorController::class,'storeHorario'])->name('horarios.store');
Route::post('/horarios/importar', [administradorController::class,'importHorarios'])->name('horarios.import');
Route::get('/horarios/plantilla', [administradorController::class,'downloadPlantillaHorarios'])->name('horarios.plantilla');
Route::delete('/horarios/{id}', [administradorController::class,'destroyHorario'])->name('horarios.destroy');
Route::post('/horarios/{id}/update', [administradorController::class,'updateHorario'])->name('horarios.update');

Route::get('/anuncios',[administradorController::class,'showAnuncios'])->name('anuncios.index');
Route::post('/anunciosguardar', [administradorController::class,'storeAnuncio'])->name('anuncios.store');
Route::delete('/anuncios/{id}', [administradorController::class,'destroyAnuncio'])->name('anuncios.destroy');
Route::post('/anuncios/{id}/update', [administradorController::class,'updateAnuncio'])->name('anuncios.update');

Route::get('/circulares',[administradorController::class,'indexCirculares'])->name('circulares.index');
Route::post('/circulares', [administradorController::class,'storeCircular'])->name('circulares.store');
Route::put('/circulares/{id}', [administradorController::class,'updateCircular'])->name('circulares.update');
Route::delete('/circulares/{id}', [administradorController::class,'destroyCircular'])->name('circulares.destroy');
Route::get('/circulares/{id}/download', [administradorController::class,'downloadCircular'])->name('circulares.download');

// Ruta de prueba para crear circular global
Route::get('/test-circular-global', function() {
    try {
        $circular = new \App\Models\Circular();
        $circular->titulo = 'Circular Global de Prueba';
        $circular->archivo = 'test/test.pdf';
        $circular->nombre_archivo_original = 'test.pdf';
        $circular->tipo_archivo = 'application/pdf';
        $circular->usuario_id = auth()->id();
        $circular->es_global = true;
        $circular->grupo_id = null;
        $circular->seccion = null;
        $circular->fecha_expiracion = null;
        
        $circular->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Circular global creada exitosamente',
            'circular_id' => $circular->id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
})->name('test.circular.global');

// Rutas para cursos
Route::resource('cursos', CursosController::class)->middleware('auth');
Route::post('/cursos/{curso}/toggle-status', [CursosController::class, 'toggleStatus'])->name('cursos.toggle-status')->middleware('auth');
Route::get('/api/cursos-activos', [CursosController::class, 'getCursosActivos'])->name('cursos.activos')->middleware('auth');


});
// Rutas para descarga de PDF de tareas
Route::get('/tareas/{grupoId}/pdf', [TareasPdfController::class, 'downloadTareasPdf'])->name('tareas.pdf.download');
Route::get('/tareas/{grupoId}/pdf-preview', [TareasPdfController::class, 'previewTareasPdf'])->name('tareas.pdf.preview');

Route::get('/actividades/{id}', [administradorController::class,'showAlumnos'])->name('tareas.alumnos');//Cambiar a volt

require __DIR__.'/auth.php';

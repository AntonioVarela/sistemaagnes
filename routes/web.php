<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\administradorController;

Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/tareas', [administradorController::class,'index'])->name('tareas.index');
Route::post('/tareasguardar', [administradorController::class,'store'])->name('tareas.store');

Route::get("/grupos",[administradorController::class,'showGrupos'])->name('grupos.index');
Route::post('/gruposguardar', [administradorController::class,'storeGrupo'])->name('grupos.store');
Route::delete('/grupos/{id}', [administradorController::class,'destroyGrupo'])->name('grupos.destroy');
Route::post('/grupos/{id}/update', [administradorController::class,'updateGrupo'])->name('grupos.update');

Route::get('/materias',[administradorController::class,'showMaterias'])->name('materias.index');
Route::post('/materiasguardar', [administradorController::class,'storeMateria'])->name('materias.store');
Route::delete('/materias/{id}', [administradorController::class,'destroyMateria'])->name('materias.destroy');
Route::post('/materias/{id}/update', [administradorController::class,'updateMateria'])->name('materias.update');

Route::post('/tareas/{id}/update', [administradorController::class,'update'])->name('tareas.update');
Route::post('/tareas/{id}/destroy', [administradorController::class,'destroyTarea'])->name('tareas.destroy');
Route::get('/dashboard', [administradorController::class,'indexDashboard'])->name('dashboard')->middleware(['auth', 'verified']);

    Route::get('/usuarios',[administradorController::class,'showUsuarios'])->name('usuarios.index');
Route::post('/usuariosguardar', [administradorController::class,'storeUsuario'])->name('usuarios.store');
Route::delete('/usuarios/{id}', [administradorController::class,'destroyUsuario'])->name('usuarios.destroy');
Route::post('/usuarios/{id}/update', [administradorController::class,'updateUsuario'])->name('usuarios.update');

Route::get('/horarios',[administradorController::class,'showHorarios'])->name('horarios.index');
Route::post('/horariosguardar', [administradorController::class,'storeHorario'])->name('horarios.store');
Route::delete('/horarios/{id}', [administradorController::class,'destroyHorario'])->name('horarios.destroy');
Route::post('/horarios/{id}/update', [administradorController::class,'updateHorario'])->name('horarios.update');

Route::get('/anuncios',[administradorController::class,'showAnuncios'])->name('anuncios.index');
Route::post('/anunciosguardar', [administradorController::class,'storeAnuncio'])->name('anuncios.store');
Route::delete('/anuncios/{id}', [administradorController::class,'destroyAnuncio'])->name('anuncios.destroy');
Route::post('/anuncios/{id}/update', [administradorController::class,'updateAnuncio'])->name('anuncios.update');

});

Route::get('/actividades/{id}', [administradorController::class,'showAlumnos'])->name('tareas.alumnos');//Cambiar a volt

require __DIR__.'/auth.php';

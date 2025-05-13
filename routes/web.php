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
Route::get('/actividades/{id}', [administradorController::class,'showAlumnos'])->name('tareas.alumnos');//Cambiar a volt
Route::get("/grupos",[administradorController::class,'showGrupos'])->name('grupos.index');
Route::post('/gruposguardar', [administradorController::class,'storeGrupo'])->name('grupos.store');
Route::delete('/grupos/{id}', [administradorController::class,'destroyGrupo'])->name('grupos.destroy');

Route::get('/grupos/{id}/edit', function ($id) {
    return view('grupos.edit', ['id' => $id]);
})->name('grupos.edit');

Route::get('/materias',[administradorController::class,'showMaterias'])->name('materias.index');
Route::post('/materiasguardar', [administradorController::class,'storeMateria'])->name('materias.store');
Route::delete('/materias/{id}', [administradorController::class,'destroyMateria'])->name('materias.destroy');
Route::get('/materias/{id}/edit', function ($id) {
    return view('materias.edit', ['id' => $id]);
})->name('materias.edit');

Route::get('/tareas/{id}/edit', function ($id) {
    return view('tareas.edit', ['id' => $id]);
})->name('tareas.edit');
Route::post('/tareas/{id}/update', [administradorController::class,'update'])->name('tareas.update');
Route::get('/tareas/{id}/delete', function ($id) {
    return view('tareas.delete', ['id' => $id]);
})->name('tareas.delete');
Route::post('/tareas/{id}/destroy', [administradorController::class,'destroy'])->name('tareas.destroy');
Route::get('/dashboard', [administradorController::class,'indexDashboard'])->name('dashboard')->middleware(['auth', 'verified']);

    Route::get('/usuarios',[administradorController::class,'showUsuarios'])->name('usuarios.index');
Route::post('/usuariosguardar', [administradorController::class,'storeUsuario'])->name('usuarios.store');
Route::delete('/usuarios/{id}', [administradorController::class,'destroyUsuario'])->name('usuarios.destroy');
Route::get('/usuarios/{id}/edit', function ($id) {
    return view('usuarios.edit', ['id' => $id]);
})->name('usuarios.edit');
Route::post('/usuarios/{id}/update', [administradorController::class,'updateUsuario'])->name('usuarios.update');

Route::get('/horarios',[administradorController::class,'showHorarios'])->name('horarios.index');
Route::post('/horariosguardar', [administradorController::class,'storeHorario'])->name('horarios.store');
Route::delete('/horarios/{id}', [administradorController::class,'destroyHorario'])->name('horarios.destroy');
});



require __DIR__.'/auth.php';

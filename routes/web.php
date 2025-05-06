<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\administradorController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tareas', [administradorController::class,'index'])->name('tareas.index');
Route::post('/tareasguardar', [administradorController::class,'store'])->name('tareas.store');
Route::get('/tareasalumno', [administradorController::class,'showAlumnos'])->name('tareas.alumnos');//Cambiar a volt
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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});



require __DIR__.'/auth.php';

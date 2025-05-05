<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\administradorController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tareas', [administradorController::class,'index'])->name('tareas.index');
Route::get('/tareasguardar', [administradorController::class,'store'])->name('tareas.store');
Route::get('/tareasalumno', [administradorController::class,'showAlumnos'])->name('tareas.alumnos');

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

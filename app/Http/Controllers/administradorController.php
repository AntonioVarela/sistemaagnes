<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class administradorController extends Controller
{
    public function index()
    {
        return view('tareas');
    }

    public function store()
    {
        // Logic to store a new task
    }

    public function showAlumnos()
    {
      return view("tareasAlumno");   // Logic to show students
    }
}

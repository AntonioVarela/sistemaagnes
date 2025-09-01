<?php

namespace App\Http\Controllers;

use App\Models\grupo;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $grupos = grupo::orderBy('seccion')->orderBy('nombre')->get();
        return view('welcome', compact('grupos'));
    }
}

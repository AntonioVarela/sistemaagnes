<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre'
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'materia_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'horarios', 'materia_id', 'grupo_id');
    }

    public function maestro()
    {
        return $this->belongsTo(User::class, 'maestro');
    }

    public function tareas()
    {
        return $this->hasMany(tarea::class, 'materia');
    }
}

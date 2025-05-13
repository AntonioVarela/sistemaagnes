<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class materia extends Model
{
    //
    public function horario()
    {
        return $this->hasOne(horario::class, 'materia');
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

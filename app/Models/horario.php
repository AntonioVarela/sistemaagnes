<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class horario extends Model
{
    //
    public function grupo()
    {
        return $this->belongsTo(grupo::class, 'grupo_id');
    }
    public function materia()
    {
        return $this->belongsTo(materia::class, 'materia_id');
    }  
}

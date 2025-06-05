<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class horario extends Model
{
    use SoftDeletes;

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

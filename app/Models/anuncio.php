<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class anuncio extends Model
{
    //
    
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class anuncio extends Model
{
    use SoftDeletes;

    //
    
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

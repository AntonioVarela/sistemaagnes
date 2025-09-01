<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class grupo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'seccion',
        'titular'
    ];

    public function circulares()
    {
        return $this->hasMany(Circular::class);
    }

    public function anuncios()
    {
        return $this->hasMany(anuncio::class);
    }

    public function tareas()
    {
        return $this->hasMany(tarea::class, 'grupo');
    }
}

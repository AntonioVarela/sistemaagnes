<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class grupo extends Model
{
    use SoftDeletes, HasFactory;

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

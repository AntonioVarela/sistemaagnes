<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Curso extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'categoria',
        'nivel',
        'activo',
        'orden',
        'url_externa',
        'contenido_detallado',
        'user_id'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Relación con el usuario que creó el curso
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para cursos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para ordenar por orden
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('created_at', 'desc');
    }

    // Accessor para la URL de la imagen
    public function getUrlImagenAttribute()
    {
        if ($this->imagen) {
            return Storage::disk('s3')->url($this->imagen);
        }
        return null;
    }

    // Scope para filtrar por categoría
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Scope para filtrar por nivel
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }
}

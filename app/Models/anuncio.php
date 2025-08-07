<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class anuncio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'contenido',
        'archivo',
        'fecha_expiracion',
        'usuario_id',
        'grupo_id',
        'materia_id',
        'seccion'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
    ];
    
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

    // Scope para obtener solo anuncios activos (no expirados)
    public function scopeActivos($query)
    {
        return $query->where(function($q) {
            $q->whereNull('fecha_expiracion')
              ->orWhere('fecha_expiracion', '>=', Carbon::today());
        });
    }

    // Scope para obtener anuncios expirados
    public function scopeExpirados($query)
    {
        return $query->where('fecha_expiracion', '<', Carbon::today());
    }

    // Método para verificar si el anuncio está activo
    public function estaActivo()
    {
        return $this->fecha_expiracion === null || $this->fecha_expiracion >= Carbon::today();
    }
}

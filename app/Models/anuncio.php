<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
        'seccion',
        'es_global'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
    ];
    
    public function grupo()
    {
        return $this->belongsTo(grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(materia::class);
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

    // Scope para obtener anuncios globales
    public function scopeGlobales($query)
    {
        return $query->where('es_global', true);
    }

    // Scope para obtener anuncios por grupo
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where(function($q) use ($grupoId) {
            // Anuncios específicos del grupo
            $q->where('grupo_id', $grupoId);
            
            // Anuncios globales (tienen grupo_id = 1 pero es_global = true)
            $q->orWhere(function($subQ) {
                $subQ->where('es_global', true)
                     ->where('grupo_id', 1); // Grupo 1A
            });
        });
    }

    // Método para verificar si el anuncio está activo
    public function estaActivo()
    {
        return $this->fecha_expiracion === null || $this->fecha_expiracion >= Carbon::today();
    }

    // Método para obtener la URL del archivo
    public function getUrlArchivoAttribute()
    {
        return Storage::disk('s3')->url($this->archivo);
    }
}

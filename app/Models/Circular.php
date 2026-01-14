<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Circular extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'circulares';

    protected $fillable = [
        'titulo',
        'archivo',
        'nombre_archivo_original',
        'tipo_archivo',
        'usuario_id',
        'grupo_id',
        'seccion',
        'fecha_expiracion',
        'es_global'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date:Y-m-d',
    ];
    
    public function grupo()
    {
        return $this->belongsTo(grupo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scope para obtener solo circulares activas (no expiradas)
    public function scopeActivas($query)
    {
        return $query->where(function($q) {
            $q->whereNull('fecha_expiracion')
              ->orWhere('fecha_expiracion', '>=', Carbon::today());
        });
    }

    // Scope para obtener circulares expiradas
    public function scopeExpiradas($query)
    {
        return $query->where('fecha_expiracion', '<', Carbon::today());
    }

    // Scope para obtener circulares globales
    public function scopeGlobales($query)
    {
        return $query->where('es_global', true);
    }

    // Scope para obtener circulares por grupo específico o globales
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where(function($q) use ($grupoId) {
            // Circulares específicas del grupo
            $q->where('grupo_id', $grupoId);
            
            // Circulares globales (tienen grupo_id = 1 pero es_global = true)
            $q->orWhere(function($subQ) {
                $subQ->where('es_global', true)
                     ->where('grupo_id', 1); // Grupo 1A
            });
        });
    }

    // Scope para obtener circulares por grupo específico, globales y por sección
    public function scopePorGrupoYSeccion($query, $grupoId, $seccion = null)
    {
        return $query->where(function($q) use ($grupoId, $seccion) {
            // Circulares específicas del grupo
            $q->where('grupo_id', $grupoId);
            
            // Circulares globales (tienen grupo_id = 1 pero es_global = true)
            $q->orWhere(function($subQ) {
                $subQ->where('es_global', true)
                     ->where('grupo_id', 1); // Grupo 1A
            });
            
            // Circulares por sección (si se especifica)
            if ($seccion) {
                $q->orWhere(function($subQ) use ($seccion) {
                    $subQ->where('es_global', false)
                         ->where('seccion', $seccion);
                });
            }
        });
    }

    // Método para verificar si la circular está activa
    public function estaActiva()
    {
        return $this->fecha_expiracion === null || $this->fecha_expiracion >= Carbon::today();
    }

    // Método para obtener la URL del archivo
    public function getUrlArchivoAttribute()
    {
        return Storage::disk('s3')->url($this->archivo);
    }

    // Método para formatear el tamaño del archivo
    public function getTamanioFormateadoAttribute()
    {
        if (!$this->archivo) return null;
        
        $bytes = Storage::disk('s3')->size($this->archivo);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

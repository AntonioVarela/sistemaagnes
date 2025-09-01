<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Circular extends Model
{
    use SoftDeletes;
    protected $table = 'circulares';

    protected $fillable = [
        'titulo',
        'descripcion',
        'archivo',
        'nombre_archivo_original',
        'tipo_archivo',
        'usuario_id',
        'grupo_id',
        'seccion',
        'fecha_expiracion'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
    ];
    
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
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

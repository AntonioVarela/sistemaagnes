<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class tarea extends Model
{
    use SoftDeletes;

    protected $table = 'tareas';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_entrega',
        'hora_entrega',
        'archivo',
        'grupo',
        'materia'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'hora_entrega' => 'datetime',
    ];

    /**
     * Obtener la materia relacionada
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia');
    }

    /**
     * Obtener el grupo relacionado
     */
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\grupo::class, 'grupo');
    }

    /**
     * Obtener el usuario que creó la tarea
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo', $grupoId);
    }

    /**
     * Scope para filtrar por materia
     */
    public function scopePorMateria($query, $materiaId)
    {
        return $query->where('materia', $materiaId);
    }

    /**
     * Scope para tareas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('fecha_entrega', '>=', now()->toDateString());
    }

    /**
     * Scope para tareas vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->where('fecha_entrega', '<', now()->toDateString());
    }

    /**
     * Scope para tareas de la semana en curso
     */
    public function scopeDeEstaSemana($query)
    {
        return $query->whereBetween('fecha_entrega', [
            now()->startOfWeek()->toDateString(), 
            now()->endOfWeek()->toDateString()
        ]);
    }

    /**
     * Scope para tareas desde viernes de la semana pasada hasta jueves de la semana en curso
     */
    public function scopeDeViernesAJueves($query)
    {
        // Obtener el viernes de la semana pasada
        $viernesSemanaPasada = now()->subWeek()->next(5); // Viernes de la semana pasada
        
        // Obtener el jueves de la semana en curso
        $juevesSemanaActual = now()->next(4); // Jueves de la semana actual
        
        return $query->whereBetween('fecha_entrega', [
            $viernesSemanaPasada->toDateString(), 
            $juevesSemanaActual->toDateString()
        ]);
    }
}

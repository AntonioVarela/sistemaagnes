<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tarea;
use App\Models\grupo;
use App\Models\materia;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TareasPdfController extends Controller
{
    /**
     * Obtener tareas de la semana en curso para un grupo
     */
    private function obtenerTareasSemana($grupoId)
    {
        return tarea::where('grupo', $grupoId)
            ->deEstaSemana()
            ->with(['materia', 'grupo'])
            ->orderBy('fecha_entrega', 'asc')
            ->get();
    }

    /**
     * Obtener tareas desde viernes de la semana pasada hasta jueves de la siguiente semana
     */
    private function obtenerTareasViernesAJueves($grupoId)
    {
        return tarea::where('grupo', $grupoId)
            ->deViernesAJueves()
            ->with(['materia', 'grupo'])
            ->orderBy('fecha_entrega', 'asc')
            ->get();
    }

    /**
     * Generar PDF de tareas para un grupo específico
     */
    public function downloadTareasPdf($grupoId)
    {
        // No se requiere autenticación para descargar PDFs

        // Obtener el grupo
        $grupo = grupo::findOrFail($grupoId);
        
        // Obtener las tareas del grupo para la semana en curso
        $tareas = $this->obtenerTareasSemana($grupoId);

        // Preparar datos para la tabla
        $tareasData = [];
        foreach ($tareas as $tarea) {
            // Debug: verificar qué contiene la tarea
            \Log::info('Tarea:', [
                'id' => $tarea->id,
                'materia_id' => $tarea->materia,
                'materia_relation' => $tarea->materia,
                'materia_type' => gettype($tarea->materia)
            ]);
            
            // Obtener nombre de materia de forma segura
            $nombreMateria = 'No especificada';
            if ($tarea->materia && is_object($tarea->materia) && isset($tarea->materia->nombre)) {
                $nombreMateria = $tarea->materia->nombre;
            } elseif (is_numeric($tarea->materia)) {
                // Si es solo el ID, buscar la materia
                $materia = \App\Models\Materia::find($tarea->materia);
                $nombreMateria = $materia ? $materia->nombre : 'Materia ID: ' . $tarea->materia;
            }
            
            $tareasData[] = [
                'materia' => $nombreMateria,
                'descripcion' => strip_tags($tarea->descripcion), // Remover HTML
                'fecha_entrega' => $tarea->fecha_entrega ? date('d/m/Y', strtotime($tarea->fecha_entrega)) : 'No especificada',
                'hora_entrega' => $tarea->hora_entrega ?? 'No especificada',
                'realizado' => '□', // Checkbox vacío para marcar manualmente
            ];
        }

        // Generar el PDF
        $pdf = PDF::loadView('pdf.tareas-grupo', [
            'grupo' => $grupo,
            'tareas' => $tareasData,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'usuario' => Auth::user() ?? (object)['name' => 'Usuario Público'],
            'inicioSemana' => now()->startOfWeek()->format('d/m/Y'),
            'finSemana' => now()->endOfWeek()->format('d/m/Y')
        ]);

        // Configurar el PDF
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Descargar el PDF
        return $pdf->download("tareas-semana-{$grupo->nombre}-{$grupo->seccion}-" . date('Y-m-d') . ".pdf");
    }

    /**
     * Vista previa del PDF (opcional, para testing)
     */
    public function previewTareasPdf($grupoId)
    {
        // No se requiere autenticación para ver PDFs

        // Obtener el grupo
        $grupo = grupo::findOrFail($grupoId);
        
        // Obtener las tareas del grupo desde viernes de la semana pasada hasta jueves de la semana en curso
        $tareas = $this->obtenerTareasViernesAJueves($grupoId);

        // Calcular las fechas del rango
        $viernesSemanaPasada = now()->subWeek()->next(5);
        $juevesSemanaActual = now()->next(4);

        // Preparar datos para la tabla
        $tareasData = [];
        foreach ($tareas as $tarea) {
            // Obtener nombre de materia de forma segura
            $nombreMateria = 'No especificada';
            if ($tarea->materia && is_object($tarea->materia) && isset($tarea->materia->nombre)) {
                $nombreMateria = $tarea->materia->nombre;
            } elseif (is_numeric($tarea->materia)) {
                // Si es solo el ID, buscar la materia
                $materia = \App\Models\Materia::find($tarea->materia);
                $nombreMateria = $materia ? $materia->nombre : 'Materia ID: ' . $tarea->materia;
            }
            
            $tareasData[] = [
                'materia' => $nombreMateria,
                'descripcion' => strip_tags($tarea->descripcion),
                'fecha_entrega' => $tarea->fecha_entrega ? date('d/m/Y', strtotime($tarea->fecha_entrega)) : 'No especificada',
                'hora_entrega' => $tarea->hora_entrega ?? 'No especificada',
                'realizado' => '□',
            ];
        }

        // Generar el PDF para vista previa
        $pdf = PDF::loadView('pdf.tareas-grupo', [
            'grupo' => $grupo,
            'tareas' => $tareasData,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'usuario' => Auth::user() ?? (object)['name' => 'Usuario Público'],
            'inicioSemana' => $viernesSemanaPasada->format('d/m/Y'),
            'finSemana' => $juevesSemanaActual->format('d/m/Y')
        ]);

        // Configurar el PDF
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Mostrar el PDF en el navegador
        return $pdf->stream("tareas-semana-{$grupo->nombre}-{$grupo->seccion}-" . date('Y-m-d') . ".pdf");
    }
}

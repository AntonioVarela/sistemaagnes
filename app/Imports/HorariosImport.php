<?php

namespace App\Imports;

use App\Models\horario;
use App\Models\grupo;
use App\Models\materia;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class HorariosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $errors = [];
    protected $successCount = 0;
    protected $rowNumber = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++;
        
        try {
            // Buscar grupo por nombre y sección
            $grupoNombre = trim($row['grupo'] ?? '');
            $grupoSeccion = trim($row['seccion'] ?? '');
            
            $grupo = grupo::where('nombre', $grupoNombre)
                ->where('seccion', $grupoSeccion)
                ->first();
            
            if (!$grupo) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Grupo '{$grupoNombre}' con sección '{$grupoSeccion}' no encontrado";
                return null;
            }

            // Buscar materia por nombre
            $materiaNombre = trim($row['materia'] ?? '');
            $materia = materia::where('nombre', $materiaNombre)->first();
            
            if (!$materia) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Materia '{$materiaNombre}' no encontrada";
                return null;
            }

            // Buscar maestro por nombre o email
            $maestroNombre = trim($row['maestro'] ?? '');
            $maestro = User::where(function($query) use ($maestroNombre) {
                $query->where('name', 'like', '%' . $maestroNombre . '%')
                      ->orWhere('email', 'like', '%' . $maestroNombre . '%');
            })->where('rol', 'Maestro')->first();
            
            if (!$maestro) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Maestro '{$maestroNombre}' no encontrado";
                return null;
            }

            // Procesar días (puede venir como "Lunes,Martes" o "Lunes, Martes")
            $dias = trim($row['dias'] ?? '');
            $diasArray = array_map('trim', explode(',', $dias));
            $diasString = implode(',', $diasArray);

            // Procesar horas (formato puede ser "08:00" o "8:00")
            $horaInicio = $this->normalizeTime($row['hora_inicio'] ?? '');
            $horaFin = $this->normalizeTime($row['hora_fin'] ?? '');

            if (!$horaInicio || !$horaFin) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Formato de hora inválido";
                return null;
            }

            $this->successCount++;

            return new horario([
                'grupo_id' => $grupo->id,
                'materia_id' => $materia->id,
                'maestro_id' => $maestro->id,
                'dias' => $diasString,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    /**
     * Normalizar formato de hora
     */
    private function normalizeTime($time)
    {
        if (empty($time)) {
            return null;
        }

        $time = trim($time);
        
        // Si ya está en formato HH:MM, retornarlo
        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            $parts = explode(':', $time);
            return str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        }

        // Intentar parsear otros formatos
        try {
            $date = \DateTime::createFromFormat('H:i:s', $time);
            if ($date) {
                return $date->format('H:i');
            }
            
            $date = \DateTime::createFromFormat('H:i', $time);
            if ($date) {
                return $date->format('H:i');
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'grupo' => 'required|string',
            'seccion' => 'required|string',
            'materia' => 'required|string',
            'maestro' => 'required|string',
            'dias' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ];
    }


    /**
     * Obtener errores
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Obtener contador de éxito
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }
}


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
    protected $totalRows = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++;
        $this->totalRows++;
        
        try {
            // Normalizar nombres de columnas (case-insensitive y sin espacios)
            $row = array_change_key_case($row, CASE_LOWER);
            $row = array_map('trim', $row);
            
            // Verificar que la fila no esté completamente vacía
            $rowValues = array_filter($row);
            if (empty($rowValues)) {
                // Fila vacía, saltarla sin error
                return null;
            }
            
            // Buscar grupo por nombre y sección
            $grupoNombre = trim($row['grupo'] ?? '');
            $grupoSeccion = trim($row['seccion'] ?? '');
            
            if (empty($grupoNombre) || empty($grupoSeccion)) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Grupo o Sección vacío";
                return null;
            }
            
            // Normalizar sección: convertir primera letra a mayúscula (Primaria/Secundaria)
            $grupoSeccionNormalizada = ucfirst(strtolower($grupoSeccion));
            
            // Buscar grupo con búsqueda case-insensitive para nombre y sección
            $grupo = grupo::whereRaw('LOWER(TRIM(nombre)) = ?', [strtolower(trim($grupoNombre))])
                ->where(function($query) use ($grupoSeccionNormalizada, $grupoSeccion) {
                    // Intentar con la sección normalizada (Primaria/Secundaria)
                    $query->whereRaw('LOWER(TRIM(seccion)) = ?', [strtolower($grupoSeccionNormalizada)])
                          // O con la sección original
                          ->orWhereRaw('LOWER(TRIM(seccion)) = ?', [strtolower(trim($grupoSeccion))]);
                })
                ->first();
            
            // Si aún no se encuentra, buscar solo por nombre (último intento)
            if (!$grupo) {
                $grupo = grupo::whereRaw('LOWER(TRIM(nombre)) = ?', [strtolower(trim($grupoNombre))])
                    ->first();
                
                // Si encontramos por nombre pero la sección no coincide, advertir
                if ($grupo && strtolower(trim($grupo->seccion)) !== strtolower($grupoSeccionNormalizada) && strtolower(trim($grupo->seccion)) !== strtolower(trim($grupoSeccion))) {
                    $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Grupo '{$grupoNombre}' encontrado pero con sección '{$grupo->seccion}' (esperada: '{$grupoSeccion}')";
                    return null;
                }
            }
            
            if (!$grupo) {
                // Mostrar información de debug: listar grupos disponibles similares
                $gruposSimilares = grupo::whereRaw('LOWER(TRIM(nombre)) LIKE ?', ['%' . strtolower(trim($grupoNombre)) . '%'])
                    ->get(['nombre', 'seccion'])
                    ->take(5);
                
                $gruposInfo = $gruposSimilares->map(function($g) {
                    return "{$g->nombre} ({$g->seccion})";
                })->implode(', ');
                
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Grupo '{$grupoNombre}' con sección '{$grupoSeccion}' no encontrado. Grupos disponibles similares: " . ($gruposInfo ?: 'Ninguno');
                return null;
            }

            // Buscar materia por nombre
            $materiaNombre = $row['materia'] ?? '';
            
            if (empty($materiaNombre)) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Materia vacía";
                return null;
            }
            
            $materia = materia::where('nombre', $materiaNombre)->first();
            
            if (!$materia) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Materia '{$materiaNombre}' no encontrada";
                return null;
            }

            // Buscar maestro por ID, nombre o email
            $maestroNombre = trim($row['maestro'] ?? '');
            
            if (empty($maestroNombre)) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Maestro vacío";
                return null;
            }
            
            $maestro = null;
            
            // Primero intentar buscar por ID si es numérico
            if (is_numeric($maestroNombre)) {
                $maestro = User::where('id', $maestroNombre)
                    ->where('rol', 'Maestro')
                    ->first();
            }
            
            // Si no se encontró por ID, buscar por nombre o email (case-insensitive y parcial)
            if (!$maestro) {
                $maestroNombreLower = strtolower($maestroNombre);
                
                $maestro = User::where('rol', 'Maestro')
                    ->where(function($query) use ($maestroNombre, $maestroNombreLower) {
                        // Búsqueda exacta case-insensitive
                        $query->whereRaw('LOWER(TRIM(name)) = ?', [$maestroNombreLower])
                              // Búsqueda parcial en nombre
                              ->orWhereRaw('LOWER(TRIM(name)) LIKE ?', ['%' . $maestroNombreLower . '%'])
                              // Búsqueda exacta en email
                              ->orWhereRaw('LOWER(TRIM(email)) = ?', [$maestroNombreLower])
                              // Búsqueda parcial en email
                              ->orWhereRaw('LOWER(TRIM(email)) LIKE ?', ['%' . $maestroNombreLower . '%']);
                    })
                    ->first();
            }
            
            if (!$maestro) {
                // Mostrar información de debug: listar maestros disponibles similares
                $maestrosSimilares = User::where('rol', 'Maestro')
                    ->where(function($query) use ($maestroNombre) {
                        $query->whereRaw('LOWER(TRIM(name)) LIKE ?', ['%' . strtolower(trim($maestroNombre)) . '%'])
                              ->orWhereRaw('LOWER(TRIM(email)) LIKE ?', ['%' . strtolower(trim($maestroNombre)) . '%']);
                    })
                    ->get(['id', 'name', 'email'])
                    ->take(5);
                
                $maestrosInfo = $maestrosSimilares->map(function($m) {
                    return "ID: {$m->id} - {$m->name} ({$m->email})";
                })->implode(', ');
                
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Maestro '{$maestroNombre}' no encontrado. Maestros disponibles similares: " . ($maestrosInfo ?: 'Ninguno');
                return null;
            }

            // Procesar días (puede venir como "Lunes,Martes" o "Lunes, Martes")
            $dias = $row['dias'] ?? '';
            
            if (empty($dias)) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Días vacío";
                return null;
            }
            
            $diasArray = array_map('trim', explode(',', $dias));
            $diasString = implode(',', $diasArray);

            // Procesar horas (formato puede ser "08:00" o "8:00")
            $horaInicio = $this->normalizeTime($row['hora_inicio'] ?? $row['hora inicio'] ?? '');
            $horaFin = $this->normalizeTime($row['hora_fin'] ?? $row['hora fin'] ?? '');

            if (!$horaInicio || !$horaFin) {
                $this->errors[] = "Fila " . ($this->rowNumber + 1) . ": Formato de hora inválido (Inicio: " . ($row['hora_inicio'] ?? $row['hora inicio'] ?? 'N/A') . ", Fin: " . ($row['hora_fin'] ?? $row['hora fin'] ?? 'N/A') . ")";
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


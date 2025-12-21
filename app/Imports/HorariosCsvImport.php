<?php

namespace App\Imports;

use App\Models\horario;
use App\Models\grupo;
use App\Models\materia;
use App\Models\User;

class HorariosCsvImport
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedRows = 0;
    protected $debugInfo = [];

    /**
     * Procesar archivo CSV
     */
    public function import($filePath)
    {
        // Leer el contenido del archivo para detectar BOM y delimitador
        $content = @file_get_contents($filePath);
        
        if ($content === false) {
            throw new \Exception('No se pudo leer el archivo CSV.');
        }
        
        // Remover BOM UTF-8 si existe
        $hasBOM = false;
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            $hasBOM = true;
        }
        
        // Detectar delimitador automáticamente
        $delimiter = ',';
        $firstLine = strtok($content, "\n");
        
        if ($firstLine === false) {
            throw new \Exception('El archivo CSV está vacío.');
        }
        
        // Contar comas y punto y comas en la primera línea
        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        
        // Si hay más punto y comas que comas, usar punto y coma
        if ($semicolonCount > $commaCount) {
            $delimiter = ';';
        }
        
        // Si se removió el BOM, crear un archivo temporal limpio
        $tempFilePath = $filePath;
        if ($hasBOM) {
            $tempFilePath = sys_get_temp_dir() . '/' . uniqid('csv_import_') . '.csv';
            file_put_contents($tempFilePath, $content);
        }
        
        $handle = fopen($tempFilePath, 'r');
        
        if (!$handle) {
            throw new \Exception('No se pudo abrir el archivo CSV.');
        }

        // Leer primera línea para verificar si tiene encabezados o es directamente datos
        $firstLine = fgetcsv($handle, 0, $delimiter);
        
        if (!$firstLine) {
            fclose($handle);
            throw new \Exception('El archivo CSV está vacío.');
        }

        // Limpiar BOM del primer campo si existe
        if (!empty($firstLine[0])) {
            $firstLine[0] = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine[0]);
            $firstLine[0] = trim($firstLine[0], "\xEF\xBB\xBF");
        }

        // Detectar si la primera línea es encabezado o datos
        // Si parece ser encabezado (texto sin números en las columnas de hora), saltarlo
        $isHeader = false;
        if (count($firstLine) >= 7) {
            // Verificar si la columna 5 (hora_inicio) y 6 (hora_fin) parecen ser encabezados
            $horaInicio = strtolower(trim($firstLine[5] ?? ''));
            $horaFin = strtolower(trim($firstLine[6] ?? ''));
            
            // Si contienen palabras como "hora", "inicio", "fin" pero no formato de hora, es encabezado
            if ((strpos($horaInicio, 'hora') !== false || strpos($horaInicio, 'inicio') !== false) && 
                !preg_match('/^\d{1,2}:\d{2}/', $horaInicio)) {
                $isHeader = true;
            }
        }

        // Guardar información para debug
        $this->debugInfo['delimiter'] = $delimiter;
        $this->debugInfo['first_line_is_header'] = $isHeader;
        
        // Mapa de columnas por posición fija (índice 0-based)
        // Orden: Grupo, Seccion, Materia, Maestro, Dias, Hora Inicio, Hora Fin
        $columnMap = [
            'grupo' => 0,
            'seccion' => 1,
            'materia' => 2,
            'maestro' => 3,
            'dias' => 4,
            'hora_inicio' => 5,
            'hora_fin' => 6
        ];
        
        $this->debugInfo['column_map'] = $columnMap;
        
        $rowNumber = 0;
        $rows = [];
        
        // Si la primera línea es encabezado, guardarla para debug y continuar leyendo
        if ($isHeader) {
            $this->debugInfo['headers'] = $firstLine;
            // Continuar leyendo desde la siguiente línea
        } else {
            // La primera línea es datos, agregarla al array
            $rows[] = $firstLine;
        }
        
        // Leer el resto de las líneas usando fgetcsv para parsear correctamente
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);
        
        // Si no hay líneas de datos, lanzar error
        if (empty($rows)) {
            throw new \Exception('El archivo CSV no contiene datos para importar.');
        }
        
        // Procesar cada fila
        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            
            // Verificar que la fila tenga al menos 7 columnas
            if (count($row) < 7) {
                $this->errors[] = "Fila " . ($rowNumber) . ": La fila debe tener 7 columnas (Grupo, Seccion, Materia, Maestro, Dias, Hora Inicio, Hora Fin). Columnas encontradas: " . count($row);
                continue;
            }
            
            // Verificar que la fila no esté completamente vacía
            $rowValues = array_filter(array_map('trim', $row));
            if (empty($rowValues)) {
                $this->skippedRows++;
                continue;
            }
            
            // Guardar primera fila para debug
            if ($rowNumber == 1) {
                $this->debugInfo['first_row_data'] = [
                    'grupo' => $row[0] ?? '',
                    'seccion' => $row[1] ?? '',
                    'materia' => $row[2] ?? '',
                    'maestro' => $row[3] ?? '',
                    'dias' => $row[4] ?? '',
                    'hora_inicio' => $row[5] ?? '',
                    'hora_fin' => $row[6] ?? ''
                ];
                $this->debugInfo['first_row_raw'] = $row;
                $this->debugInfo['first_row_count'] = count($row);
            }
            
            try {
                // Extraer valores por posición fija (columna 0-6)
                $grupoNombre = trim($row[0] ?? '');
                $grupoSeccion = trim($row[1] ?? '');
                $materiaNombre = trim($row[2] ?? '');
                $maestroNombre = trim($row[3] ?? '');
                $dias = trim($row[4] ?? '');
                $horaInicio = trim($row[5] ?? '');
                $horaFin = trim($row[6] ?? '');
                
                // Validar campos requeridos
                if (empty($grupoNombre) || empty($grupoSeccion)) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Grupo o Sección vacío";
                    continue;
                }
                
                if (empty($materiaNombre)) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Materia vacía";
                    continue;
                }
                
                if (empty($maestroNombre)) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Maestro vacío";
                    continue;
                }
                
                if (empty($dias)) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Días vacío";
                    continue;
                }
                
                if (empty($horaInicio) || empty($horaFin)) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Horas vacías";
                    continue;
                }
                
                // Buscar grupo
                $grupoSeccionNormalizada = ucfirst(strtolower($grupoSeccion));
                $grupo = grupo::whereRaw('LOWER(TRIM(nombre)) = ?', [strtolower(trim($grupoNombre))])
                    ->where(function($query) use ($grupoSeccionNormalizada, $grupoSeccion) {
                        $query->whereRaw('LOWER(TRIM(seccion)) = ?', [strtolower($grupoSeccionNormalizada)])
                              ->orWhereRaw('LOWER(TRIM(seccion)) = ?', [strtolower(trim($grupoSeccion))]);
                    })
                    ->first();
                
                if (!$grupo) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Grupo '{$grupoNombre}' con sección '{$grupoSeccion}' no encontrado";
                    continue;
                }
                
                // Buscar materia
                $materia = materia::where('nombre', $materiaNombre)->first();
                
                if (!$materia) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Materia '{$materiaNombre}' no encontrada";
                    continue;
                }
                
                // Buscar maestro
                $maestro = null;
                
                if (is_numeric($maestroNombre)) {
                    $maestro = User::where('id', $maestroNombre)
                        ->where('rol', 'Maestro')
                        ->first();
                }
                
                if (!$maestro) {
                    $maestroNombreLower = strtolower($maestroNombre);
                    $maestro = User::where('rol', 'Maestro')
                        ->where(function($query) use ($maestroNombre, $maestroNombreLower) {
                            $query->whereRaw('LOWER(TRIM(name)) = ?', [$maestroNombreLower])
                                  ->orWhereRaw('LOWER(TRIM(name)) LIKE ?', ['%' . $maestroNombreLower . '%'])
                                  ->orWhereRaw('LOWER(TRIM(email)) = ?', [$maestroNombreLower])
                                  ->orWhereRaw('LOWER(TRIM(email)) LIKE ?', ['%' . $maestroNombreLower . '%']);
                        })
                        ->first();
                }
                
                if (!$maestro) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Maestro '{$maestroNombre}' no encontrado";
                    continue;
                }
                
                // Procesar días
                $diasArray = array_map('trim', explode(',', $dias));
                $diasString = implode(',', $diasArray);
                
                // Normalizar horas
                $horaInicioNormalizada = $this->normalizeTime($horaInicio);
                $horaFinNormalizada = $this->normalizeTime($horaFin);
                
                if (!$horaInicioNormalizada || !$horaFinNormalizada) {
                    $this->errors[] = "Fila " . ($rowNumber + 1) . ": Formato de hora inválido (Inicio: {$horaInicio}, Fin: {$horaFin})";
                    continue;
                }
                
                // Crear horario
                horario::create([
                    'grupo_id' => $grupo->id,
                    'materia_id' => $materia->id,
                    'maestro_id' => $maestro->id,
                    'dias' => $diasString,
                    'hora_inicio' => $horaInicioNormalizada,
                    'hora_fin' => $horaFinNormalizada,
                ]);
                
                $this->successCount++;
                
            } catch (\Exception $e) {
                $this->errors[] = "Fila " . ($rowNumber + 1) . ": " . $e->getMessage();
            }
        }
        
        // Limpiar archivo temporal si se creó
        if ($hasBOM && $tempFilePath !== $filePath && file_exists($tempFilePath)) {
            @unlink($tempFilePath);
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

    /**
     * Obtener información de debug
     */
    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     * Obtener filas saltadas
     */
    public function getSkippedRows()
    {
        return $this->skippedRows;
    }
}


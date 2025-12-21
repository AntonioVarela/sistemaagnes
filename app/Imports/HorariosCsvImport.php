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
        $content = file_get_contents($filePath);
        
        // Remover BOM UTF-8 si existe
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            // Reescribir el archivo sin BOM temporalmente
            file_put_contents($filePath, $content);
        }
        
        // Detectar delimitador automáticamente
        $delimiter = ',';
        $firstLine = strtok($content, "\n");
        
        // Contar comas y punto y comas en la primera línea
        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        
        // Si hay más punto y comas que comas, usar punto y coma
        if ($semicolonCount > $commaCount) {
            $delimiter = ';';
        }
        
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception('No se pudo abrir el archivo CSV.');
        }

        // Leer encabezados con el delimitador detectado
        $headers = fgetcsv($handle, 0, $delimiter);
        
        if (!$headers) {
            fclose($handle);
            throw new \Exception('El archivo CSV está vacío o no tiene encabezados.');
        }

        // Limpiar BOM del primer encabezado si aún existe
        if (!empty($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            $headers[0] = trim($headers[0], "\xEF\xBB\xBF");
        }

        // Guardar encabezados para debug
        $this->debugInfo['headers'] = $headers;
        $this->debugInfo['delimiter'] = $delimiter;
        
        // Normalizar encabezados (case-insensitive y limpiar BOM)
        $normalizedHeaders = array_map(function($h) {
            // Remover BOM y espacios
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
            return strtolower(trim($h));
        }, $headers);
        
        $this->debugInfo['normalized_headers'] = $normalizedHeaders;
        
        // Crear mapa de índices de columnas requeridas
        $requiredColumns = ['grupo', 'seccion', 'materia', 'maestro', 'dias', 'hora_inicio', 'hora_fin'];
        $columnMap = [];
        $missingColumns = [];
        
        foreach ($requiredColumns as $col) {
            $found = false;
            $variations = [
                $col,
                str_replace('_', ' ', $col),
                str_replace('_', '', $col),
                ucfirst($col),
                ucwords(str_replace('_', ' ', $col))
            ];
            
            foreach ($variations as $variation) {
                $index = array_search(strtolower(trim($variation)), $normalizedHeaders);
                if ($index !== false) {
                    $columnMap[$col] = $index;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $missingColumns[] = $col;
            }
        }
        
        $this->debugInfo['column_map'] = $columnMap;
        $this->debugInfo['missing_columns'] = $missingColumns;
        
        // Si faltan columnas críticas, lanzar error
        if (!empty($missingColumns)) {
            fclose($handle);
            throw new \Exception('Columnas faltantes: ' . implode(', ', $missingColumns) . '. Columnas disponibles: ' . implode(', ', $headers));
        }
        
        $rowNumber = 0;
        
        // Procesar cada fila de datos CON EL DELIMITADOR DETECTADO
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;
            
            // Verificar que la fila no esté completamente vacía
            $rowValues = array_filter(array_map('trim', $row));
            if (empty($rowValues)) {
                $this->skippedRows++;
                continue;
            }
            
            // Guardar primera fila para debug
            if ($rowNumber == 1) {
                $this->debugInfo['first_row_data'] = array_combine($headers, $row);
            }
            
            try {
                // Extraer valores usando el mapa de columnas
                $grupoNombre = trim($row[$columnMap['grupo']] ?? '');
                $grupoSeccion = trim($row[$columnMap['seccion']] ?? '');
                $materiaNombre = trim($row[$columnMap['materia']] ?? '');
                $maestroNombre = trim($row[$columnMap['maestro']] ?? '');
                $dias = trim($row[$columnMap['dias']] ?? '');
                $horaInicio = trim($row[$columnMap['hora_inicio']] ?? '');
                $horaFin = trim($row[$columnMap['hora_fin']] ?? '');
                
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
        
        fclose($handle);
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


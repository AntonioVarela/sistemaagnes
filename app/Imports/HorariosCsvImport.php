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
        
        // Leer todas las líneas primero para manejar mejor el formato
        $lines = [];
        while (($line = fgets($handle)) !== false) {
            $lines[] = trim($line);
        }
        fclose($handle);
        
        // Si no hay líneas de datos, lanzar error
        if (empty($lines)) {
            throw new \Exception('El archivo CSV no contiene datos para importar.');
        }
        
        // Procesar cada línea
        foreach ($lines as $lineIndex => $line) {
            $rowNumber = $lineIndex + 1;
            
            // Saltar línea vacía
            if (empty(trim($line))) {
                $this->skippedRows++;
                continue;
            }
            
            // Remover comillas que envuelven toda la línea si existen
            $line = trim($line);
            $originalLine = $line;
            
            // Detectar si toda la línea está entre comillas (formato incorrecto de Excel)
            if (substr($line, 0, 1) === '"' && substr($line, -1) === '"') {
                // Contar comillas dobles escapadas ("" dentro de campos)
                $escapedQuotes = substr_count($line, '""');
                $totalQuotes = substr_count($line, '"');
                
                // Si hay muchas comillas y la línea empieza y termina con comillas,
                // probablemente toda la línea está mal formateada
                if ($totalQuotes > 6) {
                    // Remover comillas iniciales y finales
                    $line = substr($line, 1, -1);
                    // Reemplazar comillas dobles escapadas por comillas simples temporales
                    $line = str_replace('""', '___TEMP_QUOTE___', $line);
                    // Ahora parsear normalmente
                    $row = str_getcsv($line, $delimiter, '"');
                    // Restaurar comillas dobles escapadas
                    $row = array_map(function($field) {
                        return str_replace('___TEMP_QUOTE___', '"', $field);
                    }, $row);
                } else {
                    // Parsear normalmente
                    $row = str_getcsv($line, $delimiter, '"');
                }
            } else {
                // Parsear normalmente
                $row = str_getcsv($line, $delimiter, '"');
            }
            
            // Si después de parsear solo tenemos un elemento pero debería haber más, intentar dividir manualmente
            if (count($row) == 1 && count($headers) > 1) {
                // La línea está mal formateada, intentar dividir por el delimitador directamente
                $row = explode($delimiter, $line);
                // Limpiar comillas de cada campo
                $row = array_map(function($field) {
                    $field = trim($field);
                    $field = trim($field, '"');
                    return str_replace('""', '"', $field);
                }, $row);
            }
            
            // Verificar que la fila no esté completamente vacía
            $rowValues = array_filter(array_map('trim', $row));
            if (empty($rowValues)) {
                $this->skippedRows++;
                continue;
            }
            
            // Guardar primera fila para debug
            if ($rowNumber == 1) {
                // Asegurar que row tenga el mismo número de elementos que headers
                $paddedRow = array_pad($row, count($headers), '');
                // Si row tiene más elementos, truncar
                if (count($paddedRow) > count($headers)) {
                    $paddedRow = array_slice($paddedRow, 0, count($headers));
                }
                // Combinar solo si tienen el mismo número de elementos
                if (count($headers) === count($paddedRow)) {
                    $this->debugInfo['first_row_data'] = array_combine($headers, $paddedRow);
                } else {
                    // Si aún no coinciden, crear un array asociativo manualmente
                    $this->debugInfo['first_row_data'] = [];
                    foreach ($headers as $index => $header) {
                        $this->debugInfo['first_row_data'][$header] = $paddedRow[$index] ?? '';
                    }
                }
                $this->debugInfo['first_row_raw'] = $row;
                $this->debugInfo['first_row_count'] = count($row);
                $this->debugInfo['headers_count'] = count($headers);
                $this->debugInfo['first_row_line'] = $line;
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


<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * Subir archivo de manera segura
     */
    public static function uploadFile(UploadedFile $file, string $path, string $disk = 's3'): string
    {
        // Sanitizar el nombre del archivo
        $filename = self::sanitizeFilename($file->getClientOriginalName());
        
        // Generar nombre único
        $uniqueName = time() . '_' . Str::random(10) . '_' . $filename;
        
        // Subir archivo
        $filePath = $file->storeAs($path, $uniqueName, $disk);
        
        return $filePath;
    }

    /**
     * Eliminar archivo de manera segura
     */
    public static function deleteFile(string $filePath, string $disk = 's3'): bool
    {
        try {
            if (Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar archivo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sanitizar nombre de archivo
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remover caracteres especiales y espacios
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Limitar longitud
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 255 - strlen($extension) - 1) . '.' . $extension;
        }
        
        return $filename;
    }

    /**
     * Obtener URL del archivo
     */
    public static function getFileUrl(string $filePath, string $disk = 's3'): string
    {
        try {
            return Storage::disk($disk)->url($filePath);
        } catch (\Exception $e) {
            \Log::error('Error al obtener URL del archivo: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Verificar si el archivo es válido
     */
    public static function isValidFile(UploadedFile $file): bool
    {
        $allowedMimes = config('upload.allowed_mimes');
        $maxSize = config('upload.max_file_size') * 1024; // Convertir KB a bytes
        
        return $file->isValid() && 
               in_array($file->getMimeType(), $allowedMimes) && 
               $file->getSize() <= $maxSize;
    }

    /**
     * Obtener información del archivo
     */
    public static function getFileInfo(string $filePath, string $disk = 's3'): array
    {
        try {
            $exists = Storage::disk($disk)->exists($filePath);
            $size = $exists ? Storage::disk($disk)->size($filePath) : 0;
            $url = $exists ? Storage::disk($disk)->url($filePath) : '';
            
            return [
                'exists' => $exists,
                'size' => $size,
                'url' => $url,
                'filename' => basename($filePath),
            ];
        } catch (\Exception $e) {
            \Log::error('Error al obtener información del archivo: ' . $e->getMessage());
            return [
                'exists' => false,
                'size' => 0,
                'url' => '',
                'filename' => basename($filePath),
            ];
        }
    }
}

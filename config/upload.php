<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la subida de archivos en el sistema
    |
    */

    'max_file_size' => env('MAX_FILE_SIZE', 10240), // 10MB en KB

    'allowed_mimes' => [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
    ],

    'storage_disk' => env('STORAGE_DISK', 's3'),

    'file_paths' => [
        'anuncios' => 'archivos/anuncios',
        'tareas' => 'archivos/tareas',
    ],

    'security' => [
        'sanitize_filenames' => true,
        'max_filename_length' => 255,
        'prevent_duplicate_names' => true,
    ],
];

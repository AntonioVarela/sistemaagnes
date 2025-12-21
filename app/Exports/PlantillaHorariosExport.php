<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantillaHorariosExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['1 A', 'Primaria', 'Matemáticas', 'Juan Pérez', 'Lunes,Martes,Miércoles', '08:00', '09:00'],
            ['1 B', 'Primaria', 'Español', 'María García', 'Jueves,Viernes', '10:00', '11:00'],
            ['2 A', 'Secundaria', 'Inglés', 'Carlos López', 'Lunes,Martes', '14:00', '15:00'],
        ];
    }

    public function headings(): array
    {
        return [
            'Grupo',
            'Seccion',
            'Materia',
            'Maestro',
            'Dias',
            'Hora Inicio',
            'Hora Fin'
        ];
    }
}


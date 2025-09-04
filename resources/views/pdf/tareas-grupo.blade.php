<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas del Grupo {{ $grupo->nombre }} {{ $grupo->seccion }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4F46E5;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #6B7280;
            font-size: 16px;
            margin: 0;
        }
        
        .info-section {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-item {
            flex: 1;
            margin: 0 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th {
            background-color: #4F46E5;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #4F46E5;
        }
        
        td {
            padding: 10px 8px;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        tr:hover {
            background-color: #F3F4F6;
        }
        
        .checkbox-column {
            width: 60px;
            text-align: center;
            font-size: 18px;
        }
        
        .materia-column {
            width: 120px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .descripcion-column {
            width: 300px;
        }
        
        .fecha-column {
            width: 100px;
            text-align: center;
        }
        
        .hora-column {
            width: 80px;
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-tareas {
            text-align: center;
            padding: 40px;
            color: #6B7280;
            font-style: italic;
        }
        
        .instrucciones {
            background-color: #FEF3C7;
            border: 1px solid #F59E0B;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .instrucciones h3 {
            margin: 0 0 10px 0;
            color: #92400E;
            font-size: 14px;
        }
        
        .instrucciones ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .instrucciones li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>Tareas de la Semana</h1>
        <p class="subtitle">Grupo: {{ $grupo->nombre }} {{ $grupo->seccion }}</p>
        <p class="subtitle">Período: {{ $inicioSemana ?? 'Inicio de semana' }} - {{ $finSemana ?? 'Fin de semana' }}</p>
    </div>
    
    <!-- Información del Grupo y Fecha -->
    <div class="info-section">
        <div class="info-item">
            <div class="info-label">Total de Tareas:</div>
            <div class="info-value">{{ count($tareas) }}</div>
        </div>
    </div>
    
    <!-- Tabla de Tareas -->
    <div class="table-container">
        @if(count($tareas) > 0)
            <table style="width: 100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="" style="width: 20%;"> Materia</th>
                        <th class="descripcion-column" style="width: 40%;"> Descripción</th>
                        <th class="fecha-column" style="width: 20%;"> Fecha de Entrega</th>
                        <th class="" style="width: 10%;"> Realizado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tareas as $index => $tarea)
                        <tr>
                            <td class="materia-column">{{ $tarea['materia'] }}</td>
                            <td class="descripcion-column">{{ $tarea['descripcion'] }}</td>
                            <td class="fecha-column">{{ $tarea['fecha_entrega'] }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-tareas">
                <h3>No hay tareas disponibles para este grupo</h3>
                <p>El grupo {{ $grupo->nombre }} {{ $grupo->seccion }} no tiene tareas asignadas actualmente.</p>
            </div>
        @endif
    </div>
    
    <!-- Pie de Página -->
    <div class="footer">
        <p>Documento generado automáticamente el {{ $fechaGeneracion }}</p>
    </div>
</body>
</html>

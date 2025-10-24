<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Tareas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/locales/es.global.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .fc-event {
            border-radius: 8px !important;
            padding: 4px 8px !important;
            margin: 2px 0 !important;
            border: none !important;
            transition: all 0.2s ease-in-out !important;
        }
        .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: #1F2937 !important;
        }
        .fc-button-primary {
            background-color: #4F46E5 !important;
            border-color: #4F46E5 !important;
            border-radius: 6px !important;
            padding: 8px 16px !important;
            font-weight: 500 !important;
        }
        .fc-button-primary:hover {
            background-color: #4338CA !important;
            border-color: #4338CA !important;
        }

        /* Estilos responsivos para el calendario */
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column !important;
                gap: 1rem !important;
            }
            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }
            .fc-button {
                padding: 6px 12px !important;
                font-size: 0.875rem !important;
            }
            .fc-header-toolbar {
                margin-bottom: 1rem !important;
            }
            .fc-view-harness {
                min-height: 400px !important;
            }
            .fc-event {
                font-size: 0.875rem !important;
                padding: 2px 4px !important;
            }
        }

        @media (max-width: 480px) {
            .fc-toolbar-title {
                font-size: 1rem !important;
            }
            .fc-button {
                padding: 4px 8px !important;
                font-size: 0.75rem !important;
            }
            .fc-view-harness {
                min-height: 300px !important;
            }
        }

        /* Estilos responsivos para el header */
        @media (max-width: 768px) {
            .header-mobile {
                padding: 1rem 0;
            }
        }

        
        /* Estilos adicionales para mejor responsividad */
        @media (max-width: 640px) {
            .header-mobile h1 {
                font-size: 1rem !important;
                line-height: 1.3;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="min-h-screen">
        <!-- Header Responsivo -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <!-- Layout para desktop -->
                <div class="hidden md:flex justify-between items-center">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Panel de Actividades de {{ $grupo->nombre }} {{ $grupo->seccion }}</h1>
                    <div class="flex items-center space-x-4">
                        <!-- Botón de Preview -->
                        <a href="{{ route('tareas.pdf.preview', $grupo->id) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            PDF de tareas 
                        </a>
                        
                        <a href="{{ route('home') }}" class="hover:opacity-80 transition-opacity duration-200">
                            <img src="/logo.png" alt="Logo" class="h-10 lg:h-12 w-auto opacity-60" />
                        </a>
                    </div>
                </div>

                <!-- Layout para móvil -->
                <div class="md:hidden">
                    <!-- Fila superior: Logo y botón PDF -->
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('home') }}" class="hover:opacity-80 transition-opacity duration-200">
                            <img src="/logo.png" alt="Logo" class="h-8 w-auto opacity-60" />
                        </a>
                        
                        <a href="{{ route('tareas.pdf.preview', $grupo->id) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            PDF
                        </a>
                    </div>
                    
                    <!-- Fila inferior: Título -->
                    <div class="text-center">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
                            Panel de Actividades de {{ $grupo->nombre }} {{ $grupo->seccion }}
                        </h1>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sección de anuncios y circulares -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Anuncios y Circulares -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            Anuncios y Circulares
                        </h2>
                        <div class="space-y-4">
                            @if(count($anuncios) > 0)
                                @foreach($anuncios as $anuncio)
                                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 hover:border-indigo-200 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                
                                                <h3 class="text-lg font-semibold text-indigo-900">{{ $anuncio->titulo }}</h3>
                                            </div>
                                            <p class="text-sm text-indigo-600 mt-1">
                                                <span class="font-medium">Materia:</span> 
                                                {{ $anuncio->materia->nombre ?? 'No especificada' }}
                                            </p>
                                        </div>
                                        <span class="text-xs text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full ml-2">
                                            {{ $anuncio->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-indigo-700 mt-2">{{ $anuncio->contenido }}</p>
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center text-xs text-indigo-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $anuncio->created_at->format('h:i A') }}
                                        </div>
                                        @if($anuncio->archivo)
                                            <div class="mt-2">
                                                <a href="{{ $anuncio->url_archivo }}" 
                                                   class="text-indigo-600 hover:text-indigo-800 font-medium" 
                                                   target="_blank">
                                                    Ver archivo adjunto
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            <!-- Separador visual entre anuncios y circulares -->
                            @if(count($anuncios) > 0 && count($circulares) > 0)
                                <div class="border-t border-gray-200 my-6"></div>
                            @endif

                            <!-- Circulares Semanales -->
                            @if(count($circulares) > 0)
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Circulares Semanales
                                    </h3>
                                </div>
                                @foreach($circulares as $circular)
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:border-blue-200 transition-colors {{ $circular->es_global ? 'ring-2 ring-purple-200' : '' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                               
                                                @if($circular->es_global)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                         Para todos los grupos
                                                    </span>
                                                @elseif($circular->grupo)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                         {{ $circular->grupo->nombre }} {{ $circular->grupo->seccion }}
                                                    </span>
                                                @elseif($circular->seccion)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        Sección {{ $circular->seccion }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-lg font-semibold text-blue-900 mt-2">{{ $circular->titulo }}</h4>
                                            @if($circular->descripcion)
                                                <p class="text-sm text-blue-700 mt-2">{{ Str::limit($circular->descripcion, 120) }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end ml-2">
                                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full mb-1">
                                                {{ $circular->created_at->format('d M Y') }}
                                            </span>
                                            @if($circular->fecha_expiracion)
                                                <span class="text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded-full">
                                                    Expira: {{ $circular->fecha_expiracion->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center text-xs text-blue-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $circular->user->name ?? 'Usuario no disponible' }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($circular->tamanio_formateado)
                                                <span class="text-xs text-gray-500">
                                                    {{ $circular->tamanio_formateado }}
                                                </span>
                                            @endif
                                            <a href="{{ $circular->url_archivo }}" 
                                               class="text-blue-600 hover:text-blue-800 font-medium flex items-center"
                                               target="_blank">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Ver archivo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            <!-- Mensaje cuando no hay contenido -->
                            @if(count($anuncios) == 0 && count($circulares) == 0)
                                <div class="bg-gray-50 rounded-lg p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No hay contenido disponible</h3>
                                    <p class="text-sm text-gray-500">Los anuncios y circulares aparecerán aquí cuando estén disponibles.</p>
                                </div>
                            @endif
                        </div>
                    </div>



                </div>

                <!-- Sección del calendario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Calendario de Tareas
                            </h2>
                            <div id="restrictionMessage" class="mt-2 sm:mt-0 text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 hidden">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Las tareas de la siguiente semana se mostrarán los viernes a las 2:00 PM
                                </div>
                            </div>
                        </div>
                        <div id='calendar' class="calendar-container"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Mejorado -->
    <div id="eventModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg mx-4 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Detalles de la Tarea</h2>
                <button id="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <div class="text-gray-600">
                    <h3 class="text-lg font-semibold text-indigo-600 mb-2" id="modalTitle"></h3>
                    <hr>
                    <small>¿Qué tengo que hacer?</small>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div id="modalDescription" class="text-gray-700 ql-editor" style="padding: 0;"></div>
                    </div>
                </div>

                <div class="text-gray-600">
                    @if($grupo->seccion == 'Primaria')
                    <small>Tarea del dia</small>
                    @else
                    <small>Información de la tarea</small>
                    @endif
                    <br>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div id="modalDate" class="font-medium"></div>
                    </div>
                    
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Recursos Adjuntos</h4>
                    <p id="modalResources" class="text-sm text-gray-500">No hay recursos disponibles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para vista en pantalla completa de imágenes -->
    <div id="imageModal" class="fixed inset-0 hidden bg-black bg-opacity-90 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <button id="closeImageModal" class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="fullscreenImage" src="" alt="Imagen en pantalla completa" class="max-w-full max-h-full object-contain">
        </div>
    </div>

    <script>
        // Función auxiliar para formatear fechas en español de manera legible
        function formatearFecha(fechaString, horaString = null) {
            if (!fechaString) return 'Fecha no disponible';
            
            try {
                // Limpiar la fecha
                let fechaLimpia = fechaString.toString().trim();
                
                // Crear objeto Date con la fecha original
                let fecha = new Date(fechaLimpia);
                
                if (isNaN(fecha.getTime())) {
                    // Si no se puede parsear, devolver la fecha original
                    return fechaString + (horaString ? ' a las ' + horaString : '');
                }
                
                // Formatear la fecha en español usando UTC para evitar problemas de zona horaria
                let fechaFormateada = fecha.toLocaleDateString('es-MX', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    timeZone: "UTC"
                });
                
                // Capitalizar la primera letra
                fechaFormateada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                
                // Solo agregar la hora si existe y no es null o vacía
                if (horaString && horaString !== 'null' && horaString !== '' && horaString !== 'undefined') {
                    // Limpiar la hora para que solo muestre la parte de tiempo, no la fecha completa
                    let horaLimpia = horaString.toString().trim();
                    // Si la hora contiene una fecha ISO completa, extraer solo la parte de tiempo
                    if (horaLimpia.includes('T')) {
                        let fechaHora = new Date(horaLimpia);
                        if (!isNaN(fechaHora.getTime())) {
                            horaLimpia = fechaHora.toLocaleTimeString('es-MX', {
                                hour: '2-digit',
                                minute: '2-digit',
                                timeZone: "UTC"
                            });
                        }
                    }
                    fechaFormateada += ' a las ' + horaLimpia;
                }
                
                return fechaFormateada;
            } catch (error) {
                console.error('Error al formatear fecha:', error);
                return fechaString + (horaString ? ' a las ' + horaString : '');
            }
        }

        // Función para verificar si se pueden mostrar tareas de la siguiente semana
        function puedeVerTareasSiguienteSemana() {
            const ahora = new Date();
            const diaSemana = ahora.getDay(); // 0 = Domingo, 1 = Lunes, ..., 5 = Viernes
            const hora = ahora.getHours();
            
            // Solo permitir los viernes a las 2 PM o después
            return diaSemana === 5 && hora >= 14;
        }
        
        // Función para filtrar tareas según la restricción
        function filtrarTareasPorRestriccion(tareas) {
            const ahora = new Date();
            const inicioSemanaActual = new Date(ahora);
            inicioSemanaActual.setDate(ahora.getDate() - ahora.getDay() + 1); // Lunes de esta semana
            const finSemanaActual = new Date(inicioSemanaActual);
            finSemanaActual.setDate(inicioSemanaActual.getDate() + 6); // Domingo de esta semana
            
            const inicioSemanaSiguiente = new Date(finSemanaActual);
            inicioSemanaSiguiente.setDate(finSemanaActual.getDate() + 1); // Lunes de la siguiente semana
            const finSemanaSiguiente = new Date(inicioSemanaSiguiente);
            finSemanaSiguiente.setDate(inicioSemanaSiguiente.getDate() + 6); // Domingo de la siguiente semana
            
            return tareas.filter(function(tarea) {
                if (!tarea.fecha_entrega) return true;
                
                const fechaEntrega = new Date(tarea.fecha_entrega);
                
                // Si la tarea es de esta semana, siempre se muestra
                if (fechaEntrega >= inicioSemanaActual && fechaEntrega <= finSemanaActual) {
                    return true;
                }
                
                // Si la tarea es de la siguiente semana, solo se muestra si es viernes 2 PM o después
                if (fechaEntrega >= inicioSemanaSiguiente && fechaEntrega <= finSemanaSiguiente) {
                    return puedeVerTareasSiguienteSemana();
                }
                
                // Para tareas de otras semanas, siempre se muestran
                return true;
            });
        }

        $(document).ready(function () {
            var tareas = @json($tareas);
            var grupo = @json($grupo); 
            var s3BaseUrl = '{{ config("filesystems.disks.s3.url") }}';
            var eventos;
            
            // Filtrar tareas según la restricción
            var tareasFiltradas = filtrarTareasPorRestriccion(tareas);
            
            // Mostrar mensaje informativo si no se pueden ver tareas de la siguiente semana
            if (!puedeVerTareasSiguienteSemana()) {
                document.getElementById('restrictionMessage').classList.remove('hidden');
            }
            
            if(grupo.seccion == 'Primaria') {
                eventos = tareasFiltradas.map(function(element) {
                    // Restar un día a la fecha para primaria
                    let startDate = element.fecha_entrega;
                    if (element.fecha_entrega) {
                        try {
                            let date = new Date(element.fecha_entrega);
                            if (!isNaN(date.getTime())) {
                                date.setDate(date.getDate());
                                startDate = date.toISOString().split('T')[0];
                            }
                        } catch (error) {
                            console.error('Error al procesar fecha para primaria:', error);
                        }
                    }

                    return {
                        title: element.titulo || 'Sin título',
                        start: startDate,
                        color: '#4F46E5',
                        allDay: false,
                        extendedProps: {
                            description: element.descripcion || 'Sin descripción',
                            fecha_entrega: element.fecha_entrega || '',
                            hora_entrega: element.hora_entrega || '',
                            archivo: element.archivo || ''
                        }
                    };
                });
            } else {
                // Para secundaria, mostrar tareas en el día que fueron asignadas (created_at)
                eventos = tareasFiltradas.map(function(element) {
                    // Usar la fecha de creación (asignación) en lugar de la fecha de entrega
                    let startDate = element.created_at;
                    if (element.created_at) {
                        try {
                            let date = new Date(element.created_at);
                            if (!isNaN(date.getTime())) {
                                startDate = date.toISOString().split('T')[0];
                            }
                        } catch (error) {
                            console.error('Error al procesar fecha de asignación para secundaria:', error);
                            // Fallback a fecha de entrega si hay error
                            startDate = element.fecha_entrega || new Date().toISOString().split('T')[0];
                        }
                    } else {
                        // Fallback a fecha de entrega si no hay created_at
                        startDate = element.fecha_entrega || new Date().toISOString().split('T')[0];
                    }

                    return {
                        title: element.titulo || 'Sin título',
                        start: startDate,
                        color: '#4F46E5',
                        allDay: false,
                        extendedProps: {
                            description: element.descripcion || 'Sin descripción',
                            fecha_entrega: element.fecha_entrega || '',
                            hora_entrega: element.hora_entrega || '',
                            archivo: element.archivo || '',
                            fecha_asignacion: element.created_at || ''
                        }
                    };
                });
            }

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'listWeek',
                locale: 'es',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next today'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    list: 'Lista'
                },
                events: eventos,
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.35,
                handleWindowResize: true,
                windowResizeDelay: 200,
                eventClick: function(info) {
                    $('#modalTitle').text(info.event.title);
                    $('#modalDescription').html(info.event.extendedProps.description);
                    
                    // Para secundaria, mostrar información diferente
                    if (grupo.seccion !== 'Primaria') {
                        // Mostrar fecha de asignación y fecha de entrega por separado
                        let fechaAsignacion = '';
                        let fechaEntrega = '';
                        
                        if (info.event.extendedProps.fecha_asignacion) {
                            fechaAsignacion = formatearFecha(info.event.extendedProps.fecha_asignacion);
                        }
                        
                        if (info.event.extendedProps.fecha_entrega) {
                            fechaEntrega = formatearFecha(
                                info.event.extendedProps.fecha_entrega,
                                info.event.extendedProps.hora_entrega
                            );
                        }
                        
                        let fechaInfo = '';
                        if (fechaAsignacion && fechaEntrega) {
                            fechaInfo = `<div class="space-y-2">
                                <div><strong>Asignada el:</strong> ${fechaAsignacion}</div>
                                <div><strong>Entregar el:</strong> ${fechaEntrega}</div>
                            </div>`;
                        } else if (fechaEntrega) {
                            fechaInfo = `<div><strong>Entregar el:</strong> ${fechaEntrega}</div>`;
                        } else {
                            fechaInfo = 'Fecha no disponible';
                        }
                        
                        $('#modalDate').html(fechaInfo);
                    } else {
                        // Para primaria, mantener el comportamiento original
                        const fechaFormateada = formatearFecha(
                            info.event.extendedProps.fecha_entrega,
                            info.event.extendedProps.hora_entrega
                        );
                        $('#modalDate').text(fechaFormateada);
                    }
                    
                    if (info.event.extendedProps.archivo) {
                        // Generar la URL de S3 usando la configuración del servidor
                        const s3Url = s3BaseUrl + '/' + info.event.extendedProps.archivo;
                        $('#modalResources').html(`<a href="${s3Url}" class="text-indigo-600 hover:text-indigo-800" target="_blank">Ver archivo adjunto</a>`);
                    } else {
                        $('#modalResources').text('No hay recursos disponibles');
                    }
                    
                    $('#eventModal').removeClass('hidden');
                }
            });
            calendar.render();

            // Cerrar el modal
            $('#closeModal').on('click', function () {
                $('#eventModal').addClass('hidden');
            });

            // Cerrar el modal al hacer clic fuera
            $('#eventModal').on('click', function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });
            
            
        });
        
        
        // La función descargarCircular() ya no es necesaria ya que ahora usamos enlaces directos
        
        // Función para abrir imagen en pantalla completa
        function openFullscreenImage(imageSrc, imageAlt) {
            const modal = document.getElementById('imageModal');
            const image = document.getElementById('fullscreenImage');
            
            image.src = imageSrc;
            image.alt = imageAlt;
            modal.classList.remove('hidden');
            
            // Prevenir scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        // Cerrar modal de imagen
        document.getElementById('closeImageModal').addEventListener('click', function() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
        
        // Cerrar modal de imagen al hacer clic fuera
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Cerrar modal de imagen con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const imageModal = document.getElementById('imageModal');
                if (!imageModal.classList.contains('hidden')) {
                    imageModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }
        });
        
    </script>
</body>
</html>
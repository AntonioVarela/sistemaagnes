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

        /* Estilos para el logo */
        @media (max-width: 768px) {
            .header-logo {
                height: 2rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .header-logo {
                height: 1.5rem !important;
            }
        }

        /* Estilos para el carrusel */
        .carousel-indicator.active {
            background-color: #10b981 !important;
        }
        
        .carousel-indicator:hover {
            background-color: #059669 !important;
        }
        
        /* Animaciones del carrusel */
        #cursosCarouselTrack {
            transition: transform 0.5s ease-in-out;
        }
        
        /* Efectos hover para las imágenes del carrusel */
        #cursosCarousel .group:hover img {
            transform: scale(1.02);
            transition: transform 0.3s ease-in-out;
        }
        
        /* Responsive para el carrusel */
        @media (max-width: 768px) {
            #cursosCarousel .w-full {
                height: 12rem !important;
            }
            
            #prevBtn, #nextBtn {
                padding: 0.5rem !important;
            }
            
            #prevBtn svg, #nextBtn svg {
                width: 1rem !important;
                height: 1rem !important;
            }
        }
        
        /* Responsive para los botones de descarga */
        @media (max-width: 1024px) {
            .header-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .header-buttons .space-x-2 {
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .header-buttons {
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }
            
            .header-buttons a {
                width: 100%;
                justify-content: center;
            }
            
            .header h1 {
                font-size: 1.5rem !important;
                text-align: center;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Panel de Actividades de {{ $grupo->nombre }} {{ $grupo->seccion }}</h1>
                    <div class="flex items-center space-x-4">
                        <!-- Botón de Descarga PDF -->
                        <div class="flex items-center space-x-2 header-buttons">
                            <a href="{{ route('tareas.pdf.download', $grupo->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                 Descargar Tareas PDF
                            </a>
                            
                            <!-- Botón de Vista Previa (opcional) -->
                            <a href="{{ route('tareas.pdf.preview', $grupo->id) }}" 
                               target="_blank"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                 Vista Previa
                            </a>
                        </div>
                        
                        <a href="{{ route('home') }}" class="hover:opacity-80 transition-opacity duration-200">
                            <img src="/logo.png" alt="Logo" class="header-logo h-12 w-auto opacity-60" />
                        </a>
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
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    📢 Anuncio
                                                </span>
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
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:border-blue-200 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    📋 Circular
                                                </span>
                                                @if($circular->es_global)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        🌍 Global
                                                    </span>
                                                @endif
                                                <h4 class="text-lg font-semibold text-blue-900">{{ $circular->titulo }}</h4>
                                            </div>
                                            @if($circular->descripcion)
                                                <p class="text-sm text-blue-700 mt-1">{{ Str::limit($circular->descripcion, 100) }}</p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full ml-2">
                                            {{ $circular->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center text-xs text-blue-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $circular->user->name ?? 'Usuario no disponible' }}
                                        </div>
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

                    <!-- Carrusel de Cursos Adicionales -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Cursos Adicionales
                        </h2>
                        
                        <!-- Carrusel de Imágenes -->
                        <div class="relative">
                            <div id="cursosCarousel" class="overflow-hidden rounded-lg">
                                <div class="flex transition-transform duration-500 ease-in-out" id="cursosCarouselTrack">
                                    @php
                                        $cursosActivos = \App\Models\Curso::activos()->ordenados()->take(5)->get();
                                    @endphp
                                    
                                    @if($cursosActivos->count() > 0)
                                        @foreach($cursosActivos as $curso)
                                        <div class="w-full flex-shrink-0">
                                            <div class="relative group cursor-pointer">
                                                @if($curso->imagen)
                                                    <img src="{{ $curso->url_imagen }}" alt="{{ $curso->titulo }}" class="w-full h-48 object-cover rounded-lg">
                                                @else
                                                    <div class="w-full h-48 bg-gradient-to-br from-{{ $loop->index % 2 == 0 ? 'blue' : 'emerald' }}-500 to-{{ $loop->index % 2 == 0 ? 'purple' : 'teal' }}-600 rounded-lg flex items-center justify-center">
                                                        <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent rounded-lg"></div>
                                                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                                    <h3 class="text-lg font-semibold mb-1">{{ $curso->titulo }}</h3>
                                                    <p class="text-sm opacity-90">{{ $curso->descripcion }}</p>
                                                </div>
                                                <div class="absolute top-3 right-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-500 text-white">
                                                        {{ ucfirst($curso->nivel) }}
                                                    </span>
                                                </div>
                                                <div class="absolute top-3 left-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                                        {{ ucfirst($curso->categoria) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <!-- Fallback si no hay cursos -->
                                        <div class="w-full flex-shrink-0">
                                            <div class="relative group cursor-pointer">
                                                <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <div class="text-center text-white">
                                                        <svg class="w-16 h-16 mx-auto mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                        </svg>
                                                        <p class="text-lg font-semibold">No hay cursos disponibles</p>
                                                        <p class="text-sm opacity-80">Contacta con tu administrador</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Botones de Navegación -->
                            <button id="prevBtn" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-lg transition-all duration-200 hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button id="nextBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-lg transition-all duration-200 hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            
                            <!-- Indicadores de Página -->
                            <div class="flex justify-center mt-4 space-x-2" id="carouselIndicators">
                                @php
                                    $cursosActivos = \App\Models\Curso::activos()->ordenados()->take(5)->get();
                                @endphp
                                @for($i = 0; $i < $cursosActivos->count(); $i++)
                                    <button class="w-2 h-2 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors duration-200 carousel-indicator {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"></button>
                                @endfor
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Sección del calendario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Calendario de Tareas
                        </h2>
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
                    
                    <small>¿Cuándo tengo que entregarla?</small>
                    <br>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <p id="modalDate" class="font-medium"></p>
                    </div>
                    
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Recursos Adjuntos</h4>
                    <p id="modalResources" class="text-sm text-gray-500">No hay recursos disponibles</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var tareas = @json($tareas);
            var grupo = @json($grupo); 
            var s3BaseUrl = '{{ config("filesystems.disks.s3.url") }}';
            var eventos;
            
            if(grupo.seccion == 'Primaria') {
                eventos = tareas.map(function(element) {
                    // Restar un día a la fecha para primaria
                    let date = new Date(element.fecha_entrega);
                    date.setDate(date.getDate() - 1);
                    let startDate = date.toISOString().split('T')[0];

                    return {
                        title: element.titulo,
                        start: startDate,
                        color: '#4F46E5',
                        allDay: false,
                        extendedProps: {
                            description: element.descripcion,
                            fecha_entrega: element.fecha_entrega,
                            hora_entrega: element.hora_entrega,
                            archivo: element.archivo
                        }
                    };
                });
            } else {
                eventos = tareas.map(function(element) {
                return {
                    title: element.titulo,
                    start: element.fecha_entrega,
                    color: '#4F46E5',
                    allDay: false,
                    extendedProps: {
                        description: element.descripcion,
                        fecha_entrega: element.fecha_entrega,
                        hora_entrega: element.hora_entrega,
                        archivo: element.archivo
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
                    $('#modalDate').text(new Date(info.event.extendedProps.fecha_entrega + 'T00:00:00').toLocaleDateString('es-ES', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    if(info.event.extendedProps.hora_entrega != null){
                        $('#modalDate').append(' a las ' + info.event.extendedProps.hora_entrega);
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
            
            // Inicializar el carrusel de cursos
            initCursosCarousel();
            
        });
        
        // Función para inicializar el carrusel de cursos
        function initCursosCarousel() {
            const track = document.getElementById('cursosCarouselTrack');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const indicators = document.querySelectorAll('.carousel-indicator');
            
            let currentSlide = 0;
            const totalSlides = indicators.length;
            
            // Función para ir a una slide específica
            function goToSlide(slideIndex) {
                currentSlide = slideIndex;
                const translateX = -slideIndex * 100;
                track.style.transform = `translateX(${translateX}%)`;
                
                // Actualizar indicadores
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index === slideIndex);
                    indicator.classList.toggle('bg-emerald-500', index === slideIndex);
                    indicator.classList.toggle('bg-gray-300', index !== slideIndex);
                });
            }
            
            // Event listeners para botones de navegación
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    goToSlide(currentSlide);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    goToSlide(currentSlide);
                });
            }
            
            // Event listeners para indicadores
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    goToSlide(index);
                });
            });
            
            // Auto-play del carrusel (opcional)
            let autoPlayInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                goToSlide(currentSlide);
            }, 5000); // Cambiar cada 5 segundos
            
            // Pausar auto-play al hacer hover
            const carousel = document.getElementById('cursosCarousel');
            if (carousel) {
                carousel.addEventListener('mouseenter', () => {
                    clearInterval(autoPlayInterval);
                });
                
                carousel.addEventListener('mouseleave', () => {
                    autoPlayInterval = setInterval(() => {
                        currentSlide = (currentSlide + 1) % totalSlides;
                        goToSlide(currentSlide);
                    }, 5000);
                });
            }
        }
        
        // La función descargarCircular() ya no es necesaria ya que ahora usamos enlaces directos
        
    </script>
</body>
</html>
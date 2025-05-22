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
            background: #4338CA !important;
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
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900">Panel de Actividades</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sección de anuncios -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            Anuncios Importantes
                        </h2>
                        <div class="space-y-4">
                            @if(count($tareas) == 0)
                                @foreach($tareas as $tarea)
                                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 hover:border-indigo-200 transition-colors">
                                    <h3 class="text-lg font-semibold text-indigo-900">{{ $tarea->titulo }}</h3>
                                    <p class="text-sm text-indigo-700 mt-1">{{ $tarea->descripcion }}</p>
                                    <div class="mt-3 flex items-center text-xs text-indigo-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Entrega: {{ $tarea->fecha_entrega }} {{ $tarea->hora_entrega }}
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="bg-gray-50 rounded-lg p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No hay anuncios disponibles</h3>
                                    <p class="text-sm text-gray-500">Los anuncios importantes aparecerán aquí cuando estén disponibles.</p>
                                </div>
                            @endif
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
                <div>
                    <h3 class="text-lg font-semibold text-indigo-600 mb-2" id="modalTitle"></h3>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <p id="modalDescription" class="text-gray-700"></p>
                    </div>
                </div>

                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span id="modalDate" class="font-medium"></span>
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
            var eventos = tareas.map(element => {
                return {
                    title: element.titulo,
                    start: element.created_at,
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

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'listWeek',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
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
                    $('#modalDescription').text(info.event.extendedProps.description);
                    $('#modalDate').text('Fecha de entrega: ' + info.event.extendedProps.fecha_entrega + ' ' + info.event.extendedProps.hora_entrega);
                    
                    if (info.event.extendedProps.archivo) {
                        $('#modalResources').html(`<a href="/storage/${info.event.extendedProps.archivo}" class="text-indigo-600 hover:text-indigo-800" target="_blank">Ver archivo adjunto</a>`);
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
    </script>
</body>
</html>
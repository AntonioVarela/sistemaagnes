<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/locales/es.global.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Actividades a realizar</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sección de anuncios -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Anuncios</h2>
                <div class="space-y-4">
                    <!-- Card de anuncio 1 -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="text-lg font-bold">Anuncio 1</h3>
                        <p class="text-sm text-gray-600">Este es el contenido del anuncio 1.</p>
                    </div>
                    <!-- Card de anuncio 2 -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="text-lg font-bold">Anuncio 2</h3>
                        <p class="text-sm text-gray-600">Este es el contenido del anuncio 2.</p>
                    </div>
                    <!-- Card de anuncio 3 -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="text-lg font-bold">Anuncio 3</h3>
                        <p class="text-sm text-gray-600">Este es el contenido del anuncio 3.</p>
                    </div>
                </div>
            </div>

            <!-- Sección del calendario -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Calendario</h2>
                <div id='calendar' class="bg-white rounded-lg p-4"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="eventModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
            <p id="modalDescription" class="text-gray-700 mb-6"></p>
            <button id="closeModal" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Cerrar
            </button>
        </div>
    </div>
</body>
<script>
    $(document).ready(function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'listWeek',
            locale: 'es', // Configurar el idioma a español
            events: [
                { 
                    title: 'Matemáticas', 
                    start: '2025-05-06', 
                    extendedProps: {
                        description: 'Descripción del Evento 1'
                    }
                },
                { 
                    title: 'Español', 
                    start: '2025-05-06', 
                    extendedProps: {
                        description: 'Descripción del Evento 3'
                    }
                },
                { 
                    title: 'Historia', 
                    start: '2025-05-07', 
                    allDay: false,
                    startTime: '11:00:00',
                    endTime: '11:30:00',
                    color: 'red',
                    extendedProps: {
                        description: 'Descripción del Evento 2'
                    }
                },
            ],
            eventClick: function(info) {
                // Mostrar el modal con los datos del evento
                $('#modalTitle').text(info.event.title);
                $('#modalDescription').text(info.event.extendedProps.description);
                $('#eventModal').removeClass('hidden');
            }
        });
        calendar.render();

        // Cerrar el modal
        $('#closeModal').on('click', function () {
            $('#eventModal').addClass('hidden');
        });
    });
</script>
</html>
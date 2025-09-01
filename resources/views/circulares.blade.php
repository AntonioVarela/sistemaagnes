<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circulares Semanales</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Circulares Semanales</h1>
                    <div class="flex items-center space-x-4">
                        <button onclick="openModal('new-circular')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nueva Circular
                        </button>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">← Volver al Dashboard</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Filtrar por:</label>
                        <select id="filtroSeccion" class="text-sm border border-gray-300 rounded-md px-3 py-1 bg-white text-gray-900">
                            <option value="">Todas las secciones</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Estado:</label>
                        <select id="filtroEstado" class="text-sm border border-gray-300 rounded-md px-3 py-1 bg-white text-gray-900">
                            <option value="">Todos</option>
                            <option value="activas">Activas</option>
                            <option value="expiradas">Expiradas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de Circulares -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subido por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expira</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if(isset($circulares) && count($circulares) > 0)
                                @foreach ($circulares as $circular)
                                    <tr class="hover:bg-gray-50 transition-colors circular-row" 
                                        data-seccion="{{ $circular->seccion }}" 
                                        data-estado="{{ $circular->estaActiva() ? 'activas' : 'expiradas' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $circular->titulo }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $circular->seccion }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ Str::limit($circular->descripcion, 100) ?: 'Sin descripción' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($circular->archivo)
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('circulares.download', $circular->id) }}" 
                                                       class="inline-flex items-center px-3 py-1 text-sm text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200 transition-colors" 
                                                       target="_blank">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        {{ $circular->nombre_archivo_original }}
                                                    </a>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $circular->tipo_archivo }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-500">Sin archivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $circular->grupo->nombre ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $circular->user->name ?? 'Usuario no disponible' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $circular->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $circular->created_at->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($circular->fecha_expiracion)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($circular->fecha_expiracion->isPast()) 
                                                            bg-red-100 text-red-800
                                                        @elseif($circular->fecha_expiracion->isToday()) 
                                                            bg-yellow-100 text-yellow-800
                                                        @else 
                                                            bg-green-100 text-green-800
                                                        @endif">
                                                        {{ $circular->fecha_expiracion->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">Sin expiración</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                @if(auth()->user()->id === $circular->usuario_id || auth()->user()->rol === 'administrador')
                                                    <button onclick="prepareEditCircular({{ $circular->id }}, '{{ $circular->titulo }}', '{{ $circular->descripcion }}', '{{ $circular->grupo_id }}', '{{ $circular->seccion }}', '{{ $circular->fecha_expiracion ? $circular->fecha_expiracion->format('Y-m-d') : 'null' }}')" 
                                                            class="text-indigo-600 hover:text-indigo-900">
                                                        Editar
                                                    </button>
                                                    <form action="{{ route('circulares.destroy', $circular->id) }}" method="POST" class="form-eliminar inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500">Solo el creador puede editar</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center">
                                        <div class="bg-gray-50 rounded-lg p-8">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No hay circulares disponibles</h3>
                                            <p class="text-sm text-gray-500">Las circulares aparecerán aquí cuando estén disponibles.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para nueva circular -->
    <div id="new-circular" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl mx-4 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Nueva Circular Semanal</h2>
                <button onclick="closeModal('new-circular')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('circulares.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título de la Circular *</label>
                        <input type="text" id="titulo" name="titulo" required 
                               placeholder="Ej: Circular Semanal del 1-5 de Septiembre"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                        <textarea id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Breve descripción del contenido de la circular..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label for="archivo" class="block text-sm font-medium text-gray-700 mb-1">Archivo de la Circular *</label>
                        <input type="file" id="archivo" name="archivo" required 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 10MB.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="grupo_id" class="block text-sm font-medium text-gray-700 mb-1">Grupo *</label>
                            <select id="grupo_id" name="grupo_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="seccion" class="block text-sm font-medium text-gray-700 mb-1">Sección *</label>
                            <select id="seccion" name="seccion" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="fecha_expiracion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Expiración (opcional)</label>
                        <input type="date" id="fecha_expiracion" name="fecha_expiracion" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Si no se especifica, la circular no expirará.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('new-circular')" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Subir Circular
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar circular -->
    <div id="edit-circular" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl mx-4 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Editar Circular</h2>
                <button onclick="closeModal('edit-circular')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editCircularForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit_titulo" class="block text-sm font-medium text-gray-700 mb-1">Título de la Circular *</label>
                        <input type="text" id="edit_titulo" name="titulo" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label for="edit_archivo" class="block text-sm font-medium text-gray-700 mb-1">Archivo de la Circular</label>
                        <input type="file" id="edit_archivo" name="archivo" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Deja vacío para mantener el archivo actual. Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 10MB.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_grupo_id" class="block text-sm font-medium text-gray-700 mb-1">Grupo *</label>
                            <select id="edit_grupo_id" name="grupo_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="edit_seccion" class="block text-sm font-medium text-gray-700 mb-1">Sección *</label>
                            <select id="edit_seccion" name="seccion" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="edit_fecha_expiracion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Expiración (opcional)</label>
                        <input type="date" id="edit_fecha_expiracion" name="fecha_expiracion" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Si no se especifica, la circular no expirará.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('edit-circular')" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Actualizar Circular
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones para modales
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Cerrar modales al hacer clic fuera
        document.querySelectorAll('[id$="-circular"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Filtros
        document.getElementById('filtroSeccion').addEventListener('change', filtrarCirculares);
        document.getElementById('filtroEstado').addEventListener('change', filtrarCirculares);

        function filtrarCirculares() {
            const seccion = document.getElementById('filtroSeccion').value;
            const estado = document.getElementById('filtroEstado').value;
            const filas = document.querySelectorAll('.circular-row');

            filas.forEach(fila => {
                const filaSeccion = fila.dataset.seccion;
                const filaEstado = fila.dataset.estado;
                
                const mostrarSeccion = !seccion || filaSeccion === seccion;
                const mostrarEstado = !estado || filaEstado === estado;
                
                if (mostrarSeccion && mostrarEstado) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        // Función para preparar el modal de edición
        function prepareEditCircular(id, titulo, descripcion, grupoId, seccion, fechaExpiracion) {
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_grupo_id').value = grupoId;
            document.getElementById('edit_seccion').value = seccion;
            document.getElementById('edit_fecha_expiracion').value = fechaExpiracion !== 'null' ? fechaExpiracion : '';
            
            // Actualizar la acción del formulario
            document.getElementById('editCircularForm').action = `/circulares/${id}`;
            
            // Abrir el modal
            openModal('edit-circular');
        }

        // Confirmación para eliminar
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('¿Estás seguro de que quieres eliminar esta circular? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

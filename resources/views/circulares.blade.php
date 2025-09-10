<x-layouts.app :title="__('Circulares')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header de la página -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Circulares Semanales</h1>
                <button onclick="openModal('new-circular')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Circular
                </button>
            </div>
        </div>
            <!-- Filtros -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-zinc-300">Filtrar por:</label>
                        <select id="filtroSeccion" class="text-sm border border-zinc-300 dark:border-zinc-600 rounded-md px-3 py-1 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                            <option value="">Todas las secciones</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-zinc-300">Estado:</label>
                        <select id="filtroEstado" class="text-sm border border-zinc-300 dark:border-zinc-600 rounded-md px-3 py-1 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                            <option value="">Todos</option>
                            <option value="activas">Activas</option>
                            <option value="expiradas">Expiradas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de Circulares -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Archivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Alcance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Subido por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Expira</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @if(isset($circulares) && count($circulares) > 0)
                                @foreach ($circulares as $circular)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors circular-row" 
                                        data-seccion="{{ $circular->seccion }}" 
                                        data-estado="{{ $circular->estaActiva() ? 'activas' : 'expiradas' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $circular->titulo }}</div>
                                            <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                {{ $circular->seccion }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-zinc-100">
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
                                            @if($circular->es_global)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    🌍 Global
                                                </span>
                                            @else
                                                <div class="text-sm text-gray-900">
                                                    {{ $circular->grupo->nombre ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $circular->seccion }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-zinc-100">
                                                {{ $circular->user->name ?? 'Usuario no disponible' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                {{ $circular->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-zinc-100">
                                                {{ $circular->created_at->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-zinc-100">
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
                                                    <span class="text-gray-500 dark:text-zinc-400">Sin expiración</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                @if(auth()->user()->id === $circular->usuario_id || auth()->user()->rol === 'administrador')
                                                    <button onclick="prepareEditCircular({{ $circular->id }}, '{{ $circular->titulo }}', '{{ $circular->descripcion }}', '{{ $circular->grupo_id }}', '{{ $circular->seccion }}', '{{ $circular->fecha_expiracion ? $circular->fecha_expiracion->format('Y-m-d') : 'null' }}', '{{ $circular->es_global ? 'true' : 'false' }}')" 
                                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Editar
                                                    </button>
                                                    <form action="{{ route('circulares.destroy', $circular->id) }}" method="POST" class="form-eliminar inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500 dark:text-zinc-400">Solo el creador puede editar</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center">
                                        <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-8">
                                            <svg class="w-12 h-12 mx-auto text-zinc-400 dark:text-zinc-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-1">No hay circulares disponibles</h3>
                                            <p class="text-sm text-gray-500 dark:text-zinc-400">Las circulares aparecerán aquí cuando estén disponibles.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Modal para nueva circular -->
    <div id="new-circular" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-8 w-full max-w-2xl mx-4 transform transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">Nueva Circular Semanal</h2>
                <button onclick="closeModal('new-circular')" class="text-gray-400 hover:text-gray-500 dark:text-zinc-400 dark:hover:text-zinc-300 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('circulares.store') }}" method="POST" enctype="multipart/form-data" id="formNuevaCircular">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Título de la Circular *</label>
                        <input type="text" id="titulo" name="titulo" required 
                               placeholder="Ej: Circular Semanal del 1-5 de Septiembre"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Descripción (opcional)</label>
                        <textarea id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Breve descripción del contenido de la circular..."
                                  class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100"></textarea>
                    </div>

                    <div>
                        <label for="archivo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Archivo de la Circular *</label>
                        <input type="file" id="archivo" name="archivo" required 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                            Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 10MB.
                        </p>
                    </div>

                    <div class="flex items-center mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <input type="checkbox" name="es_global" id="es_global" value="1" 
                               class="w-4 h-4 text-blue-600 bg-zinc-100 dark:bg-zinc-700 border-zinc-300 dark:border-zinc-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-zinc-800 focus:ring-2">
                        <label for="es_global" class="ml-2 text-sm font-medium text-gray-700 dark:text-zinc-300">
                            🌍 Circular Global (visible para todos los grupos)
                        </label>
                        <!-- Campo oculto para asegurar que se envíe el valor -->
                        <input type="hidden" name="es_global_hidden" value="0">
                    </div>

                    <div id="grupo-seccion-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="grupo_id" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Grupo *</label>
                            <select id="grupo_id" name="grupo_id" required
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="seccion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Sección *</label>
                            <select id="seccion" name="seccion" required
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="fecha_expiracion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Fecha de Expiración (opcional)</label>
                        <input type="date" id="fecha_expiracion" name="fecha_expiracion" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                            Si no se especifica, la circular no expirará.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('new-circular')" 
                            class="px-4 py-2 text-gray-700 dark:text-zinc-300 bg-zinc-200 dark:bg-zinc-600 rounded-md hover:bg-zinc-300 dark:hover:bg-zinc-500 transition-colors">
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
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-8 w-full max-w-2xl mx-4 transform transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">Editar Circular</h2>
                <button onclick="closeModal('edit-circular')" class="text-gray-400 hover:text-gray-500 dark:text-zinc-400 dark:hover:text-zinc-300 focus:outline-none">
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
                        <label for="edit_titulo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Título de la Circular *</label>
                        <input type="text" id="edit_titulo" name="titulo" required
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                    </div>

                    <div>
                        <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Descripción (opcional)</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100"></textarea>
                    </div>

                    <div>
                        <label for="edit_archivo" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Archivo de la Circular</label>
                        <input type="file" id="edit_archivo" name="archivo" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                            Deja vacío para mantener el archivo actual. Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 10MB.
                        </p>
                    </div>

                    <div class="flex items-center mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <input type="checkbox" name="es_global" id="edit_es_global" value="1" 
                               class="w-4 h-4 text-blue-600 bg-zinc-100 dark:bg-zinc-700 border-zinc-300 dark:border-zinc-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-zinc-800 focus:ring-2">
                        <label for="edit_es_global" class="ml-2 text-sm font-medium text-gray-700 dark:text-zinc-300">
                            🌍 Circular Global (visible para todos los grupos)
                        </label>
                        <!-- Campo oculto para asegurar que se envíe el valor -->
                        <input type="hidden" name="es_global_hidden" value="0">
                    </div>

                    <div id="edit-grupo-seccion-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_grupo_id" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Grupo *</label>
                            <select id="edit_grupo_id" name="grupo_id" required
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="edit_seccion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Sección *</label>
                            <select id="edit_seccion" name="seccion" required
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="edit_fecha_expiracion" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Fecha de Expiración (opcional)</label>
                        <input type="date" id="edit_fecha_expiracion" name="fecha_expiracion" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100">
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                            Si no se especifica, la circular no expirará.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('edit-circular')" 
                            class="px-4 py-2 text-gray-700 dark:text-zinc-300 bg-zinc-200 dark:bg-zinc-600 rounded-md hover:bg-zinc-300 dark:hover:bg-zinc-500 transition-colors">
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
        function prepareEditCircular(id, titulo, descripcion, grupoId, seccion, fechaExpiracion, esGlobal) {
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_es_global').checked = esGlobal === 'true';
            document.getElementById('edit_grupo_id').value = grupoId;
            document.getElementById('edit_seccion').value = seccion;
            document.getElementById('edit_fecha_expiracion').value = fechaExpiracion !== 'null' ? fechaExpiracion : '';
            
            // Actualizar la acción del formulario
            document.getElementById('editCircularForm').action = `/circulares/${id}`;
            
            // Aplicar la lógica de mostrar/ocultar campos según si es global
            toggleCircularGlobal(esGlobal === 'true', 'edit-grupo-seccion-container');
            
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

        // Función para manejar el checkbox de circular global
        function toggleCircularGlobal(isGlobal, containerId) {
            const grupoSeccionContainer = document.getElementById(containerId);
            if (grupoSeccionContainer) {
                // Mostrar/ocultar el contenedor
                grupoSeccionContainer.style.display = isGlobal ? 'none' : 'grid';
                
                // Obtener los campos de grupo y sección
                const grupoSelect = grupoSeccionContainer.querySelector('select[name="grupo_id"]');
                const seccionSelect = grupoSeccionContainer.querySelector('select[name="seccion"]');
                
                if (grupoSelect) {
                    grupoSelect.required = !isGlobal;
                    if (isGlobal) {
                        grupoSelect.value = '';
                        grupoSelect.disabled = true;
                    } else {
                        grupoSelect.disabled = false;
                    }
                }
                
                if (seccionSelect) {
                    seccionSelect.required = !isGlobal;
                    if (isGlobal) {
                        seccionSelect.value = '';
                        seccionSelect.disabled = true;
                    } else {
                        seccionSelect.disabled = false;
                    }
                }
                
                // Agregar una clase visual para indicar que está deshabilitado
                if (isGlobal) {
                    grupoSeccionContainer.classList.add('opacity-50', 'pointer-events-none');
                } else {
                    grupoSeccionContainer.classList.remove('opacity-50', 'pointer-events-none');
                }
            }
        }

        // Event listeners para los checkboxes de circular global
        document.addEventListener('DOMContentLoaded', function() {
            const esGlobalCheckbox = document.getElementById('es_global');
            const editEsGlobalCheckbox = document.getElementById('edit_es_global');
            
            if (esGlobalCheckbox) {
                esGlobalCheckbox.addEventListener('change', function() {
                    toggleCircularGlobal(this.checked, 'grupo-seccion-container');
                });
            }
            
            if (editEsGlobalCheckbox) {
                editEsGlobalCheckbox.addEventListener('change', function() {
                    toggleCircularGlobal(this.checked, 'edit-grupo-seccion-container');
                });
            }
            
            // Validación del formulario de nueva circular
            const formNuevaCircular = document.getElementById('formNuevaCircular');
            if (formNuevaCircular) {
                formNuevaCircular.addEventListener('submit', function(e) {
                    const esGlobal = document.getElementById('es_global').checked;
                    const grupoId = document.getElementById('grupo_id').value;
                    const seccion = document.getElementById('seccion').value;
                    
                    console.log('Enviando formulario - esGlobal:', esGlobal);
                    
                    // Si no es global, validar que se seleccione grupo y sección
                    if (!esGlobal) {
                        if (!grupoId) {
                            e.preventDefault();
                            alert('Debe seleccionar un grupo para la circular.');
                            document.getElementById('grupo_id').focus();
                            return false;
                        }
                        if (!seccion) {
                            e.preventDefault();
                            alert('Debe seleccionar una sección para la circular.');
                            document.getElementById('seccion').focus();
                            return false;
                        }
                    } else {
                        // Si es global, limpiar los valores de grupo y sección
                        document.getElementById('grupo_id').value = '';
                        document.getElementById('seccion').value = '';
                        // Asegurar que el campo oculto tenga el valor correcto
                        document.querySelector('input[name="es_global_hidden"]').value = '1';
                    }
                });
            }
            
            // Validación del formulario de edición
            const editCircularForm = document.getElementById('editCircularForm');
            if (editCircularForm) {
                editCircularForm.addEventListener('submit', function(e) {
                    const esGlobal = document.getElementById('edit_es_global').checked;
                    const grupoId = document.getElementById('edit_grupo_id').value;
                    const seccion = document.getElementById('edit_seccion').value;
                    
                    // Si no es global, validar que se seleccione grupo y sección
                    if (!esGlobal) {
                        if (!grupoId) {
                            e.preventDefault();
                            alert('Debe seleccionar un grupo para la circular.');
                            document.getElementById('edit_grupo_id').focus();
                            return false;
                        }
                        if (!seccion) {
                            e.preventDefault();
                            alert('Debe seleccionar una sección para la circular.');
                            document.getElementById('edit_seccion').focus();
                            return false;
                        }
                    } else {
                        // Si es global, limpiar los valores de grupo y sección
                        document.getElementById('edit_grupo_id').value = '';
                        document.getElementById('edit_seccion').value = '';
                    }
                });
            }
        });
    </script>
</x-layouts.app>

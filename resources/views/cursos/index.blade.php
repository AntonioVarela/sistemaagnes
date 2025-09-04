<x-layouts.app :title="__('Cursos')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Cursos') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los cursos adicionales disponibles</p>
            </div>
            <flux:modal.trigger name="new-course">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Curso</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table id="myTable" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Imagen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nivel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($cursos as $curso)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($curso->imagen)
                                        <img src="{{ $curso->url_imagen }}" alt="{{ $curso->titulo }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $curso->titulo }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Creado por: {{ $curso->user->name ?? 'Usuario no disponible' }}
                                    </div>
                                    @if($curso->descripcion)
                                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                            {{ Str::limit($curso->descripcion, 100) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $curso->categoria }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $curso->nivel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($curso->activo)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $curso->orden }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="edit-course">
                                            <flux:button icon='pencil' variant="filled" 
                                                onclick="prepareEditModal({{ $curso->id }}, '{{ $curso->titulo }}', '{{ $curso->descripcion }}', '{{ $curso->categoria }}', '{{ $curso->nivel }}', {{ $curso->activo ? 'true' : 'false' }}, {{ $curso->orden }}, '{{ $curso->url_externa }}', '{{ $curso->contenido_detallado }}')" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </flux:button>
                                        </flux:modal.trigger>
                                        
                                        <form action="{{ route('cursos.toggle-status', $curso->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-{{ $curso->activo ? 'yellow' : 'green' }}-600 hover:text-{{ $curso->activo ? 'yellow' : 'green' }}-900 transition-colors">
                                                <flux:icon name="{{ $curso->activo ? 'pause' : 'play' }}" />
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('cursos.destroy', $curso->id) }}" method="POST" class="form-eliminar inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                                <flux:icon name="trash" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de nuevo curso -->
    <flux:modal name="new-course" class="md:w-[600px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Curso</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('cursos.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="titulo" label="Título" type="text"
                        placeholder="Ingresa el título del curso" required />
                    
                    <flux:textarea name="descripcion" id="descripcion" label="Descripción"
                        placeholder="Ingresa una descripción breve del curso" />
                    
                    <flux:input name="imagen" id="imagen" label="Imagen del curso" type="file" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select name="categoria" id="categoria" label="Categoría" required>
                            <option value="">Selecciona una categoría</option>
                            <option value="general">General</option>
                            <option value="matematicas">Matemáticas</option>
                            <option value="ciencias">Ciencias</option>
                            <option value="lenguaje">Lenguaje</option>
                            <option value="arte">Arte</option>
                            <option value="deportes">Deportes</option>
                            <option value="tecnologia">Tecnología</option>
                        </flux:select>
                        
                        <flux:select name="nivel" id="nivel" label="Nivel" required>
                            <option value="">Selecciona un nivel</option>
                            <option value="básico">Básico</option>
                            <option value="intermedio">Intermedio</option>
                            <option value="avanzado">Avanzado</option>
                        </flux:select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="orden" id="orden" label="Orden de visualización" type="number" min="0" value="0" />
                        <flux:input name="url_externa" id="url_externa" label="URL externa (opcional)" type="url" placeholder="https://ejemplo.com" />
                    </div>
                    
                    <flux:textarea name="contenido_detallado" id="contenido_detallado" label="Contenido detallado"
                        placeholder="Información detallada del curso" />
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" value="1" checked
                               class="w-4 h-4 text-blue-600 bg-zinc-100 dark:bg-zinc-700 border-zinc-300 dark:border-zinc-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-zinc-800 focus:ring-2">
                        <label for="activo" class="ml-2 text-sm font-medium text-gray-700 dark:text-zinc-300">
                            Curso activo (visible en el carrusel)
                        </label>
                    </div>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('new-course')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar curso</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-course" class="md:w-[600px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Editar Curso</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-course-form" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid gap-4">
                    <flux:input name="titulo" id="edit_titulo" label="Título" type="text"
                        placeholder="Ingresa el título del curso" required />
                    
                    <flux:textarea name="descripcion" id="edit_descripcion" label="Descripción"
                        placeholder="Ingresa una descripción breve del curso" />
                    
                    <flux:input name="imagen" id="edit_imagen" label="Imagen del curso" type="file" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select name="categoria" id="edit_categoria" label="Categoría" required>
                            <option value="">Selecciona una categoría</option>
                            <option value="general">General</option>
                            <option value="matematicas">Matemáticas</option>
                            <option value="ciencias">Ciencias</option>
                            <option value="lenguaje">Lenguaje</option>
                            <option value="arte">Arte</option>
                            <option value="deportes">Deportes</option>
                            <option value="tecnologia">Tecnología</option>
                        </flux:select>
                        
                        <flux:select name="nivel" id="edit_nivel" label="Nivel" required>
                            <option value="">Selecciona un nivel</option>
                            <option value="básico">Básico</option>
                            <option value="intermedio">Intermedio</option>
                            <option value="avanzado">Avanzado</option>
                        </flux:select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="orden" id="edit_orden" label="Orden de visualización" type="number" min="0" />
                        <flux:input name="url_externa" id="edit_url_externa" label="URL externa (opcional)" type="url" placeholder="https://ejemplo.com" />
                    </div>
                    
                    <flux:textarea name="contenido_detallado" id="edit_contenido_detallado" label="Contenido detallado"
                        placeholder="Información detallada del curso" />
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="edit_activo" value="1"
                               class="w-4 h-4 text-blue-600 bg-zinc-100 dark:bg-zinc-700 border-zinc-300 dark:border-zinc-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-zinc-800 focus:ring-2">
                        <label for="edit_activo" class="ml-2 text-sm font-medium text-gray-700 dark:text-zinc-300">
                            Curso activo (visible en el carrusel)
                        </label>
                    </div>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-course')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar curso</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.js"></script>
    <script>
        function iniciarComponentes() {
            // Inicializar DataTable con configuración específica
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#myTable')) {
                $('#myTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                    dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                    order: [[5, 'asc']], // Ordenar por orden ascendente
                    columnDefs: [
                        { orderable: false, targets: [0, -1] } // Deshabilitar ordenamiento en imagen y acciones
                    ]
                });
            }

            // Inicializar formularios de eliminación
            initDeleteForms();
        }

        // Funciones globales
        function closeModal(modalName) {
            const modal = document.querySelector(`[data-modal="${modalName}"]`);
            if (modal) {
                modal.close();
            }
        }

        function prepareEditModal(id, titulo, descripcion, categoria, nivel, activo, orden, url_externa, contenido_detallado) {
            const form = document.getElementById('edit-course-form');
            if (!form) return;

            form.action = `/cursos/${id}`;
            
            console.log('Preparando modal de edición con datos:', {
                id, titulo, descripcion, categoria, nivel, activo, orden, url_externa, contenido_detallado
            });
            
            // Configurar campos de texto
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_descripcion').value = descripcion || '';
            document.getElementById('edit_categoria').value = categoria;
            document.getElementById('edit_nivel').value = nivel;
            document.getElementById('edit_orden').value = orden;
            document.getElementById('edit_url_externa').value = url_externa || '';
            document.getElementById('edit_contenido_detallado').value = contenido_detallado || '';
            
            // Configurar checkbox - asegurarse de que sea booleano
            const checkbox = document.getElementById('edit_activo');
            if (checkbox) {
                checkbox.checked = activo === true || activo === 'true';
                console.log('Checkbox configurado:', activo, 'Tipo:', typeof activo, 'Resultado:', checkbox.checked);
            } else {
                console.error('No se encontró el checkbox edit_activo');
            }
        }

        // Función para inicializar checkboxes cuando se abren modales
        function initializeCheckboxes() {
            console.log('Inicializando checkboxes de cursos...');
            
            // Modal de nuevo curso
            const modalNewCourse = document.querySelector('[data-modal="new-course"]');
            if (modalNewCourse) {
                modalNewCourse.addEventListener('flux:opened', function() {
                    console.log('Modal nuevo curso abierto');
                    const checkbox = document.getElementById('activo');
                    if (checkbox) {
                        checkbox.checked = true; // Por defecto activo
                        console.log('Checkbox nuevo curso configurado:', checkbox.checked);
                    }
                });
            }
            
            // Modal de edición
            const modalEditCourse = document.querySelector('[data-modal="edit-course"]');
            if (modalEditCourse) {
                modalEditCourse.addEventListener('flux:opened', function() {
                    console.log('Modal de edición abierto, inicializando checkboxes...');
                });
            }
            
            // Agregar event listeners a los checkboxes para debugging
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Checkbox cambiado:', this.id, 'Valor:', this.checked);
                });
            });
        }
        
        // Función para manejar cambios dinámicos en el DOM
        function setupMutationObserver() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                const checkboxes = node.querySelectorAll ? node.querySelectorAll('input[type="checkbox"]') : [];
                                checkboxes.forEach(checkbox => {
                                    if (!checkbox.hasAttribute('data-event-added')) {
                                        checkbox.setAttribute('data-event-added', 'true');
                                        checkbox.addEventListener('change', function() {
                                            console.log('Checkbox dinámico cambiado:', this.id, 'Valor:', this.checked);
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
        
        // Función para verificar el estado del formulario antes de enviar
        function setupFormValidation() {
            const editForm = document.getElementById('edit-course-form');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    const checkbox = document.getElementById('edit_activo');
                    if (checkbox) {
                        console.log('Enviando formulario con checkbox:', checkbox.checked);
                    }
                });
            }
            
            const newForm = document.querySelector('form[action*="cursos.store"]');
            if (newForm) {
                newForm.addEventListener('submit', function(e) {
                    const checkbox = document.getElementById('activo');
                    if (checkbox) {
                        console.log('Enviando formulario nuevo con checkbox:', checkbox.checked);
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            iniciarComponentes();
            initializeCheckboxes();
            setupMutationObserver();
            setupFormValidation();
        });
        document.addEventListener('livewire:navigated', function() {
            iniciarComponentes();
            initializeCheckboxes();
            setupMutationObserver();
            setupFormValidation();
        });
    </script>
</x-layouts.app>

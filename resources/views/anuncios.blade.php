<x-layouts.app :title="__('Anuncios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Anuncios') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los anuncios del sistema</p>
            </div>
            <flux:modal.trigger name="new-announcement">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Anuncio</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table id="myTable" class="w-full min-w-full">
                <thead class="bg-indigo-200 dark:bg-gray-600">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Título</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden sm:table-cell">Contenido</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden md:table-cell">Alcance</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden xl:table-cell">Expira</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                    @foreach ($anuncios as $anuncio)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="ml-2 sm:ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $anuncio->titulo }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Creado por: {{ $anuncio->user->name ?? 'Usuario no disponible' }}
                                        </div>
                                        <!-- Información adicional para móviles -->
                                        <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <div>{{ Str::limit($anuncio->contenido, 100) }}</div>
                                            @if($anuncio->es_global)
                                                <div class="mt-1">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                        🌍 Global
                                                    </span>
                                                </div>
                                            @else
                                                <div class="mt-1">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        {{ $anuncio->grupo->nombre ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>{{ $anuncio->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden sm:table-cell">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ Str::limit($anuncio->contenido, 200) }}
                                </div>
                                @if($anuncio->archivo)
                                    <div class="mt-2">
                                        <a href="{{ $anuncio->url_archivo }}" 
                                           class="inline-flex items-center px-3 py-1 text-sm text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200 transition-colors" 
                                           target="_blank">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            Ver archivo
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                @if($anuncio->es_global)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        Global
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        {{ $anuncio->grupo->nombre ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $anuncio->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                                @if($anuncio->fecha_expiracion)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($anuncio->fecha_expiracion->isPast()) 
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($anuncio->fecha_expiracion->isToday()) 
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else 
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @endif">
                                        {{ $anuncio->fecha_expiracion->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin expiración</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    @if(auth()->user()->id === $anuncio->user_id || auth()->user()->rol === 'administrador')
                                        <flux:modal.trigger name="edit-announcement">
                                            <button type="button"
                                                onclick="prepareEditModal({{ $anuncio->id }}, '{{ addslashes($anuncio->titulo) }}', '{{ addslashes($anuncio->contenido) }}', '{{ $anuncio->grupo_id }}', '{{ $anuncio->materia_id }}', '{{ $anuncio->fecha_expiracion ? $anuncio->fecha_expiracion->format('Y-m-d') : 'null' }}', {{ $anuncio->es_global ? 'true' : 'false' }})"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors p-1">
                                                <flux:icon name="pencil" class="w-4 h-4" />
                                            </button>
                                        </flux:modal.trigger>
                                        <form action="{{ route('anuncios.destroy', $anuncio->id) }}" method="POST" class="form-eliminar inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors p-1">
                                                <flux:icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">Solo el creador puede editar</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de nuevo anuncio -->
    <flux:modal name="new-announcement" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Nuevo Anuncio</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('anuncios.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="titulo" label="Título" type="text"
                        placeholder="Ingresa el título del anuncio" required />
                    <flux:textarea name="contenido" id="contenido" label="Contenido"
                        placeholder="Ingresa el contenido del anuncio" required />
                                         <flux:input name="archivo" id="archivo" label="Archivo" type="file" />
                    <flux:input name="fecha_expiracion" id="fecha_expiracion" label="Fecha de expiración (opcional)" type="date" />
                    
                    <!-- Checkbox para anuncio global -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="es_global" id="es_global" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="es_global" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Anuncio Global (visible para todos los usuarios)
                        </label>
                    </div>
                    
                    @if(count($horario) > 1)
                        <div id="grupo-materia-new" class="grupo-materia-container grid grid-cols-2 gap-4">
                            <flux:select name="grupo_id" id="grupo" label="Grupo" onchange="filtrarMaterias(this.value)">
                                <option value="">Selecciona un grupo</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia_id" id="materia" label="Materia">
                                <option value="">Selecciona una materia</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}" data-grupos="{{ json_encode($materia->grupos->pluck('id')) }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('new-announcement')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar anuncio</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-announcement" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Editar Anuncio</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-announcement-form"  method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="edit_titulo" label="Título" type="text"
                        placeholder="Ingresa el título del anuncio" required />
                    <flux:textarea name="contenido" id="edit_contenido" label="Contenido"
                        placeholder="Ingresa el contenido del anuncio" required />
                                         <flux:input name="archivo" id="edit_archivo" label="Archivo" type="file" />
                     <flux:input name="fecha_expiracion" id="edit_fecha_expiracion" label="Fecha de expiración (opcional)" type="date" />
                     
                     <!-- Checkbox para anuncio global -->
                     <div class="flex items-center space-x-3">
                         <input type="checkbox" name="es_global" id="edit_es_global" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                         <label for="edit_es_global" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                             Anuncio Global (visible para todos los usuarios)
                         </label>
                     </div>
                     
                    @if(count($horario) > 1)
                        <div id="grupo-materia-edit" class="grupo-materia-container grid grid-cols-2 gap-4">
                            <flux:select name="grupo_id" id="edit_grupo" label="Grupo" onchange="filtrarMateriasEditar(this.value)">
                                <option value="">Selecciona un grupo</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia_id" id="edit_materia" label="Materia">
                                <option value="">Selecciona una materia</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}" data-grupos="{{ json_encode($materia->grupos->pluck('id')) }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-announcement')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar anuncio</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Flag global para evitar inicializaciones duplicadas
        let componentesInicializados = false;
        
        function iniciarComponentes() {
            // Evitar inicialización duplicada
            if (componentesInicializados) {
                return;
            }
            componentesInicializados = true;
            // Inicializar DataTable con configuración específica
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#myTable')) {
                $('#myTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                    dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                    order: [[2, 'desc']], // Ordenar por fecha descendente
                    columnDefs: [
                        { orderable: false, targets: -1 } // Deshabilitar ordenamiento en la columna de acciones
                    ]
                });
            }

            // Filtrar materias al cargar la página
            const grupoSelect = document.getElementById('grupo');
            if (grupoSelect) {
                filtrarMaterias(grupoSelect.value);
            }

            // Configuración de formularios de eliminación
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        // Funciones globales
        function closeModal(modalName) {
            const modal = document.querySelector(`[data-modal="${modalName}"]`);
            if (modal) {
                modal.close();
            }
        }

        function filtrarMaterias(grupoId) {
            const materiaSelect = document.getElementById('materia');
            if (!materiaSelect) return;

            const opciones = materiaSelect.getElementsByTagName('option');
            
            // Ocultar todas las opciones primero
            for (let opcion of opciones) {
                if (opcion.value === '') continue;
                opcion.style.display = 'none';
            }
            
            // Mostrar solo las materias del grupo seleccionado
            for (let opcion of opciones) {
                if (opcion.value === '') continue;
                const grupos = JSON.parse(opcion.getAttribute('data-grupos'));
                if (grupos.includes(parseInt(grupoId))) {
                    opcion.style.display = '';
                }
            }
            
            // Seleccionar la primera materia visible
            for (let opcion of opciones) {
                if (opcion.style.display !== 'none') {
                    materiaSelect.value = opcion.value;
                    break;
                }
            }
        }

        function filtrarMateriasEditar(grupoId) {
            const materiaSelect = document.getElementById('edit_materia');
            if (!materiaSelect) return;

            const opciones = materiaSelect.getElementsByTagName('option');
            
            // Ocultar todas las opciones primero
            for (let opcion of opciones) {
                if (opcion.value === '') continue;
                opcion.style.display = 'none';
            }
            
            // Mostrar solo las materias del grupo seleccionado
            for (let opcion of opciones) {
                if (opcion.value === '') continue;
                const grupos = JSON.parse(opcion.getAttribute('data-grupos'));
                if (grupos.includes(parseInt(grupoId))) {
                    opcion.style.display = '';
                }
            }
            
            // Seleccionar la primera materia visible
            for (let opcion of opciones) {
                if (opcion.style.display !== 'none') {
                    materiaSelect.value = opcion.value;
                    break;
                }
            }
        }

        function prepareEditModal(id, titulo, contenido, grupo_id, materia_id, fecha_expiracion, es_global) {
            const form = document.getElementById('edit-announcement-form');
            if (!form) return;

            form.action = `/anuncios/${id}/update`;
            
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_contenido').value = contenido;
            if (fecha_expiracion && fecha_expiracion !== 'null') {
                document.getElementById('edit_fecha_expiracion').value = fecha_expiracion;
            } else {
                document.getElementById('edit_fecha_expiracion').value = '';
            }
            
            // Establecer el checkbox de anuncio global
            document.getElementById('edit_es_global').checked = es_global;
            
            // Establecer grupo y materia
            if (grupo_id) {
                document.getElementById('edit_grupo').value = grupo_id;
                filtrarMateriasEditar(grupo_id);
                if (materia_id) {
                    document.getElementById('edit_materia').value = materia_id;
                }
            }
        }

        // Función para mostrar/ocultar campos según el tipo de anuncio
        function toggleAnuncioGlobal(isGlobal, containerId) {
            console.log('toggleAnuncioGlobal called:', isGlobal, containerId);
            const grupoMateriaContainer = document.querySelector(containerId);
            console.log('Container found:', grupoMateriaContainer);
            if (grupoMateriaContainer) {
                grupoMateriaContainer.style.display = isGlobal ? 'none' : 'grid';
                console.log('Display changed to:', grupoMateriaContainer.style.display);
            }
        }

        // Agregar event listeners para los checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event fired');
            const esGlobalCheckbox = document.getElementById('es_global');
            const editEsGlobalCheckbox = document.getElementById('edit_es_global');
            
            console.log('Checkboxes found:', { esGlobalCheckbox, editEsGlobalCheckbox });
            
            if (esGlobalCheckbox) {
                console.log('Adding event listener to es_global checkbox');
                esGlobalCheckbox.addEventListener('change', function() {
                    console.log('es_global checkbox changed:', this.checked);
                    toggleAnuncioGlobal(this.checked, '#grupo-materia-new');
                });
            }
            
            if (editEsGlobalCheckbox) {
                console.log('Adding event listener to edit_es_global checkbox');
                editEsGlobalCheckbox.addEventListener('change', function() {
                    console.log('edit_es_global checkbox changed:', this.checked);
                    toggleAnuncioGlobal(this.checked, '#grupo-materia-edit');
                });
            }
        });

        // Función para inicializar checkboxes cuando se abren los modales
        function initializeCheckboxes() {
            const esGlobalCheckbox = document.getElementById('es_global');
            const editEsGlobalCheckbox = document.getElementById('edit_es_global');
            
            if (esGlobalCheckbox && !esGlobalCheckbox.hasAttribute('data-initialized')) {
                console.log('Initializing es_global checkbox');
                esGlobalCheckbox.setAttribute('data-initialized', 'true');
                esGlobalCheckbox.addEventListener('change', function() {
                    console.log('es_global checkbox changed:', this.checked);
                    toggleAnuncioGlobal(this.checked, '#grupo-materia-new');
                });
            }
            
            if (editEsGlobalCheckbox && !editEsGlobalCheckbox.hasAttribute('data-initialized')) {
                console.log('Initializing edit_es_global checkbox');
                editEsGlobalCheckbox.setAttribute('data-initialized', 'true');
                editEsGlobalCheckbox.addEventListener('change', function() {
                    console.log('edit_es_global checkbox changed:', this.checked);
                    toggleAnuncioGlobal(this.checked, '#grupo-materia-edit');
                });
            }
        }

        // Observar cambios en el DOM para detectar cuando se abren los modales
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    initializeCheckboxes();
                }
            });
        });

        // Observar cambios en el body
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        document.addEventListener('DOMContentLoaded', iniciarComponentes);
        document.addEventListener('livewire:navigated', iniciarComponentes);
        
        // Mostrar toast si existe mensaje
        @if(session('toast'))
            Toastify({
                text: "{{ session('toast.message') }}",
                duration: 3500,
                gravity: "top",
                position: "right",
                backgroundColor: 
                    @if(session('toast.type') == 'success') "#22c55e"
                    @elseif(session('toast.type') == 'error') "#ef4444"
                    @elseif(session('toast.type') == 'warning') "#f59e42"
                    @else "#3b82f6" @endif,
                stopOnFocus: true,
                close: true
            }).showToast();
        @endif
    </script>
</x-layouts.app>

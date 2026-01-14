<x-layouts.app :title="__('Circulares')">
    <!-- Librerías para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Circulares') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona las circulares del sistema</p>
            </div>
            <flux:modal.trigger name="new-circular">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nueva Circular</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

            <!-- Filtros -->
        <div class="bg-indigo-100 dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-300 dark:border-gray-600">
            <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-start sm:items-center">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap">Filtrar por sección:</label>
                    <flux:select name="filtroSeccion" id="filtroSeccion" class="w-full sm:w-48">
                            <option value="">Todas las secciones</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                    </flux:select>
                    </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap">Estado:</label>
                    <flux:select name="filtroEstado" id="filtroEstado" class="w-full sm:w-48">
                            <option value="">Todos</option>
                            <option value="activas">Activas</option>
                            <option value="expiradas">Expiradas</option>
                    </flux:select>
                </div>
                </div>
            </div>

        <div class="overflow-x-auto rounded-lg">
            <table id="myTable" class="w-full min-w-full">
                <thead class="bg-indigo-200 dark:bg-gray-600">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Título</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden md:table-cell">Archivo</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden lg:table-cell">Alcance</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden xl:table-cell">Fecha</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden xl:table-cell">Expira</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                            @if(isset($circulares) && count($circulares) > 0)
                                @foreach ($circulares as $circular)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors circular-row" 
                                        data-seccion="{{ $circular->seccion }}" 
                                        data-estado="{{ $circular->estaActiva() ? 'activas' : 'expiradas' }}">
                                        <td class="px-3 sm:px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="ml-2 sm:ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $circular->titulo }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $circular->seccion }}
                                                    </div>
                                                    <!-- Información adicional para móviles -->
                                                    <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        @if($circular->archivo)
                                                            <div class="mt-1">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                                                    📄 {{ $circular->nombre_archivo_original }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        @if($circular->es_global)
                                                            <div class="mt-1">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                                    Global
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="mt-1">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                                    {{ $circular->grupo->nombre ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>{{ $circular->created_at->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
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
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                            @if($circular->es_global)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    Global
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    {{ $circular->grupo->nombre ?? 'N/A' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $circular->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $circular->user->name ?? 'Usuario no disponible' }}
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                                                @if($circular->fecha_expiracion)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($circular->fecha_expiracion->isPast()) 
                                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @elseif($circular->fecha_expiracion->isToday()) 
                                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @else 
                                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @endif">
                                                        {{ $circular->fecha_expiracion->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                <span class="text-gray-500 dark:text-gray-400">Sin expiración</span>
                                                @endif
                                        </td>
                                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                @if(auth()->user()->id === $circular->usuario_id || auth()->user()->rol === 'administrador')
                                                    <flux:modal.trigger name="edit-circular">
                                                        <button type="button"
                                                            onclick="prepareEditCircular({{ $circular->id }}, '{{ addslashes($circular->titulo) }}', '{{ $circular->grupo_id }}', '{{ $circular->seccion }}', '{{ $circular->fecha_expiracion ? $circular->fecha_expiracion->format('Y-m-d') : 'null' }}', {{ $circular->es_global ? 'true' : 'false' }})"
                                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors p-1">
                                                            <flux:icon name="pencil" class="w-4 h-4" />
                                                    </button>
                                                    </flux:modal.trigger>
                                                    <form action="{{ route('circulares.destroy', $circular->id) }}" method="POST" class="form-eliminar inline">
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
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No hay circulares disponibles
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Modal para nueva circular -->
    <flux:modal name="new-circular" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Nueva Circular</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('circulares.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="titulo" label="Título de la Circular" type="text"
                        placeholder="Ej: Circular Semanal del 1-5 de Septiembre" required />
                    <flux:input name="archivo" id="archivo" label="Archivo de la Circular" type="file" 
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required />
                    
                    <!-- Checkbox para circular global -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="es_global" id="es_global" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="es_global" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Circular Global (visible para todos los grupos)
                        </label>
                    </div>

                    <div id="grupo-seccion-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select name="grupo_id" id="grupo_id" label="Grupo" required>
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                        </flux:select>

                        <flux:select name="seccion" id="seccion" label="Sección" required>
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                        </flux:select>
                    </div>

                    <flux:input name="fecha_expiracion" id="fecha_expiracion" label="Fecha de Expiración (opcional)" type="date" 
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled" class="dark:text-white">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Subir Circular</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal para editar circular -->
    <flux:modal name="edit-circular" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Editar Circular</flux:heading>
            </div>
            <flux:separator />

            <form id="editCircularForm" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid gap-4">
                    <flux:input name="titulo" id="edit_titulo" label="Título de la Circular" type="text" required />
                    <flux:input name="archivo" id="edit_archivo" label="Archivo de la Circular" type="file" 
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                    
                    <!-- Checkbox para circular global -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="es_global" id="edit_es_global" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="edit_es_global" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Circular Global (visible para todos los grupos)
                        </label>
                    </div>

                    <div id="edit-grupo-seccion-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select name="grupo_id" id="edit_grupo_id" label="Grupo" required>
                                <option value="">Selecciona un grupo</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }} - {{ $grupo->seccion }}</option>
                                    @endforeach
                                @endif
                        </flux:select>

                        <flux:select name="seccion" id="edit_seccion" label="Sección" required>
                                <option value="">Selecciona una sección</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                        </flux:select>
                    </div>

                    <flux:input name="fecha_expiracion" id="edit_fecha_expiracion" label="Fecha de Expiración (opcional)" type="date" 
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled" class="dark:text-white">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Actualizar Circular</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <script>
        // Flag global para evitar inicializaciones duplicadas
        let componentesInicializados = false;
        
        function iniciarComponentes() {
            // Evitar inicialización duplicada
            if (componentesInicializados) {
                return;
            }
            componentesInicializados = true;
            
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
        function prepareEditCircular(id, titulo, grupoId, seccion, fechaExpiracion, esGlobal) {
            console.log('Preparando edición de circular:', {
                id, titulo, grupoId, seccion, fechaExpiracion, esGlobal
            });
            
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_es_global').checked = esGlobal === 'true';
            document.getElementById('edit_grupo_id').value = grupoId;
            document.getElementById('edit_seccion').value = seccion;
            
            // Manejar la fecha de expiración
            const fechaExpiracionValue = fechaExpiracion !== 'null' ? fechaExpiracion : '';
            document.getElementById('edit_fecha_expiracion').value = fechaExpiracionValue;
            console.log('Fecha de expiración asignada:', fechaExpiracionValue);
            
            // Actualizar la acción del formulario
            document.getElementById('editCircularForm').action = `/circulares/${id}`;
            
            // Aplicar la lógica de mostrar/ocultar campos según si es global
            toggleCircularGlobal(esGlobal === 'true', 'edit-grupo-seccion-container');
            
            // Abrir el modal
            openModal('edit-circular');
        }


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
            iniciarComponentes();
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
                    const fechaExpiracion = document.getElementById('edit_fecha_expiracion').value;
                    
                    console.log('Datos del formulario de edición:', {
                        esGlobal, grupoId, seccion, fechaExpiracion
                    });
                    
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
        
        // Inicializar componentes también en navegación de Livewire
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

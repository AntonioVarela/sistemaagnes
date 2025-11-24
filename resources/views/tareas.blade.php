<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl  p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tareas') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona las tareas y actividades escolares</p>
            </div>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nueva Tarea</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <!-- Filtros -->
        <div class="bg-indigo-100 dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-300 dark:border-gray-600">
            <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-start sm:items-center">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap">Filtrar por grupo:</label>
                    <flux:select name="grupo_filter" class="w-full sm:w-48" onchange="window.location.href=this.value">
                        <option value="{{ route('tareas.index') }}">Todos los grupos</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ route('tareas.index') }}?grupo_filter={{ $grupo->id }}" 
                                    {{ request('grupo_filter') == $grupo->id ? 'selected' : '' }}>
                                {{ $grupo->nombre }} - {{ $grupo->seccion }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap">Filtrar por materia:</label>
                    <flux:select name="materia_filter" class="w-full sm:w-48" onchange="window.location.href=this.value">
                        <option value="{{ route('tareas.index') }}">Todas las materias</option>
                        @foreach($materias as $materia)
                            <option value="{{ route('tareas.index') }}?materia_filter={{ $materia->id }}" 
                                    {{ request('materia_filter') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
                @if(request('grupo_filter') || request('materia_filter'))
                    <a href="{{ route('tareas.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar filtros
                    </a>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto rounded-lg">
            <table id="myTable" class="w-full min-w-full">
                <thead class="bg-indigo-200 dark:bg-gray-600">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Tarea</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden sm:table-cell">Fecha entrega</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden md:table-cell">Grupo</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden lg:table-cell">Descripción</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                    @forelse ($tareas as $tarea)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    
                                    <div class="ml-2 sm:ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tarea->titulo }}</div>
                                        <!-- Información adicional para móviles -->
                                        <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <div>{{ \Carbon\Carbon::parse($tarea->fecha_entrega)->format('d/m/Y') }}</div>
                                            @if($tarea->hora_entrega)
                                                <div>{{ \Carbon\Carbon::parse($tarea->hora_entrega)->format('H:i') }}</div>
                                            @endif
                                            @foreach ($grupos as $grupo)
                                                @if ($tarea->grupo == $grupo->id)
                                                    <div class="mt-1">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                            {{ $grupo->nombre }} {{ $grupo->seccion }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($tarea->fecha_entrega)->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tarea->hora_entrega ? \Carbon\Carbon::parse($tarea->hora_entrega)->format('H:i') : '' }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                @foreach ($grupos as $grupo)
                                    @if ($tarea->grupo == $grupo->id)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            {{ $grupo->nombre }} {{ $grupo->seccion }}
                                        </span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                <div class="text-sm text-gray-900 dark:text-white line-clamp-2">{!! $tarea->descripcion !!}</div>
                                @if($tarea->archivo)
                                    <div class="mt-2">
                                        @php
                                            $url = Storage::disk('s3')->url($tarea->archivo);
                                        @endphp
                                        <a href="{{ $url }}" 
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
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    <flux:modal.trigger name="edit-task">
                                        <button type="button"
                                            onclick="prepareEditModal({{ $tarea->id }}, `{{ addslashes($tarea->descripcion) }}`, '{{ $tarea->fecha_entrega }}', '{{ $tarea->hora_entrega }}', '{{ $tarea->grupo }}', '{{ $tarea->materia }}')"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors p-1">
                                            <flux:icon name="pencil" class="w-4 h-4" />
                                        </button>
                                    </flux:modal.trigger>
                                    <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" class="form-eliminar inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors p-1">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No hay tareas disponibles</p>
                                    <p class="text-sm">Las tareas aparecerán aquí cuando estén disponibles.</p>
                                    @if(!request('grupo_filter') && !request('materia_filter'))
                                        <p class="text-xs mt-2 text-gray-400">Nota: Solo se muestran tareas de las últimas 2 semanas.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de nueva tarea -->
    <flux:modal name="edit-profile" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Nueva Tarea</flux:heading>
            </div>
            <flux:separator class="dark:border-gray-700" />

            <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="form-nueva-tarea">
                @csrf
                <div class="grid gap-4">
                    <div class="dark:text-white">
                        <flux:heading size="lg" class="dark:text-white">Descripción</flux:heading>
                        <div class="quill-container">
                            <div id="editor-nueva" style="height: 156px;" class="dark:text-white">
                                
                            </div>
                        </div>
                    </div>

                    <textarea name="descripcion" class="hidden" id="descripcion" required></textarea>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="fecha_entrega" id="fecha_entrega" label="Fecha de Entrega *" type="date" required 
                            x-bind:min="new Date().toISOString().split('T')[0]" class="dark:text-white" />
                        <div id="hora_entrega_container">
                            <flux:input name="hora_entrega" type="time" id='hora_entrega' label='Hora de entrega (opcional)' class="dark:text-white" />
                        </div>
                    </div>
                    @if ($grupos->count() > 1)
                        <div class="grid grid-cols-2 gap-4">
                            <flux:select name="grupo" id="grupo" label="Grupo *" onchange="filtrarMaterias(this.value)" required class="dark:text-white">
                                <option value="">Selecciona un grupo</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia" id="materia" label="Materia *" required class="dark:text-white">
                                <option value="">Selecciona una materia</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}" data-grupos="{{ json_encode($materia->grupos->pluck('id')) }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                    <flux:input name="archivo" type="file" id="archivo" label="Archivo adjunto (opcional)" accept=".pdf" class="dark:text-white">
                    </flux:input>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-profile')" class="dark:text-white">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" id="btn-guardar-tarea">Guardar tarea</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-task" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Editar Tarea</flux:heading>
            </div>
            <flux:separator class="dark:border-gray-700" />

            <form id="edit-task-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <div class="dark:text-white">
                        <div class="quill-container">
                            <div id="editor-editar" style="height: 156px;" class="dark:text-white">
                            </div>
                        </div>
                    </div>
                    <textarea name="descripcion" id="edit_descripcion" class="hidden" required></textarea>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="fecha_entrega" id="edit_fecha_entrega" label="Fecha de Entrega *" type="date" required 
                            x-bind:min="new Date().toISOString().split('T')[0]" class="dark:text-white"/>
                        <div id="edit_hora_entrega_container">
                            <flux:input name="hora_entrega" type="time" id='edit_hora_entrega' label='Hora de entrega (opcional)' class="dark:text-white" />
                        </div>
                    </div>
                    @if ($grupos->count() > 1)
                        <div class="grid grid-cols-2 gap-4">
                            <flux:select name="grupo" id="edit_grupo" label="Grupo *" onchange="filtrarMateriasEditar(this.value)" required class="dark:text-white">
                                <option value="">Selecciona un grupo</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia" id="edit_materia" label="Materia *" required class="dark:text-white">
                                <option value="">Selecciona una materia</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}" data-grupos="{{ json_encode($materia->grupos->pluck('id')) }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                    <flux:input name="archivo" type="file" id="edit_archivo" label="Archivo adjunto (opcional)" accept=".pdf" class="dark:text-white">
                    </flux:input>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-task')" class="dark:text-white">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar tarea</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <style>
        /* Estilos para mejorar la compatibilidad entre Flux UI y DataTables */
        .dataTables_wrapper {
            position: relative;
            z-index: 1;
        }
        
        .dataTables_filter,
        .dataTables_length {
            margin-bottom: 1rem;
        }
        
        .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .dataTables_paginate {
            margin-top: 1rem;
        }
        
        .dataTables_paginate .paginate_button {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            color: #374151;
            background: white;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        /* Asegurar que el sidebar tenga mayor z-index */
        [data-flux-sidebar] {
            z-index: 50 !important;
        }
        
        /* Mejorar la responsividad de la tabla */
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                margin-bottom: 0.5rem;
            }
            
            .dataTables_wrapper .dataTables_paginate {
                text-align: center;
            }
            
            .dataTables_wrapper .dataTables_info {
                text-align: center;
                margin-top: 0.5rem;
            }
        }
        
        /* Estilos para el modo oscuro */
        .dark .dataTables_filter input,
        .dark .dataTables_length select {
            background-color: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        
        .dark .dataTables_paginate .paginate_button {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        
        .dark .dataTables_paginate .paginate_button:hover {
            background: #4b5563;
            border-color: #6b7280;
        }
    </style>
    
    <script>
        // Flag global para evitar inicializaciones duplicadas
        let componentesInicializados = false;
        
        function iniciarComponentes() {
            // Inicializar DataTables primero, independientemente del flag
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                // Verificar que el elemento exista antes de manipularlo
                // Si no existe, simplemente salir (es normal en vistas que no usan tabla)
                const tableElement = document.getElementById('myTable');
                if (!tableElement) {
                    // No mostrar warning, es normal que no exista en otras vistas
                    return;
                }
                
                // Destruir DataTable si ya existe de forma segura
                try {
                    if ($.fn.DataTable.isDataTable('#myTable')) {
                        $('#myTable').DataTable().destroy();
                    }
                } catch (e) {
                    console.warn('Error al destruir DataTable:', e);
                }
                
                // Inicializar DataTable con un pequeño delay
                setTimeout(function() {
                    // Verificar nuevamente que el elemento exista
                    const table = document.getElementById('myTable');
                    if (!table) {
                        // No mostrar warning, simplemente salir
                        return;
                    }
                    
                    if (!$.fn.DataTable.isDataTable('#myTable')) {
                        try {
                            $('#myTable').DataTable({
                                language: {
                                    url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                                },
                                responsive: true,
                                pageLength: 10,
                                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                                dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4"lf>rt<"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 gap-4"ip>',
                                order: [[1, 'asc']],
                                columnDefs: [
                                    { orderable: false, targets: -1 },
                                    { responsivePriority: 1, targets: 0 },
                                    { responsivePriority: 2, targets: -1 },
                                    { responsivePriority: 3, targets: 1 },
                                    { responsivePriority: 4, targets: 2 },
                                    { responsivePriority: 5, targets: 3 }
                                ],
                                scrollX: true,
                                autoWidth: false,
                                processing: true,
                                stateSave: false,
                                destroy: true
                            });
                        } catch (e) {
                            console.error('Error al inicializar DataTable:', e);
                        }
                    }
                }, 150);
            }
            
            // Solo evitar duplicación de event listeners, no de inicialización completa
            if (componentesInicializados) {
                // Reinicializar solo los componentes necesarios sin duplicar event listeners
                reinicializarComponentes();
                return;
            }
            componentesInicializados = true;
            // Configuración del editor Quill para nueva tarea
            const editorNueva = document.getElementById('editor-nueva');
            if (editorNueva && !document.querySelector('#editor-nueva .ql-editor')) {
                var quillNueva = new Quill('#editor-nueva', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'direction': 'rtl' }],
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'font': [] }],
                            [{ 'align': [] }],
                            ['clean']
                        ]
                    }
                });

                // Eventos para actualizar el contenido del textarea
                quillNueva.on('text-change', function() {
                    document.getElementById('descripcion').value = quillNueva.root.innerHTML;
                });
            }

            // Configuración del editor Quill para editar tarea
            const editorEditar = document.getElementById('editor-editar');
            if (editorEditar && !document.querySelector('#editor-editar .ql-editor')) {
                var quillEditar = new Quill('#editor-editar', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'direction': 'rtl' }],
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'font': [] }],
                            [{ 'align': [] }],
                            ['clean']
                        ]
                    }
                });

                quillEditar.on('text-change', function() {
                    document.getElementById('edit_descripcion').value = quillEditar.root.innerHTML;
                });
            }

            // Manejo del formulario de nueva tarea
            const formNuevaTarea = document.getElementById('form-nueva-tarea');
            if (formNuevaTarea) {
                formNuevaTarea.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validar que el editor tenga contenido
                    if (quillNueva.root.innerHTML === '<p><br></p>') {
                        Toastify({
                            text: "Por favor, ingresa una descripción para la tarea",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                            close: true
                        }).showToast();
                        return false;
                    }

                    // Validar campos requeridos
                    const fechaEntrega = document.getElementById('fecha_entrega');
                    const grupo = document.getElementById('grupo');
                    const materia = document.getElementById('materia');

                    if (!fechaEntrega.value) {
                        Toastify({
                            text: "Por favor, selecciona una fecha de entrega",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                            close: true
                        }).showToast();
                        return false;
                    }

                    if (grupo && !grupo.value) {
                        Toastify({
                            text: "Por favor, selecciona un grupo",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                            close: true
                        }).showToast();
                        return false;
                    }

                    if (materia && !materia.value) {
                        Toastify({
                            text: "Por favor, selecciona una materia",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                            close: true
                        }).showToast();
                        return false;
                    }

                    // Si todo está bien, enviar el formulario
                    this.submit();
                });
            }
            
            // DataTables ya se inicializa al principio de iniciarComponentes()

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
        }

        function reinicializarComponentes() {
            // Solo reinicializar componentes que necesitan ser actualizados
            // sin duplicar event listeners
            
            // Reinicializar DataTable si es necesario
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                // Verificar que el elemento exista
                // Si no existe, simplemente salir (es normal en vistas que no usan tabla)
                const tableElement = document.getElementById('myTable');
                if (!tableElement) {
                    return;
                }
                
                // Destruir DataTable si ya existe de forma segura
                try {
                    if ($.fn.DataTable.isDataTable('#myTable')) {
                        $('#myTable').DataTable().destroy();
                    }
                } catch (e) {
                    console.warn('Error al destruir DataTable en reinicializar:', e);
                }
                
                // Reinicializar con un pequeño delay para asegurar que el DOM esté listo
                setTimeout(function() {
                    // Verificar nuevamente que el elemento exista
                    const table = document.getElementById('myTable');
                    if (!table) {
                        // No mostrar warning, simplemente salir
                        return;
                    }
                    
                    try {
                        $('#myTable').DataTable({
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                            },
                            responsive: true,
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                            dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4"lf>rt<"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 gap-4"ip>',
                            order: [[1, 'asc']],
                            columnDefs: [
                                { orderable: false, targets: -1 },
                                { responsivePriority: 1, targets: 0 },
                                { responsivePriority: 2, targets: -1 },
                                { responsivePriority: 3, targets: 1 },
                                { responsivePriority: 4, targets: 2 },
                                { responsivePriority: 5, targets: 3 }
                            ],
                            scrollX: true,
                            autoWidth: false,
                            processing: true,
                            stateSave: false,
                            destroy: true
                        });
                    } catch (e) {
                        console.error('Error al reinicializar DataTable:', e);
                    }
                }, 150);
            }
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

        function prepareEditModal(id, descripcion, fecha_entrega, hora_entrega, grupo_id, materia_id) {
            const form = document.getElementById('edit-task-form');
            if (!form) return;

            form.action = `/tareas/${id}/update`;
            
            // Establecer el contenido del editor Quill
            const quillEditor = document.querySelector('#editor-editar .ql-editor');
            if (quillEditor) {
                quillEditor.innerHTML = descripcion;
            }
            
            document.getElementById('edit_fecha_entrega').value = fecha_entrega;
            document.getElementById('edit_hora_entrega').value = hora_entrega;
            
            // Establecer grupo y materia
            if (grupo_id) {
                document.getElementById('edit_grupo').value = grupo_id;
                filtrarMateriasEditar(grupo_id);
                if (materia_id) {
                    document.getElementById('edit_materia').value = materia_id;
                }
            }
        
        }

        document.addEventListener('DOMContentLoaded', iniciarComponentes);
        document.addEventListener('livewire:navigated', iniciarComponentes);
       
    </script>
</x-layouts.app>

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
        <div class="overflow-hidden rounded-lg">
            <table id="myTable" class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tarea</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha entrega</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class=" divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($tareas as $tarea)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tarea->titulo }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $tarea->fecha_entrega }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tarea->hora_entrega }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach ($grupos as $grupo)
                                    @if ($tarea->grupo == $grupo->id)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            {{ $grupo->nombre }} {{ $grupo->seccion }}
                                        </span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white line-clamp-2">{!! $tarea->descripcion !!}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:modal.trigger name="edit-task">
                                        <flux:button icon='pencil' variant="filled" 
                                            onclick="prepareEditModal({{ $tarea->id }}, `{{ addslashes($tarea->descripcion) }}`, '{{ $tarea->fecha_entrega }}', '{{ $tarea->hora_entrega }}', '{{ $tarea->grupo }}', '{{ $tarea->materia }}')" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Editar
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" class="form-eliminar inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
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
                <flux:heading size="lg" class="dark:text-white">Editar Tarea</flux:heading>
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
    <script>
        
        function iniciarComponentes() {
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
            // Configuración de DataTable
            if ($.fn.DataTable) {
                // Verificar si la tabla ya está inicializada
                if (!$.fn.DataTable.isDataTable('#myTable')) {
                    $('#myTable').DataTable({
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                        },
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                        order: [[1, 'asc']],
                        columnDefs: [
                            { orderable: false, targets: -1 }
                        ]
                    });
                }
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

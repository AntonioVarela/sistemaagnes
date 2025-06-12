<x-layouts.app :title="__('Anuncios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
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

        <div class="rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table id="myTable" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contenido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($anuncios as $anuncio)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $anuncio->titulo }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Creado por: {{ $anuncio->user->name ?? 'Usuario no disponible' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ Str::limit($anuncio->contenido, 2000) }}
                                    </div>
                                    @if($anuncio->archivo)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $anuncio->archivo) }}" 
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $anuncio->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        @if(auth()->user()->id === $anuncio->user_id)
                                            <flux:modal.trigger name="edit-announcement">
                                                <flux:button icon='pencil' variant="filled" 
                                                    onclick="prepareEditModal({{ $anuncio->id }}, '{{ $anuncio->titulo }}', '{{ $anuncio->contenido }}', '{{ $anuncio->grupo_id }}', '{{ $anuncio->materia_id }}')" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    Editar
                                                </flux:button>
                                            </flux:modal.trigger>
                                            <form action="{{ route('anuncios.destroy', $anuncio->id) }}" method="POST" class="form-eliminar inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                                    <flux:icon name="trash" />
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Solo el creador puede editar</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de nuevo anuncio -->
    <flux:modal name="new-announcement" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Anuncio</flux:heading>
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
                    @if(count($horario) > 1)
                        <div class="grid grid-cols-2 gap-4">
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
                <flux:heading size="lg">Editar Anuncio</flux:heading>
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
                    @if(count($horario) > 1)
                        <div class="grid grid-cols-2 gap-4">
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
        function iniciarComponentes() {
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
                        order: [[2, 'desc']], // Ordenar por fecha descendente
                        columnDefs: [
                            { orderable: false, targets: -1 } // Deshabilitar ordenamiento en la columna de acciones
                        ]
                    });
                }
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

        function prepareEditModal(id, titulo, contenido, grupo_id, materia_id) {
            const form = document.getElementById('edit-announcement-form');
            if (!form) return;

            form.action = `/anuncios/${id}/update`;
            
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_contenido').value = contenido;
            
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

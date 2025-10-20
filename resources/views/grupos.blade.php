<x-layouts.app :title="__('Grupos')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Grupos') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los grupos del sistema</p>
            </div>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Grupo</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table id="myTable" class="w-full min-w-full">
                <thead class="bg-indigo-200 dark:bg-gray-600">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Nombre</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden sm:table-cell">Sección</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider hidden md:table-cell">Titular</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                    @foreach ($grupos as $grupo)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                        <flux:icon name="academic-cap" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="ml-2 sm:ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $grupo->nombre }}</div>
                                        <!-- Información adicional para móviles -->
                                        <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <div class="mt-1">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                                    {{ $grupo->seccion }}
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                @foreach ($usuarios as $usuario)
                                                    @if ($grupo->titular == $usuario->id)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                            {{ $usuario->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                    {{ $grupo->seccion }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    @foreach ($usuarios as $usuario)
                                        @if ($grupo->titular == $usuario->id)
                                            {{ $usuario->name }}
                                        @endif
                                    @endforeach
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    <flux:modal.trigger name="edit-task">
                                        <button type="button"
                                            onclick="prepareEditModal({{ $grupo->id }}, '{{ addslashes($grupo->nombre) }}', '{{ $grupo->seccion }}', {{ $grupo->titular }})"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors p-1">
                                            <flux:icon name="pencil" class="w-4 h-4" />
                                        </button>
                                    </flux:modal.trigger>
                                    <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" class="form-eliminar inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors p-1">
                                            <flux:icon name="trash" class="w-4 h-4" />
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

    <!-- Modal de nuevo grupo -->
    <flux:modal name="edit-profile" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Nuevo Grupo</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('grupos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="nombre" id="nombre" label="Nombre del grupo" type="text"
                        placeholder="Ingresa el nombre del grupo" required />
                    <flux:select name="seccion" id="seccion" label="Sección">
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                    </flux:select>
                    <flux:select name="titular_id" id="titular_id" label="Titular">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-profile')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar grupo</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-task" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="dark:text-white">Editar Grupo</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-task-form" method="POST" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="nombre" id="edit_nombre" label="Nombre del grupo" type="text"
                        placeholder="Ingresa el nombre del grupo" required />
                    <flux:select name="seccion" id="edit_seccion" label="Sección">
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                    </flux:select>
                    <flux:select name="titular_id" id="edit_titular_id" label="Titular">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-task')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar grupo</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });

        function closeModal(modalName) {
            const modal = document.querySelector(`[data-modal="${modalName}"]`);
            if (modal) {
                modal.close();
            }
        }

        function prepareEditModal(id, nombre, seccion, titular) {
            const form = document.getElementById('edit-task-form');
            form.action = `/grupos/${id}/update`;
            
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_seccion').value = seccion;
            document.getElementById('edit_titular_id').value = titular;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.form-eliminar');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
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
        });

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

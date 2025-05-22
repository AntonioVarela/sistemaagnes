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

        <div class="rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table id="myTable" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sección</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Titular</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($grupos as $grupo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                            <flux:icon name="academic-cap" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $grupo->nombre }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                        {{ $grupo->seccion }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @foreach ($usuarios as $usuario)
                                            @if ($grupo->titular == $usuario->id)
                                                {{ $usuario->name }}
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="edit-task">
                                            <flux:button icon='pencil' variant="filled" 
                                                onclick="prepareEditModal({{ $grupo->id }}, '{{ $grupo->nombre }}', '{{ $grupo->seccion }}', {{ $grupo->titular }})" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </flux:button>
                                        </flux:modal.trigger>
                                        <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" class="inline">
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

    <!-- Modal de nuevo grupo -->
    <flux:modal name="edit-profile" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Grupo</flux:heading>
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
                <flux:heading size="lg">Editar Grupo</flux:heading>
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
    </script>
</x-layouts.app>

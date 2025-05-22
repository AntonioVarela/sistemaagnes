<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl bg-white dark:bg-gray-700 p-6 shadow-sm">
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
                <tbody class="bg-white dark:bg-gray-600 divide-y divide-gray-200 dark:divide-gray-700">
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
                                <div class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ $tarea->descripcion }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:modal.trigger name="edit-task">
                                        <flux:button icon='pencil' variant="filled" 
                                            onclick="prepareEditModal({{ $tarea->id }}, '{{ $tarea->descripcion }}', '{{ $tarea->fecha_entrega }}', '{{ $tarea->hora_entrega }}')" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Editar
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" class="inline">
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

            <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <flux:textarea name="descripcion" id="descripcion"
                        label='Descripción' rows="4"
                        placeholder="Describe la tarea" required />
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="fecha_entrega" id="fecha_entrega" label="Fecha de Entrega" type="date" required />
                        <flux:input name="hora_entrega" type="time" id='hora_entrega' label='Hora de entrega' />
                    </div>
                    @if ($seccion->count() > 1)
                        <div class="grid grid-cols-2 gap-4">
                            <flux:select name="grupo" id="grupo" label="Grupo">
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia" id="materia" label="Materia">
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                    <flux:input name="archivo" type="file" id="archivo" label="Archivo adjunto" accept=".pdf">
                    </flux:input>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-profile')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar tarea</flux:button>
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
                    <flux:textarea name="descripcion" id="edit_descripcion"
                        label='Descripción' rows="4"
                        placeholder="Describe la tarea" required />
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="fecha_entrega" id="edit_fecha_entrega" label="Fecha de Entrega" type="date" required />
                        <flux:input name="hora_entrega" type="time" id='edit_hora_entrega' label='Hora de entrega' />
                    </div>
                    <flux:input name="archivo" type="file" id="edit_archivo" label="Archivo adjunto" accept=".pdf">
                    </flux:input>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-task')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar tarea</flux:button>
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
                order: [[1, 'asc']],
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

        function prepareEditModal(id, descripcion, fecha_entrega, hora_entrega) {
            const form = document.getElementById('edit-task-form');
            form.action = `/tareas/${id}/update`;
            
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_fecha_entrega').value = fecha_entrega;
            document.getElementById('edit_hora_entrega').value = hora_entrega;
        }
    </script>
</x-layouts.app>

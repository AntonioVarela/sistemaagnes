<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Tareas') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>


            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Tareas</flux:heading>
                    <flux:separator />

                    <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:textarea name="descripcion" id="descripcion"
                            label='Descripción' rows="4"
                            placeholder="Describe la tarea" required />
                            <flux:input name="fecha_entrega" id="fecha_entrega" label="Fecha de Entrega" type="date"
                                required />
                            @if ($seccion->count() > 1)
                                <flux:select name="grupo" id="grupo" label="Grupo (opcional)">
                                    @foreach ($grupos as $grupo)
                                        <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:select name="materia" id="materia" label="Materia (opcional)">
                                    @foreach ($materias as $materia)
                                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                    @endforeach
                                </flux:select>
                            @endif
                            <flux:input name="archivo" type="file" id="archivo" label="Archivo (opcional)" accept=".pdf">
                            </flux:input>
                            <flux:input name="hora_entrega" type="time" id='hora_entrega' label='Hora de entrega (opcional)'
                                required />
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar tarea</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <!-- Modal de edición -->
            <flux:modal name="edit-task" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Editar Tarea</flux:heading>
                    <flux:separator />

                    <form id="edit-task-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4">
                            <flux:textarea name="descripcion" id="edit_descripcion"
                            label='Descripción' rows="4"
                            placeholder="Describe la tarea" required />
                            <flux:input name="fecha_entrega" id="edit_fecha_entrega" label="Fecha de Entrega" type="date"
                                required />
                           
                            <flux:input name="archivo" type="file" id="edit_archivo" label="Archivo (opcional)" accept=".pdf">
                            </flux:input>
                            <flux:input name="hora_entrega" type="time" id='edit_hora_entrega' label='Hora de entrega (opcional)'
                                required />
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Actualizar tarea</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha entrega</th>
                        <th>Grupo</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tareas as $tarea)
                        <tr>
                            <td>{{ $tarea->titulo }}</td>
                            <td>{{ $tarea->fecha_entrega }}</td>
                            @foreach ($grupos as $grupo)
                                @if ($tarea->grupo == $grupo->id)
                                    <td>{{ $grupo->nombre }}</td>
                                @endif
                            @endforeach
                            <td>{{ $tarea->descripcion }}</td>
                            <td>
                                <flux:modal.trigger name="edit-task">
                                    <flux:button icon='pencil' variant="filled" 
                                        onclick="prepareEditModal({{ $tarea->id }}, '{{ $tarea->descripcion }}', '{{ $tarea->fecha_entrega }}', '{{ $tarea->hora_entrega }}')" 
                                        class="ml-2"> Editar </flux:button>
                                </flux:modal.trigger>
                                <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                },
            });
        });

        function prepareEditModal(id, descripcion, fecha_entrega, hora_entrega) {
            const form = document.getElementById('edit-task-form');
            form.action = `/tareas/${id}/update`;
            
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_fecha_entrega').value = fecha_entrega;
            document.getElementById('edit_hora_entrega').value = hora_entrega;
        }
    </script>
</x-layouts.app>

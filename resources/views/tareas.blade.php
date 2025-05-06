<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Tareas') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>
            <flux:button icon='eye' variant="filled" name="edit-profile" :href="route('tareas.alumnos')"
                class="ml-2"> Vista del alumno </flux:button>

            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Tareas</flux:heading>
                    <flux:text class="mt-2">Haz cambios en tus detalles personales.</flux:text>

                    <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:input name="titulo" id="titulo" label="Título" type="text"
                                placeholder="Ingresa el título" required />
                            <flux:textarea name="descripcion" id="descripcion" label="Descripción" rows="4"
                                placeholder="Describe la tarea" required />
                                
                            <flux:input name="archivo" type="file" id="archivo" label="Archivo" />
                            <flux:input name="fecha_entrega" id="fecha_entrega" label="Fecha de Entrega" type="date"
                                required />
                                <flux:input name="hora_entrega" type="time" id='hora_entrega' label='Hora de entrega' required/>
                            <flux:select name="grupo" id="grupo" label="Grupo">
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia" id="materia" label="Materia">
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar Tarea</flux:button>
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
                    @foreach ($tareas as $tarea )
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
                                <flux:button icon='pencil' variant="filled" name="edit-profile"
                                    :href="route('tareas.edit', $tarea->id)" class="ml-2"> Editar </flux:button>
                                <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST"
                                    class="inline">
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
    </script>
</x-layouts.app>

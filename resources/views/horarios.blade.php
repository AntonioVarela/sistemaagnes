<x-layouts.app :title="__('usuarios')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('horarios') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>

            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Horarios</flux:heading>
                    <flux:text class="mt-2">Haz cambios en tus detalles personales.</flux:text>

                    <form action="{{ route('horarios.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:input name="hora_inicio" id="hora_inicio" label="Hora de Inicio" type="time"
                                placeholder="Ingresa la hora de inicio" required />
                            <flux:input name="hora_fin" id="hora_fin" label="Hora de Fin" type="time"
                                placeholder="Ingresa la hora de fin" required />

                            <!-- Selección de días de la semana -->
                            <flux:fieldset label="Días de la Semana" name="dias_semana">
                                <flux:checkbox name="dias[]" id="lunes" value="lunes" label="Lunes" />
                                <flux:checkbox name="dias[]" id="martes" value="martes" label="Martes" />
                                <flux:checkbox name="dias[]" id="miercoles" value="miercoles" label="Miércoles" />
                                <flux:checkbox name="dias[]" id="jueves" value="jueves" label="Jueves" />
                                <flux:checkbox name="dias[]" id="viernes" value="viernes" label="Viernes" />
                            </flux:fieldset>

                            <flux:select name="grupo_id" id="grupo_id" label="Grupo">
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="materia_id" id="materia_id" label="Materia">
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar Horario</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <!-- DataTable -->
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grupo</th>
                        <th>Horario</th>
                        <th>Maestro</th>
                        <th>Días de la Semana</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($horarios as $horario)
                        <tr>
                            @foreach ($materias as $materia)
                                @if ($horario->materia == $materia->id)
                                    <td>{{ $materia->nombre }}</td>
                                @endif
                            @endforeach
                            @foreach ($grupos as $grupo)
                                @if ($horario->grupo == $grupo->id)
                                    <td>{{ $grupo->nombre }}</td>
                                @endif
                            @endforeach
                            <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                            <td>
                                @foreach ( $materias as $materia)
                                    @if ($horario->materia == $materia->id)
                                        @foreach ($usuarios as $usuario)
                                            @if ($materia->maestro == $usuario->id)
                                                {{ $usuario->name }}
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </td>
                            <td>
                            {{$horario->dias}}
                                   
                            </td>
                            <td>
                                <flux:button icon='pencil' variant="filled" name="edit-profile"
                                    :href="route('grupos.edit', $horario->id)" class="ml-2"> Editar </flux:button>
                                <form action="{{ route('grupos.destroy', $horario->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" icon='trash' variant="filled" class="ml-2">Eliminar</flux:button>
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
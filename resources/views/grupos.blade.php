<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Grupos') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>
            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Grupos</flux:heading>
                    <flux:text class="mt-2">Haz cambios en tus detalles personales.</flux:text>

                    <form action="{{ route('grupos.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:input name="nombre" id="nombre" label="Nombre del grupo" type="text"
                                placeholder="Ingresa el nombre del grupo" required />
                                <flux:select name="seccion" id="seccion" label="Sección">
                                    <option value="primaria">Primaria</option>
                                    <option value="secundaria">Secundaria</option>
                                </flux:select>
                            <flux:select name="titular_id" id="titular_id" label="Titular">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar grupo</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Seccion</th>
                        <th>Titular</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupos as $grupo)
                        <tr>
                            <td>{{ $grupo->nombre }}</td>
                            <td>{{ $grupo->seccion }}</td>
                            @foreach ($usuarios as $usuario)
                                @if ($grupo->titular == $usuario->id)
                                    <td>{{ $usuario->name }}</td>
                                @endif
                            @endforeach
                            <td>
                                <flux:button icon='pencil' variant="filled" name="edit-profile"
                                    :href="route('grupos.edit', $grupo->id)" class="ml-2"> Editar </flux:button>
                                <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST"
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

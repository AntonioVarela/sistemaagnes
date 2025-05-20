<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Materias') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>


            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Materias</flux:heading>
                    <flux:text class="mt-2">Haz cambios en tus detalles personales.</flux:text>

                    <form action="{{ route('materias.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:input name="nombre" id="nombre" label="Nombre de la materia" type="text"
                                placeholder="Ingresa el nombre de la materia" required />
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar Materia</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <!-- DataTable -->
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materias as $materia)
                        <tr>
                            <td>{{ $materia->nombre }}</td>
                            <td>
                                <flux:button icon='pencil' variant="filled" name="edit-profile"
                                    :href="route('grupos.edit', $materia->id)" class="ml-2"> Editar </flux:button>
                                <form action="{{ route('grupos.destroy', $materia->id) }}" method="POST"
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
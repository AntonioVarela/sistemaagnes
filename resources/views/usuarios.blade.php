<x-layouts.app :title="__('usuarios')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Usuarios') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary">Agregar</flux:button>
            </flux:modal.trigger>

            <flux:modal name="edit-profile" class="md:w-96 p-6">
                <flux:container class="space-y-6">
                    <flux:heading size="lg">Usuarios</flux:heading>
                    <flux:text class="mt-2">Haz cambios en tus detalles personales.</flux:text>

                    <form action="{{ route('usuarios.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid gap-4">
                            <flux:input name="name" id="name" label="Nombre" type="text"
                                placeholder="Ingresa el nombre" required />
                            <flux:input name="email" id="email" label="Email" type="email"
                                placeholder="Ingresa el email" required />
                            <flux:input name="password" id="password" label="Contraseña" type="password"
                                placeholder="Ingresa la contraseña" required />
                            <flux:select name="rol" id="rol" label="Rol">
                                <option value="" disabled selected>Selecciona un rol</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Maestro">Maestro</option>
                                <option value="Coordinador">Coordinador</option>
                                
                            </flux:select>
                        </div>

                        <flux:footer class="flex justify-between">
                            <flux:button type="button" variant="filled">Cancelar</flux:button>
                            <flux:button type="submit" class="ml-3" variant="primary">Guardar Usuario</flux:button>
                        </flux:footer>
                    </form>
                </flux:container>
            </flux:modal>

            <!-- DataTable -->
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->rol }}</td>
                            <td>
                                <flux:button icon='pencil' variant="filled" name="edit-profile"
                                    :href="route('usuarios.edit', $usuario->id)" class="ml-2"> Editar </flux:button>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button icon='trash' variant="danger" type="submit">Eliminar</flux:button>
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


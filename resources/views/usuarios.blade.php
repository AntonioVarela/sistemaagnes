<x-layouts.app :title="__('Usuarios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Usuarios') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los usuarios del sistema</p>
            </div>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Usuario</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class=" rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table id="myTable" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class=" divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                            <flux:icon name="user" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $usuario->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $usuario->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        {{ $usuario->rol }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        @if($usuario->id === Auth::id())
                                            <flux:icon name="user" class="w-3 h-3 mr-1" />
                                            Tú
                                        @else
                                            Activo
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="edit-usuario">
                                            <flux:button icon='pencil' variant="filled" 
                                                onclick="prepareEditModal({{ $usuario->id }}, '{{ $usuario->name }}', '{{ $usuario->email }}', '{{ $usuario->rol }}')" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </flux:button>
                                        </flux:modal.trigger>
                                        @if($usuario->id !== Auth::id())
                                            <button onclick="confirmarEliminacion({{ $usuario->id }}, '{{ $usuario->name }}')" 
                                                    class="text-red-600 hover:text-red-900 transition-colors">
                                                <flux:icon name="trash" />
                                            </button>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="No puedes eliminar tu propia cuenta">
                                                <flux:icon name="lock" />
                                            </span>
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

    <!-- Modal de nuevo usuario -->
    <flux:modal name="edit-profile" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Usuario</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
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
                        <option value="Coordinador Primaria">Coordinador Primaria</option>
                        <option value="Coordinador Secundaria">Coordinador Secundaria</option>
                    </flux:select>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-profile')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar usuario</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-usuario" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Editar Usuario</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-usuario-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4">
                    <flux:input name="name" id="edit_name" label="Nombre" type="text"
                        placeholder="Ingresa el nombre" required />
                    <flux:input name="email" id="edit_email" label="Email" type="email"
                        placeholder="Ingresa el email" required />
                    <flux:input name="password" id="edit_password" label="Contraseña" type="password"
                        placeholder="Deja en blanco para mantener la actual" />
                    <flux:select name="rol" id="edit_rol" label="Rol">
                        <option value="Administrador">Administrador</option>
                        <option value="Maestro">Maestro</option>
                        <option value="Coordinador Primaria">Coordinador Primaria</option>
                        <option value="Coordinador Secundaria">Coordinador Secundaria</option>
                    </flux:select>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-usuario')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar usuario</flux:button>
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

        function prepareEditModal(id, name, email, rol) {
            const form = document.getElementById('edit-usuario-form');
            form.action = `/usuarios/${id}`;
            
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_rol').value = rol;
        }

        function confirmarEliminacion(id, nombre) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                html: `
                    <div class="text-left">
                        <p class="mb-3">¿Estás seguro de que quieres eliminar al usuario <strong>${nombre}</strong>?</p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Advertencia:</strong> Esta acción no se puede deshacer y se verificará que el usuario no tenga:
                                    </p>
                                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                                        <li>Horarios asignados</li>
                                        <li>Grupos como titular</li>
                                        <li>Anuncios o circulares publicados</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear y enviar el formulario de eliminación
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/usuarios/${id}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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


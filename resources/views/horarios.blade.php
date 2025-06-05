<x-layouts.app :title="__('Horarios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Horarios') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los horarios de las materias</p>
            </div>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Horario</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($horarios as $horario)
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $horario->materia->nombre }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-200 ">{{ $horario->grupo->nombre }} {{ $horario->grupo->seccion }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:modal.trigger name="edit-task">
                                <flux:button icon='pencil' variant="filled" 
                                    onclick="prepareEditModal({{ $horario->id }}, '{{ $horario->materia_id }}', '{{ $horario->grupo_id }}', '{{ $horario->maestro_id }}', '{{ $horario->dias }}', '{{ $horario->hora_inicio }}', '{{ $horario->hora_fin }}')" 
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </flux:button>
                            </flux:modal.trigger>
                            <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" class="form-eliminar inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                    <flux:icon name="trash" />
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-200">
                            <flux:icon name="user" class="w-4 h-4" />
                            @foreach ($usuarios as $usuario)    
                                @if ($usuario->id == $horario->maestro_id)
                                    <span>{{ $usuario->name }}</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-200">
                            <flux:icon name="clock" class="w-4 h-4" />
                            <span>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-200">
                            <flux:icon name="calendar" class="w-4 h-4" />
                            <span>{{ str_replace(',', ', ', $horario->dias) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de nuevo horario -->
    <flux:modal name="edit-profile" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Horario</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('horarios.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select name="grupo_id" id="grupo_id" label="Grupo">
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                            @endforeach
                        </flux:select>
                        <flux:select name="materia_id" id="materia_id" label="Materia">
                            @foreach ($materias as $materia)
                                <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:select name="maestro_id" id="maestro_id" label="Maestro">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </flux:select>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="hora_inicio" type="time" id='hora_inicio' label='Hora de inicio' required />
                        <flux:input name="hora_fin" type="time" id='hora_fin' label='Hora de fin' required />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Días de la semana</label>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Lunes" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Lunes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Martes" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Martes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Miércoles" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Miércoles</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Jueves" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Jueves</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Viernes" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Viernes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-profile')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar horario</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-task" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Editar Horario</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-task-form" method="POST" class="space-y-4">
                @csrf
                <div class="grid gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select name="grupo_id" id="edit_grupo_id" label="Grupo">
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nombre }} {{ $grupo->seccion}}</option>
                            @endforeach
                        </flux:select>
                        <flux:select name="materia_id" id="edit_materia_id" label="Materia">
                            @foreach ($materias as $materia)
                                <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:select name="maestro_id" id="edit_maestro_id" label="Maestro">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </flux:select>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input name="hora_inicio" type="time" id='edit_hora_inicio' label='Hora de inicio' required />
                        <flux:input name="hora_fin" type="time" id='edit_hora_fin' label='Hora de fin' required />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Días de la semana</label>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Lunes" class="edit-dias rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Lunes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Martes" class="edit-dias rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Martes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Miércoles" class="edit-dias rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Miércoles</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Jueves" class="edit-dias rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Jueves</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="dias[]" value="Viernes" class="edit-dias rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-600">Viernes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-task')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar horario</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function closeModal(modalName) {
            const modal = document.querySelector(`[data-modal="${modalName}"]`);
            if (modal) {
                modal.close();
            }
        }

        function prepareEditModal(id, materia_id, grupo_id, maestro_id, dias, hora_inicio, hora_fin) {
            const form = document.getElementById('edit-task-form');
            form.action = `/horarios/${id}/update`;
            
            document.getElementById('edit_materia_id').value = materia_id;
            document.getElementById('edit_grupo_id').value = grupo_id;
            document.getElementById('edit_maestro_id').value = maestro_id;
            document.getElementById('edit_hora_inicio').value = hora_inicio;
            document.getElementById('edit_hora_fin').value = hora_fin;

            // Limpiar todas las casillas de verificación
            document.querySelectorAll('.edit-dias').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Marcar los días seleccionados
            const diasArray = dias.split(',');
            diasArray.forEach(dia => {
                const checkbox = document.querySelector(`.edit-dias[value="${dia}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
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
    </script>
</x-layouts.app>

<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1>{{ __('Tareas') }}</h1>
        <div class=''>
            <flux:modal.trigger name="edit-profile">
                <flux:button icon='plus' variant="primary" >Agregar</flux:button>
            </flux:modal.trigger>
            <flux:button icon='eye' variant="filled" name="edit-profile"  :href="
            route('tareas.alumnos')" class="ml-2" > Vista del alumno </flux:button>

            <flux:modal name="edit-profile" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Tareas</flux:heading>
                        <flux:text class="mt-2">Make changes to your personal details.</flux:text>
                    </div>

                    <form action="{{ route('tareas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-gray-700">{{ __('Título') }}</label>
                            <input type="text" name="titulo" id="titulo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
        
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">{{ __('Descripción') }}</label>
                            <textarea name="descripcion" id="descripcion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                        </div>
        
                        <div>
                            <label for="archivo" class="block text-sm font-medium text-gray-700">{{ __('Archivo') }}</label>
                            <input type="file" name="archivo" id="archivo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
        
                        <div>
                            <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">{{ __('Fecha de Entrega') }}</label>
                            <input type="date" name="fecha_entrega" id="fecha_entrega" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
        
                        <div>
                            <label for="grupo" class="block text-sm font-medium text-gray-700">{{ __('Grupo') }}</label>
                            <select name="grupo" id="grupo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">{{ __('Seleccione un grupo') }}</option>
                                <option value="grupo1">{{ __('Grupo 1') }}</option>
                                <option value="grupo2">{{ __('Grupo 2') }}</option>
                                <option value="grupo3">{{ __('Grupo 3') }}</option>
                                <!-- Agrega más opciones según sea necesario -->
                            </select>
                        </div>
        
                        <div>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('Guardar Tarea') }}
                            </button>
                        </div>
                    </form>

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </div>
            </flux:modal>
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha entrega</th>
                        <th>Grupo</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Row 1</td>
                        <td>Row 1 Data 2</td>
                        <td>Row 1 Data 2</td>
                        <td>Row 1 Data 2</td>
                    </tr>
                    <tr>
                        <td>Row 2 Data 1</td>
                        <td>Row 2 Data 1</td>
                        <td>Row 2 Data 1</td>
                        <td>Row 2 Data 2</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json',
                },
            });
        });
    </script>
</x-layouts.app>


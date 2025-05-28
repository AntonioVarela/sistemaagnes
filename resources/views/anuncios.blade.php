<x-layouts.app :title="__('Anuncios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Anuncios') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los anuncios del sistema</p>
            </div>
            <flux:modal.trigger name="new-announcement">
                <flux:button icon='plus' variant="primary" class="flex items-center gap-2">
                    <span>Nuevo Anuncio</span>
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table id="myTable" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contenido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($anuncios as $anuncio)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $anuncio->titulo }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ Str::limit($anuncio->contenido, 2000) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $anuncio->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="edit-announcement">
                                            <flux:button icon='pencil' variant="filled" 
                                                onclick="prepareEditModal({{ $anuncio->id }}, '{{ $anuncio->titulo }}', '{{ $anuncio->contenido }}')" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </flux:button>
                                        </flux:modal.trigger>
                                        <form action="{{ route('anuncios.destroy', $anuncio->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
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
    </div>

    <!-- Modal de nuevo anuncio -->
    <flux:modal name="new-announcement" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Nuevo Anuncio</flux:heading>
            </div>
            <flux:separator />

            <form action="{{ route('anuncios.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="titulo" label="Título" type="text"
                        placeholder="Ingresa el título del anuncio" required />
                    <flux:textarea name="contenido" id="contenido" label="Contenido"
                        placeholder="Ingresa el contenido del anuncio" required />
                    <flux:input name="archivo" id="archivo" label="Archivo" type="file" />
                    @if(count($horario) > 1)
                        <flux:input name="materia_id" id="materia_id" label="Materia" type="text"
                            placeholder="Ingresa el id de la materia" required />
                        <flux:input name="grupo_id" id="grupo_id" label="Grupo" type="text"
                            placeholder="Ingresa el id del grupo" required />
                    @endif
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('new-announcement')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Guardar anuncio</flux:button>
                </flux:footer>
            </form>
        </flux:container>
    </flux:modal>

    <!-- Modal de edición -->
    <flux:modal name="edit-announcement" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Editar Anuncio</flux:heading>
            </div>
            <flux:separator />

            <form id="edit-announcement-form"  method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <flux:input name="titulo" id="edit_titulo" label="Título" type="text"
                        placeholder="Ingresa el título del anuncio" required />
                    <flux:textarea name="contenido" id="edit_contenido" label="Contenido"
                        placeholder="Ingresa el contenido del anuncio" required />
                    <flux:input name="archivo" id="edit_archivo" label="Archivo" type="file" />
                </div>

                <flux:footer class="flex justify-end gap-3">
                    <flux:button type="button" variant="filled" onclick="closeModal('edit-announcement')">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Actualizar anuncio</flux:button>
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
                order: [[2, 'desc']],
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

        function prepareEditModal(id, titulo, contenido) {
            const form = document.getElementById('edit-announcement-form');
            form.action = `/anuncios/${id}/update`;
            
            document.getElementById('edit_titulo').value = titulo;
            document.getElementById('edit_contenido').value = contenido;
            document.getElementById('edit_archivo').value = archivo;
        }
    </script>
</x-layouts.app>

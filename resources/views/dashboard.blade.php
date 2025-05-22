<x-layouts.app :title="__('Dashboard')">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('Dashboard') }}
            </h1>
            <div class="flex space-x-4">
                <flux:button icon='plus' variant="filled" name="new-group" :href="route('grupos.index')"
                    class="bg-green-600 hover:bg-green-700 text-sm sm:text-base">Nuevo Grupo</flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach ($horario as $horario)
                @foreach ($grupos as $grupo)
                    @if ($horario->grupo_id == $grupo->id)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $grupo->nombre }}
                                    </h2>
                                    <span class="px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded-full self-start sm:self-auto">
                                        {{ $grupo->seccion }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 sm:space-y-3">
                                    <div class="flex items-center text-sm sm:text-base text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Horario: {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm sm:text-base text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Días: {{ $horario->dias }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 sm:mt-6 flex flex-col justify-end gap-2 sm:gap-3">
                                    <flux:button icon='eye' variant="filled" name="view-students" 
                                        :href="route('tareas.alumnos', $grupo->id)"
                                        class="bg-blue-600 hover:bg-blue-700 text-sm sm:text-base w-full sm:w-auto">
                                        Ver Tareas
                                    </flux:button>
                                    <flux:button icon='pencil' variant="filled" name="edit-group" 
                                        :href="route('tareas.index')"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-sm sm:text-base w-full sm:w-auto">
                                        Editar Tareas
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</x-layouts.app>

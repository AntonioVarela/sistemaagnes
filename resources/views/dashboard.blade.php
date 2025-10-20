<x-layouts.app :title="__('Dashboard')">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('Dashboard') }}
            </h1>
            <div class="flex space-x-4">
                <flux:button icon='plus' variant="filled" name="new-group" :href="route('grupos.index')"
                    class="bg-green-600 hover:bg-green-700 text-sm sm:text-base">Nuevo Grupo</flux:button>
                <flux:button icon='document-text' variant="filled" name="circulares" :href="route('circulares.index')"
                    class="bg-blue-600 hover:bg-blue-700 text-sm sm:text-base">Circulares</flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        @php
            // Agrupar por secciones disponibles (A y B)
            $gruposSeccionA = $grupos->filter(function($grupo) {
                return $grupo->seccion === 'Primaria';
            });
            
            $gruposSeccionB = $grupos->filter(function($grupo) {
                return $grupo->seccion === 'Secundaria';
            });
        @endphp
        
        @if($gruposSeccionA->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                        Sección Primaria
                    </h2>
                    <div class="ml-4 px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm font-semibold">
                        {{ $gruposSeccionA->count() }} grupo{{ $gruposSeccionA->count() != 1 ? 's' : '' }}
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach ($gruposSeccionA as $grupo)
                        @php
                            $horariosGrupo = $horarios->where('grupo_id', $grupo->id);
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $grupo->nombre }}
                                    </h3>
                                    <span class="px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded-full self-start sm:self-auto">
                                        {{ $grupo->seccion }}
                                    </span>
                                </div>
                                
                                <div class="space-y-4">
                                    @foreach($horariosGrupo as $horario)
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                                {{ $horario->materia->nombre }}
                                                @php
                                                    $inicioSemana = now()->startOfWeek();
                                                    $finSemana = now()->endOfWeek();
                                                    $tareasSemana = DB::table('tareas')
                                                        ->where('materia', $horario->materia->id)
                                                        ->where('grupo', $grupo->id)
                                                        ->whereBetween('fecha_entrega', [$inicioSemana, $finSemana])
                                                        ->count();
                                                @endphp
                                                @if(Auth::user()->rol == 'administrador' || Auth::user()->rol == 'Coordinador Primaria' || Auth::user()->rol == 'Coordinador Secundaria')
                                                    <flux:badge color="lime" class="text-xs">
                                                        {{ $tareasSemana }} tarea{{ $tareasSemana != 1 ? 's' : '' }} esta semana
                                                    </flux:badge>
                                                @endif
                                            </h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Horario: {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</span>
                                                </div>
                                                
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="font-medium">Días:</span>
                                                        @foreach(explode(',', $horario->dias) as $dia)
                                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">{{ trim($dia) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 sm:mt-6 flex flex-col justify-end gap-2 sm:gap-3">
                                    <flux:button  variant="filled" name="view-students" 
                                        :href="route('tareas.index', ['grupo' => $grupo->id])"
                                        class="bg-blue-600 hover:bg-blue-700 text-sm sm:text-base w-full sm:w-auto">
                                        Agregar tarea
                                    </flux:button>
                                    
                                    <flux:button variant="filled" name="view-alumnos" 
                                        :href="route('tareas.alumnos', $grupo->id)"
                                        class="bg-green-600 hover:bg-green-700 text-sm sm:text-base w-full sm:w-auto">
                                        Vista de alumnos
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($gruposSeccionB->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                        Sección Secundaria
                    </h2>
                    <div class="ml-4 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold">
                        {{ $gruposSeccionB->count() }} grupo{{ $gruposSeccionB->count() != 1 ? 's' : '' }}
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach ($gruposSeccionB as $grupo)
                        @php
                            $horariosGrupo = $horarios->where('grupo_id', $grupo->id);
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $grupo->nombre }}
                                    </h3>
                                    <span class="px-3 py-1 text-sm font-semibold text-white bg-blue-600 rounded-full self-start sm:self-auto">
                                        {{ $grupo->seccion }}
                                    </span>
                                </div>
                                
                                <div class="space-y-4">
                                    @foreach($horariosGrupo as $horario)
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                                {{ $horario->materia->nombre }}
                                                @php
                                                    $inicioSemana = now()->startOfWeek();
                                                    $finSemana = now()->endOfWeek();
                                                    $tareasSemana = DB::table('tareas')
                                                        ->where('materia', $horario->materia->id)
                                                        ->where('grupo', $grupo->id)
                                                        ->whereBetween('fecha_entrega', [$inicioSemana, $finSemana])
                                                        ->count();
                                                @endphp
                                                @if(Auth::user()->rol == 'administrador' || Auth::user()->rol == 'Coordinador Primaria' || Auth::user()->rol == 'Coordinador Secundaria')
                                                    <flux:badge color="lime" class="text-xs">
                                                        {{ $tareasSemana }} tarea{{ $tareasSemana != 1 ? 's' : '' }} esta semana
                                                    </flux:badge>
                                                @endif
                                            </h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Horario: {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</span>
                                                </div>
                                                
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="font-medium">Días:</span>
                                                        @foreach(explode(',', $horario->dias) as $dia)
                                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">{{ trim($dia) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 sm:mt-6 flex flex-col justify-end gap-2 sm:gap-3">
                                    <flux:button  variant="filled" name="view-students" 
                                        :href="route('tareas.index', ['grupo' => $grupo->id])"
                                        class="bg-blue-600 hover:bg-blue-700 text-sm sm:text-base w-full sm:w-auto">
                                        Agregar tarea
                                    </flux:button>
                                    
                                    <flux:button variant="filled" name="view-alumnos" 
                                        :href="route('tareas.alumnos', $grupo->id)"
                                        class="bg-green-600 hover:bg-green-700 text-sm sm:text-base w-full sm:w-auto">
                                        Vista de alumnos
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

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
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-full">
                            <div class="p-5 sm:p-6 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $grupo->nombre }}
                                    </h3>
                                    <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-800 dark:bg-gray-700 rounded-full">
                                        {{ $grupo->seccion }}
                                    </span>
                                </div>
                                
                                <div class="space-y-4 flex-1 mb-6">
                                    @foreach($horariosGrupo as $horario)
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 first:border-t-0 first:pt-0">
                                            <div class="flex items-start justify-between mb-3">
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $horario->materia->nombre }}
                                                </h4>
                                                @php
                                                    $totalTareas = DB::table('tareas')
                                                        ->where('materia', $horario->materia->id)
                                                        ->where('grupo', $grupo->id)
                                                        ->count();
                                                @endphp
                                                <span class="px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-full ml-2">
                                                    {{ $totalTareas }} tarea{{ $totalTareas != 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                            <div class="space-y-2.5">
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ $horario->hora_inicio }} – {{ $horario->hora_fin }}</span>
                                                </div>
                                                
                                                <div class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach(explode(',', $horario->dias) as $dia)
                                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-medium">{{ trim($dia) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex flex-col gap-2.5 pt-4 border-t border-gray-200 dark:border-gray-700 mt-auto">
                                    <a href="{{ route('tareas.index', ['grupo' => $grupo->id]) }}" 
                                       class="w-full px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg text-center transition-colors duration-200">
                                        Agregar tarea
                                    </a>
                                    
                                    <a href="{{ route('tareas.alumnos', $grupo->id) }}" 
                                       class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg text-center transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Vista de alumnos
                                    </a>
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
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-full">
                            <div class="p-5 sm:p-6 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $grupo->nombre }}
                                    </h3>
                                    <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-800 dark:bg-gray-700 rounded-full">
                                        {{ $grupo->seccion }}
                                    </span>
                                </div>
                                
                                <div class="space-y-4 flex-1 mb-6">
                                    @foreach($horariosGrupo as $horario)
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 first:border-t-0 first:pt-0">
                                            <div class="flex items-start justify-between mb-3">
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $horario->materia->nombre }}
                                                </h4>
                                                @php
                                                    $totalTareas = DB::table('tareas')
                                                        ->where('materia', $horario->materia->id)
                                                        ->where('grupo', $grupo->id)
                                                        ->count();
                                                @endphp
                                                <span class="px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-full ml-2">
                                                    {{ $totalTareas }} tarea{{ $totalTareas != 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                            <div class="space-y-2.5">
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ $horario->hora_inicio }} – {{ $horario->hora_fin }}</span>
                                                </div>
                                                
                                                <div class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach(explode(',', $horario->dias) as $dia)
                                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-medium">{{ trim($dia) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex flex-col gap-2.5 pt-4 border-t border-gray-200 dark:border-gray-700 mt-auto">
                                    <a href="{{ route('tareas.index', ['grupo' => $grupo->id]) }}" 
                                       class="w-full px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg text-center transition-colors duration-200">
                                        Agregar tarea
                                    </a>
                                    
                                    <a href="{{ route('tareas.alumnos', $grupo->id) }}" 
                                       class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg text-center transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Vista de alumnos
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

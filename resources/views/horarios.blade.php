<x-layouts.app :title="__('Horarios')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6 shadow-sm bg-gray-50 dark:bg-gray-900 min-h-screen">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Horarios') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los horarios de las materias</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('horarios.plantilla') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar Plantilla
                </a>
                <flux:modal.trigger name="import-modal">
                    <flux:button variant="filled" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span>Importar Excel</span>
                    </flux:button>
                </flux:modal.trigger>
                <flux:modal.trigger name="edit-profile">
                    <flux:button icon='plus' variant="filled" class="flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                        <span>Nuevo Horario</span>
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <!-- Search and Filters -->
        <form action="{{ route('horarios.index') }}" method="GET" class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <!-- Search Bar and Dropdowns Row -->
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            name="search" 
                            id="searchInput"
                            placeholder="Buscar por nombre..." 
                            value="{{ request('search') }}"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                        <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400 dark:text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Dropdowns -->
                    <div class="flex flex-col md:flex-row gap-4 md:w-auto">
                        <div class="relative md:w-64">
                            <select name="grupo_filter" id="grupoFilter" class="w-full px-4 py-3 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none cursor-pointer transition-all">
                                <option value="">Todos los grupos</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ request('grupo_filter') == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->nombre }} {{ $grupo->seccion }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="w-5 h-5 absolute right-3 top-3.5 text-gray-400 dark:text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        
                        <div class="relative md:w-64">
                            <select name="materia_filter" id="materiaFilter" class="w-full px-4 py-3 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none cursor-pointer transition-all">
                                <option value="">Todas las materias</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id }}" {{ request('materia_filter') == $materia->id ? 'selected' : '' }}>
                                        {{ $materia->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="w-5 h-5 absolute right-3 top-3.5 text-gray-400 dark:text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2 md:items-start">
                        <button type="submit" class="px-5 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-md hover:shadow-lg transition-all whitespace-nowrap">
                            Filtrar
                        </button>
                        <a href="{{ route('horarios.index') }}" class="px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-all whitespace-nowrap">
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Main Content - Vertical Layout -->
        <div class="flex-1">
            @php
                // Agrupar horarios por materia
                $horariosPorMateria = $horarios->groupBy('materia_id');
                
                // Colores temáticos para cada materia (ciclo de colores)
                $colores = [
                    'bg-green-500', 'bg-purple-500', 'bg-blue-500', 'bg-teal-500', 
                    'bg-pink-500', 'bg-orange-500', 'bg-indigo-500', 'bg-red-500',
                    'bg-yellow-500', 'bg-cyan-500', 'bg-lime-500', 'bg-amber-500'
                ];
                $coloresCards = [
                    'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                    'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
                    'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
                    'bg-teal-50 dark:bg-teal-900/20 border-teal-200 dark:border-teal-800',
                    'bg-pink-50 dark:bg-pink-900/20 border-pink-200 dark:border-pink-800',
                    'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
                    'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800',
                    'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                    'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
                    'bg-cyan-50 dark:bg-cyan-900/20 border-cyan-200 dark:border-cyan-800',
                    'bg-lime-50 dark:bg-lime-900/20 border-lime-200 dark:border-lime-800',
                    'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800'
                ];
                // Usar solo iconos básicos confirmados que existen en Flux
                // Basado en uso en otros archivos del proyecto
                $iconos = [
                    'cog', 'document-text', 'device-tablet', 'book-open',
                    'academic-cap', 'book-open-text', 'home', 'calendar-days',
                    'user', 'pencil', 'trash', 'plus'
                ];
            @endphp
            
            <div class="space-y-6">
                @foreach($horariosPorMateria as $materiaId => $horariosMateria)
                    @php
                        $materia = $materias->firstWhere('id', $materiaId);
                        if (!$materia) continue;
                        
                        $indiceColor = ($loop->index) % count($colores);
                        $color = $colores[$indiceColor];
                        $colorCard = $coloresCards[$indiceColor];
                        $icono = $iconos[$indiceColor % count($iconos)];
                        $totalHorarios = $horariosMateria->count();
                    @endphp
                    
                    <div>
                        <!-- Materia Header -->
                        <div class="mb-4">
                            <div class="{{ $color }} text-white rounded-lg p-4 shadow-md flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    {{-- Usar solo iconos que sabemos que existen en Flux --}}
                                    <flux:icon name="{{ $icono }}" class="w-6 h-6" />
                                    <div>
                                        <h2 class="text-lg font-bold">{{ $materia->nombre }}</h2>
                                        <p class="text-sm opacity-90">({{ $totalHorarios }})</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($horariosMateria as $horario)
                                <div class="{{ $colorCard }} rounded-lg p-4 shadow-sm border relative hover:shadow-md transition-shadow">
                                    <!-- Menu Button -->
                                    <button type="button" 
                                        onclick="toggleDropdown({{ $horario->id }})"
                                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown menu -->
                                    <div id="dropdown-{{ $horario->id }}" class="hidden absolute right-3 top-10 mt-2 w-32 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                        <div class="py-1">
                                            <flux:modal.trigger name="edit-task">
                                                <button type="button" 
                                                    onclick="prepareEditModal({{ $horario->id }}, '{{ $horario->materia_id }}', '{{ $horario->grupo_id }}', '{{ $horario->maestro_id }}', '{{ $horario->dias }}', '{{ $horario->hora_inicio }}', '{{ $horario->hora_fin }}')"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                    <flux:icon name="pencil" class="w-3 h-3" />
                                                    Editar
                                                </button>
                                            </flux:modal.trigger>
                                            <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" class="form-eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                    <flux:icon name="trash" class="w-3 h-3" />
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Content -->
                                    <div class="pr-8">
                                        @if($horario->maestro_id)
                                            @php
                                                $maestro = $usuarios->firstWhere('id', $horario->maestro_id);
                                            @endphp
                                            @if($maestro)
                                                <div class="mb-2">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $maestro->name }}</p>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <div class="mb-2">
                                            <span class="inline-block px-2.5 py-1 text-xs font-medium bg-gray-800 dark:bg-gray-700 text-white rounded">
                                                {{ $horario->grupo->nombre ?? 'N/A' }} {{ $horario->grupo->seccion ?? '' }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-2">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $horario->hora_inicio }} – {{ $horario->hora_fin }}</span>
                                        </div>
                                        
                                        <div class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ str_replace(',', ', ', $horario->dias) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
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

    <!-- Modal de importación -->
    <flux:modal name="import-modal" class="md:w-[500px] p-6">
        <flux:container class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="xl">Importar Horarios desde Excel</flux:heading>
            </div>
            <flux:separator />

            <div class="space-y-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">Instrucciones:</h3>
                    <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1 list-disc list-inside">
                        <li>Descarga la plantilla de Excel haciendo clic en "Descargar Plantilla"</li>
                        <li>Completa la plantilla con los datos de los horarios</li>
                        <li>Las columnas requeridas son: Grupo, Seccion, Materia, Maestro, Dias, Hora Inicio, Hora Fin</li>
                        <li>Los días deben estar separados por comas (ej: Lunes,Martes,Miércoles)</li>
                        <li>Las horas deben estar en formato HH:MM (ej: 08:00)</li>
                    </ul>
                </div>

                <form action="{{ route('horarios.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Seleccionar archivo Excel (.xlsx o .xls)
                        </label>
                        <input 
                            type="file" 
                            name="archivo_excel" 
                            accept=".xlsx,.xls"
                            required
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100
                                   dark:file:bg-blue-900 dark:file:text-blue-300
                                   dark:hover:file:bg-blue-800
                                   cursor-pointer"
                        />
                        @error('archivo_excel')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(session('import_errors'))
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 max-h-40 overflow-y-auto">
                            <h4 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">Errores encontrados:</h4>
                            <ul class="text-xs text-red-800 dark:text-red-300 space-y-1">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('import_debug'))
                        @php
                            $debug = session('import_debug');
                        @endphp
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 max-h-60 overflow-y-auto">
                            <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-200 mb-2">Información de depuración:</h4>
                            <div class="text-xs text-yellow-800 dark:text-yellow-300 space-y-2">
                                @if(isset($debug['headers']))
                                    <div>
                                        <strong>Encabezados encontrados:</strong> 
                                        <code class="bg-yellow-100 dark:bg-yellow-900 px-1 rounded">{{ implode(', ', $debug['headers']) }}</code>
                                    </div>
                                @endif
                                @if(isset($debug['normalized_headers']))
                                    <div>
                                        <strong>Encabezados normalizados:</strong> 
                                        <code class="bg-yellow-100 dark:bg-yellow-900 px-1 rounded">{{ implode(', ', $debug['normalized_headers']) }}</code>
                                    </div>
                                @endif
                                @if(isset($debug['first_row_data']))
                                    <div>
                                        <strong>Primera fila (datos originales):</strong>
                                        <pre class="bg-yellow-100 dark:bg-yellow-900 p-2 rounded mt-1 text-xs overflow-x-auto">{{ json_encode($debug['first_row_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @endif
                                @if(isset($debug['first_row_normalized']))
                                    <div>
                                        <strong>Primera fila (normalizada):</strong>
                                        <pre class="bg-yellow-100 dark:bg-yellow-900 p-2 rounded mt-1 text-xs overflow-x-auto">{{ json_encode($debug['first_row_normalized'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @endif
                                @if(isset($debug['missing_columns_row_1']))
                                    <div>
                                        <strong>Columnas faltantes:</strong> 
                                        <span class="text-red-600 dark:text-red-400">{{ implode(', ', $debug['missing_columns_row_1']) }}</span>
                                    </div>
                                @endif
                                @if(isset($debug['available_columns_row_1']))
                                    <div>
                                        <strong>Columnas disponibles:</strong> 
                                        <code class="bg-yellow-100 dark:bg-yellow-900 px-1 rounded">{{ implode(', ', $debug['available_columns_row_1']) }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <flux:footer class="flex justify-end gap-3">
                        <flux:button type="button" variant="filled" onclick="closeModal('import-modal')">Cancelar</flux:button>
                        <flux:button type="submit" variant="primary">Importar</flux:button>
                    </flux:footer>
                </form>
            </div>
        </flux:container>
    </flux:modal>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            // Configuración de formularios de eliminación
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro de eliminar este horario?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Mostrar toast si existe mensaje
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
        });

        function closeModal(modalName) {
            const modal = document.querySelector(`[data-modal="${modalName}"]`);
            if (modal) {
                modal.close();
            }
        }

        function toggleDropdown(id) {
            // Cerrar todos los dropdowns abiertos
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                if (dropdown.id !== `dropdown-${id}`) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Toggle del dropdown actual
            const dropdown = document.getElementById(`dropdown-${id}`);
            dropdown.classList.toggle('hidden');
        }

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

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

    </script>
</x-layouts.app>

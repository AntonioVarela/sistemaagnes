<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-neutral-950">
        <div class="relative min-h-screen">
            <!-- Header -->
            
            <!-- Main Content -->
            <main class="flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] px-4">
                <div class="max-w-6xl w-full text-center">
                    <div class="mb-8">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-6">
                        <h1 class="text-4xl font-semibold mb-4 dark:text-white">Sistema de Gestión</h1>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-8">
                            Una solución integral para la gestión eficiente de tu organización
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto mb-12">
                        <div class="p-6 bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-800">
                            <div class="text-2xl mb-3">📊</div>
                            <h3 class="font-medium mb-2 dark:text-white">Gestión de Datos</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Organiza y analiza tu información de manera eficiente</p>
                        </div>
                        <div class="p-6 bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-800">
                            <div class="text-2xl mb-3">⚡</div>
                            <h3 class="font-medium mb-2 dark:text-white">Rendimiento</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Optimiza tus procesos y mejora la productividad</p>
                        </div>
                        <div class="p-6 bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-800">
                            <div class="text-2xl mb-3">🔒</div>
                            <h3 class="font-medium mb-2 dark:text-white">Seguridad</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Protege tus datos con las mejores prácticas</p>
                        </div>
                    </div>

                    <!-- Sección de Grupos -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold mb-6 dark:text-white">Grupos Disponibles</h2>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-8">
                            Explora las actividades y recursos disponibles para cada grupo
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                            @if(isset($grupos) && count($grupos) > 0)
                                @foreach($grupos as $grupo)
                                    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-800 p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                        {{ substr($grupo->nombre, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="text-left">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $grupo->nombre }}</h3>
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($grupo->seccion == 'Primaria') 
                                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @else 
                                                            bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                        @endif">
                                                        {{ $grupo->seccion }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-left mb-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <strong>Titular:</strong> 
                                                @if($grupo->titular)
                                                    {{ \App\Models\User::find($grupo->titular)->name ?? 'No asignado' }}
                                                @else
                                                    No asignado
                                                @endif
                                            </p>
                                        </div>
                                        
                                        <div class="flex justify-center">
                                            <a href="{{ route('tareas.alumnos', $grupo->id) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                Ver Actividades
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-8">
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8">
                                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay grupos disponibles</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Los grupos aparecerán aquí cuando estén configurados en el sistema.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (Route::has('login'))
                    <nav class="flex items-center justify-center gap-4">
                        @auth
                            <div class="mt-8">
                                <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-sm hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors">
                                    Dashboard
                                </a>
                            </div>
                        @else
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-sm hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors">
                                Comenzar Ahora
                            </a>
                        </div>
                        @endauth
                    </nav>
                @endif
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-6 text-center text-sm text-neutral-600 dark:text-neutral-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos reservados.</p>
            </footer>
        </div>
        @fluxScripts
    </body>
</html>

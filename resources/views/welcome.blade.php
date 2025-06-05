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
                <div class="max-w-4xl w-full text-center">
                    <div class="mb-8">
                        <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-6">
                        <h1 class="text-4xl font-semibold mb-4 dark:text-white">Sistema de Gestión</h1>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-8">
                            Una solución integral para la gestión eficiente de tu organización
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto">
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

                    @if (Route::has('login'))
                    <nav class="flex items-center justify-center gap-4">
                        @auth
                            <div class="mt-12">
                                <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-sm hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors">
                                    Dashboard
                                </a>
                            </div>
                        @else
                        <div class="mt-12">
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

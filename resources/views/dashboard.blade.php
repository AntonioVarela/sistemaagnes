<x-layouts.app :title="__('Tareas por Grado')">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('Tareas por Grado') }}
        </h1>
    </x-slot>

    <div class="flex flex-col gap-6">
        @foreach ($grupos as $grupo )
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $grupo->nombre }}
                </h2>
            <flux:button icon='eye' variant="filled" name="edit-profile" :href="route('tareas.alumnos', $grupo->id)"
                class="ml-2"> Vista del alumno </flux:button>
        @endforeach
    </div>
</x-layouts.app>

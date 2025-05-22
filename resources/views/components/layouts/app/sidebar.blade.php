<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 transition-all duration-300" x-data="{ minimized: false }" x-bind:class="minimized ? 'w-25' : 'w-64'">
            <div class="flex items-center justify-between" x-bind:class="minimized ? 'px-0' : 'px-4'">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo x-bind:class="minimized ? 'w-8 h-8' : 'w-10 h-10'" />
                </a>
                <button @click="minimized = !minimized" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg x-show="!minimized" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <svg x-show="minimized" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:navlist variant="outline" x-bind:class="minimized ? 'items-center px-0' : 'px-4'">
                <flux:navlist.group x-bind:heading="minimized ? '' : '{{ __('Control escolar') }}'" class="grid">
                    <flux:navlist.item icon="home" class="text-2xl"  :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <span x-show="!minimized">{{ __('Dashboard') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item icon="book-open" :href="route('tareas.index')" :current="request()->routeIs('tareas.index')" wire:navigate>
                        <span x-show="!minimized">{{ __('Tareas') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item icon="device-tablet" style="display:none;" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <span x-show="!minimized">{{ __('Anuncios') }}</span>
                    </flux:navlist.item>
                    @if (Auth::user()->rol == "administrador")
                        <flux:navlist.item icon="calendar-days" :href="route('horarios.index')" :current="request()->routeIs('horarios.index')" wire:navigate>
                            <span x-show="!minimized">{{ __('Horarios') }}</span>
                        </flux:navlist.item>
                    @endif
                    <flux:navlist.item icon="list-bullet" style="display:none;" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <span x-show="!minimized">{{ __('Planeaciones') }}</span>
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            @if (Auth::user()->rol == "administrador")
                <flux:navlist variant="outline" x-bind:class="minimized ? 'items-center px-2' : 'px-4'">
                    <flux:navlist.item icon="user" :href="route('usuarios.index')">
                        <span x-show="!minimized">{{ __('Usuarios') }}</span>
                    </flux:navlist.item>

                    <flux:navlist.item icon="user-group" :href="route('grupos.index')">
                        <span x-show="!minimized">{{ __('Grupos') }}</span>
                    </flux:navlist.item>

                    <flux:navlist.item icon="book-open-text" :href="route('materias.index')">
                        <span x-show="!minimized">{{ __('Materias') }}</span>
                    </flux:navlist.item>
                </flux:navlist>
            @endif
            

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start" x-bind:class="minimized ? 'w-full' : ''">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                    x-bind:class="minimized ? 'justify-center' : ''"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>

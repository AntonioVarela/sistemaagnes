<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e  border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 transition-all duration-300 md:w-50">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo/>
                </a>
            </div>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="Control escolar" class="grid">
                    <flux:navlist.item icon="home" class="text-2xl"  :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <span>{{ __('Dashboard') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item icon="device-tablet" :href="route('anuncios.index')" :current="request()->routeIs('anuncios.index')" wire:navigate>
                        <span>{{ __('Anuncios') }}</span>
                    </flux:navlist.item>
                    @if (Auth::user()->rol != "Coordinador")
                    <flux:navlist.item icon="book-open" :href="route('tareas.index')" :current="request()->routeIs('tareas.index')" wire:navigate>
                        <span>{{ __('Tareas') }}</span>
                    </flux:navlist.item>
                    
                    @if (Auth::user()->rol == "administrador")
                        <flux:navlist.item icon="calendar-days" :href="route('horarios.index')" :current="request()->routeIs('horarios.index')" wire:navigate>
                            <span>{{ __('Horarios') }}</span>
                        </flux:navlist.item>
                    @endif
                    <flux:navlist.item icon="list-bullet" style="display:none;" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <span>{{ __('Planeaciones') }}</span>
                    </flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            @if (Auth::user()->rol == "administrador")
                    <flux:navlist variant="outline">
                    <flux:navlist.item icon="user" :href="route('usuarios.index')">
                        <span>{{ __('Usuarios') }}</span>
                    </flux:navlist.item>

                    <flux:navlist.item icon="user-group" :href="route('grupos.index')">
                        <span>{{ __('Grupos') }}</span>
                    </flux:navlist.item>

                    <flux:navlist.item icon="book-open-text" :href="route('materias.index')">
                        <span>{{ __('Materias') }}</span>
                    </flux:navlist.item>
                </flux:navlist>
            @endif
            

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
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
        </flux:header>
        
        {{ $slot }}

        @fluxScripts
    </body>
</html>

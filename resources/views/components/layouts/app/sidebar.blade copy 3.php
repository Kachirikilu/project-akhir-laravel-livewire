<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --sidebar-width: 72px;
        }

        .sidebar-expanded {
            --sidebar-width: 256px;
        }

        /* Sidebar dipaksa lebar tertentu */
        .flux-sidebar-custom {
            width: var(--sidebar-width) !important;
            transition: width 0.3s ease !important;
            position: fixed !important;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 50;
        }

        /* Main menyesuaikan padding-left agar tidak tertutup */
        .main-content {
            padding-left: var(--sidebar-width);
            transition: padding-left 0.3s ease;
            width: 100%;
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-900" :class="{ 'sidebar-expanded': expanded }" x-data="{
    expanded: $persist(false).as('sidebar_expanded'),
    isAuto: $flux.appearance === 'system',
    updateAppearance(val) {
        $flux.appearance = val;
        this.isAuto = (val === 'system');
    },
    manualToggle() {
        this.isAuto = false;
        const nextTheme = $flux.appearance === 'dark' ? 'light' : 'dark';
        $flux.appearance = nextTheme;
    }
}">

    {{-- Sidebar --}}
    <flux:sidebar sticky stashable
        class="flux-sidebar-custom overflow-hidden border-e border-sky-600 bg-sky-800 dark:border-sky-800 dark:bg-sky-950 flex flex-col">
        {{-- Header Logo & Toggle --}}
        {{-- <div class="flex items-center h-10 mt-2 pl-1" :class="expanded ? 'px-1' : 'pl-2'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                <x-app-logo class="w-8 h-8 shrink-0" />
            </a>
        </div> --}}


        <div class="flex items-center h-10 mt-2 mx-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                <x-app-logo />
            </a>
        </div>


        {{-- Navigasi --}}
        <nav class="flex-1 space-y-1 no-scrollbar">
            @php
                $navItems = [
                    ['icon' => 'home', 'route' => 'dashboard', 'label' => 'Dashboard'],
                    ['icon' => 'user', 'route' => 'user-management', 'label' => 'User Management'],
                    ['icon' => 'academic-cap', 'route' => 'program-studi-management', 'label' => 'Study Program'],
                    ['icon' => 'rectangle-stack', 'route' => 'mata-kuliah-management', 'label' => 'Mata Kuliah'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="flex items-center text-xs mx-1 p-2 rounded-lg transition-colors {{ request()->routeIs($item['route']) ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                    title="{{ !$item['label'] ? $item['label'] : '' }}">
                    <flux:icon :name="$item['icon']" variant="outline" class="w-4 h-4 shrink-0" />
                    <span x-show="expanded" x-cloak x-cloak x-transition:enter="transition-all duration-300 ease-out"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition-all duration-200 ease-in"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-2" class="ml-3 whitespace-nowrap"
                        class="ml-3 whitespace-nowrap">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>


        <div class="mb-4 mx-1 flex justify-end">
            <button type="button" @click="expanded = !expanded"
                class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all"
                :class="expanded ? 'rotate-[-180deg]' : ''">
                <flux:icon name="chevron-double-right" variant="mini" class="w-6 h-6 text-white" />
            </button>
        </div>

        <flux:spacer />

        {{-- Dark Mode Switcher --}}
        <div x-data="{
            isAuto: $flux.appearance === 'system',
            updateAppearance(val) {
                $flux.appearance = val;
                this.isAuto = (val === 'system');
            },
            manualToggle() {
                this.isAuto = false;
                const nextTheme = $flux.appearance === 'dark' ? 'light' : 'dark';
                $flux.appearance = nextTheme;
            }
        }" class="mb-4 px-1 pr-4 flex items-center">

            {{-- Toggle Switch --}}
            {{-- <div :class="isAuto ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-300">
                    <button type="button" @click="manualToggle()"
                        class="relative inline-flex items-center h-6 rounded-full w-11 transition-all focus:outline-none shadow-lg border border-white/10"
                        :class="$flux.appearance === 'dark' ? 'bg-amber-400' : 'bg-sky-600'">

                        <span
                            class="flex items-center justify-center w-4 h-4 transition-transform transform bg-white rounded-full shadow-sm"
                            :class="$flux.appearance === 'dark' ? 'translate-x-6' : 'translate-x-1'">

                            <flux:icon x-show="$flux.appearance !== 'dark'" name="sun" variant="mini"
                                class="w-2.5 h-2.5 text-amber-500" />

                            <flux:icon x-show="$flux.appearance === 'dark'" name="moon" variant="mini"
                                class="w-2.5 h-2.5 text-sky-900" />
                        </span>
                    </button>
                </div> --}}


            {{-- Toggle Icon (Sun / Moon) --}}
            <div :class="isAuto ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-300 mr-6">

                <button type="button" @click="manualToggle()"
                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all">

                    <span
                        class="flex items-center justify-center w-4 h-4 transition-transform transform bg-white rounded-full shadow-sm">
                        {{-- Light Mode (Sun) --}}
                        <flux:icon x-show="$flux.appearance !== 'dark'" name="sun" variant="mini"
                            class="w-4 h-4 text-amber-500" />

                        {{-- Moon --}}
                        <flux:icon x-show="$flux.appearance === 'dark'" name="moon" variant="mini"
                            class="w-4 h-4 text-sky-900" />
                    </span>

                </button>
            </div>

            {{-- Checkbox Auto dengan Label Manual --}}
            <div class="flex items-center space-x-2" x-show="expanded" x-cloak
                x-transition:enter="transition-all duration-300 ease-out"
                x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-2"
                class="ml-3 whitespace-nowrap">
                <flux:checkbox x-model="isAuto"
                    @change="isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')" />

                <span class="text-sm font-medium text-white select-none cursor-pointer"
                    @click="isAuto = !isAuto; isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')">
                    System
                </span>
            </div>

        </div>





        {{-- Profile --}}
        <div class="pb-6 px-2">
            <div :class="!expanded ? 'flex justify-center [&_span]:hidden [&_svg:last-child]:hidden' : ''">
                <livewire:navigation.profile-dropdown />
            </div>
        </div>
    </flux:sidebar>

    {{-- Area Utama Konten --}}
    <main class="main-content min-h-screen">
        <div class="p-4 lg:p-8">
            {{ $slot }}
        </div>
    </main>

    @fluxScripts
</body>

</html>

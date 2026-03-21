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

        .flux-sidebar-custom {
            width: var(--sidebar-width) !important;
            transition: width 0.3s ease !important;
            position: fixed !important;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 50;
        }
        .main-content {
            /* padding-left: var(--sidebar-width); */
            transition: padding-left 0.3s ease;
            width: 100%;
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-900" :class="{ 'sidebar-expanded': expanded }"
x-data="{
    expanded: $persist(false).as('sidebar_expanded'),

    isDesktop: window.matchMedia('(min-width: 1024px)').matches,

    init() {
        const media = window.matchMedia('(min-width: 1024px)');

        this.isDesktop = media.matches;

        media.addEventListener('change', (e) => {
            this.isDesktop = e.matches;
        });
    },

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
                    <span x-show="expanded" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition-all duration-200 ease-in"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-4"
                        class="ml-3 whitespace-nowrap overflow-hidden text-ellipsis block">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>


        <div class="mb-4 mx-1 flex justify-end">
            <button type="button" @click="expanded = !expanded"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white">
                <span class="transition-all" :class="expanded ? 'rotate-[-180deg]' : ''">
                    <flux:icon name="chevron-double-right" variant="mini" class="w-6 h-6 text-white" />
                </span>
            </button>
        </div>

        <flux:spacer />

        {{-- Dark Mode Switcher --}}
        <livewire:navigation.dark-mode />


        {{-- Profile --}}
        <div class="pb-6">
            <livewire:navigation.profile-dropdown />
        </div>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <livewire:navigation.mobile-profile-dropdown />

    {{-- Area Utama Konten --}}
<main 
    class="min-h-screen transition-all duration-300 w-full"
    :style="isDesktop ? `padding-left: var(--sidebar-width)` : ''"
>
        <div class="py-2 lg:py-6 px-2 2xl:px-6 transition-all duration-300"
        :class="expanded ? 'lg:px-0 xl:px-2' : 'lg:px-4 xl:px-4'">
            {{ $slot }}
        </div>
    </main>

    @fluxScripts
</body>

</html>

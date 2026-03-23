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

:root { 
    --wadah-color: #feffff;
    --border-wadah-color: #e5cbd0; /* Kemerahan lembut */

    --main-color: #991b1b;       /* red-800 */
    --hover-main-color: #b91c1c; /* red-700 */
    --border-main-color: #dc2626; /* red-600 */
    --contrast-main-color: #991b1b;
    
    --main-text: #ffffff;
    --second-text: rgb(164, 166, 170);
    --contrast-main-text: #000000;
    --contrast-second-text: #52525b;
    --contrast-third-text: #818187; 

    /* Focus color diubah ke Rose/Pink agar harmonis dengan merah */
    --focus-color: #e11d48;       /* rose-600 */
    --hover-focus-color: #be123c; /* rose-700 */

    --main-table-color: #fcf1f1; /* Background tabel merah tipis */
    --second-table-color: #ffffff;
    --sub-table-color: #fffafb;

    --main-table-trans: #fcf1f1a0;
    --second-table-trans: #ffffffa0;
    --sub-table-trans: #fffafba0;
    --hover-table-color: #fecaca99;

    --border-table-color: #d8c4c4;

    --pop-up-color: #f3eded;
    --hover-pop-up-color: #e9dbdb;
}

.dark {
    --wadah-color: #1a1717;       /* Hitam kemerahan */
    --border-wadah-color: #3f1e1e; 

    --main-color: #450a0a;       /* red-950 */
    --hover-main-color: #7f1d1d; /* red-900 */
    --border-main-color: #991b1b; /* red-800 */
    --contrast-main-color: #ffffff;
    
    --main-text: #e9e0e0;
    --second-text: #a19292;
    --contrast-main-text: #ffffff;
    --contrast-second-text: #bdb6b6; 
    --contrast-third-text: #a09999; 

    --focus-color: #fb7185;       /* rose-400 */
    --hover-focus-color: #f43f5e; /* rose-500 */

    --main-table-color: #2d2525;
    --second-table-color: #313030;
    --sub-table-color: #352f2fa0;
    --hover-table-color: #7f1d1d66;

    --main-table-trans: #2d2525a0;
    --second-table-trans: #313030a0;
    --sub-table-trans: #352f2fa0;

    --border-table-color: #1f1414;

    --pop-up-color: #1a1919;
    --hover-pop-up-color: #272525;
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

<body class="min-h-screen bg-white dark:bg-zinc-900" :class="{ 'sidebar-expanded': expanded }" x-data="{
    expanded: $persist(false).as('sidebar_expanded'),
    expanded2: false,
    isDesktop: window.matchMedia('(min-width: 1024px)').matches,

    toggleExpanded() {
        this.expanded = !this.expanded;
        if (this.isDesktop) {
            this.expanded2 = this.expanded;
        }
    },

    init() {
        const media = window.matchMedia('(min-width: 1024px)');
        this.isDesktop = media.matches;

        this.expanded2 = this.expanded;

        media.addEventListener('change', (e) => {
            this.isDesktop = e.matches;

            if (!e.matches) {
                this.expanded = false;
            } else {
                this.expanded = this.expanded2;
            }
        });
    }
}">

    {{-- Sidebar --}}
    <div x-show="isDesktop || (expanded && !isDesktop)" x-cloak
        x-transition:enter="transition transform duration-300 ease-in-out" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200 ease-in-out"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 transition-all duration-300"
        :class="isDesktop && !expanded ? 'w-[72px]' : 'w-[256px]'">

        <flux:sidebar x-cloak
            class="flux-sidebar-custom overflow-hidden border-e
            bg-[var(--main-color)] border-[var(--border-main-color)]
            flex flex-col">

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
                        class="flex items-center text-xs mx-1 p-2 rounded-lg transition-colors {{ request()->routeIs($item['route']) ? 'bg-white/20 text-[var(--main-text)]' : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
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


            <div x-show="isDesktop" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
                class="mb-4 mx-1 flex justify-end">
                <button type="button" @click="toggleExpanded()"
                    class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[var(--main-text)]">
                    <span class="transition-all" :class="expanded ? 'rotate-[-180deg]' : ''">
                        <flux:icon name="chevron-double-right" variant="mini" class="w-6 h-6 text-[var(--main-text)]" />
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
    </div>

    <!-- Mobile User Menu -->
    <livewire:navigation.mobile-profile-dropdown />

    {{-- Area Utama Konten --}}
    <main x-cloak class="min-h-screen transition-all duration-300 w-full"
        :style="isDesktop ? `padding-left: var(--sidebar-width)` : ''">
        <div class="py-2 lg:py-6 px-0 2xl:px-6 transition-all duration-300"
            :class="expanded ? 'md:px-0 xl:px-2' : 'md:px-2 lg:px-4 xl:px-4'">
            {{ $slot }}
        </div>
    </main>

    @fluxScripts
</body>

</html>

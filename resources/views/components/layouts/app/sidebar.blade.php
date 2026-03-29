<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    {{-- @fluxStyles --}}
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

<body class="scrollbar-large min-h-screen bg-white dark:bg-zinc-900" :class="{ 'sidebar-expanded': expanded }" x-data="{
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
                $user = Auth::user();
                
                $allNavItems = [
                    ['icon' => 'home', 'route' => 'dashboard', 'label' => 'Dashboard', 'roles' => ['admin', 'dosen', 'mahasiswa']],
                    ['icon' => 'user', 'route' => 'user-management', 'label' => 'User Management', 'roles' => ['admin']],
                    ['icon' => 'academic-cap', 'route' => 'program-studi-management', 'label' => 'Study Program', 'roles' => ['admin']],
                    ['icon' => 'rectangle-stack', 'route' => 'mata-kuliah-management', 'label' => 'Mata Kuliah', 'roles' => ['admin', 'dosen']],
                    ['icon' => 'clipboard-document-list', 'route' => 'rps-management', 'label' => 'RPS Management', 'roles' => ['admin', 'dosen']],
                ];

                $navItems = array_filter($allNavItems, function($item) use ($user) {
                    if ($user->admin) {
                        return in_array('admin', $item['roles']);
                    } elseif ($user->dosen) {
                        return in_array('dosen', $item['roles']);
                    }
                    return false;
                });
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



            <div class="relative h-16 w-full flex items-center">
                {{-- Container Induk dengan posisi relatif untuk mengunci anak-anaknya --}}
                
                <div class="absolute transition-all duration-500 ease-in-out"
                    :class="expanded ? '-translate-y-8 opacity-100' : 'translate-y-0 opacity-100'">
                    <livewire:navigation.dark-mode />
                </div>

                <div class="absolute transition-all duration-500 ease-in-out"
                    :class="expanded ? 'translate-y-4 translate-x-0 opacity-100' : 'translate-x-12 opacity-0 pointer-events-none'">
                    <livewire:navigation.color-mode />
                </div>
            </div>


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
            {{-- <flux:toast /> --}}
            {{ $slot }}
        </div>
    </main>

    {{-- @persist('toast') --}}
        {{-- <x-flux::toaster /> --}}
        {{-- <flux:toast /> --}}
    {{-- @endpersist --}}
    @fluxScripts
</body>

</html>

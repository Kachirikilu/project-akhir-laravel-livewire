<div x-data="{
    currentTheme: localStorage.getItem('app-theme') || 'blue',

    // Definisikan di sini agar tidak melewati proses toJSON Livewire
    allThemes: [
        { id: 'blue', color: '#075985' },
        { id: 'purple', color: '#7e22ce' },
        { id: 'red', color: '#991b1b' },
        { id: 'green', color: '#059669' },
        { id: 'amber', color: '#b45309' },
        { id: 'pink', color: '#db2777' },
        { id: 'navy', color: '#475569' },
        { id: 'brown', color: '#5d4037' },
        { id: 'black', color: '#000000' }
    ],

    setTheme(id) {
        this.currentTheme = id;
        document.documentElement.setAttribute('data-theme', id);
        localStorage.setItem('app-theme', id);
    }
}" x-init="document.documentElement.setAttribute('data-theme', currentTheme)" class="flex items-center gap-2">

    {{-- Container Scrollable --}}
    <div x-show="expanded" x-cloak
        x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
        class="w-[220px] gap-3 p-1 flex items-center bg-gray-100 dark:bg-white/90 rounded-full border border-gray-200 dark:border-white/10 overflow-x-auto no-scrollbar snap-x">
        <template x-for="theme in allThemes" :key="theme.id">
            <button type="button" @click="setTheme(theme.id)"
                class="relative flex-shrink-0 w-5 h-5 rounded-full transition-all duration-300 hover:scale-110 focus:outline-none snap-center"
                :class="currentTheme === theme.id ? 'ring-2 ring-[var(--main-color)] ring-offset-2' :
                    'opacity-60'"
                :style="`background-color: ${theme.color}`">
                <span x-show="currentTheme === theme.id" class="absolute inset-0 flex items-center justify-center">
                    <flux:icon name="check" variant="mini" class="w-3 h-3 text-white" />
                </span>
            </button>
        </template>
    </div>

    {{-- <a href="{{ route($item['route']) }}" wire:navigate
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
                    </a> --}}
</div>

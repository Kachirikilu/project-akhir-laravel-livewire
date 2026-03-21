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
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all">

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
    <div class="cursor-pointer flex items-center space-x-2" x-show="expanded" x-cloak
        x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
        class="ml-3 whitespace-nowrap">
        <flux:checkbox x-model="isAuto"
            @change="isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')" />

        <span class="text-sm font-medium text-white select-none cursor-pointer"
            @click="isAuto = !isAuto; isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')">
            System
        </span>
    </div>

</div>

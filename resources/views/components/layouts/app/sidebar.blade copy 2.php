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
        }" class="mb-4">
            <div
                class="flex items-center justify-between p-2.5 border rounded-xl border-white/20 bg-white/10 dark:bg-black/20 backdrop-blur-sm">

                {{-- Toggle Switch --}}
                <div :class="isAuto ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-300">
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
                </div>


                {{-- Checkbox Auto dengan Label Manual --}}
                <div class="flex items-center space-x-2">
                    {{-- Kita hilangkan atribut label='' agar tidak bentrok --}}
                    <flux:checkbox x-model="isAuto"
                        class="!mb-0 [--base-color:white] [--accent:theme(colors.amber.400)]"
                        @change="isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')" />

                    {{-- Label Manual: Ini PASTI Putih karena menggunakan class Tailwind standar --}}
                    <span class="text-sm font-medium text-white select-none cursor-pointer"
                        @click="isAuto = !isAuto; isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')">
                        Auto
                    </span>
                </div>
            </div>
        </div>
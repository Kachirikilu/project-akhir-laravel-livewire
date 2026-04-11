<div>
    <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-{{ $colorLink }}-600 rounded-lg shadow-sm shadow-{{ $colorLink }}-200 dark:shadow-none">
            <flux:icon.book-open variant="solid" class="size-4 text-white" />
        </div>
        <div>
            <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">Referensi {{ $targetString ?? null }}</h3>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">{{ $textString }}</p>
        </div>
    </div>

    {{-- WADAH DENGAN SCROLL --}}
    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin">
        <template x-if="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}.length === 0">
            <div
                class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                <flux:icon.document-text class="size-8 text-zinc-300 mb-2" />
                <p class="text-xs italic text-zinc-400">Belum ada Referensi {{ $targetString ?? null }}</p>
            </div>
        </template>

        <template x-for="ref in $store.{{ $alpine ?? 'config' }}.{{ $modelString }}" :key="'main-' + ref.id">
            <div x-data="{ expanded: false }"
                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden transition-all hover:border-{{ $colorLink }}-300 dark:hover:border-{{ $colorLink }}-800 shadow-sm mb-3">

                <div x-on:click="expanded = !expanded"
                    class="p-3 cursor-pointer flex items-center gap-3 hover:bg-{{ $colorLink }}-50/30 dark:hover:bg-{{ $colorLink }}-900/10 border-l-4 border-l-{{ $colorLink }}-600">
                    <div class="size-7 flex-none flex items-center justify-center bg-{{ $colorLink }}-100 dark:bg-{{ $colorLink }}-900/30 text-{{ $colorLink }}-700 dark:text-{{ $colorLink }}-400 rounded-md font-bold text-xs"
                        x-text="'ID' + ref.id"></div>
                    <div class="flex-grow min-w-0">
                        <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200" x-text="ref.judul"></p>
                        <p class="text-xs text-zinc-500 font-medium"
                            x-text="ref.penulis + ' (' + (ref.tahun || '-') + ')'"></p>
                    </div>
                    <flux:icon.chevron-down variant="micro" class="text-zinc-400 transition-transform duration-200"
                        x-bind:class="expanded ? 'rotate-180' : ''" />
                </div>

                <div x-show="expanded" x-collapse>
                    <div class="px-3 pb-3 pt-0 ml-1">
                        <div
                            class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-2">
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 italic leading-relaxed">
                                <span class="font-bold text-zinc-500 not-italic">Sitasi:</span>
                                <span
                                    x-text="ref.penulis + '. (' + ref.tahun + '). ' + ref.judul + '. ' + (ref.penerbit || '-')"></span>
                            </p>
                            <div class="flex flex-wrap items-center gap-4 pt-1">
                                <span class="flex items-center gap-1.5 text-xs text-zinc-500">
                                    <flux:icon.building-library variant="micro" class="text-zinc-400" />
                                    <span x-text="ref.penerbit || 'Penerbit -'"></span>
                                </span>
                                <template x-if="ref.link">
                                    <a :href="ref.link" target="_blank"
                                        class="flex items-center gap-1 text-{{ $colorLink }}-600 dark:text-{{ $colorLink }}-400 hover:underline text-xs font-bold">
                                        <flux:icon.link variant="micro" /> <span x-text="ref.link"></span>
                                    </a>
                                </template>
                                <flux:badge color="{{ $colorLink }}" size="xs" x-text="ref.kode"
                                    class="scale-80 origin-left"></flux:badge>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<div x-show="expanded.includes(index)" x-collapse>
    <div class="px-4 pb-4 bg-white/20 dark:bg-black/5">
        <div class="border-t border-[var(--border-table-color)] pt-3 overflow-x-auto scrollbar-medium">

            <table class="w-full text-xs text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-tighter border-b border-[var(--border-table-color)]">
                        <th class="pb-3 px-4 text-center font-bold min-w-16">Kode</th>
                        <th class="pb-3 px-4 min-w-32">Deskripsi</th>
                        <th class="pb-3 px-4 min-w-32">Materi</th>
                        <th class="pb-3 px-4 min-w-32">Metodologi</th>
                        <th class="pb-3 px-4 min-w-32">Indikator</th>
                        <th class="pb-3 px-4 text-center">Metode</th>
                        <th class="pb-3 px-4 text-center">Bobot</th>
                        <th class="pb-3 px-4 min-w-32">Deskripsi Tugas</th>
                        <th class="pb-3 px-4 text-center">Waktu Tugas</th>
                        <th class="pb-3 px-4 text-center">Waktu Mandiri</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-table-color)]">
                    <template x-for="sub in subItems[index]?.scpmk" :key="sub.id">
                        <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            <td class="py-2.5 px-2">
                                <flux:badge color="fuchsia" size="sm"
                                    class="py-0 px-1.5 text-xs font-bold uppercase">
                                    <span x-text="sub.kode || '-'"></span>
                                </flux:badge>
                            </td>
                            <td class="py-2.5 px-2 leading-relaxed" x-text="sub.materi || '-'">
                            </td>
                            <td class="py-2.5 px-2 leading-relaxed" x-text="sub.metodologi || '-'"></td>
                            <td class="py-2.5 px-2 leading-relaxed" x-text="sub.indikator || '-'">
                            </td>
                            <td class="py-2.5 px-2 leading-relaxed" x-text="sub.deskripsi || '-'">
                            </td>
                            <td class="py-2.5 px-2 text-center leading-relaxed">
                                <div class="flex justify-center">
                                    <template x-if="sub.metode === 'UTS' || sub.metode === 'UAS'">
                                        <flux:badge color="amber" size="sm" class="text-xs font-bold uppercase"
                                            x-text="sub.metode"></flux:badge>
                                    </template>
                                    <template x-if="sub.metode === 'Teori'">
                                        <flux:badge color="emerald" size="sm" class="text-xs font-bold">Teori
                                        </flux:badge>
                                    </template>
                                    <template x-if="['Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                        <flux:badge color="cyan" size="sm" class="text-xs font-bold"
                                            x-text="sub.metode">
                                        </flux:badge>
                                    </template>
                                    <template
                                        x-if="!['UTS', 'UAS', 'Teori', 'Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                        <flux:badge color="zinc" size="sm" class="text-xs font-bold"
                                            x-text="sub.metode || '-'">
                                        </flux:badge>
                                    </template>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 text-center leading-relaxed font-black text-[var(--hover-focus-color)]"
                                x-text="sub.bobot + '%'"></td>
                            <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.tugas || '-'"></td>
                            <td class="py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.w_tugas || '-'"></td>
                            <td class="py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.w_mandiri || '-'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
    </div>
</div>

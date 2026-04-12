<div class="mb-6 space-y-3" x-data="{ expanded: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-500 flex items-center gap-2">
            <flux:icon name="link" variant="micro" />
            RPS Terhubung ({{ count($rps_items_list) }})
        </h3>
        
        @if(count($rps_items_list) > 1)
            <button type="button" @click="expanded = !expanded" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                <span x-text="expanded ? 'Sembunyikan' : 'Lihat Semua'"></span>
            </button>
        @endif
    </div>

    <div class="grid gap-3">
        @foreach($rps_items_list as $index => $r)
            <div 
                @if($index > 0) x-show="expanded" x-collapse @endif
                class="group relative p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 shadow-sm"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300">
                                {{ $r['kode_mk'] }}
                            </span>
                            <h4 class="text-sm font-bold text-zinc-800 dark:text-white">
                                {{ $r['mk'] }} </h4>
                        </div>
                        <p class="text-xs text-zinc-500 line-clamp-1 group-hover:line-clamp-none transition-all">
                            {{ $r['deskripsi'] }}
                        </p>
                    </div>
                    
                    <div class="text-right flex flex-col items-end gap-1.5">
                        <flux:badge size="sm" :color="$r['draf'] ? 'yellow' : 'green'" variant="pill">
                            {{ $r['draf_text'] }}
                        </flux:badge>
                        
                        <div class="flex flex-col items-end">
                             <span class="text-[10px] font-medium text-zinc-400">{{ $r['akademik'] }}</span>
                             <span class="text-[10px] font-bold text-indigo-500 uppercase">{{ $r['sks_text'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700/50 grid grid-cols-4 gap-2">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-400 uppercase font-semibold">Blok</span>
                        <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">{{ $r['kode_blok'] ?: '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-400 uppercase font-semibold">Bobot</span>
                        <span class="text-xs font-bold text-emerald-600">{{ $r['total_bobot'] }}%</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-400 uppercase font-semibold">CPMK/Sub</span>
                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">
                            {{ $r['count_cpmk'] }}/{{ $r['count_scpmk'] }}
                        </span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] text-zinc-400 uppercase font-semibold">Revisi</span>
                        <span class="text-[10px] font-mono text-zinc-500 italic">
                            {{ $r['revisi'] ? \Carbon\Carbon::parse($r['revisi'])->format('d/m/Y') : 'Original' }}
                        </span>
                    </div>
                </div>

                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[9px] px-1.5 py-0.5 rounded-full border border-zinc-200 dark:border-zinc-600 text-zinc-500 uppercase font-bold">
                        {{ $r['wajib_text'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
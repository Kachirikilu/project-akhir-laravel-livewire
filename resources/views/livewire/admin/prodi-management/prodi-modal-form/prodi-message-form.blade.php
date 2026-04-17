<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl shadow-sm transition-colors duration-300">
            <div class="flex items-center gap-2 mb-3">
                <flux:icon name="exclamation-triangle" variant="mini" class="text-red-700 dark:text-red-400" />
                <h4 class="font-bold text-red-700 dark:text-red-400 text-xs uppercase tracking-wider">
                    Ada beberapa kesalahan:
                </h4>
            </div>

            <div class="space-y-2">
                @foreach ($errors->all() as $error)
                    <div class="flex items-start gap-3">
                        <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 dark:bg-red-500 shrink-0"></div>
                        <p class="text-sm text-red-600 dark:text-red-300 leading-relaxed">
                            {{ $error }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 💡 2. Tips (Di bawah Error) --}}
    <div class="rounded-xl bg-[var(--second-table-trans)] border-[var(--border-wadah-color)] border p-4 shadow-sm backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed" x-data="{
                    {{-- JS Wrap diperbarui untuk mendukung dark mode pada tag strong --}}
                    wrap: (txt) => `<strong class='text-[var(--focus-color)] font-semibold'>${txt}</strong>`,
                
                    get labels() {
                        const mapping = {
                            'prodi': ['Program Studi', 'ID Jurusan'],
                            'jurusan': ['Jurusan', 'ID Fakultas'],
                            'fakultas': ['Fakultas']
                        };
                        return mapping[$store.prodi?.typeModal] || [];
                    },
                
                    formatList(arr) {
                        if (arr.length === 0) return '';
                        if (arr.length === 1) return this.wrap(arr[0]);
                        const last = arr.pop();
                        return arr.map(i => this.wrap(i)).join(', ') + ' dan ' + this.wrap(last);
                    }
                }">
                    Pastikan <span x-html="formatList(labels)"></span> yang dimasukkan adalah
                    <strong class="text-[var(--contrast-main-text)] font-semibold">unik</strong> dan
                    <strong class="text-[var(--contrast-main-text)] font-semibold">valid</strong>.
                </p>
            </div>

            <template x-if="$store.prodi?.typeModal == 'mahasiswa' || $store.prodi?.typeModal == 'file'" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Pastikan <strong class="text-[var(--focus-color)] font-semibold">Tahun Angkatan</strong> minimal <strong class="text-[var(--contrast-main-text)] font-semibold">tahun 1960</strong>.
                    </p>
                </div>
            </template>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode
                     <span 
                        x-text="
                            $store.prodi?.typeModal === 'prodi' ? 'Program Studi' :
                            $store.prodi?.typeModal === 'jurusan' ? 'Jurusan' :
                            $store.prodi?.typeModal === 'fakultas' ? 'Fakultas' :
                            'Data'
                        "
                    ></span>
                    </strong> minimal 3 huruf.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan semua kolom <strong class="text-[var(--contrast-main-text)] font-semibold">wajib diisi</strong> dengan benar.
                </p>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Perubahan akan tersimpan segera setelah formulir dikirim.
                </p>
            </div>
        </div>
    </div>
</div>
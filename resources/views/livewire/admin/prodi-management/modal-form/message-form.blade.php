<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <flux:icon name="exclamation-triangle" variant="mini" class="text-red-600" />
                <h4 class="font-bold text-red-700 text-xs uppercase tracking-wider">
                    Ada beberapa kesalahan:
                </h4>
            </div>

            <div class="space-y-2">
                @foreach ($errors->all() as $error)
                    <div class="flex items-start gap-3">
                        <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 shrink-0"></div>
                        <p class="text-sm text-red-600 leading-relaxed">
                            {{ $error }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 💡 2. Tips (Di bawah Error) --}}
    <div class="rounded-xl border border-slate-200 bg-white/50 p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-indigo-600" />
            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tips</span>
        </div>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                <p class="text-sm text-slate-600 leading-relaxed" x-data="{
                    wrap: (txt) => `<strong class='text-blue-900 font-semibold'>${txt}</strong>`,
                
                    get labels() {
                        const mapping = {
                            'prodi': ['Program Studi', 'ID Jurusan'],
                            'jurusan': ['Jurusan', 'ID Fakultas'],
                            'fakultas': ['Fakultas']
                        };
                        return mapping[$store.config?.typeModal] || [];
                    },
                
                    formatList(arr) {
                        if (arr.length === 0) return '';
                        if (arr.length === 1) return this.wrap(arr[0]);
                        const last = arr.pop();
                        return arr.map(i => this.wrap(i)).join(', ') + ' dan ' + this.wrap(last);
                    }
                }">

                    Pastikan <span x-html="formatList(labels)"></span> yang dimasukkan adalah
                    <strong class="text-slate-900 font-semibold">unik</strong> dan
                    <strong class="text-slate-900 font-semibold">valid</strong>.
                </p>
            </div>

            <template x-if="$store.config?.typeModal == 'mahasiswa' || $store.config?.typeModal == 'file'" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pastikan <strong class="text-blue-900 font-semibold">Tahun Angkatan</strong> minimal <strong class="text-slate-900 font-semibold">tahun 1960</strong>.
                    </p>
                </div>
            </template>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Pastikan semua kolom <strong class="text-slate-900 font-semibold">wajib
                        diisi</strong> dengan benar.
                </p>
            </div>
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Perubahan akan tersimpan segera setelah formulir dikirim.
                </p>
            </div>
        </div>
    </div>
</div>
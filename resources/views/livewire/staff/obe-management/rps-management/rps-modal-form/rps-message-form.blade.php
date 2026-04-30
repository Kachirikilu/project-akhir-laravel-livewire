<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @if (($show ?? true) && $errors->any())
        <div
            class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl shadow-sm transition-colors duration-300">
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
    <div class="space-y-3">
        {{-- Tips 1: Nama & Kode Unik --}}
        <div class="flex items-start gap-3">
            <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
            <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed" x-data="{
                wrap: (txt) => `<strong class='text-[var(--focus-color)] font-semibold'>${txt}</strong>`,
                get labels() {
                    const mapping = {
                        'rps-prodi': ['Program Studi'],
                        'rps-departemen': ['Program Studi', 'Departemen'],
                        'rps-fakultas': ['Program Studi', 'Fakultas'],
                        'rps-universitas': ['Program Studi']
                    };
                    return mapping[$store.rps?.typeModal] || [];
                },
                formatList(arr) {
                    if (arr.length === 0) return '';
                    const items = [...arr];
                    if (items.length === 1) return this.wrap(items[0]);
                    const last = items.pop();
                    return items.map(i => this.wrap(i)).join(', ') + ' dan ' + this.wrap(last);
                }
            }">
                Pastikan <span x-html="formatList(labels)"></span> yang dimasukkan <strong>sesuai</strong> dengan
                kurikulum yang berlaku.
            </p>
        </div>

        {{-- Tips 2: Validasi Khusus MK (SKS & Semester) --}}
        <div class="flex items-start gap-3">
            <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
            <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                Pastikan <strong class="text-[var(--focus-color)] font-semibold">SKS</strong> dan <strong
                    class="text-[var(--focus-color)] font-semibold">Semester</strong> <strong>(1-8)</strong> diisi
                dengan angka yang
                valid.
            </p>
        </div>

        {{-- Tips 3: Minimal Karakter Kode --}}
        <div class="flex items-start gap-3">
            <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
            <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode Mata Kuliah
                </strong> terpenuhi (contoh: <strong class="text-[var(--focus-color)] font-semibold"><span class="italic">TKE1107</span></strong>).
            </p>
        </div>

        {{-- Tips 4: Relasi Program Studi --}}
        <template x-if="$store.rps?.typeModal == 'rps'" x-cloak>
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pilih <strong class="text-[var(--contrast-main-text)] font-semibold">Program Studi</strong> yang
                    tepat agar Mata Kuliah muncul pada kurikulum yang sesuai.
                </p>
            </div>
        </template>

        {{-- Tips Umum --}}
        <div class="flex items-start gap-3">
            <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
            <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                Pastikan semua kolom <strong class="text-[var(--contrast-main-text)] font-semibold">wajib diisi</strong>
                untuk menjaga integritas data akademik.
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

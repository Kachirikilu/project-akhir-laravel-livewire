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
            @if ($roleType === 'file')
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Unggah file <strong class="text-slate-900 font-semibold">Excel</strong> dengan format yang
                        sesuai untuk menambahkan
                        banyak pengguna sekaligus.
                    </p>
                </div>
            @elseif ($isEditing)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kosongkan kolom <strong class="text-slate-900 font-semibold">password</strong>
                        untuk mempertahankan password lama.
                    </p>
                </div>
            @endif

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Pastikan
                    {{ $roleType == 'admin'
                        ? 'NIP, NITK'
                        : ($roleType == 'dosen'
                            ? 'NIP, NIDN, dan NIDK'
                            : ($roleType == 'mahasiswa'
                                ? 'NIM'
                                : 'NIP, NITK, NIDN, NIDK, dan NIM')) }}
                    yang dimasukkan adalah
                    <strong class="text-slate-900 font-semibold">unik</strong> dan
                    <strong class="text-slate-900 font-semibold">valid</strong>.
                </p>
            </div>

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

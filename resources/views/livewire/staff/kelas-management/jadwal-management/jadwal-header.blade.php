{{-- Header Section --}}
<div class="mb-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ $backUrl ?? route('kelas-management') }}" wire:navigate
            class="p-2 rounded-full hover:bg-[var(--hover-table-color)] transition-colors">
            <flux:icon name="arrow-left" class="h-6 w-6 text-[var(--contrast-second-text)]" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-[var(--contrast-second-text)]">{{ $kelas->kelas }}</h2>
            <p class="text-[var(--contrast-main-text)] opacity-70">Manajemen {{ $subHead }} dan Detail untuk {{ $mainHead }} ini</p>
        </div>
    </div>

    <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-[var(--second-pop-up-color)] p-6 rounded-xl border border-[var(--border-table-color)] shadow-sm">
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Kode
                {{ $mainHead }}</span>
            <span class="text-lg font-semibold text-[var(--focus-color)]">{{ $mainKode ?? '---' }}</span>

            @if ($subLabel ?? false)
                <span class="text-xs text-[var(--contrast-main-text)] opacity-70">{{ $subLabel ?? '---' }}</span>
            @endif
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Mata
                Kuliah</span>
            <span class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kelas->mk ?? '-----' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">{{ $kelas->kode_mk ?? '---' }}</span>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Program
                Studi</span>
            <span class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kelas->prodi ?? '-' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">{{ $kelas->kode_pr ?? '---' }}
                <strong class="px-2">|</strong>
                {{ $kelas->pr_rel->fakultas_fk ?? '----' }}
            </span>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">RPS /
                Semester / SKS</span>
            <span
                class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kelas->kode_rps ?? '---' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">Sem {{ $kelas->semester ?? '-' }}
                <strong class="px-2">|</strong>
                {{ $kelas->sks ?? '-' }} SKS ({{ $kelas->sks_text ?? '-' }})</span>
        </div>
    </div>
</div>

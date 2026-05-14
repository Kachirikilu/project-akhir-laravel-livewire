<x-layouts.app :title="__('Kelas Management')">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-32 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('jadwal-management'))
                <livewire:staff.kelas-management.jadwal-management :kode="request()->route('kode')" />
            @else
                <livewire:staff.kelas-management />
            @endif
        </div>
    </div>
</x-layouts.app>


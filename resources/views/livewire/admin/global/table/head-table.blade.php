@if ($withTh ?? true)
    <th rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colSpan ?? 1 }}"
        class="bg-[var(--main-table-color)] border-[var(--border-table-color)] px-6 py-3 relative 
        {{ ($isBorderX ?? false) || ($isMain ?? false) ? 'border-x' : '' }}
        {{ $isBorderL ?? false ? 'border-l' : '' }}
        {{ $isBorderR ?? false ? 'border-r' : '' }}
    ">
@endif

<button x-cloak x-data="{
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    localDir: '{{ $sortDirection }}',
    clicked: false,
    init() {
        this.$watch('sortDirection', v => this.localDir = v)
    },
    async doSort() {
        this.clicked = true
        if (this.sortField === '{{ $sortFieldString }}') {
            this.localDir = this.localDir === 'asc' ? 'desc' : 'asc'
        } else {
            this.localDir = 'asc'
        }
        await $wire.sortBy('{{ $sortFieldString }}')
        this.clicked = false
    }
}" @click="doSort()" {{-- Tambahkan h-full agar button memenuhi area TH untuk garis bawah --}}
    class="w-full h-full cursor-pointer group flex {{ $isCenter ?? false ? 'justify-center' : '' }} items-center gap-1 text-xs font-medium uppercase whitespace-nowrap transition-all duration-200">

    {{-- Container Teks dengan Border Bawah Dinamis --}}
    <div class="flex items-center gap-1">

        <span
            :class="{
                'text-[var(--focus-color)] {{ $isMain ?? false ? 'font-bold' : '' }}': (
                    sortField === '{{ $sortFieldString }}' || clicked),
                'font-bold text-[var(--contrast-main-text)]':
                    !(
                        sortField === '{{ $sortFieldString }}' || clicked)
            }"
            class="transition-colors duration-200">
            {{ $headString ?? $sortFieldString }}
        </span>

        <span
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) ?
                'opacity-100 text-[var(--focus-color)] font-bold' :
                'opacity-0 group-hover:opacity-80 {{ $isMain ?? false ? 'text-[var(--contrast-main-text)]' : 'text-[var(--contrast-second-text)]' }}',
            
                (sortField === '{{ $sortFieldString }}' && localDir === 'desc') ?
                'rotate-180' :
                'rotate-0'
            ]"
            class="inline-block transition-all duration-200 ease-in-out">
            ↑
        </span>

        {{-- Garis Biru Absolut di Paling Bawah TH --}}
        <div class="absolute bottom-0 left-0 w-full h-[3px] bg-[var(--focus-color)] origin-left"
            x-show="sortField === '{{ $sortFieldString }}' || clicked"
            x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="scale-x-0"
            x-transition:enter-end="scale-x-100" x-transition:leave="transition transform ease-in duration-200"
            x-transition:leave-start="scale-x-100" x-transition:leave-end="scale-x-0">
        </div>

</button>

@if ($withTh ?? true)
    </th>
@endif

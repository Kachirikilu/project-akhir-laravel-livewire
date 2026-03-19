@if ($withTh ?? true)
    <th rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colSpan ?? 1 }}"
        class="px-6 py-3 relative {{ $isSubHeader ?? false ? 'bg-gray-100/50' : '' }}
        {{ ($isBorderX ?? false) || ($isMain ?? false) ? 'border-x' : '' }}
        {{ $isBorderL ?? false ? 'border-l' : '' }}
        {{ $isBorderR ?? false ? 'border-r' : '' }}
        border-gray-300">
@endif

<button x-data="{
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
}" @click="doSort()"
    {{-- Tambahkan h-full agar button memenuhi area TH untuk garis bawah --}}
    class="w-full h-full cursor-pointer group flex {{ $isCenter ?? false ? 'justify-center' : '' }} items-center gap-1 text-xs font-medium uppercase whitespace-nowrap transition-all duration-200">

    {{-- Container Teks dengan Border Bawah Dinamis --}}
    <div class="flex items-center gap-1">
        
        <span
            :class="{
                'text-indigo-600 {{ $isMain ?? false ? 'font-bold' : '' }}': (sortField === '{{ $sortFieldString }}' || clicked),
                '{{ $isMain ?? false ? 'font-bold text-gray-900' : 'text-gray-500' }}': !(
                    sortField === '{{ $sortFieldString }}' || clicked)
            }"
            class="transition-colors duration-200">
            {{ $headString ?? $sortFieldString }}
        </span>

        <span
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) ?
                'opacity-100 text-indigo-600 font-bold' :
                'opacity-0 group-hover:opacity-80 text-gray-400',
            
                sortField === '{{ $sortFieldString }}' && localDir === 'desc' ?
                'rotate-180' :
                'rotate-0'
            ]"
            class="inline-block transition-all transition-transform duration-300 ease-in-out">↑</span>
    </div>

    {{-- Garis Biru Absolut di Paling Bawah TH --}}
    <div class="absolute bottom-0 left-0 w-full h-[3px] bg-indigo-500 origin-left"
        x-show="sortField === '{{ $sortFieldString }}' || clicked"
        x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="scale-x-0"
        x-transition:enter-end="scale-x-100"
        x-transition:leave="transition transform ease-in duration-200"
        x-transition:leave-start="scale-x-100"
        x-transition:leave-end="scale-x-0">
    </div>

</button>

@if ($withTh ?? true)
    </th>
@endif
<th class="px-6 py-3 text-left">
    <button wire:click="sortBy('{{ $sortFieldString }}')"
        class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
        {{ $sortFieldString }} {!! $sortField === $sortFieldString ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
    </button>
</th>

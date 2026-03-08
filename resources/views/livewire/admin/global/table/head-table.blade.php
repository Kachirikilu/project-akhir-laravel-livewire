@if ($withTh ?? true)
    <th class="px-6 py-3 text-left">
@endif

<button wire:click="sortBy('{{ $sortFieldString }}')"
    class="cursor-pointer flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">

    {{ $headString ?? $sortFieldString }}

    {!! $sortField === $sortFieldString ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
</button>

@if ($withTh ?? true)
    </th>
@endif

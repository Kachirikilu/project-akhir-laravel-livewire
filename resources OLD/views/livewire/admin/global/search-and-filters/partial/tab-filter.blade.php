<button wire:click="{{ $xString }}('{{ $tabString }}')"
    class="cursor-pointer {{ isset($xFilter) && $xFilter == $tabString ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
    <i class="fas fa-users mr-2"></i> {{ $tabNameString }} (<span
        id="count-all">{{ $tabFilter }}</span>)
</button>

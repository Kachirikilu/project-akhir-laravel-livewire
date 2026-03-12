<button type="button" x-show="{{ $xShow }}" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-50"
    @click="
        open = false; 
        {{ $xClick ? $xClick . ';' : '' }} 
        {{ isset($xWire) ? '$wire.' . $xWire . ';' : '' }} 
        {{ isset($xWire2) ? '$wire.' . $xWire2 . ';' : '' }}
    "
    class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $xColor ?? 'text-gray-700' }} hover:text-red-500 transition duration-150"
    @empty($xColor)
        x-bind:class="$store.config.colorIcon || 'text-gray-700'"
    @endempty
    title="Bersihkan">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
</button>

{{-- <button type="button" 
    x-show="('{{ $xSearch ?? 'search' }}' in $data) && ({{ $xShow }})" 
    
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-50" 
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150" 
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-50" 
    
    @click.prevent="
        open = false;
        {{ $xClick }};
        $wire.{{ $xWire }};
        {{ isset($xWire2) ? '$wire.' . $xWire2 : '' }}
    "
    class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $xColor ?? 'text-gray-400' }} hover:text-red-500 transition duration-150"
    title="Bersihkan">
    
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
</button> --}}

<div class="relative">
    <input
        type="text"
        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm
               focus:outline-none focus:ring focus:ring-blue-500/20
               dark:border-neutral-700 dark:bg-neutral-900"
        placeholder="Cari Prodi..."
        wire:model.debounce.300ms="searchProdi"
    />

    {{-- hasil autocomplete --}}
    @if(!empty($prodiResults))
        <ul class="absolute z-10 mt-1 w-full rounded-lg border
                   border-neutral-200 bg-white shadow
                   dark:border-neutral-700 dark:bg-neutral-900">
            @foreach($prodiResults as $prodi)
                <li
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800"
                    wire:click="selectProdi({{ $prodi->id }})"
                >
                    {{ $prodi->nama_prodi ?? $prodi->name }}
                </li>
            @endforeach
        </ul>
    @endif
</div>

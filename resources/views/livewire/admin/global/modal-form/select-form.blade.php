<div class="relative"
    x-data="{
        open: false,
        value: $store.config?.{{ $modelString }}
    }"
    x-effect="
        if ($store.config?.isEdit === 0) {
            value = '';
        } else {
            value = $store.config?.{{ $modelString }};
        }
    "
    wire:key="select-form-{{ $modelString }}"
>

    <label for="{{ $modelString }}" class="block text-sm font-medium">
        {{ $labelString }}
        @if ($isRequired ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
        </div>

        <input autocomplete="off" x-model="value" {{-- Gunakan x-model agar Alpine tahu isinya --}} type="text" readonly @click="open = true"
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
            placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                placeholder-[var(--contrast-third-text)]
            w-full border rounded-lg pl-10 px-3 py-2 pr-10 cursor-pointer">

        {{-- 2. Tombol Reset --}}
        @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow'   => 'value',
            'xClick' => "value = ''",
            'xAlpine' => $modelString
            // 'xColor' => $colorIcon
        ])
    </div>

    {{-- Dropdown Result --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="bg-[var(--pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">

        @foreach ($xOptions as $option)
            <div wire:key="option-{{ $option }}"
                @click="
                    value = '{{ $option }}'; 
                    $store.config['{{ $modelString }}'] = value;
                    open = false
                "
                 class="px-4 py-2 cursor-pointer transition-colors duration-200
                bg-[var(--pop-up-color)] border-[var(--focus-color)]
                hover:bg-[var(--hover-pop-up-color)] hover:text-[var(--main-text)]
                {{-- border-b last:border-none  --}}
                text-sm">
                <div class="flex justify-between items-center my-1">
                    <span class="text-[var(--contrast-main-text)] font-semibold leading-tight">{{ $option }}</span>
                    <span class="bg-[var(--focus-color)] text-[var(--main-text)] text-xs text-white px-2 py-1 rounded-md ml-2">Pilih</span>
                </div>
            </div>
        @endforeach
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>

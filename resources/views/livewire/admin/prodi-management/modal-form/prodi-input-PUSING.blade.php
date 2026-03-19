<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Input Program Studi</h4>

        <div x-data
            x-effect="
        if($store.config?.isEdit === 0){
            $store.config.{{ $modelString }} = '';
        }
    ">
            <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">{{ $labelString }}
                @if ($isRequired ?? false)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
                    {{-- $store.config?.setEdit(0) --}}
                </div>
                <input x-model="$store.config.{{ $modelString }}" {{-- wire:model.lazy="{{ $modelString }}" --}} name="{{ $modelString }}"
                    x-bind:value="$store.config?.isEdit ? $el.value : ''" type="{{ $typeString ?? 'text' }}"
                    id="{{ $modelString }}" placeholder="{{ $placeholder }}"
                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500"
                    @if (!empty($isKode) && $isKode > 0) maxlength="{{ $isKode }}"
                inputmode="text"
                oninput="
                    this.value = this.value
                        .replace(/[^a-zA-Z]/g, '')
                        .toUpperCase()
                        .slice(0, {{ $isKode }})
                "
            @elseif (isset($numberOnly) && $numberOnly) inputmode="numeric" pattern="[0-9]*" maxlength="{{ $maxlength ?? 255 }}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, {{ $maxlength ?? 255 }})"
            @else
        maxlength="{{ $maxlength ?? 255 }}" @endif>
                {{-- <div wire:loading wire:target="{{ $targetLoading }}"
            class="absolute inset-y-0 right-0 flex items-center pt-4 pr-3">
        <flux:icon name="arrow-path" class="animate-spin h-4 w-4 text-gray-400"/>
    </div> --}}
            </div>
            @error($modelString)
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>


        <div x-data
            x-effect="
        if($store.config?.isEdit === 0){
            $store.config.{{ $modelString }} = '';
        }
    ">
            <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">{{ $labelString }}
                @if ($isRequired ?? false)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
                    {{-- $store.config?.setEdit(0) --}}
                </div>
                <input x-model="$store.config.{{ $modelString }}" {{-- wire:model.lazy="{{ $modelString }}" --}} name="{{ $modelString }}"
                    x-bind:value="$store.config?.isEdit ? $el.value : ''" type="{{ $typeString ?? 'text' }}"
                    id="{{ $modelString }}" placeholder="{{ $placeholder }}"
                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500"
                    @if (!empty($isKode) && $isKode > 0) maxlength="{{ $isKode }}"
                inputmode="text"
                oninput="
                    this.value = this.value
                        .replace(/[^a-zA-Z]/g, '')
                        .toUpperCase()
                        .slice(0, {{ $isKode }})
                "
            @elseif (isset($numberOnly) && $numberOnly) inputmode="numeric" pattern="[0-9]*" maxlength="{{ $maxlength ?? 255 }}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, {{ $maxlength ?? 255 }})"
            @else
        maxlength="{{ $maxlength ?? 255 }}" @endif>
                {{-- <div wire:loading wire:target="{{ $targetLoading }}"
            class="absolute inset-y-0 right-0 flex items-center pt-4 pr-3">
        <flux:icon name="arrow-path" class="animate-spin h-4 w-4 text-gray-400"/>
    </div> --}}
            </div>
            @error($modelString)
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>


        <div class="relative" x-data="{
            open: false,
            value: @entangle($modelString)
        }"
            x-effect="
        if ($store.config?.isEdit === 0) {
            value = '';
        } else {
            value = $store.config?.{{ $modelString }};
        }
    "
            wire:key="select-form-{{ $modelString }}">

            <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">
                {{ $labelString }}
                @if ($isRequired ?? false)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            <div class="relative mt-2">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
                </div>

                <input autocomplete="off" x-model="value" {{-- Gunakan x-model agar Alpine tahu isinya --}} type="text" readonly
                    @click="open = true" @click.outside="open = false" @keydown.escape.window="open = false"
                    id="{{ $modelString }}" placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
                    class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10 cursor-pointer">

                {{-- 2. Tombol Reset --}}
                @include('livewire.admin.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'value',
                    'xClick' => "value = ''",
                    // 'xColor' => $colorIcon
                ])
            </div>

            {{-- Dropdown Result --}}
            <div x-show="open" x-transition.opacity x-cloak
                class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

                @foreach ($xOptions as $option)
                    <div wire:key="option-{{ $option }}"
                        @click="
                    value = '{{ $option }}'; 
                    $store.config['{{ $modelString }}'] = value;
                    open = false
                "
                        {{-- Set nilai via Alpine --}}
                        class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-800 leading-tight">{{ $option }}</span>
                            <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">Pilih</span>
                        </div>
                    </div>
                @endforeach
            </div>
            @error($modelString)
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>


        <div class="relative" x-data="{
            open: false,
            search: @entangle($nameSearchString),
            selectedId: @entangle($idString).live,
        }"
            x-effect="
        if ($store.config?.isEdit === 0) {
            search = '';
            selectedId = null;
        } else {
            if('{{ $typeXString }}' == 'prodi') {
                search = $store.config?.{{ $modelString }};
            } else {
                search = '{{ $nameXString }} ' + $store.config?.{{ $modelString }};
            }
            selectedId = $store.config?.['{{ $idString }}'];
        }
    "
            wire:key="search-input-form-{{ $typeXString }}">
            <label for="{{ $searchString }}" class="block text-sm font-medium text-gray-700">
                {{ $nameXString }} <span class="text-red-500">*</span>
            </label>

            <div class="relative mt-2">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
                </div>

                <input x-model="search" autocomplete="off" type="text"
                    @focus="
                open = true; 
                $event.target.select();
                $wire.{{ $fetchString }}(); 
            "
                    @input.debounce.300ms="
                open = true;
                $wire.{{ $fetchString }}(search); 
            "
                    @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $searchString }}"
                    placeholder="Cari nama {{ $nameXString }}..."
                    class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

                {{-- Tombol Reset --}}
                @include('livewire.admin.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'search || selectedId',
                    'xClick' => "search = ''; selectedId = null",
                    'xWire' => $resetXInput,
                    'xWire2' => $fetchString . '()',
                    // 'xColor' => $colorIcon
                ])
            </div>

            {{-- Info Terpilih --}}
            <div x-show="selectedId && search" x-cloak>
                <p class="text-xs text-indigo-600 mt-1 font-medium italic">
                    Terpilih: <span x-text="search"></span> (ID: <span x-text="selectedId"></span>)
                </p>
            </div>

            {{-- DROPDOWN HASIL --}}
            <div x-show="open" x-transition.opacity x-cloak
                class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto">

                @forelse ($xResults as $x)
                    <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
                        @click="
                    search = '{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}'; 
                    selectedId = {{ $x['id'] }}; 
                    $store.config['{{ $idString }}'] = selectedId;
                    open = false; 
                    $wire.{{ $selectX }}({{ $x['id'] }}, '{{ $x[$typeXString] }}')
                "
                        class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span
                                    class="font-semibold text-gray-800 leading-tight">{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}</span>

                                @if ($typeXString !== 'fakultas')
                                    <span class="text-xs text-gray-500 mt-0.5">Fakultas {{ $x['fakultas'] }}</span>
                                @endif

                                {{-- <span x-show="{{$typeXString}} !== 'fakultas'" class="text-xs text-gray-500 mt-0.5">{{ $nameXString }} {{ $x[$typeXString] }}</span> --}}
                            </div>
                            <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">ID:
                                {{ $x['id'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                    </div>
                @endforelse
            </div>
            @error($idString)
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

    </div>
</div>
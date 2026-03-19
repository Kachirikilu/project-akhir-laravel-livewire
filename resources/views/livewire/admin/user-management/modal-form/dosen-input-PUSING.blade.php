<div>
    {{-- ****************************************************** --}}
    {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Personal Information</h4>

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
                </div>
                <input x-model="$store.config.{{ $modelString }}" name="{{ $modelString }}"
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
                </div>
                <input x-model="$store.config.{{ $modelString }}" name="{{ $modelString }}"
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
                </div>
                <input x-model="$store.config.{{ $modelString }}" name="{{ $modelString }}"
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
                </div>
                <input x-model="$store.config.{{ $modelString }}" name="{{ $modelString }}"
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

                <button type="button" x-show="{{ $xShow }}"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-50"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-50"
                    @click="
        open = false; 
        {{ $xClick ? $xClick . ';' : '' }} 
        {{ isset($xWire) ? '$wire.' . $xWire . ';' : '' }} 
        {{ isset($xWire2) ? '$wire.' . $xWire2 . ';' : '' }}
    "
                    class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-{{ $xPr ?? 3 }} {{ $xColor ?? 'text-gray-700' }} hover:text-red-500 transition duration-150"
                    @empty($xColor)
        x-bind:class="$store.config?.colorIcon || 'text-gray-700'"
    @endempty
                    title="Reset">
                    <svg class="h-{{ $xSize ?? 5 }} w-{{ $xSize ?? 5 }}" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div x-show="selectedId && search" x-cloak>
                <p class="text-xs text-indigo-600 mt-1 font-medium italic">
                    Terpilih: <span x-text="search"></span> (ID: <span x-text="selectedId"></span>)
                </p>
            </div>

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

                <input autocomplete="off" x-model="value" type="text" readonly @click="open = true"
                    @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
                    placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
                    class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10 cursor-pointer">

                <button type="button" x-show="{{ $xShow }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-50"
                    @click="
        open = false; 
        {{ $xClick ? $xClick . ';' : '' }} 
        {{ isset($xWire) ? '$wire.' . $xWire . ';' : '' }} 
        {{ isset($xWire2) ? '$wire.' . $xWire2 . ';' : '' }}
    "
                    class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-{{ $xPr ?? 3 }} {{ $xColor ?? 'text-gray-700' }} hover:text-red-500 transition duration-150"
                    @empty($xColor)
        x-bind:class="$store.config?.colorIcon || 'text-gray-700'"
    @endempty
                    title="Reset">
                    <svg class="h-{{ $xSize ?? 5 }} w-{{ $xSize ?? 5 }}" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Dropdown Result --}}
            <div x-show="open" x-transition.opacity x-cloak
                class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">
                @foreach ([
        'Aktif', // Hijau (Produktif)
        'Tugas Belajar', // Kuning (Transisi/Studi)
        'Izin Belajar', // Kuning (Transisi/Studi)
        'Cuti Sabatika', // Kuning (Transisi/Riset)
        'Alih Tugas', // Orange (Perubahan Jabatan)
        'Resign', // Orange (Keluar Prosedural)
        'Pensiun', // Orange (Keluar Prosedural)
        'Diberhentikan', // Merah (Masalah/Sanksi)
        'Meninggal Dunia', // Merah (Permanen)
    ] as $option)
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

    </div>
</div>
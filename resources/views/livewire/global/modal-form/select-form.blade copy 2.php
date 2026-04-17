@php
    $isDisabled = $disabled ?? false;
    $xOptions = is_array($xOptions ?? null) ? $xOptions : [];
    $xValues = is_array($xValues ?? null) ? $xValues : $xOptions;
@endphp

<div class="relative" wire:key="select-form-{{ $modelString }}-{{ $alpine }}" x-data="{
    open: false,
    options: [],
    values: [],
    isDisabled: @js($isDisabled),

    getLabel(val) {
        if (val === '' || val === null || val === undefined) return '';
        const index = this.values.indexOf(Number(val));
        if (index !== -1) {
            return this.options[index];
        }

        if (typeof val === 'string' && this.options.includes(val)) {
            return val;
        }

        return '';
    },

    updateMkDigitSemester() {
        if (!this.$store?.mk) return;

        const sem = parseInt(this.$store.mk.semester);
        const blok = parseInt(this.$store.mk.kode_blok);

        if (!sem) {
            this.$store.mk.digit_semester = '';
            return;
        }

        if (blok === 0) {
            this.$store.mk.digit_semester = Math.ceil(sem / 2).toString() + '0';
            return;
        }

        const tahun = Math.ceil(sem / 2);
        const tipe = sem % 2 !== 0 ? '1' : '2';
        this.$store.mk.digit_semester = tahun.toString() + tipe;
    },

    value: ''
}"
    x-init="value = getLabel($store.{{ $alpine ?? 'config' }}?.{{ $modelString }})"
    x-effect="
        options = @js($xOptions);
        values = @js($xValues ?? $xOptions);
        isDisabled = @js($isDisabled);

        const rawVal = $store.{{ $alpine ?? 'config' }}?.{{ $modelString }};

        if ((rawVal === null || rawVal === undefined || rawVal === '') && $store.{{ $alpine ?? 'config' }}?.isEdit === 0) {
            value = '';
        } else {
            value = getLabel(rawVal);
        }

        if ('{{ $alpine }}' === 'mk' && ['semester','kode_blok'].includes('{{ $modelString }}')) {
            this.updateMkDigitSemester();
        }
    "
    wire:key="select-form-{{ $modelString }}">

    <label for="{{ $modelString }}" class="block text-sm font-medium" :class="isDisabled ? 'opacity-50' : ''">
        {{ $nameXString ?? ucfirst($modelString) }}
        @if ($isRequired ?? true)
            <span class="text-red-500" x-show="!isDisabled">*</span>
        @endif
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="isDisabled ? 'text-gray-400' : ($store.{{ $alpine ?? 'config' }}?.colorIcon)" />
        </div>

        {{-- Modifikasi Input: Tambahkan kondisi :readonly dan :class --}}
        <input autocomplete="off" x-model="value" type="text" readonly {{-- Jika disable, klik tidak melakukan apa-apa --}}
            @click="if(!isDisabled) open = true" @click.outside="open = false" @keydown.escape.window="open = false"
            id="{{ $modelString }}"
            :placeholder="isDisabled ? '{{ $placeholder ?? 'Terkunci' }}' : '{{ $placeholder ?? 'Pilih Opsi' }}'"
            {{-- Class dinamis untuk mode disable --}}
            :class="isDisabled ?
                'bg-gray-100 dark:bg-zinc-800 cursor-not-allowed opacity-70 text-gray-500 border-gray-200' :
                'bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] cursor-pointer'"
            class="w-full border rounded-lg pl-10 px-3 py-2 pr-10 transition-all duration-200">

        {{-- 2. Tombol Reset (Sembunyikan jika disable) --}}
        <template x-if="!isDisabled">
            @include('livewire.global.search-and-filters.partial.reset-button', [
                'xShow' => 'value',
                'xClick' => "value = ''",
                'xAlpine' => $modelString,
            ])
        </template>
    </div>

    {{-- Dropdown Result --}}
    <div x-show="open && !isDisabled" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">

        @foreach ($xOptions ?? [] as $i => $option)
            @php
                $label = is_array($option) ? $option['label'] ?? '-' : $option;
                $valueOption = is_array($option) ? $option['value'] ?? $label : $option;
            @endphp

            <div wire:key="option-{{ $i }}"
                @click="
            const selectedValue = {{ is_numeric($valueOption) ? $valueOption : "'$valueOption'" }};
            value = getLabel(selectedValue);
            $store.{{ $alpine ?? 'config' }}['{{ $modelString }}'] = selectedValue;
            if ('{{ $alpine }}' === 'mk' && ['semester','kode_blok'].includes('{{ $modelString }}')) {
                this.updateMkDigitSemester();
            }
            open = false
        "
                class="px-4 py-2 cursor-pointer">

                <div class="flex justify-between items-center my-1">
                    <span class="text-[var(--contrast-main-text)] font-semibold">
                        {{ $label }}
                    </span>

                    <span class="bg-[var(--focus-color)] text-white text-xs px-2 py-1 rounded-md ml-2">
                        Pilih
                    </span>
                </div>
            </div>
        @endforeach
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>

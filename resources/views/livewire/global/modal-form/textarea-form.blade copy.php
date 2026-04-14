<div x-data="{
    textItems: @entangle($modelString).live,
    parentSelected: @entangle($parentIdString ?? 'null').live,

    init() {
        // 1. Sinkronisasi Awal dari Livewire ke Store
        // Gunakan timeout kecil untuk memastikan Store sudah siap
        this.$nextTick(() => {
            if (this.textItems !== undefined) {
                $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = this.textItems;
            }
        });

        // 2. Watcher: Jika Store berubah (karena x-model), update Livewire
        this.$watch('$store.{{ $alpine ?? 'config' }}.{{ $modelString }}', (value) => {
            if (value !== this.textItems) {
                this.textItems = value;
            }
        });

        // 3. Watcher: Jika Livewire berubah (dari server), update Store
        this.$watch('textItems', (value) => {
            if (value !== $store.{{ $alpine ?? 'config' }}.{{ $modelString }}) {
                $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = value;
            }
        });
    },

    get isParentReady() {
        if (Array.isArray(this.parentSelected)) return this.parentSelected.length > 0;
        return this.parentSelected != null && this.parentSelected != '';
    }
}"
    x-effect="
    if($store.{{ $alpine ?? 'config' }}?.isEdit === 0 && !isParentReady){
        $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '';
    }
"
    wire:key="textarea-form-{{ $modelString }}-{{ $alpine }}">

    {{-- Label dengan efek redup jika belum siap --}}
    <div :class="!isParentReady ? 'opacity-50 transition-opacity' : ''">
        @include('livewire.global.modal-form.partial.label')
    </div>

    <div class="relative mt-1">
        {{-- Icon Samping Kiri --}}
        <div class="absolute top-3 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="isParentReady ? $store.{{ $alpine ?? 'config' }}?.colorIcon : 'text-gray-400'" />
        </div>

        <textarea x-model.debounce.300ms="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}"    
             id="{{ $modelString }}"
            :disabled="!isParentReady"
            :placeholder="isParentReady ? '{{ $placeholder }}' : 'Pilih {{ $nameXParent ?? 'Parent' }} terlebih dahulu...'"
            :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' :
                'bg-[var(--second-table-color)]'"
            class="border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none min-h-[100px] transition-all"></textarea>

    </div>

    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>

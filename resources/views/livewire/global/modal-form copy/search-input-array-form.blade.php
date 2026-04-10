<div class="relative" wire:key="search-array-{{ $typeXString }}-{{ $selectX }}" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    items: @entangle($idString).live,
    itemsAll: @entangle($itemsAllString).live,
    parentSelectedId: @entangle($parentIdString ?? null).live,

    init() {
        if (!Array.isArray(this.items)) this.items = [];
        if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
    },

    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    addItem(id, kode, name, slot2, slot3) {
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);

            this.itemsAll.push({
                kode: kode,
                name: name,
                slot2: slot2,
                slot3: slot3
            });
        }
    },

    removeItem(index) {
        this.items.splice(index, 1);
        this.itemsAll.splice(index, 1);
    },

    move(index, direction) {
        let to = index + direction;
        if (to < 0 || to >= this.items.length) return;
        const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];
        swap(this.items, index, to);
        swap(this.itemsAll, index, to);
    }
}">

    <label class="block text-sm font-medium mb-2">
        {{ $nameX2String ?? $nameXString }} 
        @if ($isRequired ?? true)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{-- 1. INPUT SEARCH --}}
    <div class="relative">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            @if ($wireLoadingParent ?? null)
                <div wire:loading wire:target="{{ $wireLoadingParent }}">
                    <svg class="animate-spin h-4 w-4 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            @endif
            <div @if ($wireLoadingParent ?? null) wire:loading.remove wire:target="{{ $wireLoadingParent }}" @endif>
                <flux:icon icon="{{ $iconString }}" variant="mini"
                    x-bind:class="isParentReady ? $store.{{ $alpine ?? 'config' }}?.colorIcon : 'text-gray-400'" />
            </div>
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @if ($wireLoadingParent ?? null) wire:loading.attr="disabled"
             wire:target="{{ $wireLoadingParent }}" @endif
            :disabled="!isParentReady" @focus="open = true; if(search === '') $wire.{{ $fetchString }}('', 'array');"
            @input.debounce.300ms="
                open = true; 
                if(search === '') { 
                    $wire.{{ $fetchString }}('', 'array'); 
                } else {
                    $wire.{{ $fetchString }}(search, 'array');
                }
            "
            @click.outside="open = false"
            :placeholder="isParentReady ? 'Cari dan tambahkan {{ $nameXString }}...' :
                'Pilih {{ $nameXParent ?? 'Induk' }} terlebih dahulu...'"
            :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' :
                'bg-[var(--second-table-color)]'"
            class="border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 py-2 focus:ring-2 focus:ring-[var(--focus-color)] transition-all">

        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''",
        ])
    </div>

    {{-- 2. DROPDOWN HASIL --}}


    {{-- 2. DROPDOWN HASIL --}}
    <div x-show="open && isParentReady" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @click.stop
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-72 overflow-y-auto">

        {{-- KONTEN LIST (Akan transparan saat loading) --}}
        <div
            @if ($wireLoadingParent ?? null) wire:target="{{ $wireLoadingParent }}, {{ $wireLoading }}" wire:loading.class="opacity-60 pointer-events-none" @endif">
            @forelse ($xResults as $x)
                <div wire:key="res-{{ $typeXString }}-{{ $x['id'] }}"
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                    <div class="flex flex-col mr-4">
                        <span
                            class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
                        <div class="text-[var(--contrast-main-text) font-medium text-xs flex items-center mt-1">
                            <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                                    {{ $x['id'] }}</span></span>
                            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                            <span>{{ $x['kode'] }}</span>
                            @if ($typeX2String ?? null)
                                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                                <span>{{ $x[$typeX2String] }}</span>
                            @endif
                            @if ($typeX3String ?? null)
                                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                                <span>{{ $x[$typeX3String] }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button"
                        @click="
                        if (items.includes({{ $x['id'] }})) {
                            let index = items.indexOf({{ $x['id'] }});
                            if (index !== -1) {
                                items.splice(index, 1);
                                itemsAll.splice(index, 1);
                            }
                        } else {
                           addItem(
                                {{ $x['id'] }}, 
                                '{{ $x['kode'] }}', 
                                '{{ $x[$typeXString] }}', 
                                @isset($typeX2String) '{{ $x[$typeX2String] ?? '' }}' @else null @endisset, 
                                @isset($typeX3String) '{{ $x[$typeX3String] ?? '' }}' @else null @endisset
                            );
                        }
                        "
                        :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white hover:bg-red-500' :
                            'bg-[var(--focus-color)] text-white'"
                        class="p-1.5 rounded-md transition-all group">

                        <template x-if="items.includes({{ $x['id'] }})">
                            <div class="relative">
                                <flux:icon icon="check" variant="mini" class="group-hover:hidden" />
                                <flux:icon icon="trash" variant="mini" class="hidden group-hover:block" />
                            </div>
                        </template>

                        <template x-if="!items.includes({{ $x['id'] }})">
                            <flux:icon icon="plus" variant="mini" />
                        </template>
                    </button>
                </div>
            @empty
                <div class="p-4 text-center">
                    <div wire:loading @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                        <p class="text-sm text-[var(--focus-color)] font-medium animate-pulse">
                            Sedang mencari data {{ $nameXString ?? null }}...
                        </p>
                    </div>

                    <div wire:loading.remove
                        @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                            Data {{ $nameXString ?? null }} tidak ditemukan!
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. AREA OPSI TERPILIH (DI DALAM KOTAK) --}}
    <div
        class="mt-4 p-4 border-2 border-dashed border-[var(--border-table-color)] rounded-xl bg-gray-50/30 dark:bg-neutral-900/10">

        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold uppercase tracking-widest text-gray-400">Daftar Terpilih:</span>
            <div class="flex items-center gap-2">
                <span x-show="items.length > 0"
                    class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full"
                    x-text="items.length + ' Terpilih'"></span>
            </div>
        </div>

        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1 scrollbar-medium">
            <template x-for="(id, index) in items" :key="id">
                <div
                    class="group relative flex items-start justify-between bg-[var(--second-table-color)] border border-[var(--border-table-color)] px-3 py-3 rounded-lg shadow-sm transition-all hover:border-[var(--focus-color)]">
                    <div class="flex items-start gap-3 flex-1">

                        <span class="text-xs font-black text-[var(--hover-focus-color)] w-4 mt-0.5"
                            x-text="index + 1"></span>

                        <div class="flex flex-col gap-1 flex-1">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs font-bold mb-1.5 px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white"
                                    x-text="itemsAll[index]?.kode"></span>
                                <div class="h-px flex-1 mb-1.5 bg-gray-200 dark:bg-neutral-800 opacity-40"></div>
                            </div>

                            <span class="text-sm mb-1 font-semibold text-[var(--contrast-main-text)] leading-tight"
                                x-text="itemsAll[index]?.name"></span>

                            <div
                                class="flex items-center flex-wrap text-xs text-[var(--contrast-second-text)] gap-y-1">
                                -<span class="ml-1 font-bold text-[var(--hover-focus-color)]" x-text="'ID: ' + id"></span>

                                @if ($typeX2String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot2"></span>
                                @endif

                                @if ($typeX3String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot3"></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center gap-1 ml-2">
                        <div class="flex flex-col gap-0.5">
                            <button @click="move(index, -1)" type="button"
                                class="p-0.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-10"
                                :disabled="index === 0">
                                <flux:icon icon="chevron-up" variant="mini" class="size-4" />
                            </button>
                            <button @click="move(index, 1)" type="button"
                                class="p-0.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-10"
                                :disabled="index === items.length - 1">
                                <flux:icon icon="chevron-down" variant="mini" class="size-4" />
                            </button>
                        </div>

                        <button @click="removeItem(index)" type="button"
                            class="p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition-colors ml-1">
                            <flux:icon icon="trash" variant="mini" class="size-5" />
                        </button>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="pt-6 pb-12 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="list-bullet" variant="outline" class="mb-1" />
                <p class="text-xs italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>
    </div>
    @error($idString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>

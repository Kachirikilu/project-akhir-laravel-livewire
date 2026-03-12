<flux:modal name="prodi-modal" wire:model="showProdiModal"
    {{-- x-data="{ prodiType: @entangle('prodiType').live, isEditing: @entangle('isEditing').live }"  --}}
    {{-- @setup-modal.window="pType = $event.detail.prodiType" --}}
    class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full relative">

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="addProdi, editProdi">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>


        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">

                @php
                    $textShow = $isEditing ? 'Edit ' : 'Tambah ';

                    match ($prodiType) {
                        'prodi' => [
                            ($colorIcon = 'text-red-700'),
                            ($colorBadge = 'red'),
                            ($textShow .= 'Program Studi'),
                        ],
                        'jurusan' => [($colorIcon = 'text-lime-700'), ($colorBadge = 'lime'), ($textShow .= 'Jurusan')],
                        'fakultas' => [
                            ($colorIcon = 'text-cyan-700'),
                            ($colorBadge = 'cyan'),
                            ($textShow .= 'Fakultas'),
                        ],
                        'file' => [
                            ($colorIcon = 'text-green-700'),
                            ($colorBadge = 'green'),
                            ($textShow = 'Input Program Studi dengan File Excel'),
                        ],
                        default => [
                            ($colorIcon = 'text-gray-700'),
                            ($colorBadge = 'gray'),
                            ($textShow .= 'Program Studi'),
                        ],
                    };
                @endphp

                <flux:badge icon="cog-6-tooth" color="{{ $colorBadge }}" size="lg">{{ $textShow }}
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
        <div class="p-6 pb-flex-1 overflow-y-auto space-y-6">

            <form
                @if ($prodiType == 'file') wire:submit.present="saveAllRows"
                @else
                    wire:submit.prevent="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}" @endif
                enctype="multipart/form-data" id="prodiForm">

                @if ($prodiType === 'file')
                    {{-- @include('livewire.admin.prodi-management.modal-form.excel-form') --}}
                @else
                    @if ($prodiType === 'prodi')
                        @include('livewire.admin.prodi-management.modal-form.prodi-form')
                    @elseif ($prodiType === 'jurusan')
                        @include('livewire.admin.prodi-management.modal-form.jurusan-form')
                    @elseif ($prodiType === 'fakultas')
                        @include('livewire.admin.prodi-management.modal-form.fakultas-form')
                    @endif
                @endif

                {{-- <template x-if="prodiType === 'prodi'">
                    @include('livewire.admin.prodi-management.modal-form.prodi-form')
                </template>
                <template x-if="prodiType === 'jurusan'">
                    @include('livewire.admin.prodi-management.modal-form.jurusan-form')
                </template>
                <template x-if="prodiType === 'fakultas'">
                    @include('livewire.admin.prodi-management.modal-form.fakultas-form')
                </template> --}}

                {{-- 3. Footer/Tombol --}}
                <div class="p-4 mt-4 bg-gray-50 rounded-b-lg rounded-t-sm gap-4 shadow-sm">

                    <div class="flex-1 text-xs text-gray-600 space-y-3">
                        @include('livewire.admin.prodi-management.modal-form.message-form')
                        @include('livewire.admin.prodi-management.modal-form.button-form')
                    </div>

                </div>
            </form>
        </div>

    </div>

</flux:modal>

<!-- pada Modal-Form ini, terhubung ke Toolbar, saya ingin variabel prodiType dikirim lebih cepat daripada bawaan Laravel yang delay, supaya input di dalam Modal-Form bisa tampil cepat. Saya sudah menggantinya dengan Alpine.js supaya variabel pType dan isEdit ditampilkan lebih cepat daripada backend Livewire itu sendiri, namun saya gagal, karena tetap ada delay. Bisakah kamu memberikan solusi? -->
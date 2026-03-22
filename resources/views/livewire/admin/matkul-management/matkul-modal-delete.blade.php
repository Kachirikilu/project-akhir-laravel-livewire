<flux:modal name="prodi-delete" wire:model="showProdiDelete" class="min-w-[20rem] max-w-md">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus
                <strong class="text-red-700 dark:text-red-400"
                    x-text="
                            $store.config?.typeModal_2 == 'prodi' ? '***Program Studi ' + $store.config?.nama_prodi_2 + '***' : 
                            ($store.config?.typeModal_2 == 'jurusan' ? '***Jurusan ' + $store.config?.nama_jurusan_2 + '***' : 
                            ($store.config?.typeModal_2 == 'fakultas' ? '***Fakultas ' + $store.config?.nama_fakultas_2 + '***' : 'Data ini'))
                        ">
                </strong>?
                Tindakan ini tidak dapat dibatalkan.
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost"
                    class="cursor-pointer w-full sm:w-auto 
                bg-[var(--sub-table-color)] hover:bg-[var(--main-table-color)]
                text-[var(--contrast-second-text)]
                transition-colors duration-200">
                    Batal</flux:button>
            </flux:modal.close>

            <flux:button wire:click="destroyProdi" wire:loading.attr="disabled" wire:target="deleteProdi, destroyProdi"
                type="submit" variant="primary"
                class="text-[var(--contrast-main-text)] cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                <span
                    x-text="
                            $store.config?.typeModal_2 === 'prodi' ? 'Hapus Program Studi' : 
                            ($store.config?.typeModal_2 === 'jurusan' ? 'Hapus Jurusan' : 
                            ($store.config?.typeModal_2 === 'fakultas' ? 'Hapus Fakultas' : 'Hapus Data ini'))
                        "
                    wire:loading.remove wire:target="destroyProdi">
                    Ya, Hapus Data
                </span>

                <span wire:loading wire:target="destroyProdi">
                    Menghapus...
                </span>
            </flux:button>
            {{-- </flux:modal.close> --}}

        </div>
    </div>
    {{-- <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('close-delete-modal', () => {
                Flux.modal('user-delete').hide()
            })
        })
    </script> --}}

</flux:modal>

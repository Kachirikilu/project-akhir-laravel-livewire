<flux:modal name="prodi-delete" wire:model="showProdiDelete" class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus
                <strong class="text-red-700 dark:text-red-400"
                    x-text="
                            $store.config?.typeModal_delete == 'prodi' ? '***Program Studi ' + $store.config?.nama_prodi_delete + '***' : 
                            ($store.config?.typeModal_delete == 'jurusan' ? '***Jurusan ' + $store.config?.nama_jurusan_delete + '***' : 
                            ($store.config?.typeModal_delete == 'fakultas' ? '***Fakultas ' + $store.config?.nama_fakultas_delete + '***' : '***Data ini***'))
                        ">
                </strong> dengan <strong class="text-red-700 dark:text-red-400"
                    x-text="
                            '***Kode ' + $store.config?.kode_pr_delete + '***'
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
                            $store.config?.typeModal_delete === 'prodi' ? 'Ya, Hapus Program Studi' : 
                            ($store.config?.typeModal_delete === 'jurusan' ? 'Ya, Hapus Jurusan' : 
                            ($store.config?.typeModal_delete === 'fakultas' ? 'Ya, Hapus Fakultas' : 'Ya, Hapus Data ini'))
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

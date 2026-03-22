<flux:modal name="user-delete" wire:model="showUserDelete" class="min-w-[20rem] max-w-md">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus
                <strong class="text-red-700 dark:text-red-400"
                    x-text="$store.config?.email_2 ? '***' + $store.config?.email_2 + '***' : 'Pengguna ini'">
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

            <flux:button 
                wire:click="destroyUser" 
                wire:loading.attr="disabled" 
                wire:target="deleteUser, destroyUser"
                type="submit" variant="primary"
                class="text-[var(--contrast-main-text)] cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200"
            >
                <span wire:loading.remove wire:target="destroyUser">
                    Ya, Hapus Pengguna
                </span>

                <span wire:loading wire:target="destroyUser">
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

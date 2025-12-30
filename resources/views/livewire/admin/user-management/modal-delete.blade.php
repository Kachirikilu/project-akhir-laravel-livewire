<flux:modal 
    name="delete-confirmation"
    wire:model="showDeleteConfirmation"
    class="min-w-[20rem] max-w-md"
    >
    
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus <strong class="text-slate-900">**{{ $userEmailToDelete }}**</strong>? 
                Tindakan ini tidak dapat dibatalkan.
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Batal</flux:button>
            </flux:modal.close>

            {{-- <flux:modal.close> --}}
                <flux:button 
                    variant="danger"
                    wire:click="deleteUser"
                >
                    <span wire:loading.remove wire:target="deleteUser">Ya, Hapus Pengguna</span>
                    <span wire:loading wire:target="deleteUser">Menghapus...</span>
                </flux:button>
            {{-- </flux:modal.close> --}}
            

        </div>
    </div>
    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-delete-modal', () => {
            Flux.modal('delete-confirmation').hide()
        })
    })
</script>

    
</flux:modal>

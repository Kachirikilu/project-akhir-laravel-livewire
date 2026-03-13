<flux:modal 
    name="user-delete"
    wire:model="showUserDelete"
    class="min-w-[20rem] max-w-md"
    >
    
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus <strong class="text-slate-900">**{{ $userTypeProdiToDelete }}**</strong>? 
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
                    wire:click="deleteProdi"
                >
                    <span wire:loading.remove wire:target="deleteProdi">Ya, Hapus Pengguna</span>
                    <span wire:loading wire:target="deleteProdi">Menghapus...</span>
                </flux:button>
            {{-- </flux:modal.close> --}}
            

        </div>
    </div>
    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-delete-modal', () => {
            Flux.modal('user-delete').hide()
        })
    })
</script>

    
</flux:modal>

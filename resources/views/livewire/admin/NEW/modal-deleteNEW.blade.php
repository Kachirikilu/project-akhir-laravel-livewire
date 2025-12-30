<flux:modal wire:model="showDeleteConfirmation" class="max-w-sm">
    <flux:heading class="text-red-600">
        Konfirmasi Hapus
    </flux:heading>

    <p class="text-sm text-gray-600">
        Yakin ingin menghapus <strong>{{ $userEmailToDelete }}</strong>?
    </p>

    <div class="flex justify-end gap-2 mt-4">
        <flux:button variant="ghost" wire:click="cancelDelete">
            Batal
        </flux:button>

        <flux:button variant="danger" wire:click="deleteUser">
            Hapus
        </flux:button>
    </div>
</flux:modal>

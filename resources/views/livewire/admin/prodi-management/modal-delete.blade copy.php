@if ($showDeleteConfirmation)
    <div x-show="$wire.showDeleteConfirmation" x-transition.opacity.duration.200ms x-cloak
        class="fixed inset-0 bg-gray-900 bg-opacity-40 flex justify-center items-center z-50">
        <div @click.outside="$wire.cancelDelete()"
            class="bg-white rounded-lg p-6 w-full max-w-sm transform transition-all duration-200 ease-out scale-100">

            {{-- Header --}}
            <h3 class="text-xl font-bold mb-2 text-red-600">Konfirmasi Hapus</h3>

            {{-- Body Pesan --}}
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus **{{ $userEmailToDelete }}**?
                Tindakan ini tidak dapat dibatalkan.
            </p>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3">
                <button wire:click="cancelDelete"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                    Batal
                </button>
                <button wire:click="deleteUser"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
@endif

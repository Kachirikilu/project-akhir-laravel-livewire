<flux:modal wire:model="showModal" class="max-w-4xl">
    <flux:heading>
        {{ $isEditing ? 'Edit ' . ucfirst($roleType) : 'Tambah ' . ucfirst($roleType) }}
    </flux:heading>

    <form wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
        <flux:input
            label="Email"
            wire:model.lazy="email"
            type="email"
            required
        />

        <flux:input
            label="Password"
            wire:model.lazy="password"
            type="password"
            :hint="$isEditing ? 'Kosongkan jika tidak diubah' : null"
        />

        <flux:input
            label="Nama Lengkap"
            wire:model.lazy="name"
            required
        />

        {{-- Role specific --}}
        @if ($roleType === 'dosen')
            <flux:input label="NIP" wire:model.lazy="nip" />
        @elseif ($roleType === 'mahasiswa')
            <flux:input label="NIM" wire:model.lazy="nim" />
            <flux:input label="Tahun Masuk" wire:model.lazy="tahun_angkatan" type="number" />
        @endif

        {{-- Autocomplete prodi tetap pakai Blade biasa --}}
        @include('livewire.admin.user-management.partials.prodi-autocomplete')

        <div class="flex justify-end gap-2 mt-6">
            <flux:button variant="ghost" wire:click="$set('showModal', false)">
                Batal
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $isEditing ? 'Perbarui' : 'Simpan' }}
            </flux:button>
        </div>
    </form>
</flux:modal>
